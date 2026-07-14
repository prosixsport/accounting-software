<?php

namespace App\Http\Controllers;

use App\Models\ExpenseReceiptUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ExpenseReceiptUploadController extends Controller
{
    /**
     * PC se naya mobile upload session create hoga.
     */
    public function createSession()
    {
        $token = Str::random(64);

        $upload = ExpenseReceiptUpload::create([
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        return response()->json([
            'success' => true,
            'token' => $upload->token,
            'mobile_url' => route(
                'expense.receipt.mobile',
                $upload->token
            ),
            'expires_at' => $upload->expires_at?->toDateTimeString(),
        ]);
    }

    /**
     * Mobile camera/upload page.
     */
    public function mobilePage(string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)
            ->firstOrFail();

        if (
            $upload->expires_at &&
            $upload->expires_at->isPast()
        ) {
            if ($upload->status !== 'completed') {
                $upload->update([
                    'status' => 'expired',
                ]);
            }

            return view('expenses.mobile-receipt-expired');
        }

        return view(
            'expenses.mobile-receipt-upload',
            compact('upload')
        );
    }

    /**
     * Mobile se bill photo upload hogi.
     */
    public function uploadFromMobile(
        Request $request,
        string $token
    ) {
        try {
            $upload = ExpenseReceiptUpload::where('token', $token)
                ->firstOrFail();

            if (
                $upload->expires_at &&
                $upload->expires_at->isPast()
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'This upload link has expired.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Request mein file field check
            |--------------------------------------------------------------------------
            */

            if (!$request->has('receipt') && !$request->hasFile('receipt')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No receipt image was received.',
                ], 422);
            }

            $file = $request->file('receipt');

            /*
            |--------------------------------------------------------------------------
            | PHP upload error ko readable message mein convert karein
            |--------------------------------------------------------------------------
            */

            if (!$file || !$file->isValid()) {
                $errorCode = $file?->getError();

                $errorMessage = match ($errorCode) {
                    UPLOAD_ERR_INI_SIZE =>
                        'The image is larger than the PHP upload limit.',

                    UPLOAD_ERR_FORM_SIZE =>
                        'The image is larger than the allowed form limit.',

                    UPLOAD_ERR_PARTIAL =>
                        'The image was only partially uploaded. Please try again.',

                    UPLOAD_ERR_NO_FILE =>
                        'No image was selected.',

                    UPLOAD_ERR_NO_TMP_DIR =>
                        'The server temporary upload folder is missing.',

                    UPLOAD_ERR_CANT_WRITE =>
                        'The server could not save the uploaded image.',

                    UPLOAD_ERR_EXTENSION =>
                        'A PHP extension stopped the image upload.',

                    default =>
                        'The receipt image failed to upload.',
                };

                Log::error('Mobile receipt upload failed', [
                    'token' => $token,
                    'upload_error_code' => $errorCode,
                    'upload_error_message' => $errorMessage,
                    'content_length' => $request->server('CONTENT_LENGTH'),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_code' => $errorCode,
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | File validation — maximum 20MB
            |--------------------------------------------------------------------------
            */

            $request->validate([
                'receipt' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,webp,heic,heif,pdf',
                    'max:20480',
                ],
            ], [
                'receipt.required' =>
                    'Please take or select a bill picture.',

                'receipt.file' =>
                    'The selected receipt is not a valid file.',

                'receipt.mimes' =>
                    'Only JPG, JPEG, PNG, WEBP, HEIC, HEIF or PDF files are allowed.',

                'receipt.max' =>
                    'The receipt image must not be larger than 20 MB.',

                'receipt.uploaded' =>
                    'The receipt could not upload because of the server upload limit.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Purani temporary receipt delete karein
            |--------------------------------------------------------------------------
            */

            if (
                $upload->file_path &&
                Storage::disk('public')->exists($upload->file_path)
            ) {
                Storage::disk('public')->delete(
                    $upload->file_path
                );
            }

            $extension = strtolower(
                $file->getClientOriginalExtension()
            );

            if (!$extension) {
                $extension = 'jpg';
            }

            $fileName = now()->format('YmdHis')
                . '_'
                . Str::random(16)
                . '.'
                . $extension;

            $path = $file->storeAs(
                'temporary-expense-receipts',
                $fileName,
                'public'
            );

            if (!$path) {
                throw new \RuntimeException(
                    'The server could not save the receipt image.'
                );
            }

            $upload->update([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'status' => 'uploaded',
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Bill picture successfully sent to computer.',
                'file_name' => $file->getClientOriginalName(),
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())
                    ->flatten()
                    ->first() ?? 'Receipt validation failed.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Throwable $exception) {
            Log::error('Mobile receipt upload exception', [
                'token' => $token,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' =>
                    'The server could not upload the receipt. Please try again.',
            ], 500);
        }
    }

    /**
     * PC har 2 second baad status check karega.
     */
    public function status(string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)
            ->firstOrFail();

        if (
            $upload->expires_at &&
            $upload->expires_at->isPast() &&
            $upload->status === 'pending'
        ) {
            $upload->update([
                'status' => 'expired',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $upload->status,
            'file_url' => $upload->file_path
                ? Storage::url($upload->file_path)
                : null,
            'original_name' => $upload->original_name,
            'mime_type' => $upload->mime_type,
        ]);
    }

    /**
     * Temporary uploaded picture remove karna.
     */
    public function destroy(string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)
            ->firstOrFail();

        if (
            $upload->file_path &&
            Storage::disk('public')->exists($upload->file_path)
        ) {
            Storage::disk('public')->delete(
                $upload->file_path
            );
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receipt removed.',
        ]);
    }
}
