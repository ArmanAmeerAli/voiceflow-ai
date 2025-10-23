<?php

namespace App\Filament\Resources\Transcriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use App\Models\Transcriptions;

class TranscriptionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                            $set('slug', Str::slug($state));
                        }),

                FileUpload::make('audio_file_path')
                    ->label('Audio File')
                    ->required()
                    ->acceptedFileTypes(['audio/*'])
                    ->directory('transcriptions/audio')
                    ->maxSize(10240) //10MB
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->helperText('Make file size: 10MB. Supported formats: .mp3, .wav, .ogg'),

                Select::make('status')
                    ->options(Transcriptions::getStatusOptions())
                    ->default('pending')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->in(Transcriptions::getStatusOptions())
                    ->hidden(fn (string $operation) => $operation !== 'edit'),

                Textarea::make('transcription')
                    ->label('Transcription Text')
                    ->nullable()
                    ->rows(5)
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->hidden(fn (string $operation) => $operation !== 'edit'),

                Textarea::make('error_message')
                    ->label('Error Message')
                    ->nullable()
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->hidden(fn (string $operation) => $operation !== 'edit'),
            ]);

    }
}
