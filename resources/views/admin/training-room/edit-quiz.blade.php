@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="editTrainingRoomQuizPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Training Room Quiz</span></h4>
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

        <?php
            $TopicId = $Data[0];
            $Topic = $Data[1];
            $TrainingRoomRoleId = $Data[2];
            $Questions = $Data[3];
        ?>

        @if(Session::get('user_role') == 1)
            <form action="{{url('admin/training-room/quiz/update')}}" method="post" id="editQuizForm" enctype="multipart/form-data">
        @endif
              @csrf
              <input type="hidden" name="id" id="training_room_quiz_id" value="{{$quiz['id']}}" />
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
                                        Edit Quiz
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-1">
                                            <label for="title"><strong>Title</strong></label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                   placeholder="Enter Quiz Title" value="{{$quiz['title']}}" required/>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-1">
                                            <div class="repeater-custom-show-hide">
                                                <div data-repeater-list="questions">
                                                    @foreach($Questions as $question)
                                                        <div data-repeater-item="" style="" class="mb-3">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="">Question</label>
                                                                                        <input type="text" name="add_quiz_question" class="form-control" autocomplete="off" value="{{$question->question}}" required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 1</label>
                                                                                        <input type="text" name="add_quiz_choice1" class="form-control" autocomplete="off" value="{{$question->choice1}}" required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 2</label>
                                                                                        <input type="text" name="add_quiz_choice2" class="form-control" autocomplete="off" value="{{$question->choice2}}" required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 3</label>
                                                                                        <input type="text" name="add_quiz_choice3" class="form-control" autocomplete="off" value="{{$question->choice3}}" required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 4</label>
                                                                                        <input type="text" name="add_quiz_choice4" class="form-control" autocomplete="off" value="{{$question->choice4}}" required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Answer</label>
                                                                                        <select name="add_quiz_answer" class="form-control" required>
                                                                                            @if($question->answer == 1)
                                                                                                <option value="" disabled="disabled">Select Answer</option>
                                                                                                <option value="1" selected>Choice 1</option>
                                                                                                <option value="2">Choice 2</option>
                                                                                                <option value="3">Choice 3</option>
                                                                                                <option value="4">Choice 4</option>
                                                                                            @elseif($question->answer == 2)
                                                                                                <option value="" disabled="disabled">Select Answer</option>
                                                                                                <option value="1">Choice 1</option>
                                                                                                <option value="2" selected>Choice 2</option>
                                                                                                <option value="3">Choice 3</option>
                                                                                                <option value="4">Choice 4</option>
                                                                                            @elseif($question->answer == 3)
                                                                                                <option value="" disabled="disabled">Select Answer</option>
                                                                                                <option value="1">Choice 1</option>
                                                                                                <option value="2">Choice 2</option>
                                                                                                <option value="3" selected>Choice 3</option>
                                                                                                <option value="4">Choice 4</option>
                                                                                            @elseif($question->answer == 4)
                                                                                                <option value="" disabled="disabled">Select Answer</option>
                                                                                                <option value="1">Choice 1</option>
                                                                                                <option value="2">Choice 2</option>
                                                                                                <option value="3">Choice 3</option>
                                                                                                <option value="4" selected>Choice 4</option>
                                                                                            @endif
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                <span data-repeater-delete="" class="btn btn-outline-danger btn-sm float-right deletePayeeBtn">
                                                                                    <span class="far fa-trash-alt mr-1"></span>&nbsp;
                                                                                    Delete
                                                                                </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="col-sm-12">
                                                        <span data-repeater-create="" class="btn btn-outline-success btn-sm float-right">
                                                            <span class="fa fa-plus"></span>&nbsp;
                                                            Add
                                                        </span>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
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
