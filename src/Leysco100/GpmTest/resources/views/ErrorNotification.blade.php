@component('mail::message')
## Document Details
Document Type: {{ $documentData['objecttype']['DocumentName'] }}

Scanned By: {{ $documentData['creator']['name'] }}

Doc Num#: {{ $documentData['DocNum'] }}

Scan Date#: {{ $documentData['created_at'] }}

### Scan Result Description:
{{ $error}}

Thanks,

{{ config('app.name') }}
@endcomponent