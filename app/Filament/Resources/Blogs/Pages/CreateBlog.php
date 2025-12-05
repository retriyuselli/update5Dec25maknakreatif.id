<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Filament\Resources\Blogs\BlogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set author information if not already set
        if (empty($data['author_name']) && Auth::check()) {
            $data['author_name'] = Auth::user()->name;
        }

        // Auto-set meta_title if not provided
        if (empty($data['meta_title']) && ! empty($data['title'])) {
            $data['meta_title'] = $data['title'].' - WOFINS';
        }

        // Auto-set meta_description if not provided but excerpt exists
        if (empty($data['meta_description']) && ! empty($data['excerpt'])) {
            $data['meta_description'] = substr($data['excerpt'], 0, 160);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
