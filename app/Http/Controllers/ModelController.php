<?php

namespace App\Http\Controllers;

use App\Models\ModelOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required',
            'order_id' => 'required'
        ]);

        $type = 'model';
        $model = null;
        if ($request->file('model')) {
            $file = $request->file('model');
            $fileName = $request->order_id. now()->timestamp;
            $extension = $file->extension();
            $model = $fileName . '.' . $extension;
            Storage::putFileAs('public/model', $file, $model);
            $model = 'model/' . $fileName . '.' . $extension;
        }

        $model = ModelOrder::create([
            'type' => $type,
            'model' => $model,
            'order_id' => $request->order_id
        ]);
        
     
        if ($model) {
            return response()->json([
                'message' => 'Model created successfully',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $model = ModelOrder::findOrfail($id);

        if ($model) {
            Storage::delete('public/' . $model->model);
            $model->delete();
            return response()->json(['message' => 'Model deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Model not found'], 404);
        }
    }
}
