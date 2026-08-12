<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = ['numero_mesa', 'nombre_mesero', 'observaciones'];

    // Relación: Un pedido tiene muchos detalles
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }
}
