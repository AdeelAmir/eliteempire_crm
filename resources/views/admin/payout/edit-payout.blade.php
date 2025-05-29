@extends('admin.layouts.app')
@section('content')
<div class="page-content" id="editUserPage">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Edit Payout Form</span></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            @if($Role == 1)
            <button type="button" class="btn btn-primary"
            onclick="window.location.href='{{url('admin/payout')}}';">
            <i class="fas fa-arrow-left mr-1"></i>
            Back
        </button>
        @endif
    </div>
</div>

@if(Session::get('user_role') == 1)
<form action="{{url('admin/payout/update')}}" method="post" id="editPayoutForm" enctype="multipart/form-data">
    @endif
    @csrf
    <input type="hidden" name="id" id="_id" value="{{$payout_settings_id}}"/>
    <div class="row">
        <div class="col-12 mb-3">
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
            @elseif(session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
        </div>

        {{--General Details--}}
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <br>
                    @endif
                    <h6 class="card-title">
                        General
                    </h6>
                    @foreach($payout_details as $pay_details)
                    <div class="row">
                        <div class="col-md-4 mb-4 mt-4">
                            <label for="payout_type">Payout Type</label>
                            <input type="text" name="payout_type" id="payout_type"
                            value="{{$pay_details->payout_type}}" class="form-control"
                            placeholder="Enter Your PayOut Type" readonly/>
                        </div>
                        <div class="col-md-4 mb-4 mt-4">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control"
                            value="{{$pay_details->amount}}" placeholder="Enter Your Amount"
                            required/>
                        </div>
                        <!-- <div class="col-md-4 mb-4 mt-4">
                            <label for="dob">Percentage</label>
                            <input type="Number" name="percentage" id="percentage" class="form-control" placeholder="Enter Percentage"  value="{{$pay_details->percentage}}" required/>
                        </div> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right mt-3" style="margin-bottom: 14px; margin-left: -12px;">
                        <input type="submit" class="btn btn-primary w-10" name="submitEditPayoutForm"
                        id="submitEditPayoutForm" value="Save"/>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</form>

@endsection
