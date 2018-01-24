<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use function GuzzleHttp\Psr7\build_query;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User */
    private $user;

    /* @var string */
    private $token;

    /** @var string */
    private $locale;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $token
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;

        $this->locale = $user->locale ?: App::getLocale();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $query = [
            'feature' => 'reset',
            '$publicly_indexable' => 0,
            'action' => json_encode([
                'page' => 'reset',
                'params' => [
                    'email' => $this->user->email,
                    'token' => $this->token,
                ]
            ]),
        ];

        $url = "https://app.link?" . build_query($query);

        if (View::exists("mail.{$this->locale}.reset")) {
            return $this->markdown("mail.{$this->locale}.reset")
                ->with('user', $this->user)
                ->with('token', $this->token)
                ->with('url', $url);
        }

        return $this->markdown("mail.en.reset")
            ->with('user', $this->user)
            ->with('token', $this->token)
            ->with('url', $url);
    }
}