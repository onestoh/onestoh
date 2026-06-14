<?php

namespace App\Http\Controllers;

use App\Models\Yard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class YardController extends Controller
{
    public function index()
    {
        $yards = Yard::where('user_id', auth()->id())->withCount('listings')->latest()->get();
        return view('yards.index', compact('yards'));
    }

    public function create()
    {
        return view('yards.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:150'],
            'description'         => ['nullable', 'string'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:100'],
            'county'              => ['required', 'string', 'max:100'],
            'city'                => ['nullable', 'string', 'max:100'],
            'address'             => ['nullable', 'string', 'max:255'],
            'business_reg_number' => ['nullable', 'string', 'max:100'],
            'kra_pin'             => ['nullable', 'string', 'max:50'],
            'logo'                => ['nullable', 'image', 'max:2048'],
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        $data['status'] = 'pending';

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('yards/logos', 'public');
        }

        Yard::create($data);

        return redirect()->route('yards.index')->with('success', 'Yard registered and pending verification.');
    }

    public function edit(Yard $yard)
    {
        abort_unless($yard->user_id === auth()->id(), 403);
        return view('yards.edit', compact('yard'));
    }

    public function update(Request $request, Yard $yard)
    {
        abort_unless($yard->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'email'       => ['nullable', 'email', 'max:100'],
            'county'      => ['required', 'string', 'max:100'],
            'city'        => ['nullable', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:255'],
            'logo'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($yard->logo) Storage::disk('public')->delete($yard->logo);
            $data['logo'] = $request->file('logo')->store('yards/logos', 'public');
        }

        $yard->update($data);

        return redirect()->route('yards.index')->with('success', 'Yard updated.');
    }

    public function publicProfile(string $slug)
    {
        $yard = Yard::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $yard->load(['listings' => fn($q) => $q->active()->with('primaryPhoto', 'category')]);
        return view('yards.show', compact('yard'));
    }

    public function destroy(Yard $yard)
    {
        abort_unless($yard->user_id === auth()->id(), 403);
        $yard->delete();
        return back()->with('success', 'Yard removed.');
    }
}
