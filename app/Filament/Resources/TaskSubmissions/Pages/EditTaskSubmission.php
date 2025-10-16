<?php

namespace App\Filament\Resources\TaskSubmissions\Pages;

use App\Filament\Resources\TaskSubmissions\TaskSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskSubmission extends EditRecord
{
    protected static string $resource = TaskSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
