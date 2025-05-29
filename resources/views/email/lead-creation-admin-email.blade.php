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
            <img src="{{asset('public/storage/logo/DynamicEmpireLogo.png')}}" alt="Logo" style="width: 30%; height: auto;" />
        </div>
        <div>
            <p style="margin-top: 1rem !important; font-size: 14px;">Hello <span style="color: #1818CD !important;"><?php if(isset($Name)) { echo $Name; } else { echo 'Dynamic Empire'; } ?>,</span></p>
        </div>
        <p style="margin-top: 15px; font-size: 14px;">
            A new lead has been generated. The lead number is <b style="color: #1818CD !important;"><?php if(isset($LeadNumber)) { echo $LeadNumber; } else { echo '12345678'; } ?></b>.
        </p>
        <table class="table-bordered" style="width: 100%; margin-bottom: 1rem; color: #212529; border: 1px dashed #dee2e6; font-size: 14px;">
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Lead Number: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($LeadNumber)) { echo $LeadNumber; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">First Name: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($FirstName)) { echo $FirstName; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Last Name: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($LastName)) { echo $LastName; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Phone 1: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Phone)) { echo $Phone; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Phone 2: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Phone2)) { echo $Phone2; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Marital Status: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Marital)) { echo $Marital; } else { echo ''; } ?></span>
                </td>
            </tr>
            @if(isset($Marital))
                @if($Marital == 'Married')
                    <tr>
                        <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                            <span style="margin-left: 10px; ">Spouse: </span>
                        </td>
                        <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                            <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Spouse)) { echo $Spouse; } else { echo ''; } ?></span>
                        </td>
                    </tr>
                @endif
            @endif
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Language: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Language)) { echo $Language; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">State: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($State)) { echo $State; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">City: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($City)) { echo $City; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Street: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Street)) { echo $Street; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Zip code: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Zipcode)) { echo $Zipcode; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Product: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Product)) { echo $Product; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px dashed #dee2e6; border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Email: </span>
                </td>
                <td style="border-bottom: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Email)) { echo $Email; } else { echo ''; } ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px dashed #dee2e6; width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; ">Note: </span>
                </td>
                <td style="width: 33%; padding: 5px;">
                    <span style="margin-left: 10px; color: #1818CD !important; font-size: 14px;"><?php if(isset($Note)) { echo $Note; } else { echo ''; } ?></span>
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
