<?php

namespace App\Filament\Resources\LeaveBalances\Pages;

use App\Filament\Resources\LeaveBalances\LeaveBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LeaveBalances\Widgets\LeaveBalanceWidget;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListLeaveBalances extends ListRecords
{
    protected static string $resource = LeaveBalanceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LeaveBalanceWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('top_up')
                ->label('Top Up Cuti Pengganti')
                ->icon('heroicon-m-plus-circle')
                ->color('success')
                ->form([
                    TextInput::make('days_to_add')
                        ->label('Jumlah Hari')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->helperText('Masukkan jumlah hari (misal: 1 untuk pengganti 1 hari kerja di hari libur)'),
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),
                    TextInput::make('reason')
                        ->label('Alasan / Keterangan')
                        ->required()
                        ->placeholder('Contoh: Masuk kerja di hari Minggu'),
                ])
                ->action(function (array $data) {
                    $user = Auth::user();
                    $leaveType = LeaveType::where('name', 'like', '%Pengganti%')
                        ->orWhere('name', 'like', '%Replacement%')
                        ->first();
                    
                    if (!$leaveType) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Jenis Cuti Pengganti tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $balance = LeaveBalance::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => now()->year,
                        ],
                        [
                            'allocated_days' => 0,
                            'used_days' => 0,
                            'remaining_days' => 0,
                        ]
                    );

                    $isSuperAdmin = $user->roles->contains('name', 'super_admin');
                    $status = $isSuperAdmin ? 'approved' : 'pending';

                    if ($status === 'approved') {
                        $balance->allocated_days += $data['days_to_add'];
                        $balance->save(); 
                    }
                    
                    $balance->histories()->create([
                        'amount' => $data['days_to_add'],
                        'transaction_date' => $data['date'],
                        'reason' => $data['reason'],
                        'created_by' => Auth::id(),
                        'status' => $status,
                    ]);

                    $date = Carbon::parse($data['date'])->translatedFormat('d F Y');

                    if ($status === 'approved') {
                        Notification::make()
                            ->title('Saldo Berhasil Ditambahkan')
                            ->body("Menambahkan {$data['days_to_add']} hari untuk tanggal {$date}. Total Saldo: {$balance->remaining_days} hari.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Permintaan Top Up Berhasil')
                            ->body("Permintaan top up {$data['days_to_add']} hari untuk tanggal {$date} telah diajukan dan menunggu persetujuan.")
                            ->success()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Top Up Saldo Cuti')
                ->modalDescription('Fitur ini digunakan untuk menambah saldo cuti secara manual, misalnya untuk Cuti Pengganti.'),
            Action::make('history')
                ->label('Riwayat Top Up')
                ->icon('heroicon-m-clock')
                ->color('info')
                ->modalHeading('Riwayat Top Up Saldo')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(function () {
                    $user = Auth::user();
                    $leaveType = LeaveType::where('name', 'like', '%Pengganti%')
                        ->orWhere('name', 'like', '%Replacement%')
                        ->first();
                    
                    if (!$leaveType) {
                        return 'Jenis Cuti Pengganti tidak ditemukan.';
                    }

                    $record = LeaveBalance::where('user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('year', now()->year)
                        ->first();
                        
                    if (!$record) {
                         return 'Belum ada riwayat top up.';
                    }

                    return view('filament.resources.leave-balances.history-modal', ['record' => $record]);
                }),
        ];
    }
}
