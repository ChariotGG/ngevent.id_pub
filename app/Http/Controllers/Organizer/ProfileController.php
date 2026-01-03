<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $organizer = auth()->user()->organizer;
        return view('organizer.settings.index', compact('organizer'));
    }

    public function update(Request $request): RedirectResponse
    {
        $organizer = auth()->user()->organizer;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        $organizer->update($request->only([
            'name', 'description', 'email', 'phone', 'website', 'instagram', 'address'
        ]));

        return back()->with('success', 'Pengaturan organizer berhasil diperbarui');
    }
}
