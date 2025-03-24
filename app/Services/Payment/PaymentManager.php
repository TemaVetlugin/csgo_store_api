<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Entity\DTO\Payment\PaymentCallbackPayload;
use App\Entity\Events\PaymentProcessed;
use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Exceptions\Market\BuyProduct\Case\BuyProductExceptionCase;
use App\Jobs\SendOrderAdminMailJob;
use App\Jobs\SendSuccessTransactionMailJob;
use App\Models\Enum\OrderStatus;
use App\Models\Enum\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Order\OrderProvider;
use App\Services\Transaction\TransactionStatusMapper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PaymentManager
{
    public function __construct(
        private readonly OrderProvider $orderProvider,
        private readonly TransactionStatusMapper $transactionStatusMapper,
    )
    {
    }

    public function createPayment(Order $order, User $user): array
    {
        $data = [
            'amount' => $order->getTotalPrice() ?? 0,
            'currency' =>  strtoupper($order->getTotalPriceCurrency()),
            'merchant_order_id' => $order->getId() ?? '',
            'description' => 'Order #' . ($order->getId() ?? ''),
            'return_url' => ENV('HOME_PAGE_REDIRECT_URL') . '/en/order-status?uuid=' . $order->getUuid() ?? '',
            'client' => [
                'name' => ($order->getFirstName() ?? '') . ' ' . ($order->getLastName() ?? ''),
                'email' => $user->getEmail() ?? '',
                'phone' => $order->getPhone() ?? '',
                'address' => $order->getAddress() ?? '',
                'city' => $order->getCity() ?? '',
                'country' => $order->getCountryIso() ?? '',
            ],

            'options' => [
                'language' => 'en',
                'return_url' => ENV('HOME_PAGE_REDIRECT_URL') . '/en/order-status?uuid=' . $order->getUuid() ?? '',
            ],

            'custom_fields' => [
                'steam_id64' => $user->getSteamId() ?? '',
                'partner' => $this->extractPartnerFromTradeLink($user->getSteamTradeLink()) ?? '',
                'token' => $this->extractTokenFromTradeLink($user->getSteamTradeLink()) ?? '',
            ],
        ];

        $login = Config::get('services.payment.login');
        $password = Config::get('services.payment.password');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("{$login}:{$password}"),
        ])->post('https://api.sandbox.xiva.co/orders/create', $data);

        if (array_key_exists('errors', $response->json())) {
            throw new BuyProductException(
                "The payment system is currently unable to process your request. Please try again later or contact support if the issue persists.",
                BuyProductExceptionCase::PaymentInitialisationFailed,
            );
        }

        $payment = (new Payment())
            ->setExternalId($response['orders'][0]['id'])
            ->setStatus(PaymentStatus::New)
            ->setStatusChangedAt(Carbon::now())
            ->setAmount($order->getTotalPrice())
            ->setCurrency($order->getTotalPriceCurrency())
            ->setResolution('success');
        $payment->save();

        $order->setPayment($payment);
        $order->save();

        return ['url' => $response->header('location')];
    }

    public function checkPaymentStatus(Order $order): array
    {
        $result = $order->getStatus();
        if($result === OrderStatus::Pending) {
            $result = $this->updatePaymentStatus($order);
        }
        $tradeStatus = $this->transactionStatusMapper->getStatusDescription($order->getTransactions());
        return [
                'status' => $result,
            ] + $tradeStatus;
    }

    private function updatePaymentStatus(Order $order): OrderStatus
    {
        $login = Config::get('services.payment.login');
        $password = Config::get('services.payment.password');
        $payment = $order->getPayment();
        $paymentId = $payment->getExternalId();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("{$login}:{$password}"),
        ])->get("https://api.sandbox.xiva.co/orders/$paymentId");

        if (array_key_exists('errors', $response->json())) {
            throw new BuyProductException(
                "The payment system is currently unable to process your request. Please try again later or contact support if the issue persists.",
                BuyProductExceptionCase::PaymentInitialisationFailed,
            );
        }

        $paymentStatus = PaymentStatus::tryFrom($response->json()['orders'][0]['status']);

        if($paymentStatus===PaymentStatus::Authorized){
            $paymentStatus = $this->chargePayment($paymentId, $order->getTotalPrice());
        }

        $payment->setStatus($paymentStatus);
        $payment->setStatusChangedAt(Carbon::now());
        $payment->save();

        $result = match ($paymentStatus) {
            PaymentStatus::Charged => OrderStatus::Processed,
            PaymentStatus::Rejected, PaymentStatus::Declined, PaymentStatus::Fraud, PaymentStatus::Error => OrderStatus::Failed,
            default => OrderStatus::Pending,
        };

        $order->setStatus($result);
        $order->save();
        SendOrderAdminMailJob::dispatch($order);
        $transactions = $order->getTransactions();
        $hasNullBuyId = !empty(array_filter($transactions, function ($transaction) {
            return $transaction['buy_id'] === null;
        }));

        if ($result === OrderStatus::Processed && $hasNullBuyId) {
            PaymentProcessed::dispatch($order);
        }

        return $result;
    }

    private function chargePayment(string $paymentId, float $price): PaymentStatus
    {
        $login = Config::get('services.payment.login');
        $password = Config::get('services.payment.password');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("{$login}:{$password}"),
        ])->put("https://api.sandbox.xiva.co/orders/$paymentId/charge", ['amount' => $price]);
        $statusEnum = PaymentStatus::tryFrom($response->json()['orders'][0]['status']);
        return $statusEnum;
    }

    private function extractPartnerFromTradeLink(?string $tradeLink): string
    {
        if (!$tradeLink) {
            return '';
        }

        parse_str(parse_url($tradeLink, PHP_URL_QUERY), $params);
        return $params['partner'] ?? '';
    }

    private function extractTokenFromTradeLink(?string $tradeLink): string
    {
        if (!$tradeLink) {
            return '';
        }

        parse_str(parse_url($tradeLink, PHP_URL_QUERY), $params);
        return $params['token'] ?? '';
    }








//    public function createOrUpdateFromCallback(Order $order, PaymentCallbackPayload $callbackPayload): Payment
//    {
//        $payment = $order->getPayment();
//        if ($payment !== null && $this->isOutdatedCallback($payment, $callbackPayload)) {
//            return $payment;
//        }
//
//        if ($payment === null) {
//            $payment = new Payment();
//        }
//
//        $this->fillWithCallbackPayload($payment, $callbackPayload);
//        $payment->save();
//
//        if ($order->getPayment() === null) {
//            $order->setPayment($payment);
//        }
//
//        if ($payment->getStatus() === PaymentStatus::Processed) {
//            PaymentProcessed::dispatch($order);
//        }
//
//        return $payment;
//    }
//
//    private function isOutdatedCallback(Payment $payment, PaymentCallbackPayload $callbackPayload): bool
//    {
//        return $payment->getStatus() === $callbackPayload->getStatus()
//            || $payment->getStatusChangedAt()->getTimestamp() > $callbackPayload->getTimestamp()
//        ;
//    }
//
//    private function fillWithCallbackPayload(Payment $payment, PaymentCallbackPayload $callbackPayload): void
//    {
//        $payment->setExternalId($callbackPayload->getPaymentId());
//        $payment->setStatus($callbackPayload->getStatus());
//        $payment->setStatusChangedAt(Carbon::createFromTimestamp($callbackPayload->getTimestamp()));
//
//        if ($callbackPayload->getPaymentMethod() !== null) {
//            $payment->setPaymentService($callbackPayload->getPaymentMethod());
//        }
//
//        if ($callbackPayload->getAmount() !== null) {
//            $payment->setAmount($callbackPayload->getAmount());
//        }
//
//        if ($callbackPayload->getFee() !== null) {
//            $payment->setFee($callbackPayload->getFee());
//        }
//
//        if ($callbackPayload->getCurrency() !== null) {
//            $payment->setCurrency($callbackPayload->getCurrency());
//        }
//
//        if ($callbackPayload->getResolution() !== null) {
//            $payment->setResolution($callbackPayload->getResolution());
//        }
//
//        if ($callbackPayload->getToken() !== null) {
//            $payment->setToken($callbackPayload->getToken());
//        }
//    }
}
