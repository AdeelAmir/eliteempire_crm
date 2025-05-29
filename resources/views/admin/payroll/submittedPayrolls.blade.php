@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="PayrollPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Approve Payroll</span></h4>
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

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Approve Payroll
                        </h6>
                        <div class="table-responsive">
                            <table id="submitted_payroll_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Commissions</th>
                                    <th>Bonuses</th>
                                    <th>Gross Income</th>
                                    <th>Taxes Amount</th>
                                    <th>Draw Balance</th>
                                    <th>Net Income</th>
                                    <th>View</th>
                                    <th>GENERATE PAYROLL</th>
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
    @include('admin.includes.approvePayrollModal')
    @include('admin.includes.userPayrollBreakdownModal')
@endsection