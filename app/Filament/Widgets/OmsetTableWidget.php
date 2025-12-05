<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OmsetTableWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Tabel Closing per-Bulan';

    protected static ?int $sort = 20;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->whereNotNull('closing_date')
                    ->selectRaw('
                        DATE_FORMAT(closing_date, "%m") as month,
                        DATE_FORMAT(closing_date, "%M") as month_name,
                        YEAR(closing_date) as year,
                        CONCAT(DATE_FORMAT(closing_date, "%m"), "-", YEAR(closing_date)) as month_year_key,
                        SUM(total_price + COALESCE(penambahan, 0) - COALESCE(promo, 0) - COALESCE(pengurangan, 0)) as total_omset,
                        COUNT(*) as total_orders
                    ')
                    ->groupBy('month', 'month_name', 'year', 'month_year_key')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
            )
            ->columns([
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('month_name')
                    ->label('Bulan')
                    ->formatStateUsing(fn ($state) => __($state))
                    ->sortable(),

                TextColumn::make('total_omset')
                    ->label('Revenue')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => 'IDR '.number_format($state, 0, ',', '.'))
                    ),

                TextColumn::make('total_orders')
                    ->label('Jumlah Project')
                    ->sortable()
                    ->summarize(Sum::make())
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(
                        Order::query()
                            ->whereNotNull('closing_date')
                            ->selectRaw('YEAR(closing_date) as year')
                            ->distinct()
                            ->pluck('year', 'year')
                            ->sortByDesc('year')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $year): Builder => $query->whereYear('closing_date', $year)
                        );
                    }),
            ])
            ->paginated([6, 12, 25, 50])
            ->recordUrl(null)
            ->defaultSort('month', 'asc');
    }

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->month_year_key;
    }
}
