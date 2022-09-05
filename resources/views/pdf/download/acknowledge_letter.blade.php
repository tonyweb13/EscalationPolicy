<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style>
    @page { margin: 100px 25px; }
    .title {
        width: 100%;
        text-align:center;
    }
    .content {
        width: 100%;
        z-index: 100;
    }
    hr.divider {
        border: 1px solid black;
        margin-bottom: 20px;
    }
    .table-border {
        border: 1px solid black;
        border-collapse: collapse;
        width:100%
    }
    .table-border, .table-border-tr, .table-border-td {
        border: 1px solid black;
    }
    .plain {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        page-break-after:avoid;
    }
    #watermark {
        position: fixed;
        bottom: 0px;
        right: 0px;

        /* opacity: .1; */
        width:    21.8cm;
        height:   28cm;

        /** Your watermark should be behind every content**/
        z-index:  -1000;
    }
    .pull-left {
        float: left!important;
    }
    .pull-right {
        float: right!important;
    }
    .mainContent {
        margin-right: 25px;
        margin-left: 25px;
        margin-bottom: -20px;
        font-size: 12px;
        page-break-after: avoid;
    }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.footer')
    <div class="mainContent">
        @include('pdf.header')

        <div class="title">
                <h3><u>ACKNOWLEDGEMENT LETTER</u></h3>
        </div>
        <div class="content">
            <div class="plain">
                {{ date('l F d,Y') }}
            </div>

            <div class="plain">
                Dear {{ $res_full_name }},
            </div>

            <div class="plain">
                This is to acknowledge that we formally accept your resignation letter dated {{ date("F d, Y", strtotime($start_date)) }} which
                takes effect on {{ date("F d, Y", strtotime($takes_date)) }} . Your last day with the company will be on {{ date("F d, Y", strtotime($last_date)) }}.
            </div>
            <br>
            <div class="plain">
                As part of the Clearance process, we wish to remind you of the following:
            </div>

            <div class="plain" style="margin-left:30px">
                1. 	All company assets, documents and property under your accountability and custody such as Locker Keys
                    or any other assigned assets<br><label style="margin-left:12px">under your name should be surrendered.</label><br>
                2.	Secure a printed copy of your Clearance from the HR Department which will be routed by HR
                    department to various domains.<br>
                3.	Undergo the Exit Interview to be facilitated by the HR Department<br>
                4.	Your remaining salary shall be withheld and likewise included in your final pay, which will be
                    processed upon the submission of your duly <label style="margin-left:12px">accomplished clearance</label>.<br>
            </div>

            <div class="plain">
                By signing your name below, you are acknowledging the contents of this letter. Should you have
                clarifications, you may contact the HR department.
            </div>

            <div class="plain" style="margin-top:20px;">
                Please note that a copy of this memo will be filed on your 201 for future reference.
            </div>
            <div class="plain" style="margin-top:20px;">
                @include('pdf.signatory')
            </div>
        </div>
    </div>
</body>
</html>
