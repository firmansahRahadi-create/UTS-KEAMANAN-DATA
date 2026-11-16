<?php
// crypto_helper.php

// Fungsi untuk membuat pasangan kunci RSA-2048 secara aman dan kompatibel dengan Laragon
function getRsaKeys()
{
    $config = [
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    // Deteksi otomatis file openssl.cnf pada environment Laragon (Windows)
    $php_bin_dir = dirname(PHP_BINARY);
    $possible_configs = [
        $php_bin_dir . '/extras/ssl/openssl.cnf',
        dirname($php_bin_dir, 2) . '/apache/conf/openssl.cnf'
    ];

    foreach ($possible_configs as $cfg) {
        if (file_exists($cfg)) {
            $config["config"] = realpath($cfg);
            break;
        }
    }

    $res = openssl_pkey_new($config);
    if (!$res) {
        die("Error OpenSSL: Gagal membuat kunci RSA. Pastikan ekstensi openssl aktif di Laragon.");
    }

    // Ekstrak Private Key dalam format string PEM
    openssl_pkey_export($res, $privKey, null, $config);

    // Ekstrak Public Key
    $pubKeyDetails = openssl_pkey_get_details($res);
    $pubKey = $pubKeyDetails["key"];

    return [$privKey, $pubKey];
}

// 1. ENKRIPSI HYBRID (Simetris AES-256-CBC + Asimetris RSA-2048) untuk Semua Konten
function encryptContent($plainTitle, $plainText)
{
    // Generate AES-256 Key (32 byte) & IV secara acak
    $aesKey = openssl_random_pseudo_bytes(32);
    $iv     = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Enkripsi Simetris (AES-256-CBC) untuk Judul & Isi Konten
    $encryptedTitle   = openssl_encrypt($plainTitle, 'aes-256-cbc', $aesKey, 0, $iv);
    $encryptedPayload = openssl_encrypt($plainText, 'aes-256-cbc', $aesKey, 0, $iv);

    // Generate Pasangan Kunci RSA-2048
    list($privKey, $pubKey) = getRsaKeys();

    // Enkripsi Asimetris (RSA Public Key) untuk mengamankan AES Key
    openssl_public_encrypt($aesKey, $encryptedKey, $pubKey);

    return [
        'encrypted_title'   => $encryptedTitle,
        'encrypted_payload' => $encryptedPayload,
        'iv'                => base64_encode($iv),
        'encrypted_key'     => base64_encode($encryptedKey),
        'private_key'       => base64_encode($privKey)
    ];
}

// 2. DEKRIPSI HYBRID (Asimetris RSA-2048 + Simetris AES-256-CBC)
function decryptContent($encryptedTitle, $encryptedPayload, $base64Iv, $base64EncryptedKey, $base64PrivKey)
{
    $iv           = base64_decode($base64Iv);
    $encryptedKey = base64_decode($base64EncryptedKey);
    $privKey      = base64_decode($base64PrivKey);

    // Ambil Private Key RSA
    $privateKey = openssl_get_privatekey($privKey);
    if (!$privateKey) {
        return [
            'status'  => false,
            'title'   => '[Gagal Dekripsi: Private Key Tidak Valid]',
            'content' => '[Gagal Dekripsi: Private Key Tidak Valid]'
        ];
    }

    // Dekripsi Asimetris untuk membuka AES Key
    $decryptedAesKey = '';
    $success = openssl_private_decrypt($encryptedKey, $decryptedAesKey, $privateKey);

    if (!$success) {
        return [
            'status'  => false,
            'title'   => '[Gagal Dekripsi: Kunci Privat Tidak Cocok]',
            'content' => '[Gagal Dekripsi: Kunci Privat Tidak Cocok]'
        ];
    }

    // Dekripsi Simetris untuk mengembalikan Judul & Isi Konten asli
    $decryptedTitle   = openssl_decrypt($encryptedTitle, 'aes-256-cbc', $decryptedAesKey, 0, $iv);
    $decryptedContent = openssl_decrypt($encryptedPayload, 'aes-256-cbc', $decryptedAesKey, 0, $iv);

    return [
        'status'  => ($decryptedTitle !== false && $decryptedContent !== false),
        'title'   => $decryptedTitle !== false ? $decryptedTitle : '[Gagal Dekripsi AES]',
        'content' => $decryptedContent !== false ? $decryptedContent : '[Gagal Dekripsi AES]'
    ];
}
?>