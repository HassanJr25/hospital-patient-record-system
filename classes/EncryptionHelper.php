<?php


class EncryptionHelper
{
       public static function encrypt(string $plainText): string
    {
        
        $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $cipherText = openssl_encrypt(
            $plainText,
            ENCRYPTION_METHOD,
            ENCRYPTION_KEY,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $cipherText);
    }

   
    public static function decrypt(string $encryptedText): string
    {
        $raw = base64_decode($encryptedText);

        $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);

        // The first $ivLength bytes are the IV we stored earlier.
        $iv = substr($raw, 0, $ivLength);

        // Everything AFTER the IV is the actual encrypted content.
        $cipherText = substr($raw, $ivLength);

        $plainText = openssl_decrypt(
            $cipherText,
            ENCRYPTION_METHOD,
            ENCRYPTION_KEY,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $plainText !== false ? $plainText : '';
    }
}
