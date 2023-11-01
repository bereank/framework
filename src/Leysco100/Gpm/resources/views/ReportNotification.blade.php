@component('mail::message')


## Documents Summary
@component('mail::table')
| Title | Figures |
| :--- | ----: |
| **Total Sync Documents** | {{$summaryReport['totalSync']}} |
| **Total Open Documents** | {{$summaryReport['totalOpen']}} |
| **Total Released Documents** | {{$summaryReport['totalReleased']}} |
@endcomponent

### Documents Summary Summary for : **{{$date}}**

@component('mail::table')
| Title | Figures |
| :--- | ----: |
| Total Sync Documents | {{$summaryReport['totalSyncY']}} |
| Total Open Documents | {{$summaryReport['totalOpenY']}} |
| Total Released Documents | {{$summaryReport['totalReleasedY']}} |
| Total Closed By Target Documents | {{$summaryReport['totalReleasedbyTargetY']}} |
| Total Cancelled Documents | {{$summaryReport['totalCancelledDocs']}} |
@endcomponent


### Scan Logs Summary for : **{{$date}}**

@component('mail::table')
| Title | Figures |
| :--- | ----: |
| Total Scans | {{$summaryReport['totalYesterdayScans']}} |
| Total Successfull Released Documents| {{$summaryReport['totalSuccessfulReleased']}}
| Total Successfull Not Yet Released Documents | {{$summaryReport['totalSuccessfulNotReleased']}} |
| Total Does Not Exist Scans | {{$summaryReport['totalFaildDoesNotExistY']}} |
| Total Duplicate Scans | {{$summaryReport['totalFailedDuplicateY']}} |
| Total Cancelled Scans | {{$summaryReport['totalCancelledLogs']}} |
| Total Flagged Scans |{{$summaryReport['totalFlagged']}} |
@endcomponent

Thanks,

{{ config('app.name') }}
@endcomponent