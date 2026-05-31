<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asiento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'fecha',
        'glosa',
        'estado',
        'anulado_at',
        'motivo_anulacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'anulado_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class);
    }

    public function getTotalDebeAttribute(): string
    {
        return number_format((float) $this->detalles->sum('debe'), 2, '.', '');
    }

    public function getTotalHaberAttribute(): string
    {
        return number_format((float) $this->detalles->sum('haber'), 2, '.', '');
    }
}
