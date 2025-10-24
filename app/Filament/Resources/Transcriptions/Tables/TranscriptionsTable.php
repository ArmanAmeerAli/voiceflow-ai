<?php

namespace App\Filament\Resources\Transcriptions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;

class TranscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->native(false),

                Filter::make('created_at')
                    ->label('Date Added')
                    ->form([
                        DatePicker::make('created_from')->label('From'),
                        DatePicker::make('created_until')->label('Until'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'From: ' . \Illuminate\Support\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Until: ' . \Illuminate\Support\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    })
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, 
                            function ($q, $date) {
                                return $q->where('created_at', '>=', $date);
                            })
                            ->when($data['created_until'] ?? null, 
                            function ($q, $date) {
                                return $q->where('created_at', '<=', $date);
                            });
                    }),
            ])
            ->recordActions([
                Action::make('downloadTranscription')
                    ->label('Download Transcription')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn ($record) => $record->status === 'completed')
                    ->action(function () {
                        // dummy action for now
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
