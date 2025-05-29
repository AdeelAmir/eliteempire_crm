@extends('admin.layouts.app')
@section('content')

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Edit Team</span></h4>
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
        <form action="{{url('admin/teams/update')}}" method="post" id="updateTeamForm" enctype="multipart/form-data">
        @elseif(Session::get('user_role') == 2)
        <form action="{{url('general_manager/teams/update')}}" method="post" id="updateTeamForm" enctype="multipart/form-data">
        @endif
            @csrf
            <input type="hidden" name="id" value="{{$Id}}">
            <div class="row" id="editTeamPage">
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
                                    <label for="editTeamType">Team Type*</label>
                                    <select class="form-control" name="editTeamType" id="addTeamType" onchange="checkTeamType();" required>
                                      <option value="">Select Team Type</option>
                                      <option value="1" <?php if($Teams[0]->team_type == 1){echo "selected";} ?> >Representative</option>
                                      <option value="2" <?php if($Teams[0]->team_type == 2){echo "selected";} ?> >Confirmation Agent</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="editTeamName">Team Name*</label>
                                    <input type="text" name="editTeamName" id="editTeamName" class="form-control"
                                           placeholder="Team Name" value="{{$Teams[0]->title}}" required/>
                                </div>
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="editTeamManager">Manager*</label>
                                    <select name="editTeamManager" id="editTeamManager" class="form-control">
                                        <option value="">Select Team Manager</option>
                                        @foreach($Managers as $manager)
                                            <option value="{{$manager->id}}" <?php if($Teams[0]->team_manager == $manager->id){echo "selected";} ?> >{{$manager->firstname . ' ' . $manager->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($Teams[0]->team_type == 1)
                                <div class="col-md-4 mb-3 mt-3" id="_supervisorBlock">
                                    <label for="editTeamSupervisorTeamLead">Supervisor*</label>
                                    <select name="editTeamSupervisor" id="editTeamSupervisor" class="form-control">
                                        <option value="">Select Team Supervisor</option>
                                        @foreach($Supervisors as $supervisor)
                                          @if($Teams[0]->team_supervisor == $supervisor->id)
                                            <option value="{{$supervisor->id}}" selected>{{$supervisor->firstname . ' ' . $supervisor->lastname}}</option>
                                          @else
                                            <option value="{{$supervisor->id}}">{{$supervisor->firstname . ' ' . $supervisor->lastname}}</option>
                                          @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_representativeBlock">
                                    <label for="editTeamMembers">Team Members*</label>
                                    <select name="editTeamMembers[]" id="editTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        <?php
                                            $Members = explode(',', $Teams[0]->members);
                                            foreach ($Users as $user){
                                                if(in_array($user->id, $Members)){
                                                    echo '<option value="' . $user->id . '" selected>' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                                else{
                                                    echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_confirmationAgentBlock" style="display:none;">
                                    <label for="editConfirmationAgentTeamMembers">Confirmation Agent Team Members*</label>
                                    <select name="editConfirmationAgentTeamMembers[]" id="editConfirmationAgentTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        <?php
                                            $Members = explode(',', $Teams[0]->members);
                                            foreach ($ConfirmationAgent as $user){
                                                if(in_array($user->id, $Members)){
                                                    echo '<option value="' . $user->id . '" selected>' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                                else{
                                                    echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                @elseif($Teams[0]->team_type == 2)
                                <div class="col-md-4 mb-3 mt-3" id="_supervisorBlock" style="display:none;">
                                    <label for="editTeamSupervisorTeamLead">Supervisor*</label>
                                    <select name="editTeamSupervisor" id="editTeamSupervisor" class="form-control">
                                        <option value="">Select Team Supervisor</option>
                                        @foreach($Supervisors as $supervisor)
                                          @if($Teams[0]->team_supervisor == $supervisor->id)
                                            <option value="{{$supervisor->id}}" selected>{{$supervisor->firstname . ' ' . $supervisor->lastname}}</option>
                                          @else
                                            <option value="{{$supervisor->id}}">{{$supervisor->firstname . ' ' . $supervisor->lastname}}</option>
                                          @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_representativeBlock" style="display:none;">
                                    <label for="editTeamMembers">Team Members*</label>
                                    <select name="editTeamMembers[]" id="editTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        <?php
                                            $Members = explode(',', $Teams[0]->members);
                                            foreach ($Users as $user){
                                                if(in_array($user->id, $Members)){
                                                    echo '<option value="' . $user->id . '" selected>' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                                else{
                                                    echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mt-3" id="_confirmationAgentBlock">
                                    <label for="editConfirmationAgentTeamMembers">Confirmation Agent Team Members*</label>
                                    <select name="editConfirmationAgentTeamMembers[]" id="editConfirmationAgentTeamMembers" class="form-control" multiple>
                                        <option value="" disabled="disabled">Select Members</option>
                                        <?php
                                            $Members = explode(',', $Teams[0]->members);
                                            foreach ($ConfirmationAgent as $user){
                                                if(in_array($user->id, $Members)){
                                                    echo '<option value="' . $user->id . '" selected>' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                                else{
                                                    echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <input type="submit" class="btn btn-primary w-10" id="addTeamSubmitButton" value="Save" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
