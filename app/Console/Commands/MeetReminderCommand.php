<?php

namespace App\Console\Commands;

use App\Models\Communication\Meet;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class MeetReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:meet-reminder-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meets = Meet::where('scheduled_at', '=', now()->addHours(2))->get();
        $meets->each(function ($meet) {
            $meet->users->each(function ($user) use ($meet) {
                \Log::info("Reminder sent to User ID: {$user->id} for Meet ID: {$meet->id}");

                WhatsAppService::sendMessage(
                    $user->phone_number,
                    "👋 Halo {$user->name}, ini adalah pengingat untuk pertemuan berjudul '{$meet->title}' yang dijadwalkan pada {$meet->scheduled_at->format('Y-m-d H:i:s')}.\n📅 Silakan bergabung dengan memantaunya via grup. 👍"
                );
            });
        });
    }
}
