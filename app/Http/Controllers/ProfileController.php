<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Repositories\ProfileRepository;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @var ProfileRepository
     */
    private $repo;

    public function __construct(ProfileRepository $repo)
    {
        $this->repo = $repo;
    }

    public function auth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device' => 'required',
        ]);
        $auth = $this->repo->authenticate($request->email, $request->password, $request->device);

        return [
            'token' => $auth['token'],
            'profile' => new ProfileResource($auth['profile']),
        ];
    }

    public function revoke()
    {
        $this->repo->revoke();

        return response()->noContent();
    }

    public function revokeAll()
    {
        $this->repo->revokeAll();

        return response()->noContent();
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'firstname' => 'required|alpha',
            'lastname' => 'required|alpha',
        ]);

        $profile = $this->repo->create($request->all());

        return new ProfileResource($profile);
    }

    public function show()
    {
        $profile = $this->repo->get();

        return new ProfileResource($profile);
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstname' => 'required|alpha',
            'lastname' => 'required|alpha',
        ]);

        $profile = $this->repo->update($request->all());

        return new ProfileResource($profile);
    }

    public function destroy()
    {
        $this->repo->delete();

        return response()->noContent();
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->repo->sendResetEmail($request->get('email'));

        return response()->noContent();
    }

    public function refreshPINTime(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin' => 'required',
        ]);

        $this->repo->refreshPINTime($request->get('email'), $request->get('pin'));

        return response()->noContent();
    }

    public function changePasswordWithPIN(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin' => 'required',
            'password' => 'required',
        ]);

        $email = $request->get('email');
        $pin = $request->get('pin');
        $password = $request->get('password');

        $this->repo->checkPIN($email, $pin);
        $this->repo->changePasswordByEmail($email, $password);

        return response()->noContent();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'new' => 'required',
        ]);

        $this->repo->changePassword($request->get('password'), $request->get('new'));

        return response()->noContent();
    }
}
