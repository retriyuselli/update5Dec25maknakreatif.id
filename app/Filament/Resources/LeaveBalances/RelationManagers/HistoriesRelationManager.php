<?php

namespace App\Filament\Resources\LeaveBalances\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $title = 'Riwayat Top Up';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('Jumlah Hari'),
                Forms\Components\DatePicker::make('transaction_date')
                    ->required()
                    ->label('Tanggal'),
                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->label('Alasan'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reason')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->color('success')
                    ->formatStateUsing(fn ($state) => "+$state")
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Oleh'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Disetujui',
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->roles->contains('name', 'super_admin'))
                    ->action(function ($record) {
                        $record->status = 'approved';
                        $record->save();
                        
                        // Update balance
                        $balance = $record->leaveBalance;
                        $balance->allocated_days += $record->amount;
                        $balance->save();
                        
                        Notification::make()
                            ->title('Top Up Disetujui')
                            ->body("Saldo cuti user bertambah {$record->amount} hari.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->roles->contains('name', 'super_admin'))
                    ->action(function ($record) {
                        $record->status = 'rejected';
                        $record->save();
                        
                        Notification::make()
                            ->title('Top Up Ditolak')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                DeleteAction::make()
                    ->visible(fn () => Auth::user()->roles->contains('name', 'super_admin')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
