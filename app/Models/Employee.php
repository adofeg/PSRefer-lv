<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'internal_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            if (empty($employee->internal_code)) {
                $employee->internal_code = 'EMP-' . strtoupper(str()->random(6));
            }
        });
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profileable');
    }
}
