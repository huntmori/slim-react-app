<?php

namespace App\Application\Common;

interface PasswordEncryptInterface
{
    public function encrypt(string $plainText) : string;
    public function decrypt(string $cryptText) : string;
}