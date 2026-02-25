<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTranslationsRequest;
use App\Models\Translation;
use App\Services\TranslationCompiler;

class TranslationController extends Controller
{
    public function index()
    {
        $groups = Translation::query()
            ->selectRaw('`group`, count(*) as total')
            ->groupBy('group')
            ->orderBy('group')
            ->get();

        return view('admin.translations.index', compact('groups'));
    }

    public function edit(string $group)
    {
        $translations = Translation::query()
            ->forGroup($group)
            ->orderBy('key')
            ->get()
            ->groupBy('key');

        return view('admin.translations.edit', compact('group', 'translations'));
    }

    public function update(UpdateTranslationsRequest $request, string $group, TranslationCompiler $compiler)
    {
        foreach ($request->validated('translations') as $item) {
            Translation::query()
                ->where('id', $item['id'])
                ->update(['value' => $item['value']]);
        }

        $compiler->compile();

        return redirect()
            ->route('admin.translations.edit', $group)
            ->with('success', 'Translations updated and compiled.');
    }
}
