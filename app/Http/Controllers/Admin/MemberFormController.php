<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use App\Models\FormSection;
use App\Support\MemberForm;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Admin: Member Form Builder.
 * Show/hide sections and questions, edit dropdown options,
 * and add custom questions to the member registration form.
 */
class MemberFormController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(function (Request $request, Closure $next) {
                abort_unless($request->user()?->isAdmin(), 403, 'Only Admins can manage the member form.');
                return $next($request);
            }),
        ];
    }

    public function index(): View
    {
        $sections = FormSection::with('fields')->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.member-form.index', [
            'sections' => $sections,
            'types'    => FormField::TYPES,
        ]);
    }

    // ---------------------------------------------------------------------
    // Sections
    // ---------------------------------------------------------------------

    public function storeSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        FormSection::create([
            ...$data,
            'key'        => $this->uniqueKey(FormSection::class, $data['title'], 'section_'),
            'is_builtin' => false,
            'is_visible' => true,
            'sort_order' => (int) FormSection::max('sort_order') + 10,
        ]);

        return $this->done('Section added.');
    }

    public function updateSection(Request $request, FormSection $section): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $section->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order'  => $data['sort_order'] ?? $section->sort_order,
        ]);

        return $this->done('Section updated.');
    }

    public function toggleSection(FormSection $section): RedirectResponse
    {
        if ($section->is_locked) {
            return back()->with('error', "\"{$section->title}\" is required for the ID card and can't be hidden.");
        }

        $section->update(['is_visible' => ! $section->is_visible]);

        return $this->done($section->is_visible ? "\"{$section->title}\" is now shown to members." : "\"{$section->title}\" is now hidden.");
    }

    public function destroySection(FormSection $section): RedirectResponse
    {
        if ($section->is_builtin) {
            return back()->with('error', 'Built-in sections can be hidden but not deleted.');
        }

        $section->delete(); // its questions and answers are removed too

        return $this->done('Section deleted.');
    }

    // ---------------------------------------------------------------------
    // Questions
    // ---------------------------------------------------------------------

    public function storeField(Request $request): RedirectResponse
    {
        $data = $this->validateField($request, null);

        $section = FormSection::findOrFail($data['form_section_id']);

        FormField::create([
            'form_section_id' => $section->id,
            'key'             => $this->uniqueKey(FormField::class, $data['label'], 'q_'),
            'label'           => $data['label'],
            'type'            => $data['type'],
            'help_text'       => $data['help_text'] ?? null,
            'options'         => in_array($data['type'], FormField::OPTION_TYPES, true) ? FormField::parseOptions($data['options_text'] ?? '') : null,
            'is_builtin'      => false,
            'is_visible'      => true,
            'is_required'     => $request->boolean('is_required'),
            'sort_order'      => $data['sort_order'] ?? ((int) $section->fields()->max('sort_order') + 10),
        ]);

        return $this->done('Question added.');
    }

    public function updateField(Request $request, FormField $field): RedirectResponse
    {
        $data = $this->validateField($request, $field);

        $changes = [
            'label'      => $data['label'],
            'help_text'  => $data['help_text'] ?? null,
            'sort_order' => $data['sort_order'] ?? $field->sort_order,
        ];

        // Built-in questions keep their type, section and saving rules;
        // only their wording and dropdown options can change.
        if (! $field->is_builtin) {
            $changes['type']            = $data['type'];
            $changes['form_section_id'] = $data['form_section_id'];
            $changes['is_required']     = $request->boolean('is_required');
        }

        $type = $changes['type'] ?? $field->type;
        if (in_array($type, FormField::OPTION_TYPES, true)) {
            $changes['options'] = FormField::parseOptions($data['options_text'] ?? '');
        }

        $field->update($changes);

        return $this->done('Question updated.');
    }

    public function toggleField(FormField $field): RedirectResponse
    {
        if ($field->is_locked) {
            return back()->with('error', "\"{$field->label}\" is required for the ID card and can't be hidden.");
        }

        $field->update(['is_visible' => ! $field->is_visible]);

        return $this->done($field->is_visible ? 'Question is now shown.' : 'Question is now hidden.');
    }

    public function destroyField(FormField $field): RedirectResponse
    {
        if ($field->is_builtin) {
            return back()->with('error', 'Built-in questions can be hidden but not deleted.');
        }

        $field->delete(); // members' answers to it are removed too

        return $this->done('Question deleted.');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function validateField(Request $request, ?FormField $field): array
    {
        $builtin = $field?->is_builtin ?? false;
        $type    = $builtin ? $field->type : $request->input('type');

        $data = $request->validate([
            'label'           => ['required', 'string', 'max:255'],
            'help_text'       => ['nullable', 'string', 'max:255'],
            'sort_order'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'type'            => $builtin ? ['nullable'] : ['required', Rule::in(array_keys(FormField::TYPES))],
            'form_section_id' => $builtin ? ['nullable'] : ['required', 'exists:form_sections,id'],
            'options_text'    => [
                in_array($type, FormField::OPTION_TYPES, true) ? 'required' : 'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'options_text.required' => 'Add at least one option (one per line).',
        ]);

        if (in_array($type, FormField::OPTION_TYPES, true) && empty(FormField::parseOptions($data['options_text'] ?? ''))) {
            throw ValidationException::withMessages(['options_text' => 'Add at least one option (one per line).']);
        }

        return $data;
    }

    private function uniqueKey(string $model, string $text, string $prefix): string
    {
        $base = $prefix . (Str::slug(Str::limit($text, 40, ''), '_') ?: 'item');
        $key  = $base;
        $i    = 2;

        while ($model::where('key', $key)->exists()) {
            $key = "{$base}_{$i}";
            $i++;
        }

        return $key;
    }

    private function done(string $message): RedirectResponse
    {
        MemberForm::flush();

        return back()->with('success', $message);
    }
}