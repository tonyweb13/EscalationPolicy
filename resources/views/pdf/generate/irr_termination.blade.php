<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
   <style>
    @page { margin: 100px 25px; }
    #watermark {
        position: fixed;
        top:10px;
        bottom: 0px;
        right: 10px;

        /* opacity: .1; */
        width:    20cm;
        height:   28cm;

        /** Your watermark should be behind every content**/
        z-index:  -1000;
    }
    .title {
        width: 100%;
        text-align:center;
    }
    .table-border {
        page-break-after:avoid;
        border: 1px solid black;
        border-collapse: collapse;
        width:100%;
        text-align:center;
    }
    .table-border, .table-border-tr, .table-border-td {
        border: 1px solid black;
        padding: 10px;
    }
    .mainContent {
        margin-right: 25px;
        margin-left: 25px;
        font-size: 12px;
        page-break-after: avoid;
        text-align: justify;
        page-break-after:avoid;
    }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.header')
    @include('pdf.footer')
    <div class="mainContent">
        <div class="title">
            <h3><u>NOTICE OF DECISION</u></h3>
        </div>

        <div>
            <table style="font-weight:bold;">
                <tr>
                    <td>DATE</td>
                    <td> : </td>
                    <td colspan="2">{{ date_format(date_create($incident_report['date_da']), "F d, Y") }}</td>
                </div>
                <tr>
                    <td>EMPLOYEE NAME</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }}</td>
                </tr>
                <tr>
                    <td>EMPLOYEE NUMBER</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['employee_no'] }}</td>
                </tr>
                <tr>
                    <td>POSITION TITLE</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['designation']['name'] }}</td>
                </tr>
                <tr>
                    <td>NAME OF DEPT.</td>
                    <td> : </td>
                    <td colspan="2">{{ $respondent_user['department']['name'] }}</td>
                </tr>
                <tr>
                <td>CC</td>
                <td> : </td>
                    <td colspan="2">Department Head, HR, DOLE, 201 File</td>
                </tr>
            </table>
            <br>

            <div>
                Dear Mr./Ms. {{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }},
            </div>
            <br>

            <div>
                This refers to the Notice to Explain memo which we have served last
                    <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                regarding the allegation that you have violated our provisions of our Code of Conduct.
                It was reported that you have allegedly committed an infraction under the provision of
                @if ($offense_multiple)
                    @foreach ($offense_multiple as $off_multi)
                        <span style="margin-right:5px;"><b>{{ $off_multi['category']['name'] }}</b>,</span>
                    @endforeach
                @elseif ($complainant['offense'])
                    <b>{{ $complainant['offense']['category']['name'] }}</b>
                @elseif ($complainant['attendancepointssystem'])
                    Attendance.
                @endif
                on <b>{{ date_format(date_create($incident_report['created_at']), "F d, Y") }}.</b>
                Please refer to the NTE served to you. 

            @if ($response || $response == "Will not further explain" || $second_response)
                In your written response dated
                @if ($date_response == null)
                    <b>{{ date_format(date_create($created_at), "F d, Y")  }}</b>
                @elseif ($date_response != null && $second_response_date == null)
                    <b>{{ date_format(date_create($date_response), "F d, Y")  }}</b>
                @elseif ($second_response_date != null)
                    <b>{{ date_format(date_create($second_response_date), "F d, Y")  }}</b>
                @endif
                which we received on
                <b>{{ date_format(date_create($incident_report['created_at']), "F d, Y")  }}</b>
                , you mentioned that
            @endif
            </div>
            <br>

            <div>
                @if ($response == "Will not further explain" && $second_response) 
                    {!! $second_response !!}
                @elseif ($response || $response == "Will not further explain")
                    {!! $response !!} 
                @endif
            </div>
            <br>

            <div> 
                @if (!$response && !$second_response && $first_comment)
                    {!! $first_comment !!}
                @endif
            </div>
            <br>
            
            <div>
                @if ($second_comment) {!! $second_comment !!} @endif
            </div>
            <br>

            @if ($incident_report['date_admin_hearing'] && $incident_report['meeting_place'])
            <div>
                An administrative hearing was conducted on
                    <b>{{ date_format(date_create($incident_report['date_admin_hearing']), "F d, Y")
                ." at ". $incident_report['meeting_place'] }}</b>
                which was attended by the members of the administrative committee namely:
            </div>
            <br>
            @endif

            @if ($mom || $incident_report['minutes_of_the_meeting_attachment_id'])
                <div>
                    After the said hearing, the committee have found out that:
                </div>
                <br>
                @if ($mom)
                    {!! $mom !!}
                @elseif ($incident_report['minutes_of_the_meeting_attachment_id'])
                    <p>
                        <b>Please see attach file</b>
                    </p>
                @endif
                <br>
            @endif

            <div>
                After careful review of available records and evidence including but not limited to the written
                explanation, it has been found out that there is substantial proof that you are liable for the offense
                stated below:
            </div>
            <br>

            <!-- Start Multiple Offense -->
            @if ($offense_multiple)
            <table class="table-border" >
                <tr class="table-border-tr" >
                    <td class="table-border-td" >
                        <b>Provision</b>
                    </td>
                    <td class="table-border-td" >
                        <b>Offense</b>
                    </td>
                    <td class="table-border-td" >
                        <b>Gravity</b>
                    </td>
                    @if ($offense_multiple_new)
                        <td class="table-border-td offenseTable" >
                            <b>Instance</b>
                        </td>
                        <td class="table-border-td offenseTable" >
                            <b>Corrective Action</b>
                        </td>
                    @elseif (!$offense_multiple_new)
                        <td class="table-border-td" style="padding: 10px;width: 20%">
                            @if($incident_report['final_instance'])
                                <b>Corrective Action ({{ $incident_report['final_instance'] }}) </b>
                            @else
                                <b>Corrective Action </b>
                            @endif
                        </td>
                    @endif
                </tr>
                @foreach ($offense_multiple as $off_multi)
                <tr class="table-border-tr" >
                    <td class="table-border-td" >
                        {{ $off_multi['category']['name'] }}
                    </td>
                    <td class="table-border-td">
                        {{ $off_multi['offense'] }}
                    </td>
                    <td class="table-border-td" >
                        {{ $off_multi['gravity']['gravity'] }}
                    </td>
                    @if ($incident_report['final_instance'] != null)
                        <td class="table-border-td offenseTable" >
                            {{ $off_multi['instance'] }}
                        </td>
                        <td class="table-border-td offenseTable" >
                            {{ $off_multi['irr'] }}
                        </td>
                    @elseif ($incident_report['final_instance'] == null)
                        <td class="table-border-td" style="padding: 10px;" >
                            {{ $incident_report['irr']['name'] }}
                        </td>
                    @endif
                </tr>
                @endforeach
            </table>
            <br>
            @endif
            <!-- End Multiple Offense -->

            <!-- Start Single Offense -->
            @if ($complainant['offense'] || $complainant['attendancepointssystem'])
            <table class="table-border">
                <tr class="table-border-tr">
                    <td class="table-border-td">
                        <b>Provision</b>
                    </td>
                    @if ($complainant['offense'])
                        <td class="table-border-td">
                            <b>Offense</b>
                        </td>
                        <td class="table-border-td" >
                            <b>Gravity</b>
                        </td>
                    @elseif ($complainant['attendancepointssystem'])
                        <td class="table-border-td">
                            <b>Type of Infraction</b>
                        </td>
                        <td class="table-border-td" >
                            <b>Attendance Points</b>
                        </td>
                    @endif
                    @if ($incident_report['final_instance'] != null)
                        <td class="table-border-td" >
                            <b>Corrective Action {{ ($incident_report['final_instance']) }}</b>
                        </td>
                    @else
                        <td class="table-border-td" >
                            <b>Corrective Action</b>
                        </td>
                    @endif
                </tr>
                <tr class="table-border-tr">
                    @if ($complainant['offense'])
                        <td class="table-border-td">
                            {{ $complainant['offense']['category']['name'] }}
                        </td>
                        <td class="table-border-td">
                            {{ $complainant['offense']['offense'] }}
                        </td>
                        <td class="table-border-td" >
                            {{ $complainant['offense']['gravity']['gravity'] }}
                        </td>

                    @elseif ($complainant['attendancepointssystem'])
                        <td class="table-border-td">
                            Attendance
                        </td>
                        <td class="table-border-td">
                            {{ $complainant['attendancepointssystem']['type_infraction'] }}
                        </td>
                        <td class="table-border-td" >
                            {{ $complainant['attendancepointssystem']['attendancepoint']['attendance_points'] }}
                        </td>
                    @endif
                    @if ($incident_report['final_instance'] != null)
                        <td class="table-border-td">
                            {{ $final_irr_single }}
                        </td>
                    @else
                        <td class="table-border-td">
                            {{ $incident_report['irr']['name'] }}
                        </td>
                    @endif
                </tr>
            </table>
            <br>
            @endif
            <!-- End Single Offense -->

            @if($incident_report['remarks'])
            <div>
                HR Remarks: {!! $incident_report['remarks'] !!}
            </div>
            <br>
            @endif

            @if ($incident_report['additional_notes'])
            <div>
                Additional Notes: {!! $incident_report['additional_notes'] !!}
            </div>
            <br>
            @endif

            <div>
                In addition, above circumstances can be a sufficient ground to impose administrative sanctions
                including termination under Article 297 of the Labor Code. An excerpt of which is provided below for
                your reference:
            </div>
            <br>

            <div>
                Art. 297. Termination by employer. An employer may terminate an employment for any of the following
                causes:

                @if ($terminable_art297) 
                    @foreach($terminable_art297 as $art297)
                        <div style="margin-left: 30px;">
                        @if ($art297 == "a")
                        a. Serious misconduct or wilful disobedience by the employee of the 
                        lawful orders of his employer or representative in connection with 
                        his work;<br>
                        @endif

                        @if ($art297 == "b")
                        b. Gross and habitual neglect by the employee of his duties;<br>
                        @endif

                        @if ($art297 == "c")
                        c. Fraud or wilful breach by the employee of the trust reposed in him by his employer or duly
                        authorized representative;<br>
                        @endif

                        @if ($art297 == "d")
                        d. Commission of a crime or offense by the employee against the 
                        person of his employer or any immediate member of his family or his 
                        duly authorized representatives; and<br>
                        @endif

                        @if ($art297 == "e")
                        e. Other analogous cases of the foregoing<br>
                        @endif
                        </div>
                    @endforeach
                @else
                <ol type="a">
                    <li>
                        Serious misconduct or wilful disobedience by the employee of the lawful orders of his employer
                        or representative in connection with his work;
                    </li>
                    <li>
                        Gross and habitual neglect by the employee of his duties;
                    </li>
                    <li>
                        Fraud or wilful breach by the employee of the trust reposed in him by his employer or duly
                        authorized representative;
                    </li>
                    <li>
                        Commission of a crime or offense by the employee against the person of his employer or any
                        immediate member of his family or his duly authorized representatives; and
                    </li>
                    <li>
                        Other analogous cases of the foregoing
                    </li>
                </ol>
                @endif
            </div>
            <br>

            <div>
                Furthermore, all other circumstances involving the 
                charge against you have been considered;
                and the grounds have been established to 
                justify the severance of your employment.
            </div>
            <br>

            <div>
                With the above findings, the management has come to a decision that you shall be warranted with
                <b><i>Termination of Employment</i></b> as behavioral action for violating the provision as indicated
                above and has considered this case closed.
            </div>
            <br>

            <div>
                Please ensure that you submit all your accountabilities and process your clearance upon receipt of
                this decision.
            </div>
            <br>

            <div>
                By signing your name below, you are acknowledging the contents of this document. Should you have
                clarifications, you may contact the HR department.
            </div>
            <br>

            <div>
                Please note that a copy of this memo will be filed on your 201 for future reference.
            </div>
            <br>

            <div>
                @include('pdf.signatory')
            </div>
        </div>
    </div>
</body>
</html>
