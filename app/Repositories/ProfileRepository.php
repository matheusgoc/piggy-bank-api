<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * ProfileRepository
 * @package App\Repositories
 */
class ProfileRepository
{
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

        } catch (\Exception $ex) {

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

        } catch (\Exception $ex) {

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

        } catch (\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }
}
