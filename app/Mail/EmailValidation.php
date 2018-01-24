<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use function GuzzleHttp\Psr7\build_query;

class EmailValidation extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User */
    private $user;

    /** @var string */
    private $locale;

    /** @var string */
    private $token;

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
                'page' => 'validate',
                'params' => [
                    'email' => $this->user->email,
                    'token' => $this->token,
                ]
            ]),
        ];

        $url = "https://app.link?" . build_query($query);

        if (View::exists("mail.{$this->locale}.email_validation")) {
            return $this->markdown("mail.{$this->locale}.email_validation")
                ->with('user', $this->user)
                ->with('token', $this->token)
                ->with('url', $url);
        }

        return $this->markdown('mail.en.email_validation')
            ->with('user', $this->user)
            ->with('token', $this->token)
            ->with('url', $url);
    }
}