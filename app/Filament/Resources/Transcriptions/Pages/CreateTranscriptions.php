<?php

namespace App\Filament\Resources\Transcriptions\Pages;

use App\Filament\Resources\Transcriptions\TranscriptionsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessTranscriptions;

class CreateTranscriptions extends CreateRecord
{
    protected static string $resource = TranscriptionsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        ProcessTranscriptions::dispatch($this->record);
    }
}
