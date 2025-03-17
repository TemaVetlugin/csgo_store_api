<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class LoginController extends Controller
{
    public function __construct(private readonly Redirector $redirector)
    {
    }

    public function __invoke(): RedirectResponse
    {
        return $this->redirector->route('auth.steam.login');
    }
}
