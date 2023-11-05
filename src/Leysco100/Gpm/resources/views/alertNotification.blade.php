<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        color: black;
        /* font-weight: ; */
        margin: 3%;
    }
    
    #template-title {
        font-size: 20px; 
        text-decoration: underline; 
        text-align: center;
        margin: 0; 
    }
    
    .template-body {
        margin: 3px;
        padding: 2rem;
        font-size: 14px;
        background-color: rgb(247, 250, 241);
    }
    
    p {
        font-size: 12px;
        margin-top: 20px; 
        text-align: center; 
    }
    table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #dddddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 500;
        }
</style>
</head>
<body>
    <h4 id="template-title">{{$template->tempTitle}}</h4>

    <div class="template-body">
        {!! $tempBody !!}
    </div>
    
    {{-- <p>Thank you for using our service!</p> --}}
</body>
</html>
