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

    public const STATUS_NEW = 'nova';
    public const STATUS_CONTACTED = 'contactat';
    public const STATUS_BUDGET_APPROVED = 'pressupost_aprovat';
    public const STATUS_IN_PROGRESS = 'en_curs';
    public const STATUS_IN_TRANSIT = 'en_transit';
    public const STATUS_FINISHED = 'finalitzat';
    public const STATUS_REJECTED = 'rebutjat';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CONTACTED,
        self::STATUS_BUDGET_APPROVED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_IN_TRANSIT,
        self::STATUS_FINISHED,
        self::STATUS_REJECTED,
    ];

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
