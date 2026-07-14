<?php

namespace App\Http\Controllers;

use App\Models\ExpenseReceiptUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'mobile_url' => route('expense.receipt.mobile', $upload->token),
            'expires_at' => $upload->expires_at?->toDateTimeString(),
        ]);
    }

    /**
     * Mobile camera/upload page.
     */
    public function mobilePage(string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)->firstOrFail();

        if ($upload->expires_at && $upload->expires_at->isPast()) {
            if ($upload->status !== 'completed') {
                $upload->update([
                    'status' => 'expired',
                ]);
            }

            return view('expenses.mobile-receipt-expired');
        }

        return view('expenses.mobile-receipt-upload', compact('upload'));
    }

    /**
     * Mobile se bill photo upload hogi.
     */
    public function uploadFromMobile(Request $request, string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)->firstOrFail();

        if ($upload->expires_at && $upload->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This upload link has expired.',
            ], 422);
        }

        $request->validate([
            'receipt' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],
        ]);

        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $file = $request->file('receipt');

        $fileName = now()->format('YmdHis')
            . '_'
            . Str::random(10)
            . '.'
            . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'temporary-expense-receipts',
            $fileName,
            'public'
        );

        $upload->update([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'status' => 'uploaded',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bill picture successfully sent to computer.',
        ]);
    }

    /**
     * PC har 2 second baad status check karega.
     */
    public function status(string $token)
    {
        $upload = ExpenseReceiptUpload::where('token', $token)->firstOrFail();

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
        $upload = ExpenseReceiptUpload::where('token', $token)->firstOrFail();

        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receipt removed.',
        ]);
    }
}
