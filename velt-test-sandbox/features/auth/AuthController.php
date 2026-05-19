<?php

declare(strict_types=1);

namespace App\Features\Auth;

final class AuthController
{
    public function index(): mixed
    {
        return require __DIR__ . '/views/login.velt.php';
    }
}
