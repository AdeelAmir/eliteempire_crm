<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Email</title>

    <style type="text/css">
        body{
            margin: 30px;
            font-family: Arial,
            Helvetica, sans-serif;
        }

        @media only screen and (max-width: 767px){
            .inner-column {
                margin-left: 0;
                max-width: 100%;
                text-align: justify;
            }
        }

        @media only screen and (min-width: 768px){
            .inner-column {
                margin-left: 20%;
                margin-right: 20%;
                max-width: 100%;
                text-align: justify;
            }
        }
    </style>
</head>
<body>
<div>
    <div class="inner-column">
        <div style="text-align: center;">
            <img src="{{asset('public/storage/logo/logo.png')}}" alt="Logo" style="width: 30%; height: auto;" />
        </div>
        <div>
            <p style="margin-top: 1rem !important; font-size: 14px;">Hello <span style="color: #1818CD !important;"><?php if(isset($Name)) { echo $Name; } else { echo 'Elite Empire'; } ?>,</span></p>
        </div>
        <p style="margin-top: 15px; font-size: 14px;">
            {{$Message}}
        </p>
        <p style="margin-top: 15px; font-size: 14px;">
            Thank You,
            <br>
            <span style="color: #1818CD !important;">Elite Empire</span>
        </p>
    </div>
</div>
</body>
</html>
