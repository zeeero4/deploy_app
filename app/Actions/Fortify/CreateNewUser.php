<?php

namespace App\Actions\Fortify;

use App\Models\Company;
use App\Models\User;
use Hamcrest\Collection\IsEmptyTraversable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'company_name' => ['string', 'max:255', 'unique:companies,name'],
        ])->validate();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            if (isset($input['company_name'])) {
                Company::create([
                    'user_id' => $user->id,
                    'name' => $input['company_name'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
        return $user;
    }
}