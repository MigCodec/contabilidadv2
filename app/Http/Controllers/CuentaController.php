<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuentaController extends Controller
{
    public function index()
    {
        $cuentas = Cuenta::query()
            ->with('padre')
            ->orderBy('codigo')
            ->paginate(20);

        return view('cuentas.index', compact('cuentas'));
    }

    public function create()
    {
        return view('cuentas.create', [
            'cuenta' => new Cuenta(['activa' => true, 'acepta_movimientos' => true]),
            'cuentasPadre' => Cuenta::query()->orderBy('codigo')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Cuenta::create($this->validar($request));

        return redirect()->route('cuentas.index')->with('status', 'Cuenta creada correctamente.');
    }

    public function show(Cuenta $cuenta)
    {
        $cuenta->load('padre', 'hijas');

        return view('cuentas.show', compact('cuenta'));
    }

    public function edit(Cuenta $cuenta)
    {
        return view('cuentas.edit', [
            'cuenta' => $cuenta,
            'cuentasPadre' => Cuenta::query()
                ->whereKeyNot($cuenta->id)
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function update(Request $request, Cuenta $cuenta)
    {
        $cuenta->update($this->validar($request, $cuenta));

        return redirect()->route('cuentas.index')->with('status', 'Cuenta actualizada correctamente.');
    }

    public function destroy(Cuenta $cuenta)
    {
        $cuenta->update(['activa' => false]);

        return redirect()->route('cuentas.index')->with('status', 'Cuenta desactivada correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?Cuenta $cuenta = null): array
    {
        return $request->validate([
            'cuenta_padre_id' => ['nullable', 'exists:cuentas,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(Cuenta::TIPOS)],
            'subtipo_codigo' => ['required', 'integer', Rule::in(array_keys(Cuenta::SUBTIPOS))],
            'acepta_movimientos' => ['nullable', 'boolean'],
            'activa' => ['nullable', 'boolean'],
        ]) + [
            'subtipo_codigo' => 0,
            'acepta_movimientos' => false,
            'activa' => false,
        ];
    }
}
