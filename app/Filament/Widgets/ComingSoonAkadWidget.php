<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ComingSoonAkadWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Coming Soon Akad';

    protected static ?int $sort = 40;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderResource::getEloquentQuery()
                    ->whereHas('prospect', function (Builder $query) {
                        $query->whereNotNull('date_akad')
                            ->where('date_akad', '>=', now());
                    })
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('prospect.date_akad', 'asc')
            ->columns([
                TextColumn::make('prospect.date_akad')
                    ->label('Tgl Akad')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('prospect.name_event')
                    ->label('Nama Event')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Account Manager')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
                ]),
            ]);
    }
}
