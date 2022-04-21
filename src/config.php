<?php

return [
    'disk' => null, // only [null, 's3'] work for now due to how mattwebsite/excel is handling file reading
    'importer' => SimonHamp\LaravelNovaCsvImport\Importer::class,
];
