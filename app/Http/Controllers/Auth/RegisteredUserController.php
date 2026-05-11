<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isUniformed = $request->input('personnel_type') === 'Uniformed';

        $request->validate([
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'personnel_type' => 'required|in:Uniformed,Non-Uniformed',
            'rank'           => $isUniformed ? 'required|string|max:100' : 'nullable',
            'badge_number'   => $isUniformed ? 'required|string|max:50|unique:personnel,badge_number' : 'nullable',
            'position_title' => !$isUniformed ? 'required|string|max:150' : 'nullable',
            'last_name'      => 'required|string|max:100',
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'age'            => 'required|integer|min:1|max:100',
            'unit'           => 'required|string|max:100',
            'station'        => 'required|string|max:100',
        ]);

        $personnelData = [
            'personnel_type' => $request->personnel_type,
            'last_name'      => $request->last_name,
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'gender'         => $request->gender,
            'age'            => $request->age,
            'unit'           => $request->unit,
            'station'        => $request->station,
        ];

        if ($isUniformed) {
            $personnelData['rank'] = $request->rank;
            $personnelData['badge_number'] = $request->badge_number;
            $personnelData['position_title'] = null;
        } else {
            $personnelData['position_title'] = $request->position_title;
            $personnelData['rank'] = null;
            $personnelData['badge_number'] = null;
        }

        $user = DB::transaction(function () use ($request, $personnelData) {
            $personnel = Personnel::create($personnelData);

            $fullName = $personnelData['last_name'] . ', ' . $personnelData['first_name'];

            $user = User::create([
                'name'         => $fullName,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'personnel_id' => $personnel->id,
            ]);

            return $user;
        });

        event(new Registered($user));

        return redirect(route('login'))->with('status', 'Registration successful! Please check your email to verify your account. After verification, an admin will review and approve your account.');
    }
}
