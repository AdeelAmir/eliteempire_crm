@extends('admin.layouts.app')
@section('content')

<div class="page-content" id="addNewCallRequest">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">New Call Request</span></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            @if($Role == 1)
            <button type="button" class="btn btn-primary"
            onclick="window.location.href='{{url('admin/call-requests')}}';">
            <i class="fas fa-arrow-left mr-1"></i>
            Back
        </button>
        @elseif($Role == 2)
        <button type="button" class="btn btn-primary"
        onclick="window.location.href='{{url('general_manager/call-requests')}}';">
        <i class="fas fa-arrow-left mr-1"></i>
        Back
    </button>
    @elseif($Role == 3)
    <button type="button" class="btn btn-primary"
    onclick="window.location.href='{{url('confirmationAgent/call-requests')}}';">
    <i class="fas fa-arrow-left mr-1"></i>
    Back
</button>
@elseif($Role == 4)
<button type="button" class="btn btn-primary"
onclick="window.location.href='{{url('supervisor/call-requests')}}';">
<i class="fas fa-arrow-left mr-1"></i>
Back
</button>
@elseif($Role == 5)
<button type="button" class="btn btn-primary"
onclick="window.location.href='{{url('representative/call-requests')}}';">
<i class="fas fa-arrow-left mr-1"></i>
Back
</button>
@endif
</div>
</div>

@if($Role == 1)
<form action="{{url('admin/call-request/store')}}" method="post" id="addCallRequestForm" enctype="multipart/form-data">
    @elseif($Role == 2)
    <form action="{{url('general_manager/call-request/store')}}" method="post" id="addCallRequestForm" enctype="multipart/form-data">
        @elseif($Role == 3)
        <form action="{{url('confirmationAgent/call-request/store')}}" method="post" id="addCallRequestForm" enctype="multipart/form-data">
            @elseif($Role == 4)
            <form action="{{url('supervisor/call-request/store')}}" method="post" id="addCallRequestForm" enctype="multipart/form-data">
                @elseif($Role == 5)
                <form action="{{url('representative/call-request/store')}}" method="post" id="addCallRequestForm" enctype="multipart/form-data">
                    @endif
                    @csrf
                    <div class="row" id="addCallRequestPage">
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
                                    <div class="row">
                                        <input type="hidden" name="team" id="team" value="{{$TeamId}}" />
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="firstName">First Name*</label>
                                            <input type="text" name="firstName" id="firstName" class="form-control"
                                            placeholder="First Name" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="lastName">Last Name*</label>
                                            <input type="text" name="lastName" id="lastName" class="form-control"
                                            placeholder="Last Name" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="phone">Phone Number 1</label>
                                            <input type="text" name="phone" id="phone" class="form-control"
                                            placeholder="Enter Your Phone Number" maxlength="20"
                                            required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="appointmenttime">Appointment Time*</label>
                                            <div class="input-group date form_datetime" data-date-format="mm/dd/yyyy - HH:ii p"
                                            data-link-field="appointmenttime">
                                            <input class="form-control" size="16" type="text" value="">
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                            <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="appointmenttime" name="appointmenttime" value="" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="product">Product*</label>
                                            <select name="product" id="product" class="form-control" onchange="checkProduct();"
                                            required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="_ProductDescriptionBlock" style="display:none;">
                                            <label for="product_desc">Product Description</label>
                                            <input type="text" name="product_desc" id="product_desc" class="form-control"
                                            placeholder="Enter Product Description"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="_electricbillblock" style="display:none;">
                                            <label for="electricbill">Electric Bill</label>
                                            <input type="file" name="electricbill" id="electricbill" class="form-control"
                                            accept="image/jpeg, image/png, image/jpg, application/pdf"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="split">Split</label>
                                            <select name="split" id="split" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($SplitOptions as $item)
                                                <option value="{{$item->id}}">{{$item->firstname . ' ' . $item->lastname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-3">
                                            <label for="note">Note*</label>
                                            <textarea class="form-control" id="note" name="note" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <input type="submit" class="btn btn-primary w-10" value="Submit"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @endsection
