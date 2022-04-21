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

    /**
     * @var Importer
     */
    protected $importer;

    public function __construct()
    {
        $class = config('nova-csv-importer.importer');
        $this->importer = new $class;
    }

    public function handle(NovaRequest $request)
    {
        $data = Validator::make($request->all(), [
            'file' => 'required|file',
        ])->validate();

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $disk = config('nova-csv-importer.disk');

        try {
            (new Importer)->toCollection($file, null);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'Sorry, we could not import that file'], 422);
        }

        // Store the file temporarily
        $hash = File::hash($file->getRealPath()) . "." . $extension;

        try {
            if ($disk === null) {
                $file->move(storage_path('nova/laravel-nova-import-csv/tmp'), $hash);
            } else {
                $file->storeAs('nova/laravel-nova-import-csv/tmp/', $hash, $disk);
            }
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'Sorry, we could not import that file'], 422);
        }

        return response()->json(['result' => 'success', 'file' => $hash]);
    }
}
