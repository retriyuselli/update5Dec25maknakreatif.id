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

class RevenueBulananWeddingWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Revenue Wedding per-Bulan';

    protected static ?int $sort = 21;

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->year.'-'.str_pad($record->month, 2, '0', STR_PAD_LEFT);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->join('prospects', 'orders.prospect_id', '=', 'prospects.id')
                    ->selectRaw('
                        MONTH(prospects.date_resepsi) as month,
                        MONTHNAME(prospects.date_resepsi) as month_name,
                        YEAR(prospects.date_resepsi) as year,
                        COUNT(DISTINCT CASE WHEN prospects.date_resepsi IS NOT NULL THEN orders.id END) as resepsi_count,
                        COUNT(DISTINCT CASE WHEN prospects.date_akad IS NOT NULL THEN orders.id END) as akad_count,
                        COUNT(DISTINCT CASE WHEN prospects.date_lamaran IS NOT NULL THEN orders.id END) as lamaran_count,
                        SUM(orders.total_price + COALESCE(orders.penambahan, 0) - COALESCE(orders.promo, 0) - COALESCE(orders.pengurangan, 0)) as total_revenue
                    ')
                    ->whereNotNull('prospects.date_resepsi')
                    ->groupBy('year', 'month', 'month_name')
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
                TextColumn::make('resepsi_count')
                    ->label('Resepsi')
                    ->alignCenter()
                    ->sortable()
                    ->summarize(Sum::make()),
                TextColumn::make('akad_count')
                    ->label('Akad')
                    ->sortable()
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('lamaran_count')
                    ->label('Lamaran')
                    ->alignCenter()
                    ->sortable()
                    ->summarize(Sum::make()),
                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->alignCenter()
                    ->money('IDR')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => 'IDR '.number_format($state, 0, ',', '.'))
                    )
                    ->alignEnd(),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(
                        Order::query()
                            ->join('prospects', 'orders.prospect_id', '=', 'prospects.id')
                            ->whereNotNull('prospects.date_resepsi')
                            ->selectRaw('YEAR(prospects.date_resepsi) as year')
                            ->distinct()
                            ->pluck('year', 'year')
                            ->sortByDesc('year')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $year): Builder => $query->whereYear('prospects.date_resepsi', $year)
                        );
                    }),
            ])
            ->paginated([6, 12, 25, 50])
            ->defaultSort('month', 'desc');
    }
}
