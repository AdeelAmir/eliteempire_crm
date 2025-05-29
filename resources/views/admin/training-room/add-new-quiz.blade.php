@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="addTrainingRoomQuizPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Training Room Quiz</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/training-room/folder/details/' . $FolderId . '/' . $TrainingRoomRoleId)}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        @if(Session::get('user_role') == 1)
            <form action="{{url('admin/training-room/quiz/store')}}" method="post" id="addQuizForm" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="training_room_role_id" id="training_room_role_id" value="{{$TrainingRoomRoleId}}"/>
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
                                            Add Quiz
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-12 mb-3 mt-1">
                                                <label for="title"><strong>Title</strong></label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                       placeholder="Enter Quiz Title" required/>
                                            </div>
                                            <div class="col-md-12 mb-3 mt-1">
                                                <div class="repeater-custom-show-hide">
                                                    <div data-repeater-list="questions">
                                                        <div data-repeater-item="" style="" class="mb-3">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="" class="add_quiz_question_label">Question 1</label>
                                                                                        <input type="text"
                                                                                               name="add_quiz_question"
                                                                                               class="form-control"
                                                                                               autocomplete="off"
                                                                                               required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 1</label>
                                                                                        <input type="text"
                                                                                               name="add_quiz_choice1"
                                                                                               class="form-control"
                                                                                               autocomplete="off"
                                                                                               required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 2</label>
                                                                                        <input type="text"
                                                                                               name="add_quiz_choice2"
                                                                                               class="form-control"
                                                                                               autocomplete="off"
                                                                                               required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 3</label>
                                                                                        <input type="text"
                                                                                               name="add_quiz_choice3"
                                                                                               class="form-control"
                                                                                               autocomplete="off"
                                                                                               required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Choice 4</label>
                                                                                        <input type="text"
                                                                                               name="add_quiz_choice4"
                                                                                               class="form-control"
                                                                                               autocomplete="off"
                                                                                               required/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label for="">Answer</label>
                                                                                        <select name="add_quiz_answer"
                                                                                                class="form-control"
                                                                                                required>
                                                                                            <option value="">Select
                                                                                                Answer
                                                                                            </option>
                                                                                            <option value="1">Choice 1
                                                                                            </option>
                                                                                            <option value="2">Choice 2
                                                                                            </option>
                                                                                            <option value="3">Choice 3
                                                                                            </option>
                                                                                            <option value="4">Choice 4
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                <span data-repeater-delete=""
                                                                                      class="btn btn-outline-danger btn-sm float-right deletePayeeBtn">
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
                                                    </div>
                                                    <div class="form-group row mb-0">
                                                        <div class="col-sm-12">
                                                        <span data-repeater-create=""
                                                              class="btn btn-outline-success btn-sm float-right">
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
                                            <div class="col-md-12 text-right mt-1">
                                                <button type="submit" class="btn btn-primary w-15">
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
