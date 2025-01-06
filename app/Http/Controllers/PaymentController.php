<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function processEasyMoney(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string',
        ]);

        $transaction = Transaction::create([
            'payment_gateway' => 'EasyMoney',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'pending',
            'request_data' => json_encode($request->all()),
        ]);

        try {
            $response = Http::post('http://localhost:3000/process', $request->only('amount', 'currency'));

            if ($response->successful()) {
                $transaction->update([
                    'status' => 'success',
                    'response_data' => $response->body(),
                ]);
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'response_data' => $response->body(),
                ]);
            }

            return response()->json($transaction);
        } catch (\Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'response_data' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processSuperWalletz(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        $transaction = Transaction::create([
            'payment_gateway' => 'SuperWalletz',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'pending',
            'request_data' => json_encode($request->all()),
        ]);

        try {
            $response = Http::post('http://localhost:3003/pay', [
                'amount' => $request->amount,
                'currency' => $request->currency,
                'callback_url' => route('webhook.superwalletz'),
            ]);

            $transaction->update([
                'status' => 'processing',
                'transaction_id' => $response->json('transaction_id'),
                'response_data' => $response->body(),
            ]);

            return response()->json($transaction);
        } catch (\Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'response_data' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleSuperWalletzWebhook(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();

        if ($transaction) {
            $transaction->update([
                'status' => $request->status,
                'response_data' => json_encode($request->all()),
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
