<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'industry_name',
        'description',
        'is_active',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }

    public function prospectApps()
    {
        return $this->hasMany(ProspectApp::class);
    }
}
