<?php

namespace App\Http\Controllers;

use App\Services\ReportesContablesService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function libroMayor(Request $request, ReportesContablesService $service): View
    {
        $anio = $this->anio($request);

        return view('reportes.libro-mayor', [
            'anio' => $anio,
            'cuentas' => $service->libroMayor($anio),
        ]);
    }

    public function balanceGeneral(Request $request, ReportesContablesService $service): View
    {
        $anio = $this->anio($request);
        $filas = $service->balanceGeneral($anio);

        return view('reportes.balance-general', [
            'anio' => $anio,
            'filas' => $filas,
            'totales' => $service->totalesBalance($filas),
        ]);
    }

    public function balanceGeneralExtendido(Request $request, ReportesContablesService $service): View
    {
        $anio = $this->anio($request);
        $grupos = $service->balanceGeneralExtendido($anio);
        $filas = $grupos->flatMap(fn (array $grupo) => $grupo['filas']);

        return view('reportes.balance-general-extendido', [
            'anio' => $anio,
            'grupos' => $grupos,
            'totales' => $service->totalesBalance($filas),
        ]);
    }

    private function anio(Request $request): int
    {
        $datos = $request->validate([
            'anio' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        return (int) ($datos['anio'] ?? now()->year);
    }
}
