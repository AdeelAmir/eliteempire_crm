@extends('admin.layouts.app')
@section('content')
    <style media="screen">
      .dropdown-menu{
        left: 330px !important;
      }
    </style>
    <div class="page-content" id="marketingReport">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">ELITE EMPIRE - <span class="text-primary">USERS REPORT</span></h4>
            </div>
        </div>

        <div class="row mb-5">
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
            <div class="col-md-3"></div>
            <div class="col-md-6 grid-margin stretch-card" id="filterUsersReportPage">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Filter
                        </h6>
                        <div class="row">
                           <div class="col-md-12 mb-3">
                             <label for="userFilter">User</label>
                             <select name="userFilter" id="userFilter" class="form-control">
                                 <option value="7">Cold Caller</option>
                                 <option value="3">Acquisition</option>
                                 <option value="4">Disposition</option>
                                 <option value="8">Affiliate</option>
                                 <option value="9">Realtor</option>
                             </select>
                           </div>

                           <div class="col-md-12 mb-3">
                              <label for="stateFilter">State</label>
                              <select name="stateFilter" id="stateFilter" class="form-control">
                                  <option value="0">All States</option>
                                  @if(isset($States))
                                      @foreach($States as $state)
                                          <option value="{{$state->name}}">{{$state->name}}</option>
                                      @endforeach
                                  @endif
                              </select>
                           </div>

                           <div class="col-md-12 mb-3">
                             <label for="searchBy">Search By</label>
                                <select name="searchBy" id="searchBy" class="form-control" onchange="CalculateDates(this.value);">
                                  <option value="0">Select</option>
                                  <option value="customRange">Custom Range</option>
                                  <option value="yesterday">Yesterday</option>
                                  <option value="today">Today</option>
                                  <option value="tomorrow">Tomorrow</option>
                                  <option value="lastWeek">Last Week</option>
                                  <option value="currentWeek">Current Week</option>
                                  <option value="nextWeek">Next Week</option>
                                  <option value="lastMonth">Last Month</option>
                                  <option value="currentMonth">Current Month</option>
                                  <option value="nextMonth">Next Month</option>
                                  <option value="lastYear">Last Year</option>
                                  <option value="CurrentYear">Current Year</option>
                                </select>
                           </div>

                            <div class="col-md-12 mb-3" id="customRangeStartDate" style="display: none;">
                                <label for="customStartDate">Start Date</label>
                                <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="customStartDate">
                                    <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="customStartDate" name="startDateFilter" required/>
                            </div>

                            <div class="col-md-12 mb-3" id="customRangeEndDate" style="display: none;">
                                <label for="customEndDate">End Date</label>
                                <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="customEndDate">
                                    <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="customEndDate" name="startDateFilter" required/>
                            </div>

                            <input type="hidden" name="searchStartDate" id="searchStartDate" value="" />
                            <input type="hidden" name="searchEndDate" id="searchEndDate" value="" />

                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary" onclick="FilterUsersReport();">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>

            <div class="col-md-12 grid-margin stretch-card" id="tableUsersReportPage" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Users Report
                            <button class="btn btn-primary float-right mb-3" onclick="UsersReportBackButton();">Back
                            </button>
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_users_report_table" class="table w-100">
                                <thead id="table_header">
                                    <tr>
                                      <td></td>
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
@endsection
