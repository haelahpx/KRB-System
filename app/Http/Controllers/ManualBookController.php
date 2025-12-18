<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManualBookController extends Controller
{
    /**
     * Tampilkan file manual book PDF yang ada di resources/docs/
     */
    public function show(Request $request)
    {
        // File disimpan di storage/app/manual book KRBS.pdf
        $path = storage_path('app/manual book KRBS.pdf');

        if (!file_exists($path)) {
            abort(404, 'Manual book not found');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="manual book KRBS.pdf"'
        ]);
    }
}
