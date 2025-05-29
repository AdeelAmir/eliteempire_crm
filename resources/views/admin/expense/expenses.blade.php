@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ALL Expense</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            onclick="window.location.href='{{url('admin/add/expenses')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                        Add New Expense
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            onclick="window.location.href='{{url('global_manager/add/expenses')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                        Add New Expense
                    </button>
                @endif
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
            <div class="col-md-1" id="beforeTablePage"></div>
            <div class="col-md-10 grid-margin stretch-card" id="tablePage">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                            <button class="btn btn-primary float-right mb-3" data-toggle="tooltip" title="Filter" onclick="FilterBackButton();">
                                <i class="fa fa-filter" aria-hidden="true"></i>
                            </button>
                        </h6>

                        <div class="table-responsive">
                            <table id="admin_expense_table" class="table w-100">
                                <thead>
                                <tr>
                                  <th style="width: 5%;">#</th>
                                  <th style="width: 25%;">Description</th>
                                  <th style="width: 5%;">Total</th>
                                  <th style="width: 5%;">Date</th>
                                  <th style="width: 10%;">Vendor</th>
                                  <th style="width: 10%;">Location</th>
                                  <th style="width: 30%;">Note</th>
                                  <th style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 grid-margin stretch-card" id="filterPage" style="display:none;">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Filter
                            <i class="fa fa-window-close float-right" style="font-size: 16px;
                                cursor: pointer;" aria-hidden="true" onclick="FilterCloseButton();"></i>
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="expense_start_date" value=""
                                       class="form-control">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="expense_end_date" value="" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <button type="button" name="button" class="btn btn-primary" onclick="MakeExpenseTable();">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.deleteExpensModal')
@endsection
