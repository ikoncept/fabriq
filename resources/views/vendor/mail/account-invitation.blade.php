@component('mail::message')

# Hej {{ $invitation->user->first_name }}!

Du har blivit inbjuden till Fabriq CMS av {{ $invitation->invitedBy->first_name }}. Klicka på knappen nedan för att acceptera inbjudan och skapa ett konto!

@component('mail::button', ['url' => $inviteUrl])
Acceptera inbjudan
@endcomponent


<small>Länken för inbjudan är endast giltig i 48 timmar, därefter behöver administratören skicka en ny inbjudan.</small>

@endcomponent
