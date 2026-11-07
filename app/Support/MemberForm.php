<?php

namespace App\Support;

use App\Models\FormField;
use App\Models\FormSection;
use App\Models\Member;
use App\Models\MemberFieldValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Reads the Admin's member form settings (Member Form Builder) for the
 * member create / edit / profile pages.
 *
 * Anything not configured falls back to the form's original behaviour,
 * so pages keep working even before the migration has been run.
 */
class MemberForm
{
    private static ?self $instance = null;

    /** @var Collection<string, FormSection> */
    private Collection $sections;

    /** @var Collection<string, FormField> */
    private Collection $fields;

    private function __construct()
    {
        $this->sections = collect();
        $this->fields   = collect();

        if (! Schema::hasTable('form_sections') || ! Schema::hasTable('form_fields')) {
            return;
        }

        $this->sections = FormSection::with('fields')
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->keyBy('key');

        $this->fields = $this->sections
            ->flatMap(fn (FormSection $s) => $s->fields->each->setRelation('section', $s))
            ->keyBy('key');
    }

    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Forget cached settings (after the Admin saves changes).
     */
    public static function flush(): void
    {
        self::$instance = null;
    }

    // ---------------------------------------------------------------------
    // Built-in sections and fields
    // ---------------------------------------------------------------------

    public function sectionVisible(string $key): bool
    {
        $section = $this->sections->get($key);

        return ! $section || $section->is_locked || $section->is_visible;
    }

    public function sectionTitle(string $key, string $default): string
    {
        return $this->sections->get($key)?->title ?: $default;
    }

    public function fieldVisible(string $key): bool
    {
        $field = $this->fields->get($key);

        if (! $field) {
            return true;
        }

        return $field->is_locked
            || ($field->is_visible && $this->sectionVisible($field->section->key));
    }

    public function label(string $key, string $default): string
    {
        return $this->fields->get($key)?->label ?: $default;
    }

    public function help(string $key): ?string
    {
        return $this->fields->get($key)?->help_text;
    }

    /**
     * Dropdown / checkbox options as [value => label].
     */
    public function options(string $key, array $default = []): array
    {
        $field = $this->fields->get($key);

        if (! $field || empty($field->options)) {
            return $default;
        }

        return $field->optionList();
    }

    // ---------------------------------------------------------------------
    // Custom questions added by the Admin
    // ---------------------------------------------------------------------

    /**
     * Visible custom questions in a section.
     *
     * @return Collection<int, FormField>
     */
    public function customFields(string $sectionKey): Collection
    {
        if (! $this->sectionVisible($sectionKey)) {
            return collect();
        }

        return ($this->sections->get($sectionKey)?->fields ?? collect())
            ->filter(fn (FormField $f) => ! $f->is_builtin && $f->is_visible)
            ->values();
    }

    /**
     * Visible sections created by the Admin.
     *
     * @return Collection<int, FormSection>
     */
    public function customSections(): Collection
    {
        return $this->sections
            ->filter(fn (FormSection $s) => ! $s->is_builtin && $s->is_visible)
            ->values();
    }

    /**
     * Every visible custom question, in any section.
     *
     * @return Collection<int, FormField>
     */
    public function allCustomFields(): Collection
    {
        return $this->fields
            ->filter(fn (FormField $f) => ! $f->is_builtin && $f->is_visible && $this->sectionVisible($f->section->key))
            ->values();
    }

    /**
     * Validation rules for the custom questions (submitted as custom[key]).
     */
    public function rules(): array
    {
        $rules = [];

        foreach ($this->allCustomFields() as $field) {
            $base  = "custom.{$field->key}";
            $need  = $field->is_required ? 'required' : 'nullable';
            $allow = array_keys($field->optionList());

            $rules[$base] = match ($field->type) {
                'number'     => [$need, 'numeric'],
                'date'       => [$need, 'date'],
                'textarea'   => [$need, 'string', 'max:2000'],
                'yes_no'     => [$need, 'in:0,1'],
                'select',
                'radio'      => [$need, Rule::in($allow)],
                'checkboxes' => [$need, 'array'],
                default      => [$need, 'string', 'max:255'],
            };

            if ($field->type === 'checkboxes') {
                $rules["{$base}.*"] = [Rule::in($allow)];
            }
        }

        return $rules;
    }

    /**
     * Friendly field names in error messages ("Farm size is required").
     */
    public function attributes(): array
    {
        return $this->allCustomFields()
            ->flatMap(fn (FormField $f) => ["custom.{$f->key}" => $f->label, "custom.{$f->key}.*" => $f->label])
            ->all();
    }

    /**
     * Save a member's answers to the visible custom questions.
     */
    public function saveAnswers(Member $member, array $answers): void
    {
        if (! Schema::hasTable('member_field_values')) {
            return;
        }

        foreach ($this->allCustomFields() as $field) {
            $value = $answers[$field->key] ?? null;

            if (is_array($value)) {
                $value = json_encode(array_values($value));
            }

            MemberFieldValue::updateOrCreate(
                ['member_id' => $member->id, 'form_field_id' => $field->id],
                ['value' => $value === '' ? null : $value]
            );
        }
    }

    /**
     * A member's saved answers as [field key => value] (checkboxes as arrays).
     */
    public function answersFor(?Member $member): array
    {
        if (! $member?->exists || ! Schema::hasTable('member_field_values')) {
            return [];
        }

        $byId = $this->fields->keyBy('id');

        return MemberFieldValue::where('member_id', $member->id)->get()
            ->mapWithKeys(function (MemberFieldValue $v) use ($byId) {
                $field = $byId->get($v->form_field_id);
                if (! $field) {
                    return [];
                }
                $value = $field->type === 'checkboxes' ? (json_decode((string) $v->value, true) ?: []) : $v->value;
                return [$field->key => $value];
            })
            ->all();
    }

    /**
     * Human-readable answer for display (option labels, "Yes"/"No").
     */
    public function displayAnswer(FormField $field, mixed $value): string
    {
        if ($value === null || $value === '' || $value === []) {
            return 'N/A';
        }

        $labels = $field->optionList();

        return match ($field->type) {
            'yes_no'     => (string) $value === '1' ? 'Yes' : 'No',
            'checkboxes' => collect((array) $value)->map(fn ($v) => $labels[$v] ?? $v)->implode(', '),
            'select',
            'radio'      => $labels[$value] ?? (string) $value,
            'date'       => rescue(fn () => \Illuminate\Support\Carbon::parse($value)->format('M d, Y'), (string) $value, false),
            default      => (string) $value,
        };
    }
}