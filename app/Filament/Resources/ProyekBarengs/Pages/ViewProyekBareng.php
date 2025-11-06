<?php

namespace App\Filament\Resources\ProyekBarengs\Pages;

use App\Filament\Resources\ProyekBarengs\ProyekBarengResource;
use App\Services\WhatsAppService;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class ViewProyekBareng extends ViewRecord
{
    protected static string $resource = ProyekBarengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendWhatsAppMessage')
                ->label('Kirim WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color(Color::Green)
                ->form([
                    Textarea::make('message')
                        ->label('Pesan')
                        ->placeholder('Tulis pesan yang akan dikirim ke anggota tim...')
                        ->required()
                        ->rows(5)
                        ->maxLength(1000)
                        ->helperText('Pesan akan dikirim ke semua anggota yang sudah disetujui dan memiliki nomor WhatsApp yang valid.'),
                    
                    Toggle::make('send_to_all')
                        ->label('Kirim ke Semua Anggota')
                        ->helperText('Jika dinonaktifkan, hanya akan dikirim ke anggota yang sudah disetujui')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $this->sendWhatsAppToMembers($data['message'], $data['send_to_all']);
                })
                ->modalHeading('Kirim Pesan WhatsApp ke Anggota Tim')
                ->modalDescription('Pesan akan dikirim ke anggota tim proyek melalui WhatsApp.')
                ->modalSubmitActionLabel('Kirim Pesan')
                ->modalWidth('lg')
                ->visible(fn (): bool => $this->record->users()->count() > 0),
                
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    private function sendWhatsAppToMembers(string $message, bool $sendToAll = false): void
    {
        $users = $sendToAll 
            ? $this->record->users
            : $this->record->users()->wherePivot('is_approved', true)->get();

        if ($users->isEmpty()) {
            Notification::make()
                ->title('Tidak ada anggota')
                ->body($sendToAll ? 'Tidak ada anggota dalam proyek ini.' : 'Tidak ada anggota yang disetujui dalam proyek ini.')
                ->warning()
                ->send();
            return;
        }

        $sentCount = 0;
        $failedCount = 0;
        $invalidNumbers = [];

        foreach ($users as $user) {
            if (empty($user->whatsapp)) {
                $failedCount++;
                continue;
            }

            try {
                $fullMessage = "📢 *Pesan dari Proyek: {$this->record->title}*\n\n" . 
                    "{$message}\n\n" .
                    "Detail Proyek:\nhttps://baricode.org/proyek-bareng/{$this->record->id}";

                $response = WhatsAppService::sendMessage($user->whatsapp, $fullMessage);
                
                if ($response->successful()) {
                    $sentCount++;
                } else {
                    $failedCount++;
                    $invalidNumbers[] = $user->name . " ({$user->whatsapp})";
                }
            } catch (\Exception $e) {
                $failedCount++;
                $invalidNumbers[] = $user->name . " ({$user->whatsapp})";
            }
        }

        // Send notification about results
        if ($sentCount > 0) {
            Notification::make()
                ->title('Pesan berhasil dikirim!')
                ->body("Berhasil mengirim pesan ke {$sentCount} anggota." . 
                      ($failedCount > 0 ? " {$failedCount} pesan gagal dikirim." : ""))
                ->success()
                ->send();
        }

        if ($failedCount > 0 && !empty($invalidNumbers)) {
            Notification::make()
                ->title('Beberapa pesan gagal dikirim')
                ->body("Gagal mengirim ke: " . implode(', ', array_slice($invalidNumbers, 0, 3)) . 
                      (count($invalidNumbers) > 3 ? ' dan ' . (count($invalidNumbers) - 3) . ' lainnya.' : '.'))
                ->warning()
                ->send();
        }

        if ($sentCount === 0) {
            Notification::make()
                ->title('Tidak ada pesan yang terkirim')
                ->body('Semua anggota tidak memiliki nomor WhatsApp yang valid atau tidak dapat dihubungi.')
                ->danger()
                ->send();
        }
    }
}