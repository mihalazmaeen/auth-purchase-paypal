<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalController extends Controller
{
    private $clientId;
    private $secret;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.mode') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function createPayment(Request $request)
    {
        $productName = $request->input('productName');
        $price = $request->input('price');

        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/payments/payment", [
                'intent' => 'sale',
                'redirect_urls' => [
                    'return_url' => route('paypal.return'),
                    'cancel_url' => route('paypal.cancel'),
                ],
                'payer' => [
                    'payment_method' => 'paypal',
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => $price,
                            'currency' => 'USD',
                        ],
                        'description' => $productName,
                    ],
                ],
            ]);

        $payment = json_decode($response->body(), true);

        foreach ($payment['links'] as $link) {
            if ($link['rel'] === 'approval_url') {
                return redirect()->away($link['href']);
            }
        }

        return redirect()->route('checkout')->with('error', 'Something went wrong.');
    }

    public function executePayment(Request $request)
    {
        $paymentId = $request->query('paymentId');
        $payerId = $request->query('PayerID');

        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/payments/payment/{$paymentId}/execute", [
                'payer_id' => $payerId,
            ]);

        $payment = json_decode($response->body(), true);

        if ($payment['state'] === 'approved') {
            return redirect()->route('dashboard')->with('success', 'Payment successful!');
        }

        return redirect()->route('checkout')->with('error', 'Payment failed.');
    }

    public function cancelPayment()
    {
        return redirect()->route('checkout')->with('error', 'Payment cancelled.');
    }
}
