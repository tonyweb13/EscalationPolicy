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
            <h3><u>REPORT TO WORK ORDER / NOTICE OF EXPLANATION</u></h3>
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
                    <td colspan="2">Return To Work</td>
                </tr>
            </table>

            <div style="margin-top:20px;"><b>Dear {{ $res_fullname }},</b></div>

            <div class="plain">
                It has come to our attention that you have not reported to work from 
                {{ date("F d, Y", strtotime($rtw_date)) }} without proper
                documentation.  See below extracted attendance from the Timekeeping tool.
            </div>

            <div class="plain">
                This letter serves as a report to work order letter and a Notice of explain. You are hereby advised
                to return to work not later than {{ date("F d, Y", strtotime($rtw_advice_date)) }} and provide your explanation for each day of NCNS/Unauthorized
                Absences, please attach supporting documents to substantiate your statement. Your failure to comply
                shall be construed as abandonment of work which and shall be dealt with accordingly based on the
                provisions of the Code of Conduct to with: 
            </div>

            @if($hr_attachments)
                @if( file_exists( "storage/app/public/attachments/{{ $hr_attachments }}"  ) )
                    <img src="storage/app/public/attachments/{{ $hr_attachments }}" >
                @endif
            @endif

            <div class="plain">
                <b><i>
                Please note that under Article 297(b) of the Philippine Labor Code, AWOL is considered as Gross
            and Habitual neglect by the employee of his/her es
                </b></i>
            </div>

            <div class="plain">
                Kindly confirm receipt of this mail. Should you have any questions, please do not hesitate
                to contact us through phone 8050054.
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
