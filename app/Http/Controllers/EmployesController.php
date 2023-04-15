<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployesResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Employes as ModelsEmployes;
use App\Http\Resources\EmployesDetailResource;

class EmployesController extends Controller
{
    public function index()
    {
        $employes = ModelsEmployes::all();
        $data = [];

        foreach ($employes as $employe) {
            $photo = $employe->photo;
            if ($photo) {
                $employe->photo = asset('storage/' . $photo);
            } else {
                $employe->photo = '';
            }

            $data[] = [
                'id' => $employe->id,
                'name' => $employe->name,
                'phone' => $employe->phone,
                'email' => $employe->email,
                'gender' => $employe->gender,
                'address' => $employe->address,
                'photo' =>  $employe->photo,
            ];
        }

        return response()->json($data);
        //di edit sesuai keinginan
        // return EmployesResource::collection($employes);
    }


    public function show($id)
    {
        $employes = ModelsEmployes::findOrFail($id);

        $photo = $employes->photo;
        if ($photo) {
            $photo = asset('storage/' . $employes->photo);
        } else {
            $photo = '';
        }
        $data = [
            'name' => $employes->name,
            'phone' => $employes->phone,
            'email' => $employes->email,
            'address' => $employes->address,
            'gender' => $employes->gender,
            'photo' => $photo
        ];


        return response()->json($data);
        // return new EmployesDetailResource($employes);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'phone' => 'required|max:13',
            'address' => 'required',
            'gender' => 'required',
            // 'photo' => 'image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        $photo = null;
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $fileName = $request->phone . now()->timestamp;
            $extension = $file->extension();
            $photo = $fileName . '.' . $extension;
            Storage::putFileAs('public/photo', $file, $photo);
            $photo = 'photo/' . $fileName . '.' . $extension;
        }

        $employe = ModelsEmployes::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'gender' => $request->gender,
            'photo' => $photo
        ]);
        
     
        if ($employe) {
            return response()->json([
                'message' => 'Employee created successfully',
                'employee' => $request->name
            ], 201);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:50',
            'phone' => 'required',
            'address' => 'required',
            'gender' => 'required',
        ]);

        $employee = ModelsEmployes::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $photo = null;
        if($request->hasFile('photo')) {
            Storage::delete('public/'. $employee->photo);
            $file = $request->file('photo');
            $fileName = $employee->phone. now()->timestamp;
            $extension = $file->extension();
            $photo = $fileName . '.' . $extension;
            Storage::putFileAs('public/photo', $file, $photo);
            $photo = 'photo/'. $fileName . '.' . $extension;
        }else if ($request->photo == 'hapus foto') {
            Storage::delete('public/' . $employee->photo);
            $photo = null;  
        }else if($request->photo == asset('storage/' . $employee->photo)) {
            $photo = $employee->photo;
        }else{
            $photo = null;
        }
        $employee->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'address' => $request->address,
            'photo' => $photo,
        ]);

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $request
        ], 200);
    }


    public function destroy($id)
    {
        $employe = ModelsEmployes::findOrfail($id);

        if ($employe) {
            Storage::delete('public/' . $employe->photo);
            $employe->delete();
            return response()->json(['message' => 'Employe deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Employe not found'], 404);
        }
    }

}
