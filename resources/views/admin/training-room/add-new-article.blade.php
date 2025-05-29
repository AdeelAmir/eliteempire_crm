@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="addTrainingRoomArticlePage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Training Room Article</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/training-room/details/' . $TrainingRoomRoleId)}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        @if(Session::get('user_role') == 1)
            <form action="{{url('admin/training-room/article/store')}}" method="post" id="addArticleForm" enctype="multipart/form-data">
        @endif
              @csrf
              <input type="hidden" name="training_room_role_id" id="training_room_role_id" value="{{$TrainingRoomRoleId}}" />
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

                          <div class="col-md-2"></div>
                          <div class="col-md-8 grid-margin stretch-card">
                              <div class="card">
                                  <div class="card-body">
                                    <h6 class="card-title">
                                        Add Article
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-1">
                                            <label for="title"><strong>Title</strong></label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                   placeholder="Enter Article Title" required/>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-1">
                                            <label for="add_article_details">Details</label>
                                            <textarea name="add_article_details" id="add_article_details" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <button type="submit" class="btn btn-primary w-20">
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection
