<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Design as ModelsDesign;

class DesignController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'design' => 'required',
            'order_id' => 'required'
        ]);

        $type = 'design';
        $design = null;
        if ($request->file('design')) {
            $file = $request->file('design');
            $fileName = $request->order_id. now()->timestamp;
            $extension = $file->extension();
            $design = $fileName . '.' . $extension;
            Storage::putFileAs('public/design', $file, $design);
            $design = 'design/' . $fileName . '.' . $extension;
        }

        $design = ModelsDesign::create([
            'type' => $type,
            'design' => $design,
            'order_id' => $request->order_id
        ]);
        
     
        if ($design) {
            return response()->json([
                'message' => 'Design created successfully',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $design = ModelsDesign::findOrfail($id);

        if ($design) {
            Storage::delete('public/' . $design->design);
            $design->delete();
            return response()->json(['message' => 'Design deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Design not found'], 404);
        }
    }
}
