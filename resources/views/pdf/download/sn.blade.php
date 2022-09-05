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
                <h3><u>SEPARATION NOTICE</u></h3>
        </div>

        <div class="content">
            <table style="font-weight:bold; text-align:left; margin-top:20px;">
                <tr >
                    <td>TO</td>
                    <td> : </td>
                    <td colspan="2">{{ $res_fullname }}</td>
                </tr>
                <tr>
                    <td><strong>ADDRESS</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $res_address }}</td>
                </tr>
                <tr>
                    <td>DATE</td>
                    <td> : </td>
                    <td colspan="2">{{ date("F d, Y") }}</td>
                </tr>
                <tr>
                    <td>FROM</td>
                    <td> : </td>
                    <td colspan="2">Human Resource Department</td>
                </tr>
                <tr>
                    <td>SUBJECT</td>
                    <td> : </td>
                    <td colspan="2">SEPARATION NOTICE</td>
                </tr>
            </table>

            <div style="margin-top:20px;"><b>Dear {{ $res_fullname }},</b></div>

            <div class="plain">
                This is to formally inform you that your employment has been terminated effective
                {{ date("F d, Y", strtotime($sn_termination_date)) }}. This is due to your unauthorized absences since <b>Date of absences</b>
                without proper notification and is construed as abandonment of work.
            </div>

            <div class="plain">
                We have sent you an RTWO (Return to Work Order) to your home address on file last
                {{ date("F d, Y", strtotime($sn_date_absence_end)) }} requesting you to report
                to work on or before {{ date("F d, Y", strtotime($sn_date_absence_start)) }}.
            </div>

            <div class="plain">
                Under our implementing Rules on Attendance, you action is a clear violation of our company policy
                stated below.
            </div>

            @if($hr_attachments)
                <img src="storage/app/public/attachments/{{ $hr_attachments }}" >
            @endif

            <div class="plain">
                <b><i>
                Please note that under Article 297(b) of the Philippine Labor Code, AWOL is considered as Gross
                and Habitual neglect by the employee of his/her duties.
                </i></b>
            </div>

            <div class="plain">
                You are hereby advised to surrender all company issued collaterals/materials to include Company ID,
                training manual(s) and headset. After which, you are required to go through the normal Exit Clearance
                Process and Exit Interview which is necessary for the subsequent initiation of your Full and Final
                Pay computation.
            </div>

            <div class="plain">
                Kindly confirm receipt of this mail. Should you have any questions, please do not hesitate to contact
                us through phone 8050054.
            </div>

            <div class="plain" style="margin-top:20px;">
                Thank You,
            </div>

            <table style="width:100%; text-align:left; font-weight:bold; margin-top:20px;">
                <tr>
                    <td>Prepared by :</td>
                    <td>Approved by :</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>__________________________________</td>
                    <td>__________________________________</td>
                </tr>
                <tr>
                    <td>{{ $hrbp_name }}</td>
                    <td>{{ $hrm_name }}</td>
                </tr>
                <tr>
                    <td>HR Business Partner</td>
                    <td>Senior Manager Human Capital</td>
                </tr>
                <tr>
                    <td>Emp No: {{ $hrbp_emp_no }}</td>
                    <td>Emp No: {{ $hrm_emp_no }}</td>
                </tr>
                <tr>
                    <td>Date Signed:_______________________</td>
                    <td>Date Signed:_______________________</td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
