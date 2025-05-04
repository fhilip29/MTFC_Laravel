<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayMongoService
{
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
    }

    public function createPaymentIntent($amount, $currency = 'PHP')
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post('https://api.paymongo.com/v1/payment_intents', [
                'data' => [
                    'attributes' => [
                        'amount' => $amount * 100,
                        'payment_method_allowed' => ['gcash', 'card', 'paymaya'],
                        'payment_method_options' => [],
                        'currency' => $currency,
                    ]
                ]
            ]);

        return $response->json();
    }

    public function attachPaymentMethod($intentId, $paymentMethodId)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("https://api.paymongo.com/v1/payment_intents/{$intentId}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId
                    ]
                ]
            ]);

        return $response->json();
    }

    public function createPaymentMethod($type, $details)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("https://api.paymongo.com/v1/payment_methods", [
                'data' => [
                    'attributes' => array_merge($details, [
                        'type' => $type
                    ])
                ]
            ]);

        return $response->json();
    }

    /**
     * Create a PayMongo checkout session/link
     * 
     * @param float $amount The amount to be paid
     * @param string $description Description of the payment
     * @param array $billingDetails Customer billing details
     * @param string $successUrl URL to redirect on successful payment
     * @param string $failureUrl URL to redirect on failed payment
     * @param string $referenceNumber Unique reference for this transaction
     * @return array The checkout session data including the checkout URL
     */
    public function createCheckoutSession($amount, $description, $billingDetails, $successUrl, $failureUrl, $referenceNumber)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'billing' => $billingDetails,
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'reference_number' => $referenceNumber,
                        'success_url' => $successUrl,
                        'failure_url' => $failureUrl,
                        'description' => $description,
                        'line_items' => [
                            [
                                'name' => $description,
                                'quantity' => 1,
                                'amount' => $amount * 100, // Amount in cents
                                'currency' => 'PHP',
                            ]
                        ],
                        'payment_method_types' => ['card', 'gcash', 'paymaya'],
                    ]
                ]
            ]);

        return $response->json();
    }
}
