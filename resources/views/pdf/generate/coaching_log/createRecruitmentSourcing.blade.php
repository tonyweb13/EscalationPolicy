<style>
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
      width: 100% !important;
    }
    .table-row {
      display: table-row;
    }
    .table-cell {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-top: 0px !important;
    }
    .table-cell-first {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-top: 0px !important;
      border-left: 0px !important;
    }
    .table-cell-last {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-top: 0px !important;
      border-right: 0px !important;
    }
    .table-cell-max {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-bottom: 0px !important;
      border-top: 0px !important;
      height: 100px;
    }
    .table-cell-max-last {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-bottom: 0px !important;
      border-right: 0px !important;
      height: 100px;
    }
    .table-cell-max-first {
      display: table-cell;
      padding: 10px;
      border: 1px solid black;
      border-bottom: 0px !important;
      border-left: 0px !important;
      height: 100px;
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
</style>
<table style="width: 690px;">
    <tr>
		<th colspan="7">Key Performance Indicators for Sourcing (KPI)</th>
	</tr>
    <tr>
		<td>Key Result Areas</td>
        <td>Indicators</td>
		<td>Target</td>
        <td>Actual Rating</td>
        <td>Weight of KPI</td>
        <td>Rating (Actual*%)</td>
        <td>Comments</td>
	</tr>
    <tr>
		<td rowspan="2">Sourcing Productivity</td>
        <td>
            <p>
                Number of Show Ups:
            </p>
            <p>
                > 400 - 5
            </p>
            <p>
                300-399 - 4
            </p>
            <p>
                200-299 - 3
            </p>
            <p>
                100-199 - 2
            </p>
            <p>
                < 99 - 1
            </p>
        </td>
        <td>{{$form_added['show_up_target']}}</td>
        <td>{{$form_added['show_up_actual_rating']}}</td>
        <td>{{$form_added['show_up_weight']}}</td>
        <td>{{$form_added['show_up_rating_scale']}}</td>
        <td>{{$form_added['show_up_comment']}}</td>
    </tr>
    <tr>
		<td>
            <p>
                Time to Fill : (Agent/Suport Hiring)
            </p>
            <p>
                15-20 Days - 5
            </p>
            <p>
                21-25 Days - 4
            </p>
            <p>
                26-30 Days - 3
            </p>
            <p>
                31-35 Days - 2
            </p>
            <p>
                > 35 Days - 1
            </p>
        </td>
        <td>{{$form_added['time_to_fill_target']}}</td>
        <td>{{$form_added['time_to_fill_actual_rating']}}</td>
        <td>{{$form_added['time_to_fill_weight']}}</td>
        <td>{{$form_added['time_to_fill_rating_scale']}}</td>
        <td>{{$form_added['time_to_fill_comment']}}</td>
	</tr>
    <tr>
        <td rowspan="2">Work Ethics</td>
		<td>
            <p>
                Number of Escalation process or complaints from client:
            </p>
            <p>
                0 - 5
            </p>
            <p>
                1 - 4
            </p>
            <p>
                2 - 3
            </p>
            <p>
                3 - 2
            </p>
            <p>
                4 - 1
            </p>
        </td>
        <td>{{$form_added['escalation_target']}}</td>
        <td>{{$form_added['escalation_actual_rating']}}</td>
        <td>{{$form_added['escalation_weight']}}</td>
        <td>{{$form_added['escalation_rating_scale']}}</td>
        <td>{{$form_added['escalation_comment']}}</td>
	</tr>
    <tr>
		<td>
            <p>
                No NTE/s relative to attendance or any infractions in the code of conduct to get 5 rating" else:
            </p>
            <p>
                1 NTE - 3
            </p>
            <p>
                2 NTE - 1
            </p>
            <p>
                3 NTE - 0
            </p>
        </td>
        <td>{{$form_added['no_nte_target']}}</td>
        <td>{{$form_added['no_nte_actual_rating']}}</td>
        <td>{{$form_added['no_nte_weight']}}</td>
        <td>{{$form_added['no_nte_rating_scale']}}</td>
        <td>{{$form_added['no_nte_comment']}}</td>
	</tr>
    <tr>
        <td>Sourcing Initiative</td>
		<td>
            <p>
                4 - 5
            </p>
            <p>
                3- 4
            </p>
            <p>
                2- 3
            </p>
            <p>
                1 - 2
            </p>
            <p>
                0 - 1
            </p>
        </td>
        <td>{{$form_added['initiative_target']}}</td>
        <td>{{$form_added['initiative_actual_rating']}}</td>
        <td>{{$form_added['initiative_weight']}}</td>
        <td>{{$form_added['initiative_rating_scale']}}</td>
        <td>{{$form_added['initiative_comment']}}</td>
	</tr>
    <tr>
        <td colspan="3">Total:</td>
		<td colspan="4">{{$form_added['total_score']}}</td>
	</tr>
    <tr>
        <td colspan="3">
            <p style="text-align:left">
                II. DEVELOPMENT PLAN
            </p>
        </td>
		<td colspan="4">{{$form_added['development_plan']}}</td>
	</tr>
    <tr>
        <td style="border-right: 0px !important">STRENGHTS:</td>
		<td colspan="2" style="border-left: 0px !important">{{$form_added['strengths']}}</td>
        <td style="border-right: 0px !important">WEAKNESSES:</td>
        <td colspan="3" style="border-left: 0px !important">{{$form_added['weaknesses']}}</td>
	</tr>
    <tr>
        <td colspan="3">III.  IMMEDIATE MANAGER'S FEEDBACK</td>
        <td colspan="4">IV. EMPLOYEE'S ACKNOWLEDGEMENT</td>
    </tr>
    <tr>
        <td colspan="3">{{$form_added['managers_feedback']}}</td>
        <td colspan="4">{{$form_added['employees_acknowledge']}}</td>
    </tr>

</table>
<div class="plain" >
	<div class="pull-left" style="margin-left: 50px;font-size:14px;width:50%">
		<div style="text-align: center;width:50%"></div>
		_______________________________<br>
		<div style="text-align: center;width:50%">SIGNATURE OVER PRINTED NAME<br></div>
	</div>
	<div class="pull-right" style="font-size:14px;width:50%;">
		_______________________________<br>
		<div style="text-align: center;width:50%">SIGNATURE OVER PRINTED NAME<br></div>
	</div>
</div>
