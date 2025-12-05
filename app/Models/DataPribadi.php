<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataPribadi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'nomor_telepon',
        'tanggal_lahir',
        'tanggal_mulai_gabung',
        'jenis_kelamin',
        'alamat',
        'foto',
        'pekerjaan',
        'gaji',
        'motivasi_kerja',
        'pelatihan',
        // Encrypted fields
        'gaji_encrypted',
        'nomor_telepon_encrypted',
        'alamat_encrypted',
        // Audit fields
        'last_salary_accessed_at',
        'last_accessed_by',
    ];

    /**
     * Fields yang harus di-encrypted
     */
    protected $encrypted = [
        'gaji',
        'nomor_telepon',
        'alamat',
    ];

    /**
     * Hidden attributes untuk keamanan
     */
    protected $hidden = [
        'gaji_encrypted',
        'nomor_telepon_encrypted',
        'alamat_encrypted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_mulai_gabung' => 'date',
        'gaji' => 'decimal:2',
        'last_salary_accessed_at' => 'datetime',
    ];

    /**
     * Encrypt sensitive data mutator - GAJI
     */
    public function setGajiAttribute($value)
    {
        if (! is_null($value)) {
            $this->attributes['gaji_encrypted'] = Crypt::encryptString((string) $value);
            $this->attributes['gaji'] = null; // Clear plaintext

            // Log salary access for audit
            Log::info('Salary data encrypted', [
                'user_id' => Auth::id(),
                'data_pribadi_id' => $this->id,
                'action' => 'encrypt_salary',
            ]);
        }
    }

    /**
     * Decrypt sensitive data accessor - GAJI
     */
    public function getGajiAttribute()
    {
        // Update audit trail
        $this->updateSalaryAccessAudit();

        try {
            if (! empty($this->attributes['gaji_encrypted'])) {
                return (float) Crypt::decryptString($this->attributes['gaji_encrypted']);
            }

            // Fallback untuk data lama yang belum dienkripsi
            return $this->attributes['gaji'] ?? null;

        } catch (Exception $e) {
            Log::error('Failed to decrypt salary', [
                'data_pribadi_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Encrypt sensitive data mutator - NOMOR TELEPON
     */
    public function setNomorTeleponAttribute($value)
    {
        if (! is_null($value)) {
            // Clean nomor telepon
            $cleanedValue = preg_replace('/^(\+62|0)/', '', $value);
            $this->attributes['nomor_telepon_encrypted'] = Crypt::encryptString($cleanedValue);
            $this->attributes['nomor_telepon'] = null; // Clear plaintext
        }
    }

    /**
     * Decrypt sensitive data accessor - NOMOR TELEPON
     */
    public function getNomorTeleponAttribute()
    {
        try {
            if (! empty($this->attributes['nomor_telepon_encrypted'])) {
                return Crypt::decryptString($this->attributes['nomor_telepon_encrypted']);
            }

            // Fallback untuk data lama
            return $this->attributes['nomor_telepon'] ?? null;

        } catch (Exception $e) {
            Log::error('Failed to decrypt phone number', [
                'data_pribadi_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Encrypt sensitive data mutator - ALAMAT
     */
    public function setAlamatAttribute($value)
    {
        if (! is_null($value)) {
            $this->attributes['alamat_encrypted'] = Crypt::encryptString($value);
            $this->attributes['alamat'] = null; // Clear plaintext
        }
    }

    /**
     * Decrypt sensitive data accessor - ALAMAT
     */
    public function getAlamatAttribute()
    {
        try {
            if (! empty($this->attributes['alamat_encrypted'])) {
                return Crypt::decryptString($this->attributes['alamat_encrypted']);
            }

            // Fallback untuk data lama
            return $this->attributes['alamat'] ?? null;

        } catch (Exception $e) {
            Log::error('Failed to decrypt address', [
                'data_pribadi_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update salary access audit trail
     */
    private function updateSalaryAccessAudit()
    {
        if (Auth::check()) {
            $this->update([
                'last_salary_accessed_at' => now(),
                'last_accessed_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Get the URL for the profile photo.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }

        // Anda bisa mengembalikan URL default jika tidak ada foto
        // return "https://ui-avatars.com/api/?name=" . urlencode($this->nama_lengkap) . "&color=FFFFFF&background=0D83DD";
        return null;
    }

    /**
     * Get the age of the person.
     */
    public function getUsiaAttribute(): ?int
    {
        if ($this->tanggal_lahir) {
            return Carbon::parse($this->tanggal_lahir)->age;
        }

        return null;
    }

    /**
     * Get the formatted salary.
     */
    public function getFormattedGajiAttribute(): string
    {
        return 'Rp '.number_format($this->gaji ?: 0, 0, ',', '.');
    }

    public function getInitialsAttribute(): string
    {
        $name = trim($this->nama_lengkap ?? '');
        if (empty($name)) {
            return 'N/A';
        }
        $nameParts = preg_split('/\s+/', $name);
        $initials = strtoupper(substr($nameParts[0], 0, 1));
        if (count($nameParts) > 1) {
            $initials .= strtoupper(substr(end($nameParts), 0, 1));
        }

        return $initials;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'email', 'email');
    }
}
