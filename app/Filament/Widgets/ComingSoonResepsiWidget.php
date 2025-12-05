<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ComingSoonResepsiWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Coming Soon Resepsi';

    protected static ?int $sort = 41;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderResource::getEloquentQuery()
                    ->whereHas('prospect', function (Builder $query) {
                        $query->whereNotNull('date_resepsi')
                            ->where('date_resepsi', '>=', now());
                    })
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('prospect.date_resepsi', 'asc')
            ->columns([
                TextColumn::make('prospect.date_resepsi')
                    ->label('Tgl Resepsi')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('prospect.name_event')
                    ->label('Prospect')
                    ->label('Nama event')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->numeric()
                    ->label('Account Manager')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('items.product.name')
                //     ->label('Product')
                //     ->sortable()
                //     ->wrap(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
                ]),
            ]);
    }
}
