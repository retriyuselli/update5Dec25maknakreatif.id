<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class EventManager extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Event Manager Performance Dashboard';

    protected static ?int $sort = 13;

    protected static ?int $contentHeight = 400;

    protected int $pageSize = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()
                    ->where('position', 'Event Manager')
                    ->withCount('orders as events_count')
                    ->orderBy('events_count', 'desc')
            )
            ->columns([
                ImageColumn::make('photo')
                    ->label('Profile')
                    ->defaultImageUrl(function ($record) {
                        // Generate default avatar based on employee's name initials
                        $name = $record->name ?? 'Employee';
                        $initials = collect(explode(' ', $name))
                            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->implode('');

                        // Fallback if no initials
                        if (empty($initials)) {
                            $initials = strtoupper(substr($name, 0, 2));
                        }

                        // Use UI Avatars service to generate default avatar
                        return 'https://ui-avatars.com/api/?'.http_build_query([
                            'name' => $initials,
                            'size' => 128,
                            'background' => '059669', // Green color for Event Managers
                            'color' => 'ffffff',
                            'font-size' => 0.6,
                            'rounded' => true,
                            'bold' => true,
                            'format' => 'svg',
                        ]);
                    })
                    ->circular(),

                TextColumn::make('name')
                    ->label('Event Manager')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('events_count')
                    ->label('Total Events')
                    ->numeric()
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('email')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->toggleable(),

                TextColumn::make('date_of_join')
                    ->label('Join Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('salary')
                    ->label('Salary')
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tables\Columns\IconColumn::make('status')
                //     ->label('Status')
                //     ->boolean()
                //     ->trueIcon('heroicon-o-check-circle')
                //     ->falseIcon('heroicon-o-x-circle')
                //     ->trueColor('success')
                //     ->falseColor('danger')
                //     ->getStateUsing(fn ($record): bool =>
                //         !$record->date_of_out && $record->date_of_join),
            ])

            ->recordActions([

            ])
            ->toolbarActions([
                BulkAction::make('export')
                    ->label('Export Selected')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Collection $records) {
                        $filename = 'event_managers_'.now()->format('Ymd_His').'.csv';

                        return response()->streamDownload(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['Name', 'Email', 'Phone', 'Events Count', 'Join Date']);

                            foreach ($records as $employee) {
                                $eventsCount = $employee->events_count ?? $employee->orders()->count();
                                $joinDate = $employee->date_of_join ? \Carbon\Carbon::parse($employee->date_of_join)->format('Y-m-d') : '';

                                fputcsv($handle, [
                                    $employee->name,
                                    $employee->email,
                                    $employee->phone,
                                    $eventsCount,
                                    $joinDate,
                                ]);
                            }

                            fclose($handle);
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
            ])
            ->paginated([3, 6, 12])
            ->defaultSort('events_count', 'desc')
            ->poll('30s');
    }
}
