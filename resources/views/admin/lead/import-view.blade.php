@extends('admin.layouts.app')
@section('style')
    <style type="text/css">
        .files input {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            padding: 120px 0 85px 35%;
            text-align: center !important;
            margin: 0;
            width: 100% !important;
        }

        .files input:focus {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            border: 1px solid #92b0b3;
        }

        .files {
            position: relative;
        }

        .files:after {
            pointer-events: none;
            position: absolute;
            top: 60px;
            left: 0;
            width: 50px;
            right: 0;
            height: 56px;
            content: "";
            background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
            display: block;
            margin: 0 auto;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        .color input {
            background-color: #f1f1f1;
        }

        .files:before {
            position: absolute;
            bottom: 10px;
            left: 0;
            pointer-events: none;
            width: 100%;
            right: 0;
            height: 57px;
            content: " or drag it here. ";
            display: block;
            margin: 0 auto;
            color: #2ea591;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
        }
    </style>
@endsection
@section('content')

    <div class="container" style="margin-top: 5%">
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/import/leads');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 8) {
            $Url = url('affiliate/import/leads');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 9) {
            $Url = url('realtor/import/leads');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/import/leads');
        }
        ?>
        <div class="col-md-12">
            <form action="{{ $Url }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group files color mt-2">
                    <strong>Upload File</strong>
                    <input type="file" name="file" class="form-control" required/>
                </div>
                <button type="submit" name="submit" value="Import" class="btn btn-primary" style="margin-top: 2.4em;">
                    Import
                </button>
            </form>
        </div>
    </div>
@endsection
