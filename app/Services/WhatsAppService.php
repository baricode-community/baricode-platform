<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private static $apiUrl = 'https://api.starsender.online/api/';

    private static function sendRequest($endpoint, $data): Response
    {
        logger()->info('Sending request to WhatsApp API', [
            'endpoint' => $endpoint,
            'data' => $data,
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => env('WHATSAPP_API_KEY'),
        ])->post(self::$apiUrl . $endpoint, $data);

        logger()->info('WhatsApp API response received', [
            'endpoint' => $endpoint,
            'data' => $data,
            'response' => $response->body(),
        ]);

        return $response;
    }

    public static function isValidNumber($number): bool
    {
        $data = ['number' => $number];
        $response = self::sendRequest('check-number', $data);
        return $response->json()['data']['status'];
    }

    public static function sendMessage($to, $message)
    {
        $data = [
            'number' => $to,
            'message' => $message
        ];
        
        return self::sendRequest('send-message', $data);
    }

    /**
     * Send attendance reminder notification
     */
    public static function sendAttendanceReminder($phoneNumber, $studentName, $courseName, $reminderTime): bool
    {
        $message = "🔔 *Pengingat Kelas*\n\n";
        $message .= "Halo *{$studentName}*! 👋\n\n";
        $message .= "⏰ Waktu untuk mengikuti kelas *{$courseName}*\n";
        $message .= "🕐 Jam: {$reminderTime}\n\n";
        $message .= "Jangan lupa untuk hadir tepat waktu! 📚\n";
        $message .= "Silakan melakukan absensi sekarang.\n\n";
        $message .= "Semangat belajar! 💪";
        
        return self::sendMessage($phoneNumber, $message);
    }
}
