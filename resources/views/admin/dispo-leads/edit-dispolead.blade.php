@extends('admin.layouts.app')
@section('content')

<div class="page-content" id="editCallRequest">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Edit Dispo Lead</span></h4>
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
<form action="{{url('admin/dispo-lead/update')}}" method="post" id="editLeadForm" enctype="multipart/form-data">
    @elseif($Role == 2)
    <form action="{{url('general_manager/dispo-lead/update')}}" method="post" id="editLeadForm" enctype="multipart/form-data">
        @elseif($Role == 3)
        <form action="{{url('confirmationAgent/dispo-lead/update')}}" method="post" id="editLeadForm" enctype="multipart/form-data">
            @elseif($Role == 4)
            <form action="{{url('supervisor/dispo-lead/update')}}" method="post" id="editLeadForm" enctype="multipart/form-data">
                @elseif($Role == 5)
                <form action="{{url('representative/dispo-lead/update')}}" method="post" id="editLeadForm" enctype="multipart/form-data">
                    @endif
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$Lead[0]->id}}" />
                    <input type="hidden" name="electricbill_Old" id="electricbill_Old" value="{{$Lead[0]->electricbill}}"/>
                    <div class="row" id="editLeadPage">
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
                                        <input type="hidden" name="team" id="team" value="{{$Lead[0]->team_id}}" />
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="firstName">First Name*</label>
                                            <input type="text" name="firstName" id="firstName" class="form-control"
                                            placeholder="First Name" value="{{$Lead[0]->firstname}}" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="lastName">Last Name*</label>
                                            <input type="text" name="lastName" id="lastName" class="form-control"
                                            placeholder="Last Name" value="{{$Lead[0]->lastname}}" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="phone">Phone Number</label>
                                            <input type="text" name="phone" id="phone" class="form-control"
                                            placeholder="Enter Your Phone Number" maxlength="20"
                                            value="{{$Lead[0]->phone}}" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="appointmenttime">Appointment Time*</label>
                                            <div class="input-group date form_datetime" data-date-format="mm/dd/yyyy - HH:ii p"
                                            data-link-field="appointmenttime">
                                            <input class="form-control" size="16" type="text"
                                            value="{{$AppointmentTime}}" <?php if ($Role == 4 || $Role == 5) {
                                                if ($Lead[0]->lead_status == 1 || $Lead[0]->lead_status == 4 || $Lead[0]->lead_status == 5) {
                                                    echo "disabled";
                                                }
                                            } ?> />
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                            <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="appointmenttime" name="appointmenttime"
                                            value="{{$Lead[0]->appointment_time}}" required/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="product">Product*</label>
                                            <select name="product" id="product" class="form-control" onchange="checkProduct();"
                                            required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                            @if($Lead[0]->product == $product->id)
                                            <option value="{{$product->id}}" selected>{{$product->name}}</option>
                                            @else
                                            <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                       </div>
                                       @if($Lead[0]->product == 6)
                                       <div class="col-md-3 mb-3 mt-3" id="_ProductDescriptionBlock">
                                           <label for="product_desc">Product Description</label>
                                           <input type="text" name="product_desc" value="{{$Lead[0]->product_desc}}"
                                           id="product_desc" class="form-control"
                                           placeholder="Enter Product Description"/>
                                       </div>
                                       @else
                                       <div class="col-md-3 mb-3 mt-3" id="_ProductDescriptionBlock" style="display:none;">
                                           <label for="product_desc">Product Description</label>
                                           <input type="text" name="product_desc" id="product_desc" class="form-control"
                                           placeholder="Enter Product Description"/>
                                       </div>
                                       @endif

                                       @if($Lead[0]->product == 5)
                                       <div class="col-md-3 mb-3 mt-3" id="_electricbillblock">
                                           {{-- Give Download Option For Roles 1,2,3 --}}
                                           @if($Role == 1 || $Role == 2 || $Role == 3)
                                           @if($Lead[0]->electricbill != "")
                                           <label class="w-100">Electric Bill <a class="text-black"
                                             href="<?php echo asset('storage/app/public/leads/' . $Lead[0]->electricbill); ?>"
                                             download><i
                                             class="fa fa-download float-right"></i></a></label>
                                             @else
                                             <label for="electricbill">Electric Bill</label>
                                             @endif
                                             @else
                                             <label for="electricbill">Electric Bill</label>
                                             @endif
                                             <input type="file" name="electricbill" id="electricbill" class="form-control"
                                             accept="image/jpeg, image/png, image/jpg, application/pdf"/>
                                         </div>
                                         @else
                                         <div class="col-md-3 mb-3 mt-3" id="_electricbillblock" style="display: none;">
                                           <label for="electricbill">Electric Bill</label>
                                           <input type="file" name="electricbill" id="electricbill" class="form-control"
                                           accept="image/jpeg, image/png, image/jpg, application/pdf"/>
                                       </div>
                                       @endif

                                       <div class="col-md-3 mb-3 mt-3">
                                          <label for="split">Split</label>
                                          <select name="split" id="split" class="form-control">
                                              <option value="">Select</option>
                                              @foreach($SplitOptions as $item)
                                              @if($Lead[0]->split == $item->id)
                                              <option value="{{$item->id}}" selected>{{$item->firstname . ' ' . $item->lastname}}</option>
                                              @else
                                              <option value="{{$item->id}}">{{$item->firstname . ' ' . $item->lastname}}</option>
                                              @endif
                                              @endforeach
                                          </select>
                                       </div>
                                       <div class="col-md-3 mb-3 mt-3" id="_emailblock">
                                            <label for="email">Created at</label>
                                            <input type="datetime-local" name="created_at" id="email" class="form-control"
                                            placeholder="DateTime"  value="{{date('Y-m-d\TH:i',strtotime($Lead[0]->created_at))}}" readonly />
                                       </div>
                                       <div class="col-md-12 mb-3 mt-3">
                                            <label for="note">Note</label>
                                            <textarea class="form-control" id="note" name="note" rows="3">{{$Lead[0]->note}}</textarea>
                                       </div>
                                     </div>
                                     <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <input type="submit" class="btn btn-primary w-10" value="Submit" />
                                        </div>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            {{--General Details--}}
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form>
                            <input type="hidden" name="id" id="id" value="{{$Lead[0]->id}}"/>
                            <div class="row">
                                <div class="col-md-12 mb-3 mt-3">
                                    <label for="note">History Note</label>
                                    <textarea class="form-control" id="history_note" name="history_note"
                                    rows="3"></textarea>
                                </div>
                                <div class="ml-3" style="color:green; font-size: 12px;display:none;"
                                id="history_note_msg"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <button type="button" class="btn btn-primary w-10" onclick="SaveCallHistoryNote();">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Lead History Notes -->
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Call Lead History
                        </h6>
                        <div class="table-responsive">
                            <table id="lead_historynotes_table" class="table w-100">
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
            @endsection
