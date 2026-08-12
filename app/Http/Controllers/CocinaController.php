<?php

namespace App\Http\Controllers;

use App\Models\Cocina;
use App\Models\Pedido;
use Illuminate\Http\Request;

class CocinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function pantallaCocina()
    {
        // Traemos los pedidos pendientes (asumiendo que añadas un campo 'estado' en el futuro)
        // Por ahora traemos los últimos pedidos con sus detalles
        $pedidos = Pedido::with('detalles')
            ->orderBy('created_at', 'asc') // El más antiguo primero para atender en orden
            ->get();

        return view('cocina', compact('pedidos'));
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
    public function show(Cocina $cocina)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cocina $cocina)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cocina $cocina)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cocina $cocina)
    {
        //
    }
}
