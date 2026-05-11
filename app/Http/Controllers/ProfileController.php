<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $personnel = $user->personnel;

        return view('profile.edit', compact('user', 'personnel'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        AuditLog::log('updated', 'Updated profile email', $request->user());

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Compress and save
        $file = $request->file('profile_photo');
        $filename = 'profile-photos/' . $user->id . '_' . time() . '.jpg';
        $path = storage_path('app/public/' . $filename);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('profile-photos');

        // Resize to max 300x300, compress to 75% quality JPEG
        Image::read($file->getPathname())
            ->cover(300, 300)
            ->toJpeg(75)
            ->save($path);

        $user->update(['profile_photo' => $filename]);

        return Redirect::route('profile.edit')->with('success', 'Profile photo updated successfully.');
    }

    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile photo removed.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
