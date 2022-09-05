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
                <h3><u>HR INTERVENTION DOCUMENTATION</u></h3>
        </div>
        <div class="content">
            <table style="font-weight:bold; text-align:left; margin-top:20px;">
                <tr>
                    <td><strong>DATE</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ date("F d, Y") }}</td>
                </tr>
                <tr >
                    <td><strong>Employee Name</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $res_full_name }}</td>
                </tr>
                <tr>
                    <td><strong>ID Number</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $res_emp_no }}</td>
                </tr>

                <tr>
                    <td><strong>Position Title</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $res_position }}</td>
                </tr>
                <tr>
                    <td><strong>Name of Dept</strong></td>
                    <td> : </td>
                    <td colspan="2">{{ $res_department }}</td>
                </tr>
                <tr>
                    <td>CC</td>
                    <td> : </td>
                    <td colspan="2">Department Head, HR, DOLE, 201 File</td>
                </tr>
            </table>

            <div class="plain">
                This is to document the discussion between Mr./Ms. {{$res_full_name}} and Mr./Ms.
                {{ $supervisor_name }} & {{ $hrm_name }} which transpired on
                {{ date("F d, Y", strtotime($date_transpired)) }}. This is with regard to
                {{$offense}}.
            </div>

            <div class="plain">
                It was agreed that {{$agreement}}. 
            </div>
            <br><br><br>
            <div class="plain">
                By signing your name below, you are acknowledging the contents of this letter.
            </div>

            <div class="plain">
                Please be aware that a copy of this memo will be filed on your 201 for future reference. Should you have
                 clarifications, you may contact the HR department.
            </div>

            <div class="plain" style="margin-top:20px;">
                Thank You,
            </div>

            <table style="width:100%; text-align:left; font-weight:bold;">
                <tr>
                    <td>Received by :</td>
                    <td>Received by:</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>_________________________________</td>
                    <td>_________________________________</td>
                </tr>
                <tr>
                    <td>{{$res_full_name}}</td>
                    <td>{{$supervisor_name}}</td>
                </tr>
                <tr>
                    <td>{{$res_position}}</td>
                    <td>{{$supervisor_position}}</td>
                </tr>
                <tr>
                    <td>Emp No: {{$res_emp_no}}</td>
                    <td>Emp No: {{$supervisor_emp_id}}</td>
                </tr>
                <tr>
                    <td>Date Signed:_______________________</td>
                    <td>Date Signed:_______________________</td>
                </tr>
            </table>
            <br><br>
            <table style="width:100%; text-align:left; font-weight:bold;">
                <tr>
                    <td>Noted by :</td>
                    <td>Prepared by:</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>_________________________________</td>
                    <td>_________________________________</td>
                </tr>
                <tr>
                    <td>{{$hrm_name}}</td>
                    <td>{{$hrbp_full_name}}</td>
                </tr>
                <tr>
                    <td>Senior Manager Human Capital</td>
                    <td>{{$hrbp_position}}</td>
                </tr>
                <tr>
                    <td>Emp No: {{$hrm_emp_no}}</td>
                    <td>Emp No: {{$hrbp_emp_no}}</td>
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
