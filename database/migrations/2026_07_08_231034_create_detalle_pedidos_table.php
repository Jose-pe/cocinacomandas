<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('detalle_pedidos', function (Blueprint $table) {
        $table->id();
        // Llave foránea que conecta con la cabecera del pedido
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
        $table->string('platillo');
        $table->integer('cantidad')->default(1);
        $table->string('ensalada')->nullable();
        $table->string('bebida')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }
};
