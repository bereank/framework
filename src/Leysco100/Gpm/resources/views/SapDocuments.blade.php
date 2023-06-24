@component('mail::message')


{{--    ## Documents Summary--}}
{{--    @component('mail::table')--}}
{{--        | Title | Figures |--}}
{{--        | :--- | ----: |--}}
{{--        | **Total Sync Documents** | {{$summaryReport['totalSync']}} |--}}
{{--        | **Total Open Documents** | {{$summaryReport['totalOpen']}} |--}}
{{--        | **Total Released Documents** | {{$summaryReport['totalReleased']}} |--}}
{{--    @endcomponent--}}

{{--    ### Documents Summary Summary for : **{{$date}}**--}}

{{--    @component('mail::table')--}}
{{--        | Title | Figures |--}}
{{--        | :--- | ----: |--}}
{{--        | Total Sync Documents | {{$summaryReport['totalSyncY']}} |--}}
{{--        | Total Open Documents | {{$summaryReport['totalOpenY']}} |--}}
{{--        | Total Released Documents | {{$summaryReport['totalReleasedY']}} |--}}
{{--    @endcomponent--}}


{{--    ### Scan Logs Summary for : **{{$date}}**--}}
    ### Scan Synced Documents

{{--    @component('mail::table')--}}
{{--        | Title | Figures |--}}
{{--        | :--- | ----: |--}}
{{--        | Total Scans | {{$summaryReport['totalYesterdayScans']}} |--}}
{{--        | Total Successfull | {{$summaryReport['totalSuccessful']}} |--}}
{{--        | Total Does Not Exist Scans | {{$summaryReport['totalFaildDoesNotExistY']}} |--}}
{{--        | Total Duplicate Scans | {{$summaryReport['totalFailedDuplicateY']}} |--}}
{{--        |Total Flagged Scans |{{$summaryReport['totalFlagged']}} |--}}
{{--    @endcomponent--}}



    Thanks,

    {{ config('app.name') }}
@endcomponent