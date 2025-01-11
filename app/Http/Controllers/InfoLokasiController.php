<?php

namespace App\Http\Controllers;

use App\Models\InfoLokasi;
use Illuminate\Http\Request;

class InfoLokasiController extends Controller
{
    public function index()
    {
        return response()->json(InfoLokasi::all());
    }

    public function admin()
    {
        return response()->json(InfoLokasi::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'deskripsi_tempat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        InfoLokasi::create($validated);
    
        return response()->json(['message' => 'Lokasi berhasil ditambahkan'], 201);
    }

    public function update(Request $request, $id)
    {
        $lokasi = InfoLokasi::findOrFail($id);
        $lokasi->update($request->all());
        return response()->json($lokasi);
    }

    public function destroy(InfoLokasi $infoLokasi)
    {
        $infoLokasi->delete();
        return response()->json(null, 204);
    }
}
