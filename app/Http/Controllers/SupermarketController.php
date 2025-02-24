<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Supermarket;

class SupermarketController extends Controller
{
    // スーパーマーケット情報を保存
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'website' => 'nullable|url',
        ]);

        $supermarket = Supermarket::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'website' => $request->website,
        ]);

        return response()->json($supermarket, 201);
    }

    // 全てのスーパーマーケット情報を取得
    public function index()
    {
        return response()->json(Supermarket::all());
    }
}
