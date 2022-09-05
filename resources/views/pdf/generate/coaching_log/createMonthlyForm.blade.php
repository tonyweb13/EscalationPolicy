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
        height:   25cm;+

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
    <div class="row">
        <div class="top">
            <div class="pull-left">
                <img src="img/arb.jpg" style="width:110px; height:50px;">
            </div>
            <div class="pull-right" style="font-size: 12px; color: gray;">
                19th Floor Two/Neo Building <br>
                3rd Avenue corner E-Square Crescent Park <br>
                West, 28th St, Taguig, 1634 Metro Manila
            </div>
        </div>
        <table >
            <tr>
                <th style="width:15%;">Name</th>
                <td>{{ $name}}</td>
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
            <tr>
                <th>Tenureship</th>
                <td>{{$tenureship}}</td>
                <th>Days (If Probitionary)</th>
                <td>{{$days}}</td>
            </tr>
            <tr>
                <th>Coverage</th>
                <td>{{$coverage}}</td>
                <th>Portfolio</th>
                <td>{{$portfolio}}</td>
            </tr>

        </table>
        <br>
        <table>
            <tr>
                <th colspan="2">Target KPI/s</th>
                <td colspan="8">Loan Origination</td>
            </tr>
            <tr>
                <th colspan="2">Identified Behaivior/s</th>
                <td colspan="8"></td>
            </tr>
            <tr>
                <th colspan="2">Root Cause/s</th>
                <td colspan="8"></td>
            </tr>
            <tr>
                <th colspan="2" style="height:40px;">Action Plan/s</th>
                <td colspan="8" style="height:40px;"></td>
            </tr>
            <tr>
                <th colspan="2">Monthly Goal</th>
                <td colspan="3"></td>
                <th colspan="2">Goal</th>
                <!-- <td colspan="3">{{$end_scg[0] ? "$".number_format($end_scg[0]) : ""}}</td> -->
                <td colspan="3">{{$end_mtdg[0] ? "$".number_format($end_mtdg[0]) : ""}}</td>
            </tr>
            <tr>
                <th rowspan="12">Weekly Goals</th>
                <th rowspan="2">KPI</th>
                <th colspan="2">Week 1</th>
                <th colspan="2">Week 2</th>
                <th colspan="2">Week 3</th>
                <th colspan="2">Week 4</th>
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
            </tr>
            <tr>
                <th>Loan Origination</th>
                @<?php foreach ($loan_amount as $key => $value): ?>
                    <!-- <td class="shaded">{{$scg[$key][0]/4 ? "$".number_format($scg[$key][0]/4) : "$0"}}</td> -->
                    <td class="shaded">{{$mtdg[$key][0]/4 ? "$".number_format($mtdg[$key][0]/4) : "$0"}}</td>
                    <td>${{number_format($value, 2)}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th>QA Scores</th>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>
            </tr>

            <tr>
                <th>Compliance</th>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>

                <td class="shaded"></td>
                <td></td>
            </tr>

            <tr>
                <th>Attendance</th>

                @<?php foreach ($attendance_reliability as $key => $value): ?>
                    <td class="shaded">100%</td>
                    <td>{{($value != "%" && $value)  ? $value : "0%"}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th rowspan="2">Efficiency</th>
                <th colspan="2">Week 1</th>
                <th colspan="2">Week 2</th>
                <th colspan="2">Week 3</th>
                <th colspan="2">Week 4</th>
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
            </tr>

            <tr>
                <th>In Call (%)</th>
                @<?php foreach ($in_call as $key => $value): ?>
                    <td class="shaded">{{$in_call_tag}}%</td>
                    <td>{{$value}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th>Ready (%)</th>
                @<?php foreach ($ready as $key => $value): ?>
                    <td class="shaded">{{$ready_tag}}%</td>
                    <td>{{$value}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th>Wrap up (%)</th>
                @<?php foreach ($wrap_up as $key => $value): ?>
                    <td class="shaded">{{$wrap_up_tag}}%</td>
                    <td>{{$value}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th>Not Ready (%)</th>
                @<?php foreach ($not_ready as $key => $value): ?>
                    <td class="shaded">{{$not_ready_tag}}%</td>
                    <td>{{$value}}</td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <th colspan="2">Extend Action Plan ?</th>
                <td colspan="3"></td>

                <th colspan="2">Justification</th>
                <td colspan="3"></td>
            </tr>

            <tr>
                <th colspan="4" style="height:40px;">Agents Comments</th>
                <td colspan="6" style="height:40px;"></td>
            </tr>

        </table>

        <br/>
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
                <td class="shaded">50.00%</td>
                <td>{{$loan_amount_ave}}%</td>
                <td>{{$loan_amount_score}}</td>
                <td class="shaded">{{$loan_amount_point}}</td>
                <td class="no-bottom-border"></td>
            </tr>

            <tr>
                <th>QA Score</th>
                <td class="shaded">10.00%</td>
                <td></td>
                <td></td>
                <td class="shaded"></td>
                <td class="no-bottom-border no-top-border"></td>
            </tr>

            <tr>
                <th>Compliance</th>
                <td class="shaded">10.00%</td>
                <td></td>
                <td></td>
                <td class="shaded"></td>
                <td class="no-bottom-border no-top-border"></td>
            </tr>

            <tr>
                <th>Coaching Logs</th>
                <td class="shaded">10.00%</td>
                <td></td>
                <td></td>
                <td class="shaded"></td>
                <td class="no-bottom-border no-top-border"></td>
            </tr>

            <tr>
                <th>Attendance Reliability</th>
                <td class="shaded">20.00%</td>
                <td>{{$attendance_reliability_ave}}</td>
                <td>{{$attendance_score}}</td>
                <td class="shaded">{{$attendance_point}}</td>
                <td class="no-top-border"></td>
            </tr>
            <tr>
                <th>Total</th>
                <td class="shaded no-right-border">100.00%</td>
                <td class="shaded no-left-border no-right-border"></td>
                <td class="shaded no-left-border no-right-border"></td>
                <td class="shaded no-left-border no-right-border"></td>
                <td class="shaded no-left-border"></td>
            </tr>

        </table>
        <br>
        <div class="pull-left" style="font-size:14px;">

                Recived By:<br>
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
