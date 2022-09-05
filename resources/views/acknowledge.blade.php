<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EP') }}</title>
    <link href="{{ mix('css/public.css') }}" rel="stylesheet">
</head>
<body class="gray-bg">
    <div class="loginColumns animated fadeInDown">
        <div class="row">
            <div class="col-md-6">
                <h2 class="font-bold" style="margin-bottom:50px;">Welcome to {{ config('app.name', 'EP') }}</h2>
                <div class="m-t-xl" style="text-align:center;">
                    @if ($error)
                        <span class="help-block text-danger">
                            <strong><h2>{{ $error }}</h2></strong>
                            <strong>THANK YOU !</strong>
                        </span>
                    @endif
                    @if ($success)
                        <span class="help-block text-success">
                            <strong><h2>{{ $success }}</h2></strong>
                            <strong>THANK YOU !</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="ibox-content">
                    <img src="/img/ep_logo.jpg" style="width: 320px;">
                    <form class="m-t" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        @if ($errors->has('employee_no'))
                            <span class="help-block text-danger">
                                <strong>{{ $errors->first('employee_no') }}</strong>
                            </span>
                        @endif
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                        <div class="form-group{{ $errors->has('employee_no') ? ' has-error' : '' }}">
                            <input dusk="employee_no" id="employee_no" type="number" class="form-control" placeholder="Employee No."
                             name="employee_no" value="{{ old('employee_no') }}" required autofocus>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input dusk="password" id="password" type="password" name="password" class="form-control"
                            placeholder="●●●●●●●" required>
                        </div>
                        <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                    </form>
                    <div style="text-align:center;">
                        <h6>Behavioral Management System @ Version {{ config('app.version', '1.0.0.0') }}</h6>
                    </div>

                </div>
            </div>
        </div>
        <hr style="border-top: 1px solid #ddd;"/>
        <div class="row">
            <div class="col-md-6">
                ARB Call Facilities Inc.
            </div>
            <div class="col-md-6 text-right">
               <small>© 2019</small>
            </div>
        </div>
    </div>
</body>
</html>
