<?php
// Helper untuk cetak struk (USB / Bluetooth) + template HTML

function getPrintConfig() {
    $file = __DIR__ . "/print_config.json";

    if (!file_exists($file)) {
        return [
            "mode" => "usb",
            "template" => "html_b"
        ];
    }

    $json = file_get_contents($file);
    return json_decode($json, true);
}

function savePrintConfig($mode, $template) {
    $file = __DIR__ . "/print_config.json";
    $data = [
        "mode" => $mode,
        "template" => $template
    ];

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function renderHTML_Struk($klinik, $pasien, $transaksi, $detail) {

    $logoPath = "../includes/logo.png"; // FIX seperti permintaan
    $logoBase64 = "";
    if (file_exists($logoPath)) {
        $logoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath));
    }

    $html = '
    <div style="font-family: monospace; width:240px; margin:auto; text-align:center;">
        <img src="'.$logoBase64.'" style="width:80px; margin-bottom:3px;">
        <div style="font-size:14px; font-weight:bold;">'.$klinik['nama_klinik'].'</div>
        <div style="font-size:11px;">'.$klinik['alamat'].'</div>
        <div style="font-size:11px;">IG: '.$klinik['instagram'].' | WA: '.$klinik['no_hp'].'</div>
        <hr>

        <table style="width:100%; font-size:12px; text-align:left;">
            <tr><td>Nama</td><td>: '.$pasien['nama'].'</td></tr>
            <tr><td>Tanggal</td><td>: '.date("d/m/Y H:i").'</td></tr>
        </table>

        <hr>
        <div style="font-weight:bold; margin-bottom:3px;">Rincian Tindakan</div>
        <table style="width:100%; font-size:12px;">';

    foreach ($detail as $d) {
        $html .= '
            <tr>
                <td>'.$d['nama_layanan'].' ('.$d['qty'].')</td>
                <td style="text-align:right;">'.number_format($d['subtotal'],0,",",".").'</td>
            </tr>';
    }

    $html .= '
        </table>
        <hr>
        <table style="width:100%; font-size:12px;">
            <tr><td>Subtotal</td><td style="text-align:right;">'.number_format($transaksi['subtotal'],0,",",".").'</td></tr>
            <tr><td>Diskon</td><td style="text-align:right;">'.number_format($transaksi['diskon'],0,",",".").'</td></tr>
            <tr><td><b>Total</b></td><td style="text-align:right;"><b>'.number_format($transaksi['total'],0,",",".").'</b></td></tr>
        </table>

        <hr>
        <div style="font-size:11px; margin-top:5px;">
            Terima kasih atas kunjungannya!
        </div>
    </div>';

    return $html;
}
