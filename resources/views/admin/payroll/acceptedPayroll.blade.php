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

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="approve_payroll_table" class="table w-100">
                               <thead>
                                  <tr>
                                      <th>Sr. No.</th>
                                      <th>Name</th>
                                      <th>Account</th>
                                      <th>Lead Number</th>
                                      <th>Payout Type</th>
                                      <th>Earning</th>
                                      <th>Bonus</th>
                                      <th>Edit Earning</th>
                                      <th>Add Bonus</th>
                                      <th>Approve</th>
                                      <th>Reject</th>
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
    @include('admin.includes.bonusPayrollModal')
    @include('admin.includes.approvePayrollModal')
    @include('admin.includes.editEarningPayrollModal')
@endsection
