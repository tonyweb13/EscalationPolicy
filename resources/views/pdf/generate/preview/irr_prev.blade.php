<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <link href="{{ asset('css/public.css') }}" rel="stylesheet">
</head>
<body style="background-color: #525659;">
    <div class="mainPreview">
        <div class="col-lg-12" style="padding-top: 3%; padding-bottom: 3%;">
            @if ($message = Session::get('success'))
                <div class="alert alert-success" style="text-align: center;">
                    <b>{{ $message }}</b>
                </div>
            @endif
            <h3 class="pull-left" style="color:red; margin-top: 2px;">Preview IR:#{{ $ir_number }}</h3>
            <div class="pull-right">
                <a class="btn btn-warning" href="/irhistory"><i class="fa fa-backward"></i> Back</a>
                <a class="btn btn-success" href="{{ route('preview_da.edit', $incident_report['id']) }}"><i class="fa fa-pencil"></i> Edit DA</a>
                <button type="button" class="btn btn-info" 
                onclick="window.location.href='/api/admin/incidentreport/generate/irr/{{ $complainant_id }}/{{ $respondent_user_id }}';">
                    <i class="fa fa-download"></i> Download DA
                </button>
            </div>
        </div>
        <div class="mainContent">
            <div class="title">
                <h3><u>IMPLEMENTATION OF DISCIPLINARY ACTION</u></h3>
            </div>
            <div>
                <table style="font-weight:bold; margin-bottom: 25px;" class="col-lg-12">
                    <tr >
                        <td style="width: 17%;">DATE</td>
                        <td> : </td>
                        <td colspan="2">
                            {{ date_format(date_create($incident_report['date_da']), "F d, Y") }} 
                            <small style="color:red;">[editable]</small>
                        </td>
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
                        <img src="{{ '/'.$file_location }}" height="120px" width="690" /><br><br>
                        @endforeach
                    </div>
                @endif
                <br>

                <div>
                    @if (($response == "Will not further explain" || $response == "") && $second_response == null)
                        The management has given you five (5) business days to revert with a reply but you failed to do so.
                        Therefore, you have waived your right to be heard in the proceedings of this case.
                    @elseif ($response && $second_response == null)
                        <div>
                            {!! trim(preg_replace( "/[\r\n]+/", "\n", $response ))  !!}
                        </div>
                        <br>
                        <br>
                        Based on the evidence at hand and after investigation, we find your explanation insufficient to
                        clear you of the charges against you for violations of the Code of Conduct/Company policy.
                    @elseif ($response == "Will not further explain" && $second_response != null)
                            <div>
                                {!! trim(preg_replace( "/[\r\n]+/", "\n", $second_response ))  !!}
                            </div>
                            Based on the evidence at hand and after investigation, we find your explanation insufficient to
                            clear you of the charges against you for violations of the Code of Conduct/Company policy.
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
                                <small style="color:red;">[editable]</small>
                            </td>
                            <td class="table-border-td offenseTable" style="width: 15%;" >
                                <b>Corrective Action</b>
                                <small style="color:red;">[editable]</small>
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
                            <td class="table-border-td" style="width: 15%;">
                                <b>Corrective Action ({{ $incident_report['final_instance'] }})</b>
                                <small style="color:red;">[editable]</small>
                            </td>
                        @else
                            <td class="table-border-td" style="width: 20%;">
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
                                    <small style="color:red;">[editable]</small>
                                </td>
                            @else
                                <td class="table-border-td">
                                    {{ $incident_report['irr']['name'] }}
                                    <small style="color:red;">[editable]</small>
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
                                        <small style="color:red;">[editable]</small>
                                    </td>
                                @else
                                    <td class="table-border-td">
                                        {{ $incident_report['irr']['name'] }}
                                        <small style="color:red;">[editable]</small>
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
                                <small style="color:red;">[editable]</small>
                            </td>
                        @else
                            <td class="table-border-td">
                                {{ $incident_report['irr']['name'] }}
                                <small style="color:red;">[editable]</small>
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
                    <small style="color:red;">[editable]</small>
                    @if ($in_lieu)
                        <b>in lieu with Termination of Employment</b>
                    @endif
                    
                    effective

                    @if ($suspension_date_multi_count > 0)
                        <b>{{ $suspension_date_multi  }}</b>
                        <small style="color:red;">[editable]</small>
                    @elseif ($incident_report['suspension_date'])
                        <b>{{ date_format(date_create($incident_report['suspension_date']), "F d, Y") }}</b>
                        <small style="color:red;">[editable]</small>
                    @else
                        immediately.
                    @endif
                </div>
                <br>

                <div>
                    The management strongly warns you to desist from such acts in the future. 
                    Please note that any further occurrences of such acts shall be viewed strictly 
                    and may lead to recommendation for Dismissal. By signing your name below, you 
                    are acknowledging the contents of this letter.
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
                    Please be aware that a copy of this memo will be filed on your 201 for future 
                    reference. Should you have clarifications, you may contact the HR department.
                </div>
                <br>

            </div>
            <div>
                @include('pdf.signatory')
            </div>
        </div>
    </div>
</body>
</html>
