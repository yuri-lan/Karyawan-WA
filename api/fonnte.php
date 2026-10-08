<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Kirim pesan WhatsApp via Fonnte
 * @param string $target  Nomor tujuan (format 628xxx)
 * @param string $message Isi pesan
 * @return array          Response dari Fonnte
 */
function kirimWA($target, $message) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target'  => $target,
            'message' => $message,
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . FONNTE_TOKEN
        ],
    ]);

    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);

    if ($err) return ['status' => false, 'error' => $err];
    return json_decode($response, true) ?? ['status' => false, 'raw' => $response];
}

/**
 * Normalisasi nomor HP jadi format 62...
 */
function formatNomor($no) {
    $no = preg_replace('/[^0-9]/', '', $no);
    if (substr($no, 0, 1) === '0') $no = '62' . substr($no, 1);
    if (substr($no, 0, 2) !== '62') $no = '62' . $no;
    return $no;
}