<?php

namespace App\Filament\Resources\TaskSubmissions\Pages;

use App\Filament\Resources\TaskSubmissions\TaskSubmissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskSubmission extends CreateRecord
{
    protected static string $resource = TaskSubmissionResource::class;
}
