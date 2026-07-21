<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogBackupService;
use Illuminate\Http\Request;
use RuntimeException;

class CatalogBackupController extends Controller
{
    public function index(Request $request, CatalogBackupService $backups)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.backups.catalog', [
            'summary' => $backups->summary(),
        ]);
    }

    public function download(Request $request, CatalogBackupService $backups)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ], [
            'category_ids.required' => 'Selecciona al menos una categoria para respaldar.',
            'category_ids.min' => 'Selecciona al menos una categoria para respaldar.',
        ]);

        try {
            $backup = $backups->create($validated['category_ids']);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['backup' => $exception->getMessage()]);
        }

        return response()
            ->download($backup['path'], $backup['filename'])
            ->deleteFileAfterSend(true);
    }
}
