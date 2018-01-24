@component('mail::message')
# Geachte {{ $user->name }},

Er is een wachtwoord herstel aangevraagd voor uw account.

Om uw wachtwoord the herstellen volgt u de volgende stappen:
1. Open de applicatie
2. Navigeer naar de wachtwoord herstel pagina
3. Voer uw e-mail adres in
4. Voer de volgende herstelcode in: {{ $token }}
5. Voer uw (nieuwe) wachtwoord in

De herstelcode blijft één uur na aanvraag geldig, indien meer dan één uur is verstreken na de aanvraag dan dient u
een nieuwe herstelcode aan te vragen.

@component('mail::button', ['url' => $url])
Wachtwoord herstel openen
@endcomponent

Indien u geen wachtwoord herstel heeft aangevraagd dan kunt u deze e-mail negeren.

Met vriendelijke groet,<br>
Dermatude
@endcomponent
