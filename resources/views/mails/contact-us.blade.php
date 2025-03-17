@component('mail::message')

# You have new application for the **Contact Us** form


From the user: <b>{{ $userEmail }}</b>

# Message content


{{ $content }}
@endcomponent
