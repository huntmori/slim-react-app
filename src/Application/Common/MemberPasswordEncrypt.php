<?php

namespace App\Application\Common;

use App\Application\Settings\SettingsInterface;

class MemberPasswordEncrypt implements PasswordEncryptInterface
{
    private string $encryptKey;
    private static string $ENC_ALGORITHM = "AES-128-ECB";

    function __construct(SettingsInterface $settings)
    {
        $this->encryptKey = $settings->get('config')['MEMBER_PASSWORD_ENCRYPT_KEY'];
    }

    public function encrypt(string $plainText): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt(
            $plainText,
            'aes-256-cbc',
            $this->encryptKey,
            OPENSSL_RAW_DATA,
            $iv
        );
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $cryptText): string
    {
        $data = base64_decode($cryptText);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encryptedData = substr($data, $ivLength);
        return openssl_decrypt(
            $encryptedData,
            'aes-256-cbc',
            $this->encryptKey,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}