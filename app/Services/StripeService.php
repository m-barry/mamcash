<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Event;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe PaymentIntent.
     *
     * @param  float  $amount    Amount in EUR
     * @param  string $currency  ISO currency code (e.g. 'eur')
     * @param  array  $metadata  Extra metadata to attach
     */
    public function createPaymentIntent(float $amount, string $currency, array $metadata = []): PaymentIntent
    {
        return PaymentIntent::create([
            'amount'               => (int) round($amount * 100),
            'currency'             => strtolower($currency),
            'payment_method_types' => ['card'],
            'description'          => 'MAMCash — Transfert vers Guinée',
            'metadata'             => $metadata,
        ]);
    }

    /**
     * Retrieve an existing PaymentIntent by ID.
     */
    public function retrieve(string $id): PaymentIntent
    {
        return PaymentIntent::retrieve($id);
    }

    /**
     * Construct and verify a Stripe webhook event.
     */
    public function constructEvent(string $payload, string $sig, string $secret): Event
    {
        return Webhook::constructEvent($payload, $sig, $secret);
    }
}
