<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    #[OA\Post(
        path: "/payment/subscribe",
        summary: "Buat transaksi langganan, return Snap token",
        security: [["sanctum" => []]],
        tags: ["Payment"],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "snap_token", type: "string"),
                    new OA\Property(property: "order_id", type: "string"),
                ]
            )),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function subscribe(Request $request)
    {
        $user   = $request->user();
        $amount = config('midtrans.price');

        $transaction = Transaction::create([
            'user_id'          => $user->id,
            'midtrans_order_id' => 'SUB-' . $user->id . '-' . time(),
            'status'           => 'pending',
            'amount'           => $amount,
        ]);

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id'     => $transaction->midtrans_order_id,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'item_details' => [[
                'id'       => 'SUBSCRIPTION-30D',
                'price'    => $amount,
                'quantity' => 1,
                'name'     => 'Langganan 30 Hari',
            ]],
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => $transaction->midtrans_order_id,
        ]);
    }
    
    #[OA\Post(
        path: "/payment/webhook",
        summary: "Webhook Midtrans (tidak perlu auth)",
        tags: ["Payment"],
        responses: [
            new OA\Response(response: 200, description: "OK"),
            new OA\Response(response: 403, description: "Invalid signature"),
            new OA\Response(response: 404, description: "Transaction not found"),
        ]
    )]
    public function webhook(Request $request)
    {
        $payload = $request->all();
    
        // Verifikasi signature Midtrans
        $signatureKey = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('midtrans.server_key')
        );
    
        if ($signatureKey !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }
    
        $transaction = Transaction::where('midtrans_order_id', $payload['order_id'])->first();
    
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;
    
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->activateSubscription($transaction);
        } elseif ($transactionStatus === 'settlement') {
            $this->activateSubscription($transaction);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->update(['status' => $transactionStatus]);
        }
    
        return response()->json(['message' => 'OK']);
    }
    
    private function activateSubscription(Transaction $transaction): void
    {
        $transaction->update([
            'status'                   => 'success',
            'midtrans_transaction_id'  => request('transaction_id'),
            'paid_at'                  => now(),
        ]);
    
        $user = $transaction->user;
    
        // Kalau masih aktif, extend dari tanggal expiry — tidak reset
        $from = ($user->subscribed_until && $user->subscribed_until->isFuture())
            ? $user->subscribed_until
            : now();
    
        $user->update(['subscribed_until' => $from->addDays(30)]);
    }
}
