@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">ALL INVESTORS</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0" onclick="window.location.href='{{url('admin/investor/add')}}';">
                    <i class="fas fa-plus-square mr-1"></i>
                    Add New Investor
                </button>
                @elseif($Role == 2)
                <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0" onclick="window.location.href='{{url('global_manager/investor/add')}}';">
                    <i class="fas fa-plus-square mr-1"></i>
                    Add New Investor
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
            <div class="col-12 col-md-2"></div>
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_investor_table" class="table w-100">
                                <thead>
                                  <tr>
                                      <th style="width: 5%;">#</th>
                                      <th style="width: 20%;">User Information</th>
                                      <th style="width: 25%;">Contact</th>
                                      <th style="width: 10%;">Status</th>
                                      <th style="width: 40%;">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-2"></div>
        </div>
    </div>
    @include('admin.includes.deleteUserModal')
    @include('admin.includes.changePasswordModal')
    @include('admin.includes.userBanModal')
    @include('admin.includes.userActivityModal')
@endsection
