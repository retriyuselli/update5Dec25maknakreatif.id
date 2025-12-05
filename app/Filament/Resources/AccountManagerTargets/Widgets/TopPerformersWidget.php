<?php

namespace App\Filament\Resources\AccountManagerTargets\Widgets;

use App\Models\AccountManagerTarget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopPerformersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Top Performers';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AccountManagerTarget::query()
                    ->with(['user'])
                    ->whereHas('user.roles', function ($query) {
                        $query->where('name', 'Account Manager');
                    })
                    ->whereHas('user', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->orderByDesc('achieved_amount')
            )
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        $years = AccountManagerTarget::selectRaw('DISTINCT year')
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->toArray();

                        return $years;
                    })
                    ->default(now()->year)
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            $query->where('year', $data['value']);
                        }
                    }),

                SelectFilter::make('month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->default(now()->month)
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            $query->where('month', $data['value']);
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->getStateUsing(function ($rowLoop) {
                        return $rowLoop->iteration;
                    })
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'warning',  // Gold
                        2 => 'gray',     // Silver
                        3 => 'success',  // Bronze
                        default => 'primary',
                    }),

                TextColumn::make('user.name')
                    ->label('Account Manager')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('target_amount')
                    ->label('Target')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('achieved_amount')
                    ->label('Achievement')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->color('success'),

                TextColumn::make('achievement_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->alignCenter()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 80 => 'warning',
                        $state >= 60 => 'info',
                        default => 'danger',
                    })
                    ->weight('bold'),

                TextColumn::make('calculated_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'overachieved' => 'info',
                        'achieved' => 'success',
                        'partially achieved' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->alignCenter(),

                TextColumn::make('remaining_target')
                    ->label('Remaining')
                    ->money('IDR')
                    ->alignEnd()
                    ->color(fn (float $state): string => $state <= 0 ? 'success' : 'warning'),
            ])
            ->defaultSort('achieved_amount', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
