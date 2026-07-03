<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'temporary_password' => ['nullable', 'string'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        if ($user->must_change_password) {
            $temporaryPassword = $input['temporary_password'] ?? null;
            if (blank($temporaryPassword) || !Hash::check($temporaryPassword, $user->temporary_password_hash)) {
                throw ValidationException::withMessages([
                    'temporary_password' => __('The temporary password is invalid.'),
                ])->errorBag('updatePassword');
            }
        }

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'must_change_password' => false,
            'temporary_password_hash' => null,
        ])->save();
    }
}
