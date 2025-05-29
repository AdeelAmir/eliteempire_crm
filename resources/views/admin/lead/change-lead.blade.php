@extends('admin.layouts.app')
@section('content')

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Change Lead Status</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('global_manager/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 3)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('confirmationAgent/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 4)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('supervisor/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 5)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('representative/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <?php
        $Url = "";
        if ($Role == 1) {
            $Url = url('admin/lead/update/status');
        } elseif ($Role == 2) {
            $Url = url('global_manager/lead/update/status');
        } elseif ($Role == 3) {
            $Url = url('confirmationAgent/lead/update/status');
        } elseif ($Role == 4) {
            $Url = url('supervisor/lead/update/status');
        } else {
            $Url = url('representative/lead/update/status');
        }
        ?>

        <form action="{{$Url}}" method="post" id="changeLeadStatusForm"
              enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$Lead[0]->id}}"/>

            <div class="row" id="changeLeadStatusPage">
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
                                <input type="hidden" name="team" id="team"
                                       value="{{$Lead[0]->team_id}}"/>
                                <div class="col-md-3 mb-3 mt-3">
                                    <label for="lead_status">Lead Status*</label>
                                    <select name="lead_status" id="lead_status" class="form-control"
                                            onchange="checkLeadStatus();" required>
                                        <option value="" selected>Select Status</option>
                                        <option value="1">Confirm</option>
                                        <option value="2">Cancelled</option>
                                        <option value="6">Out of Coverage Area</option>
                                        <option value="7">Not Interested</option>
                                        <option value="3">Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 mt-3" id="_companyBlock"
                                     style="display:none;">
                                    <label for="lead_company">Company</label>
                                    <select name="lead_company" id="lead_company"
                                            class="form-control">
                                        <option value="">Select Company</option>
                                        @foreach($Company as $c_details)
                                            <option value="{{$c_details->id}}">{{$c_details->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3 mt-3" id="_cancellationReasonBlock"
                                     style="display:none;">
                                    <label for="cancellation_reason">Cancellation Reason</label>
                                    <textarea class="form-control" id="cancellation_reason"
                                              name="cancellation_reason" rows="3"></textarea>
                                </div>
                                <div class="col-md-12 mb-3 mt-3" id="_confirmationReasonBlock"
                                     style="display:none;">
                                    <label for="confirmation_reason">Confirmation Reason</label>
                                    <textarea class="form-control" id="confirmation_reason"
                                              name="confirmation_reason" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <input type="submit" class="btn btn-primary w-10" value="Save"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection