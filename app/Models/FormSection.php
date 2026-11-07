<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    protected $fillable = ['key', 'title', 'description', 'is_builtin', 'is_locked', 'is_visible', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_builtin' => 'boolean',
            'is_locked'  => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order')->orderBy('id');
    }
}