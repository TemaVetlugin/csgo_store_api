<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ContactUsRequest;
use App\Services\Mail\ContactUsMailService;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ContactUsController extends Controller
{
    public function __construct(private readonly ContactUsMailService $contactUsMailService)
    {
    }

    public function __invoke(ContactUsRequest $request): Response
    {
        $this->contactUsMailService->sendToAdministrator($request->getValidatedPayload());

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
