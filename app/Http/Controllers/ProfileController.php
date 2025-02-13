<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'dial_code' => $request->dial_code,
            'phone' => $request->phone,
            'phone_number' => $request->dial_code . $request->phone,
        ]);

        if ($request->hasFile('profile_photo')) {
            $user->clearMediaCollection('profile_photo');
            $user->addMedia($request->profile_photo)->toMediaCollection('profile_photo');
        }

        if ($request->profile_action == 'remove') {
            $user->clearMediaCollection('profile_photo');
        }

        return back()->with('toast');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadKyc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kyc_image' => ['required'],
        ])->setAttributeNames([
            'kyc_image' => trans('public.kyc_file')
        ]);
        $validator->validate();

        $user = $request->user();

        if ($request->hasFile('kyc_image')) {
            $user->clearMediaCollection('kyc_image');
            foreach ($request->file('kyc_image') as $image) {
                $user->addMedia($image)->toMediaCollection('kyc_image');
            }

            $user->kyc_status = 'pending';
            $user->save();
        }

        return back()->with('toast');
    }
}
