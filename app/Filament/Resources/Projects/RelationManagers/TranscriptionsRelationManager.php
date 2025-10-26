<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Transcriptions\TranscriptionsResource;
use Filament\Actions\AssociateAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Forms\Components\Schema as ComponentsSchema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
class TranscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transcriptions';

    protected static ?string $relatedResource = TranscriptionsResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('audio_file_path')
                    ->label('Audio File')
                    ->required()
                    ->acceptedFileTypes(['audio/*'])
                    ->directory('transcriptions/audio')
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable()
                    ->previewable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'transcribing' => 'Transcribing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->disabledOn('create')
                    ->native(false),

                Textarea::make('transcription')
                    ->label('Transcription')
                    ->nullable()
                    ->rows(4)
                    ->disabledOn('create')
                    ->columnSpan('full'),

                Textarea::make('error_message')
                    ->label('Error Details')
                    ->nullable()
                    ->rows(3)
                    ->disabledOn('create')
                    ->columnSpan('full'),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false; //allow create/edit on View Page
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('downloadTranscription')
                    ->label('Download Transcription')
                    ->icon('heroicon-o-download')
                    ->visible(fn ($record) => $record->status === 'completed')
                    ->action(function () {
                        // Add your download logic here
                        return true;
                    })
                    ->successNotificationTitle('Download started'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data) : array {
                        $data['user_id'] = Auth::id();
                        $data['status'] = 'pending';
                        //project is automatically by the relation manager 
                        return $data;
                    }),
                AssociateAction::make()
                    ->preloadRecordSelect()
                    ->label('Associate existing transcription'),
            ]);
    }
}
