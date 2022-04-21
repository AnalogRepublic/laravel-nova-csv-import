<?php

namespace SimonHamp\LaravelNovaCsvImport\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimonHamp\LaravelNovaCsvImport\Importer;

class UploadController
{
    use Importable;

    public function handle(NovaRequest $request)
    {
        $data = Validator::make($request->all(), [
            'file' => 'required|file',
        ])->validate();

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            (new Importer)->toCollection($file, null);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'Sorry, we could not import that file'], 422);
        }

        // Store the file temporarily
        $hash = File::hash($file->getRealPath()) . "." . $extension;

        try {
            $file->move(storage_path('nova/laravel-nova-import-csv/tmp'), $hash);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'Sorry, we could not import that file'], 422);
        }

        return response()->json(['result' => 'success', 'file' => $hash]);
    }
}
