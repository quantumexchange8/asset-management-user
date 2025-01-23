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
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $form_step = $request->step;

        $validateData = [
            'name' => ['required', 'regex:/^[a-zA-Z0-9\p{Han}. ]+$/u', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required'],
            'country' => ['required'],
            'dial_code' => ['required'],
            'phone' => ['required', 'max:255', 'unique:' . User::class],
        ];

        switch ($form_step) {
            case 1:
                Validator::make($request->all(), $validateData)->validate();
                return back();

            case 2:
                $validateData['password'] = ['required', Rules\Password::defaults(), 'confirmed'];
                Validator::make($request->all(), $validateData)->validate();
                return back();

            default:
                $validateData['referral_code'] =  ['nullable', function($value, $fail){
                    if($value && !User::where('referral_code', $value)->exists()){ //check referral_code exists or not
                        $fail('The referral code is invalid or does not exist.');
                    }
                }];
                Validator::make($request->all(), $validateData)->validate();
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

        // Check if the referral code exists in the database
        if (!empty($request->referral_code)) {
            $referrer = User::where('referral_code', $request->referral_code)->first();

            if ($referrer) {

                if(empty($referrer->hierarchyList)){
                    $hierarchyList =  $hierarchyList = "-" . $referrer->id . "-";
                } else {
                    $hierarchyList = $referrer->hierarchyList . $referrer->id . "-";
                }

                $user->upline_id = $referrer->id;
                $user->hierarchyList = $hierarchyList;
            }
        }

        $user->setReferralId();

        $id_no = 'VTA' . Str::padLeft($user->id - 1, 6, "0");
        $user->id_number = $id_no;

        if ($request->hasFile('kyc_image')) {
            foreach ($request->file('kyc_image') as $file) {
                try {
                    $user->addMedia($file)->toMediaCollection('kyc_verification');
                } catch (FileDoesNotExist|FileIsTooBig $e) {
                    Log::error($e);
                    return back();
                }
            }
        }

        $user->save();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->type = 'cash_wallet';
        $wallet->address = RunningNumberService::getID('cash_wallet');
        $wallet->currency = 'CNY';
        $wallet->currency_symbol = '¥';
        $wallet->save();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->type = 'bonus_wallet';
        $wallet->address = RunningNumberService::getID('bonus_wallet');
        $wallet->currency = 'CNY';
        $wallet->currency_symbol = '¥';
        $wallet->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
