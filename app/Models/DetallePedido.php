<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{

    protected $table = 'detalle_pedidos'; // Opcional si respeta el plural en inglés, obligatorio si usas español
    
    protected $fillable = ['pedido_id', 'platillo', 'cantidad', 'ensalada', 'bebida'];

    // Relación inversa: El detalle pertenece a un pedido
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
