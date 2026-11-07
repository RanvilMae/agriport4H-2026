<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black leading-tight text-slate-800">Member Form Builder</h2>
                <p class="mt-1 text-xs font-bold tracking-widest uppercase text-slate-400">
                    Choose what members are asked when they register or update their profile
                </p>
            </div>
            <button type="button" x-data @click="$dispatch('open-section', {})"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-black text-white transition-all shadow-xl bg-emerald-600 rounded-2xl shadow-emerald-100 hover:bg-emerald-700 active:scale-95">
                <i class="text-xs fas fa-layer-group"></i> Add Section
            </button>
        </div>
    </x-slot>

    @php
        $fieldUpdateUrl   = route('admin.member-form.fields.update', '__ID__');
        $sectionUpdateUrl = route('admin.member-form.sections.update', '__ID__');

        // Re-open the right dialog with the typed values if saving failed
        $reopen = null;
        if ($errors->any() && old('_dialog')) {
            $reopen = [
                'dialog' => old('_dialog'),
                'data'   => [
                    'id'              => old('_id') ?: null,
                    'is_builtin'      => (bool) old('_builtin'),
                    'form_section_id' => old('form_section_id'),
                    'label'           => old('label', ''),
                    'title'           => old('title', ''),
                    'description'     => old('description', ''),
                    'type'            => old('type', 'text'),
                    'help_text'       => old('help_text', ''),
                    'options_text'    => old('options_text', ''),
                    'is_required'     => (bool) old('is_required'),
                    'sort_order'      => old('sort_order'),
                ],
            ];
        }
    @endphp

    <div class="px-4 py-8 mx-auto max-w-6xl"
         x-data="memberFormBuilder({
            fieldStoreUrl:    @js(route('admin.member-form.fields.store')),
            fieldUpdateUrl:   @js($fieldUpdateUrl),
            sectionStoreUrl:  @js(route('admin.member-form.sections.store')),
            sectionUpdateUrl: @js($sectionUpdateUrl),
            optionTypes:      @js(\App\Models\FormField::OPTION_TYPES),
            reopen:           @js($reopen),
         })"
         @open-section.window="openSection($event.detail)"
         @keydown.escape.window="fieldOpen = false; sectionOpen = false">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-6 text-xs font-bold border rounded-2xl bg-emerald-50 border-emerald-100 text-emerald-700">
                <i class="fas fa-circle-check text-emerald-600"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 mb-6 text-xs font-bold border rounded-2xl bg-amber-50 border-amber-200 text-amber-800">
                <i class="fas fa-triangle-exclamation text-amber-600"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 mb-6 text-xs font-bold text-red-700 border border-red-100 rounded-2xl bg-red-50">
                <p class="mb-1 font-black uppercase tracking-widest text-[10px]">Couldn't save</p>
                <ul class="ml-4 list-disc">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- How it works --}}
        <div class="grid grid-cols-1 gap-3 mb-8 sm:grid-cols-3">
            @foreach([
                ['fa-eye', 'Show or hide', 'Hide a whole section or a single question. Hidden questions keep any answers already saved.'],
                ['fa-list', 'Edit dropdowns', 'Change the choices in any dropdown or checklist, one option per line.'],
                ['fa-circle-plus', 'Add questions', 'Add your own questions to any section, or create a new section.'],
            ] as [$icon, $title, $text])
                <div class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100">
                    <div class="flex items-center gap-2 text-xs font-black text-slate-700">
                        <i class="fas {{ $icon }} text-emerald-600"></i> {{ $title }}
                    </div>
                    <p class="mt-1 text-[11px] font-semibold text-slate-500 leading-snug">{{ $text }}</p>
                </div>
            @endforeach
        </div>

        @if($sections->isEmpty())
            <div class="p-10 text-center bg-white border rounded-[2rem] border-slate-100">
                <i class="text-3xl fas fa-database text-slate-300"></i>
                <h3 class="mt-3 text-base font-black text-slate-700">No form settings yet</h3>
                <p class="mt-1 text-xs font-semibold text-slate-400">Run <code class="px-1 rounded bg-slate-100">php artisan migrate</code> to load the default member form.</p>
            </div>
        @endif

        {{-- Sections --}}
        <div class="space-y-6">
            @foreach($sections as $section)
                @php $sectionShown = $section->is_locked || $section->is_visible; @endphp
                <div class="bg-white rounded-[2rem] shadow-sm border overflow-hidden {{ $sectionShown ? 'border-slate-100' : 'border-dashed border-slate-300 opacity-75' }}">

                    {{-- Section header --}}
                    <div class="flex flex-col gap-3 px-6 py-4 border-b sm:flex-row sm:items-center sm:justify-between bg-slate-50/60 border-slate-100">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-black text-slate-800">{{ $section->title }}</h3>
                                @if($section->is_builtin)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-200 text-slate-600">Built-in</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-violet-100 text-violet-700">Custom</span>
                                @endif
                                @if($section->is_locked)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-sky-100 text-sky-700" title="Needed for the ID card"><i class="fas fa-lock text-[8px]"></i> Always shown</span>
                                @elseif(! $section->is_visible)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700"><i class="fas fa-eye-slash text-[8px]"></i> Hidden</span>
                                @endif
                            </div>
                            @if($section->description)
                                <p class="mt-0.5 text-[11px] font-semibold text-slate-400">{{ $section->description }}</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button type="button" @click="openField({ form_section_id: {{ $section->id }} })"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100">
                                <i class="fas fa-plus"></i> Add Question
                            </button>
                            <button type="button"
                                    @click="openSection(@js(['id' => $section->id, 'title' => $section->title, 'description' => $section->description, 'sort_order' => $section->sort_order]))"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                                <i class="fas fa-pen"></i> Rename
                            </button>
                            @unless($section->is_locked)
                                <form method="POST" action="{{ route('admin.member-form.sections.toggle', $section) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black uppercase tracking-wider rounded-xl border {{ $section->is_visible ? 'text-slate-600 bg-white border-slate-200 hover:bg-slate-50' : 'text-white bg-slate-800 border-slate-800 hover:bg-black' }}">
                                        <i class="fas {{ $section->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        {{ $section->is_visible ? 'Hide Section' : 'Show Section' }}
                                    </button>
                                </form>
                            @endunless
                            @unless($section->is_builtin)
                                <form method="POST" action="{{ route('admin.member-form.sections.destroy', $section) }}"
                                      onsubmit="return confirm('Delete this section, its questions, and all members\' answers to them?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </div>

                    {{-- Questions --}}
                    <ul class="divide-y divide-slate-100">
                        @forelse($section->fields as $field)
                            @php
                                $fieldShown = $field->is_locked || $field->is_visible;
                                $opts = $field->optionList();
                            @endphp
                            <li class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between {{ $fieldShown ? '' : 'bg-slate-50/70' }}">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold {{ $fieldShown ? 'text-slate-800' : 'text-slate-400 line-through' }}">{{ $field->label }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-100 text-slate-500">
                                            {{ \App\Models\FormField::TYPES[$field->type] ?? ($field->type === 'file' ? 'File upload' : $field->type) }}
                                        </span>
                                        @if($field->is_required && ! $field->is_builtin)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-700">Required</span>
                                        @endif
                                        @if($field->is_locked)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-sky-100 text-sky-700" title="Needed for the ID card"><i class="fas fa-lock text-[8px]"></i> Always shown</span>
                                        @elseif(! $field->is_visible)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700"><i class="fas fa-eye-slash text-[8px]"></i> Hidden</span>
                                        @endif
                                        @unless($field->is_builtin)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-violet-100 text-violet-700">Custom</span>
                                        @endunless
                                    </div>
                                    @if($field->help_text)
                                        <p class="mt-0.5 text-[11px] font-semibold text-slate-400">{{ $field->help_text }}</p>
                                    @endif
                                    @if($field->hasOptions())
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach(array_slice($opts, 0, 6, true) as $label)
                                                <span class="px-2 py-0.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg">{{ $label }}</span>
                                            @endforeach
                                            @if(count($opts) > 6)
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-slate-400">+{{ count($opts) - 6 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @unless($field->type === 'file')
                                        <button type="button"
                                                @click="openField(@js([
                                                    'id'              => $field->id,
                                                    'is_builtin'      => $field->is_builtin,
                                                    'form_section_id' => $field->form_section_id,
                                                    'label'           => $field->label,
                                                    'type'            => $field->type,
                                                    'help_text'       => $field->help_text,
                                                    'options_text'    => $field->optionsAsText(),
                                                    'is_required'     => $field->is_required,
                                                    'sort_order'      => $field->sort_order,
                                                ]))"
                                                class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                    @endunless
                                    @unless($field->is_locked)
                                        <form method="POST" action="{{ route('admin.member-form.fields.toggle', $field) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $field->is_visible ? 'Hide from members' : 'Show to members' }}"
                                                    class="px-3 py-2 text-[10px] font-black uppercase tracking-wider rounded-xl border {{ $field->is_visible ? 'text-slate-600 bg-white border-slate-200 hover:bg-slate-50' : 'text-white bg-slate-800 border-slate-800 hover:bg-black' }}">
                                                <i class="fas {{ $field->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                {{ $field->is_visible ? 'Hide' : 'Show' }}
                                            </button>
                                        </form>
                                    @endunless
                                    @unless($field->is_builtin)
                                        <form method="POST" action="{{ route('admin.member-form.fields.destroy', $field) }}"
                                              onsubmit="return confirm('Delete this question and all members\' answers to it?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-2 text-[10px] font-black uppercase text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-8 text-xs italic font-semibold text-center text-slate-400">
                                No questions yet. Click <span class="not-italic font-black text-emerald-700">Add Question</span> to add one.
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- ================= Question dialog ================= --}}
        <div x-show="fieldOpen" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="fieldOpen = false" class="w-full max-w-lg bg-white shadow-2xl rounded-[2rem] overflow-hidden">
                <form method="POST" :action="field.id ? fieldUpdateUrl.replace('__ID__', field.id) : fieldStoreUrl">
                    @csrf
                    <template x-if="field.id"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="_dialog" value="field">
                    <input type="hidden" name="_id" :value="field.id ?? ''">
                    <input type="hidden" name="_builtin" :value="field.is_builtin ? 1 : 0">

                    <div class="flex items-center justify-between px-6 py-4 border-b bg-slate-50 border-slate-100">
                        <h3 class="text-xs font-black tracking-widest uppercase text-slate-700"
                            x-text="field.id ? (field.is_builtin ? 'Edit built-in question' : 'Edit question') : 'Add question'"></h3>
                        <button type="button" @click="fieldOpen = false" class="p-2 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Question</label>
                            <input type="text" name="label" x-model="field.label" required maxlength="255"
                                   class="w-full px-3 py-2 text-sm font-semibold border bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>

                        <div x-show="!field.is_builtin" class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Answer type</label>
                                <select name="type" x-model="field.type" :disabled="field.is_builtin"
                                        class="w-full px-3 py-2 text-sm font-semibold border bg-slate-50 border-slate-200 rounded-xl outline-none">
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Section</label>
                                <select name="form_section_id" x-model="field.form_section_id" :disabled="field.is_builtin"
                                        class="w-full px-3 py-2 text-sm font-semibold border bg-slate-50 border-slate-200 rounded-xl outline-none">
                                    @foreach($sections as $s)
                                        <option value="{{ $s->id }}">{{ $s->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div x-show="hasOptions()">
                            <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Options <span class="normal-case font-semibold tracking-normal">(one per line)</span></label>
                            <textarea name="options_text" x-model="field.options_text" rows="6" :required="hasOptions()"
                                      placeholder="Owner&#10;Tenant&#10;Lessee"
                                      class="w-full px-3 py-2 font-mono text-xs border bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
                            <p class="mt-1 text-[10px] font-semibold leading-snug text-slate-400">
                                To show a longer name than what's saved, write <code class="px-1 rounded bg-slate-100">saved value | shown name</code>,
                                e.g. <code class="px-1 rounded bg-slate-100">Owner | Owner / Titled</code>.
                                <span x-show="field.is_builtin">Keep existing saved values unchanged so members' current answers stay selected.</span>
                            </p>
                        </div>

                        <div>
                            <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Help text <span class="normal-case font-semibold tracking-normal">(optional)</span></label>
                            <input type="text" name="help_text" x-model="field.help_text" maxlength="255" placeholder="Shown under the question"
                                   class="w-full px-3 py-2 text-sm border bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label x-show="!field.is_builtin" class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" x-model="field.is_required" class="rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-700">Members must answer this</span>
                            </label>
                            <div class="flex items-center gap-2 ml-auto">
                                <label class="text-[10px] font-black tracking-widest uppercase text-slate-400">Order</label>
                                <input type="number" name="sort_order" x-model="field.sort_order" min="0" max="9999" placeholder="auto"
                                       class="w-20 px-2 py-1.5 text-xs font-semibold border bg-slate-50 border-slate-200 rounded-lg outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 px-6 py-4 border-t bg-slate-50 border-slate-100">
                        <button type="button" @click="fieldOpen = false" class="px-5 py-2.5 text-xs font-black tracking-wider uppercase text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 text-xs font-black tracking-wider text-white uppercase bg-emerald-600 rounded-xl hover:bg-emerald-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= Section dialog ================= --}}
        <div x-show="sectionOpen" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="sectionOpen = false" class="w-full max-w-md bg-white shadow-2xl rounded-[2rem] overflow-hidden">
                <form method="POST" :action="section.id ? sectionUpdateUrl.replace('__ID__', section.id) : sectionStoreUrl">
                    @csrf
                    <template x-if="section.id"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="_dialog" value="section">
                    <input type="hidden" name="_id" :value="section.id ?? ''">

                    <div class="flex items-center justify-between px-6 py-4 border-b bg-slate-50 border-slate-100">
                        <h3 class="text-xs font-black tracking-widest uppercase text-slate-700" x-text="section.id ? 'Edit section' : 'Add section'"></h3>
                        <button type="button" @click="sectionOpen = false" class="p-2 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Section title</label>
                            <input type="text" name="title" x-model="section.title" required maxlength="120" placeholder="e.g. Youth Leadership"
                                   class="w-full px-3 py-2 text-sm font-semibold border bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 text-[10px] font-black tracking-widest uppercase text-slate-400">Description <span class="normal-case font-semibold tracking-normal">(optional)</span></label>
                            <input type="text" name="description" x-model="section.description" maxlength="255"
                                   class="w-full px-3 py-2 text-sm border bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div x-show="section.id" class="flex items-center gap-2">
                            <label class="text-[10px] font-black tracking-widest uppercase text-slate-400">Order</label>
                            <input type="number" name="sort_order" x-model="section.sort_order" min="0" max="9999"
                                   class="w-24 px-2 py-1.5 text-xs font-semibold border bg-slate-50 border-slate-200 rounded-lg outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 px-6 py-4 border-t bg-slate-50 border-slate-100">
                        <button type="button" @click="sectionOpen = false" class="px-5 py-2.5 text-xs font-black tracking-wider uppercase text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 text-xs font-black tracking-wider text-white uppercase bg-emerald-600 rounded-xl hover:bg-emerald-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function memberFormBuilder(config) {
            const blankField = () => ({ id: null, is_builtin: false, form_section_id: '', label: '', type: 'text', help_text: '', options_text: '', is_required: false, sort_order: '' });
            const blankSection = () => ({ id: null, title: '', description: '', sort_order: '' });

            return {
                ...config,
                fieldOpen: false,
                sectionOpen: false,
                field: blankField(),
                section: blankSection(),

                init() {
                    if (!this.reopen) return;
                    if (this.reopen.dialog === 'field') this.openField(this.reopen.data);
                    if (this.reopen.dialog === 'section') this.openSection(this.reopen.data);
                },
                openField(data) {
                    this.field = { ...blankField(), ...data };
                    this.field.form_section_id = String(this.field.form_section_id ?? '');
                    this.fieldOpen = true;
                },
                openSection(data) {
                    this.section = { ...blankSection(), ...data };
                    this.sectionOpen = true;
                },
                hasOptions() {
                    return this.optionTypes.includes(this.field.type);
                },
            };
        }
    </script>
</x-app-layout>