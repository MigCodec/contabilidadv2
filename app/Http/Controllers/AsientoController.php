<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Cuenta;
use App\Services\RegistrarAsientoService;
use Illuminate\Http\Request;

class AsientoController extends Controller
{
    public function index()
    {
        $asientos = Asiento::query()
            ->with('usuario')
            ->withCount('detalles')
            ->latest('fecha')
            ->paginate(20);

        return view('asientos.index', compact('asientos'));
    }

    public function create()
    {
        return view('asientos.create', [
            'cuentas' => $this->cuentasParaFormulario(),
        ]);
    }

    public function store(Request $request, RegistrarAsientoService $service)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'glosa' => ['required', 'string', 'max:255'],
            'detalles' => ['required', 'array', 'min:2'],
            'detalles.*.cuenta_id' => ['required', 'integer', 'exists:cuentas,id'],
            'detalles.*.descripcion' => ['nullable', 'string', 'max:1000'],
            'detalles.*.debe' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.haber' => ['nullable', 'numeric', 'min:0'],
        ]);

        $datos['user_id'] = $request->user()?->id;

        $asiento = $service->crear($datos);

        return redirect()->route('asientos.show', $asiento)->with('status', 'Asiento registrado correctamente.');
    }

    public function show(Asiento $asiento)
    {
        $asiento->load('detalles.cuenta', 'usuario');

        return view('asientos.show', compact('asiento'));
    }

    public function edit(Asiento $asiento)
    {
        abort(403, 'Los asientos registrados no se editan; registre una reversa o anulacion.');
    }

    public function update(Request $request, Asiento $asiento)
    {
        abort(403, 'Los asientos registrados no se editan; registre una reversa o anulacion.');
    }

    public function destroy(Asiento $asiento)
    {
        abort(403, 'Los asientos registrados no se eliminan.');
    }

    private function cuentasParaFormulario()
    {
        return Cuenta::query()
            ->where('activa', true)
            ->where('acepta_movimientos', true)
            ->orderBy('codigo')
            ->get();
    }
}
