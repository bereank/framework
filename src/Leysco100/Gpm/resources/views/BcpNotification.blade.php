@component('mail::message')

{{ $message }}

Date: {{ $date }}

Thanks,

{{ config('app.name') }}
@endcomponent
