<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <div class="asterisk_label">*</div>
            <strong>Date DA:</strong>
            {!! Form::date('date_da', $incident_report->date_da, array('class' => 'form-control', 'required' => 'required')) !!}
        </div>
    </div>

    @if ($incident_report->irr->name == "Absolved" || $incident_report->irr_id == 8)
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <div class="asterisk_label">*</div>
                <strong>Final Corrective Action:</strong> {{ $incident_report->irr->name }}
                {!! Form::hidden('irr_absolved', $incident_report->irr->name) !!}
            </div>
        </div>
    @else

        @if ($incident_report->final_irr_multiple_new && $incident_report->final_instance_new) 

            @foreach($incident_report->final_instance_new  as $key => $final_instance_new)
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <div class="asterisk_label">*</div>
                        <strong>({{ $key+1 }}) Final Instance:</strong>
                        {!! Form::select('final_instance_new_'.$key, $instance_dropdown, 
                            $final_instance_new, array('class' => 'form-control', 'required' => 'required')) !!}
                    </div>
                </div>
            @endforeach

            @foreach($incident_report->final_irr_multiple_new as $key => $final_irr_multiple_new)
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <div class="asterisk_label">*</div>
                        <strong>({{ $key+1 }}) Final Corrective Action:</strong>
                        {!! Form::select('final_irr_multiple_new_'.$key, $irr_dropdown, 
                            $final_irr_multiple_new, array('class' => 'form-control', 'required' => 'required')) !!}                    
                    </div>
                </div>
            @endforeach

            {!! Form::hidden('multiple_key', count($incident_report->final_irr_multiple_new)) !!}

        @else
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="asterisk_label">*</div>
                    <strong>Final Instance:</strong>
                    {!! Form::select('final_instance', $instance_dropdown, 
                        $incident_report->final_instance, array('class' => 'form-control', 'required' => 'required')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="asterisk_label">*</div>
                    <strong>Final Corrective Action:</strong>
                    {!! Form::select('final_irr_single', $irr_dropdown, $incident_report->final_irr_single,
                        array('class' => 'form-control', 'required' => 'required')) !!}
                </div>
            </div>
        @endif

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <div class="asterisk_label">*</div>
                <strong>Generate Case Closure:</strong>
                {!! Form::select('irr_id', $irr_dropdown, $incident_report->irr_id,
                    array('class' => 'form-control', 'required' => 'required', 'id' => 'selectSuspension')) !!}
            </div>
        </div>
    
        <div id="showBladeSuspensionDate">
            @if (count($incident_report->suspension_date_multi) > 0)

                @for ($i = 0; $i < count($incident_report->suspension_date_multi); $i++)
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <div class="asterisk_label">*</div>
                        <strong>({{ $i+1 }}) Suspension Date:</strong>
                        {!! Form::date('suspension_date_multi_'.$i, $incident_report->suspension_date_multi[$i], array('class' => 'form-control')) !!}
                    </div>
                </div>
                @endfor

                {!! Form::hidden('suspension_date_multi_key', count($incident_report->suspension_date_multi)) !!}

            @elseif ($incident_report->suspension_date)
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <div class="asterisk_label">*</div>
                        <strong>Suspension Date:</strong>
                        {!! Form::date('suspension_date', $incident_report->suspension_date, array('class' => 'form-control')) !!}
                    </div>
                </div>
            @else
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <div id="asteriskSingleSuspension" class="asterisk_label">*</div>
                        <strong>Suspension Date:</strong>
                        {!! Form::date('suspension_date', $incident_report->suspension_date, array('class' => 'form-control')) !!}
                    </div>
                </div>
            @endif
        </div>

        @if ($incident_report->irr_id == 10 || $incident_report->final_irr_single == 10)
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>
                    Art. 297. Termination by employer. An employer may 
                    terminate an employment for any of the following causes:
                </strong>
                <br>
                <small style="background-color: yellow;">
                Note: By default, it's displays checked all in the list, uncheck/s you want to omit
                </small>
                <br>
                {{ Form::checkbox('art_297[]', 'a', true) }}
                a. Serious misconduct or wilful disobedience by the employee of the lawful <br>
                <span style="margin-left:30px;">
                orders of his employer or representative in connection with his work;
                </span><br>

                {{ Form::checkbox('art_297[]', 'b', true) }}
                b. Gross and habitual neglect by the employee of his duties;
                <br>

                {{ Form::checkbox('art_297[]', 'c', true) }}
                c. Fraud or wilful breach by the employee of the trust reposed in him by<br>
                <span style="margin-left:30px;"> his employer or duly authorized representative; </span>
                <br>

                {{ Form::checkbox('art_297[]', 'd', true) }}
                d. Commission of a crime or offense by the employee against the person <br>
                <span style="margin-left:30px;">
                of his employer or any immediate member of his family or his duly authorized 
                representatives; and
                </span><br>

                {{ Form::checkbox('art_297[]', 'e', true) }}
                e. Other analogous cases of the foregoing;
                <br>
            </div>
        </div>
        @endif <!-- end art297 termination  -->
    @endif <!-- end absolved  -->

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>DA Status Stage of the Case:</strong>
            {!! Form::select('da_status_stage_case', $daStatusStageCase_dropdown, 
            $get_da_status_stage_case, array('class' => 'form-control')) !!}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 pull-left">
            <a class="btn btn-warning" href="/irhistory"><i class="fa fa-backward"></i> Back to IR History</a>
            <button type="submit" class="btn btn-primary">Update DA</button>
    </div>
</div>
