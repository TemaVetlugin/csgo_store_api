<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Exceptions\Market\User\UserIsNotTradableException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->registerBuyProductException();
        $this->registerUserIsNotTradableException();
    }

    private function registerBuyProductException(): void
    {
        $this->renderable(
            function (BuyProductException $exception) {
                return response()->json(
                    [
                        'error' => [
                            'code' => $exception->getCase()->toString(),
                            'message' => $exception->getMessage(),
                            'products' => array_filter(
                                array_map(
                                    static fn (array $product) => $product['name'] ?? null,
                                    $exception->getProducts(),
                                )
                            ),
                        ],
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                );
            }
        );
    }

    private function registerUserIsNotTradableException(): void
    {
        $this->renderable(
            function (UserIsNotTradableException $exception) {
                return response()->json(
                    [
                        'error' => [
                            'code' => $exception->getCase()->toString(),
                            'message' => $exception->getMessage(),
                        ],
                    ],
                    Response::HTTP_FORBIDDEN,
                );
            }
        );
    }
}
