<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Services\ScheduleOptimizer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function __construct(private ScheduleOptimizer $optimizer) {}

    public function checkSchedule(Request $request)
    {
        try {
            // Controller cukup passing seluruh input ke service
            $result = $this->optimizer->checkClientAvailability($request->all());

            // Selalu 200; kalau mau 422 saat invalid, tinggal ubah baris di bawah:
            return response()->json($result, 200);

        } catch (ValidationException $e) {
            // Kalau service melempar ValidationException
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'available' => false,
                'message'   => 'Terjadi kesalahan saat memeriksa jadwal.',
                'error'     => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
