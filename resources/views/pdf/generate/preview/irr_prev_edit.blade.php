<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <link href="{{ asset('css/public.css') }}" rel="stylesheet">
</head>
<body style="background-color: #525659;">
    <div class="mainPreview" >
        <div class="col-lg-12" style="padding-bottom: 3%; text-align:center;">
            <h2 style="color:#42b983;">Preview DA Update Form</h2>
        </div>
        <small class="pull-right" style="color:red; margin-right: 15px;">
            <i> Complete the form by providing all the required fields with the information being asked.
                ( <span>*</span> )
            </i>
        </small>
        <div class="mainContent">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! Form::model($incident_report, ['method' => 'PATCH','route' => ['preview_da.update', $incident_report['id']]]) !!}
            @include('pdf.generate.preview.irr_prev_form')
        {!! Form::close() !!}
        </div>
    </div>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(function(){
    $("#asteriskSingleSuspension").hide();

   $('#selectSuspension').change(function() {
    if ($(this).find("option:selected").text().search("Suspension") > 0) {
        $("#showBladeSuspensionDate").show();
        $("#asteriskSingleSuspension").show();
        $("input[type=date][name=suspension_date]").attr("required", "required");

        for (var i=0; i<10; i++) {
            $("input[type=date][name=suspension_date_multi_" + i + "]").attr("required", "required");
        }

    } else {
        $("#showBladeSuspensionDate").hide();
        $("#asteriskSingleSuspension").hide();
        $("input[type=date][name=suspension_date]").val("").removeAttr("required");

        for (var i=0; i<10; i++) {
            $("input[type=hidden][name=suspension_date_multi_key]").val("");
            $("input[type=date][name=suspension_date_multi_" + i + "]").val("").removeAttr("required");
        }
    }
   });
});
</script>

