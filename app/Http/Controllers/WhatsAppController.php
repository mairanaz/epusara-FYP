<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    private function buildWhatsAppUrl(string $phone, string $message): string
    {
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public function laporKematian(Request $request)
    {
        $adminPhone = '60132186469';

        $namaSiMati = $request->get('nama_si_mati', '[Nama Si Mati]');
        $noTelefonPelapor = $request->get('no_tel', '[No Telefon]');
        $namaPelapor = $request->get('nama_pelapor', '[Nama Pelapor]');

        $message = "Assalamualaikum, saya ingin memaklumkan bahawa ahli khairat bernama {$namaSiMati} telah meninggal dunia. Mohon pihak pentadbiran hubungi saya, {$namaPelapor}, di {$noTelefonPelapor} untuk tindakan lanjut.";

        $whatsappUrl = $this->buildWhatsAppUrl($adminPhone, $message);

        return redirect()->away($whatsappUrl);
    }
}