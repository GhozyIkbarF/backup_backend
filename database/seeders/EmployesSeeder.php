<?php

namespace Database\Seeders;

use App\Models\Employes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employes::create([
            'name' => 'rani',
            'phone' => '088823458764',
            'email' => 'rani@gmail.com',
            'address' => 'jl. jambu no 5, kebak kramat, Karanganyar',
            'gender' => 'Male',
            'photo' => null,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ]);
    }
}
