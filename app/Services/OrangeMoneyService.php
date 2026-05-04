<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Orange Money Guinea WebPay API Service
 *
 * API docs: https://developer.orange.com/apis/om-webpay-gn
 * Base URL: https://api.orange.com/orange-money-webpay/gn/v1
 *
 * TO SWITCH TO PRODUCTION:
 *   1. Set ORANGE_MONEY_ENV=production in .env
 *   2. Fill in ORANGE_MONEY_CLIENT_ID, ORANGE_MONEY_CLIENT_SECRET,
 *      ORANGE_MONEY_MERCHANT_KEY, ORANGE_MONEY_MERCHANT_NUMBER
 *   3. Remove the sandbox simulation block in initiatePayment()
 */
class OrangeMoneyService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $merchantKey;
    private string $merchantNumber;
    private string $notifyUrl;
    private string $returnUrl;
    private string $cancelUrl;
    private bool   $isSandbox;

    public function __construct()
    {
        $this->isSandbox      = config('services.orange_money.env', 'sandbox') !== 'production';
        $this->baseUrl        = config('services.orange_money.base_url', 'https://api.orange.com/orange-money-webpay/gn/v1');
        $this->clientId       = config('services.orange_money.client_id', '');
        $this->clientSecret   = config('services.orange_money.client_secret', '');
        $this->merchantKey    = config('services.orange_money.merchant_key', '');
        $this->merchantNumber = config('services.orange_money.merchant_number', '');
        $this->notifyUrl      = config('services.orange_money.notify_url', url('/orange-money/callback'));
        $this->returnUrl      = config('services.orange_money.return_url',  url('/user/dashboard'));
        $this->cancelUrl      = config('services.orange_money.cancel_url',  url('/user/dashboard'));
    }

    /**
     * Step 1 – Get OAuth2 access token.
     * Returns the token string or throws an exception.
     */
    public function getAccessToken(): string
    {
        // ── SANDBOX SIMULATION ──────────────────────────────────────────────
        if ($this->isSandbox) {
            return 'sandbox_fake_access_token';
        }
        // ── PRODUCTION ───────────────────────────────────────────────────────

        $response = Http::timeout(15)
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            Log::error('OrangeMoney: token request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Orange Money: impossible d\'obtenir le token d\'accès.');
        }

        return $response->json('access_token');
    }

    /**
     * Step 2 – Initiate a WebPay payment.
     *
     * @param  float  $amount        Amount in EUR (or sender currency)
     * @param  string $currency      Sender currency (EUR, USD, CAD)
     * @param  string $recipientPhone Guinean mobile number (+224…)
     * @param  string $orderId       Unique order reference
     * @param  string $type          'TRANSFERT' or 'RECHARGE'
     * @return array  ['payment_url'=>string, 'pay_token'=>string, 'notif_token'=>string]
     */
    public function initiatePayment(
        float  $amount,
        string $currency,
        string $recipientPhone,
        string $orderId,
        string $type = 'TRANSFERT'
    ): array {
        // ── SANDBOX SIMULATION ──────────────────────────────────────────────
        if ($this->isSandbox) {
            Log::info('OrangeMoney SANDBOX: initiatePayment', compact('amount', 'currency', 'recipientPhone', 'orderId', 'type'));
            return [
                'payment_url' => url('/orange-money/sandbox-success?order_id=' . $orderId),
                'pay_token'   => 'sandbox_pay_token_' . $orderId,
                'notif_token' => 'sandbox_notif_token_' . $orderId,
            ];
        }
        // ── PRODUCTION ───────────────────────────────────────────────────────

        $token = $this->getAccessToken();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/webpayment', [
                'merchant_key'   => $this->merchantKey,
                'currency'       => $currency,
                'order_id'       => $orderId,
                'amount'         => number_format($amount, 2, '.', ''),
                'return_url'     => $this->returnUrl . '?order_id=' . $orderId,
                'cancel_url'     => $this->cancelUrl . '?order_id=' . $orderId . '&status=cancel',
                'notif_url'      => $this->notifyUrl,
                'lang'           => 'fr',
                'reference'      => $recipientPhone,
            ]);

        if (! $response->successful()) {
            Log::error('OrangeMoney: payment initiation failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'order'  => $orderId,
            ]);
            throw new \RuntimeException('Orange Money: impossible d\'initier le paiement. Réessayez.');
        }

        $data = $response->json();

        return [
            'payment_url' => $data['payment_url'] ?? '',
            'pay_token'   => $data['pay_token']   ?? '',
            'notif_token' => $data['notif_token'] ?? '',
        ];
    }

    /**
     * Step 3 – Check payment status by order_id.
     * Returns 'SUCCESSFULL' | 'FAILED' | 'PENDING' | 'CANCELLED'
     */
    public function checkPaymentStatus(string $orderId): string
    {
        // ── SANDBOX SIMULATION ──────────────────────────────────────────────
        if ($this->isSandbox) {
            return 'SUCCESSFULL'; // Always success in sandbox
        }
        // ── PRODUCTION ───────────────────────────────────────────────────────

        $token = $this->getAccessToken();

        $response = Http::timeout(15)
            ->withToken($token)
            ->acceptJson()
            ->get($this->baseUrl . '/webpayment/' . $orderId, [
                'merchant_key' => $this->merchantKey,
            ]);

        if (! $response->successful()) {
            Log::error('OrangeMoney: status check failed', ['order' => $orderId, 'status' => $response->status()]);
            return 'PENDING';
        }

        return $response->json('status', 'PENDING');
    }

    /**
     * Validate the callback notification signature.
     * Returns true if the payload is authentic.
     */
    public function validateCallback(array $payload, string $notifToken): bool
    {
        // ── SANDBOX SIMULATION ──────────────────────────────────────────────
        if ($this->isSandbox) {
            return true;
        }
        // ── PRODUCTION ───────────────────────────────────────────────────────
        // Orange Money sends a hash to verify; compare with stored notif_token
        return isset($payload['notif_token']) && $payload['notif_token'] === $notifToken;
    }

    /**
     * Send a B2C payout to a Guinean mobile wallet (called after Stripe payment confirmed).
     *
     * @param  float  $amountGnf      Amount in GNF to send
     * @param  string $recipientPhone Guinean number (e.g. +224XXXXXXXXX)
     * @param  string $reference      Unique reference (transaction ID)
     * @return array  Response data from Orange Money
     */
    public function sendPayout(float $amountGnf, string $recipientPhone, string $reference): array
    {
        // ── SANDBOX SIMULATION ──────────────────────────────────────────────
        if ($this->isSandbox) {
            Log::info('OrangeMoney SANDBOX: sendPayout', compact('amountGnf', 'recipientPhone', 'reference'));
            return [
                'status'         => 'SUCCESS',
                'transaction_id' => 'SANDBOX_PAYOUT_' . $reference,
            ];
        }
        // ── PRODUCTION ───────────────────────────────────────────────────────

        $token = $this->getAccessToken();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/transfer', [
                'merchant_key' => $this->merchantKey,
                'currency'     => 'GNF',
                'order_id'     => $reference,
                'amount'       => number_format($amountGnf, 2, '.', ''),
                'receiver'     => $recipientPhone,
                'description'  => 'MAMCash payout',
            ]);

        if (! $response->successful()) {
            Log::error('OrangeMoney: sendPayout failed', [
                'status'    => $response->status(),
                'body'      => $response->body(),
                'reference' => $reference,
            ]);
            throw new \RuntimeException('Orange Money payout failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Generate a unique order ID for a transaction.
     */
    public static function generateOrderId(int $transactionId): string
    {
        return 'MAM-' . date('Ymd') . '-' . str_pad($transactionId, 6, '0', STR_PAD_LEFT);
    }
}
