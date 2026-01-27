<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\DocumentRequest;
use App\Models\PaymentMethod;
use App\Models\DocumentType; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

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
            'request_reference_ticket' => 'nullable|string',
            'paid_amount' => 'nullable|numeric',
            'fk_pay_method_id' => 'nullable|integer',
            'gateway' => 'nullable|string',
            'client_note' => 'nullable|string',
            'create_receipt' => 'nullable|boolean',
            'onsite' => 'nullable|boolean',
            'transaction_ref' => 'nullable|string', // optional client-provided transaction ref
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120', // optional file up to 5MB
        ]);

        $docReq = DocumentRequest::find($data['fk_doc_request_id']);
        if (! $docReq) {
            return response()->json(['message' => 'Document request not found'], 404);
        }

        // prefer paid_amount (client) otherwise fallback to processing_fee on the document request
        $amount = isset($data['paid_amount']) && $data['paid_amount'] !== null
            ? (float)$data['paid_amount']
            : $docReq->processing_fee;

        if ($amount === null) {
            return response()->json(['message' => 'No processing fee available for this request'], 422);
        }

        $isOnsite = $request->boolean('onsite');

        $payMethodId = null;
        $transactionRef = null;

        if ($isOnsite) {
            $pm = PaymentMethod::whereRaw('LOWER(pay_method_name) LIKE ?', ['%onsite%'])->first();
            if (! $pm) {
                $pm = PaymentMethod::create(['pay_method_name' => 'Onsite']);
            }
            $payMethodId = $pm->pay_method_id;
            $transactionRef = null;
        } else {
            $payMethodId = $data['fk_pay_method_id'] ?? null;

            if (empty($payMethodId) && !empty($data['gateway'])) {
                $gatewayName = trim($data['gateway']);
                $pm = PaymentMethod::whereRaw('LOWER(pay_method_name) = ?', [strtolower($gatewayName)])->first();
                if (! $pm) {
                    $pm = PaymentMethod::where('pay_method_name', 'LIKE', '%' . $gatewayName . '%')->first();
                }
                if ($pm) {
                    $payMethodId = $pm->pay_method_id;
                } else {
                    $pm = PaymentMethod::create(['pay_method_name' => $gatewayName]);
                    $payMethodId = $pm->pay_method_id;
                }
            }

            if (empty($payMethodId)) {
                $defaultName = 'Manual';
                $pm = PaymentMethod::whereRaw('LOWER(pay_method_name) = ?', [strtolower($defaultName)])->first();
                if (! $pm) {
                    $pm = PaymentMethod::create(['pay_method_name' => $defaultName]);
                }
                $payMethodId = $pm->pay_method_id;
            }

            $transactionRef = !empty($data['transaction_ref'])
                ? $data['transaction_ref']
                : 'SIM-' . strtoupper(Str::random(8)) . '-' . time();
        }

        try {
            $result = DB::transaction(function () use ($docReq, $amount, $payMethodId, $transactionRef, $data, $isOnsite, $request) {

                // Check if there's an existing PENDING payment for this document request
                $existingPayment = Payment::where('fk_doc_request_id', $docReq->doc_request_id)
                    ->where('status', 'PENDING')
                    ->first();

                $receiptContent = null;
                if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
                    $file = $request->file('evidence');
                    $path = $file->store('payments', 'public');
                    // store a public URL into the receipt_content column
                    $receiptContent = Storage::url($path);
                }

                // If existing payment found, update it instead of creating a new one
                if ($existingPayment) {
                    // Delete old receipt file if it exists and we're uploading a new one
                    if ($receiptContent && $existingPayment->receipt_content) {
                        $oldPath = str_replace(Storage::url(''), '', $existingPayment->receipt_content);
                        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }

                    // Update existing payment with new payment method and evidence
                    $existingPayment->fk_pay_method_id = $payMethodId;
                    $existingPayment->paid_amount = $amount;
                    $existingPayment->updated_at = now();
                    
                    // Handle transaction_ref: if onsite, set to null; if online, use provided or generate one
                    if ($isOnsite) {
                        $existingPayment->transaction_ref = null;
                    } else {
                        // For online payments, use provided transaction_ref or generate one if not provided
                        $existingPayment->transaction_ref = $transactionRef;
                    }
                    
                    // Update receipt_content if new evidence is uploaded
                    if ($receiptContent) {
                        $existingPayment->receipt_content = $receiptContent;
                    }
                    
                    $existingPayment->save();

                    return ['payment' => $existingPayment, 'updated' => true];
                }

                // Create new payment if no existing PENDING payment found
                $payment = Payment::create([
                    'fk_doc_request_id' => $docReq->doc_request_id,
                    'fk_user_id' => Auth::id() ?? ($data['fk_user_id'] ?? null),
                    'request_reference_ticket' => $data['request_reference_ticket'] ?? $docReq->doc_request_ticket ?? null,
                    'paid_amount' => $amount,
                    'fk_pay_method_id' => $payMethodId,
                    'transaction_ref' => $isOnsite ? null : $transactionRef,
                    // model's fillable includes receipt_content
                    'receipt_content' => $receiptContent,
                    'status' => 'PENDING',
                    'fk_treasurer_id' => null,
                    'paid_at' => null,
                    'handled_at' => null,
                    'updated_at' => now(),
                ]);

                return ['payment' => $payment, 'updated' => false];
            });

            $paymentMethods = PaymentMethod::all(['pay_method_id', 'pay_method_name']);

            $message = $result['updated'] 
                ? 'Payment record updated successfully' 
                : 'Payment record created';

            return response()->json([
                'message' => $message,
                'payment' => $result['payment'],
                'payment_methods' => $paymentMethods,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Payment creation/update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create/update payment', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Index - fetch payments for the Treasurer view
     */
    public function index(Request $request)
    {
        // Load payments and eager load relations we need
        // Only show PENDING payments - approved/rejected payments disappear from view
        $payments = Payment::with([
            'paymentMethod',
            'user.credential',
            'documentRequest.documentType'
        ])
        ->where('status', 'PENDING')
        ->orderBy('payment_id', 'desc')
        ->get();

        // Map into the shape expected by the frontend
        $mapped = $payments->map(function ($p) {
            // Requestor name
            $requestor = $p->user?->name
                ?? trim(($p->user?->first_name ?? '') . ' ' . ($p->user?->last_name ?? ''))
                ?? 'Unknown User';

            // Document
            $document = $p->documentRequest->doc_request_ticket
                ?? $p->reference_ticket
                ?? 'N/A';

            // Document type
            $documentTypeName = null;
            if (!empty($p->documentRequest?->documentType?->document_name)) {
                $documentTypeName = $p->documentRequest->documentType->document_name;
            } else {
                $documentTypeName = $p->documentRequest->document_type
                    ?? $p->documentRequest->doc_type
                    ?? $p->documentRequest->request_type
                    ?? 'N/A';
            }

            // Amount
            $amount = (float) ($p->paid_amount ?? $p->amount ?? 0.0);

            // Date to display & ISO - use updated_at for submission date (since created_at doesn't exist)
            $dateObj = $p->updated_at ?? $p->paid_at ?? $p->handled_at ?? null;
            if ($dateObj instanceof \DateTime || $dateObj instanceof Carbon) {
                $dateDisplay = $dateObj->format('m/d/Y');
                $dateIso = $dateObj->toDateTimeString();
            } else {
                $dateDisplay = now()->format('m/d/Y');
                $dateIso = now()->toDateTimeString();
            }
            
            // Payment status
            $status = strtoupper(trim($p->status ?? 'PENDING'));

            // Profile image
            $profileImg = $p->user?->profile_pic ?? '/assets/DEFAULT.jpg';
            if ($profileImg && !Str::startsWith($profileImg, ['http://', 'https://', '/'])) {
                $profileImg = asset('storage/' . ltrim($profileImg, '/'));
            } elseif (empty($profileImg)) {
                $profileImg = '/assets/DEFAULT.jpg';
            }
            
            // User role
            $roleId = $p->user?->fk_role_id ?? $p->user?->role_id ?? 1;
            $roleName = match($roleId) {
                1 => 'Resident',
                2 => 'Barangay Captain',
                3 => 'Barangay Secretary',
                4 => 'Barangay Treasurer',
                5 => 'Barangay Kagawad',
                6 => 'Sangguniang Kabataan Chairman',
                7 => 'Sangguniang Kabataan Kagawad',
                9 => 'System Admin',
                default => 'Resident',
            };

            // Payment method name normalization
            $rawMethod = trim($p->paymentMethod?->pay_method_name ?? '');
            $upper = strtoupper($rawMethod);
            if (str_contains($upper, 'GCASH')) {
                $payMethodName = 'GCASH';
            } elseif (str_contains($upper, 'MAYA')) {
                $payMethodName = 'MAYA';
            } elseif (str_contains($upper, 'CASH')) {
                $payMethodName = 'CASH';
            } elseif (str_contains($upper, 'ONSITE') || str_contains($upper, 'ON-SITE') || str_contains($upper, 'ON SITE')) {
                $payMethodName = 'ONSITE';
            } else {
                $payMethodName = $upper !== '' ? $upper : 'ONSITE';
            }

            // Contact
            $contact = $p->user?->credential?->contact_number
                ?? $p->user?->contact_number
                ?? 'N/A';

            // Address
            $address = $p->user?->address
                ?? $p->documentRequest->address
                ?? 'N/A';

            // Transaction id
            $transactionId = $p->transaction_ref
                ?? ($p->gateway_response['transaction_id'] ?? null)
                ?? 'N/A';

            // Payment time (friendly)
            $paymentTime = $p->paid_at?->format('m/d/Y h:i A')
                ?? ($p->gateway_response['timestamp'] ?? null)
                ?? 'N/A';

            // ====== Receipt image URL handling (important fix) ======
            // We store a URL into receipt_content (Storage::url($path)) in your store() method.
            // Handle several possibilities:
            //  - receipt_content already contains a URL (absolute or beginning with '/storage')
            //  - receipt_content contains a relative path -> build asset('storage/...') for it
            //  - older column/field 'receipt_path' (if you used that before) -> also convert to asset(...)
            $receiptImage = null;

            // priority: receipt_content (exists and non-empty)
            if (!empty($p->receipt_content)) {
                // if it looks like a full URL use as-is
                if (Str::startsWith($p->receipt_content, ['http://', 'https://', '/'])) {
                    $receiptImage = $p->receipt_content;
                } else {
                    // assume it's a storage path (e.g. payments/xyz.jpg)
                    $receiptImage = asset('storage/' . ltrim($p->receipt_content, '/'));
                }
            } elseif (!empty($p->receipt_path)) {
                // fallback for older field names
                $receiptImage = asset('storage/' . ltrim($p->receipt_path, '/'));
            } else {
                $receiptImage = null;
            }

            return [
                'id' => $p->payment_id,
                'requestor' => $requestor,
                'document' => $document,
                'documentType' => $documentTypeName,
                'amount' => $amount,
                'date' => $dateDisplay,
                'date_iso' => $dateIso,
                'paymentMethod' => $payMethodName,
                'contact' => $contact,
                'address' => $address,
                'transactionId' => $transactionId,
                'paymentTime' => $paymentTime,
                'receiptImage' => $receiptImage,
                'status' => $status,
                'profileImg' => $profileImg,
            ];
        });

        $paymentMethods = PaymentMethod::all(['pay_method_id', 'pay_method_name']);

        return Inertia::render('Admin/Treasurer/T_View_Payment', [
            'payments' => $mapped->values(),
            'payment_methods' => $paymentMethods,
        ]);
    }


    public function updateStatus(Request $request, $paymentId)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['APPROVED', 'REJECTED', 'approved', 'rejected', 'Approved', 'Rejected'])],
            'notes' => 'nullable|string',
        ]);

        // Normalize status to uppercase
        $status = strtoupper($data['status']);

        try {
            $payment = Payment::where('payment_id', $paymentId)->firstOrFail();

            // Only allow changes if current status is PENDING (optional protective check)
            // Remove or adjust this check if you want to allow re-handling
            if (strtoupper($payment->status) !== 'PENDING') {
                // Return Inertia response instead of JSON to avoid Inertia error
                return back()->withErrors([
                    'payment_status' => 'Payment is not in a pending state. It has already been ' . $payment->status . '.'
                ]);
            }

            // Set common fields
            $payment->status = $status;
            $payment->fk_treasurer_id = Auth::id() ?? $payment->fk_treasurer_id;
            $payment->handled_at = now();

            if ($status === 'APPROVED') {
                // mark as paid
                $payment->paid_at = now();
            } elseif ($status === 'REJECTED') {
                // ensure paid_at is null when rejected (optional)
                $payment->paid_at = null;
            }

            $payment->save();

            // Return Inertia redirect instead of JSON to avoid Inertia error
            return back()->with('success', 'Payment status updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['payment' => 'Payment not found']);
        } catch (\Throwable $e) {
            \Log::error('Failed to update payment status: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'Failed to update payment status. Please try again.']);
        }
    }

    /**
     * History - fetch approved/rejected payments for the Treasurer history view
     */
    public function history(Request $request)
    {
        // Load payments that are APPROVED or REJECTED (not PENDING)
        $payments = Payment::with([
            'paymentMethod',
            'user.credential',
            'documentRequest.documentType'
        ])
        ->whereIn('status', ['APPROVED', 'REJECTED'])
        ->orderBy('handled_at', 'desc')
        ->orderBy('payment_id', 'desc')
        ->get();

        // Map into the shape expected by the frontend
        $mapped = $payments->map(function ($p) {
            // Requestor name
            $requestor = $p->user?->name
                ?? trim(($p->user?->first_name ?? '') . ' ' . ($p->user?->last_name ?? ''))
                ?? 'Unknown User';

            // Document
            $document = $p->documentRequest->doc_request_ticket
                ?? $p->reference_ticket
                ?? 'N/A';

            // Document type
            $documentTypeName = null;
            if (!empty($p->documentRequest?->documentType?->document_name)) {
                $documentTypeName = $p->documentRequest->documentType->document_name;
            } else {
                $documentTypeName = $p->documentRequest->document_type
                    ?? $p->documentRequest->doc_type
                    ?? $p->documentRequest->request_type
                    ?? 'N/A';
            }

            // Amount
            $amount = (float) ($p->paid_amount ?? $p->amount ?? 0.0);

            // Date to display - use handled_at (when treasurer processed it) or paid_at
            $dateObj = $p->handled_at ?? $p->paid_at ?? $p->updated_at ?? null;
            if ($dateObj instanceof \DateTime || $dateObj instanceof Carbon) {
                $dateDisplay = $dateObj->format('m/d/Y');
                $dateIso = $dateObj->toDateTimeString();
            } else {
                $dateDisplay = now()->format('m/d/Y');
                $dateIso = now()->toDateTimeString();
            }
            
            // Payment status
            $status = strtoupper(trim($p->status ?? 'PENDING'));

            // Payment method name normalization
            $rawMethod = trim($p->paymentMethod?->pay_method_name ?? '');
            $upper = strtoupper($rawMethod);
            if (str_contains($upper, 'GCASH')) {
                $payMethodName = 'GCASH';
                $methodKey = 'online';
            } elseif (str_contains($upper, 'MAYA')) {
                $payMethodName = 'MAYA';
                $methodKey = 'online';
            } elseif (str_contains($upper, 'CASH')) {
                $payMethodName = 'CASH';
                $methodKey = 'onsite';
            } elseif (str_contains($upper, 'ONSITE') || str_contains($upper, 'ON-SITE') || str_contains($upper, 'ON SITE')) {
                $payMethodName = 'ONSITE';
                $methodKey = 'onsite';
            } else {
                $payMethodName = $upper !== '' ? $upper : 'ONSITE';
                $methodKey = 'onsite';
            }

            // Transaction ID (use for Payment No.)
            $transactionId = $p->transaction_ref
                ?? ($p->gateway_response['transaction_id'] ?? null)
                ?? 'N/A';

            // Receipt image URL
            $receiptImage = null;
            if (!empty($p->receipt_content)) {
                if (Str::startsWith($p->receipt_content, ['http://', 'https://', '/'])) {
                    $receiptImage = $p->receipt_content;
                } else {
                    $receiptImage = asset('storage/' . ltrim($p->receipt_content, '/'));
                }
            } elseif (!empty($p->receipt_path)) {
                $receiptImage = asset('storage/' . ltrim($p->receipt_path, '/'));
            }

            // Profile image
            $profileImg = $p->user?->profile_pic ?? '/assets/DEFAULT.jpg';
            if ($profileImg && !Str::startsWith($profileImg, ['http://', 'https://', '/'])) {
                $profileImg = asset('storage/' . ltrim($profileImg, '/'));
            } elseif (empty($profileImg) || $profileImg === '/assets/DEFAULT.jpg') {
                $profileImg = '/assets/DEFAULT.jpg';
            }
            
            // User role
            $roleId = $p->user?->fk_role_id ?? $p->user?->role_id ?? 1;
            $roleName = match($roleId) {
                1 => 'Resident',
                2 => 'Barangay Captain',
                3 => 'Barangay Secretary',
                4 => 'Barangay Treasurer',
                5 => 'Barangay Kagawad',
                6 => 'Sangguniang Kabataan Chairman',
                7 => 'Sangguniang Kabataan Kagawad',
                9 => 'System Admin',
                default => 'Resident',
            };

            return [
                'id' => $p->payment_id,
                'payment_no' => $transactionId, // Use transaction ID as payment number
                'name' => $requestor,
                'doc_type' => $documentTypeName,
                'document' => $document,
                'amount' => $amount,
                'date' => $dateDisplay,
                'date_iso' => $dateIso,
                'method' => $methodKey,
                'paymentMethod' => $payMethodName,
                'status' => $status,
                'receiptImage' => $receiptImage,
                'transactionId' => $transactionId,
                'paymentTime' => $p->handled_at?->format('m/d/Y h:i A') ?? $p->paid_at?->format('m/d/Y h:i A') ?? 'N/A',
                'profileImg' => $profileImg,
                'role' => $roleName,
                'roleId' => $roleId,
            ];
        });

        return Inertia::render('Admin/Treasurer/T_History', [
            'payments' => $mapped->values(),
        ]);
    }
}
