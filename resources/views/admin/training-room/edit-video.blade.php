@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="editTrainingRoomVideoPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Training Room Video</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/training-room/folder/details/' . $FolderId . '/' . $RoleId)}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        @if(Session::get('user_role') == 1)
            <form action="{{url('admin/training-room/video/update')}}" method="post" id="editVideoForm" enctype="multipart/form-data">
        @endif
              @csrf
              <input type="hidden" name="id" id="training_room_video_id" value="{{$video['id']}}" />
              <input type="hidden" name="training_room_role_id" id="training_room_role_id" value="{{$RoleId}}" />
              <input type="hidden" name="training_room_folder_id" id="training_room_folder_id" value="{{$FolderId}}" />
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
                                        Edit Video
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-3">
                                            <label for="title">Title*</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                   placeholder="Enter Video Title" value="{{$video['title']}}" required/>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-3">
                                            <label for="link">Link*</label>
                                            <textarea name="link" rows="5" class="form-control" required>{{$video['video_url']}}</textarea>
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
