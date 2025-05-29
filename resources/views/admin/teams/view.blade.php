@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="viewTeam">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Team Details</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                <button type="button" class="btn btn-primary"
                        onclick="window.location.href='{{url('supervisor/teams')}}';">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </button>
            </div>
        </div>

        <form action="#" method="post" id="viewTeamForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$Id}}">
            <div class="row" id="viewTeamPage">
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
                                    <label for="editTeamName">Team Name*</label>
                                    <input type="text" name="editTeamName" id="editTeamName" class="form-control"
                                           placeholder="Team Name" value="{{$Teams[0]->title}}" required/>
                                </div>
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="editTeamSupervisorTeamLead">Supervisor*</label>
                                    <select name="editTeamSupervisor" id="editTeamSupervisor" class="form-control" required>
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
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="editTeamMembers">Team Members*</label>
                                    <select name="editTeamMembers[]" id="editTeamMembers" class="form-control" multiple required>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
