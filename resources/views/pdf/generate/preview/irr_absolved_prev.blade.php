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
                <a class="btn btn-success" href="{{ route('preview_da.edit', $incident_report['id']) }}">
                <i class="fa fa-pencil"></i> Edit DA</a>
                <button type="button" class="btn btn-info" onclick="window.location.href=
                '/api/admin/incidentreport/generate/irr/{{ $complainant_id }}/{{ $respondent_user_id }}';">
                    <i class="fa fa-download"></i> Download DA
                </button>
            </div>
        </div>

        <div class="mainContent">
            <div class="title">
                <h3><u>CASE CLOSURE</u></h3>
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
                <br>

                <div>
                    Dear Mr./Ms. {{ $respondent_user['first_name'].' '.$respondent_user['last_name'] }},
                </div>
                <br>

                <div>
                    This is in relation to the Notice to Explain memo dated
                        <b>{{ date_format(date_create($complainant['created_at']), "F d, Y") }}</b>
                    wherein you allegedly committed an infraction under the provision of
                    @if ($offense_multiple)
                        @foreach ($offense_multiple as $off_multi)
                            <span style="margin-right:5px;"><b>{{ $off_multi['category']['name'] }}
                            </b>,</span>
                        @endforeach
                    @elseif ($complainant['offense'])
                        {{ $complainant['offense']['category']['name'] }}.
                    @elseif ($complainant['attendancepointssystem'])
                        Attendance.
                    @endif
                    dated <b>{{ date_format(date_create($incident_report['created_at']), "F d, Y")  }}</b>
                </div>
                <br>

                @if ($incident_report['additional_notes'])
                <div>
                    Additional Notes: {!! $incident_report['additional_notes'] !!}
                </div>
                <br>
                @endif

                <div>
                    After careful and exhaustive deliberation, it has been found that there is no 
                    substantial evidence to prove that you deliberately commit the alleged infraction.
                </div>
                <br>

                <div>
                    All other causes of action and claims are hereby DISMISSED for lack of merit 
                    and/or failure to substantiate. While we understand your explanation and the 
                    situation, any valid occurrence in the future of the same or similar offense 
                    will merit behavioral action up to and including termination of your employment.
                </div>
                <br>

                <div>
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
                    Please be aware that a copy of this memo will be filed on your 201 for future 
                    reference. Should you have clarifications, you may contact the HR department.
                </div>
                <br>

                <div>
                    @include('pdf.signatory')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
