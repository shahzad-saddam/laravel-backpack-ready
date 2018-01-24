@component('mail::message')
# Dear {{ $user->name }},

A new account has been registered with the [app name] App.

To activate your account follow these steps:<br/>
1. Open the app
2. Go to the validation form
3. Enter your e-mail address
4. Enter the following validation token: {{ $token }}

@component('mail::button', ['url' => $url])
Open validation form
@endcomponent

If you did not request an account no further action is required.

Yours sincerely,<br>
Dermatude
@endcomponent
