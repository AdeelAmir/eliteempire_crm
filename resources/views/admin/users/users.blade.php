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
    <div class="page-content" id="usersPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">ALL USERS</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                            data-toggle="tooltip" title="Action" onclick="HandleUserAction();">
                        <i class="fa fa-tasks mr-1"></i>
                    </button>

                    <!-- Delete,Upgrade,Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Delete Selected Users" onclick="DeleteMultipleUsers();" id="deleteAllUsersBtn">
                        <i class="fas fa-trash mr-1"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Upgrade Selected Users" onclick="UpgradeMultipleUsers();" id="upgradeAllUsersBtn">
                        <i class="fas fa-level-up-alt mr-1"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Broadcast Selected Users" onclick="BroadcastMultipleUsers();"  id="broadcastAllUsersBtn">
                        <i class="fa fa-broadcast-tower mr-1"></i>
                    </button>
                    <!-- Delete,Upgrade,Broadcast -->

                    <?php
                        $Url = url('admin/announcements');
                    ?>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                            data-toggle="tooltip" title="Announcement"
                            onclick="window.location.href='{{$Url}}';">
                        <i class="fa fa-microphone mr-1"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            data-toggle="tooltip" title="Add New User"
                            onclick="window.location.href='{{url('admin/add/user')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                            data-toggle="tooltip" title="Action" onclick="HandleUserAction();">
                        <i class="fa fa-tasks mr-1"></i>
                    </button>

                    <!-- Delete,Upgrade,Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Delete Selected Users" onclick="DeleteMultipleUsers();" id="deleteAllUsersBtn">
                        <i class="fas fa-trash mr-1"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Upgrade Selected Users" onclick="UpgradeMultipleUsers();" id="upgradeAllUsersBtn">
                        <i class="fas fa-level-up-alt mr-1"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Broadcast Selected Users" onclick="BroadcastMultipleUsers();"  id="broadcastAllUsersBtn">
                        <i class="fa fa-broadcast-tower mr-1"></i>
                    </button>
                    <!-- Delete,Upgrade,Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            data-toggle="tooltip" title="Add New User"
                            onclick="window.location.href='{{url('global_manager/add/user')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 3)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                            data-toggle="tooltip" title="Action" onclick="HandleUserAction();">
                        <i class="fa fa-tasks mr-1"></i>
                    </button>
                    <!-- Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Broadcast Selected Users" onclick="BroadcastMultipleUsers();"  id="broadcastAllUsersBtn">
                        <i class="fa fa-broadcast-tower mr-1"></i>
                    </button>
                    <!-- Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            data-toggle="tooltip" title="Add New User"
                            onclick="window.location.href='{{url('acquisition_manager/add/user')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 4)
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                            data-toggle="tooltip" title="Action" onclick="HandleUserAction();">
                        <i class="fa fa-tasks mr-1"></i>
                    </button>
                    <!-- Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                            data-toggle="tooltip" title="Broadcast Selected Users" onclick="BroadcastMultipleUsers();"  id="broadcastAllUsersBtn">
                        <i class="fa fa-broadcast-tower mr-1"></i>
                    </button>
                    <!-- Broadcast -->
                    <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0"
                            data-toggle="tooltip" title="Add New User"
                            onclick="window.location.href='{{url('disposition_manager/add/user')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
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
            <div class="col-12 col-md-1" id="beforeTablePage"></div>
            <div class="col-12 col-md-10 grid-margin stretch-card" id="tablePage">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                            <button class="btn btn-primary float-right mb-3" data-toggle="tooltip" title="Filter" onclick="UserFilterBackButton();">
                              <i class="fa fa-filter" aria-hidden="true"></i>
                            </button>
                        </h6>
                        <div class="table-responsive">
                            <form action="{{url('')}}" method="post" enctype="multipart/form-data" id="usersForm">
                              @csrf
                              @include('admin.includes.deleteUserModal')
                              @include('admin.includes.upgradeUserAccountModal')
                              @include('admin.includes.userBroadcastModal')
                              <table id="admin_users_table" class="table w-100">
                                  <thead>
                                  <tr>
                                      <!--<th class="allUsersActionCheckBoxColumn">-->
                                      <!--    <input type="checkbox" name="checkAllBox" class="allUsersCheckBox" id="checkAllBox"-->
                                      <!--           onchange="CheckAllUserRecords(this);"/>-->
                                      <!--</th>-->
                                      <th>
                                          <input type="checkbox" name="checkAllBox" class="allUsersCheckBox" id="checkAllBox"
                                                 onchange="CheckAllUserRecords(this);"/>
                                      </th>
                                      <th style="width: 5%;">#</th>
                                      <th style="width: 25%;">User Information</th>
                                      <th style="width: 25%;">Contact</th>
                                      <th style="width: 10%;">Status</th>
                                      <th style="width: 35%;">Action</th>
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
            <div class="col-12 col-md-1 grid-margin stretch-card" id="filterPage" style="display:none;">
              <div class="card">
                  <div class="card-body">
                      <h6 class="card-title">
                          Filter
                          <i class="fa fa-window-close float-right" style="font-size: 16px;
                          cursor: pointer;" aria-hidden="true" onclick="UserFilterCloseButton();"></i>
                      </h6>
                      <div class="row">
                          <div class="col-md-12 mt-2 mb-3" id="filterCityBlock" style="display:none;">
                            <label for="filter_city">City</label>
                            <select class="form-control" name="filter_city" id="filter_city">
                              <option value="">Select</option>
                            </select>
                          </div>
                          <div class="col-md-12 mt-2 mb-3">
                            <label for="filter_state">State</label>
                            <select class="form-control" name="filter_state" id="filter_state" onchange="LoadStateCountyCity();">
                              <option value="">Select</option>
                              @foreach($states as $state)
                                <option value="{{$state->name}}">{{$state->name}}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-12 mt-2 mb-3">
                            <label for="filter_status">Status</label>
                            <select class="form-control" name="filter_status" id="filter_status">
                              <option value="1">Active</option>
                              <option value="0">Ban</option>
                            </select>
                          </div>
                          <div class="col-md-12 mt-2 mb-3">
                            <label for="filter_role">Role</label>
                            <select class="form-control" name="filter_role" id="filter_role">
                              <option value="">Select</option>
                              @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->title}}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-12 text-right">
                              <button class="btn btn-primary" onclick="MakeUsersTable();">Filter
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
    @include('admin.includes.changePasswordModal')
    @include('admin.includes.userBanModal')
    @include('admin.includes.userActivityModal')
@endsection
