<?php

namespace App\Filament\Widgets;

use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use App\Models\InternalMessage;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InternalMessageResourceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Recent Internal Messages';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InternalMessage::query()
                    ->with(['sender', 'replies'])
                    ->where(function (Builder $query) {
                        $userId = Auth::id();
                        // Messages where user is recipient or sender
                        $query->where('sender_id', $userId)
                            ->orWhereJsonContains('recipient_ids', $userId)
                            ->orWhereJsonContains('cc_ids', $userId)
                            ->orWhere('is_public', true);
                    })
                    ->whereNull('parent_id') // Only parent messages, not replies
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                IconColumn::make('type_icon')
                    ->label('')
                    ->icon(fn (InternalMessage $record): string => match ($record->type) {
                        'instruction' => 'heroicon-o-academic-cap',
                        'announcement' => 'heroicon-o-megaphone',
                        'task' => 'heroicon-o-clipboard-document-check',
                        'reminder' => 'heroicon-o-bell-alert',
                        'feedback' => 'heroicon-o-chat-bubble-left-right',
                        'urgent' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-chat-bubble-left',
                    })
                    ->color(fn (InternalMessage $record): string => match ($record->type) {
                        'urgent' => 'danger',
                        'task' => 'warning',
                        'announcement' => 'info',
                        'instruction' => 'primary',
                        default => 'gray',
                    })
                    ->size('sm'),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(40)
                    ->searchable()
                    ->weight('medium')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 40 ? $state : null;
                    })
                    ->formatStateUsing(function (string $state, InternalMessage $record): string {
                        $prefix = '';
                        if ($record->is_pinned) {
                            $prefix .= '📌 ';
                        }
                        if ($record->requires_response) {
                            $prefix .= '❗ ';
                        }

                        return $prefix.$state;
                    }),

                TextColumn::make('sender.name')
                    ->label('From')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, InternalMessage $record): string => $record->sender_id === Auth::id() ? 'Me' : $state
                    ),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'low' => 'heroicon-o-minus',
                        'normal' => 'heroicon-o-equals',
                        'high' => 'heroicon-o-arrow-up',
                        'urgent' => 'heroicon-o-fire',
                        default => 'heroicon-o-minus',
                    }),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'instruction' => 'primary',
                        'communication' => 'info',
                        'announcement' => 'success',
                        'task' => 'warning',
                        'reminder' => 'gray',
                        'feedback' => 'purple',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('thread_count')
                    ->label('Replies')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state}" : '0'),

                TextColumn::make('read_status')
                    ->label('Status')
                    ->badge()
                    ->color(function (InternalMessage $record): string {
                        $userId = Auth::id();
                        $readBy = $record->read_by ?? [];

                        return in_array($userId, $readBy) ? 'success' : 'warning';
                    })
                    ->formatStateUsing(function (InternalMessage $record): string {
                        $userId = Auth::id();
                        $readBy = $record->read_by ?? [];

                        return in_array($userId, $readBy) ? 'Read' : 'Unread';
                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->since()
                    ->sortable()
                    ->tooltip(fn (InternalMessage $record): string => $record->created_at->format('M j, Y g:i A')
                    ),

                TextColumn::make('due_date')
                    ->label('Due')
                    ->date('M j, Y')
                    ->placeholder('No due date')
                    ->color(function (InternalMessage $record): string {
                        if (! $record->due_date) {
                            return 'gray';
                        }

                        return $record->due_date->isPast() ? 'danger' : 'success';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View Message')
                    ->modalHeading(fn (InternalMessage $record): string => $record->subject)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->fillForm(fn (InternalMessage $record): array => [
                        'subject' => $record->subject,
                        'sender_name' => $record->sender->name ?? 'Unknown',
                        'type' => ucfirst($record->type),
                        'priority' => ucfirst($record->priority),
                        'message' => $record->message,
                        'department' => $record->department,
                        'recipients' => $this->getRecipientsText($record),
                        'due_date' => $record->due_date?->format('M j, Y g:i A'),
                        'created_at' => $record->created_at->format('M j, Y g:i A'),
                        'thread_count' => $record->thread_count,
                        'show_due_date' => ! empty($record->due_date),
                        'show_department' => ! empty($record->department),
                    ])
                    ->schema([
                        Hidden::make('show_due_date'),
                        Hidden::make('show_department'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sender_name')
                                    ->label('From')
                                    ->disabled(),

                                TextInput::make('subject')
                                    ->label('Subject')
                                    ->disabled(),

                                TextInput::make('type')
                                    ->label('Type')
                                    ->disabled(),

                                TextInput::make('priority')
                                    ->label('Priority')
                                    ->disabled(),
                            ]),

                        Textarea::make('message')
                            ->label('Message')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),

                        Textarea::make('recipients')
                            ->label('Recipients')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('department')
                                    ->label('Department')
                                    ->disabled()
                                    ->hidden(fn (callable $get): bool => ! $get('show_department')),

                                TextInput::make('due_date')
                                    ->label('Due Date')
                                    ->disabled()
                                    ->hidden(fn (callable $get): bool => ! $get('show_due_date')),

                                TextInput::make('created_at')
                                    ->label('Created At')
                                    ->disabled(),
                            ]),
                    ])
                    ->action(function (InternalMessage $record) {
                        // Mark as read when viewing
                        $this->markAsRead($record);
                    }),

                Action::make('reply')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->iconButton()
                    ->tooltip('Quick Reply')
                    ->visible(fn (InternalMessage $record): bool => $record->sender_id !== Auth::id() // Can't reply to own message
                    )
                    ->url(fn (InternalMessage $record) => route('filament.admin.resources.internal-messages.create', [
                        'reply_to' => $record->id,
                    ])
                    ),

                Action::make('mark_read')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Mark as Read')
                    ->visible(function (InternalMessage $record): bool {
                        $userId = Auth::id();
                        $readBy = $record->read_by ?? [];

                        return ! in_array($userId, $readBy);
                    })
                    ->action(function (InternalMessage $record) {
                        $this->markAsRead($record);
                        $this->getTable()->getAction('refresh');
                    }),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No recent messages')
            ->emptyStateDescription('When new internal messages are sent, they will appear here.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->poll('60s'); // Refresh every minute
    }

    protected function getRecipientsText(InternalMessage $record): string
    {
        $recipients = [];

        if (! empty($record->recipient_ids)) {
            $users = User::whereIn('id', $record->recipient_ids)->pluck('name')->toArray();
            $recipients = array_merge($recipients, $users);
        }

        if (! empty($record->cc_ids)) {
            $ccUsers = User::whereIn('id', $record->cc_ids)->pluck('name')->toArray();
            foreach ($ccUsers as $user) {
                $recipients[] = "{$user} (CC)";
            }
        }

        if ($record->is_public) {
            $recipients[] = 'All Users (Public)';
        }

        return implode(', ', $recipients);
    }

    protected function markAsRead(InternalMessage $record): void
    {
        $userId = Auth::id();
        $readBy = $record->read_by ?? [];

        if (! in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $record->update(['read_by' => $readBy]);
        }
    }

    public function getTableRecordKey(Model|array $record): string
    {
        return (string) $record->getKey();
    }

    protected function getTableQuery(): Builder
    {
        return InternalMessage::query()
            ->with(['sender', 'replies'])
            ->where(function (Builder $query) {
                $userId = Auth::id();
                $query->where('sender_id', $userId)
                    ->orWhereJsonContains('recipient_ids', $userId)
                    ->orWhereJsonContains('cc_ids', $userId)
                    ->orWhere('is_public', true);
            })
            ->whereNull('parent_id')
            ->latest()
            ->limit(5);
    }

    public function getTableRecordsPerPage(): ?int
    {
        return 5;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function getDescription(): ?string
    {
        return 'Latest internal messages and announcements with quick actions.';
    }
}
