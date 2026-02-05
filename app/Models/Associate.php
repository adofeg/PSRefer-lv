<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'balance',
        'category',
        'payment_info',
        'w9_status',
        'w9_file_url',
        'referrer_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'payment_info' => 'array',
    ];

    // Morph Parent
    public function user()
    {
        return $this->morphOne(User::class, 'profileable');
    }

    // MLM
    public function referrer()
    {
        return $this->belongsTo(Associate::class, 'referrer_id');
    }

    // Associate-specific logic that used to be on User
    public function networkChildren()
    {
        return $this->hasMany(Network::class, 'parent_associate_id');
    }

    /**
     * Add this associate (via user) to a network
     */
    public function addToNetwork(string $referrerId): void
    {
        Network::create([
            'parent_associate_id' => $referrerId,
            'child_associate_id' => $this->id,
            'level' => 1,
            'total_sales' => 0,
        ]);
    }
}
