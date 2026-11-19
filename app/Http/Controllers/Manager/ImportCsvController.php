<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ImportCsvService;
use Illuminate\Http\Request;
use RuntimeException;

class ImportCsvController extends Controller
{
    public function importBahan(Request $request, ImportCsvService $service)
    {
        try {
            $hasil = $service->importBahanFile($request);

            return response()->json($hasil);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function importMenu(Request $request, ImportCsvService $service)
    {
        try {
            $hasil = $service->importMenuFile($request);

            return response()->json($hasil);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function importResep(Request $request, ImportCsvService $service)
    {
        try {
            $hasil = $service->importResepFile($request);

            return response()->json($hasil);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
