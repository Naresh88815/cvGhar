<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profile;
        return view('profile.show', compact('profile'));
    }

    public function create()
    {
        return view('profile.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'job_experience' => 'nullable|string',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'citizenship' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle file uploads
        $cvPath = $request->file('cv') ? $request->file('cv')->store('cvs', 'public') : null;
        $photoPath = $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null;

        Auth::user()->profile()->create([
            'address' => $request->address,
            'education' => $request->education,
            'occupation' => $request->occupation,
            'job_experience' => $request->job_experience,
            'cv' => $cvPath,
            'citizenship' => $request->citizenship,
            'photo' => $photoPath,
        ]);

        return redirect()->route('profile.index')->with('success', 'Profile created successfully.');
    }

    public function edit()
    {
        $profile = Auth::user()->profile;
        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'job_experience' => 'nullable|string',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'citizenship' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $profile = Auth::user()->profile;

        // Handle file uploads
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $profile->cv = $cvPath;
        }
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $profile->photo = $photoPath;
        }

        $profile->update($request->except(['cv', 'photo']));

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }

    public function destroy()
    {
        Auth::user()->profile()->delete();
        return redirect()->route('profile.index')->with('success', 'Profile deleted successfully.');
    }
}
