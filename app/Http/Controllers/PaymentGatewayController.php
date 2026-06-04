<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    public function createBill($paymentId)
    {
        $payment = Payment::with('user')
            ->where('user_id', Auth::id())
            ->where('id', $paymentId)
            ->firstOrFail();

        if ($payment->status === 'paid') {
            return redirect()->back()->with('success', 'Bayaran ini telah selesai.');
        }

        if ($payment->billplz_url) {
            return redirect()->away($payment->billplz_url);
        }

        $user = $payment->user;

        $amountInSen = (int) round($payment->amount * 100);

        $description = 'Bayaran Yuran Khairat e-Pusara - ' . ucfirst($payment->payment_plan);

        $response = Http::asForm()
            ->withBasicAuth(config('services.billplz.api_key'), '')
            ->post(config('services.billplz.base_url') . '/bills', [
                'collection_id' => config('services.billplz.collection_id'),
                'email' => $user->email ?? 'user@example.com',
                'name' => $user->nama ?? $user->name ?? 'Pengguna e-Pusara',
                'amount' => $amountInSen,
                'callback_url' => route('payment.callback'),
                'redirect_url' => route('payment.return'),
                'description' => $description,
                'reference_1_label' => 'Payment ID',
                'reference_1' => $payment->id,
                'reference_2_label' => 'User ID',
                'reference_2' => $payment->user_id,
            ]);

        if (!$response->successful()) {
            Log::error('Billplz create bill failed', [
                'payment_id' => $payment->id,
                'response' => $response->json(),
                'body' => $response->body(),
            ]);

            return redirect()->back()->with('error', 'Gagal menjana bil Billplz. Sila cuba lagi.');
        }

        $bill = $response->json();

        $payment->update([
            'billplz_bill_id' => $bill['id'] ?? null,
            'billplz_url' => $bill['url'] ?? null,
            'billplz_paid' => $bill['paid'] ?? false,
            'billplz_state' => $bill['state'] ?? null,
            'billplz_data' => $bill,
            'payment_method' => 'Billplz',
        ]);

        return redirect()->away($bill['url']);
    }

    public function paymentReturn(Request $request)
    {
            Log::info('Billplz return received', [
            'query' => $request->query(),
            'all' => $request->all(),
        ]);

        $billplzData = $request->query('billplz', []);

        $billId = $billplzData['id'] ?? $request->query('id');

        if (!$billId) {
            return redirect()->route('user.payments.index')
                ->with('info', 'Sila semak status bayaran anda.');
        }

        $payment = Payment::where('billplz_bill_id', $billId)->first();

        if (!$payment) {
            return redirect()->route('user.payments.index')
                ->with('error', 'Rekod bayaran tidak dijumpai.');
        }

        try {
            $response = Http::withBasicAuth(config('services.billplz.api_key'), '')
                ->get(config('services.billplz.base_url') . '/bills/' . $billId);

            if ($response->successful()) {
                $bill = $response->json();

                $paid = filter_var($bill['paid'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $state = $bill['state'] ?? null;

                $expectedAmountInSen = (int) round($payment->amount * 100);
                $billAmountInSen = (int) ($bill['amount'] ?? 0);

                if ($expectedAmountInSen !== $billAmountInSen) {
                    Log::error('Billplz return amount mismatch', [
                        'payment_id' => $payment->id,
                        'expected_amount' => $expectedAmountInSen,
                        'bill_amount' => $billAmountInSen,
                    ]);

                    return redirect()->route('user.payments.index')
                        ->with('error', 'Jumlah bayaran tidak sepadan. Sila hubungi pentadbir.');
                }

                /*
                |--------------------------------------------------------------------------
                | Jangan ubah payment yang sudah paid kembali kepada pending
                |--------------------------------------------------------------------------
                */
                if ($payment->status === 'paid' && !$paid) {
                    return redirect()->route('user.payments.index')
                        ->with('success', 'Bayaran berjaya direkodkan.');
                }

                $updateData = [
                    'status' => $paid ? 'paid' : 'pending',
                    'payment_method' => 'Billplz',
                    'billplz_paid' => $paid,
                    'billplz_state' => $state,
                    'billplz_data' => $bill,
                ];

                if ($paid) {
                    $updateData['paid_at'] = $payment->paid_at ?? now();
                }

                if ($paid && !$payment->receipt_no) {
                    $updateData['receipt_no'] = 'RCP-' . $payment->id . '-' . now()->format('YmdHis');
                }

                $payment->update($updateData);

                if ($paid) {
                    return redirect()->route('user.payments.index')
                        ->with('success', 'Bayaran berjaya direkodkan.');
                }

                return redirect()->route('user.payments.index')
                    ->with('info', 'Bayaran masih belum selesai. Sila lengkapkan pembayaran anda.');
            }

            Log::warning('Billplz bill status check failed', [
                'bill_id' => $billId,
                'payment_id' => $payment->id,
                'response' => $response->body(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Billplz return status check error', [
                'bill_id' => $billId,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        if ($payment->status === 'paid') {
            return redirect()->route('user.payments.index')
                ->with('success', 'Bayaran berjaya direkodkan.');
        }

        return redirect()->route('user.payments.index')
            ->with('info', 'Bayaran sedang diproses. Sila tunggu sebentar.');
    }

    public function paymentCallback(Request $request)
    {
        Log::info('Billplz callback received', $request->all());

        $callbackData = $request->all();

        /*
        |--------------------------------------------------------------------------
        | Sahkan request benar-benar datang daripada Billplz
        |--------------------------------------------------------------------------
        */
        if (!$this->isValidBillplzSignature($callbackData)) {
            Log::warning('Invalid Billplz callback signature', [
                'callback_data' => $callbackData,
            ]);

            return response('Invalid signature', 403);
        }

        $billId = $request->input('id');

        if (!$billId) {
            return response('Missing bill id', 400);
        }

        $payment = Payment::where('billplz_bill_id', $billId)->first();

        if (!$payment) {
            return response('Payment not found', 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Semak jumlah callback sama dengan jumlah payment dalam sistem
        |--------------------------------------------------------------------------
        */
        $expectedAmountInSen = (int) round($payment->amount * 100);
        $receivedAmountInSen = (int) $request->input('amount', 0);

        if ($expectedAmountInSen !== $receivedAmountInSen) {
            Log::error('Billplz callback amount mismatch', [
                'payment_id' => $payment->id,
                'expected_amount' => $expectedAmountInSen,
                'received_amount' => $receivedAmountInSen,
            ]);

            return response('Invalid payment amount', 422);
        }

        $paid = filter_var($request->input('paid'), FILTER_VALIDATE_BOOLEAN);
        $state = $request->input('state');

        /*
        |--------------------------------------------------------------------------
        | Jika sudah berjaya dibayar, jangan turunkan semula status kepada pending
        |--------------------------------------------------------------------------
        | Callback dan redirect boleh tiba dalam urutan berbeza.
        |--------------------------------------------------------------------------
        */
        if ($payment->status === 'paid' && !$paid) {
            return response('OK', 200);
        }

        $updateData = [
            'status' => $paid ? 'paid' : 'pending',
            'payment_method' => 'Billplz',
            'billplz_paid' => $paid,
            'billplz_state' => $state,
            'billplz_data' => $callbackData,
        ];

        if ($paid) {
            $updateData['paid_at'] = $payment->paid_at ?? now();

            if (!$payment->receipt_no) {
                $updateData['receipt_no'] = 'RCP-' . $payment->id . '-' . now()->format('YmdHis');
            }
        }

        $payment->update($updateData);

        return response('OK', 200);
    }


    private function isValidBillplzSignature(array $data): bool
{
    $receivedSignature = $data['x_signature'] ?? null;
    $xSignatureKey = config('services.billplz.x_signature');

    if (!$receivedSignature || !$xSignatureKey) {
        return false;
    }

    unset($data['x_signature']);

    /*
    |--------------------------------------------------------------------------
    | Billplz memerlukan setiap key dan value digabungkan,
    | disusun secara ascending tanpa mengira huruf besar/kecil,
    | kemudian disambung menggunakan simbol |
    |--------------------------------------------------------------------------
    */
    uksort($data, 'strcasecmp');

    $sourceStrings = [];

    foreach ($data as $key => $value) {
        $sourceStrings[] = $key . $value;
    }

    $source = implode('|', $sourceStrings);

    $calculatedSignature = hash_hmac('sha256', $source, $xSignatureKey);

    return hash_equals($calculatedSignature, $receivedSignature);
}

}