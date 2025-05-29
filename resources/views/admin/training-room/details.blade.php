@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="TrainingRoomDetailsPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">Training Room</span> : <span class="text-primary">{{$TrainingRoomRole}}</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
              <button type="button" class="btn btn-primary btn-icon-text float-right mb-2 mb-md-0"
                      onclick="openTrainingRoomTypeModal();">
                  <i class="fas fa-plus-square mr-1"></i>
                  Add New
              </button>
              <button type="button" class="btn btn-primary ml-2"
                      onclick="window.location.href='{{url('admin/training-room/folders/' . $RoleId)}}';">
                  <i class="fas fa-arrow-left mr-1"></i>
                  Back
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
            <!-- Role -->
            <input type="hidden" name="training_room_folder_id" id="training_room_folder_id" value="{{$FolderId}}">
            <input type="hidden" name="training_room_role_id" id="training_room_role_id" value="{{$RoleId}}">

            <!-- Training Room - Start -->
            <div class="col-md-2"></div>
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_training_room" class="table w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 10%;">Type</th>
                                    <th style="width: 56%;">Title</th>
                                    <th style="width: 33%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
            <!-- Training Room - End -->
        </div>
    </div>

    @include('admin.includes.trainingRoomTypeModal')
    @include('admin.includes.deleteTrainingRoomModal')
    @include('admin.includes.copyTrainingRoomItemModal')
@endsection
