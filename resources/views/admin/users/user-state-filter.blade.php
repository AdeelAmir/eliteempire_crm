@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="userStateFilterPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">User State Filter</span></h4>
            </div>
        </div>

        <?php
        $UserLeadStatus = array();
        $UserState = array();
        $StartDate = "";
        $EndDate = "";

        if (count($UserDepartmentFilterDetails) && $UserDepartmentFilterDetails != "") {
            if ($UserDepartmentFilterDetails[0]->lead_status != "") {
                $UserLeadStatus = $UserDepartmentFilterDetails[0]->lead_status;
                $UserLeadStatus = explode(",", $UserLeadStatus);
            }
            if ($UserDepartmentFilterDetails[0]->state != "") {
                $UserState = $UserDepartmentFilterDetails[0]->state;
                $UserState = explode(",", $UserState);
            }
            if ($UserDepartmentFilterDetails[0]->start_date != "") {
                $StartDate = $UserDepartmentFilterDetails[0]->start_date;
            }
            if ($UserDepartmentFilterDetails[0]->end_date != "") {
                $EndDate = $UserDepartmentFilterDetails[0]->end_date;
            }
        }
        ?>

        <form action="{{url('admin/user/state/filter/store')}}" method="post" id="addUserStateFilterForm"
              enctype="multipart/form-data">
            @csrf
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
                <div class="col-md-3"></div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                Details
                            </h6>
                            <div class="row">
                                <input type="hidden" name="user_id" value="{{$UserId}}">
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="status">Lead Status</label>
                                    <select class="form-control" name="status[]" id="lead_status" multiple>
                                        <option value="">Select Status</option>
                                        <option value="1" <?php if (in_array(1, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Interested
                                        </option>
                                        <option value="2" <?php if (in_array(2, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Not Interested
                                        </option>
                                        <option value="3" <?php if (in_array(3, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Lead In
                                        </option>
                                        <option value="4" <?php if (in_array(4, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Do Not Call
                                        </option>
                                        <option value="5" <?php if (in_array(5, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >No Answer
                                        </option>
                                        <option value="7" <?php if (in_array(7, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Offer Not Given
                                        </option>
                                        <option value="8" <?php if (in_array(8, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Offer Not Accepted
                                        </option>
                                        <option value="9" <?php if (in_array(9, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Accepted
                                        </option>
                                        <option value="10" <?php if (in_array(10, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Negotiating with Seller
                                        </option>
                                        <option value="11" <?php if (in_array(11, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Agreement Sent
                                        </option>
                                        <option value="12" <?php if (in_array(12, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Agreement Received
                                        </option>
                                        <option value="13" <?php if (in_array(13, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Send To Investor
                                        </option>
                                        <option value="14" <?php if (in_array(14, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Negotiation with Investors
                                        </option>
                                        <option value="15" <?php if (in_array(15, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Sent to Title
                                        </option>
                                        <option value="16" <?php if (in_array(16, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Send Contract to Investor
                                        </option>
                                        <option value="17" <?php if (in_array(17, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >EMD Received
                                        </option>
                                        <option value="18" <?php if (in_array(18, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >EMD Not Received
                                        </option>
                                        <option value="21" <?php if (in_array(21, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Closed WON
                                        </option>
                                        <option value="22" <?php if (in_array(22, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Deal Lost
                                        </option>
                                        <option value="23" <?php if (in_array(23, $UserLeadStatus)) {
                                            echo "selected";
                                        } ?> >Wrong Number
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="state">State</label>
                                    <select class="form-control" name="state[]" id="state" multiple>
                                        @foreach($states as $state)
                                            <option value="{{$state->name}}" <?php if (in_array($state->name, $UserState)) {
                                                echo "selected";
                                            } ?> >{{$state->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="startDateFilter">Start Date</label>
                                    <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="startDateFilter">
                                        <input class="form-control" size="16" type="text" value="">
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="startDateFilter" name="startDateFilter" value=""
                                           required/>
                                </div>

                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="endDateFilter" class="w-100">End Date
                                        <span class="badge badge-danger float-right cursor-pointer"
                                              onclick="ResetEndDateFilter();">Reset</span>
                                    </label>
                                    <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="endDateFilter">
                                        <input class="form-control" size="16" type="text" id="_endDateFilter"
                                               value="">
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="endDateFilter" name="endDateFilter" value=""/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center mt-3">
                                    <input type="submit" class="btn btn-primary w-15"
                                           name="submitUserStateFilterForm"
                                           id="submitUserStateFilterForm" value="Update"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </form>
    </div>
@endsection