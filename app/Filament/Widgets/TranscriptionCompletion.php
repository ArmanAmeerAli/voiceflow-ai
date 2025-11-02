<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transcriptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TranscriptionCompletion extends ChartWidget
{
    protected ?string $heading = 'Transcription Status Overview';
    protected static ?int $sort = 2;
    public ?string $filter = 'today';

    protected function getData(): array
    {
        $user = Auth::user();
        $activeFilter = $this->filter;

        //Determine the data range based on the active filter
        $query = Transcriptions::where('user_id', $user->id);

        switch ($activeFilter) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'last_week':
                $query->whereBetween('created_at', [
                    Carbon::now()->subWeek()->startOfweek(),
                    Carbon::now()->subWeek()->endOfweek()
                ]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', Carbon::now()->subMonth()->year);
                break;
            case 'all':
            default:
                // No date filter, show all transcriptions
                break;
        }

        // Get count of transcriptions by status for the authenticated user
        $pending = (clone $query)
            ->where('status', 'pending')
            ->count();
        $processing = (clone $query)
            ->where('status', 'processing')
            ->count();
        $completed = (clone $query)
            ->where('status', 'completed')
            ->count();
        $failed = (clone $query)
            ->where('status', 'failed')
            ->count();

        return [
            'datasets' => [
                [
                    'data' => [$pending, $processing, $completed, $failed],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)', //Blue for pending
                        'rgb(251, 191, 36)', //Amber for processing
                        'rgb(34, 197, 94)' , //Green for completed
                        'rgb(239, 68, 68)' , //Red for failed
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(251, 191, 36)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pending', 'Processing', 'Completed', 'Failed'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getFilters(): ?array
    {
        return [
            'Today' => 'today',
            'yesterday' => 'Yesterday',
            'week' => 'This Week',
            'last_week' => 'Last Week',
            'month' => 'This Month',
            'last_month' => 'Last Month',
            'all' => 'All Time',
        ];
    }

    protected function getOptions(): array 
    {
        return [
            'plugin' => [
                'lengend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
