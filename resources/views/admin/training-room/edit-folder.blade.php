@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="editTrainingRoomFolderPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Training Room Folder</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/training-room/folders/' . $RoleId)}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        @if(Session::get('user_role') == 1)
            <form action="{{url('admin/training-room/folder/update')}}" method="post" id="editFolderForm" enctype="multipart/form-data">
        @endif
              @csrf
              <input type="hidden" name="id" id="training_room_folder_id" value="{{$folder['id']}}" />
              <input type="hidden" name="training_room_role_id" id="training_room_role_id" value="{{$RoleId}}" />
              <input type="hidden" name="oldFolderPicture" id="oldFolderPicture" value="{{$folder->picture}}"/>
              <section class="contact-area pb-5">
                  <div class="container">
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

                          <div class="col-md-3"></div>
                          <div class="col-md-6 grid-margin stretch-card">
                              <div class="card">
                                  <div class="card-body">
                                    <h6 class="card-title">
                                        Edit Folder
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-3">
                                            <label for="title"><strong>Name</strong></label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                   placeholder="Enter Folder Name" value="{{$folder['name']}}" required/>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-1">
                                            <label for=""><strong>Picture</strong></label>
                                            <input type="file" name="picture" id="picture" class="form-control"
                                                   accept="image/png, image/gif, image/jpeg" />
                                        </div>
                                        <div class="col-md-12 mb-3 mt-1">
                                            <label for=""><strong>Required</strong></label>
                                            <select class="form-control" name="required_status" id="required_status" required>
                                              <option value="0" <?php if($folder['required'] == "0"){echo "selected";} ?> >No</option>
                                              <option value="1" <?php if($folder['required'] == "1"){echo "selected";} ?> >Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <button type="submit" class="btn btn-primary w-20">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection
