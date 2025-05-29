@extends('admin.layouts.app')
@section('content')
<style media="screen">
@media only screen and (min-width: 768px) {
  div.dataTables_wrapper div.dataTables_filter {
    text-align: right;
    margin-right: 170px;
  }
  .table-responsive {
    display: block;
    width: 100%;
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">ALL BUSINESS ACCOUNTS</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                        data-toggle="tooltip" title="Action" onclick="HandleBuisnessAccountAction();">
                    <i class="fa fa-tasks mr-1"></i>
                </button>
                <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                        data-toggle="tooltip" title="Delete Selected Accounts" onclick="DeleteMultipleAccounts();" id="deleteAllAccountsBtn">
                    <i class="fas fa-trash mr-1"></i>
                </button>
                <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                        data-toggle="tooltip" title="Add New Account"
                        onclick="checkBuisnessAccountType();">
                    <i class="fas fa-plus-square mr-1"></i>
                </button>
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
                          <form action="{{url('')}}" method="post" enctype="multipart/form-data" id="accountsForm">
                            @csrf
                            @include('admin.includes.deleteUserModal')
                            <table id="admin_buissness_account_table" class="table w-100">
                                <thead>
                                  <tr>
                                      <!--<th class="allAccountsActionCheckBoxColumn">-->
                                      <!--    <input type="checkbox" name="checkAllBox" class="allAccountsCheckBox" id="checkAllBox"-->
                                      <!--           onchange="CheckAllAccountsRecord(this);"/>-->
                                      <!--</th>-->
                                      <th>
                                          <input type="checkbox" name="checkAllBox" class="allAccountsCheckBox" id="checkAllBox"
                                                 onchange="CheckAllAccountsRecord(this);"/>
                                      </th>
                                      <th style="width: 5%;">#</th>
                                      <th style="width: 20%;"><?php echo wordwrap("User Information", 10, "<br>"); ?></th>
                                      <th style="width: 20%;">Contact</th>
                                      <th style="width: 5%;">Status</th>
                                      <th style="width: 50%;">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-2"></div>
        </div>
    </div>
    @include('admin.includes.changePasswordModal')
    @include('admin.includes.userBanModal')
    @include('admin.includes.userActivityModal')
    @include('admin.includes.buisnessAccountTypeModal')
@endsection
