@component('mail::message')
# Geachte {{ $user->name }},

Er is een account aangemaakt in de Dermatude App.

Om uw account te activeren volgt u de volgende stappen:
1. Open de applicatie
2. Navigeer naar de validatie pagina
3. Voer uw e-mail adres in
4. Voer de volgende validatiecode in: {{ $token }}

@component('mail::button', ['url' => $url])
Validatiepagina open
@endcomponent

Indien u geen account heeft aangevraagd dan kunt u deze e-mail negeren.

Met vriendelijke groet,<br>
Dermatude
@endcomponent
