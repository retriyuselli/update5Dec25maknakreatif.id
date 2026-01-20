<?php

namespace App\Filament\Resources\LeaveRequests;

use App\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveRequestChart;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveRequestOverview;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveTypeStats;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveBalanceHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Permohonan Cuti';

    protected static ?string $pluralModelLabel = 'Permohonan Cuti';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Cuti';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permohonan Cuti')
                    ->description('Informasi dasar tentang permohonan cuti')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Karyawan')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->disabled(function () {
                                        $user = Auth::user();

                                        return $user ? ! $user->roles->contains('name', 'super_admin') : true;
                                    })
                                    ->dehydrated(true)
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => Auth::id())
                                    ->columnSpan(1)
                                    ->live(),

                                Select::make('leave_type_id')
                                    ->label('Jenis Cuti')
                                    ->relationship('leaveType', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->live(),

                                Select::make('leave_balance_history_id')
                                    ->label('Pilih Sumber Cuti Pengganti')
                                    ->visible(fn ($get) => LeaveType::find($get('leave_type_id'))?->name === 'Cuti Pengganti')
                                    ->options(function ($get, ?LeaveRequest $record) {
                                        $userId = $get('user_id');
                                        $leaveTypeId = $get('leave_type_id');
                                        
                                        if (!$userId || !$leaveTypeId) {
                                            return [];
                                        }

                                        $leaveType = LeaveType::find($leaveTypeId);
                                        if (!$leaveType || $leaveType->name !== 'Cuti Pengganti') {
                                            return [];
                                        }

                                        $balance = LeaveBalance::where('user_id', $userId)
                                            ->where('leave_type_id', $leaveTypeId)
                                            ->first();

                                        if (!$balance) {
                                            return [];
                                        }

                                        // Get IDs of histories already used in OTHER leave requests
                                        $usedHistoryIds = LeaveRequest::query()
                                            ->whereNotNull('leave_balance_history_id')
                                            ->when($record, function ($query) use ($record) {
                                                // Exclude current record so we don't hide the currently selected value during edit
                                                $query->where('id', '!=', $record->id);
                                            })
                                            ->pluck('leave_balance_history_id')
                                            ->toArray();

                                        return $balance->histories()
                                            ->whereNotIn('id', $usedHistoryIds)
                                            ->get()
                                            ->mapWithKeys(function ($history) {
                                                $date = Carbon::parse($history->transaction_date)->format('d/m/Y');
                                                return [$history->id => "{$date} - {$history->reason} (+{$history->amount})"];
                                            });
                                    })
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!$state) return;
                                        $history = LeaveBalanceHistory::find($state);
                                        if ($history) {
                                            $set('substitution_date', $history->transaction_date);
                                            $set('substitution_notes', $history->reason);
                                        }
                                    })
                                    ->columnSpan(2),

                                DatePicker::make('substitution_date')
                                    ->label('Tanggal Pengganti')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->required(fn ($get) => LeaveType::find($get('leave_type_id'))?->name === 'Cuti Pengganti')
                                    ->visible(fn ($get) => LeaveType::find($get('leave_type_id'))?->name === 'Cuti Pengganti'),

                                TextInput::make('substitution_notes')
                                    ->label('Alasan Pengganti')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->required(fn ($get) => LeaveType::find($get('leave_type_id'))?->name === 'Cuti Pengganti')
                                    ->visible(fn ($get) => LeaveType::find($get('leave_type_id'))?->name === 'Cuti Pengganti'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $endDate = $get('end_date');
                                        if ($state && $endDate) {
                                            $startDate = Carbon::parse($state);
                                            $endDate = Carbon::parse($endDate);
                                            $totalDays = $startDate->diffInDays($endDate) + 1;
                                            $set('total_days', $totalDays);
                                        }
                                    }),

                                DatePicker::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $startDate = $get('start_date');
                                        if ($startDate && $state) {
                                            $startDate = Carbon::parse($startDate);
                                            $endDate = Carbon::parse($state);
                                            $totalDays = $startDate->diffInDays($endDate) + 1;
                                            $set('total_days', $totalDays);
                                        }
                                    }),

                                TextInput::make('total_days')
                                    ->label('Total Hari')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Textarea::make('reason')
                            ->label('Alasan Cuti')
                            ->rows(3)
                            ->placeholder('Silakan berikan alasan untuk permohonan cuti Anda...'),

                        TextInput::make('emergency_contact')
                            ->label('Kontak Darurat')
                            ->placeholder('Informasi kontak darurat (opsional)')
                            ->helperText('Nama dan nomor telepon yang dapat dihubungi selama cuti'),

                        FileUpload::make('documents')
                            ->label('Dokumen Pendukung')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048) // 2MB
                            ->directory('leave-documents')
                            ->multiple()
                            ->openable()
                            ->maxFiles(3)
                            ->helperText('Upload dokumen pendukung (PDF - maksimal 2MB per file, maksimal 3 file)'),

                        Select::make('replacement_employee_id')
                            ->label('Karyawan Pengganti')
                            ->relationship('replacementEmployee', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih karyawan pengganti (opsional)')
                            ->helperText('Pilih karyawan yang akan menangani tanggung jawab Anda selama cuti')
                            ->options(function () {
                                return User::where('status', 'active')
                                    ->where('id', '!=', Auth::id())
                                    ->pluck('name', 'id');
                            }),
                    ])
                    ->columnSpanFull(),

                Section::make('Informasi Persetujuan')
                    ->description('Status dan detail persetujuan')
                    ->visible(function () {
                        $user = Auth::user();
                        return $user ? $user->roles->contains('name', 'super_admin') : false;
                    })
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->reactive(),

                                Select::make('approved_by')
                                    ->label('Disetujui Oleh')
                                    ->relationship('approver', 'name', function (Builder $query) {
                                        return $query->whereHas('roles', function ($q) {
                                            $q->where('name', 'super_admin');
                                        });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get) => in_array($get('status'), ['approved', 'rejected'])),
                            ]),

                        Textarea::make('approval_notes')
                            ->label('Catatan Persetujuan')
                            ->rows(2)
                            ->placeholder('Tambahkan catatan tentang persetujuan/penolakan...')
                            ->visible(fn (callable $get) => in_array($get('status'), ['approved', 'rejected'])),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                // Jika bukan super_admin, hanya tampilkan data leave request milik user yang login
                if ($user && ! $user->roles->contains('name', 'super_admin')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total_days')
                    ->label('Hari')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('documents')
                    ->label('Dokumen')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Tidak ada dokumen';
                        }
                        $count = is_array($state) ? count($state) : 0;

                        return $count.' file'.($count > 1 ? '' : '');
                    })
                    ->badge()
                    ->color(function ($state) {
                        return empty($state) ? 'gray' : 'success';
                    })
                    ->icon(function ($state) {
                        return empty($state) ? 'heroicon-o-document' : 'heroicon-o-document-text';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default => $state,
                        };
                    })
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ]),

                TextColumn::make('replacementEmployee.name')
                    ->label('Pengganti')
                    ->placeholder('Tidak ada pengganti')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('emergency_contact')
                    ->label('Kontak Darurat')
                    ->placeholder('Tidak disediakan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),

                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('Belum disetujui')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),

                SelectFilter::make('leave_type_id')
                    ->label('Jenis Cuti')
                    ->relationship('leaveType', 'name'),

                SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable(),

                SelectFilter::make('replacement_employee_id')
                    ->label('Karyawan Pengganti')
                    ->relationship('replacementEmployee', 'name')
                    ->searchable(),

                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat'),
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(function () {
                        $user = Auth::user();

                        return $user ? $user->roles->contains('name', 'super_admin') : false;
                    }),

                Action::make('view_documents')
                    ->label('Dokumen')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(function (LeaveRequest $record) {
                        return ! empty($record->documents);
                    })
                    ->modalHeading('Dokumen Pendukung')
                    ->modalContent(function (LeaveRequest $record) {
                        $documents = $record->documents ?? [];
                        $documentLinks = [];

                        foreach ($documents as $document) {
                            $documentLinks[] = '<div class="mb-2">
                                <a href="'.asset('storage/'.$document).'" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 underline flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    '.basename($document).'
                                </a>
                            </div>';
                        }

                        return view('filament.components.document-list', [
                            'documents' => $documents,
                            'documentLinks' => $documentLinks,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Action::make('approve')
                    ->label('Setuju')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function (LeaveRequest $record) {
                        $user = Auth::user();
                        $isSuperAdmin = $user ? $user->roles->contains('name', 'super_admin') : false;

                        return $record->status === 'pending' && $isSuperAdmin;
                    })
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title('Permohonan cuti disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (LeaveRequest $record) {
                        $user = Auth::user();
                        $isSuperAdmin = $user ? $user->roles->contains('name', 'super_admin') : false;

                        return $record->status === 'pending' && $isSuperAdmin;
                    })
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title('Permohonan cuti ditolak')
                            ->success()
                            ->send();
                    }),

                Action::make('view_approval')
                    ->label('Lihat Persetujuan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->visible(function (LeaveRequest $record) {
                        return $record->status === 'approved';
                    })
                    ->url(fn (LeaveRequest $record) => route('leave-request.approval-detail', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->visible(function () {
                            $user = Auth::user();

                            return $user ? $user->roles->contains('name', 'super_admin') : false;
                        }),

                    BulkAction::make('approve_bulk')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(function () {
                            $user = Auth::user();

                            return $user ? $user->roles->contains('name', 'super_admin') : false;
                        })
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => Auth::id(),
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Permohonan cuti terpilih telah disetujui')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('reject_bulk')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(function () {
                            $user = Auth::user();

                            return $user ? $user->roles->contains('name', 'super_admin') : false;
                        })
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'rejected',
                                        'approved_by' => Auth::id(),
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Permohonan cuti terpilih telah ditolak')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            LeaveRequestOverview::class,
            LeaveRequestChart::class,
            LeaveTypeStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
