<?php

namespace App\Filament\Resources\Blogs\Widgets;

use App\Models\Blog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Articles', Blog::count())
                ->description('All blog articles')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Published', Blog::where('is_published', true)->count())
                ->description('Live articles')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Featured', Blog::where('is_featured', true)->count())
                ->description('Featured articles')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Views', Blog::sum('views_count'))
                ->description('All time views')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Avg Read Time', round(Blog::avg('read_time') ?? 0, 1).' min')
                ->description('Average reading time')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('Categories', Blog::distinct('category')->count('category'))
                ->description('Different categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('purple'),
        ];
    }
}
