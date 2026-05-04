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

                $updateData = [
                    'status' => $paid ? 'paid' : 'pending',
                    'paid_at' => $paid ? ($payment->paid_at ?? now()) : null,
                    'payment_method' => 'Billplz',
                    'billplz_paid' => $paid,
                    'billplz_state' => $state,
                    'billplz_data' => $bill,
                ];

                if ($paid && !$payment->receipt_no) {
                    $updateData['receipt_no'] = 'RCP-' . now()->format('YmdHis');
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

        $billId = $request->input('id');

        if (!$billId) {
            return response('Missing bill id', 400);
        }

        $payment = Payment::where('billplz_bill_id', $billId)->first();

        if (!$payment) {
            return response('Payment not found', 404);
        }

        $paid = filter_var($request->input('paid'), FILTER_VALIDATE_BOOLEAN);
        $state = $request->input('state');

        $updateData = [
            'status' => $paid ? 'paid' : 'pending',
            'paid_at' => $paid ? now() : null,
            'payment_method' => 'Billplz',
            'billplz_paid' => $paid,
            'billplz_state' => $state,
            'billplz_data' => $request->all(),
        ];

        if ($paid && !$payment->receipt_no) {
            $updateData['receipt_no'] = 'RCP-' . now()->format('YmdHis');
        }

        $payment->update($updateData);

        return response('OK', 200);
    }
}