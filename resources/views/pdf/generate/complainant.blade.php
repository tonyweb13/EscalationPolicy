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
    hr.divider {
        border: 1px solid black;
        margin-bottom: 20px;
    }
    .table-border {
        border: 1px solid black;
        border-collapse: collapse;
        width:100%
    }
    .table-border, .table-border-tr, .table-border-td {
        border: 1px solid black;
    }
    .plain {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: justify;
        page-break-after:avoid;
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
    .pull-left {
        float: left!important;
    }
    .pull-right {
        float: right!important;
    }
    .mainContent {
        margin-right: 25px;
        margin-left: 25px;
        margin-bottom: -20px;
        font-size: 12px;
        page-break-after: avoid;
    }
    .agentresponse {
        word-wrap:break-word;
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
    @include('pdf.footer')
    <div class="mainContent">
        @include('pdf.header')
        <div class="title" style="margin-bottom: 20px;">
            <h3><u>INCIDENT REPORT DETAILS</u></h3>
        </div>
        <div class="content">
            <table class="table borderless" style="width: 100%;" >
                <tbody>
                    <tr>
                        <td>
                            <strong>IR Number : </strong> {{ $ir_number }}
                        </td>
                        <td>
                            <strong>Date Reported : </strong> {{ date('Y-m-d', strtotime($created_at)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if ($respondent['respondent_user']['id'] != $user_id)
                                    <strong>Complainant : </strong>
                                    <label>
                                        {{ $complainant['complainant_user']['first_name'] }}
                                        {{ $complainant['complainant_user']['last_name'] }}
                                    </label>
                            @endif
                        </td>
                        <td>
                            <strong>Date Incident : </strong>
                            {{ $complainant['date_incident_start'] }}
                            @if ($complainant['date_incident_end'])
                                <span >
                                    - {{ $complainant['date_incident_end'] }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Respondent : </strong> {{ $respondent_user['first_name'] }}
                            {{ $respondent_user['last_name'] }}
                        </td>
                        <td>
                            @if ($complainant['witness_user'])
                                <div>
                                    <strong>Witness : </strong>
                                    @foreach ($complainant['witness_user'] as $witnesses)
                                        <span >
                                            {{ $witnesses['witness_fullname']['first_name'] }}
                                            {{ $witnesses['witness_fullname']['last_name'] }}<br>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                    @if ($progression_occurence['first_offense'] || $progression_occurence['second_offense'])
                        <tr >
                            <td>
                                @if ($progression_occurence['first_offense'])
                                    <div >
                                        <strong>First Offense : </strong> {{ $progression_occurence['first_offense'] }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($progression_occurence['second_offense'])
                                    <div >
                                        <strong>Second Offense : </strong> {{ $progression_occurence['second_offense'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if ($progression_occurence['third_offense'] || $progression_occurence['fourth_offense'])
                        <tr >
                            <td>
                                @if ($progression_occurence['third_offense'])
                                    <div >
                                        <strong>Third Offense : </strong> {{ $progression_occurence['third_offense'] }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($progression_occurence['fourth_offense'])
                                    <div >
                                        <strong>Fourth Offense : </strong> {{ $progression_occurence['fourth_offense'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if ($progression_occurence['fifth_offense'] || $progression_occurence['sixth_offense'])
                        <tr >
                            <td>
                                @if ($progression_occurence['fifth_offense'])
                                    <div >
                                        <strong>Fifth Offense : </strong> {{ $progression_occurence['fifth_offense'] }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($progression_occurence['sixth_offense'])
                                    <div >
                                        <strong>Sixth Offense : </strong> {{ $progression_occurence['sixth_offense'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if ($progression_occurence['seventh_offense'])
                        <tr >
                            <td colspan="2">
                                @if ($progression_occurence['seventh_offense'])
                                    <div >
                                        <strong>Seventh Offense : </strong>
                                        {{ $progression_occurence['seventh_offense'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                    @foreach ($offense_multiple as $key => $off_multi)
                    <tr>
                        <td colspan="2">
                            <fieldset class="fieldsetCustom">
                                <legend class="legendCustom">Offense ( {{ $key+1 }} )</legend>
                                <table class="table borderless">
                                    <tr>
                                        <td colspan="2">
                                            <strong>Provision : </strong>
                                            {{ $off_multi['category']['name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <strong>Gravity : </strong>
                                            {{ $off_multi['gravity']['gravity'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <strong>Prescriptive Period : </strong>
                                            {{ $off_multi['gravity']['prescriptive_period'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <strong>Offense : </strong>
                                            {{ $off_multi['offense'] }}
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    @endforeach
                    <!-- End Multiple Offense -->

                    <!-- Start Single Offense -->
                    @if ($complainant['offense'] || $complainant['attendancepointssystem'])
                    <tr>
                        <td colspan="2">
                            <strong>Provision : </strong>
                            @if ($complainant['offense'])
                                {{ $complainant['offense']['category']['name'] }}
                            @elseif ($complainant['attendancepointssystem'])
                                Attendance
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Gravity : </strong>
                            @if ($complainant['offense'])
                                {{ $complainant['offense']['gravity']['gravity'] }}
                            @elseif ($complainant['attendancepointssystem'])
                                Attendance Points -
                                {{ $complainant['attendancepointssystem']['attendancepoint']['attendance_points'] }}
                            @endif
                        </td>
                        <td>
                            <strong>Prescriptive Period : </strong>
                            @if ($complainant['offense'])
                                {{ $complainant['offense']['gravity']['prescriptive_period'] }}
                            @elseif ($complainant['attendancepointssystem'])

                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Offense : </strong>
                            @if ($complainant['offense'])
                                {{ $complainant['offense']['offense'] }}
                            @elseif ($complainant['attendancepointssystem'])
                                {{ $complainant['attendancepointssystem']['type_infraction'] }}
                            @endif
                        </td>
                    </tr>
                    @endif
                    <!-- End Single Offense -->

                    @if ($complainant['offense']['description'] || $complainant['attendancepointssystem']['definition'])
                        <tr >
                            <td colspan="2">
                                <strong>IR Description : </strong><br>
                                    @if ($complainant['offense'])
                                        {!! $complainant['offense']['description'] !!}
                                    @elseif ($complainant['attendancepointssystem'])
                                        {!! $complainant['attendancepointssystem']['definition'] !!}
                                    @endif
                            </td>
                        </tr>
                    @endif
                    @if ($complainant['remarks'])
                        <tr >
                            <td colspan="2">
                                <strong>Remarks : </strong><br>
                                {{ $complainant['remarks'] }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <strong>Complainant Description : </strong><br>
            <div>
                {!! $complainant['description'] !!}
            </div>
            @if ($response)
                <br>

                @if ($response == "Will not further explain" && $second_response)

                    <strong>Second Date Response : </strong> {{ $second_response_date }} <br>
                    <strong>Second Respondent Reply : </strong><br>
                    <div class="agentresponse">
                        {!! $second_response !!}
                    </div>

                @else

                    <strong>Date Response : </strong> {{ $date_response }} <br>
                    <strong>Respondent Reply : </strong><br>
                    <div class="agentresponse">
                        {!! $response !!}
                    </div>
                    {{ $respondent_response }}

                @endif

                @if ($hrbp_date_acknowledge)
                    <br>
                    <strong>HR Date Acknowledge : </strong><br>
                    {{ date('Y-m-d', strtotime($hrbp_date_acknowledge)) }}
                @endif
            @endif

            @if ($complainant_images)
                @foreach ($complainant_images as $key => $complainant_image)
                    @if ($overWidth[$key])
                        <div class="clipzone" >
                            <img src="{{ public_path($complainant_image) }}" width="700px"/>
                        </div>
                    @elseif ($overHeight[$key])
                        <div class="clipzone" >
                            <img src="{{ public_path($complainant_image) }}" width="700px" class="imgdiv" />
                        </div>
                        <div class="clipzone2" >
                            <img src="{{ public_path($complainant_image) }}" width="700px"/>
                            <br /><br />
                        </div>
                    @else
                        <span>
                            <img src="{{ public_path($complainant_image) }}" width="700px" />
                        </span>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>
