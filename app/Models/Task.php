<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * Campos que se pueden asignar desde un request.
     */
    protected $fillable = [
        'title',
        'description',
        'is_completed',
    ];

    /**
     * Convierte is_completed a booleano cuando Laravel lee el modelo.
     */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
        ];
    }
}
