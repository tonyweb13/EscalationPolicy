<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style>
    @page { margin: 100px 25px; }
    .pull-left {
        float: left !important;
        width: 50%;
    }
    .pull-right {
        float: right !important;
        width: 50%;
    }
    .center-signature {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        text-align: center;
        page-break-after:avoid;
    }
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
        page-break-after:avoid;
        border: 1px solid black;
        border-collapse: collapse;
        width:100%
    }
    table {
        border-collapse: collapse;
        width: 690px;
    }

    table, td, th {
        border: 1px solid black;
    }

    th, td {
        text-align: center;
        font-size: 12px
    }

    th {
    	background-color: #1F4788;
    	column-width: 150px;
    	color: white;
    	font-weight: normal;
    }

    td {
        /* These are technically the same, but use both */
        overflow-wrap: break-word;
        word-wrap: break-word;

        -ms-word-break: break-all;
        /* This is the dangerous one in WebKit, as it breaks things wherever */
        word-break: break-all;
        /* Instead use this non-standard one: */
        word-break: break-word;

        /* Adds a hyphen where the word breaks, if supported (No Blink) */
        -ms-hyphens: auto;
        -moz-hyphens: auto;
        -webkit-hyphens: auto;
        hyphens: auto;
    }

    .shaded {
    	background-color: #BDC3C7;
    }

    .no-bottom-border {
        border-bottom: none;
    }

    .no-top-border {
        border-top: none;
    }

    .no-left-border {
        border-left: none;
    }

    .no-right-border {
        border-right: none;
    }
    .left, .right {
        position: absolute;
        width: 30px;
        height: 20px;
        top: 0;
        bottom: 0;
        margin: auto;
    }
    .left {
        left: 0;
        margin-left: -15px;
    }
    .right {
        right: 0;
        margin-right: -15px;
    }
    .text-signed {
        font-size: 15px;
    }
    .text-center {
        margin-left: 50px;
        margin-top: 15px;
    }
    .text-center-new {
        margin-top: 0px;
    }
    .text-center-logo {
        margin-left: 300px;
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

    .offenseTable {
        padding: 10px;
    }
    .mainContent {
        margin-right: 25px;
        margin-left: 25px;
        margin-bottom: -20px;
        font-size: 12px;
        page-break-after: avoid;
    }

    .custom-table {
        display: table;
        position: absolute;
        width: 100%;
    }
    .table-row {
        display: table-row;
    }
    .table-cell {
        display: table-cell;
        height: 30px;
        border: 1px solid black;
        border-top: 0px !important;
    }
    .table-cell-first {
        display: table-cell;
        height: 20px;
        border-bottom: 1px solid black;
        border-right: 1px solid black;
    }
    .table-cell-last {
        display: table-cell;
        height: 20px;
        border-bottom: 1px solid black;
        border-left: 1px solid black;
    }
    .table-cell-max {
        display: table-cell;
        border-right: 1px solid black;
        border-left: 1px solid black;
        border-bottom: 0px solid black;
        height: 60px;
    }
    .table-cell-max-last {
        display: table-cell;
        border-bottom: 0px solid black;
        border-left: 1px solid black;
        height: 60px;
    }
    .table-cell-max-first {
        display: table-cell;
        border-right: 1px solid black;
        border-bottom: 0px solid black;
        height: 60px;
    }

    .imgdiv {
        position: absolute;
    }
    .clipzone2 {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .clipzone2 img {
        width: 100%;
        margin: -650px 0 0 0;
    }
    .clipzone
    {
        position:relative;
        height:650px;
        overflow:hidden;
    }
    .labelTitle {
        text-align:center; 
        margin-bottom: -12px;
    }
    .bodyContent {
        padding-top: 8px; 
        padding-bottom: 8px;
    }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.header')
    @include('pdf.footer')
    <div style="margin-right: 25px; margin-left: 25px; margin-bottom: -20px; font-size: 12px;">
        <div class="content">
            <div class="title">
                <h1>Coaching Log 
                @if ($form_type == 2)
                    - Admin Department
                @elseif ($form_type == 9)
                    - Recruitment Department
                @elseif ($form_type == 15)
                    - Training Department
                @endif
                </h1>
            </div>
            <div class="plain">
                <table style="width: 690px;">
                    <tr>
                        <td style="width:15%;">Name</tD>
                        <td>{{$name}}</td>
                        <td  style="width:15%;">Date of Coaching</td>
                        <td>{{$date}}</td>
                    </tr>
                    <tr>
                        <td>Position</td>
                        <td>{{$position}}</td>
                        <td>Employee ID</td>
                        <td>{{$employee_no}}</td>
                    </tr>
                    <tr>
                        <td>Department</td>
                        <td>{{$department}}</td>
                        <td>Supervisor</td>
                        <td>{{$supervisor}}</td>
                    </tr>
                </table>
            </div>
            <!-- Start Multiple Offense -->
            <br />
            <div class="plain">
                @if ($offense)
                    <table class="table-border offenseTable " style="width: 690px !important;" >
                        <tr class="table-border-tr offenseTable header" >
                            <td class="table-border-td offenseTable" >
                                <b>Classification of Coaching</b>
                            </td>
                            <td class="table-border-td offenseTable" >
                                <b>Description</b>
                            </td>
                        </tr>
                        @foreach ($offense as $off_multi)
                        <tr class="table-border-tr offenseTable" >
                            <td class="table-border-td offenseTable" >
                                {{ $off_multi['category'] }}
                            </td>
                            <td class="table-border-td offenseTable" style="font-size:10px">
                                {{ $off_multi['offense'] }}
                            </td>
                        </tr>
                        @endforeach
                    </table>
                @elseif ($others)
                    <table class="table-border offenseTable " style="width: 690px !important; ">
                        <tr class="table-border-tr offenseTable header" >
                            <td class="table-border-td offenseTable" >
                                <b>Classification of Coaching</b>
                            </td>
                            <td class="table-border-td offenseTable" >
                                <b>Description</b>
                            </td>
                        </tr>
                        <tr class="table-border-tr offenseTable" >
                            <td class="table-border-td offenseTable" >
                                Others
                            </td>
                            <td class="table-border-td offenseTable" style="font-size:10px">
                                {{ $others }}
                            </td>
                        </tr>
                    </table>
                @elseif ($performance_review)
                    <table class="table-border offenseTable " style="width: 690px !important; ">
                        <tr class="table-border-tr offenseTable header" >
                            <td class="table-border-td offenseTable" >
                                <b>Classification of Coaching</b>
                            </td>
                            <td class="table-border-td offenseTable" >
                                <b>Description</b>
                            </td>
                        </tr>
                        <tr class="table-border-tr offenseTable" >
                            <td class="table-border-td offenseTable" >
                                Performance Review
                            </td>
                            <td class="table-border-td offenseTable" style="font-size:10px">
                                {{ $performance_review }}
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
            <!-- End Single Offense -->
            <div class="plain">
                <div class="labelTitle">
                    Findings / Coaching Oppurtunities / Areas of Improvement
                </div><br>
                <table>
                    <tr>
                        <td class="bodyContent">{{$findings}}</td>
                    </tr>
                </table>
            </div>
            @if ($form_type != 15)
            <div>
                <div class="plain" >
                    <div class="labelTitle">Point of Discussion/ Coaching Details</div><br>
                    <table>
                        <tr>
                            <td class="bodyContent">{{$point_of_disscussion}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        <div>
            <div class="plain" >
                <div class="labelTitle">Action Plans</div><br>
                <table>
                    <tr>
                        <td class="bodyContent">{{$action_plans}}</td>
                    </tr>
                </table>
            </div>
            </div>
            @if ($form_type != 15)
            <div>
                <div class="plain" style="margin-top: 10px;">
                    <div class="labelTitle">Employee's Commitment</div><br>
                    <table>
                        <tr>
                            <td class="bodyContent">{{$agents_commitment}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div>
                <div class="plain" style="margin-top: 10px;">
                    <div class="labelTitle">Supervisor’s Commitment</div><br>
                    <table>
                        <tr>
                            <td  class="bodyContent">{{$supervisors_commitment}}</td>
                        </tr>
                    </table>
                </div>
            </div><br /><br />
            @endif

            @if ($kpi != null)
                @if (strtolower($kpi->stage) == 'stage 2'
                    || strtolower($kpi->stage) == 'stage 3'
                    || strtolower($kpi->stage) == 'stage 4'
                    || strtolower($kpi->stage) == 'stage 5'
                    || strtolower($kpi->stage) == 'stage two'
                    || strtolower($kpi->stage) == 'stage three'
                    || strtolower($kpi->stage) == 'stage four'
                    || strtolower($kpi->stage) == 'stage five'
                    || strtolower($kpi->stage) == 'regular')
                    <table style="width: 690px !important;">
                        <tr>
                            <td style="width:35%;">Key Performance Indicator</td>
                            <td >Target</td>
                            <td >Weight</td>
                            <td >Rating</td>
                            <td >Computation (Rating x %)</td>
                            <td >Comments</td>
                        </tr>
                        <tr>
                            <th >Loan Amount</th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_2_fifth_indicator6 }} <br />
                                4 - {{ $stage_2_fourth_indicator6 }} <br />
                                3 - {{ $stage_2_third_indicator6 }} <br />
                                2 - {{ $stage_2_second_indicator6 }} <br />
                                1 - {{ $stage_2_first_indicator6 }}
                            </td>
                            <td >{{ $kpi->loan_amount->target }}</td>
                            <td >50%</td>
                            <td >{{ $kpi->loan_amount->rating }}</td>
                            <td >{{ $kpi->loan_amount->computation }}</td>
                            <td >{{ $kpi->loan_amount->comment }}</td>
                        </tr>
                        <tr>
                            <th >QA Score </th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_2_fifth_indicator7 }} <br />
                                4 - {{ $stage_2_fourth_indicator7 }} <br />
                                3 - {{ $stage_2_third_indicator7 }} <br />
                                2 - {{ $stage_2_second_indicator7 }} <br />
                                1 - {{ $stage_2_first_indicator7 }}
                            </td>
                            <td >{{ $kpi->qa_score->target }}</td>
                            <td >10%</td>
                            <td >{{ $kpi->qa_score->rating }}</td>
                            <td >{{ $kpi->qa_score->computation }}</td>
                            <td >{{ $kpi->qa_score->comment }}</td>
                        </tr>
                        <tr>
                            <th >QA Compliance</th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_2_fifth_indicator8 }} <br />
                                4 - {{ $stage_2_fourth_indicator8 }} <br />
                                3 - {{ $stage_2_third_indicator8 }} <br />
                                2 - {{ $stage_2_second_indicator8 }} <br />
                                1 - {{ $stage_2_first_indicator8 }}
                            </td>
                            <td >{{ $kpi->qa_compliance->target }}</td>
                            <td >10%</td>
                            <td >{{ $kpi->qa_compliance->rating }}</td>
                            <td >{{ $kpi->qa_compliance->computation }}</td>
                            <td >{{ $kpi->qa_compliance->comment }}</td>
                        </tr>
                        <tr>
                            <th >Correction</th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_2_fifth_indicator9 }} <br />
                                4 - {{ $stage_2_fourth_indicator9 }} <br />
                                3 - {{ $stage_2_third_indicator9 }} <br />
                                2 - {{ $stage_2_second_indicator9 }} <br />
                                1 - {{ $stage_2_first_indicator9 }}
                            </td>
                            <td >{{ $kpi->correction->target }}</td>
                            <td >15%</td>
                            <td >{{ $kpi->correction->rating }}</td>
                            <td >{{ $kpi->correction->computation }}</td>
                            <td >{{ $kpi->correction->comment }}</td>
                        </tr>
                        <tr>
                            <th >Attendance Reliability</th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                            <th style="border-right: 0px"><font color="#1F4788"></font></th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_2_fifth_indicator10 }} <br />
                                4 - {{ $stage_2_fourth_indicator10 }} <br />
                                3 - {{ $stage_2_third_indicator10 }} <br />
                                2 - {{ $stage_2_second_indicator10 }} <br />
                                1 - {{ $stage_2_first_indicator10 }}
                            </td>
                            <td >{{ $kpi->attendance_reliability->target }}</td>
                            <td >15%</td>
                            <td >{{ $kpi->attendance_reliability->rating }}</td>
                            <td >{{ $kpi->attendance_reliability->computation }}</td>
                            <td >{{ $kpi->attendance_reliability->comment }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"style="text-align: left! important;">
                            II. DEVELOPMENT PLAN</td>
                        </tr>
                        <tr>
                            <td colspan="2">STRENGTHS</td>
                            <td colspan="4">WEAKNESSES</td>
                        </tr>
                        <tr>
                            @if (strlen($kpi->strengths) != 1)
                                <td colspan="2" style="background-color:yellow">
                                {{ $kpi->strengths }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                            @if (strlen($kpi->weakness) != 1)
                                <td colspan="4" style="background-color:yellow">
                                {{ $kpi->weakness }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="6">ACTION PLAN</td>
                        </tr>
                        <tr>
                            @if (strlen($kpi->action_plans) != 1)
                                <td colspan="6" style="background-color:yellow">
                                {{ $kpi->action_plans }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                        </tr>
                    </table>

                @elseif (strtolower($kpi->stage) == 'stage 1')
                    <table style="width: 690px !important;">
                        <tr>
                            <td style="width:35%;" >Key Performance Indicators</td>
                            <td >Target</td>
                            <td >Weight</td>
                            <td >Rating</td>
                            <td >Computation (Rating x %)</td>
                            <td >Comments</td>
                        </tr>
                        <tr>
                            <th colspan="6">Loan Improvement</th>
                        </tr>
                        <tr>
                            <td style="text-align: left !important;">
                                5 - {{ $stage_1_fifth_indicator1 }} <br />
                                4 - {{ $stage_1_fourth_indicator1 }} <br />
                                3 - {{ $stage_1_third_indicator1 }} <br />
                                2 - {{ $stage_1_second_indicator1 }} <br />
                                1 - {{ $stage_1_first_indicator1 }}
                            </td>
                            <td >{{ $kpi->loan_amount->target }}</td>
                            <td >20%</td>
                            <td>
                                <div class="custom-table" >
                                    <div class="table-row">
                                        <div class="table-cell-first" >EOM Rating</div>
                                        <div class="table-cell" >W2</div>
                                        <div class="table-cell" >W3</div>
                                        <div class="table-cell-last" >W4</div>
                                    </div>
                                    <br />
                                    <div class="table-row">
                                    @foreach(unserialize($kpi->loan_amount->rating) as $key => $rating)
                                        @if ($key == 3)
                                            <div class="table-cell-max-last" >
                                                {{ $rating }}
                                            </div>
                                        @elseif ($key == 0)
                                            <div class="table-cell-max-first" >
                                                {{ $rating }}
                                            </div>
                                        @else
                                            <div class="table-cell-max" >
                                                {{ $rating }}
                                            </div>
                                        @endif
                                    @endforeach
                                    </div>
                                </div>
                            </td>
                            <td >{{ $kpi->loan_amount->computation }}</td>
                            <td >{{ $kpi->loan_amount->comment }}</td>
                        </tr>
                        <tr>
                            <th colspan="6">Knowledge and Communication Assessment</th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_1_fifth_indicator2 }} <br />
                                4 - {{ $stage_1_fourth_indicator2 }} <br />
                                3 - {{ $stage_1_third_indicator2 }} <br />
                                2 - {{ $stage_1_second_indicator2 }} <br />
                                1 - {{ $stage_1_first_indicator2 }}
                            </td>
                            <td >{{ $kpi->knowledge->target }}</td>
                            <td >30%</td>
                            <td >{{ $kpi->knowledge->rating }}</td>
                            <td >{{ $kpi->knowledge->computation }}</td>
                            <td >{{ $kpi->knowledge->comment }}</td>
                        </tr>
                        <tr>
                            <th colspan="6">QA Score</th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_1_fifth_indicator3 }} <br />
                                4 - {{ $stage_1_fourth_indicator3 }} <br />
                                3 - {{ $stage_1_third_indicator3 }} <br />
                                2 - {{ $stage_1_second_indicator3 }} <br />
                                1 - {{ $stage_1_first_indicator3 }}
                            </td>
                            <td >{{ $kpi->qa_score->target }}</td>
                            <td >10%</td>
                            <td >{{ $kpi->qa_score->rating }}</td>
                            <td >{{ $kpi->qa_score->computation }}</td>
                            <td >{{ $kpi->qa_score->comment }}</td>
                        </tr>
                        <tr>
                            <th colspan="6">Coaching Logs</th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_1_fifth_indicator4 }} <br />
                                4 - {{ $stage_1_fourth_indicator4 }} <br />
                                3 - {{ $stage_1_third_indicator4 }} <br />
                                2 - {{ $stage_1_second_indicator4 }} <br />
                                1 - {{ $stage_1_first_indicator4 }}
                            </td>
                            <td >{{ $kpi->coaching_log->target }}</td>
                            <td >20%</td>
                            <td >{{ $kpi->coaching_log->rating }}</td>
                            <td >{{ $kpi->coaching_log->computation }}</td>
                            <td >{{ $kpi->coaching_log->comment }}</td>
                        </tr>
                        <tr>
                            <th colspan="6">Attendance Reliability</th>
                        </tr>
                        <tr>
                            <td style="text-align: left! important;">
                                5 - {{ $stage_1_fifth_indicator5 }} <br />
                                4 - {{ $stage_1_fourth_indicator5 }} <br />
                                3 - {{ $stage_1_third_indicator5 }} <br />
                                2 - {{ $stage_1_second_indicator5 }} <br />
                                1 - {{ $stage_1_first_indicator5 }}
                            </td>
                            <td >{{ $kpi->attendance_reliability->target }}</td>
                            <td >20%</td>
                            <td >{{ $kpi->attendance_reliability->rating }}</td>
                            <td >{{ $kpi->attendance_reliability->computation }}</td>
                            <td >{{ $kpi->attendance_reliability->comment }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"style="text-align: left! important;">
                            II. DEVELOPMENT PLAN</td>
                        </tr>
                        <tr>
                            <td colspan="3">STRENGTHS</td>
                            <td colspan="3">WEAKNESSES</td>
                        </tr>
                        <tr>
                            @if (strlen($kpi->strengths) != 1)
                                <td colspan="3" style="background-color:yellow;padding-right:10px;">
                                {{ $kpi->strengths }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                            @if (strlen($kpi->weakness) != 1)
                                <td colspan="3" style="background-color:yellow">
                                {{ $kpi->weakness }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="6">ACTION PLAN</td>
                        </tr>
                        <tr>
                            @if (strlen($kpi->action_plans) != 1)
                                <td colspan="6" style="background-color:yellow;">
                                {{ $kpi->action_plans }}</td>
                            @else
                                <td colspan="6" style="background-color:yellow"><br /></td>
                            @endif
                        </tr>
                    </table>
                @endif

            @endif

            @foreach ($file_image as $key => $file_location)
                @if ($overWidth[$key])
                    <div class="clipzone" >
                        <img class="img-responsive" width="700px"
                        src="{{ public_path($file_location) }}" alt="alt text"/>
                    </div>
                @elseif ($overHeight[$key])
                    <div>
                        <span>overHeight</span>
                    </div>
                    <div class="clipzone" >
                        <img class="imgdiv" width="700px" src="{{ public_path($file_location) }}" />
                    </div>
                    <div class="clipzone2" >
                        <img src="{{ public_path($file_location) }}"  width="700px" />
                        <br /><br />
                    </div>
                @else
                    <div class="clipzone" >
                        <img src="{{ public_path($file_location) }}" />
                    </div>
                @endif
            @endforeach

            <div class="plain" style="font-size:15px;">
                This is to acknowledge that I have received the coaching log. Please note that a 
                copy of this memo shall be filed<br>in 201 file for reference. By signing my name 
                below, I hereby confirm that I have received and understood the <br>contents of the 
                coaching log and likewise proceed with the commencement of any corrective action 
                required of<br>me by immediate supervisor should I failed to follow what was discussed.
            </div>
            <table style="border-collapse:collapse; border:0px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pull-left" style="margin-left: 20px;font-size:14px; border:0px;">
                            Received By:<br><br><br>
                            <div style="text-align: center;">{{$name}}</div>
                            _______________________________<br>
                            <div style="text-align: center;">Agent<br></div>
                    </td>
                    <td class="pull-right" style="font-size:14px;border:0px;">
                        <div >
                            Issued By:<br><br><br>
                            <div style="text-align: center;">{{$supervisor}}</div>
                            _______________________________<br>
                            <div style="text-align: center;">Supervisor<br></div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="plain">
                <div class="center-signature">
                    Noted By:<br><br>
                    _______________________________<br>
                    <div style="text-align: center;">Dept. Ops Manager</div>
                </div>
            </div>

            <div >
                @if ($form_type == 2)
                    @include('pdf.generate.coaching_log.createRecruitmentAdmin')
                @elseif ($form_type == 3)
                    @include('pdf.generate.coaching_log.createHRBPCompBenHris')
                @elseif ($form_type == 4)
                    @include('pdf.generate.coaching_log.createHRBPCompBenCandB')
                @elseif ($form_type == 5)
                    @include('pdf.generate.coaching_log.createHRBPCompBenFinalPay')
                @elseif ($form_type == 6)
                    @include('pdf.generate.coaching_log.createHRBPSiteLeadManager')
                @elseif ($form_type == 7)
                    @include('pdf.generate.coaching_log.createHRBPCompBenOnboarding')
                @elseif ($form_type == 8)
                    @include('pdf.generate.coaching_log.createHRBPCompBenPayroll')
                @elseif ($form_type == 9)
                    @include('pdf.generate.coaching_log.createRecruitment')
                @elseif ($form_type == 10)
                    @include('pdf.generate.coaching_log.createHRBPSelfRating')
                @elseif ($form_type == 11)
                    @include('pdf.generate.coaching_log.createHRBPSiteLeadSelfRating')
                @elseif ($form_type == 12)
                    @include('pdf.generate.coaching_log.createRecruitmentSourcing')
                @elseif ($form_type == 13)
                    @include('pdf.generate.coaching_log.createRecruitmentSupervisor')
                @elseif ($form_type == 14)
                    @include('pdf.generate.coaching_log.createHRBPSupervisorRating')
                @endif
            </div>

        </div>
    </div>
</body>
</html>
