<?php

namespace App\Filament\Resources\InternalMessages;

use App\Filament\Resources\InternalMessages\Pages\CreateInternalMessage;
use App\Filament\Resources\InternalMessages\Pages\EditInternalMessage;
use App\Filament\Resources\InternalMessages\Pages\ListInternalMessages;
use App\Filament\Resources\InternalMessages\Pages\ViewInternalMessage;
use App\Models\InternalMessage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class InternalMessageResource extends Resource
{
    protected static ?string $model = InternalMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $navigationLabel = 'Kotak Masuk';

    protected static ?string $modelLabel = 'Pesan Internal';

    protected static ?string $pluralModelLabel = 'Kotak Masuk';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pesan')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan subjek pesan...')
                            ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                // Only set reply subject if this is a reply and field is empty
                                $replyToId = request('reply_to');
                                if ($replyToId && empty($state)) {
                                    $replyToMessage = InternalMessage::find($replyToId);
                                    if ($replyToMessage) {
                                        $originalSubject = $replyToMessage->getOriginalThreadSubject();
                                        $replySubject = 'Re: '.$originalSubject;
                                        $component->state($replySubject);
                                    }
                                }
                            }),

                        Select::make('type')
                            ->label('Jenis Pesan')
                            ->required()
                            ->options([
                                'instruction' => '📋 Instruksi',
                                'communication' => '💬 Komunikasi',
                                'announcement' => '📢 Pengumuman',
                                'task' => '✅ Tugas',
                                'reminder' => '⏰ Pengingat',
                                'feedback' => '💡 Feedback',
                                'urgent' => '🚨 Mendesak',
                            ])
                            ->default('communication')
                            ->native(false),

                        Select::make('priority')
                            ->label('Prioritas')
                            ->required()
                            ->options([
                                'low' => '🟢 Rendah',
                                'normal' => '🔵 Normal',
                                'high' => '🟡 Tinggi',
                                'urgent' => '🔴 Mendesak',
                            ])
                            ->default('normal')
                            ->native(false),

                        RichEditor::make('message')
                            ->label('Isi Pesan')
                            ->required()
                            ->placeholder('Tulis pesan Anda di sini...')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'strike',
                                'underline',
                            ]),
                    ])
                    ->columns(2),

                Section::make('Penerima')
                    ->schema([
                        Select::make('department')
                            ->label('Department Target')
                            ->options([
                                'bisnis' => 'Bisnis',
                                'operasional' => 'Operasional',
                            ])
                            ->placeholder('Pilih department target...')
                            ->helperText(fn () => request('reply_to') ? 'Nonaktif saat membalas pesan' : 'Otomatis akan mengisi penerima sesuai department yang dipilih')
                            ->native(false)
                            ->live()
                            ->disabled(fn () => request('reply_to') !== null) // Disable when replying
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                // Don't auto-populate department if this is a reply
                                $replyToId = request('reply_to');
                                if ($replyToId) {
                                    return; // Skip department auto-population for replies
                                }

                                if ($state) {
                                    // Get users from selected department with office role
                                    $departmentUsers = self::getUsersByDepartment($state);

                                    if ($departmentUsers->isNotEmpty()) {
                                        // Auto-populate recipient_ids with department users
                                        $set('recipient_ids', $departmentUsers->keys()->toArray());

                                        // Show notification about auto-population
                                        Notification::make()
                                            ->title('Penerima Otomatis Ditambahkan')
                                            ->body("Berhasil menambahkan {$departmentUsers->count()} user dari department {$state}")
                                            ->success()
                                            ->duration(3000)
                                            ->send();
                                    }
                                }
                            }),
                        Select::make('recipient_ids')
                            ->label('Kepada')
                            ->multiple()
                            ->required()
                            ->options(fn () => self::getOfficeUsers())
                            ->searchable()
                            ->placeholder('Pilih penerima pesan (hanya staff office)...')
                            ->helperText('Hanya menampilkan user dengan role office. Akan terisi otomatis jika memilih department target atau saat membalas pesan.')
                            ->afterStateHydrated(function (Select $component, ?array $state, Set $set) {
                                // Auto-populate sender as recipient when replying
                                $replyToId = request('reply_to');
                                if ($replyToId) {
                                    $replyToMessage = InternalMessage::find($replyToId);
                                    if ($replyToMessage && $replyToMessage->sender_id) {
                                        // Set ONLY original sender as the recipient for reply
                                        $senderId = (int) $replyToMessage->sender_id;
                                        $component->state([$senderId]);

                                        // Also clear department selection to prevent conflicts
                                        $set('department', null);
                                    }
                                }
                            })
                            ->suffixAction(
                                Action::make('clear_recipients')
                                    ->icon('heroicon-m-x-mark')
                                    ->tooltip('Kosongkan penerima')
                                    ->action(function (Set $set) {
                                        $set('recipient_ids', []);
                                        Notification::make()
                                            ->title('Penerima dikosongkan')
                                            ->body('Daftar penerima telah dikosongkan')
                                            ->info()
                                            ->duration(2000)
                                            ->send();
                                    })
                            ),

                        Select::make('cc_ids')
                            ->label('CC (Carbon Copy)')
                            ->multiple()
                            ->options(fn () => self::getOfficeUsers())
                            ->searchable()
                            ->placeholder('Pilih penerima CC (hanya staff office)...')
                            ->helperText('Hanya menampilkan user dengan role office. User dapat memilih sendiri saat membalas.'),

                        Select::make('bcc_ids')
                            ->label('BCC (Blind Carbon Copy)')
                            ->multiple()
                            ->options(fn () => self::getOfficeUsers())
                            ->searchable()
                            ->placeholder('Pilih penerima BCC (hanya staff office)...')
                            ->helperText('Hanya menampilkan user dengan role office. User dapat memilih sendiri saat membalas.'),

                        Actions::make([
                            Action::make('add_all_office')
                                ->label('Tambah Semua Staff Office')
                                ->icon('heroicon-m-user-group')
                                ->color('success')
                                ->action(function (Set $set) {
                                    $allOfficeUsers = self::getOfficeUsers();
                                    $set('recipient_ids', $allOfficeUsers->keys()->toArray());

                                    Notification::make()
                                        ->title('Berhasil')
                                        ->body("Berhasil menambahkan {$allOfficeUsers->count()} staff office sebagai penerima")
                                        ->success()
                                        ->duration(3000)
                                        ->send();
                                })
                                ->tooltip('Menambahkan semua user dengan role office'),
                        ])
                            ->columnSpanFull()
                            ->alignment('start'),
                    ])
                    ->columns(2),

                Section::make('Pengaturan Tambahan')
                    ->schema([
                        Toggle::make('requires_response')
                            ->label('Memerlukan Balasan')
                            ->helperText('Aktifkan jika pesan memerlukan tanggapan dari penerima'),

                        DateTimePicker::make('due_date')
                            ->label('Tenggat Waktu')
                            ->placeholder('Pilih tanggal dan waktu tenggat...')
                            ->helperText('Opsional - untuk tugas atau instruksi yang memiliki deadline')
                            ->native(false)
                            ->visible(fn (Get $get) => $get('requires_response') || in_array($get('type'), ['task', 'instruction'])),

                        Toggle::make('is_public')
                            ->label('Pesan Publik')
                            ->helperText('Pesan publik dapat dilihat oleh semua karyawan')
                            ->visible(fn (Get $get) => $get('type') === 'announcement'),

                        Toggle::make('is_pinned')
                            ->label('Pin Pesan')
                            ->helperText('Pesan yang di-pin akan muncul di bagian atas'),

                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Tambahkan tag untuk kategorisasi...')
                            ->helperText('Contoh: meeting, project-x, urgent')
                            ->separator(','),

                        FileUpload::make('attachments')
                            ->label('Lampiran')
                            ->multiple()
                            ->directory('internal-messages')
                            ->acceptedFileTypes(['image/*', 'application/pdf', '.doc', '.docx', '.xls', '.xlsx'])
                            ->helperText('Upload file pendukung (gambar, PDF, dokumen)')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Hidden::make('sender_id')
                    ->default(fn () => Auth::id()),

                Hidden::make('status')
                    ->default('sent'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->forUser()->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'instruction' => '📋',
                        'communication' => '💬',
                        'announcement' => '📢',
                        'task' => '✅',
                        'reminder' => '⏰',
                        'feedback' => '💡',
                        'urgent' => '🚨',
                        default => '💬'
                    })
                    ->tooltip(fn ($record) => $record->getTypeLabel())
                    ->width('60px'),

                TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->formatStateUsing(function ($record, $state) {
                        if (! $record) {
                            return $state;
                        }

                        $prefix = '';
                        if ($record->is_pinned) {
                            $prefix .= '📌 ';
                        }
                        if (! $record->isReadBy()) {
                            $prefix .= '🔵 ';
                        }
                        if ($record->requires_response) {
                            $prefix .= '↩️ ';
                        }

                        // Show thread indicator for replies
                        if ($record->parent_id) {
                            $prefix .= '💬 ';
                        }

                        return $prefix.$state;
                    })
                    ->wrap(),

                TextColumn::make('sender.name')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record && $record->sender_id === Auth::id() ? 'success' : 'gray'),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn ($record) => $record ? match ($record->priority) {
                        'low' => 'success',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'primary'
                    } : 'primary')
                    ->formatStateUsing(fn ($record) => $record ? $record->getPriorityLabel() : 'Normal'),

                TextColumn::make('department')
                    ->label('Dept Target')
                    ->badge()
                    ->color(fn ($record) => $record && $record->department ? match ($record->department) {
                        'bisnis' => 'success',
                        'operasional' => 'info',
                        default => 'gray'
                    } : 'gray')
                    ->formatStateUsing(fn ($record) => $record && $record->department ? ucfirst($record->department) : '')
                    ->visible(fn ($record) => $record && $record->department),

                TextColumn::make('recipients_count')
                    ->label('Penerima')
                    ->state(fn ($record) => count($record->recipient_ids ?? []))
                    ->badge()
                    ->color('info')
                    ->suffix(' orang'),

                TextColumn::make('thread_info')
                    ->label('Thread')
                    ->badge()
                    ->state(function ($record) {
                        if (! $record) {
                            return '';
                        }

                        if ($record->parent_id) {
                            return 'Reply';
                        } elseif ($record->thread_count > 0) {
                            return $record->thread_count.' balasan';
                        }

                        return '';
                    })
                    ->color(function ($record) {
                        if (! $record) {
                            return 'gray';
                        }

                        if ($record->parent_id) {
                            return 'info'; // Blue for replies
                        } elseif ($record->thread_count > 0) {
                            return 'success'; // Green for threads with replies
                        }

                        return 'gray';
                    })
                    ->visible(fn ($record) => $record && ($record->thread_count > 0 || $record->parent_id)),

                IconColumn::make('requires_response')
                    ->label('Perlu Balasan')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-uturn-left')
                    ->falseIcon('')
                    ->trueColor('warning'),

                TextColumn::make('due_date')
                    ->label('Tenggat')
                    ->dateTime('d/m/Y H:i')
                    ->color(fn ($record) => $record && $record->due_date && $record->due_date->isPast() ? 'danger' : 'primary')
                    ->visible(fn ($record) => $record && $record->due_date),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('d/m/Y H:i')),

                TextColumn::make('user_status')
                    ->label('Status Saya')
                    ->badge()
                    ->state(fn ($record) => $record && $record->isDeletedByUser() ? 'Dihapus' : 'Aktif')
                    ->color(fn ($record) => $record && $record->isDeletedByUser() ? 'danger' : 'success')
                    ->visible(fn ($record) => $record && $record->isDeletedByUser()),

                TextColumn::make('deleted_at')
                    ->label('Sistem')
                    ->badge()
                    ->state(fn ($record) => $record && $record->trashed() ? 'Dihapus Sistem' : null)
                    ->color('gray')
                    ->visible(fn ($record) => $record && $record->trashed() && Auth::id() === 1),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis Pesan')
                    ->multiple()
                    ->options([
                        'instruction' => 'Instruksi',
                        'communication' => 'Komunikasi',
                        'announcement' => 'Pengumuman',
                        'task' => 'Tugas',
                        'reminder' => 'Pengingat',
                        'feedback' => 'Feedback',
                        'urgent' => 'Mendesak',
                    ]),

                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->multiple()
                    ->options([
                        'low' => 'Rendah',
                        'normal' => 'Normal',
                        'high' => 'Tinggi',
                        'urgent' => 'Mendesak',
                    ]),

                SelectFilter::make('department')
                    ->label('Department Target')
                    ->multiple()
                    ->options([
                        'bisnis' => 'Bisnis',
                        'operasional' => 'Operasional',
                    ]),

                SelectFilter::make('sender_id')
                    ->label('Pengirim')
                    ->relationship('sender', 'name')
                    ->multiple()
                    ->searchable(),

                Filter::make('unread')
                    ->label('Belum Dibaca')
                    ->query(fn (Builder $query) => $query->unread()),

                Filter::make('requires_response')
                    ->label('Perlu Balasan')
                    ->query(fn (Builder $query) => $query->where('requires_response', true)),

                Filter::make('pinned')
                    ->label('Pesan Penting')
                    ->query(fn (Builder $query) => $query->where('is_pinned', true)),

                Filter::make('my_messages')
                    ->label('Pesan Saya')
                    ->query(fn (Builder $query) => $query->where('sender_id', Auth::id())),

                SelectFilter::make('user_deleted_status')
                    ->label('Status Pesan Saya')
                    ->placeholder('Semua Pesan')
                    ->options([
                        'active' => 'Pesan Aktif',
                        'deleted' => 'Pesan Dihapus Saya',
                        'all' => 'Semua Pesan',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value === 'deleted') {
                            return $query->deletedByUser();
                        } elseif ($value === 'active') {
                            return $query->forUser();
                        } else {
                            // Show all messages user has access to (deleted + active)
                            $userId = Auth::id();
                            $userIdString = (string) $userId;

                            return $query->where(function ($q) use ($userId, $userIdString) {
                                $q->where('sender_id', $userId)
                                    ->orWhereJsonContains('recipient_ids', $userId)
                                    ->orWhereJsonContains('recipient_ids', $userIdString)
                                    ->orWhereJsonContains('cc_ids', $userId)
                                    ->orWhereJsonContains('cc_ids', $userIdString)
                                    ->orWhereJsonContains('bcc_ids', $userId)
                                    ->orWhereJsonContains('bcc_ids', $userIdString)
                                    ->orWhere('is_public', true);
                            });
                        }
                    }),

                TrashedFilter::make()
                    ->label('Pesan Sistem (Admin)')
                    ->visible(fn () => Auth::id() === 1), // Only for admin user ID 1
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->recordActions([
                // Primary action - always visible
                ViewAction::make()
                    ->label('Baca')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->before(fn ($record) => $record ? $record->markAsRead() : null),

                // Group secondary actions
                ActionGroup::make([
                    Action::make('reply')
                        ->label('Balas')
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn ($record) => $record && $record->canReply() && ! $record->isDeletedByUser())
                        ->url(fn ($record) => $record ? "/admin/internal-messages/create?reply_to={$record->id}" : '#')
                        ->tooltip('Balas pesan ini'),

                    Action::make('mark_read')
                        ->label('Tandai Dibaca')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->visible(fn ($record) => $record && ! $record->isReadBy())
                        ->action(fn ($record) => $record ? $record->markAsRead() : null)
                        ->requiresConfirmation(false),

                    EditAction::make()
                        ->visible(fn ($record) => $record && $record->sender_id === Auth::id()),

                    Action::make('delete_for_user')
                        ->label('Hapus untuk Saya')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->visible(fn ($record) => $record && ! $record->isDeletedByUser())
                        ->action(function ($record) {
                            $record->deleteForUser();
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Pesan berhasil dihapus untuk Anda')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pesan')
                        ->modalDescription('Pesan akan dihapus untuk Anda, tetapi masih terlihat untuk penerima lain.')
                        ->modalSubmitActionLabel('Hapus untuk Saya'),

                    Action::make('restore_for_user')
                        ->label('Pulihkan')
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn ($record) => $record && $record->isDeletedByUser())
                        ->action(function ($record) {
                            $record->restoreForUser();
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Pesan berhasil dipulihkan')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(false),

                    DeleteAction::make()
                        ->label('Hapus Permanen')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => $record && $record->sender_id === Auth::id())
                        ->successNotificationTitle('Pesan berhasil dihapus permanen')
                        ->modalDescription('Pesan akan dihapus secara permanen untuk SEMUA penerima dan tidak dapat dipulihkan. Hanya pengirim yang dapat melakukan ini.')
                        ->modalSubmitActionLabel('Hapus Permanen'),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_read')
                        ->label('Tandai Sebagai Dibaca')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->markAsRead();
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body(count($records).' pesan telah ditandai sebagai dibaca')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('archive')
                        ->label('Arsipkan')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'archived']);
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body(count($records).' pesan telah diarsipkan')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('delete_for_user_bulk')
                        ->label('Hapus untuk Saya')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (! $record->isDeletedByUser()) {
                                    $record->deleteForUser();
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body($count.' pesan berhasil dihapus untuk Anda')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pesan untuk Saya')
                        ->modalDescription('Pesan akan dihapus untuk Anda, tetapi masih terlihat untuk penerima lain.')
                        ->modalSubmitActionLabel('Hapus untuk Saya'),

                    BulkAction::make('restore_for_user_bulk')
                        ->label('Pulihkan Terpilih')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->isDeletedByUser()) {
                                    $record->restoreForUser();
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body($count.' pesan berhasil dipulihkan')
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make()
                        ->label('Hapus Permanen (Admin)')
                        ->visible(fn () => Auth::id() === 1)
                        ->successNotificationTitle('Pesan terpilih berhasil dihapus permanen')
                        ->modalDescription('Pesan akan dihapus secara permanen untuk SEMUA pengguna.')
                        ->modalSubmitActionLabel('Hapus Permanen'),
                ]),
            ])
            ->recordUrl(null) // Disable row click navigation
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternalMessages::route('/'),
            'create' => CreateInternalMessage::route('/create'),
            'edit' => EditInternalMessage::route('/{record}/edit'),
            'view' => ViewInternalMessage::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::forUser()->unread()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $unreadCount = static::getModel()::forUser()->unread()->count();

        return $unreadCount > 0 ? 'danger' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $unreadCount = static::getModel()::forUser()->unread()->count();

        return $unreadCount > 0 ? "{$unreadCount} pesan belum dibaca" : null;
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return 'Komunikasi';
    // }

    /**
     * Get users with office role for message recipients
     */
    private static function getOfficeUsers(): Collection
    {
        // Try to get users with 'office' role
        $officeUsers = User::query()
            ->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'office');
            })
            ->pluck('name', 'id');

        // If no office users found, return all users as fallback
        if ($officeUsers->isEmpty()) {
            return User::query()->pluck('name', 'id');
        }

        return $officeUsers;
    }

    /**
     * Get users by department with office role
     */
    private static function getUsersByDepartment(string $department): Collection
    {
        return User::query()
            ->where('department', $department)
            ->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'office');
            })
            ->pluck('name', 'id');
    }
}
