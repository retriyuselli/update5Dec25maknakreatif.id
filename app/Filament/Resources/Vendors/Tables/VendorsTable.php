<?php

namespace App\Filament\Resources\Vendors\Tables;

use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Vendor::query()->withCount([
                    'productVendors',
                    'expenses',
                    'notaDinasDetails',
                    'productPenambahans',
                ])
            )
            ->poll('5s')
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->formatStateUsing(fn ($state): string => $state ? Str::title($state) : '-')
                    ->copyMessage('Vendor copied')
                    ->description(fn (Vendor $record): string => $record->category?->name ?? '-'),

                TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn ($state) => $state ? '+62 '.$state : '-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                IconColumn::make('is_master')
                    ->label('Master')
                    ->boolean()
                    ->tooltip(fn (bool $state): string => $state ? 'Master data' : 'Regular')
                    ->alignCenter(),

                TextColumn::make('harga_publish')
                    ->label('Published Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('end'),

                TextColumn::make('harga_vendor')
                    ->label('Vendor Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('end'),

                TextColumn::make('profit_amount')
                    ->label('Profit')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('end')
                    ->color(fn (Vendor $record): string => ($record->profit_amount ?? 0) > 0 ? 'success' : 'danger'),

                TextColumn::make('profit_margin')
                    ->label('Margin')
                    ->formatStateUsing(fn ($state): string => number_format((float) ($state ?? 0), 2).' %')
                    ->alignment('end')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_account')
                    ->label('Account Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('usage_status')
                    ->label('Usage Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'In Use' => 'warning',
                        'Available' => 'success',
                        default => 'gray',
                    })
                    ->tooltip(function (Vendor $record): string {
                        $details = $record->usage_details;
                        $descriptions = [];

                        if ($details['productCount'] > 0) {
                            $descriptions[] = "{$details['productCount']} product(s)";
                        }
                        if ($details['expenseCount'] > 0) {
                            $descriptions[] = "{$details['expenseCount']} expense(s)";
                        }
                        if ($details['notaDinasCount'] > 0) {
                            $descriptions[] = "{$details['notaDinasCount']} nota dinas detail(s)";
                        }
                        if ($details['productPenambahanCount'] > 0) {
                            $descriptions[] = "{$details['productPenambahanCount']} product addition(s)";
                        }

                        return ! empty($descriptions)
                            ? 'Used in: '.implode(', ', $descriptions)
                            : 'Not used in any products, expenses, nota dinas details, or product additions';
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated Date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status')
                    ->options([
                        'vendor' => 'Vendor',
                        'product' => 'Product',
                    ])
                    ->multiple(),

                SelectFilter::make('is_master')
                    ->label('Master')
                    ->options([
                        1 => 'Master',
                        0 => 'Regular',
                    ])
                    ->placeholder('All Vendors')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== null,
                            fn (Builder $query): Builder => $query->where('is_master', $data['value']),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (isset($data['value']) && $data['value'] !== null) {
                            return 'Master: '.($data['value'] == 1 ? 'Yes' : 'No');
                        }

                        return null;
                    }),

                Filter::make('usage_status')
                    ->label('Usage Status')
                    ->schema([
                        Select::make('usage')
                            ->label('Filter by Usage')
                            ->options([
                                'in_use' => 'In Use',
                                'available' => 'Available',
                            ])
                            ->placeholder('All Vendors'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['usage'] === 'in_use',
                            fn (Builder $query): Builder => $query->whereHas('productVendors')
                                ->orWhereHas('expenses')
                                ->orWhereHas('notaDinasDetails')
                                ->orWhereHas('productPenambahans'),
                        )->when(
                            $data['usage'] === 'available',
                            fn (Builder $query): Builder => $query->whereDoesntHave('productVendors')
                                ->whereDoesntHave('expenses')
                                ->whereDoesntHave('notaDinasDetails')
                                ->whereDoesntHave('productPenambahans'),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['usage']) {
                            return 'Usage: '.($data['usage'] === 'in_use' ? 'In Use' : 'Available');
                        }

                        return null;
                    }),

                Filter::make('zero_profit')
                    ->label('Zero Profit')
                    ->query(fn (Builder $query): Builder => $query->where('profit_amount', 0))
                    ->toggle(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->icon('heroicon-m-trash')
                        ->tooltip('Delete vendor')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Vendor')
                        ->modalDescription('Are you sure you want to delete this vendor? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->visible(function (Vendor $record): bool {
                            return $record->usage_status === 'Available';
                        })
                        ->before(function (?Vendor $record) {
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('Vendor data not found. Please refresh the page and try again.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            Notification::make()
                                ->info()
                                ->title('Processing')
                                ->body('Validating vendor for deletion...')
                                ->send();
                        })
                        ->action(function (?Vendor $record) {
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Failed')
                                    ->body('Vendor data not found. May have been already deleted or moved.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            try {
                                $record->refresh();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Failed')
                                    ->body('Cannot access vendor data. May have been deleted by another user.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            $usageDetails = $record->usage_details;

                            if ($record->usage_status === 'In Use') {
                                $details = [];
                                if ($usageDetails['productCount'] > 0) {
                                    $details[] = "{$usageDetails['productCount']} product(s)";
                                }
                                if ($usageDetails['expenseCount'] > 0) {
                                    $details[] = "{$usageDetails['expenseCount']} expense(s)";
                                }
                                if ($usageDetails['notaDinasCount'] > 0) {
                                    $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                                }

                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Not Allowed')
                                    ->body("Vendor '{$record->name}' cannot be deleted because it is being used in ".implode(' and ', $details).'. Please remove these associations first.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            try {
                                $vendorName = $record->name ?? 'Unknown Vendor';
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Vendor Successfully Deleted')
                                    ->body("'{$vendorName}' has been deleted from the system.")
                                    ->duration(5000)
                                    ->send();

                                return true;

                            } catch (QueryException $e) {
                                $errorCode = $e->getCode();
                                if ($errorCode === '23000') {
                                    Notification::make()
                                        ->danger()
                                        ->title('Deletion Failed - Data Constraint')
                                        ->body('This vendor cannot be deleted because it is referenced by other data in the system.')
                                        ->persistent()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Database Error')
                                        ->body('A database error occurred while deleting the vendor. Please try again later.')
                                        ->persistent()
                                        ->send();
                                }

                                return false;

                            } catch (ModelNotFoundException $e) {
                                Notification::make()
                                    ->warning()
                                    ->title('Vendor Already Deleted')
                                    ->body('This vendor appears to have been already deleted by another user.')
                                    ->send();

                                return false;

                            } catch (Exception $e) {
                                Log::error('Vendor deletion failed', [
                                    'vendor_id' => $record->id ?? 'unknown',
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                Notification::make()
                                    ->danger()
                                    ->title('Unexpected Error')
                                    ->body('An unexpected error occurred while deleting the vendor. System administrator has been notified.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }
                        }),

                    Action::make('cannot_delete')
                        ->label('Cannot Delete')
                        ->icon('heroicon-m-shield-exclamation')
                        ->color('gray')
                        ->tooltip('This vendor cannot be deleted because it is being used')
                        ->visible(function (Vendor $record): bool {
                            return $record->usage_status === 'In Use';
                        })
                        ->action(function (Vendor $record) {
                            $usageDetails = $record->usage_details;

                            $details = [];
                            if ($usageDetails['productCount'] > 0) {
                                $details[] = "{$usageDetails['productCount']} product(s)";
                            }
                            if ($usageDetails['expenseCount'] > 0) {
                                $details[] = "{$usageDetails['expenseCount']} expense(s)";
                            }
                            if ($usageDetails['notaDinasCount'] > 0) {
                                $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                            }
                            if ($usageDetails['productPenambahanCount'] > 0) {
                                $details[] = "{$usageDetails['productPenambahanCount']} product addition(s)";
                            }

                            Notification::make()
                                ->warning()
                                ->title('Cannot Delete Vendor')
                                ->body("'{$record->name}' cannot be deleted because it has associated ".implode(' and ', $details).'. Please remove these associations first.')
                                ->persistent()
                                ->send();
                        }),
                    Action::make('view_usage')
                        ->label('View Usage')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn (Vendor $record) => 'Usage Details for: '.$record->name)
                        ->modalDescription('See where this vendor is currently being used')
                        ->modalContent(function (Vendor $record) {
                            $usageDetails = $record->usage_details;
                            $productCount = $usageDetails['productCount'];
                            $expenseCount = $usageDetails['expenseCount'];

                            $content = '<div class="space-y-4">';

                            if ($productCount > 0) {
                                $products = $record->productVendors()
                                    ->with('product')
                                    ->get()
                                    ->groupBy('product.name');

                                $content .= '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-yellow-800 mb-2">Used in Products ('.$productCount.' items)</h3>';
                                $content .= '<ul class="list-disc list-inside text-yellow-700 space-y-1">';

                                foreach ($products as $productName => $items) {
                                    $totalQty = $items->sum('quantity');
                                    $content .= '<li>'.$productName.' (Quantity: '.$totalQty.')</li>';
                                }

                                $content .= '</ul></div>';
                            }

                            if ($expenseCount > 0) {
                                $content .= '<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-blue-800 mb-2">Related Expenses</h3>';
                                $content .= '<p class="text-blue-700">'.$expenseCount.' expense transaction(s) are associated with this vendor.</p>';
                                $content .= '</div>';
                            }

                            $productPenambahanCount = $usageDetails['productPenambahanCount'];
                            if ($productPenambahanCount > 0) {
                                $productPenambahans = $record->productPenambahans()
                                    ->with('product')
                                    ->get()
                                    ->groupBy('product.name');

                                $content .= '<div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-purple-800 mb-2">Used in Product Additions ('.$productPenambahanCount.' items)</h3>';
                                $content .= '<ul class="list-disc list-inside text-purple-700 space-y-1">';

                                foreach ($productPenambahans as $productName => $items) {
                                    $totalAmount = $items->sum('harga_publish');
                                    $content .= '<li>'.$productName.' (Total: Rp '.number_format($totalAmount, 0, ',', '.').')</li>';
                                }

                                $content .= '</ul></div>';
                            }

                            $totalUsage = $productCount + $expenseCount + $usageDetails['notaDinasCount'] + $productPenambahanCount;
                            if ($totalUsage === 0) {
                                $content .= '<div class="p-4 bg-green-50 border border-green-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-green-800 mb-2">No Usage Found</h3>';
                                $content .= '<p class="text-green-700">This vendor is not currently used in any products, expenses, nota dinas, or product additions and can be safely deleted.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_products')
                        ->label('View Products')
                        ->icon('heroicon-o-shopping-bag')
                        ->color('success')
                        ->modalHeading(fn (Vendor $record) => 'Products using: '.$record->name)
                        ->modalDescription('Detailed list of all products that use this vendor')
                        ->visible(fn (Vendor $record) => $record->productVendors()->count() > 0)
                        ->modalContent(function (Vendor $record) {
                            $productVendors = $record->productVendors()
                                ->with(['product.category'])
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($productVendors->count() > 0) {
                                $content .= '<div class="p-4 bg-green-50 border border-green-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-green-800 mb-4">Products List ('.$productVendors->count().' entries)</h3>';

                                $groupedProducts = $productVendors->groupBy('product.name');

                                foreach ($groupedProducts as $productName => $items) {
                                    $product = $items->first()->product;
                                    $totalQuantity = $items->sum('quantity');

                                    $content .= '<div class="mb-4 p-3 bg-white border border-green-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-green-900">'.$productName.'</h4>';
                                    $content .= '<span class="text-sm text-green-600 bg-green-100 px-2 py-1 rounded">Total Qty: '.$totalQuantity.'</span>';
                                    $content .= '</div>';

                                    if ($product && $product->category) {
                                        $content .= '<p class="text-sm text-green-700 mb-2"><strong>Category:</strong> '.$product->category->name.'</p>';
                                    }

                                    $content .= '<div class="text-sm text-green-600">';
                                    $content .= '<strong>Usage Details:</strong>';
                                    $content .= '<ul class="list-disc list-inside mt-1 ml-2">';

                                    foreach ($items as $item) {
                                        $content .= '<li>Quantity: '.$item->quantity;
                                        if ($item->price) {
                                            $content .= ' | Price: Rp '.number_format($item->price, 0, ',', '.');
                                        }
                                        if ($item->created_at) {
                                            $content .= ' | Added: '.$item->created_at->format('d M Y');
                                        }
                                        $content .= '</li>';
                                    }

                                    $content .= '</ul>';
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor is not used in any products.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_expenses')
                        ->label('View Expenses')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->modalHeading(fn (Vendor $record) => 'Expenses for: '.$record->name)
                        ->modalDescription('Detailed list of all expenses related to this vendor')
                        ->visible(fn (Vendor $record) => $record->usage_details['expenseCount'] > 0)
                        ->modalContent(function (Vendor $record) {
                            $expenses = $record->expenses()
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($expenses->count() > 0) {
                                $totalAmount = $expenses->sum('amount');

                                $content .= '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-yellow-800 mb-4">Expenses List ('.$expenses->count().' transactions)</h3>';
                                $content .= '<p class="text-yellow-700 mb-4"><strong>Total Amount:</strong> Rp '.number_format($totalAmount, 0, ',', '.').'</p>';

                                foreach ($expenses as $expense) {
                                    $content .= '<div class="mb-3 p-3 bg-white border border-yellow-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-yellow-900">'.($expense->description ?? 'No Description').'</h4>';
                                    $content .= '<span class="text-sm text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Rp '.number_format($expense->amount, 0, ',', '.').'</span>';
                                    $content .= '</div>';

                                    $content .= '<div class="text-sm text-yellow-600 space-y-1">';
                                    if ($expense->transaction_date) {
                                        $content .= '<p><strong>Date:</strong> '.Carbon::parse($expense->transaction_date)->format('d M Y').'</p>';
                                    }
                                    if ($expense->category_uang_keluar) {
                                        $content .= '<p><strong>Category:</strong> '.ucfirst(str_replace('_', ' ', $expense->category_uang_keluar)).'</p>';
                                    }
                                    if ($expense->created_at) {
                                        $content .= '<p><strong>Recorded:</strong> '.$expense->created_at->format('d M Y H:i').'</p>';
                                    }
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor has no related expenses.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_nota_dinas')
                        ->label('View Nota Dinas')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->modalHeading(fn (Vendor $record) => 'Nota Dinas for: '.$record->name)
                        ->modalDescription('Detailed list of all nota dinas details related to this vendor')
                        ->visible(fn (Vendor $record) => $record->notaDinasDetails()->count() > 0)
                        ->modalContent(function (Vendor $record) {
                            $notaDinasDetails = $record->notaDinasDetails()
                                ->with('notaDinas')
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($notaDinasDetails->count() > 0) {
                                $totalAmount = $notaDinasDetails->sum('jumlah_transfer');

                                $content .= '<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-blue-800 mb-4">Nota Dinas Details ('.$notaDinasDetails->count().' entries)</h3>';
                                $content .= '<p class="text-blue-700 mb-4"><strong>Total Transfer Amount:</strong> Rp '.number_format($totalAmount, 0, ',', '.').'</p>';

                                foreach ($notaDinasDetails as $detail) {
                                    $content .= '<div class="mb-3 p-3 bg-white border border-blue-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-blue-900">'.($detail->keperluan ?? 'No Description').'</h4>';
                                    $content .= '<span class="text-sm text-blue-600 bg-blue-100 px-2 py-1 rounded">Rp '.number_format($detail->jumlah_transfer, 0, ',', '.').'</span>';
                                    $content .= '</div>';

                                    $content .= '<div class="text-sm text-blue-600 space-y-1">';
                                    if ($detail->event) {
                                        $content .= '<p><strong>Event:</strong> '.$detail->event.'</p>';
                                    }
                                    if ($detail->notaDinas) {
                                        $content .= '<p><strong>Nota Dinas No:</strong> '.$detail->notaDinas->no_nd.'</p>';
                                    }
                                    if ($detail->created_at) {
                                        $content .= '<p><strong>Recorded:</strong> '.$detail->created_at->format('d M Y H:i').'</p>';
                                    }
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor has no related nota dinas details.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('duplicate')
                        ->label('Duplicate')
                        ->icon('heroicon-m-document-duplicate')
                        ->color('primary')
                        ->action(function (Vendor $record) {
                            $newVendor = $record->replicate();
                            $newVendor->name = $record->name.' Copy';
                            $newSlug = Str::slug($newVendor->name);
                            $suffix = 1;
                            while (Vendor::where('slug', $newSlug)->exists()) {
                                $newSlug = Str::slug($newVendor->name.'-copy-'.($suffix++));
                            }
                            $newVendor->slug = $newSlug;
                            $newVendor->save();

                            Notification::make()
                                ->title('Vendor Duplicated')
                                ->body("Vendor '{$record->name}' has been successfully duplicated as '{$newVendor->name}'.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    ForceDeleteAction::make()
                        ->requiresConfirmation(),
                    RestoreAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Vendors')
                        ->modalDescription('Are you sure you want to delete the selected vendors? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete selected')
                        ->action(function (Collection $records) {
                            $deletedCount = 0;
                            $protectedVendors = [];
                            $errorVendors = [];

                            foreach ($records as $vendor) {
                                try {
                                    if ($vendor->usage_status === 'In Use') {
                                        $usageDetails = $vendor->usage_details;
                                        $details = [];
                                        if ($usageDetails['productCount'] > 0) {
                                            $details[] = "{$usageDetails['productCount']} product(s)";
                                        }
                                        if ($usageDetails['expenseCount'] > 0) {
                                            $details[] = "{$usageDetails['expenseCount']} expense(s)";
                                        }
                                        if ($usageDetails['notaDinasCount'] > 0) {
                                            $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                                        }
                                        if ($usageDetails['productPenambahanCount'] > 0) {
                                            $details[] = "{$usageDetails['productPenambahanCount']} product addition(s)";
                                        }
                                        $protectedVendors[] = "• {$vendor->name}: ".implode(', ', $details);

                                        continue;
                                    }

                                    $vendor->delete();
                                    $deletedCount++;

                                } catch (Exception $e) {
                                    $errorVendors[] = "{$vendor->name}: {$e->getMessage()}";
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Vendors Deleted')
                                    ->body("{$deletedCount} vendor(s) have been successfully deleted.")
                                    ->send();
                            }

                            if (! empty($protectedVendors)) {
                                Notification::make()
                                    ->warning()
                                    ->title('Some Vendors Could Not Be Deleted')
                                    ->body("The following vendors cannot be deleted because they are being used:\n\n".implode("\n", $protectedVendors)."\n\nPlease remove these associations first.")
                                    ->persistent()
                                    ->send();
                            }

                            if (! empty($errorVendors)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Errors')
                                    ->body("Errors occurred while deleting some vendors:\n\n".implode("\n", $errorVendors))
                                    ->persistent()
                                    ->send();
                            }

                            if ($deletedCount === 0 && empty($protectedVendors) && empty($errorVendors)) {
                                Notification::make()
                                    ->info()
                                    ->title('No Action Taken')
                                    ->body('No valid data found for deletion.')
                                    ->send();
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('No vendors yet')
            ->emptyStateDescription('Create your first vendor to get started.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create vendor')
                    ->url(route('filament.admin.resources.vendors.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s');
    }
}
