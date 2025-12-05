<?php

namespace App\Filament\Resources\InternalMessages\Pages;

use App\Filament\Resources\InternalMessages\InternalMessageResource;
use App\Models\InternalMessage;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateInternalMessage extends CreateRecord
{
    protected static string $resource = InternalMessageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();

        // Handle reply pre-fill for non-subject fields
        if (request()->has('reply_to')) {
            $this->handleReplyPreFill();
        }
    }

    private function handleReplyPreFill(): void
    {
        $replyToId = request('reply_to');
        $originalMessage = InternalMessage::find($replyToId);

        if (! $originalMessage) {
            return;
        }

        // Get all participants except current user
        $allParticipants = array_merge(
            $originalMessage->recipient_ids ?? [],
            $originalMessage->cc_ids ?? [],
            [$originalMessage->sender_id]
        );

        // Remove current user from participants to avoid self-sending
        $participants = array_filter($allParticipants, function ($id) {
            return (int) $id !== Auth::id();
        });

        // Pre-fill form with reply data (subject handled by afterStateHydrated)
        $this->form->fill([
            'priority' => $originalMessage->priority,
            'type' => 'communication',
            'recipient_ids' => array_values(array_unique(array_map('intval', $participants))),
            'tags' => $originalMessage->tags ?? [],
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sender_id'] = Auth::id();

        // Set default values for required fields
        $data['status'] = $data['status'] ?? 'sent'; // Fix: Add default status
        $data['read_by'] = $data['read_by'] ?? [];
        $data['deleted_by'] = $data['deleted_by'] ?? [];
        $data['thread_count'] = $data['thread_count'] ?? 0;
        $data['is_public'] = $data['is_public'] ?? false;
        $data['is_pinned'] = $data['is_pinned'] ?? false;

        // Ensure recipient_ids is always an array of integers
        if (! isset($data['recipient_ids']) || empty($data['recipient_ids'])) {
            $data['recipient_ids'] = [];
        } else {
            $data['recipient_ids'] = array_map('intval', $data['recipient_ids']);
        }

        // Handle optional array fields - ensure they're arrays or null, not empty strings
        $data['cc_ids'] = ! empty($data['cc_ids']) ? array_map('intval', $data['cc_ids']) : [];
        $data['bcc_ids'] = ! empty($data['bcc_ids']) ? array_map('intval', $data['bcc_ids']) : [];
        $data['attachments'] = ! empty($data['attachments']) ? $data['attachments'] : [];
        $data['tags'] = ! empty($data['tags']) ? $data['tags'] : [];

        // Convert tags from string to array if needed
        if (is_string($data['tags']) && ! empty($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        // Handle reply functionality - set threading relationship
        if (request()->has('reply_to')) {
            $originalMessage = InternalMessage::find(request('reply_to'));
            if ($originalMessage) {
                $data['parent_id'] = $originalMessage->id;
                $data['type'] = 'communication'; // Replies are always communication type
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Update thread count on parent message
        if ($this->record->parent_id) {
            $parent = InternalMessage::find($this->record->parent_id);
            if ($parent) {
                $parent->increment('thread_count');
            }
        }

        // Send notification to recipients
        $this->sendNotificationToRecipients();
    }

    private function sendNotificationToRecipients(): void
    {
        $recipients = $this->record->getAllRecipients();

        foreach ($recipients as $recipient) {
            if ($recipient->id !== Auth::id()) {
                Notification::make()
                    ->title('Pesan Baru dari '.$this->record->sender->name)
                    ->body(Str::limit(strip_tags($this->record->message), 100))
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary')
                    ->actions([
                        Action::make('read')
                            ->button()
                            ->url('/admin/internal-messages/'.$this->record->id)
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($recipient);
            }
        }
    }
}
