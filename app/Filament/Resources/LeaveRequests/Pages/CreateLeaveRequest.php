<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan user_id selalu diisi dengan user yang sedang login
        if (! isset($data['user_id']) || empty($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }

        return $data;
    }
}
