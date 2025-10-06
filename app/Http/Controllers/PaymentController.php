<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function index()
    {
        $amount = 10; // default amount
        return view('payment', compact('amount'));
    }

    public function createTransaction(Request $request)
    {
        $merchantKey = env('MERCHANT_KEY');
        $amount = (int) $request->input('amount', 10);

        $payload = [
            'merchant_key' => $merchantKey,
            'invoice' => [
                'items' => [
                    ['name' => 'Deposit', 'price' => $amount, 'description' => 'deposit', 'qty' => 1]
                ],
                'invoice_id' => (string) time(),
                'invoice_description' => 'Deposit',
                'total' => $amount
            ],
            'currency_code' => 'INR',
            'ip' => $request->ip(),
            'domain' => parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost(),
            'user_id' => 'demoUser',
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://sandboxtest.space/en/purchase/create-transaction', $payload);

            if ($response->successful()) {
                $result = $response->json();
                if (is_array($result) && isset($result[0])) $result = $result[0];
                return response()->json($result);
            }

            return response()->json([
                'status' => false,
                'error_message' => 'Create transaction failed (HTTP ' . $response->status() . ')'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function getDepositDetails(Request $request)
    {
        $merchantKey = env('MERCHANT_KEY');
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['status' => false, 'error_message' => 'token is required'], 400);
        }

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://sandboxtest.space/en/purchase/get-deposit-details', [
                    'merchant_key' => $merchantKey,
                    'token' => $token,
                    'type' => 'upi'
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (is_array($result) && isset($result[0])) $result = $result[0];

                // Generate QR locally if API doesn't return
                $qrLink = $result['link'] ?? null;
                if ($qrLink) {
                    $qr = QrCode::format('png')->size(250)->generate($qrLink);
                    $qrBase64 = "data:image/png;base64," . base64_encode($qr);
                    $result['qr'] = $qrBase64;
                }

                return response()->json($result);
            }

            return response()->json(['status' => false, 'error_message' => 'Get deposit failed (HTTP ' . $response->status() . ')'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function validateTransaction(Request $request)
    {
        $merchantKey = env('MERCHANT_KEY_VALIDATE') ?: env('MERCHANT_KEY');
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['status' => false, 'error_message' => 'token is required'], 400);
        }

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://sandboxtest.space/api/v1/validate-transaction', [
                    'token' => $token,
                    'merchant_key' => $merchantKey
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (is_array($result) && isset($result[0])) $result = $result[0];
                return response()->json($result);
            }

            return response()->json(['status' => false, 'error_message' => 'Validate failed (HTTP ' . $response->status() . ')'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
