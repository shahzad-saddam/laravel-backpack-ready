@component('mail::message')
# Dear {{ $user->name }},

A password reset has been requested for your account.

To reset your password follow these steps:<br/>
1. Open the app
2. Go to the reset password form
3. Enter your e-mail address
4. Enter the following reset token: {{ $token }}
5. Enter your (new) password

The reset token will remain valid for one hour after it has been requested, if more than one hour passes you must
request a new reset token.

@component('mail::button', ['url' => $url])
Open reset form
@endcomponent

If you did not request a password reset no further action is required.

Yours sincerely,<br>
[app name]
@endcomponent
