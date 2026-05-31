<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    use HasFactory;

    public const TIPOS = [
        'activo',
        'pasivo',
        'perdida',
        'ganancia',
    ];

    public const ETIQUETAS_TIPOS = [
        'activo' => 'Activo',
        'pasivo' => 'Pasivo',
        'perdida' => 'Perdida',
        'ganancia' => 'Ganancia',
    ];

    public const CODIGOS_TIPOS = [
        'activo' => 1,
        'pasivo' => 2,
        'perdida' => 3,
        'ganancia' => 4,
    ];

    public const SUBTIPOS = [
        0 => 'Otros',
        1 => 'Corriente',
        2 => 'Fijo / no corriente',
        3 => 'Patrimonio',
        4 => 'Operacional',
        5 => 'No operacional',
    ];

    protected $fillable = [
        'cuenta_padre_id',
        'nombre',
        'tipo',
        'subtipo_codigo',
        'acepta_movimientos',
        'activa',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cuenta $cuenta): void {
            $cuenta->codigo = 'TEMP-'.str_replace('.', '', uniqid('', true));
        });

        static::created(function (Cuenta $cuenta): void {
            $cuenta->forceFill(['codigo' => $cuenta->generarCodigoContable()])->saveQuietly();
        });

        static::updating(function (Cuenta $cuenta): void {
            if ($cuenta->exists) {
                $cuenta->codigo = $cuenta->generarCodigoContable();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'subtipo_codigo' => 'integer',
            'acepta_movimientos' => 'boolean',
            'activa' => 'boolean',
        ];
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cuenta_padre_id');
    }

    public function hijas(): HasMany
    {
        return $this->hasMany(self::class, 'cuenta_padre_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class);
    }

    public function etiquetaTipo(): string
    {
        return self::ETIQUETAS_TIPOS[$this->tipo] ?? ucfirst((string) $this->tipo);
    }

    public function etiquetaSubtipo(): string
    {
        return self::SUBTIPOS[$this->subtipo_codigo] ?? 'Otros';
    }

    public function generarCodigoContable(): string
    {
        return implode('.', [
            self::CODIGOS_TIPOS[$this->tipo] ?? 0,
            $this->subtipo_codigo ?? 0,
            $this->cuenta_padre_id ?: 0,
            $this->id,
        ]);
    }
}
