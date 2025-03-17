<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class VerifyUserEmailController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly string $homePageUrl,
    ) {
    }

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return $this->redirector->to($this->homePageUrl);
    }
}
