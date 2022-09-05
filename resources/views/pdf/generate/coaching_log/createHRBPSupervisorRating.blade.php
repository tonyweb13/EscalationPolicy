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
        <td rowspan="5"> 1 </td>
        <td colspan="5"> BEHAVIORAL INDICATORS </td>
    </tr>
    <tr>
        <td >
            <b>Critical thinking</b>- Ability to resolve complex situations with minimal
            supervision and utilize available data to arrive at the best solution.
            Has made a contribution that is align with the department's/company's objectives.
        </td>
        <td>{{$form_added['self_thinking_target']}}</td>
        <td>{{$form_added['self_thinking_weight']}}</td>
        <td>{{$form_added['self_thinking_rating_scale']}}</td>
        <td>{{$form_added['self_thinking_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Initiative</b>-Possess a willingness to work hard and to work smart.
            Learning the most efficient way to complete tasks and finding ways to save
            time while completing daily assignments while maintaining a positive attitude.
            Doing more than is expected.
        </td>
        <td>{{$form_added['initiative_target']}}</td>
        <td>{{$form_added['initiative_weight']}}</td>
        <td>{{$form_added['initiative_rating_scale']}}</td>
        <td>{{$form_added['initiative_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Work Ethics</b>-to set an example to all employees in effectively promoting and following
            rules and regulations of the company. Being able to respond expeditiously to inquiries
            within the operation with minimal to zero complains / escalations.
            Construclively responsive to feedback.
        </td>
        <td>{{$form_added['work_ethics_target']}}</td>
        <td>{{$form_added['work_ethics_weight']}}</td>
        <td>{{$form_added['work_ethics_rating_scale']}}</td>
        <td>{{$form_added['work_ethics_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Responsiveness/Communication/Influence</b>- Communicates effectively and is able to use
            this as a tool to partner with his/her cluster in delivering results.
        </td>
        <td>{{$form_added['responsiveness_target']}}</td>
        <td>{{$form_added['responsiveness_weight']}}</td>
        <td>{{$form_added['responsiveness_rating_scale']}}</td>
        <td>{{$form_added['responsiveness_final_score']}}</td>
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
        <td>{{$form_added['attendance_target']}}</td>
        <td>{{$form_added['attendance_weight']}}</td>
        <td>{{$form_added['attendance_rating_scale']}}</td>
        <td>{{$form_added['attendance_final_score']}}</td>
    </tr>
    <tr>
        <td rowspan="4"> 3 </td>
        <td colspan="5"> HR QUALITY </td>
    </tr>
    <tr>
        <td >
            <b>Data Analysis</b> - Employee is expected to present monthly report with go-to-green plans.
            Proposed action plans for process improvement and employee retention.
        </td>
        <td>{{$form_added['data_target']}}</td>
        <td>{{$form_added['data_weight']}}</td>
        <td>{{$form_added['data_rating_scale']}}</td>
        <td>{{$form_added['data_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>HR Intervention</b> - Ability to carry oneself when addressing issues through
            application of Implemented guidelines and policies.
            Consistent deliberation with stakeholders based on the reports and analysis.
            Verify the effectiveness of HR talks by initiating a follow through.
        </td>
        <td>{{$form_added['intervention_target']}}</td>
        <td>{{$form_added['intervention_weight']}}</td>
        <td>{{$form_added['intervention_rating_scale']}}</td>
        <td>{{$form_added['intervention_final_score']}}</td>
    </tr>
    <tr>
        <td >
            <b>Cluster Satisfaction</b> - Minimal complaints received from respective Cluster
            and Agreeableness of HR Business Partner towards his/her cluster.
        </td>
        <td>{{$form_added['cluster_target']}}</td>
        <td>{{$form_added['cluster_weight']}}</td>
        <td>{{$form_added['cluster_rating_scale']}}</td>
        <td>{{$form_added['cluster_final_score']}}</td>
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
