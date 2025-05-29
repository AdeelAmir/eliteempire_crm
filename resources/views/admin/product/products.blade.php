@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ALL Produts</span></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            @if($Role == 1)
            <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
            onclick="window.location.href='{{url('admin/add/product')}}';">
            <i class="fas fa-plus-square mr-1"></i>
            Add New Product
        </button>
        @elseif($Role == 2)
        <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
        onclick="window.location.href='{{url('manager/add/user')}}';">
        <i class="fas fa-plus-square mr-1"></i>
        Add New User
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">
                    Details
                </h6>
                <div class="table-responsive">
                    <table id="admin_product_table" class="table w-100">
                        <thead>
                            <tr>
                                <th style="width:10%;">Sr. No.</th>
                                <th style="width:70%;">Name</th>
                                <th style="width:20%;">Action</th>
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
@include('admin.includes.deleteUserModal')
<!-- @include('admin.includes.changePasswordModal')  -->
@endsection