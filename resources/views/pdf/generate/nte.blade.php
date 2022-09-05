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
        width:100%;
        text-align:center;
    }
    .table-border, .table-border-tr, .table-border-td {
        border: 1px solid black;
        padding: 10px;
    }
    .header td {
        text-align: center;
    }
    #watermark {
        position: fixed;
        bottom: 0px;
        right: 0px;

        /** The width and height may change
            according to the dimensions of your letterhead
        **/
        width:    20cm;
        height:   25cm;

        /** Your watermark should be behind every content**/
        z-index:  -1010;
    }
    .offenseTable {
        padding: 10px;
    }
    .imgdiv {
        position: absolute;
    }
    .clipzone2 {
        width: 100%;
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
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('img/bgconfidential.png') }}" height="100%" width="100%" />
    </div>
    @include('pdf.header')
    @include('pdf.footer')

    <div style="margin-right: 25px; margin-left: 25px; margin-bottom: -20px; font-size: 12px;">
        <div class="title">
            <h3><u>
                @if ($incident_report['is_generate_nte_invalid_ir'] == 4
                    && $incident_report['date_admin_hearing']
                    || $incident_report['is_generate_nte_invalid_ir'] == 4
                    && $incident_report['time_admin_hearing']
                    || $incident_report['is_generate_nte_invalid_ir'] == 4
                    && $incident_report['meeting_place'])
                    NOTICE TO EXPLAIN WITH PREVENTIVE SUSPENSION <br>
                    AND ADMINISTRATIVE HEARING
                @elseif ($incident_report['is_generate_nte_invalid_ir'] == 1
                    && $incident_report['date_admin_hearing']
                    || $incident_report['is_generate_nte_invalid_ir'] == 1
                    && $incident_report['time_admin_hearing']
                    || $incident_report['is_generate_nte_invalid_ir'] == 1
                    && $incident_report['meeting_place'])
                    NOTICE TO EXPLAIN WITH ADMINISTRATIVE HEARING
                @else
                    NOTICE TO EXPLAIN
                @endif
            </u></h3>
        </div>
        <div class="content">
            <table style="font-weight:bold; text-align:left; margin-top:20px;">
                <tr >
                    <td >DATE</td>
                    <td> : </td>
                    <td colspan="2">{{ date_format(date_create($incident_report['date_nte_serve']), "F d, Y") }}</td>
                </div>
                <tr>
                    <td><strong>EMPLOYEE NAME</strong></td>
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

            <div class="plain">
                We received an incident report dated
                <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                that you allegedly violated the Company Code of Conduct specifically on
                @if ($complainant['offense'])
                    <b>{{ $complainant['offense']['category']['name'] }}</b>.
                @elseif ($complainant['attendancepointssystem'])
                    <b>Attendance</b>.
                @endif
                See attached incident report.
            </div>

            <div class="plain">
                This constitutes to a breach of the Code of Conduct to wit:
            </div>

            <!-- Start Multiple Offense -->
            @if ($offense_multiple)
            <table class="table-border offenseTable" >
                <tr class="table-border-tr offenseTable header" >
                    <td class="table-border-td offenseTable" >
                        <b>Provision</b>
                    </td>
                    <td class="table-border-td offenseTable" >
                        <b>Offense</b>
                    </td>
                    <td class="table-border-td offenseTable" >
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
                        <td class="table-border-td offenseTable" >
                            <b>Corrective Action ({{ $incident_report['initial_instance'] }})</b>
                        </td>
                    @endif
                </tr>
                @foreach ($offense_multiple as $off_multi)
                <tr class="table-border-tr offenseTable" >
                    <td class="table-border-td offenseTable" >
                        {{ $off_multi['category']['name'] }}
                    </td>
                    <td class="table-border-td offenseTable" style="font-size:10px">
                        {{ $off_multi['offense'] }}
                    </td>
                    <td class="table-border-td offenseTable" >
                        {{ $off_multi['gravity']['gravity'] }}
                    </td>
                    @if ($off_multi['instance'] && $off_multi['irr'])
                        <td class="table-border-td offenseTable" >
                            {{ $off_multi['instance'] }}
                        </td>
                        <td class="table-border-td offenseTable" >
                            {{ $off_multi['irr'] }}
                        </td>
                    @elseif ($incident_report['initial_irr']['name'] != null)
                        <td class="table-border-td offenseTable" >
                            {{ $incident_report['initial_irr']['name'] }}
                        </td>
                    @endif
                </tr>
                @endforeach
            </table>
            @endif
            <!-- End Multiple Offense -->

            <!-- Start Single Offense -->
            @if ($complainant['offense'] || $complainant['attendancepointssystem'])
            <table class="table-border offenseTable" >
                <tr class="table-border-tr offenseTable header" >
                    <td class="table-border-td offenseTable" >
                        <b>Provision</b>
                    </td>
                    @if ($complainant['offense'])
                        <td class="table-border-td offenseTable" >
                            <b>Offense</b>
                        </td>
                        <td class="table-border-td offenseTable" >
                            <b>Gravity</b>
                        </td>
                    @elseif ($complainant['attendancepointssystem'])
                        <td class="table-border-td offenseTable" >
                            <b>Type of Infraction</b>
                        </td>
                        <td class="table-border-td offenseTable" >
                            <b>Attendance Points</b>
                        </td>
                    @endif
                    @if ($incident_report['initial_instance'] != null)
                        <td class="table-border-td offenseTable" >
                            <b>Corrective Action ({{ $incident_report['initial_instance'] }})</b>
                        </td>
                    @endif
                </tr>
                <tr class="table-border-tr offenseTable" >
                    @if ($complainant['offense'])
                        @if ($long_offense_char)
                            <td class="table-border-td offenseTable" >
                                {{ $complainant['offense']['category']['name'] }}
                            </td>
                            <td class="table-border-td offenseTable" style="font-size:10px">
                                {{ $complainant['offense']['offense'] }}
                            </td>
                            <td class="table-border-td offenseTable" >
                                {{ $complainant['offense']['gravity']['gravity'] }}
                            </td>
                        @else
                            <td class="table-border-td  offenseTable" >
                                {{ $complainant['offense']['category']['name'] }}
                            </td>
                            <td class="table-border-td offenseTable" >
                                {{ $complainant['offense']['offense'] }}
                            </td>
                            <td class="table-border-td offenseTable" >
                                {{ $complainant['offense']['gravity']['gravity'] }}
                            </td>
                        @endif
                    @elseif ($complainant['attendancepointssystem'])
                        <td class="table-border-td offenseTable" >
                            Attendance
                        </td>
                        <td class="table-border-td offenseTable" >
                            {{ $complainant['attendancepointssystem']['type_infraction'] }}
                        </td>
                        <td class="table-border-td offenseTable" >
                            {{ $complainant['attendancepointssystem']['attendancepoint']['attendance_points'] }}
                        </td>
                    @endif
                    @if ($incident_report['initial_irr']['name'] != null)
                        <td class="table-border-td offenseTable" >
                            {{ $incident_report['initial_irr']['name'] }}
                        </td>
                    @endif
                </tr>

            </table>
            @endif
            <!-- End Single Offense -->

            <div class="plain">
                You are hereby required to explain within five (5) calendar days from receipt of this memorandum why no
                behavioral action including but not limited to dismissal from employment, should be imposed upon you
                based on the following ground.
            </div>

            <div class="plain">
                Failure on your part to answer the charges against you within the time we have given you will mean a
                waiver of the right to be heard, and we shall resolve the matter based on the evidence on record.
                Management will then evaluate on the case on hand based on the facts and advise you accordingly on the
                final resolution of this case.
            </div>

            @if ($incident_report['is_generate_nte_invalid_ir'] == 4
                && $incident_report['date_admin_hearing']
                || $incident_report['is_generate_nte_invalid_ir'] == 4
                && $incident_report['time_admin_hearing']
                || $incident_report['is_generate_nte_invalid_ir'] == 4
                && $incident_report['meeting_place'])

                <div class="plain">
                    In view of the foregoing, please be advised that you shall be placed under
                    <b>PREVENTIVE SUSPENSION</b> for a period of thirty (30) calendar days from

                    @if ($incident_report['preventive_suspension_start'])
                        <b>{{ date_format(date_create($incident_report['preventive_suspension_start']), "F d, Y") }}</b>
                    @else
                        <b>{{ date_format(date_create($incident_report['date_admin_hearing']), "F d, Y") }}</b>
                    @endif

                    until

                    @if ($incident_report['preventive_suspension_end'])
                        <b>{{ date_format(date_create($incident_report['preventive_suspension_end']), "F d, Y") }}</b>
                    @else
                        <b>{{ date("F d, Y", strtotime('+30 days', strtotime($incident_report['date_admin_hearing']))) }}</b>
                    @endif

                    during the course of this investigation and shall not be entitled to
                    any compensation. However, if after the investigation, the company sees that the allegation against
                    you is unjustifiable and/or without grounds, you shall be entitled to full compensation of the days
                    you were put under suspension. Please be advised that the said Preventive Suspension is not to be
                    considered as the sanction for the case, but an intermediate measure implemented by the company as
                    a preemptive step to protect its employees and/or business.
                </div>

                <div class="plain">
                    Furthermore, we are scheduling you for an Administrative Hearing on
                    <b>{{ date_format(date_create($incident_report['date_admin_hearing']), "F d, Y") }}</b>
                    <b>{{ $incident_report['time_admin_hearing'] }} at {{ $incident_report['meeting_place'] }}</b>.
                    This hearing is called to enlighten the management regarding the investigation being conducted in
                    view of your alleged violation and will give you a chance to confront the allegations against you
                    as well as to give you an opportunity to present evidence on your behalf. You may bring witness/es
                    or counsel should you wish to do so. Your presence at the said hearing is crucial to the final
                    determination of this case and your cooperation is earnestly requested.
                </div>
            @elseif ($incident_report['is_generate_nte_invalid_ir'] == 1
                && $incident_report['date_admin_hearing']
                || $incident_report['is_generate_nte_invalid_ir'] == 1
                && $incident_report['time_admin_hearing']
                || $incident_report['is_generate_nte_invalid_ir'] == 1
                && $incident_report['meeting_place'])

                <div class="plain">
                    Furthermore, we are scheduling you for an Administrative Hearing on
                    <b>{{ date_format(date_create($incident_report['date_admin_hearing']), "F d, Y") }}</b>
                    <b>{{ $incident_report['time_admin_hearing'] }} at {{ $incident_report['meeting_place'] }}</b>.
                    This hearing is called to enlighten the management regarding the investigation being conducted in
                    view of your alleged violation and will give you a chance to confront the allegations against you
                    as well as to give you an opportunity to present evidence on your behalf. You may bring witness/es
                    or counsel should you wish to do so. Your presence at the said hearing is crucial to the final
                    determination of this case and your cooperation is earnestly requested.
                </div>
            @endif

            @if ($terminable || $terminable[0])

                <div class="plain">
                    In addition, above circumstances can be a sufficient ground to impose administrative sanctions
                    including termination under Article 297 of the Labor Code. An excerpt of which is provided below for
                    your reference:
                </div>

                <div class="plain">
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

            @endif

            <div class="plain">
                By signing your name below, you are acknowledging the contents of this document. Should you have
                clarifications, you may contact the HR department.
            </div>

            <div class="plain">
                Please note that a copy of this memo will be filed on your 201 for future reference.
            </div>

            <div class="plain" style="margin-top:20px;">
                <b>FOR YOUR STRICT COMPLIANCE.</b>
            </div>
            <div class="plain" style="margin-top:20px;">
                @include('pdf.signatory')
            </div>
        </div>
    </div>
</body>
</html>
