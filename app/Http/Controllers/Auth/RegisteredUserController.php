<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create($referral = null): Response
    {
        return Inertia::render('Auth/Register', [
            'referral_code' => $referral,
        ]);
    }

    public function store(Request $request)
    {
        $form_step = $request->step;

        $rules = [
            'name' => ['required', 'regex:/^[a-zA-Z0-9\p{Han}. ]+$/u', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required'],
            'country' => ['required'],
            'dial_code' => ['required'],
            'phone' => ['required', 'max:255', 'unique:' . User::class],
        ];

        $attributeNames = [
            'name' => trans('public.name'),
            'username' => trans('public.username'),
            'email' => trans('public.email'),
            'country' => trans('public.country'),
            'dial_code' => trans('public.phone_code'),
            'phone' => trans('public.phone'),
            'phone_number' => trans('public.phone_number'),
            'password' => trans('public.password'),
            'front_identity' => trans('public.front_identity'),
            'back_identity' => trans('public.back_identity'),
            'identity_number' => trans('public.identity_number'),
        ];

        switch ($form_step) {
            case 1:
                Validator::make($request->all(), $rules)
                    ->setAttributeNames($attributeNames)
                    ->validate();

                return back();

            case 2:
                $passwordRules = [
                    'password' => ['required', 'confirmed', Password::min(8)->letters()->symbols()->numbers()->mixedCase()],
                ];

                Validator::make($request->all(), $passwordRules)
                    ->setAttributeNames($attributeNames)
                    ->validate();

                return back();

            case 3:
                $rules['password'] = ['required', 'confirmed', Password::min(8)->letters()->symbols()->numbers()->mixedCase()];
                $rules['front_identity'] = ['nullable', 'image', 'max:8000'];
                $rules['back_identity'] = ['nullable', 'image', 'max:8000'];
                $rules['referral_code'] = ['nullable'];
                $rules['identity_number'] = ['required'];

                Validator::make($request->all(), $rules)
                    ->setAttributeNames($attributeNames)
                    ->validate();

                break;

            default:
                $rules['password'] = ['required', 'confirmed', Password::min(8)->letters()->symbols()->numbers()->mixedCase()];

                Validator::make($request->all(), $rules)
                    ->setAttributeNames($attributeNames)
                    ->validate();
                break;
        }

        $dial_code = $request->dial_code;
        $country = Country::find($request->country['id']);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->dial_code = $dial_code['phone_code'];
        $user->phone = $request->phone;
        $user->phone_number = $request->phone_number;
        $user->country_id = $country->id;
        $user->nationality = $country->nationality;
        $user->password =  Hash::make($request->password);
        $user->identity_number = $request->identity_number;

        // Check if the referral code exists in the database
        if (!empty($request->referral_code)) {
            $referrer = User::where('referral_code', $request->referral_code)->first();

            if ($referrer) {

                if(empty($referrer->hierarchyList)){
                    $hierarchyList = "-" . $referrer->id . "-";
                } else {
                    $hierarchyList = $referrer->hierarchyList . $referrer->id . "-";
                }

                $user->upline_id = $referrer->id;
                $user->hierarchyList = $hierarchyList;
            }
        }

        $user->setReferralId();

        $id_no = 'MID' . Str::padLeft($user->id - 1, 6, "0");
        $user->id_number = $id_no;

        if ($request->hasFile('front_identity')) {
            try {
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
                $user->addMedia($request->back_identity)->toMediaCollection('back_identity');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                Log::error($e);
                throw ValidationException::withMessages(['back_identity' => trans('public.file_too_big')]);
            }

            $user->kyc_status = 'pending';
            $user->kyc_requested_at = now();
        }

        $user->save();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->type = 'cash_wallet';
        $wallet->address = RunningNumberService::getID('cash_wallet');
        $wallet->currency = 'USD';
        $wallet->currency_symbol = '$';
        $wallet->save();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->type = 'bonus_wallet';
        $wallet->address = RunningNumberService::getID('bonus_wallet');
        $wallet->currency = 'USD';
        $wallet->currency_symbol = '$';
        $wallet->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
