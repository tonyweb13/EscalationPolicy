<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style>
    @page { margin: 100px 25px; }
    footer { position: fixed; bottom: -120px; left: 0px; right: 0px; height: 50px; }
    .pull-left {
        float: left!important;
    }
    .pull-right {
        float: right!important;
    }
    .top {
        width: 100%;
        position: fixed;
        top: -80px;
        left: -5px;
        right: -5px;
        height: 50px;
    }
    .title {
        width: 100%;
        text-align:center;
    }
    .content {
         width: 100%;
    }
    hr.divider {
        border: 1px solid black;
        margin-bottom: 20px;
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
    	column-width: 200px;

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

    .parent {
        position: relative;
        width: 80%;
        height: 80%;
        margin: 0 40px;
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
    #watermark {
        position: fixed;
        bottom:   0px;
        left:     0px;
        z-index:  -1000;

        /** The width and height may change
            according to the dimensions of your letterhead
        **/
        width:    20cm;
        height:   25cm;

        /** Your watermark should be behind every content**/
    }
    </style>
    @php
        if ($weekly_status === 0) {
            $divStyle='display:none';
        } else {
            $divStyle='display:block';
        }
    @endphp
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.header')
    @include('pdf.footer')
    <div class="row" style="margin-top: 15px;">
        <table >
            <tr>
                <th style="width:15%;">Name</th>
                <td>{{ $name }}</td>
                <th  style="width:15%;">Date</th>
                <td>{{ $date }}</td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{$employee_no}}</td>
                <th>Designation</th>
                <td>{{$designation}}</td>
            </tr>
            <tr>
                <th>Supervisor</th>
                <td>{{$supervisor}}</td>
                <th>Department</th>
                <td>{{$department}}</td>
            </tr>

        </table>
        <br>
        <table>
            <tr>
                <th colspan="2">Target KPI/s</th>
                @if ($week5)
                    <td colspan="10">Loan Origination</td>
                @else
                    <td colspan="8">Loan Origination</td>
                @endif
            </tr>
            <tr>
                <th colspan="2">Identified Behavior/s</th>
                @if ($week5)
                    <td colspan="10">{{ $identified_behavior }}</td>
                @else
                    <td colspan="8">{{ $identified_behavior }}</td>
                @endif
            </tr>
            <tr>
                <th colspan="2">Root Cause/s</th>
                @if ($week5)
                    <td colspan="10">{{ $root_cause }}</td>
                @else
                    <td colspan="8">{{ $root_cause }}</td>
                @endif

            </tr>
            <tr>
                <th colspan="2" style="height:40px;">Action Plan/s</th>
                @if ($week5)
                    <td colspan="10" style="height:40px;">{{ $action_plans }}</td>
                @else
                    <td colspan="8" style="height:40px;">{{ $action_plans }}</td>
                @endif
            </tr>
            <tr>
                <th colspan="2">Monthly Goal</th>
                <td colspan="3">$ {{ $base_performance ? $base_performance : "$0" }}</td>
                <th colspan="2">Goal</th>
                @if ($week5)
                    <td colspan="5">$ {{ $goal ? $goal : "$0" }}</td>
                @else
                    <td colspan="3">$ {{ $goal ? $goal : "$0" }}</td>
                @endif
            </tr>
            <tr>
                <th rowspan="12">Weekly Goals</th>
                <th rowspan="2">KPI</th>
                <th colspan="2">Week 1</th>
                <th colspan="2">Week 2</th>
                <th colspan="2">Week 3</th>
                <th colspan="2">Week 4</th>
                @if ($week5)
                    <th colspan="2">Week 5</th>
                @endif
            </tr>
            <tr>
                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                @if ($week5)
                    <th>Target</th>
                    <th>Actual</th>
                @endif
            </tr>
            <tr>
                <th>Loan Origination</th>

                <td class="shaded">@if ($week1_target_loan_origination) $ @endif {{ $week1_target_loan_origination }}</td>
                <td>@if ($week1_actual_loan_origination) $ @endif{{ $week1_actual_loan_origination }}</td>

                <td class="shaded">@if ($week2_target_loan_origination) $ @endif{{ $week2_target_loan_origination }}</td>
                <td>@if ($week2_actual_loan_origination) $ @endif{{ $week2_actual_loan_origination }}</td>

                <td class="shaded">@if ($week3_target_loan_origination) $ @endif{{ $week3_target_loan_origination }}</td>
                <td >@if ($week3_actual_loan_origination) $ @endif{{ $week3_actual_loan_origination }}</td>

                <td class="shaded">@if ($week4_target_loan_origination) $ @endif{{ $week4_target_loan_origination }}</td>
                <td>@if ($week4_actual_loan_origination) $ @endif{{ $week4_actual_loan_origination }}</td>


                @if ($week5)
                    <td class="shaded">@if ($week4_target_loan_origination) $ @endif {{ $week5_target_loan_origination }}</td>
                    <td>@if ($week4_target_loan_origination) $ @endif {{ $week5_actual_loan_origination }}</td>
                @endif
            </tr>

            <tr>
                <th>QA Scores</th>

                <td class="shaded">{{ $week1_target_qa_scores }} @if ($week1_target_qa_scores) % @endif</td>
                <td>{{ $week1_actual_qa_scores }} @if ($week1_actual_qa_scores)% @endif</td>

                <td class="shaded">{{ $week2_target_qa_scores }} @if ($week2_target_qa_scores) % @endif</td>
                <td>{{ $week2_actual_qa_scores }} @if ($week2_actual_qa_scores)% @endif</td>

                <td class="shaded">{{ $week3_target_qa_scores }} @if ($week3_target_qa_scores) % @endif</td>
                <td>{{ $week3_actual_qa_scores }} @if ($week3_actual_qa_scores)% @endif</td>

                <td class="shaded">{{ $week4_target_qa_scores }} @if ($week4_target_qa_scores) % @endif</td>
                <td>{{ $week4_actual_qa_scores }} @if ($week4_actual_qa_scores)% @endif</td>

                @if ($week5)
                    <td class="shaded">{{ $week5_target_qa_scores }} @if ($week5_target_qa_scores) % @endif</td>
                    <td>{{ $week5_actual_qa_scores }} @if ($week5_actual_qa_scores) % @endif</td>
                @endif

            </tr>

            <tr>
                <th>Compliance</th>

                <td class="shaded">{{ $week1_target_compliance }}</td>
                <td>{{ $week1_actual_compliance }}</td>

                <td class="shaded">{{ $week2_target_compliance }}</td>
                <td>{{ $week2_actual_compliance }}</td>

                <td class="shaded">{{ $week3_target_compliance }}</td>
                <td>{{ $week3_actual_compliance }}</td>

                <td class="shaded">{{ $week4_target_compliance }}</td>
                <td>{{ $week4_actual_compliance }}</td>

                @if ($week5)
                    <td class="shaded">{{ $week5_target_compliance }}</td>
                    <td>{{ $week5_actual_compliance }}</td>
                @endif
            </tr>

            <tr>
                <th>Attendance</th>

                <td class="shaded">{{ $week1_target_attendance }} @if ($week1_target_attendance) % @endif</td>
                <td>{{ $week1_actual_attendance }} @if ($week1_actual_attendance) % @endif</td>

                <td class="shaded">{{ $week2_target_attendance }} @if ($week2_target_attendance) % @endif</td>
                <td>{{ $week2_actual_attendance }} @if ($week2_actual_attendance) % @endif</td>

                <td class="shaded">{{ $week3_target_attendance }} @if ($week3_target_attendance) % @endif</td>
                <td>{{ $week3_actual_attendance }} @if ($week3_actual_attendance) % @endif</td>

                <td class="shaded">{{ $week4_target_attendance }} @if ($week4_target_attendance) % @endif</td>
                <td>{{ $week4_actual_attendance }} @if ($week4_actual_attendance) % @endif</td>

                @if ($week5)
                    <td class="shaded">{{ $week5_target_attendance }} @if ($week5_target_attendance) % @endif</td>
                    <td>{{ $week5_actual_attendance }} @if ($week5_actual_attendance) % @endif</td>
                @endif
            </tr>

            <tr>
                <th rowspan="2">Efficiency</th>
                <th colspan="2">Week 1</th>
                <th colspan="2">Week 2</th>
                <th colspan="2">Week 3</th>
                <th colspan="2">Week 4</th>
                @if ($week5)
                    <th colspan="2">Week 5</th>
                @endif
            </tr>

            <tr>
                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                <th>Target</th>
                <th>Actual</th>

                @if ($week5)
                    <th>Target</th>
                    <th>Actual</th>
                @endif
            </tr>

            <tr>
                <th>In Call (%)</th>

                <td class="shaded">{{ $week1_target_in_call }}  @if ($week1_target_in_call) % @endif</td>
                <td>{{ $week1_actual_in_call }}  @if ($week1_actual_in_call) % @endif</td>

                @if($week2_target_in_call)
                    <td class="shaded">{{ $week2_target_in_call }}  @if ($week2_target_in_call) % @endif</td>
                    <td>{{ $week2_actual_in_call }}  @if ($week2_actual_in_call) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week3_target_in_call)
                    <td class="shaded">{{ $week3_target_in_call }}  @if ($week3_target_in_call) % @endif</td>
                    <td>{{ $week3_actual_in_call }}  @if ($week3_actual_in_call) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week4_target_in_call)
                    <td class="shaded">{{ $week4_target_in_call }} @if ($week4_target_in_call) % @endif</td>
                    <td>{{ $week4_actual_in_call }} @if ($week4_actual_in_call) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if ($week5)
                    <td class="shaded">{{ $week5_target_in_call }} @if ($week5_target_in_call) % @endif</td>
                    <td>{{ $week5_actual_in_call }} @if ($week5_actual_in_call) % @endif</td>
                @endif
            </tr>

            <tr>
                <th>Ready (%)</th>

                <td class="shaded">{{ $week1_target_ready }}  @if ($week1_target_ready) % @endif</td>
                <td>{{ $week1_actual_ready }}  @if ($week1_actual_ready) % @endif</td>

                @if($week2_target_ready)
                    <td class="shaded">{{ $week2_target_ready }}  @if ($week2_target_ready) % @endif</td>
                    <td>{{ $week2_actual_ready }}  @if ($week2_actual_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week3_target_ready)
                    <td class="shaded">{{ $week3_target_ready }}  @if ($week3_target_ready) % @endif</td>
                    <td>{{ $week3_actual_ready }}  @if ($week3_actual_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week4_target_ready)
                    <td class="shaded">{{ $week4_target_ready }} @if ($week4_target_ready) % @endif</td>
                    <td>{{ $week4_actual_ready }} @if ($week4_actual_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if ($week5)
                    <td class="shaded">{{ $week5_target_ready }} @if ($week5_target_ready) % @endif</td>
                    <td>{{ $week5_actual_ready }} @if ($week5_actual_ready) % @endif</td>
                @endif
            </tr>

            <tr>
                <th>Wrap up (%)</th>

                <td class="shaded">{{ $week1_target_wrap_up }} @if ($week1_target_wrap_up) % @endif</td>
                <td>{{ $week1_actual_wrap_up }} @if ($week1_actual_wrap_up) % @endif</td>

                @if($week2_target_wrap_up)
                    <td class="shaded">{{ $week2_target_wrap_up }} @if ($week2_target_wrap_up) % @endif</td>
                    <td>{{ $week2_actual_wrap_up }} @if ($week2_actual_wrap_up) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week3_target_wrap_up)
                    <td class="shaded">{{ $week3_target_wrap_up }} @if ($week3_target_wrap_up) % @endif</td>
                    <td>{{ $week3_actual_wrap_up }} @if ($week3_actual_wrap_up) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week4_target_wrap_up)
                    <td class="shaded">{{ $week4_target_wrap_up }} @if ($week4_target_wrap_up) % @endif</td>
                    <td>{{ $week4_actual_wrap_up }} @if ($week4_actual_wrap_up) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if ($week5)
                    <td class="shaded">{{ $week5_target_wrap_up }} @if ($week5_target_wrap_up) % @endif</td>
                    <td>{{ $week5_actual_wrap_up }} @if ($week5_actual_wrap_up) % @endif</td>
                @endif
            </tr>

            <tr>
                <th>Not Ready (%)</th>

                <td class="shaded">{{ $week1_target_not_ready }} @if ($week1_target_not_ready) % @endif</td>
                <td>{{ $week1_actual_not_ready }} @if ($week1_actual_not_ready) % @endif</td>

                @if($week2_target_not_ready)
                    <td class="shaded">{{ $week2_target_not_ready }} @if ($week2_target_not_ready) % @endif</td>
                    <td>{{ $week2_actual_not_ready }} @if ($week2_actual_not_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week3_target_not_ready)
                    <td class="shaded">{{ $week3_target_not_ready }} @if ($week3_target_not_ready) % @endif</td>
                    <td>{{ $week3_actual_not_ready }} @if ($week3_actual_not_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if($week4_target_not_ready)
                    <td class="shaded">{{ $week4_target_not_ready }} @if ($week4_target_not_ready) % @endif</td>
                    <td>{{ $week4_actual_not_ready }} @if ($week4_actual_not_ready) % @endif</td>
                @else
                    <td class="shaded"></td>
                    <td></td>
                @endif

                @if ($week5)
                    <td class="shaded">{{ $week5_target_not_ready }} @if ($week5_target_not_ready) % @endif</td>
                    <td>{{ $week5_actual_not_ready }} @if ($week5_actual_not_ready) % @endif</td>
                @endif
            </tr>

            <tr>
                <th colspan="2">Extend Action Plan ?</th>
                <td colspan="3">{{ $extended_action_plan }}</td>

                <th colspan="2">Justification</th>
                @if ($week5)
                    <td colspan="5">{{ $justification }}</td>
                @else
                    <td colspan="3">{{ $justification }}</td>
                @endif

            </tr>

        </table><br />
        <table  >
            <tr>
                <th style="width:20%;">Running Scorecard</th>
                <th style="width:5%;">Weight</th>
                <th style="width:5%;">Current Scores</th>
                <th colspan="2">Rating</th>
                <th style="width:50%;">Additional Feedback</th>
            </tr>
            <tr>
                <th>Loan Amount</th>
                @if ($weight_loan_amount)
                    <td class="shaded"> @if ($weight_loan_amount) @endif {{ $weight_loan_amount }} %</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($current_score_loan_amount)
                    <td> @if ($current_score_loan_amount) @endif {{ $current_score_loan_amount }} %</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_left_loan_amount)
                    <td>{{$rating_left_loan_amount}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_loan_amount)
                    <td>{{$rating_right_loan_amount}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                
                <td class="no-bottom-border" rowspan="6"> {{ $additional_feedback }}</td>
            </tr>

            <tr>
                <th>QA Score</th>
                @if ($weight_qa_score)
                    <td class="shaded">{{ $weight_qa_score }} @if ($weight_qa_score) % @endif </td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($current_score_qa_score)
                    <td>{{ $current_score_qa_score }} @if ($current_score_qa_score) % @endif </td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_left_qa_score)
                    <td>{{$rating_left_qa_score}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_qa_score)
                    <td>{{$rating_right_qa_score}}</td>
                @else
                    <td class="shaded"></td>
                @endif
            </tr>

            <tr>
                <th>Compliance</th>
                @if ($weight_compliance)
                    <td class="shaded">{{ $weight_compliance }} @if ($weight_compliance) % @endif </td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($current_score_compliance)
                    <td>{{ $current_score_compliance }}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_left_compliance)
                    <td>{{$rating_left_compliance}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_compliance)
                    <td>{{$rating_right_compliance}}</td>
                @else
                    <td class="shaded"></td>
                @endif
            </tr>

            <tr>
                <th>Call out - QA and UW</th>
                @if ($weight_correction)
                    <td class="shaded">{{ $weight_correction }}%</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($current_score_correction)
                    <td>{{ $current_score_correction }}%</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_left_correction)
                    <td>{{$rating_left_correction}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_correction)
                    <td>{{$rating_right_correction}}</td>
                @else
                    <td class="shaded"></td>
                @endif
            </tr>

            <tr>
                <th>Attendance Reliability</th>
                @if ($weight_attendance_reliability)
                    <td class="shaded">{{ $weight_attendance_reliability }}%</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($current_score_attendance_reliability)
                    <td>{{ $current_score_attendance_reliability }}%</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_left_attendance_reliability)
                    <td>{{$rating_left_attendance_reliability}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_attendance_reliability)
                    <td>{{$rating_right_attendance_reliability}}</td>
                @else
                    <td class="shaded"></td>
                @endif
            </tr>
            <tr>
                <th>Total</th>

                @if ($weight_total)
                    <td class="shaded" colspan="2">{{ $weight_total }}%</td>
                @else
                    <td class="shaded" colspan="2"></td>
                @endif
                @if ($rating_left_total)
                    <td>{{$rating_left_total}}</td>
                @else
                    <td class="shaded"></td>
                @endif
                @if ($rating_right_total)
                    <td>{{$rating_right_total}}</td>
                @else
                    <td class="shaded"></td>
                @endif
            </tr>

        </table>
        <br/>
        <div class="pull-left" style="font-size:14px;">

                Received By:<br>
                <div style="margin-left:80px;">{{$name}}<br></div>
                ____________________________________<br>
                <div style="margin-left:100px;">Agent<br></div>
        </div>
        <div class="pull-right" style="font-size:14px;">
                Issued By:<br>
                <div style="margin-left:80px;">{{$supervisor}}<br></div>
                ____________________________________<br>
                <div style="margin-left:90px;">Supervisor<br></div>
        </div>
        <br><br><br><br><br>
        <div class="pull-left" style="font-size:14px;">
                Reviewed By:<br>
                <div style="margin-left:80px;"><br></div>
                ____________________________________<br>
                <div style="margin-left:80px;">Dept. Ops Manager</div>
        </div>
    </div>
</body>
</html>
