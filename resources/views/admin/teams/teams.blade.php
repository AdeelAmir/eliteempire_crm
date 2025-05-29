@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class=" mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ALL TEAMS</span></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            @if($Role == 1)
            <button type="button" class="btn btn-primary btn-icon-text mb-md-0" onclick="window.location.href='{{url('admin/teams/add')}}';">
                <i class="fas fa-plus-square mr-1"></i>
                New Team
            </button>
            @elseif($Role == 2)
            <button type="button" class="btn btn-primary btn-icon-text mb-md-0" onclick="window.location.href='{{url('general_manager/teams/add')}}';">
                <i class="fas fa-plus-square mr-1"></i>
                New Team
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
                        Teams
                    </h6>
                    <div class="table-responsive">
                        <table id="admin_teams_table" class="table w-100">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Team Type</th>
                                    <th>Title</th>
                                    <th>Team Supervisor</th>
                                    <th>Team Memebers</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
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
@include('admin.includes.deleteTeamModal')
@endsection
