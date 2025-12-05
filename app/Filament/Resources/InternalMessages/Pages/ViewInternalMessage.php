<?php

namespace App\Filament\Resources\InternalMessages\Pages;

use App\Filament\Resources\InternalMessages\InternalMessageResource;
use App\Models\InternalMessage;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ViewInternalMessage extends ViewRecord
{
    protected static string $resource = InternalMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Mark message as read when viewing
        $this->record->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Balas')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('success')
                ->visible(fn () => $this->record->canReply())
                ->url(fn () => "/admin/internal-messages/create?reply_to={$this->record->id}"),

            EditAction::make()
                ->visible(fn () => $this->record->sender_id === Auth::id()),

            Action::make('delete_for_user')
                ->label('Hapus untuk Saya')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->visible(fn () => ! $this->record->isDeletedByUser())
                ->action(function () {
                    $this->record->deleteForUser();
                    $this->redirect('/admin/internal-messages');
                })
                ->requiresConfirmation(),

            DeleteAction::make()
                ->label('Hapus Permanen')
                ->visible(fn () => $this->record->sender_id === Auth::id())
                ->successRedirectUrl('/admin/internal-messages'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pesan')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('type')
                            ->label('Jenis Pesan')
                            ->options([
                                'instruction' => '📋 Instruksi',
                                'communication' => '💬 Komunikasi',
                                'announcement' => '📢 Pengumuman',
                                'task' => '✅ Tugas',
                                'reminder' => '⏰ Pengingat',
                                'feedback' => '💡 Feedback',
                                'urgent' => '🚨 Mendesak',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'low' => 'Rendah',
                                'normal' => 'Normal',
                                'high' => 'Tinggi',
                                'urgent' => 'Mendesak',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        RichEditor::make('message')
                            ->label('Isi Pesan')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Lampiran')
                    ->schema([
                        Placeholder::make('attachments_display')
                            ->label('File Lampiran')
                            ->content(function ($record) {
                                if (empty($record->attachments)) {
                                    return 'Tidak ada lampiran';
                                }

                                $attachmentsList = '';
                                foreach ($record->attachments as $attachment) {
                                    $fileName = basename($attachment);
                                    $fileUrl = asset('storage/'.$attachment);

                                    // Create download link for each attachment
                                    $attachmentsList .= '<div class="mb-2 p-3 border rounded-lg bg-gray-50">';
                                    $attachmentsList .= '<div class="flex items-center justify-between">';
                                    $attachmentsList .= '<div class="flex items-center space-x-3">';

                                    // File icon based on extension
                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                    $icon = match (strtolower($extension)) {
                                        'pdf' => '📄',
                                        'doc', 'docx' => '📝',
                                        'xls', 'xlsx' => '📊',
                                        'jpg', 'jpeg', 'png', 'gif' => '🖼️',
                                        default => '📎'
                                    };

                                    $attachmentsList .= '<span class="text-2xl">'.$icon.'</span>';
                                    $attachmentsList .= '<div>';
                                    $attachmentsList .= '<div class="font-medium text-gray-900">'.$fileName.'</div>';
                                    $attachmentsList .= '<div class="text-sm text-gray-500">Klik untuk membuka/download</div>';
                                    $attachmentsList .= '</div>';
                                    $attachmentsList .= '</div>';

                                    // Action buttons
                                    $attachmentsList .= '<div class="flex items-center">';

                                    // View/Download button
                                    $attachmentsList .= '<a href="'.$fileUrl.'" target="_blank" ';
                                    $attachmentsList .= 'class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="margin-right: 6px;">';
                                    $attachmentsList .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                    $attachmentsList .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                                    $attachmentsList .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                                    $attachmentsList .= '</svg>';
                                    $attachmentsList .= 'Lihat';
                                    $attachmentsList .= '</a>';

                                    // Download button
                                    $attachmentsList .= '<a href="'.$fileUrl.'" download="'.$fileName.'" ';
                                    $attachmentsList .= 'class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">';
                                    $attachmentsList .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                    $attachmentsList .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>';
                                    $attachmentsList .= '</svg>';
                                    $attachmentsList .= 'Download';
                                    $attachmentsList .= '</a>';

                                    $attachmentsList .= '</div>';
                                    $attachmentsList .= '</div>';
                                    $attachmentsList .= '</div>';
                                }

                                return new HtmlString($attachmentsList);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->attachments)),

                Section::make('Detail Pengiriman')
                    ->schema([
                        Placeholder::make('sender_info')
                            ->label('Pengirim')
                            ->content(fn ($record) => $record->sender?->name ?? 'Unknown'),

                        Placeholder::make('recipients_info')
                            ->label('Penerima')
                            ->content(function ($record) {
                                try {
                                    $recipientIds = is_array($record->recipient_ids) ? $record->recipient_ids : [];
                                    $recipients = User::whereIn('id', $recipientIds)->pluck('name')->toArray();

                                    return ! empty($recipients) ? implode(', ', $recipients) : 'Tidak ada penerima';
                                } catch (Exception $e) {
                                    return 'Error memuat penerima';
                                }
                            }),

                        Placeholder::make('cc_info')
                            ->label('CC')
                            ->content(function ($record) {
                                try {
                                    if (empty($record->cc_ids)) {
                                        return 'Tidak ada CC';
                                    }
                                    $ccIds = is_array($record->cc_ids) ? $record->cc_ids : [];
                                    $cc = User::whereIn('id', $ccIds)->pluck('name')->toArray();

                                    return ! empty($cc) ? implode(', ', $cc) : 'Tidak ada CC';
                                } catch (Exception $e) {
                                    return 'Error memuat CC';
                                }
                            })
                            ->visible(fn ($record) => ! empty($record->cc_ids)),

                        Placeholder::make('created_at')
                            ->label('Tanggal Kirim')
                            ->content(fn ($record) => $record->created_at?->format('d M Y H:i') ?? '-'),

                        Placeholder::make('department')
                            ->label('Department')
                            ->content(fn ($record) => $record->department ?? 'Tidak ada department')
                            ->visible(fn ($record) => ! empty($record->department)),

                        Placeholder::make('tags_info')
                            ->label('Tags')
                            ->content(function ($record) {
                                if (empty($record->tags)) {
                                    return 'Tidak ada tags';
                                }

                                // Handle both array and string cases
                                if (is_array($record->tags)) {
                                    return implode(', ', $record->tags);
                                } elseif (is_string($record->tags)) {
                                    return $record->tags;
                                }

                                return 'Tidak ada tags';
                            })
                            ->visible(fn ($record) => ! empty($record->tags)),
                    ])
                    ->columns(3),
            ]);
    }

    public function getRelatedMessages()
    {
        // Get thread messages (parent and all replies)
        $rootMessage = $this->record->parent_id ?
            InternalMessage::find($this->record->parent_id) :
            $this->record;

        // Get all messages in this thread
        $threadMessages = collect([$rootMessage]);

        if ($rootMessage) {
            $replies = InternalMessage::where('parent_id', $rootMessage->id)
                ->orderBy('created_at')
                ->get();

            $threadMessages = $threadMessages->merge($replies);
        }

        return $threadMessages->filter(function ($msg) {
            return $msg && ! $msg->isDeletedByUser();
        });
    }
}
