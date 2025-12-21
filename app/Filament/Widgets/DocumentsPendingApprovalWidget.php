<?php

namespace App\Filament\Widgets;

use App\Models\DocumentApproval;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class DocumentsPendingApprovalWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Documents Pending My Approval';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DocumentApproval::query()
                    ->where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->with(['document', 'document.category', 'document.creator'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('document.document_number')
                    ->label('Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document.title')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document.category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('document.creator.name')
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received At')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->url(fn (DocumentApproval $record): string => route('filament.admin.resources.documents.edit', ['record' => $record->document_id]))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
