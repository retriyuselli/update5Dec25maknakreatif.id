<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Filament\Resources\Blogs\BlogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-update meta_title if changed and not manually set
        if (! empty($data['title']) && (empty($data['meta_title']) || $data['meta_title'] === $this->record->title.' - WOFINS')) {
            $data['meta_title'] = $data['title'].' - WOFINS';
        }

        // Auto-update meta_description if excerpt changed and meta_description wasn't manually modified
        if (! empty($data['excerpt']) && (empty($data['meta_description']) || strlen($data['meta_description']) <= 160)) {
            $data['meta_description'] = substr($data['excerpt'], 0, 160);
        }

        return $data;
    }
}
