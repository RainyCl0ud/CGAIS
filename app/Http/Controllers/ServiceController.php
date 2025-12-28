<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('counselor_only');
    }

    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $service = Service::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // Ensure slug uniqueness
        if (Service::where('slug', $service->slug)->where('id', '!=', $service->id)->exists()) {
            $service->slug = $service->slug . '-' . $service->id;
            $service->save();
        }

        return Redirect::route('services.index')->with('status', 'service-created');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $service->update($request->only(['name','description']));

        // Update slug if name changed
        $newSlug = Str::slug($request->input('name'));
        if ($newSlug !== $service->slug) {
            $service->slug = $newSlug;
            if (Service::where('slug', $service->slug)->where('id', '!=', $service->id)->exists()) {
                $service->slug = $service->slug . '-' . $service->id;
            }
            $service->save();
        }

        return Redirect::route('services.index')->with('status', 'service-updated');
    }

    public function toggle(Service $service)
    {
        $service->is_active = ! $service->is_active;
        $service->save();

        return Redirect::route('services.index')->with('status', 'service-toggled');
    }
}
