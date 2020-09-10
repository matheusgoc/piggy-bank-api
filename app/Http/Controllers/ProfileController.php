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
        \Log::info('profile', [$profile]);
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
            'firstname' => 'alpha',
            'lastname' => 'alpha',
        ]);

        $profile = $this->repo->update($request->all());
        return new ProfileResource($profile);
    }

    public function destroy()
    {
        $this->repo->delete();
        return response()->noContent();
    }
}
