<?php

namespace App\Notifications;

use App\Models\Habits\Habit;
use App\Models\Habits\HabitSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HabitReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Habit $habit,
        public HabitSchedule $schedule
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        
        // Add WhatsApp channel if user has WhatsApp number
        if (!empty($notifiable->whatsapp)) {
            // Note: You would need to implement WhatsApp channel
            // For now, we'll just log it
            \Log::info("WhatsApp reminder would be sent to: " . $notifiable->whatsapp);
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $habitUrl = route('satu-tapak.habits.show', $this->habit);
        
        return (new MailMessage)
            ->subject('🔔 Reminder: ' . $this->habit->name)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Ini adalah pengingat untuk habit "' . $this->habit->name . '"')
            ->line('Jadwal: ' . $this->schedule->day_name . ' pada ' . $this->schedule->formatted_time)
            ->when($this->habit->description, function ($mail) {
                return $mail->line('Deskripsi: ' . $this->habit->description);
            })
            ->line('Jangan lupa untuk melakukan log aktivitas Anda hari ini!')
            ->action('Buka Habit Tracker', $habitUrl)
            ->line('Tetap semangat dan konsisten! 💪')
            ->salutation('Salam, Tim Baricode Community');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'habit_id' => $this->habit->id,
            'habit_name' => $this->habit->name,
            'schedule_day' => $this->schedule->day_of_week,
            'schedule_time' => $this->schedule->scheduled_time,
            'message' => 'Reminder untuk habit: ' . $this->habit->name,
        ];
    }

    /**
     * Get WhatsApp message (placeholder for future implementation)
     */
    public function toWhatsApp(object $notifiable): string
    {
        return "🔔 *Reminder Habit*\n\n" .
               "Halo {$notifiable->name}!\n\n" .
               "Ini pengingat untuk habit: *{$this->habit->name}*\n" .
               "Jadwal: {$this->schedule->day_name} pada {$this->schedule->formatted_time}\n\n" .
               "Jangan lupa log aktivitas Anda hari ini!\n\n" .
               "💪 Tetap semangat dan konsisten!";
    }
}