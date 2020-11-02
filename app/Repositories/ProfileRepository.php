<?php

namespace App\Repositories;

use App\Mail\ResetPassword;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * ProfileRepository
 * @package App\Repositories
 */
class ProfileRepository
{
    // how long the PIN code can live in minutes
    const PIN_TIMEOUT = 5;

    /**
     * Authenticate user
     *
     * @param string $email
     * @param string $password
     * @param string $deviceName
     * @return mixed
     * @throws ValidationException
     */
    public function authenticate($email, $password, $deviceName)
    {
        // check if the user exists
        $user = User::where('email', $email)->first();
        if (!$user || ! Hash::check($password, $user->password)) {
            abort(
                Response::HTTP_FORBIDDEN,
                'The provided credentials are incorrect.'
            );
        }

        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'token' => $token,
            'profile' => $user->profile,
        ];
    }

    /**
     * Delete the user's current token
     */
    public function revoke()
    {
        Auth::user()->currentAccessToken()->delete();
    }

    /**
     * Delete all the user's token
     */
    public function revokeAll()
    {
        Auth::user()->tokens()->delete();
    }

    /**
     * Retrieves user's profile
     *
     * @return Profile
     */
    public function get()
    {
        return Auth::user()->profile;
    }

    /**
     * Create user and profile
     *
     * @param $data
     * @return Profile|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function create($data)
    {
        DB::beginTransaction();

        try {

            //check if the user exists
            $hasUser = User::where('email', $data['email'])->exists();
            if ($hasUser) {
                abort(
                    Response::HTTP_CONFLICT,
                    'The email is already registered'
                );
            }

            // create user
            $user = new User();
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();

            // attach categories suggestions
            $this->attachCategoriesSuggestions($user);

            // create profile
            $profile = $user->profile()->create($data);

            $profile->refresh();

            DB::commit();

            return $profile;

        } catch (Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Aggregates categories suggestions for the user
     *
     * @param User $user
     */
    private function attachCategoriesSuggestions(User $user) {

        $arrCategoriesId = Category::where('is_suggestion', true)->pluck('id');
        $user->categories()->attach($arrCategoriesId);
    }

    /**
     * Update user's profile
     *
     * @param $data
     * @return Profile
     * @throws \Throwable
     */
    public function update($data)
    {
        DB::beginTransaction();

        try {

            $profile = $this->get();
            $profile->fill($data);
            $profile->save();

            DB::commit();

            return $profile;

        } catch (Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Delete user and profile
     *
     * @throws \Throwable
     */
    public function delete()
    {
        DB::beginTransaction();

        try {

            $this->revokeAll();

            $user = Auth::user();
            $user->profile()->delete();
            $user->delete();

            DB::commit();

        } catch (Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Generate a PIN to the user
     *
     * @param User $user
     * @return string
     */
    public function generatePIN(User $user)
    {
        $pin = '';
        for ($i = 0; $i < 6; $i++) {
            $pin .= mt_rand(0, 9);
        };
        $user->pin = Hash::make($pin);
        $user->pinned_at = date('c');
        $user->save();

        return $pin;
    }

    /**
     * Check whether a given PIN exists or is expired
     *
     * @param $email
     * @param $pin
     */
    public function checkPIN($email, $pin)
    {
        // get the user by email
        $user = User::where('email', $email)->first();

        // check if the PIN match
        if (!$user || !$user->pin || !Hash::check($pin, $user->pin)) {
            abort(
                Response::HTTP_UNAUTHORIZED,
                'The PIN does not match'
            );
        }

        // check if the PIN has been expired
        if(!$user->pinned_at || time() - strtotime($user->pinned_at) > self::PIN_TIMEOUT * 60 ) {
            abort(
                Response::HTTP_REQUEST_TIMEOUT,
                'The PIN has been expired'
            );
        }
    }

    /**
     * Updates the PIN time for the current
     *
     * @param $email
     * @param $pin
     */
    public function refreshPINTime($email, $pin)
    {
        $this->checkPIN($email, $pin);
        User::where('email', $email)->update([
            'pinned_at' => date('c')
        ]);
    }

    /**
     * Send an email to the user with a PIN to reset the password
     *
     * @param $email
     */
    public function sendResetEmail($email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            abort(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'The email provided does not exists'
            );
        }
        $pin = $this->generatePIN($user);
        Mail::to($user->email)->send(new ResetPassword($user, $pin));
    }

    /**
     * Changes the user's password using only the email as reference
     *
     * @param $email
     * @param $password
     */
    public function changePasswordByEmail($email, $password)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            abort(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'The email provided does not exists'
            );
        }
        $user->password = Hash::make($password);
        $user->pin = null;
        $user->pinned_at = null;
        $user->save();
    }

    /**
     * Changes the user's password by checking the original one
     *
     * @param $password
     * @param $newPassword
     */
    public function changePassword($password, $newPassword)
    {
        $user = Auth::user();
        if (!Hash::check($password, $user->password)) {
            abort(
                Response::HTTP_FORBIDDEN,
                'The original password are incorrect'
            );
        }
        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
