<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style>
        #watermark {
            position: fixed;
            bottom: 0px;
            right: 0px;

            /** The width and height may change
                according to the dimensions of your letterhead
            **/
            width:    20cm;
            height:   25cm;

            /** Your watermark should be behind every content**/
            z-index:  -1010;
        }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>

    @php


    echo $sender_id;


    @endphp

</body>
</html>
