@extends('admin.layouts.app')
@section('content')

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">New Team</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/teams')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('general_manager/teams')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>
        @if(Session::get('user_role') == 1)
        <form action="{{url('admin/teams/store')}}" method="post" id="addTeamForm" enctype="multipart/form-data">
        @elseif(Session::get('user_role') == 2)
        <form action="{{url('general_manager/teams/store')}}" method="post" id="addTeamForm" enctype="multipart/form-data">
        @endif
            @csrf
            <div class="row" id="addTeamPage">
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

                {{--General Details--}}
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <br>
                            @endif
                            <h6 class="card-title">
                                General
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="addTeamType">Team Type*</label>
                                    <select class="form-control" name="addTeamType" id="addTeamType" onchange="checkTeamType();">
                                      <option value="">Select Team Type</option>
                                      <option value="1">Representative</option>
                                      <option value="2">Confirmation Agent</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="addTeamName">Team Name*</label>
                                    <input type="text" name="addTeamName" id="addTeamName" class="form-control"
                                           placeholder="Team Name" required/>
                                </div>
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="addTeamManager">Manager*</label>
                                    <select name="addTeamManager" id="addTeamManager" class="form-control">
                                        <option value="">Select Team Manager</option>
                                        @foreach($Managers as $manager)
                                            <option value="{{$manager->id}}">{{$manager->firstname . ' ' . $manager->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_supervisorBlock" style="display:none;">
                                    <label for="addTeamLead">Supervisor*</label>
                                    <select name="addTeamSupervisor" id="addTeamSupervisor" class="form-control">
                                        <option value="">Select Team Supervisor</option>
                                        @foreach($Supervisors as $supervisor)
                                            <option value="{{$supervisor->id}}">{{$supervisor->firstname . ' ' . $supervisor->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_representativeBlock" style="display:none;">
                                    <label for="addTeamMembers">Representative Team Members*</label>
                                    <select name="addTeamMembers[]" id="addTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        @foreach($Users as $user)
                                            <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_confirmationAgentBlock" style="display:none;">
                                    <label for="addConfirmationAgentTeamMembers">Confirmation Agent Team Members*</label>
                                    <select name="addConfirmationAgentTeamMembers[]" id="addConfirmationAgentTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        @foreach($ConfirmationAgent as $user)
                                            <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <input type="submit" class="btn btn-primary w-10" id="addTeamSubmitButton" value="Add" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
