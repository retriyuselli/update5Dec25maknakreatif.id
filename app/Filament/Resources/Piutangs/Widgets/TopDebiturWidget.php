<?php

namespace App\Filament\Resources\Piutangs\Widgets;

use App\Models\Piutang;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopDebiturWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Debitur Berdasarkan Sisa Piutang';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Piutang::query()
                    ->select([
                        DB::raw('ROW_NUMBER() OVER (ORDER BY SUM(sisa_piutang) DESC) as id'),
                        'nama_debitur',
                        'kontak_debitur',
                        DB::raw('COUNT(*) as total_piutang'),
                        DB::raw('SUM(total_piutang) as total_nilai'),
                        DB::raw('SUM(sisa_piutang) as total_sisa'),
                        DB::raw('SUM(sudah_dibayar) as total_dibayar'),
                        DB::raw('MAX(tanggal_jatuh_tempo) as jatuh_tempo_terdekat'),
                    ])
                    ->where('sisa_piutang', '>', 0)
                    ->groupBy('nama_debitur', 'kontak_debitur')
                    ->orderBy('total_sisa', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('nama_debitur')
                    ->label('Nama Debitur')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('total_piutang')
                    ->label('Jumlah Piutang')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_sisa')
                    ->label('Total Sisa')
                    ->money('IDR')
                    ->color('danger')
                    ->weight('bold'),

                TextColumn::make('total_dibayar')
                    ->label('Sudah Dibayar')
                    ->money('IDR')
                    ->color('success'),

                TextColumn::make('persentase_pembayaran')
                    ->label('% Dibayar')
                    ->state(function ($record) {
                        if ($record->total_nilai > 0) {
                            return number_format(($record->total_dibayar / $record->total_nilai) * 100, 1).'%';
                        }

                        return '0%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->total_nilai <= 0) {
                            return 'gray';
                        }
                        $percentage = ($record->total_dibayar / $record->total_nilai) * 100;

                        return match (true) {
                            $percentage >= 80 => 'success',
                            $percentage >= 50 => 'warning',
                            default => 'danger'
                        };
                    }),

                TextColumn::make('jatuh_tempo_terdekat')
                    ->label('Jatuh Tempo Terdekat')
                    ->date('d M Y')
                    ->color(function ($record) {
                        if (! $record->jatuh_tempo_terdekat) {
                            return 'gray';
                        }
                        $days = now()->diffInDays($record->jatuh_tempo_terdekat, false);

                        return match (true) {
                            $days < 0 => 'danger',
                            $days <= 7 => 'warning',
                            default => 'success'
                        };
                    })
                    ->badge(),

                TextColumn::make('kontak_debitur')
                    ->label('Kontak')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('hubungi')
                    ->label('Hubungi')
                    ->icon('heroicon-o-phone')
                    ->color('primary')
                    ->url(fn ($record) => $record->kontak_debitur ? 'tel:'.$record->kontak_debitur : null)
                    ->openUrlInNewTab(false)
                    ->visible(fn ($record) => $record->kontak_debitur),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        if (! $record->kontak_debitur) {
                            return null;
                        }
                        $phone = preg_replace('/[^0-9]/', '', $record->kontak_debitur);
                        if (substr($phone, 0, 1) === '0') {
                            $phone = '62'.substr($phone, 1);
                        }
                        $message = urlencode("Halo {$record->nama_debitur}, kami ingin mengingatkan tentang sisa piutang Anda sebesar Rp ".number_format($record->total_sisa, 0, ',', '.').'. Mohon untuk segera melakukan pembayaran. Terima kasih.');

                        return "https://wa.me/{$phone}?text={$message}";
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->kontak_debitur),

                Action::make('lihat_detail')
                    ->label('Lihat Semua Piutang')
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        return route('filament.admin.resources.piutangs.index', [
                            'tableSearch' => $record->nama_debitur,
                        ]);
                    })
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Tidak ada debitur dengan sisa piutang')
            ->emptyStateDescription('Semua piutang sudah lunas')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
