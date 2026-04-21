<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'user_id',
        'customer_name',
        'customer_last_name_one',
        'customer_last_name_second',
        'customer_dni',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_zip_code',
        'installation_address',
        'shipping_address',
        'shipping_price',
        'installation_price',
        'shipped_at',
        'payment_method',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'shipping_price' => 'decimal:2',
        'installation_price' => 'decimal:2',
    ];

    /**
     * Relación: Un pedido pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un pedido tiene muchos productos.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('quantity', 'installation_requested')
                    ->withTimestamps();
    }

    /**
     * Relación: Un pedido tiene muchos packs.
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'order_packs')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
