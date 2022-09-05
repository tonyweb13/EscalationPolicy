<div >
    <table style="width:100%; text-align:left; font-weight:bold; page-break-after:avoid;">
        <tr>
            <td>Prepared by :</td>

            @if ($requested_by_input || $requested_by_supervisor || $requested_by_manager || $requested_by_coo
            || $requested_by_head_uw || $requested_by_som)
                <td>Requested by :</td>
            @endif
        </tr>
        <tr>
            <td>&nbsp;</td>

            @if ($requested_by_input || $requested_by_supervisor || $requested_by_manager || $requested_by_coo
            || $requested_by_head_uw || $requested_by_som)
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr>
            <td>__________________________________</td>

            @if ($requested_by_input || $requested_by_supervisor || $requested_by_manager || $requested_by_coo
            || $requested_by_head_uw || $requested_by_som)
                <td>__________________________________</td>
            @endif
        </tr>
        <tr>

            @if ($prepared_by_input)
            <td>
                {{ $prepared_by_input_fullname }}
            </td>
            @elseif ($prepared_by_hr_manager)
            <td>
                {{ $hr_manager_fullname }}
            </td>
            @elseif ($prepared_by_hrsl)
            <td>
                {{ $hrsl_user['first_name'] }} {{ $hrsl_user['last_name'] }}
            </td>
            @else
            <td>
                {{ $incident_report['hrbp_user']['first_name'] }} {{ $incident_report['hrbp_user']['last_name'] }}
            </td>
            @endif

            @if ($requested_by_input)
                <td>{{ $requested_by_input_fullname }}</td>
            @elseif ($requested_by_coo)
                <td>{{ $coo_fullname }}</td>
            @elseif ($requested_by_head_uw)
                <td> {{ $uw_head_fullname }}</td>
            @elseif ($requested_by_som)
                <td> {{ $som_fullname }}</td>
            @elseif ($requested_by_supervisor || $requested_by_manager)
                <td>
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['first_name'] }}
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['last_name'] }}
                </td>
            @endif
        </tr>
        <tr>
            @if ($prepared_by_input)
            <td>
                {{ $prepared_by_input_designation }}
            </td>
            @elseif ($prepared_by_hr_manager)
            <td>
                {{ $hr_manager_designation }}
            </td>
            @elseif ($prepared_by_hrsl)
            <td>
                {{ $hrsl_user['designation']['name'] }}
            </td>
            @else
                <td>{{ $incident_report['hrbp_user']['designation']['name'] }}</td>
            @endif

            @if ($requested_by_input)
                <td>{{ $requested_by_input_designation }}</td>
            @elseif ($requested_by_coo)
                <td>{{ $coo_designation }}</td>
            @elseif ($requested_by_head_uw)
                <td> {{ $uw_head_designation }}</td>
            @elseif ($requested_by_som)
                <td> {{ $som_designation }}</td>
            @elseif ($requested_by_supervisor || $requested_by_manager)
                <td>{{ $respondent_user['employee_supervisor']['supervisor_assign']['designation']['name'] }}</td>
            @endif
        </tr>
        <tr>
            @if ($prepared_by_input)
                <td>Emp No: {{ $prepared_by_input_empno }}</td>
            @elseif ($prepared_by_hr_manager)
                <td>Emp No: {{ $hr_manager_employee_no }}</td>
            @elseif ($prepared_by_hrsl)
                <td>Emp No: {{ $hrsl_user['employee_no'] }}</td>
            @else
                <td>Emp No: {{ $incident_report['hrbp_user']['employee_no'] }}</td>
            @endif

            @if ($requested_by_input)
                <td>Emp No: {{ $requested_by_input_empno }}</td>
            @elseif ($requested_by_coo)
                <td>Emp No: {{ $coo_employee_no }}</td>
            @elseif ($requested_by_head_uw)
                <td>Emp No: {{ $uw_head_employee_no }}</td>
            @elseif ($requested_by_som)
                <td> {{ $som_employee_no }}</td>
            @elseif ($requested_by_supervisor || $requested_by_manager)
                <td>Emp No: {{ $respondent_user['employee_supervisor']['supervisor_assign']['employee_no'] }}</td>
            @endif
        </tr>
        <tr>
            <td>Date Signed:_______________________</td>
            @if ($requested_by_input || $requested_by_supervisor || $requested_by_manager || $requested_by_coo
            || $requested_by_head_uw || $requested_by_som)
                <td>Date Signed:_______________________</td>
            @endif
        </tr>
    </table>

    <table style="width:100%; text-align:left; font-weight:bold; margin-top:20px; page-break-after:avoid;">
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input || $approved_by_coo || $approved_by_som || $approved_by_head_uw || $approved_by_gm || ($approved_by_manager
            && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']))
                <td>Approved by :</td>
            @endif


            <td>Received by :</td>
        </tr>
        <tr>
            @if ($approved_by_input || $approved_by_coo || $approved_by_som || $approved_by_head_uw || $approved_by_gm || ($approved_by_manager
            && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']))
                <td>&nbsp;</td>
            @endif

            <td>&nbsp;</td>
        </tr>
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input || $approved_by_coo || $approved_by_som || $approved_by_head_uw || $approved_by_gm || ($approved_by_manager
            && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']))
                <td>__________________________________</td>
            @endif

            <td>__________________________________</td>
        </tr>
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input)
                <td>{{ $approved_by_input_fullname }}</td>
            @elseif ($approved_by_coo)
                <td>{{ $coo_fullname }}</td>
            @elseif ($approved_by_head_uw)
                <td>{{ $uw_head_fullname }}</td>
            @elseif ($approved_by_som)
                <td>{{ $som_fullname }}</td>
            @elseif ($approved_by_gm)
                <td>{{ $gm_fullname }}</td>
            @elseif ($approved_by_manager &&
            $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign'])
                <td>
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']['first_name'] }}
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']['last_name'] }}
                </td>
            @endif

            <td>{{ $respondent_user['first_name'] }} {{ $respondent_user['last_name'] }} </td>
        </tr>
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input)
                <td>{{ $approved_by_input_designation }}</td>
            @elseif ($approved_by_coo)
                <td>{{ $coo_designation }}</td>
            @elseif ($approved_by_head_uw)
                <td>{{ $uw_head_designation }}</td>
            @elseif ($approved_by_som)
                <td>{{ $som_designation }}</td>
            @elseif ($approved_by_gm)
                <td>{{ $gm_designation }}</td>
            @elseif ($approved_by_manager && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign'])
                <td>
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']['designation']['name'] }}
                </td>
            @endif

            <td>{{ $respondent_user['designation']['name'] }}</td>
        </tr>
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input)
                <td> Emp No: {{ $approved_by_input_empno }}</td>
            @elseif ($approved_by_coo)
                <td> Emp No: {{ $coo_employee_no }}</td>
            @elseif ($approved_by_head_uw)
                <td> Emp No: {{ $uw_head_employee_no }}</td>
            @elseif ($approved_by_som)
                <td> Emp No: {{ $som_employee_no }}</td>
            @elseif ($approved_by_gm)
                <td> Emp No: {{ $gm_employee_no }}</td>
            @elseif ($approved_by_manager && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign'])
                <td> Emp No:
                {{ $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']['employee_no'] }}
                </td>
            @endif

            <td>Emp No: {{ $respondent_user['employee_no'] }}</td>
        </tr>
        <tr>
            @if (($approved_by_input == '' && $prepared_by_input && $requested_by_input && $approved_by_manager))

            @elseif ($approved_by_input || $approved_by_coo || $approved_by_som || $approved_by_head_uw || $approved_by_gm || ($approved_by_manager
            && $respondent_user['employee_supervisor']['supervisor_assign']['employee_supervisor']['supervisor_assign']))
                <td>Date Signed:_______________________</td>
            @endif

            <td>Date Signed:_______________________</td>
        </tr>
    </table>

        @if ($noted1_by_input || $noted2_by_input || $approved_by_hr_manager || $noted_by_hr_manager || $noted_by_hr_director || $noted_by_som || $noted_by_coo)
            <table style="width:100%; text-align:left; font-weight:bold; margin-top:20px; page-break-after:avoid;">
                <tr>

                    @if ($noted1_by_input)
                        <td>Noted by :</td>
                    @elseif ($approved_by_hr_manager)
                        <td>Approved by :</td>
                    @elseif ($noted_by_hr_manager || $noted_by_coo)
                        <td>Noted by :</td>
                    @endif

                    @if ($noted2_by_input || $noted_by_hr_director || $noted_by_som)
                        <td>Noted by :</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted1_by_input || $noted_by_hr_manager || $approved_by_hr_manager || $noted_by_coo)
                        <td>&nbsp;</td>
                    @endif

                    @if ($noted2_by_input || $noted_by_hr_director || $noted_by_som)
                        <td>&nbsp;</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted1_by_input || $noted_by_hr_manager  || $approved_by_hr_manager || $noted_by_coo)
                        <td>__________________________________</td>
                    @endif

                    @if ($noted2_by_input || $noted_by_hr_director || $noted_by_som)
                        <td>__________________________________</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted1_by_input)
                        <td>{{ $noted1_by_input_fullname }}</td>
                    @elseif ($noted_by_hr_manager || $approved_by_hr_manager)
                        <td>{{ $hr_manager_fullname }}</td>
                    @elseif ($noted_by_coo && !$noted_by_hr_manager)
                        <td>{{ $coo_fullname }}</td>
                    @endif

                    @if ($noted2_by_input)
                        <td>{{ $noted2_by_input_fullname }}</td>
                    @elseif ($noted_by_hr_director)
                        <td>{{ $hr_director_fullname }}</td>
                    @elseif ($noted_by_som)
                        <td>{{ $som_fullname }}</td>
                    @endif

                </tr>
                <tr>
                    @if ($noted1_by_input)
                        <td>{{ $noted1_by_input_designation }}</td>
                    @elseif ($noted_by_hr_manager || $approved_by_hr_manager)
                        <td>{{ $hr_manager_designation }}</td>
                    @elseif ($noted_by_coo && !$noted_by_hr_manager)
                        <td>{{ $coo_designation }}</td>
                    @endif

                    @if ($noted2_by_input)
                        <td>{{ $noted2_by_input_designation }}</td>
                    @elseif ($noted_by_hr_director)
                        <td>{{ $hr_director_designation }}</td>
                    @elseif ($noted_by_som)
                        <td>{{ $som_designation }}</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted1_by_input)
                        <td>Emp No: {{ $noted1_by_input_empno }}</td>
                    @elseif ($noted_by_hr_manager || $approved_by_hr_manager)
                        <td>Emp No: {{ $hr_manager_employee_no }}</td>
                    @elseif ($noted_by_coo && !$noted_by_hr_manager)
                        <td>Emp No: {{ $coo_employee_no }}</td>
                    @endif

                    @if ($noted2_by_input)
                        <td>Emp No: {{ $noted2_by_input_empno }}</td>
                    @elseif ($noted_by_hr_director)
                        <td>Emp No: {{ $hr_director_employee_no }}</td>
                    @elseif ($noted_by_som)
                        <td>Emp No: {{ $som_employee_no }}</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted1_by_input || $noted_by_hr_manager || $approved_by_hr_manager || $noted_by_coo)
                        <td>Date Signed:_______________________</td>
                    @endif

                    @if ($noted2_by_input || $noted_by_hr_director || $noted_by_som)
                        <td>Date Signed:_______________________</td>
                    @endif
                </tr>
            </table>
        @endif

        @if ($noted3_by_input || $noted_by_gm)
            <table style="width:100%; text-align:left; font-weight:bold; margin-top:20px; page-break-after:avoid;">
                <tr>
                    <td>Noted by :</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>__________________________________</td>
                </tr>
                <tr>
                    @if ($noted3_by_input)
                        <td>{{ $noted3_by_input_fullname }}</td>
                    @elseif ($noted_by_gm)
                        <td>{{ $gm_fullname }}</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted3_by_input)
                        <td>{{ $noted3_by_input_designation }}</td>
                    @elseif ($noted_by_gm)
                        <td>{{ $gm_designation }}</td>
                    @endif
                </tr>
                <tr>
                    @if ($noted3_by_input)
                        <td>Emp No: {{ $noted3_by_input_empno }}</td>
                    @elseif ($noted_by_gm)
                        <td>Emp No: {{ $gm_employee_no }}</td>
                    @endif
                </tr>
                <tr>
                    <td>Date Signed:_______________________</td>
                </tr>
            </table>
        @endif

    @if ($incident_report['witness_user'])
        <table style="margin-left:270px; margin-top:70px; font-size:10px; font-weight:bold;">
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2">REFUSED TO SIGN</td>
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>Witnessed by:</td>
                    @php if (++$i == 2) break; @endphp
                @endforeach
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>___________________________</td>
                    @php if (++$i == 2) break; @endphp
                @endforeach
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>
                        {{ $witness_user['witness_fullname']['first_name'] }}
                        {{ $witness_user['witness_fullname']['last_name'] }}
                        @php if (++$i == 2) break; @endphp
                    </td>
                @endforeach
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>
                        {{ $witness_user['witness_fullname']['designation']['name'] }}
                        @php if (++$i == 2) break; @endphp
                    </td>
                @endforeach
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>
                        Emp No: {{ $witness_user['witness_fullname']['employee_no'] }}
                        @php if (++$i == 2) break; @endphp
                    </td>
                @endforeach
            </tr>
            <tr>
                @php $i = 0; @endphp
                @foreach($incident_report['witness_user'] as $witness_user)
                    <td>Date Signed:________________</td>
                    @php if (++$i == 2) break; @endphp
                @endforeach
            </tr>
        </table>
    @endif
</div>
