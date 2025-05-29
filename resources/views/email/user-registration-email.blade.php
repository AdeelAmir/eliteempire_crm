<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
            <p style="margin-top: 1rem !important; font-size: 14px;">Hello <span style="color: #1818CD !important;"><?php if(isset($Name)) { echo $Name; } else { echo 'Dynamic Empire'; } ?>,</span></p>
        </div>

        <table class="table-bordered" style="width: 100%; margin-bottom: 1rem; color: #212529; border: 1px dashed #dee2e6; font-size: 14px;">
            <tr>
                <td colspan="2" style="border-bottom: 1px dashed #dee2e6; text-align: center; padding: 5px;">
                    <b style="font-size: large;">Your Login Credentials</b>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px;">Email: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Email)) { echo $Email; } else { echo 'dynamicempire@gmail.com'; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px;">Password: </span>
                </td>
                <td style="width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Password)) { echo $Password; } else { echo '12345678'; } ?></span>
                </td>
            </tr>
        </table>
        <p style="margin-top: 15px; font-size: 14px;">
            Thank You,
            <br>
            <span style="color: #1818CD !important;">Elite Empire</span>
        </p>
    </div>
</div>
</body>
</html>
