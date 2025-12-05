<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SecureUserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled di controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Info - Safe untuk mass assignment
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users')->ignore($this->user),
                'filter:email',
            ],

            // Personal Info - Dengan validation ketat
            'phone_number' => 'nullable|string|regex:/^[0-9+\-\s()]+$/|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today|after:1900-01-01',
            'gender' => 'nullable|in:L,P,Laki-laki,Perempuan',

            // PROTECTED FIELDS - Tidak boleh di-mass assign via request ini
            // Field ini harus diupdate via method khusus dengan authorization
            'password' => 'prohibited',
            'role' => 'prohibited',
            'status' => 'prohibited',
            'status_id' => 'prohibited',
            'status_user' => 'prohibited',
            'avatar_url' => 'prohibited',
            'expire_date' => 'prohibited',
            'hire_date' => 'prohibited',
            'last_working_date' => 'prohibited',
            'department' => 'prohibited',
            'annual_leave_quota' => 'prohibited',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'email.unique' => 'Email sudah digunakan oleh user lain',
            'phone_number.regex' => 'Format nomor telepon tidak valid',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini',
            'date_of_birth.after' => 'Tanggal lahir tidak valid',
            'gender.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan)',

            // Security messages
            'password.prohibited' => 'Password tidak boleh diubah melalui form ini. Gunakan form khusus.',
            'role.prohibited' => 'Role tidak boleh diubah melalui form ini. Hubungi administrator.',
            'status.prohibited' => 'Status tidak boleh diubah melalui form ini.',
            'department.prohibited' => 'Department tidak boleh diubah melalui form ini. Hubungi HR.',
            'annual_leave_quota.prohibited' => 'Kuota cuti tidak boleh diubah melalui form ini.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input sebelum validation
        $this->merge([
            'name' => $this->sanitizeString($this->name),
            'phone_number' => $this->sanitizePhone($this->phone_number),
            'address' => $this->sanitizeString($this->address),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log failed validation attempts untuk security monitoring
        Log::warning('User update validation failed', [
            'user_id' => Auth::id(),
            'target_user' => $this->route('user')?->id,
            'errors' => $validator->errors()->toArray(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Sanitize string input
     */
    private function sanitizeString(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return trim(strip_tags($value));
    }

    /**
     * Sanitize phone number
     */
    private function sanitizePhone(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // Remove all characters except numbers, +, -, (, ), and spaces
        return preg_replace('/[^0-9+\-\s()]/', '', $value);
    }

    /**
     * Get only safe fields untuk mass assignment
     */
    public function safeOnly(): array
    {
        return $this->only([
            'name',
            'email',
            'phone_number',
            'address',
            'date_of_birth',
            'gender',
        ]);
    }
}
