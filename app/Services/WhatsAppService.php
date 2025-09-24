<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private $apiUrl;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        logger()->info('WhatsAppService initialized.');

        // Initialize any required properties or services here
        $this->apiUrl = 'https://api.starsender.online/api/';
    }

    private function sendRequest($endpoint, $data): bool
    {
        logger()->info('Sending request to WhatsApp API', [
            'endpoint' => $endpoint,
            'data' => $data,
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => env('WHATSAPP_API_KEY'),
        ])->post($this->apiUrl . $endpoint, $data);

        logger()->info('WhatsApp API response received', [
            'endpoint' => $endpoint,
            'data' => $data,
            'response' => $response->body(),
        ]);

        return $response->successful();
    }

    public function sendMessage($to, $message): bool
    {
        $data = [
            'number' => $to,
            'message' => $message
        ];
        
        return $this->sendRequest('send-message', $data);
    }

    /**
     * Send attendance reminder notification
     */
    public function sendAttendanceReminder($phoneNumber, $studentName, $courseName, $reminderTime): bool
    {
        $message = "🔔 *Pengingat Kelas*\n\n";
        $message .= "Halo *{$studentName}*! 👋\n\n";
        $message .= "⏰ Waktu untuk mengikuti kelas *{$courseName}*\n";
        $message .= "🕐 Jam: {$reminderTime}\n\n";
        $message .= "Jangan lupa untuk hadir tepat waktu! 📚\n";
        $message .= "Silakan melakukan absensi sekarang.\n\n";
        $message .= "Semangat belajar! 💪";
        
        return $this->sendMessage($phoneNumber, $message);
    }
}
