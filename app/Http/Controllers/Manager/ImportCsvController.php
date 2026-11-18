<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ImportCsvService;
use Illuminate\Http\Request;
use RuntimeException;

class ImportCsvController extends Controller
{
    public function store(Request $request, ImportCsvService $service)
    {
        try {
            $hasil = $service->importSemua($request);

            return response()->json($hasil);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }
    }
}
