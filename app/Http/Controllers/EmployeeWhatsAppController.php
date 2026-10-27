<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class EmployeeWhatsAppController extends Controller
{
    public function send(Request $request, Employee $employee)
    {
        abort_unless($request->user()->hasPermission('employees'), 403);
        $data = $request->validate(['message' => 'required|string|max:4000']);
        $message = trim($data['message']);
        if ($message === '') return response()->json(['message'=>'Write a message first.'],422);
        $raw = trim((string)$employee->phone);
        $number = preg_replace('/\D/', '', $raw);
        if (str_starts_with($number, '00')) $number = substr($number, 2);
        if (preg_match('/^03\d{9}$/', $number)) $number = '92'.substr($number,1);
        elseif (preg_match('/^3\d{9}$/', $number)) $number = '92'.$number;
        if (!preg_match('/^[1-9]\d{7,14}$/', $number) || !(str_starts_with($raw,'+') || str_starts_with($raw,'00') || preg_match('/^92\d{10}$/',$number))) {
            return response()->json(['message'=>'Update the employee phone number with country code.'],422);
        }
        $token = config('services.whatsapp.token');
        $id = (string)config('services.whatsapp.phone_number_id');
        $version = (string)config('services.whatsapp.version');
        if (!$token || !preg_match('/^\d+$/',$id) || !preg_match('/^v\d+\.\d+$/',$version)) {
            return response()->json(['message'=>'WhatsApp is not configured. Set the server environment values.'],503);
        }
        try {
            // Never retry a send automatically: a timeout can occur after Meta accepts it.
            $response = Http::withToken($token)->acceptJson()->connectTimeout(5)->timeout(20)
                ->post("https://graph.facebook.com/{$version}/{$id}/messages", [
                    'messaging_product'=>'whatsapp', 'to'=>$number, 'type'=>'text',
                    'text'=>['preview_url'=>false, 'body'=>$message],
                ]);
        } catch (ConnectionException $e) {
            return response()->json(['message'=>'Connection timed out. Delivery is uncertain; check WhatsApp before sending again.'],504);
        }
        if (!$response->successful()) {
            $code = (int)$response->json('error.code');
            $error = match ($code) {
                190 => 'WhatsApp token expired or is invalid. Update the server token.',
                131030 => 'Add and verify this recipient in Meta test settings first.',
                131047 => 'Ask the employee to reply on WhatsApp first. Outside 24 hours an approved template is required.',
                default => 'WhatsApp rejected the request (code '.$code.'). Check Meta configuration and recipient.',
            };
            return response()->json(['message'=>$error],422);
        }
        $messageId = $response->json('messages.0.id');
        if (!$messageId) return response()->json(['message'=>'Meta did not return a message ID. Check WhatsApp before retrying.'],502);
        return response()->json(['message'=>'Submitted to WhatsApp. Delivery is not yet confirmed.', 'id'=>$messageId]);
    }
}
