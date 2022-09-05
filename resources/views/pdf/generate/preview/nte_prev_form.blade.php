<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <div class="asterisk_label">*</div>
            <strong>Date NTE Serve:</strong>
            {!! Form::date('date_nte_serve', $incident_report->date_nte_serve, 
            array('class' => 'form-control', 'required' => 'required')) !!}
        </div>
    </div>

    @if (count(json_decode($incident_report->initial_irr_id, true)) > 1 
    && count(json_decode($incident_report->initial_instance, true)) > 1) 

        @foreach(json_decode($incident_report->initial_instance)  as $key => $initial_instance_new)
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="asterisk_label">*</div>
                    <strong>({{ $key+1 }}) Initial Instance:</strong>
                    {!! Form::select('initial_instance_new_'.$key, $instance_dropdown, 
                        $initial_instance_new, array('class' => 'form-control', 
                        'required' => 'required')) !!}
                </div>
            </div>
        @endforeach

        @foreach(json_decode($incident_report->initial_irr_id) as $key => $initial_irr_multiple_new)
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="asterisk_label">*</div>
                    <strong>({{ $key+1 }}) Initial Corrective Action:</strong>
                    {!! Form::select('initial_irr_multiple_new_'.$key, $irr_dropdown, 
                        $initial_irr_multiple_new, array('class' => 'form-control', 
                        'required' => 'required')) !!}                    
                </div>
            </div>
        @endforeach

        {!! Form::hidden('multiple_key', count(json_decode($incident_report->initial_irr_id, true))) !!}

    @else
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <div class="asterisk_label">*</div>
                <strong>Final Instance:</strong>
                {!! Form::select('initial_instance', $instance_dropdown, 
                    $incident_report->initial_instance, array('class' => 'form-control', 
                    'required' => 'required')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <div class="asterisk_label">*</div>
                <strong>Final Corrective Action:</strong>
                {!! Form::select('initial_irr_id', $irr_dropdown, $incident_report->initial_irr_id,
                    array('class' => 'form-control', 'required' => 'required')) !!}
            </div>
        </div>
    @endif

    @if ($incident_report->initial_irr_id == 10)
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>
                Art. 297. Termination by employer. An employer may 
                terminate an employment for any of the following causes:
            </strong>
            <br>
            <small style="background-color: yellow;">
            Note: By default it displays checked all list, uncheck you want to omit
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
    @endif

    <div class="col-xs-12 col-sm-12 col-md-12 pull-left">
        <a class="btn btn-warning" href="/incidentreport#in-progress">
        <i class="fa fa-backward"></i> Back to IR</a>
        <button type="submit" class="btn btn-primary">Update NTE</button>
    </div>
</div>
