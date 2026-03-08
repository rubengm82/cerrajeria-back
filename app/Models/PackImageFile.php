<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackImageFile extends Model
{
    use HasFactory;

    protected $table = 'pack_images_files';

    protected $fillable = [
        'packs_id',
        'path',
        'is_important',
    ];

    protected $casts = [
        'is_important' => 'boolean',
    ];

    /**
     * Relación: Una imagen pertenece a un pack.
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class, 'packs_id');
    }
}
