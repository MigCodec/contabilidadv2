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
        $asiento->load('detalles');

        return view('asientos.edit', [
            'asiento' => $asiento,
            'cuentas' => $this->cuentasParaFormulario($asiento),
        ]);
    }

    public function update(Request $request, Asiento $asiento, RegistrarAsientoService $service)
    {
        $datos = $this->validar($request);
        $datos['user_id'] = $request->user()?->id;

        $service->actualizar($asiento, $datos);

        return redirect()->route('asientos.show', $asiento)->with('status', 'Asiento actualizado correctamente.');
    }

    public function destroy(Asiento $asiento)
    {
        $asiento->delete();

        return redirect()->route('asientos.index')->with('status', 'Asiento eliminado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        return $request->validate([
            'fecha' => ['required', 'date'],
            'glosa' => ['required', 'string', 'max:255'],
            'detalles' => ['required', 'array', 'min:2'],
            'detalles.*.cuenta_id' => ['required', 'integer', 'exists:cuentas,id'],
            'detalles.*.descripcion' => ['nullable', 'string', 'max:1000'],
            'detalles.*.debe' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.haber' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function cuentasParaFormulario(?Asiento $asiento = null)
    {
        $cuentasDelAsiento = $asiento
            ? $asiento->detalles()->pluck('cuenta_id')->all()
            : [];

        return Cuenta::query()
            ->where('activa', true)
            ->where('acepta_movimientos', true)
            ->orWhereIn('id', $cuentasDelAsiento)
            ->orderBy('codigo')
            ->get();
    }
}
