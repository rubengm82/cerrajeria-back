<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomSolution extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_WAITING_INSTALLATION = 'waiting_installation';
    public const STATUS_INSTALLED = 'installed';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [ self::STATUS_PENDING, self::STATUS_CONTACTED, self::STATUS_WAITING_INSTALLATION, self::STATUS_INSTALLED, self::STATUS_REJECTED];

    protected $fillable = [
        'email',
        'phone',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
    public function files(): HasMany
    {
        return $this->hasMany(CustomSolutionFile::class, 'custom_solution_id');
    }
}
