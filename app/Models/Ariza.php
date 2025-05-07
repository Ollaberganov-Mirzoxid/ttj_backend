<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ariza extends Model
{
    use HasFactory;

    protected $fillable = [
        'jshshir',
        'passport',
        'passport_date',
        'region',
        'district',
        'address',
        'university',
        'has_sibling',
        'sibling_relation',
        'sibling_jshshir',
        'privilege',
        'phone',
        'email',
        'status',
        'reason',
        'user_id'
    ];

    // App\Models\Ariza.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
