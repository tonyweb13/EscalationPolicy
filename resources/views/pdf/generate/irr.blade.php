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
        page-break-after:avoid;

        /* opacity: .1; */
        width:    20cm;
        height:   28cm;

        /** Your watermark should be behind every content**/
        z-index:  -1;
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
            <h3><u>IMPLEMENTATION OF DISCIPLINARY ACTION</u></h3>
        </div>
        <div>
            <table style="font-weight:bold;">
                <tr >
                    <td>DATE</td>
                    <td> : </td>
                    <td colspan="2">{{ date_format(date_create($incident_report['date_da']), "F d, Y") }}</td>
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
                This refers to the Notice to Explain memo dated
                    <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                which you have signed and acknowledged on
                @if ($email_acknowledge_date)
                    <b>{{ date_format(date_create($email_acknowledge_date), "F d, Y") }}</b>
                @elseif ($second_response_date)
                    <b>{{ date_format(date_create($second_response_date), "F d, Y") }}</b>
                @elseif ($date_response)
                    <b>{{ date_format(date_create($date_response), "F d, Y") }}</b>
                @else
                    <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                @endif
                regarding the

                @if ($offense_multiple)
                    @foreach ($offense_multiple as $off_multi)
                        <span style="margin-right:5px;"><b>{{ $off_multi['category']['name'] }}</b>,</span>
                    @endforeach
                @elseif ($complainant['offense'])
                    <b>{{ $complainant['offense']['category']['name'] }}.</b>
                @elseif ($complainant['attendancepointssystem'])
                    <b>Attendance.</b>
                @endif
            </div>

            @if ($response && $response != "Will not further explain")
                <br>
                <div>
                    In your written response dated
                    @if ($date_response == null)
                        <b>{{ date_format(date_create($updated_at), "F d, Y") }}</b>
                    @else
                        <b>{{ date_format(date_create($date_response), "F d, Y") }}</b>
                    @endif
                    which we received on
                        <b>{{ date_format(date_create($incident_report['updated_at']), "F d, Y") }}</b>,
                    you mentioned that:
                </div>
            @elseif ($second_response)
                <div>
                    In your written response dated
                    @if ($second_response_date == null)
                        <b>{{ date_format(date_create($updated_at), "F d, Y") }}</b>
                    @else
                        <b>{{ date_format(date_create($second_response_date), "F d, Y") }}</b>
                    @endif
                    which we received on
                        @if ($hrbp_date_acknowledge)
                            <b>{{ date_format(date_create($hrbp_date_acknowledge), "F d, Y") }}</b>,
                        @elseif ($response && $response != "Will not further explain")
                            @if ($hrbp_date_acknowledge)
                                <b>{{ date_format(date_create($hrbp_date_acknowledge), "F d, Y") }}</b>,
                            @elseif ($date_response == null)
                                <b>{{ date_format(date_create($updated_at), "F d, Y") }}</b>
                            @else
                                <b>{{ date_format(date_create($date_response), "F d, Y") }}</b>
                            @endif
                        @elseif ($second_response)
                            @if ($hrbp_date_acknowledge)
                                <b>{{ date_format(date_create($hrbp_date_acknowledge), "F d, Y") }}</b>,
                            @elseif ($second_response_date == null)
                                <b>{{ date_format(date_create($updated_at), "F d, Y") }}</b>
                            @else
                                <b>{{ date_format(date_create($second_response_date), "F d, Y") }}</b>
                            @endif
                        @endif
                    you mentioned that:
                </div>
            @endif

            @if ($complainant->is_attachment_tk_image)
                <div>
                    @foreach ($file_image as $file_location)
                    <img src="{{ public_path($file_location) }}" height="120px" width="690" /><br><br>
                    @endforeach
                </div>
            @endif
            <br>

            <div>
                @if (($response == "Will not further explain" || $response == "") 
                    && $second_response == null)
                    The management has given you five (5) business days to revert with a reply 
                    but you failed to do so. Therefore, you have waived your right to be heard in 
                    the proceedings of this case.
                @elseif ($response && $second_response == null)
                    <div>
                        {!! trim(preg_replace( "/[\r\n]+/", "\n", $response ))  !!}
                    </div>
                    <br>
                    <br>
                    Based on the evidence at hand and after investigation, we find your 
                    explanation insufficient to clear you of the charges against you for violations 
                    of the Code of Conduct/Company policy.
                @elseif ($response == "Will not further explain" && $second_response != null)
                        <div>
                            {!! trim(preg_replace( "/[\r\n]+/", "\n", $second_response ))  !!}
                        </div>
                        Based on the evidence at hand and after investigation, we find your 
                        explanation insufficient to clear you of the charges against you for 
                        violations of the Code of Conduct/Company policy.
                @endif
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
            <table class="table-border" >
                <tr class="table-border-tr">
                    <td class="table-border-td">
                        <b>Provision</b>
                    </td>
                    @if ($complainant['offense'])
                        <td class="table-border-td"  >
                            <b>Offense</b>
                        </td>
                        <td class="table-border-td">
                            <b>Gravity</b>
                        </td>
                    @elseif ($complainant['attendancepointssystem'])
                        <td class="table-border-td" >
                            <b>Type of Infraction</b>
                        </td>
                        <td class="table-border-td" >
                            <b>Attendance Points</b>
                        </td>
                    @endif
                    @if ($incident_report['final_instance'] != null)
                        <td class="table-border-td">
                            <b>Corrective Action ({{ $incident_report['final_instance'] }})</b>
                        </td>
                    @else
                        <td class="table-border-td">
                            <b>Corrective Action</b>
                        </td>
                    @endif
                </tr>
                @if ($complainant['offense'])
                    @if ($long_offense_char)
                    <tr class="table-border-tr" >
                        <td class="table-border-td" >
                            {{ $complainant['offense']['category']['name'] }}
                        </td>
                        <td class="table-border-td">
                            {{ $complainant['offense']['offense'] }}
                        </td>
                        <td class="table-border-td" >
                            {{ $complainant['offense']['gravity']['gravity'] }}
                        </td>
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
                    @else
                        <tr class="table-border-tr" >
                            <td class="table-border-td" >
                                {{ $complainant['offense']['category']['name'] }}
                            </td>
                            <td class="table-border-td" >
                                {{ $complainant['offense']['offense'] }}
                            </td>
                            <td class="table-border-td" >
                                {{ $complainant['offense']['gravity']['gravity'] }}
                            </td>
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
                    @endif

                @elseif ($complainant['attendancepointssystem'])
                <tr class="table-border-tr" >
                    <td class="table-border-td" >
                        Attendance
                    </td>
                    <td class="table-border-td" >
                        {{ $complainant['attendancepointssystem']['type_infraction'] }}
                    </td>
                    <td class="table-border-td"  >
                        {{ $complainant['attendancepointssystem']['attendancepoint']['attendance_points'] }}
                    </td>
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
                @endif
            </table>
            <br>
            @endif

            @if ($incident_report['additional_notes'])
            <div>
                Additional Notes: {!! $incident_report['additional_notes'] !!}
            </div>
            <br>
            @endif

            <div>
                In view of the foregoing, you are hereby given
                <b>{{ $incident_report['irr']['name'] }}</b>
                @if ($in_lieu)
                    <b>in lieu with Termination of Employment</b>
                @endif

                effective

                @if ($suspension_date_multi_count > 0)
                        <b>{{ $suspension_date_multi  }}</b>
                @elseif ($incident_report['suspension_date'])
                    <b>{{ date_format(date_create($incident_report['suspension_date']), "F d, Y") }}</b>
                @else
                    immediately.
                @endif
            </div>
            <br>

            <div>
                The management strongly warns you to desist from such acts in the future. Please note that any further
                occurrences of such acts shall be viewed strictly and may lead to recommendation for Dismissal.
                By signing your name below, you are acknowledging the contents of this letter.
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
                Please be aware that a copy of this memo will be filed on your 201 for future reference. Should you
                have clarifications, you may contact the HR department.
            </div>
            <br>

        </div>
        <div>
            @include('pdf.signatory')
        </div>
    </div>
</body>
</html>
