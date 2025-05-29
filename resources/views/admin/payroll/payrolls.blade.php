@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="PayrollPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">All Payroll</span></h4>
            </div>
        </div>

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

            <div class="col-md-12 grid-margin stretch-card" id="payrollFilterPage">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Filter
                        </h6>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3 mb-3 mt-3">
                                <label for="startDateFilter">Start Date</label>
                                <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="startDateFilter">
                                    <input class="form-control" size="16" type="text" value="{{$PayrollStartDate}}">
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="startDateFilter" name="startDateFilter" value="{{$PayrollStartDate}}" required/>
                                <div class="mt-2" style="font-size: 12px; color: red;" id="_error"></div>
                            </div>

                            <div class="col-md-3 mt-3">
                                <label for="endDateFilter">End Date</label>
                                <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="endDateFilter">
                                    <input class="form-control" size="16" type="text" value="{{$PayrollEndDate}}">
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="endDateFilter" name="endDateFilter" value="{{$PayrollEndDate}}" required/>
                            </div>

                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary" onclick="MakeApprovePayrollTable();">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card" id="tablePayrollPage" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                            <button class="btn btn-primary float-right mb-3" onclick="PayrollFilterBackButton();">Back
                            </button>
                        </h6>
                        <div class="table-responsive">
                            <table id="approve_payroll_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Name</th>
                                    <th>Account</th>
                                    <th>Commission</th>
                                    <th>Bonus</th>
                                    <th>View</th>
                                    <th>Income</th>
                                    <th>Submit</th>
                                    <th>Cancel</th>
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
    @include('admin.includes.rejectPayrollModal')
    @include('admin.includes.submitPayrollModal')
    @include('admin.includes.userPayrollBreakdownModal')
    @include('admin.includes.incomePayrollModal')
@endsection
