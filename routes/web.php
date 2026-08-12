<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\CocinaController;

/*
|--------------------------------------------------------------------------
| Rutas para el Sistema de Pedidos y Comandas (Tiempo Real)
|--------------------------------------------------------------------------
*/

// 1. VISTAS PRINCIPALES
// Ruta para que el mesero vea y use el formulario dinámico
Route::get('/', function () {
    return view('welcome');
});



// Ruta para el monitor de la cocina (Pantalla en tiempo real)
Route::get('/cocina', [CocinaController::class, 'pantallaCocina'])
    ->name('cocina');


// 2. ACCIONES Y PROCESAMIENTO (API / ACCIONES POST)
// Ruta para recibir los datos del formulario e insertar el pedido en la BD
Route::post('/pedido_guardar', [PedidoController::class, 'guardarPedido'])
    ->name('pedido_guardar');

// Ruta para que la cocina despache (marque como "Listo") un pedido vía Fetch/Axios
Route::post('/pedidos/{id}/despachar', [PedidoController::class, 'despacharPedido'])
    ->name('pedido.despachar');