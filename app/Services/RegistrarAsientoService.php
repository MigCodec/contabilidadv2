<?php

namespace App\Services;

use App\Models\Asiento;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrarAsientoService
{
    /**
     * @param  array{fecha:string, glosa:string, user_id:int|null, detalles:array<int, array<string, mixed>>}  $datos
     */
    public function crear(array $datos): Asiento
    {
        $detalles = $this->validarDetalles($datos['detalles']);

        return DB::transaction(function () use ($datos, $detalles): Asiento {
            $asiento = Asiento::create([
                'fecha' => $datos['fecha'],
                'glosa' => $datos['glosa'],
                'user_id' => $datos['user_id'] ?? null,
                'estado' => 'registrado',
            ]);

            $asiento->detalles()->createMany($detalles);

            return $asiento->load('detalles.cuenta', 'usuario');
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $detalles
     * @return array<int, array{cuenta_id:int, descripcion:string|null, debe:string, haber:string}>
     */
    private function validarDetalles(array $detalles): array
    {
        if (count($detalles) < 2) {
            throw ValidationException::withMessages([
                'detalles' => 'El asiento debe tener al menos dos lineas contables.',
            ]);
        }

        $normalizados = [];
        $totalDebe = 0;
        $totalHaber = 0;
        $tieneDebe = false;
        $tieneHaber = false;

        foreach ($detalles as $indice => $detalle) {
            $debe = $this->aCentavos($detalle['debe'] ?? 0);
            $haber = $this->aCentavos($detalle['haber'] ?? 0);
            $cuentaId = (int) ($detalle['cuenta_id'] ?? 0);

            if ($cuentaId <= 0) {
                throw ValidationException::withMessages([
                    "detalles.$indice.cuenta_id" => 'Debe seleccionar una cuenta contable.',
                ]);
            }

            if (($debe > 0 && $haber > 0) || ($debe === 0 && $haber === 0)) {
                throw ValidationException::withMessages([
                    "detalles.$indice.debe" => 'Cada linea debe tener solo debe o solo haber.',
                    "detalles.$indice.haber" => 'Cada linea debe tener solo debe o solo haber.',
                ]);
            }

            $cuenta = Cuenta::query()
                ->whereKey($cuentaId)
                ->where('activa', true)
                ->where('acepta_movimientos', true)
                ->first();

            if (! $cuenta) {
                throw ValidationException::withMessages([
                    "detalles.$indice.cuenta_id" => 'La cuenta no existe, esta inactiva o no acepta movimientos.',
                ]);
            }

            $totalDebe += $debe;
            $totalHaber += $haber;
            $tieneDebe = $tieneDebe || $debe > 0;
            $tieneHaber = $tieneHaber || $haber > 0;

            $normalizados[] = [
                'cuenta_id' => $cuentaId,
                'descripcion' => $detalle['descripcion'] ?? null,
                'debe' => $this->desdeCentavos($debe),
                'haber' => $this->desdeCentavos($haber),
            ];
        }

        if (! $tieneDebe || ! $tieneHaber) {
            throw ValidationException::withMessages([
                'detalles' => 'El asiento debe tener al menos una linea al debe y una al haber.',
            ]);
        }

        if ($totalDebe !== $totalHaber) {
            throw ValidationException::withMessages([
                'detalles' => 'El asiento esta descuadrado: la suma del debe debe ser igual a la suma del haber.',
            ]);
        }

        return $normalizados;
    }

    private function aCentavos(mixed $valor): int
    {
        $normalizado = str_replace(',', '.', (string) ($valor ?: 0));

        return (int) round(((float) $normalizado) * 100);
    }

    private function desdeCentavos(int $valor): string
    {
        return number_format($valor / 100, 2, '.', '');
    }
}
