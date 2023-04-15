<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuktiBayar;
use Illuminate\Support\Facades\Storage;

class BuktiBayarController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'buktiBayar' => 'required',
            'order_id' => 'required'
        ]);

        $type = 'buktiBayar';
        $buktiBayar = null;
        if ($request->file('buktiBayar')) {
            $file = $request->file('buktiBayar');
            $fileName = $request->order_id. now()->timestamp;
            $extension = $file->extension();
            $buktiBayar = $fileName . '.' . $extension;
            Storage::putFileAs('public/buktiBayar', $file, $buktiBayar);
            $buktiBayar = 'buktiBayar/' . $fileName . '.' . $extension;
        }

        $buktiBayar = BuktiBayar::create([
            'type' => $type,
            'buktiBayar' => $buktiBayar,
            'order_id' => $request->order_id
        ]);
        
     
        if ($buktiBayar) {
            return response()->json([
                'message' => 'Bukti bayar created successfully',
            ], 201);
        }
    }

    public function updateDownPayment(Request $request, $id){

        $request->validate([
            'buktiBayar' => 'required',
            'order_id' => 'required'
        ]);

        $buktiBayars = BuktiBayar::find($id);

        $type = 'buktiBayar';
        if ($request->file('buktiBayar')) {
            Storage::delete('public/' . $buktiBayars->buktiBayar);
            $file = $request->file('buktiBayar');
            $fileName = $request->order_id. now()->timestamp;
            $extension = $file->extension();
            $buktiBayar = $fileName . '.' . $extension;
            Storage::putFileAs('public/buktiBayar', $file, $buktiBayar);
            $buktiBayar = 'buktiBayar/' . $fileName . '.' . $extension;
        }
        $buktiBayars->update([
            'type' => $type,
            'buktiBayar' => $buktiBayar,
        ]);
     
        if ($buktiBayars) {
            return response()->json([
                'message' => 'Update bukti bayar successfully',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $buktiBayar = BuktiBayar::findOrfail($id);

        if ($buktiBayar) {
            Storage::delete('public/' . $buktiBayar->buktiBayar);
            $buktiBayar->delete();
            return response()->json(['message' => 'buktiBayar deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'buktiBayar not found'], 404);
        }
    }
}
