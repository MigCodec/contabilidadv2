<?php

namespace App\Services;

use App\Models\Cuenta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportesContablesService
{
    public function libroMayor(int $anio): Collection
    {
        $movimientos = DB::table('asiento_detalles')
            ->join('asientos', 'asientos.id', '=', 'asiento_detalles.asiento_id')
            ->join('cuentas', 'cuentas.id', '=', 'asiento_detalles.cuenta_id')
            ->whereYear('asientos.fecha', $anio)
            ->where('asientos.estado', 'registrado')
            ->orderBy('cuentas.codigo')
            ->orderBy('asientos.fecha')
            ->orderBy('asientos.id')
            ->select([
                'cuentas.id as cuenta_id',
                'cuentas.codigo',
                'cuentas.nombre',
                'asientos.id as asiento_id',
                'asientos.fecha',
                'asientos.glosa',
                'asiento_detalles.descripcion',
                'asiento_detalles.debe',
                'asiento_detalles.haber',
            ])
            ->get();

        return $movimientos
            ->groupBy('cuenta_id')
            ->map(function (Collection $items): array {
                $saldo = 0;
                $lineas = $items->map(function (object $item) use (&$saldo): array {
                    $debe = (float) $item->debe;
                    $haber = (float) $item->haber;
                    $saldo += $debe - $haber;

                    return [
                        'fecha' => $item->fecha,
                        'asiento_id' => $item->asiento_id,
                        'glosa' => $item->glosa,
                        'descripcion' => $item->descripcion,
                        'debe' => $debe,
                        'haber' => $haber,
                        'saldo' => $saldo,
                    ];
                });

                $primero = $items->first();

                return [
                    'codigo' => $primero->codigo,
                    'nombre' => $primero->nombre,
                    'total_debe' => $items->sum(fn (object $item): float => (float) $item->debe),
                    'total_haber' => $items->sum(fn (object $item): float => (float) $item->haber),
                    'saldo' => $saldo,
                    'lineas' => $lineas,
                ];
            })
            ->values();
    }

    public function balanceGeneral(int $anio): Collection
    {
        return $this->filasBalance($anio)->values();
    }

    public function balanceGeneralExtendido(int $anio): Collection
    {
        return $this->filasBalance($anio)
            ->groupBy(fn (array $fila): string => $fila['tipo'].'-'.$fila['subtipo_codigo'])
            ->map(function (Collection $filas): array {
                $primera = $filas->first();

                return [
                    'tipo' => $primera['tipo'],
                    'tipo_codigo' => $primera['tipo_codigo'],
                    'tipo_etiqueta' => $primera['tipo_etiqueta'],
                    'subtipo_codigo' => $primera['subtipo_codigo'],
                    'subtipo_etiqueta' => $primera['subtipo_etiqueta'],
                    'filas' => $filas->values(),
                    'totales' => $this->totalesBalance($filas),
                ];
            })
            ->sortBy([
                ['tipo_codigo', 'asc'],
                ['subtipo_codigo', 'asc'],
            ])
            ->values();
    }

    public function totalesBalance(Collection $filas): array
    {
        $columnas = ['suma_debe', 'suma_haber', 'saldo_deudor', 'saldo_acreedor', 'activo', 'pasivo', 'perdida', 'ganancia'];

        return collect($columnas)
            ->mapWithKeys(fn (string $columna): array => [$columna => $filas->sum($columna)])
            ->all();
    }

    private function filasBalance(int $anio): Collection
    {
        return DB::table('cuentas')
            ->leftJoin('asiento_detalles', 'asiento_detalles.cuenta_id', '=', 'cuentas.id')
            ->leftJoin('asientos', function ($join) use ($anio): void {
                $join->on('asientos.id', '=', 'asiento_detalles.asiento_id')
                    ->whereYear('asientos.fecha', $anio)
                    ->where('asientos.estado', 'registrado');
            })
            ->groupBy('cuentas.id', 'cuentas.codigo', 'cuentas.nombre', 'cuentas.tipo', 'cuentas.subtipo_codigo')
            ->orderBy('cuentas.codigo')
            ->select([
                'cuentas.id',
                'cuentas.codigo',
                'cuentas.nombre',
                'cuentas.tipo',
                'cuentas.subtipo_codigo',
                DB::raw('COALESCE(SUM(CASE WHEN asientos.id IS NULL THEN 0 ELSE asiento_detalles.debe END), 0) as suma_debe'),
                DB::raw('COALESCE(SUM(CASE WHEN asientos.id IS NULL THEN 0 ELSE asiento_detalles.haber END), 0) as suma_haber'),
            ])
            ->get()
            ->map(function (object $cuenta): array {
                $sumaDebe = (float) $cuenta->suma_debe;
                $sumaHaber = (float) $cuenta->suma_haber;
                $saldo = $sumaDebe - $sumaHaber;
                $saldoDeudor = max($saldo, 0);
                $saldoAcreedor = max(-$saldo, 0);

                return [
                    'id' => $cuenta->id,
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'tipo' => $cuenta->tipo,
                    'tipo_codigo' => Cuenta::CODIGOS_TIPOS[$cuenta->tipo] ?? 0,
                    'tipo_etiqueta' => Cuenta::ETIQUETAS_TIPOS[$cuenta->tipo] ?? ucfirst((string) $cuenta->tipo),
                    'subtipo_codigo' => (int) $cuenta->subtipo_codigo,
                    'subtipo_etiqueta' => Cuenta::SUBTIPOS[(int) $cuenta->subtipo_codigo] ?? 'Otros',
                    'suma_debe' => $sumaDebe,
                    'suma_haber' => $sumaHaber,
                    'saldo_deudor' => $saldoDeudor,
                    'saldo_acreedor' => $saldoAcreedor,
                    'activo' => $cuenta->tipo === 'activo' ? $saldoDeudor : 0,
                    'pasivo' => $cuenta->tipo === 'pasivo' ? $saldoAcreedor : 0,
                    'perdida' => $cuenta->tipo === 'perdida' ? $saldoDeudor : 0,
                    'ganancia' => $cuenta->tipo === 'ganancia' ? $saldoAcreedor : 0,
                ];
            });
    }
}
