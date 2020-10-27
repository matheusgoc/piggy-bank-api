<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $name;
    public $pin;
    public $logo;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $pin
     * @return void
     */
    public function __construct(User $user, $pin)
    {
        $this->name = $user->profile->firstname . ' ' .$user->profile->lastname;
        $this->pin = $pin;
        $this->logo = Storage::disk('s3')->url('/images/logotype.png');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.profile.reset-password');
    }
}
