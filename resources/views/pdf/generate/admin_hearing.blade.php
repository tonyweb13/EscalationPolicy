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
    .plain {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        page-break-after:avoid;
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
    #watermark {
        position: fixed;
        bottom: 0px;
        right: 0px;
        /** The width and height may change
            according to the dimensions of your letterhead
        **/
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
        <div class="title" style="margin-bottom: 20px;">
            <h3>INVITATION TO AN ADMINISTRATIVE HEARING</h3>
        </div>
        <div class="content">
            <table style="font-weight:bold; text-align:left; margin-top:20px;">
                <tr >
                    <td>Date</td>
                    <td> : </td>
                    <td colspan="2">{{ date("F d, Y") }}</td>
                </div>
                <tr>
                    <td>Employee Name</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }}</td>
                </tr>
                <tr>
                    <td>Employee ID</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['employee_no'] }}</td>
                </tr>
                <tr>
                    <td>Position Title</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['designation']['name'] }}</td>
                </tr>
                <tr>
                    <td>Department</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['department']['name'] }}</td>
                </tr>
            </table>
            <table style="text-align:left; margin-top:20px;" >
                <tr>
                    <td >
                        Dear {{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }},
                    </td>
                </tr>
            </table>
            <table style="text-align:left; margin-top:10px;" >
                <tr >
                    <td >
                        Please be advised that there will be an Administrative Hearing on
                        {{ $incident_report['date_admin_hearing'] }} from
                        {{ $incident_report['time_admin_hearing'] }}
                        at the {{ $incident_report['meeting_place'] }}.
                    </td>
                </tr>
            </table>
            <table style="text-align:left; margin-top:10px;" >
                <tr>
                    <td >
                        This Administrative Hearing is regarding an investigation being conducted in view of your
                        alleged violation of the provisions of the Code of Conduct specifically on
                            <?php if ($complainant['offense']['offense'] != null): ?>
                                {{ $complainant['offense']['offense'] }}
                            <?php endif; ?>
                            <?php if ($complainant['attendancepointssystem'] != null): ?>
                                {{ $respondent_user['complainant']['attendancepointssystem']['type_infraction'] }}
                            <?php endif; ?>
                        cited in the Notice to Explain memo which you have signed and acknowledged
                    </td>
                </tr>
            </table>
        </table>
        <table style="text-align:left; margin-top:10px;" >
            <tr>
                <td >
                    This will to give you a chance to confront the allegations against you as well as to give you an
                    opportunity to present evidence on your behalf. You will be allowed to bring in witness/es or
                    counsel should you wish to do so. Your presence at the said hearing is crucial
                    to the final determination of this case, and your cooperation is earnestly requested.
                </td>
            </tr>
        </table>
        <table style="text-align:left; margin-top:10px;" >
            <tr>
                <td >
                    Should you fail to appear on the date, time & venue as stipulated above, the Company shall construe
                    the same as a waiver of your right to be heard and the proceedings of the administrative hearing
                    will be held ex-parte without further reference to you. Thereafter, a decision will be reached
                    based on the existing facts and evidence on record
                </td>
            </tr>
        </table>
        <div class="plain" style="margin-top:20px;">
            @include('pdf.signatory')
        </div>
    </div>
</body>
</html>
