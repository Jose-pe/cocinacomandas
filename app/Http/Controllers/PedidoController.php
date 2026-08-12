<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use BD;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\PedidoCreado;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function guardarPedido(Request $request)
    {
        // 1. Validar los datos recibidos (incluyendo los arrays dinámicos)
        $request->validate([
            'mesa' => 'required|integer|min:1',
            'mesero' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'platillo' => 'required|array|min:1',
            'platillo.*' => 'required|string',
            'cantidad' => 'required|array',
            'cantidad.*' => 'required|integer|min:1',
            'ensalada' => 'nullable|array',
            'bebida' => 'nullable|array',
        ]);

        // 2. Usar una transacción de BD por seguridad
        DB::transaction(function () use ($request , &$pedido) {
            
            // Guardar la cabecera del pedido
            $pedido = Pedido::create([
                'numero_mesa' => $request->mesa,
                'nombre_mesero' => $request->mesero,
                'observaciones' => $request->observaciones,
            ]);

            // Guardar los detalles iterando los arrays dinámicos
            // Como todos los inputs dinámicos comparten el mismo índice del formulario, usamos un bucle
            foreach ($request->platillo as $index => $nombrePlatillo) {
                $pedido->detalles()->create([
                    'platillo' => $nombrePlatillo,
                    'cantidad' => $request->cantidad[$index],
                    'ensalada' => $request->ensalada[$index] ?? 'Ninguno',
                    'bebida' => $request->bebida[$index] ?? 'Ninguno',
                ]);
            }
        });
        // ¡Disparamos el evento hacia los WebSockets!
        //broadcast(new PedidoCreado($pedido))->toOthers();
        return response()->json([
            'status' => 'success',
            'message' => '¡Pedido enviado a cocina correctamente!'
        ], 201);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}
