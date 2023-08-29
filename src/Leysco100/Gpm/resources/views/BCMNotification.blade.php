@component('mail::message')
## Details
@component('mail::table')
| Title | Figures |
| :--- | ----: |
| Total Documents| {{$summaryReport['totalDocs']}} |
| Total Released Documents | {{$summaryReport['totalReleased']}} |
| Total Not Released | {{$summaryReport['totalNotReleased']}} |
| Total Flagged Scans |{{$summaryReport['totalFlagged']}} |
| Total Synced| {{$summaryReport['totalSynced']}} |
| Total pending Sync| {{$summaryReport['totalNotSynced']}} |

@endcomponent


### Scan Result Description:
{{ $error}}

Thanks,


{{ config('app.name') }}
@endcomponent