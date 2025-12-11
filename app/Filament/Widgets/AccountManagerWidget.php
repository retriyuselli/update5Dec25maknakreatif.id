<?php

namespace App\Filament\Widgets;

use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AccountManagerWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Account Manager Performance Dashboard';

    protected static ?int $sort = 14;  // Controls widget position in the dashboard

    protected static ?int $contentHeight = 400;  // Makes the widget a reasonable size

    protected int $pageSize = 5;  // Limits number of items per page for better performance

    public function table(Table $table): Table
    {
        return $table
            // Start with the base query - only get active account managers
            ->query(
                User::query()
                    ->withCount(['orders as am_count']) // Menghitung jumlah order dan menamakannya am_count
                    ->whereHas('status', function (Builder $query) {
                        $query->where('status_name', 'Account Manager');
                    })
                    ->where('status', 'active')
                    // Only users who exclusively have the Account Manager role
                    ->has('roles', '=', 1)
                    ->whereHas('roles', function (Builder $query) {
                        $query->where('name', 'Account Manager');
                    })
                    // Add a condition to ensure they have an active employee record
                    ->whereHas('employees', function (Builder $query) { // Ensure they have an employee record
                        $query->whereDate('date_of_join', '<=', now()); // And have joined on or before today
                    })
            )
            // Define what information we want to show
            ->columns([
                // Profile image - helps users quickly identify managers
                ImageColumn::make('avatar_url')
                    ->label('Profile')
                    ->defaultImageUrl(function ($record) {
                        // Generate default avatar based on user's name initials
                        $name = $record->name ?? 'User';
                        $initials = collect(explode(' ', $name))
                            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->implode('');

                        // Fallback if no initials
                        if (empty($initials)) {
                            $initials = strtoupper(substr($name, 0, 2));
                        }

                        // Use UI Avatars service to generate default avatar
                        return 'https://ui-avatars.com/api/?'.http_build_query([
                            'name' => $initials,
                            'size' => 128,
                            'background' => '3b82f6',
                            'color' => 'ffffff',
                            'font-size' => 0.6,
                            'rounded' => true,
                            'bold' => true,
                            'format' => 'svg',
                        ]);
                    })
                    ->circular(), // Round images look more polished

                // Manager's name - the most important identifier
                TextColumn::make('name')
                    ->label('Account Manager')
                    ->sortable()    // Enables sorting by name
                    ->weight(FontWeight::Bold),  // Makes names stand out

                // Email address - hidden by default to avoid clutter
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Client count - key performance metric
                TextColumn::make('amCount')
                    ->label('Total Clients')
                    ->numeric()
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),  // Uses theme color for consistency

                TextColumn::make('closing')
                    ->numeric()
                    ->prefix('Rp. ')
                    ->weight(FontWeight::Bold),

                // Join date - useful for tracking tenure
                TextColumn::make('employees.date_of_join')
                    ->label('Join Date')
                    ->date('d M Y'),
            ])
            // Default sorting by join date
            ->defaultSort('am_count', 'desc') // Urutkan berdasarkan jumlah closing (am_count) terbanyak

            // Define available actions
            ->recordActions([
            ])
            // Bulk actions for operating on multiple records
            ->toolbarActions([
                BulkAction::make('export')
                    ->label('Export Selected')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Collection $records) {
                        $filename = 'account_managers_'.now()->format('Ymd_His').'.csv';

                        return response()->streamDownload(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['Name', 'Email', 'Total Clients', 'Closing']);

                            foreach ($records as $user) {
                                $amCount = $user->am_count ?? $user->orders()->count();
                                $closing = $user->closing ?? $user->orders()->sum('total_price');

                                fputcsv($handle, [
                                    $user->name,
                                    $user->email,
                                    $amCount,
                                    $closing,
                                ]);
                            }

                            fclose($handle);
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
            ])
            // Configure pagination
            ->paginated([3, 6, 12])
            // Auto-refresh data every 30 seconds
            ->poll('30s');
    }
}
