<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transcriptions;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class AllTranscriptions extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $totalTranscriptions = Transcriptions::where('user_id', $user->id)->count();
        $totalProjects = Project::where('user_id', $user->id)->count();
        return [
            Stat::make('Total Transcriptions', $totalTranscriptions)
                ->description('Total Transcriptions you have generated')
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Total Projects', $totalProjects)
                ->description('Total Projects you have generated')
                ->icon('heroicon-o-document-text')
                ->color('success'),
        ];
    }
}
