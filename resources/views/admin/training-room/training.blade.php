@push('style')
    <link rel="stylesheet" href="{{asset('public/assets/css/HtmlLightBox.css')}}" type="text/css"/>
    <link rel="stylesheet" href="{{asset('public/assets/css/vertical-progressbar.css')}}" type="text/css"/>
@endpush

<?php
/*All Training Assignments*/
$AllTrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
    ->join('training_rooms', 'training_assignments.assignment_id', '=', 'training_rooms.id')
    ->where('user_id', '=', \Illuminate\Support\Facades\Auth::id())
    ->where('training_assignment_folder_id', '=', $CourseId)
    ->select('training_assignments.id AS TrainingAssignmentId', 'training_assignments.assignment_id AS AssignmentId', 'training_assignments.assignment_type', 'training_assignments.status', 'training_rooms.*')
    ->get();
?>

<input type="hidden" name="training_course_id" id="training_course_id" value="{{$CourseId}}">

@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0 w-100">ELITE EMPIRE - <span class="text-primary">Training Room - {{$CourseName}} <span style="font-size: 16px;">({{round($CourseCompletionRate)}}%)</span></span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
              <?php
              $Url = "";
              if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                  $Url = url('acquisition_manager/training/faqs');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                  $Url = url('disposition_manager/training/faqs');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                  $Url = url('acquisition_representative/training/faqs');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                  $Url = url('disposition_representative/training/faqs');
              }
              ?>
              <button type="button" class="btn btn-secondary mr-1"
                      data-toggle="tooltip" title="Knowledge Zone"
                      onclick="window.location.href='{{$Url}}';">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle link-icon"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
              </button>
              <?php
              $Url = "";
              if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                  $Url = url('acquisition_manager/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                  $Url = url('disposition_manager/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                  $Url = url('acquisition_representative/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                  $Url = url('disposition_representative/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                  $Url = url('cold_caller/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                  $Url = url('affiliate/training');
              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                  $Url = url('realtor/training');
              }
              ?>
              <button type="button" class="btn btn-secondary"
                      data-toggle="tooltip" title="Back"
                      onclick="window.location.href='{{$Url}}';">
                  <i class="fa fa-arrow-left pt-1 pb-1" aria-hidden="true"></i>
              </button>
            </div>
        </div>
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

        <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Assignments
                        </h6>
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="StepProgress">
                                    <?php
                                    $StepCount = 1;
                                    $TotalAssignments = sizeof($AllTrainingAssignment);
                                    $CompletedAssignments = 0;
                                    foreach ($AllTrainingAssignment as $item) {
                                        if ($item->status == 1) {
                                            $CompletedAssignments++;
                                        }
                                    }
                                    $RemainingAssignments = $TotalAssignments - $CompletedAssignments;
                                    ?>
                                    @foreach($AllTrainingAssignment as $assignment)
                                        @if($assignment->status == 1)
                                            {{--Assignment Done--}}
                                            @if($TotalAssignments == $StepCount)
                                                <li class="StepProgress-item is-done cursor-pointer"
                                                    id="barStep_{{$StepCount}}" onclick="SetStepActive(this);">
                                                    <strong>{{$assignment->title}}</strong></li>
                                            @else
                                                <li class="StepProgress-item is-done cursor-pointer"
                                                    id="barStep_{{$StepCount}}" onclick="SetStepActive(this);">
                                                    <strong>{{$assignment->title}}</strong></li>
                                            @endif
                                        @else
                                            {{--Assignment Pending--}}
                                            @if(intval($CompletedAssignments + 1) == $StepCount)
                                                <li class="StepProgress-item complete current-task cursor-pointer"
                                                    id="barStep_{{$StepCount}}" onclick="SetStepActive(this);">
                                                    <strong>{{$assignment->title}}</strong></li>
                                            @else
                                                <li class="StepProgress-item" id="barStep_{{$StepCount}}">
                                                    <strong>{{$assignment->title}}</strong></li>
                                            @endif
                                        @endif
                                        <?php
                                        $StepCount++;
                                        ?>
                                    @endforeach
                                    @if($TotalAssignments == $CompletedAssignments)
                                        <li class="StepProgress-item cursor-pointer is-done" id="barStep_{{$StepCount}}" onclick="SetStepActive(this, 'last');"><strong>Done</strong></li>
                                    @else
                                        <li class="StepProgress-item"><strong>Done</strong></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 grid-margin stretch-card">
                <?php
                $StepCount = 1;
                $TotalAssignments = sizeof($AllTrainingAssignment);
                $CompletedAssignments = 0;
                foreach ($AllTrainingAssignment as $item) {
                    if ($item->status == 1) {
                        $CompletedAssignments++;
                    }
                }
                $RemainingAssignments = $TotalAssignments - $CompletedAssignments;
                $Display = "style='display: none;'";
                ?>
                @foreach($AllTrainingAssignment as $assignment)
                    @if($assignment->assignment_type == 'video')
                        <div class="w-100"
                             id="barStepContent_{{$StepCount}}" <?php echo intval($CompletedAssignments + 1) == $StepCount ? '' : $Display; ?>>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        Video
                                    </h6>
                                    <div class="row">
                                        <input type="hidden" name="assignmentId" id="assignmentId_{{$StepCount}}"
                                               value="{{$assignment->TrainingAssignmentId}}">
                                        <div class="col-md-12">
                                            <section class="sec-block-200 pt-0 pb-0 half-bg-top">
                                                <div class="container">
                                                    <div class="videeo-sec overlay">
                                                        <img src="{{asset('public/assets/images/msc-img2.jpg')}}"
                                                             alt="">
                                                        <div class="vide-cap">
                                                            <a href="{{$assignment->video_url}}" title=""
                                                               class="html5lightbox">
                                                                <img src="{{asset('public/assets/images/play-icon.png')}}"
                                                                     alt="">
                                                            </a>
                                                        </div>
                                                    </div><!--videeo-sec end-->
                                                </div>
                                            </section>
                                        </div>
                                        @if($assignment->status != 1)
                                            <div class="col-md-12 mt-3 text-center">
                                                <input type="button" value="Mark As Complete"
                                                       class="btn btn-primary"
                                                       onclick="MarkVideoAsComplete('{{$StepCount}}');"/>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($assignment->assignment_type == 'article')
                        <div class="w-100"
                             id="barStepContent_{{$StepCount}}" <?php echo intval($CompletedAssignments + 1) == $StepCount ? '' : $Display; ?>>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {!! $assignment->title !!}
                                    </h6>
                                    <div class="row">
                                        <input type="hidden" name="assignmentId" id="assignmentId_{{$StepCount}}"
                                               value="{{$assignment->TrainingAssignmentId}}">
                                        <div class="col-md-12">
                                            {!! $assignment->article_details !!}
                                        </div>
                                        @if($assignment->status != 1)
                                            <div class="col-md-12 mt-2 text-center">
                                                <input type="button" value="Mark As Complete"
                                                       class="btn btn-primary"
                                                       onclick="MarkArticleAsComplete('{{$StepCount}}');"/>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($assignment->assignment_type == 'quiz')
                        <div class="w-100"
                             id="barStepContent_{{$StepCount}}" <?php echo intval($CompletedAssignments + 1) == $StepCount ? '' : $Display; ?>>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {!! $assignment->title !!}
                                    </h6>
                                    <div class="row">
                                        <input type="hidden" name="assignmentId" id="assignmentId_{{$StepCount}}"
                                               value="{{$assignment->TrainingAssignmentId}}">
                                        <div class="col-md-12">
                                            <?php
                                            $Count = 0;
                                            $QuizQuestions = \Illuminate\Support\Facades\DB::table('training_quizzes')
                                                ->where('topic_id', '=', $assignment->AssignmentId)
                                                ->get();
                                            ?>
                                            @foreach($QuizQuestions as $question)
                                                <div class="mb-1" id="quizQuestionDiv{{$StepCount}}{{$Count}}"
                                                     style="padding: 5px;">
                                                    <p class="mb-1" style="font-size: 15px; font-weight: bold;">
                                                        Q.&nbsp;{{$Count + 1}}&nbsp;&nbsp;{{$question->question}}</p>
                                                    <div class="question-options-div">
                                                        @if($question->choice1 != '')
                                                            <label class="question-option-label mb-1 w-100">
                                                                <input type="radio"
                                                                       name="question{{$StepCount}}{{$Count}}"
                                                                       value="1">&nbsp;&nbsp;{{$question->choice1}}
                                                            </label>
                                                        @endif
                                                        @if($question->choice2 != '')
                                                            <label class="question-option-label mb-1 w-100">
                                                                <input type="radio"
                                                                       name="question{{$StepCount}}{{$Count}}"
                                                                       value="2">&nbsp;&nbsp;{{$question->choice2}}
                                                            </label>
                                                        @endif
                                                        @if($question->choice3 != '')
                                                            <label class="question-option-label mb-1 w-100">
                                                                <input type="radio"
                                                                       name="question{{$StepCount}}{{$Count}}"
                                                                       value="3">&nbsp;&nbsp;{{$question->choice3}}
                                                            </label>
                                                        @endif
                                                        @if($question->choice4 != '')
                                                            <label class="question-option-label mb-1 w-100">
                                                                <input type="radio"
                                                                       name="question{{$StepCount}}{{$Count}}"
                                                                       value="4">&nbsp;&nbsp;{{$question->choice4}}
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <input type="hidden" name="questionAnswer{{$StepCount}}{{$Count}}"
                                                       id="questionAnswer{{$StepCount}}{{$Count}}" value="{{$question->answer}}">
                                                <?php
                                                $Count++;
                                                ?>
                                            @endforeach
                                            <input type="hidden" name="questionsCount{{$StepCount}}" id="questionsCount{{$StepCount}}"
                                                   value="{{$Count}}"/>
                                        </div>
                                    </div>
                                    @if($assignment->status != 1)
                                        <div class="col-md-12 text-center">
                                            <input type="button" value="Mark As Complete" class="btn btn-primary"
                                                   onclick="MarkQuizAsComplete('{{$StepCount}}');"/>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <?php
                    $StepCount++;
                    ?>
                @endforeach
                @if($TotalAssignments == $CompletedAssignments)
                    <div class="w-100" id="barStepContent_{{$StepCount}}">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    Training Room
                                </h6>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <b>CONGRATULATIONS!</b>&nbsp;&nbsp;All the tasks are completed
                                        successfully!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.includes.quizResultsModal')
@endsection

@push('scripts')
    <script src="{{asset('public/assets/js/vertical-progressbar.js')}}"></script>
@endpush
