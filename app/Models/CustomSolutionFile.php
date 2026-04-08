<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomSolutionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_solution_id',
        'file_path',
    ];

    /**
     * Relación: Un archivo pertenece a una solución personalizada.
     */
    public function customSolution(): BelongsTo
    {
        return $this->belongsTo(CustomSolution::class, 'custom_solution_id');
    }
}