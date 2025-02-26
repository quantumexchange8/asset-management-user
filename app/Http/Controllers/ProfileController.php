<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile/Edit', [
            'front_identity_image' => $user->getFirstMediaUrl('front_identity'),
            'back_identity_image' => $user->getFirstMediaUrl('back_identity'),
        ]);
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
            'identity_number' => $request->identity_number,
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
            'front_identity' => ['required'],
            'back_identity' => ['required'],
        ])->setAttributeNames([
            'front_identity' => trans('public.front_identity'),
            'back_identity' => trans('public.back_identity'),
        ]);
        $validator->validate();

        $user = $request->user();

        if ($request->hasFile('front_identity')) {
            try {
                $user->clearMediaCollection('front_identity');
                $user->addMedia($request->front_identity)->toMediaCollection('front_identity');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                Log::error($e);
                throw ValidationException::withMessages(['front_identity' => trans('public.file_too_big')]);
            }

            $user->kyc_status = 'pending';
            $user->kyc_requested_at = now();
        }

        if ($request->hasFile('back_identity')) {
            try {
                $user->clearMediaCollection('back_identity');
                $user->addMedia($request->back_identity)->toMediaCollection('back_identity');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                Log::error($e);
                throw ValidationException::withMessages(['back_identity' => trans('public.file_too_big')]);
            }

            $user->kyc_status = 'pending';
            $user->kyc_requested_at = now();
        }

        $user->save();

        return back()->with('toast');
    }
}
