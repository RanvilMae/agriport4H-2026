<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    /**
     * Question types an Admin can choose for a custom question.
     */
    public const TYPES = [
        'text'       => 'Short text',
        'textarea'   => 'Long text',
        'number'     => 'Number',
        'date'       => 'Date',
        'select'     => 'Dropdown',
        'radio'      => 'Choose one (buttons)',
        'checkboxes' => 'Choose many (checkboxes)',
        'yes_no'     => 'Yes / No',
    ];

    /**
     * Types whose answers come from a list of options.
     */
    public const OPTION_TYPES = ['select', 'radio', 'checkboxes'];

    protected $fillable = [
        'form_section_id', 'key', 'label', 'type', 'options', 'help_text',
        'is_builtin', 'is_locked', 'is_visible', 'is_required', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options'     => 'array',
            'is_builtin'  => 'boolean',
            'is_locked'   => 'boolean',
            'is_visible'  => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'form_section_id');
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, self::OPTION_TYPES, true);
    }

    /**
     * Options as [value => label].
     */
    public function optionList(): array
    {
        return collect($this->options ?? [])
            ->mapWithKeys(fn ($o) => [(string) ($o['value'] ?? '') => (string) ($o['label'] ?? $o['value'] ?? '')])
            ->filter(fn ($label, $value) => $value !== '')
            ->all();
    }

    /**
     * Options as editable text: one per line, "value | label" when they differ.
     */
    public function optionsAsText(): string
    {
        return collect($this->optionList())
            ->map(fn ($label, $value) => $label === $value ? $value : "{$value} | {$label}")
            ->implode("\n");
    }

    /**
     * Turn the Admin's text (one option per line) back into stored options.
     */
    public static function parseOptions(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->map(function ($line) {
                [$value, $label] = array_pad(array_map('trim', explode('|', $line, 2)), 2, null);
                return ['value' => $value, 'label' => $label ?: $value];
            })
            ->unique('value')
            ->values()
            ->all();
    }
}