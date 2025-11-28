<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\DocumentRequest;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Store a simulated payment (called by the frontend when QR "scanned")
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fk_doc_request_id' => 'required|integer',
            'fk_user_id' => 'nullable|integer',
            'reference_ticket' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'fk_pay_method_id' => 'nullable|integer',
            'gateway' => 'nullable|string', // gateway like "GCash" or "Maya"
            'client_note' => 'nullable|string',
            'create_receipt' => 'nullable|boolean', // accepted but ignored (no payment_receipts table)
        ]);

        $docReq = DocumentRequest::find($data['fk_doc_request_id']);
        if (! $docReq) {
            return response()->json(['message' => 'Document request not found'], 404);
        }

        // determine amount
        $amount = isset($data['amount']) && $data['amount'] !== null ? (float)$data['amount'] : $docReq->processing_fee;
        if ($amount === null) {
            return response()->json(['message' => 'No processing fee available for this request'], 422);
        }

        // Resolve payment method id:
        $payMethodId = $data['fk_pay_method_id'] ?? null;

        if (empty($payMethodId)) {
            $gatewayName = trim($data['gateway'] ?? '');

            if ($gatewayName !== '') {
                $normalized = strtolower($gatewayName);
                $pm = PaymentMethod::whereRaw('LOWER(pay_method_name) = ?', [$normalized])->first();

                if (! $pm) {
                    $pm = PaymentMethod::where('pay_method_name', 'LIKE', '%' . $gatewayName . '%')->first();
                }

                if ($pm) {
                    $payMethodId = $pm->pay_method_id;
                } else {
                    // create a new method with provided gateway name
                    $pm = PaymentMethod::create(['pay_method_name' => $gatewayName]);
                    $payMethodId = $pm->pay_method_id;
                }
            }
        }

        // Final fallback: use or create a default method named 'Manual'
        if (empty($payMethodId)) {
            $defaultName = 'Manual';
            $pm = PaymentMethod::whereRaw('LOWER(pay_method_name) = ?', [strtolower($defaultName)])->first();
            if (! $pm) {
                $pm = PaymentMethod::create(['pay_method_name' => $defaultName]);
            }
            $payMethodId = $pm->pay_method_id;
        }

        $transactionRef = 'SIM-' . strtoupper(Str::random(8)) . '-' . time();

        $gatewayResp = [
            'simulated' => true,
            'method' => PaymentMethod::find($payMethodId)?->pay_method_name ?? null,
            'client_note' => $data['client_note'] ?? null,
            'transaction_id' => $transactionRef,
            'timestamp' => now()->toDateTimeString(),
        ];

        try {
            $result = DB::transaction(function () use ($docReq, $amount, $payMethodId, $transactionRef, $gatewayResp, $data) {

                $payment = Payment::create([
                    'fk_doc_request_id' => $docReq->doc_request_id,
                    'fk_user_id' => Auth::id() ?? ($data['fk_user_id'] ?? null),
                    'reference_ticket' => $data['reference_ticket'] ?? $docReq->doc_request_ticket ?? null,
                    'amount' => $amount,
                    'fk_pay_method_id' => $payMethodId,
                    'transaction_ref' => $transactionRef,
                    'receipt_path' => null,
                    'status' => 'PENDING',
                    'gateway_response' => $gatewayResp,
                    'fk_treasurer_id' => null,
                    'paid_at' => null,
                    'handled_at' => null,
                ]);

                // NOTE: We intentionally DO NOT create a payment_receipts record because your DB
                // does not have that table. If you later add the table, you can recreate receipt logic here.

                return ['payment' => $payment];
            });

            $paymentMethods = PaymentMethod::all(['pay_method_id', 'pay_method_name']);

            return response()->json([
                'message' => 'Payment record created (simulated)',
                'payment' => $result['payment'],
                'payment_methods' => $paymentMethods,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Payment creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create payment', 'error' => $e->getMessage()], 500);
        }
    }
}
