@extends('admin.layouts.app')
@section('content')
    <style rel="stylesheet" type="text/css">
    @media (max-width: 580px) {
      .item {
        margin-left: 0% !important;
      }
      .customerInformation{
        font-weight: bold;
        font-size: 15px !important;
      }
    }

    .customerInformation{
      font-weight: bold;
      font-size: 23px;
    }

    .form-control[readonly]{
        background-color: white;
    }

    .owl-prev {
        /*text-align: center !important;*/
        font-size: 40px !important;
        /*width: 50% !important;*/
        float: left !important;
        position: absolute;
        top: 35%;
        left: -2%;
    }

    .owl-next {
        /*text-align: center !important;*/
        font-size: 40px !important;
        /*width: 50% !important;*/
        /*float: left !important;*/
        position: absolute;
        top: 35%;
        right: -2%;
    }
    .item {
      margin-left: 15%;
    }
    .dataTables_filter{
      display: none;
    }

    .dataTables_info{
      display: none;
    }

    /* div.dataTables_wrapper div.dataTables_paginate {
      display: none;
    } */

    .owl-carousel .owl-nav button.owl-prev{
        background: 0 0;
        color: inherit;
        border: none;
        margin-left: 20px !important;
        font-size: 4em !important;
    }
    .owl-carousel .owl-nav button.owl-next{
        background: 0 0;
        color: inherit;
        border: none;
        margin-right: 20px !important;
        font-size: 4em !important;
    }
    </style>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
            <div>
                <h4 class=" mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ASSIGNED LEADS</span></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                @if(session()->has('message'))
                    <div class="alert alert-success mt-3 ml-2">
                        {{ session('message') }}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger mt-3 ml-2">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <div class="col-12 mb-3">
                <div class="alert alert-success mt-3 ml-2" id="leadStatusSuccessAlert" style="display:none;">
                    Lead status has been updated successfully!
                </div>
                <div class="alert alert-danger mt-3 ml-2" id="leadStatusFailedAlert" style="display:none;">
                  Error! An unhandled exception occurred.
                </div>
            </div>

            @if($CartMessage != "")
              <div class="col-12 mb-3">
                  <div class="" style="margin-top: 15%;">
                      <h4 class="text-center">{{$CartMessage}}</h4>
                  </div>
              </div>
            @endif

            <?php
            $TotalTasks = count($AssignedLeads);
            $Url = "";
            if ($Role == 2) {
                $Url = url('global_manager/lead/assignLead/update');
            } elseif ($Role == 3) {
                $Url = url('confirmationAgent/lead/assignLead/update');
            }
            $Count = 0;
            ?>
            <input type="hidden" name="totaltasks" id="totaltasks" value="{{$TotalTasks}}" />

            <div class="col-12">
                <div class="wrap owl-theme owl-carousel">
                    @foreach($AssignedLeads as $lead)
                        <div class="item" id="item_{{$Count}}">
                            <div class="row">
                                    {{-- Lead Information --}}
                                    {{--<div class="col-md-12 mb-2 stretch-card">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="leadNo{{$Count}}">Lead Number</label>
                                                        <input type="text" name="leaNo" class="form-control" id="leadNo{{$Count}}" value="{{$lead->lead_number}}" />
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="leadGenerator{{$Count}}">Lead Generator</label>
                                                        <input type="text" name="leadGenerator" class="form-control" id="leadGenerator{{$Count}}" value="{{$lead->firstname . " " . $lead->lastname}}" readonly />
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="leadDateCreated{{$Count}}">Date Created</label>
                                                        <input type="text" name="leadDateCreated" class="form-control" id="leadDateCreated{{$Count}}" value="{{\Carbon\Carbon::parse($lead->created_at)->format('m-d-Y')}}" readonly />
                                                    </div>
                                                    @if($lead->is_duplicated == 0)
                                                        <div class="col-md-3 mb-3">
                                                            <label for="duplicated{{$Count}}">Is Duplicated</label>
                                                            <div class="" id="leadduplicatestatus">
                                                              <span class="badge badge-danger" style="padding: 10px 20px;font-size: 12px;">No</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-3 mb-3">
                                                            <label for="duplicated{{$Count}}">Is Duplicated</label>
                                                            <div class="" id="leadduplicatestatus">
                                                              <span class="badge badge-success" style="padding: 10px 20px;font-size: 12px;">Yes</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>--}}

                                    {{-- Customer Information --}}
                                    <div class="col-md-8 mb-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <form action="{{$Url}}" method="post" id="assignedLeadForm{{$Count}}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$lead->id}}"/>
                                                    <input type="hidden" name="leadNumber" class="form-control" id="leadNo{{$Count}}" value="{{$lead->lead_number}}" />
                                                    <input type="hidden" name="leadType" id="leadType{{$Count}}" value="{{$lead->lead_type}}" />
                                                    <input type="hidden" name="electricbill_Old"
                                                           id="electricbill_Old{{$Count}}"
                                                           value="{{$lead->electricbill}}"/>

                                                    <div class="row mb-3">
                                                        <div class="col-3">
                                                            <b>{{$lead->lead_number}}</b>
                                                        </div>
                                                        <div class="col-6 text-center">
                                                            <p class="customerInformation">Customer Information</p>
                                                        </div>
                                                        <div class="col-3 text-right">
                                                            @if($lead->phone != "")
                                                                <a href="tel:{{$lead->phone}}"><i class="fa fa-phone-alt fa-2x"></i></a>
                                                            @else
                                                                <a href="tel:{{$lead->phone2}}"><i class="fa fa-phone-alt fa-2x"></i></a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-3 mb-2">
                                                            <label for="firstName{{$Count}}">First Name</label>
                                                            <input type="text" name="firstName" class="form-control"
                                                                   id="firstName{{$Count}}"
                                                                   value="{{$lead->firstname}}"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="lastName{{$Count}}">Last Name</label>
                                                            <input type="text" name="lastName" class="form-control"
                                                                   id="lastName{{$Count}}" value="{{$lead->lastname}}"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="martial_status{{$Count}}">Marital Status</label>
                                                            <select name="martial_status" id="martial_status{{$Count}}"
                                                                    class="form-control"
                                                                    onchange="checkTaskMaritalStatus({{$Count}});">
                                                                <option value="" <?php if ($lead->martial_status == "") {
                                                                    echo "selected";
                                                                } ?> >Select Marital Status
                                                                </option>
                                                                <option value="married" <?php if ($lead->martial_status == "married") {
                                                                    echo "selected";
                                                                } ?> >Married
                                                                </option>
                                                                <option value="single" <?php if ($lead->martial_status == "single") {
                                                                    echo "selected";
                                                                } ?> >Single
                                                                </option>
                                                                <option value="unknown" <?php if ($lead->martial_status != "" && $lead->martial_status != "married" && $lead->martial_status != "single") {
                                                                    echo "selected";
                                                                } ?> >Unknown
                                                                </option>
                                                            </select>
                                                        </div>
                                                        @if($lead->martial_status == 'married')
                                                            <div class="col-md-3 mb-3" id="_SpouceBlock{{$Count}}">
                                                                <label for="spouse{{$Count}}">Spouse</label>
                                                                <input type="text" name="spouce" class="form-control" id="spouse{{$Count}}" value="{{$lead->spouce}}" placeholder="Spouse Name" />
                                                            </div>
                                                        @else
                                                            <div class="col-md-3 mb-3" id="_SpouceBlock{{$Count}}" style="display:none;">
                                                                <label for="spouse{{$Count}}">Spouse</label>
                                                                <input type="text" name="spouce" class="form-control" id="spouse{{$Count}}" placeholder="Spouse Name" />
                                                            </div>
                                                        @endif
                                                        <div class="col-md-3 mb-2">
                                                            <label for="language{{$Count}}">Language</label>
                                                            <select name="language" id="language{{$Count}}"
                                                                    class="form-control">
                                                                <option value="" >Select Language</option>
                                                                <option value="english" <?php if ($lead->language == "english" || $lead->language == "") {
                                                                    echo "selected";
                                                                } ?> >English
                                                                </option>
                                                                <option value="spanish" <?php if ($lead->language == "spanish") {
                                                                    echo "selected";
                                                                } ?> >Spanish
                                                                </option>
                                                                <option value="bilingual" <?php if ($lead->language != "" && $lead->language == "english" && $lead->language == "spanish") {
                                                                    echo "selected";
                                                                } ?> >Bilingual
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="phone1{{$Count}}">Phone 1</label>
                                                            <input type="text" name="phone" class="form-control"
                                                                   id="phone1{{$Count}}" value="{{$lead->phone}}" readonly />
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="phone2{{$Count}}">Phone 2</label>
                                                            <input type="text" name="phone2" class="form-control"
                                                                   id="phone2{{$Count}}" value="{{$lead->phone2}}"/>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label for="email{{$Count}}">Email</label>
                                                            <input type="email" name="email" class="form-control"
                                                                   id="email{{$Count}}" value="{{$lead->email}}"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="street{{$Count}}">Street</label>
                                                            <input type="text" name="street" class="form-control"
                                                                   id="street{{$Count}}" value="{{$lead->street}}"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="city{{$Count}}">City</label>
                                                            <input type="text" name="city" class="form-control"
                                                                   id="city{{$Count}}" value="{{$lead->city}}"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="state{{$Count}}">State</label>
                                                            <select class="form-control" name="state"
                                                                    id="state{{$Count}}"
                                                                    required>
                                                                <option value="">Select State</option>
                                                                @foreach($states as $state)
                                                                    <option value="{{$state->name}}"
                                                                    <?php if ($lead->state == $state->name) {
                                                                        echo "selected";
                                                                    } ?>>{{$state->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="zipcode{{$Count}}">Zip Code</label>
                                                            <input type="text" name="zipcode" class="form-control"
                                                                   id="zipcode{{$Count}}" value="{{$lead->zipcode}}"
                                                                   placeholder="Zip Code"
                                                                   onkeypress="limitKeypress(event,this.value,5)"
                                                                   onblur="limitTaskZipCodeCheck({{$Count}});"/>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="product{{$Count}}">Product</label>
                                                            <select name="product" id="product{{$Count}}"
                                                                    class="form-control"
                                                                    onchange="checkTaskProduct({{$Count}});">
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $product)
                                                                    @if($lead->product == $product->id)
                                                                        <option value="{{$product->id}}"
                                                                                selected>{{$product->name}}</option>
                                                                    @else
                                                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if($lead->product == 6)
                                                        <div class="col-md-3 mb-2" id="_ProductDescriptionBlock{{$Count}}">
                                                            <label for="product_desc{{$Count}}">Product Description</label>
                                                            <input type="text" name="product_desc" class="form-control" id="product_desc{{$Count}}" value="{{$lead->product_desc}}" />
                                                        </div>
                                                        @else
                                                        <div class="col-md-3 mb-2" id="_ProductDescriptionBlock{{$Count}}" style="display:none;">
                                                            <label for="product_desc{{$Count}}">Product Description</label>
                                                            <input type="text" name="product_desc" class="form-control" id="product_desc{{$Count}}" value="{{$lead->product_desc}}" />
                                                        </div>
                                                        @endif

                                                        @if($lead->product == 1)
                                                        <div class="col-md-3 mb-2" id="_WindowsDoorsCountBlock{{$Count}}">
                                                            <label for="windows_doors_count{{$Count}}">How many windows?</label>
                                                            <input type="number" name="windows_doors_count" id="windows_doors_count{{$Count}}" class="form-control" min="1" value="{{$lead->windows_doors_count}}" />
                                                        </div>
                                                        @else
                                                        <div class="col-md-3 mb-2" id="_WindowsDoorsCountBlock{{$Count}}" style="display:none;">
                                                            <label for="windows_doors_count{{$Count}}">How many windows?</label>
                                                            <input type="number" name="windows_doors_count" id="windows_doors_count{{$Count}}" class="form-control" min="1" value="{{$lead->windows_doors_count}}" />
                                                        </div>
                                                        @endif

                                                        @if($lead->product == 2)
                                                        <div class="col-md-3 mb-2" id="_OldRoofDurationBlock{{$Count}}">
                                                            <label for="old_roof_duration{{$Count}}">How old is the roof?</label>
                                                            <input type="number" name="old_roof_duration" id="old_roof_duration{{$Count}}" class="form-control" min="1" value="{{$lead->old_roof}}" />
                                                        </div>
                                                        @else
                                                        <div class="col-md-3 mb-2" id="_OldRoofDurationBlock{{$Count}}" style="display:none;">
                                                            <label for="old_roof_duration">How old is the roof?</label>
                                                            <input type="number" name="old_roof_duration" id="old_roof_duration{{$Count}}" class="form-control" min="1" value="{{$lead->old_roof}}" />
                                                        </div>
                                                        @endif

                                                        @if($lead->product == 5)
                                                        <div class="col-md-3 mb-3" id="_electricbillblock{{$Count}}">
                                                            @if($lead->electricbill != "")
                                                            <label class="w-100">Electric Bill <a class="text-black" href="<?php echo asset('storage/app/public/leads/' . $lead->electricbill); ?>" download>
                                                              <i class="fa fa-download float-right"></i></a>
                                                            </label>
                                                            @else
                                                            <label for="electricbill{{$Count}}">Electric Bill</label>
                                                            @endif
                                                            <input type="file" name="electricbill" class="form-control" id="electricbill{{$Count}}" accept="image/jpeg, image/png, image/jpg, application/pdf" />
                                                        </div>
                                                        @else
                                                        <div class="col-md-3 mb-3" id="_electricbillblock{{$Count}}" style="display: none;">
                                                            <label for="electricbill{{$Count}}">Electric Bill</label>
                                                            <input type="file" name="electricbill" class="form-control" id="electricbill{{$Count}}" accept="image/jpeg, image/png, image/jpg, application/pdf" />
                                                        </div>
                                                        @endif
                                                        <?php
                                                        $AppointmentTime = null;
                                                        if ($lead->appointment_time != "") {
                                                          $AppointmentTime = \Carbon\Carbon::parse($lead->appointment_time)->format('m/d/Y - g:i a');
                                                        }
                                                        ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="appointmenttime{{$Count}}">Appointment
                                                                Time</label>
                                                            <div class="input-group date form_datetime"
                                                                 data-date-format="mm/dd/yyyy - HH:ii p"
                                                                 data-link-field="appointmenttime{{$Count}}">
                                                                <input class="form-control" size="16" type="text"
                                                                       value="{{$AppointmentTime}}"/>
                                                                <span class="input-group-addon"><span
                                                                            class="glyphicon glyphicon-remove"></span></span>
                                                                <span class="input-group-addon"><span
                                                                            class="glyphicon glyphicon-th"></span></span>
                                                            </div>
                                                            <input type="hidden" id="appointmenttime{{$Count}}"
                                                                   name="appointmenttime"
                                                                   value="{{$lead->appointment_time}}" required/>
                                                        </div>

                                                        <div class="col-md-12 mb-2">
                                                            <label for="note{{$Count}}">Note</label>
                                                            <textarea name="note" class="form-control"
                                                                      id="note{{$Count}}"
                                                                      rows="3">{{$lead->note}}</textarea>
                                                        </div>

                                                        <div class="col-md-3 mb-2">
                                                            <label for="leadStatus{{$Count}}">Lead Status</label>
                                                            <br>
                                                            <span class="" id="_task_lead_status_{{$Count}}">
                                                              @if ($lead->lead_status == 1)
                                                                  <span class="badge badge-success">Confirm</span>
                                                              @elseif ($lead->lead_status == 2)
                                                                  <span class="badge badge-danger">Cancelled</span>
                                                              @elseif ($lead->lead_status == 3)
                                                                  <span class="badge badge-warning">Pending</span>
                                                              @elseif ($lead->lead_status == 4)
                                                                  <span class="badge badge-primary">Approve Sales</span>
                                                              @elseif ($lead->lead_status == 5)
                                                                  <span class="badge badge-warning" style="background-color:pink;color:white;">Bank Turn Down</span>
                                                              @elseif ($lead->lead_status == 6)
                                                                  <span class="badge badge-warning" style="background-color:orange;">Out of coverage area</span>
                                                              @elseif ($lead->lead_status == 7)
                                                                  <span class="badge badge-secondary">Not interested</span>
                                                              @elseif ($lead->lead_status == 8)
                                                                  <span class="badge badge-success">Demo</span>
                                                              @elseif ($lead->lead_status == 9)
                                                                  <span class="badge badge-success">1 Legger</span>
                                                              @elseif ($lead->lead_status == 10)
                                                                  <span class="badge badge-success">Not Home</span>
                                                              @elseif ($lead->lead_status == 11)
                                                                  <span class="badge badge-success">Pending Sales</span>
                                                              @endif
                                                            </span>
                                                            <div class="" style="display:inline;">
                                                              <i class="fas fa-edit cursor-pointer ml-1" id="leadupdatestatus_{{$lead->id}}_{{$lead->team_id}}_{{$Count}}" onclick="showTaskLeadUpdateStatus(this.id);"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                        @if($Role == 2)
                                                            <button type="submit" id="markascompleted{{$Count}}"
                                                                    class="btn btn-outline-primary float-right mr-2" disabled>
                                                                Mark as Completed
                                                            </button>
                                                        @else
                                                            <button type="submit" id="markascompleted{{$Count}}"
                                                                    class="btn btn-outline-primary float-right mr-2" disabled>
                                                                Mark as Completed
                                                            </button>
                                                        @endif
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{--Lead History Notes Details--}}
                                    <div class="col-md-4 mb-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <form>
                                                    <input type="hidden" name="id" id="id{{$Count}}" value="{{$lead->id}}"/>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3 mt-3">
                                                            <label for="history_note{{$Count}}">History Note</label>
                                                            <textarea class="form-control" id="history_note{{$Count}}" name="history_note"
                                                                      rows="2"></textarea>
                                                        </div>
                                                        <div class="ml-3" style="color:green; font-size: 12px;display:none;"
                                                             id="history_note_msg{{$Count}}"></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 text-right mt-1">
                                                            <button type="button" class="btn btn-primary" id="saveHistoryNote_{{$Count}}" onclick="SaveTaskHistoryNote(this.id);">
                                                                Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            Lead History
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table id="lead_historynotes_table{{$Count}}" class="table w-100">
                                                                <thead>
                                                                <tr>
                                                                    <th>Sr. No.</th>
                                                                    <th>User</th>
                                                                    <th>History Note</th>
                                                                    <th>Created At</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Lead History Notes -->
                            </div>
                            <!-- End -->
                        </div>
                        <?php
                        $Count++;
                        ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.leadTaskUpdateStatusModal')
@endsection
