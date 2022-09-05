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
		<td colspan="6">
                <b>HUMAN CAPITAL DEPARTMENT</b> <br />
                HUMAN CAPITAL BUSINESS PARTNER PERFORMANCE REVIEW
        </td>
	</tr>
    <tr>
		<td colspan="2" rowspan="2">Key Performance Indicatior</td>
        <td colspan="3">Scoring</td>
		<td>Month</td>
	</tr>
    <tr>
        <td>TARGET RATING</td>
        <td>WEIGHT</td>
        <td>SCORE ON A 1 - 5 RATING SCALE</td>
		<td>FINAL SCORE</td>
	</tr>
    <tr>
        <td rowspan="3"> 1 </td>
        <td colspan="5"> BEHAVIORAL INDICATORS </td>
    </tr>
    <tr>
        <td >
            <b>Work Ethics</b> - Maintains professionalism within HR and respective site.
            Promotes high value of discipline, integrity, resiliency, confidentiality
            and effective communication.Strengthens positive working relationship.
        </td>
        <td>{{$form_added['work_ethics_target']}}</td>
        <td>{{$form_added['work_ethics_weight']}}</td>
        <td>{{$form_added['work_ethics_rating_scale']}}</td>
        <td>{{$form_added['work_ethics_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Leadership</b> - has a high sense of responsibility,
            demonstrating good strategy within the team and posseses critical
            thinking skills to resolve issues at hand.
            Improves knowledge on technical aspect to drive team's performance.
            Proactively focus on meeting objectives in a timely fashion manner.
            Has a strong business acumen to manage business risk.
        </td>
        <td>{{$form_added['leadership_target']}}</td>
        <td>{{$form_added['leadership_weight']}}</td>
        <td>{{$form_added['leadership_rating_scale']}}</td>
        <td>{{$form_added['leadership_final_score']}}</td>
    </tr>
    <tr>
        <td rowspan="2"> 2 </td>
        <td colspan="5"> ATTENDANCE RELIABILITY </td>
    </tr>
    <tr>
        <td >
            <p>
                5 - 100%
            </p>
            <p>
                4 - 95%
            </p>
            <p>
                3 - 90%
            </p>
            <p>
                2 - 85%
            </p>
            <p>
                1 - 80%.
            </p>
        </td>
        <td>{{$form_added['attendance_reliability_target']}}</td>
        <td>{{$form_added['attendance_reliability_weight']}}</td>
        <td>{{$form_added['attendance_reliability_rating_scale']}}</td>
        <td>{{$form_added['attendance_reliability_final_score']}}</td>
    </tr>
    <tr>
        <td rowspan="3"> 3 </td>
        <td colspan="5"> HR QUALITY </td>
    </tr>
    <tr>
        <td >
            <b>Monthly Data Analysis</b> - Presenting monthly awareness to site leaders to
            manage attrition, absenteeism and DA cases within the site.
            Drives high level of resolution based on business needs.
        </td>
        <td>{{$form_added['monthly_data_target']}}</td>
        <td>{{$form_added['monthly_data_weight']}}</td>
        <td>{{$form_added['monthly_data_rating_scale']}}</td>
        <td>{{$form_added['monthly_data_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Monthly Site Satisfaction Rating</b> - Maintaining good satisfaction rating by
            strengthening collaboration with operations and other departments,
            driving excellence through results and proactiveness.
        </td>
        <td>{{$form_added['monthly_site_target']}}</td>
        <td>{{$form_added['monthly_site_weight']}}</td>
        <td>{{$form_added['monthly_site_rating_scale']}}</td>
        <td>{{$form_added['monthly_site_final_score']}}</td>
    </tr>
    <tr>
        <td rowspan="2"> 4 </td>
        <td colspan="5"> Productivity </td>
    </tr>
    <tr>
        <td >
            Quality in output through monthly team's performance,
            timely submission of deliverables and efficiency in meeting corporate objectives.
        </td>
        <td>{{$form_added['productivity_target']}}</td>
        <td>{{$form_added['productivity_weight']}}</td>
        <td>{{$form_added['productivity_rating_scale']}}</td>
        <td>{{$form_added['productivity_final_score']}}</td>
    </tr>
    <tr>
        <td colspan="2">Total:</td>
        <td colspan="4">{{$form_added['total_score']}}</td>
	</tr>
    <tr>
        <td colspan="6">
            <p style="text-align:center">
                Commendation:
            </p>
            <p>
                {{$form_added['commendation']}}
            </p>
        </td>
	</tr>
    <tr>
        <td colspan="6">
            <p style="text-align:center">
                ACTION PLAN FOR OPPORTUNITIES:
            </p>
            <p>
                {{$form_added['action_plans']}}
            </p>
        </td>
	</tr>
    <tr>
        <td colspan="4">III. REVIEWED AND APPROVED BY IMMEDIATE SUPERIOR/S</td>
        <td colspan="2">IV.   EMPLOYEE ACKNOWLEDGEMENT / COMMENTS</td>
    </tr>
    <tr>
        <td colspan="4" rowspan="3">
            <div class="plain" style="padding-left:20px" >
                <div style="text-align: center;width:50%"></div>
				__________________________________________________<br>
				<div style="text-align: center;width:50%">HUMAN CAPITAL SITE LEAD -<br>

            </div><br />
            <div class="plain" >
                <div style="text-align: center;width:50%"></div>
				__________________________________________________<br>
				<div style="text-align: center;width:50%">HUMAN CAPITAL MANAGER<br>
            </div>
        </td>
        <td colspan="2">
            I acknowledge that I have seen my Overall Rating and discussed my
            Monthly Performance Review with my Site Lead.
        </td>
    </tr>
    <tr>
        <td style="height:100px" colspan="2">

        </td>
    </tr>
    <tr>
        <td colspan="2">
            SIGNATURE OVER PRINTED NAME
        </td>
    </tr>
</table>
