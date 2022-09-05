<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
   <style>
    @page { margin: 100px 25px; }
    #watermark {
        position: fixed;
        top:10px;
        bottom: 0px;
        right: 10px;

        /* opacity: .1; */
        width:    20cm;
        height:   28cm;

        /** Your watermark should be behind every content**/
        z-index:  -1000;
    }
    .title {
        width: 100%;
        text-align:center;
    }
    .table-border {
        page-break-after:avoid;
        border: 1px solid black;
        border-collapse: collapse;
        width:100%;
        text-align:center;
    }
    .table-border, .table-border-tr, .table-border-td {
        border: 1px solid black;
        padding: 10px;
    }
    .mainContent {
        margin-right: 25px;
        margin-left: 25px;
        font-size: 12px;
        page-break-after: avoid;
        text-align: justify;
        page-break-after:avoid;
    }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.header')
    @include('pdf.footer')
    <div class="mainContent">
        <div class="title">
            <h3><u>CASE CLOSURE</u></h3>
        </div>

        <div>
            <table style="font-weight:bold;">
                <tr >
                    <td>DATE</td>
                    <td> : </td>
                    <td colspan="2">{{ date_format(date_create($incident_report['date_da']), "F d, Y") }}</td>
                </div>
                <tr>
                    <td><strong>EMPLOYEE NAME</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }}</td>
                </tr>
                <tr>
                    <td>EMPLOYEE NUMBER</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['employee_no'] }}</td>
                </tr>
                <tr>
                    <td>POSITION TITLE</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['designation']['name'] }}</td>
                </tr>
                <tr>
                    <td>NAME OF DEPT.</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['department']['name'] }}</td>
                </tr>
                <tr>
                <td>CC</td>
                <td> : </td>
                    <td colspan="2">Department Head, HR, DOLE, 201 File</td>
                </tr>
            </table>
            <br>

            <div>
                Dear Mr./Ms. {{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }},
            </div>
            <br>

            <div>
                This is in relation to the Notice to Explain memo dated
                    <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                wherein you allegedly committed an infraction under the provision of
                @if ($offense_multiple)
                    @foreach ($offense_multiple as $off_multi)
                        <span style="margin-right:5px;"><b>{{ $off_multi['category']['name'] }}</b>,</span>
                    @endforeach
                @elseif ($complainant['offense'])
                    {{ $complainant['offense']['category']['name'] }}.
                @elseif ($complainant['attendancepointssystem'])
                    Attendance.
                @endif
                dated <b>{{ date_format(date_create($incident_report['created_at']), "F d, Y")  }}</b>
            </div>
            <br>

            @if ($incident_report['additional_notes'])
            <div>
                Additional Notes: {!! $incident_report['additional_notes'] !!}
            </div>
            <br>
            @endif

            <div>
                After careful and exhaustive deliberation, it has been found that there is no substantial evidence to
                prove that you deliberately commit the alleged infraction.
            </div>
            <br>

            <div>
                All other causes of action and claims are hereby DISMISSED for lack of merit and/or failure to
                substantiate. While we understand your explanation and the situation, any valid occurrence in the future
                of the same or similar offense will merit behavioral action up to and including termination of your
                employment.
            </div>
            <br>

            <div>
                By signing your name below, you are acknowledging the contents of this letter.
            </div>
            <br>

            <div>
                Furthermore, all other circumstances involving the 
                charge against you have been considered;
                and the grounds have been established to 
                justify the severance of your employment.
            </div>
            <br>

            <div>
                Please be aware that a copy of this memo will be filed on your 201 for future reference. Should you have
                clarifications, you may contact the HR department.
            </div>
            <br>

            <div>
                @include('pdf.signatory')
            </div>
        </div>
    </div>
</body>
</html>
