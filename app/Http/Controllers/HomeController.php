<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class HomeController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly string $homePageUrl,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        return $this->redirector->to($this->homePageUrl);
    }
}
