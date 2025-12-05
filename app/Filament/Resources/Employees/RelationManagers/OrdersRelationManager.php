<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use DateTime;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $title = 'Managed Projects';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-clipboard-document-list';

    /**
     * Helper function to safely format dates
     */
    private function formatDateSafely($date, $format = 'd M Y'): string
    {
        if (empty($date)) {
            return '-';
        }

        if ($date instanceof Carbon || $date instanceof DateTime) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (Exception $e) {
            return 'Invalid date';
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Information')
                    ->schema([
                        TextInput::make('number')
                            ->label('Project Number')
                            ->readOnly(),

                        Select::make('prospect_id')
                            ->relationship('prospect', 'name_event')
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->required()
                            ->readOnly(),

                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->required(),

                        DatePicker::make('closing_date')
                            ->label('Closing Date')
                            ->required(),

                        TextInput::make('pax')
                            ->label('Number of Attendees')
                            ->numeric()
                            ->required(),

                        Toggle::make('is_paid')
                            ->label('Payment Status')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-clock')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->columns(2),

                Section::make('Financial Overview')
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Total Package Price')
                            ->prefix('Rp')
                            ->disabled(),

                        TextInput::make('grand_total')
                            ->label('Final Price')
                            ->prefix('Rp')
                            ->disabled(),

                        TextInput::make('bayar')
                            ->label('Amount Paid')
                            ->prefix('Rp')
                            ->disabled(),

                        TextInput::make('sisa')
                            ->label('Outstanding Balance')
                            ->prefix('Rp')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'processing',
                        'danger' => 'cancelled',
                        'primary' => 'done',
                    ]),

                TextColumn::make('number')
                    ->label('Project #')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('prospect.name_event')
                    ->label('Event Name')
                    ->searchable()
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn (Order $record): string => $record->prospect?->name_event ?? ''),

                TextColumn::make('event_dates')
                    ->label('Event Dates')
                    ->getStateUsing(function (Order $record): string {
                        if (! $record->prospect) {
                            return 'No dates set';
                        }

                        $dates = [];

                        if ($record->prospect->date_lamaran) {
                            $formattedDate = $this->formatDateSafely($record->prospect->date_lamaran, 'd M');
                            $dates[] = "Engagement: {$formattedDate}";
                        }

                        if ($record->prospect->date_akad) {
                            $formattedDate = $this->formatDateSafely($record->prospect->date_akad, 'd M');
                            $dates[] = "Ceremony: {$formattedDate}";
                        }

                        if ($record->prospect->date_resepsi) {
                            $formattedDate = $this->formatDateSafely($record->prospect->date_resepsi, 'd M Y');
                            $dates[] = "Reception: {$formattedDate}";
                        }

                        return ! empty($dates) ? implode("\n", $dates) : 'No dates set';
                    })
                    ->wrap(),

                TextColumn::make('closing_date')
                    ->label('Closing Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Project Value')
                    ->money('IDR')
                    ->sortable()
                    ->alignment(Alignment::Right),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->getStateUsing(function (Order $record): string {
                        $paid = $record->bayar ?? 0;
                        $total = $record->grand_total ?? 0;

                        if ($total == 0) {
                            return '0%';
                        }

                        $percentage = min(round(($paid / $total) * 100), 100);

                        return $percentage.'%';
                    })
                    ->color(fn (Order $record): string => $record->is_paid
                            ? 'success'
                            : ($record->bayar > 0 ? 'warning' : 'danger')
                    )
                    ->alignment(Alignment::Center)
                    ->badge(),

                IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->multiple(),

                Filter::make('event_date')
                    ->schema([
                        Select::make('date_type')
                            ->label('Event Type')
                            ->options([
                                'closing_date' => 'Closing Date',
                                'date_resepsi' => 'Reception Date',
                                'date_akad' => 'Ceremony Date',
                                'date_lamaran' => 'Engagement Date',
                            ])
                            ->default('closing_date'),

                        DatePicker::make('from_date')
                            ->label('From'),

                        DatePicker::make('until_date')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dateField = $data['date_type'] ?? 'closing_date';

                        if ($dateField === 'closing_date') {
                            return $query
                                ->when(
                                    $data['from_date'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate('closing_date', '>=', $date),
                                )
                                ->when(
                                    $data['until_date'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate('closing_date', '<=', $date),
                                );
                        } else {
                            return $query
                                ->when(
                                    $data['from_date'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereHas(
                                        'prospect',
                                        fn (Builder $query) => $query->whereDate($dateField, '>=', $date)
                                    ),
                                )
                                ->when(
                                    $data['until_date'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereHas(
                                        'prospect',
                                        fn (Builder $query) => $query->whereDate($dateField, '<=', $date)
                                    ),
                                );
                        }
                    }),

                Filter::make('payment_status')
                    ->schema([
                        Select::make('payment_status')
                            ->options([
                                'paid' => 'Fully Paid',
                                'partial' => 'Partially Paid',
                                'unpaid' => 'Unpaid',
                            ])
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['payment_status'] ?? null) {
                            'paid' => $query->where('is_paid', true),
                            'partial' => $query->where('is_paid', false)->whereRaw('bayar > 0'),
                            'unpaid' => $query->whereRaw('COALESCE(bayar, 0) = 0'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', ['record' => $record])),

                    Action::make('generate_invoice')
                        ->label('Generate Invoice')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (Order $record): string => route('invoice.show', $record))
                        ->openUrlInNewTab()
                        ->color('success'),
                ])
                    ->size('sm'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('update_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Select::make('status')
                                ->label('New Status')
                                ->options(OrderStatus::class)
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Action::make('view_all_projects')
                    ->label('View All Projects')
                    ->url(fn (): string => route('filament.admin.resources.orders.index'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('closing_date', 'desc')
            ->poll('60s');
    }
}
