@extends('admin.layouts.app')
@section('content')
    <style>
        .cntr {
            display: table;
            width: 100%;
            height: 100%;
        }
        .cntr .cntr-innr {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        /*** STYLES ***/
        .search {
            display: inline-block;
            position: relative;
            height: 35px;
            width: 35px;
            box-sizing: border-box;
            padding: 3px 9px 0 9px;
            border: 3px solid #023A51;
            border-radius: 25px;
            transition: all 200ms ease;
            cursor: text;
        }
        .search:after {
            content: "";
            position: absolute;
            width: 3px;
            height: 20px;
            right: -5px;
            top: 21px;
            background: #023A51;
            border-radius: 3px;
            transform: rotate(-45deg);
            transition: all 200ms ease;
        }
        .search.active,
        .search:hover {
            width: 100%;
            margin-right: 0;
        }
        .search.active:after,
        .search:hover:after {
            height: 0;
        }
        .search input {
            width: 100%;
            border: none;
            box-sizing: border-box;
            font-family: Helvetica;
            font-size: 15px;
            color: inherit;
            background: transparent;
            outline-width: 0;
        }

        #searchFaq{
            width: 100%;
            border-radius: 50px;
            margin: 0 auto;
            /* padding-left: 30px; */
            padding-left: 15px;
            padding-top: 11px;
        }

        .searchIcon1 {
            position: absolute;
            right: 27px;
            top: 11px;
        }

        .searchIcon2 {
            position: absolute;
            left: 25px;
            top: 11px;
        }

        #searchFaq.active,
        #searchFaq:hover {
            width: 100%;
        }

        /* Folders CSS */
        .progress {
          display: flex;
          height: 0.8rem;
          overflow: hidden;
          line-height: 0;
          font-size: 0.55rem;
          background-color: #e9ecef;
          border-radius: 0.25rem;
          margin-top: -20px;
      }

      .progress-bar {
          display: flex;
          flex-direction: column;
          justify-content: center;
          overflow: hidden;
          color: #fff;
          text-align: center;
          white-space: nowrap;
          background-color: #4fd36d;
          transition: width 0.6s ease;
          padding-top: 1px;
          padding-top: 2px;
      }

      .cardBackgroundColor{
        background-color: #f8f8f8 !important;
      }

      .courseTitleSetting{
        margin-top: -25px;
      }

      .courseOpenLinkSetting{
        font-size: 10px;
      }

      .badge{
        font-size: 8px !important;
      }
      /* Folders CSS */
    </style>

    <!-- Get User Training Room Courses - Start -->
    <?php
    $AllTrainingAssignmentFolders = \Illuminate\Support\Facades\DB::table('training_assignment_folders')
        ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
        ->where('training_assignment_folders.user_id', '=', \Illuminate\Support\Facades\Auth::id())
        ->select('training_assignment_folders.*', 'folders.name AS FolderName', 'folders.picture', 'folders.required')
        ->get();
    ?>
    <!-- Get User Training Room Courses - End -->

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">
                    ELITE EMPIRE - <span class="text-primary">TRAINING ROOM</span>
                </h4>
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
            <div class="col-md-2"></div>
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-md-8">
                                    <span style="font-size: large;">My Courses</span>
                                </div>
                                <div class="col-md-4">
                                  <div>
                                    <i class="fa fa-search searchIcon searchIcon1"></i>
                                    <input type="text" class="form-control" name="searchFaq" id="searchFaq" placeholder="Search" onkeyup="SearchFolder(this);" />
                                  </div>
                                </div>
                            </div>

                            <!-- Training Room Folders -->
                            <div class="row mt-5" id="TrainingRoomFolders">

                              @if(count($AllTrainingAssignmentFolders) > 0)
                              @foreach($AllTrainingAssignmentFolders as $folder)
                              <?php
                              $Url = "";
                              if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                  $Url = url('acquisition_manager/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                  $Url = url('disposition_manager/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                  $Url = url('acquisition_representative/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                  $Url = url('disposition_representative/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                  $Url = url('cold_caller/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                  $Url = url('affiliate/training/course/' . $folder->id);
                              } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                  $Url = url('realtor/training/course/' . $folder->id);
                              }
                              ?>
                              <div class="col-md-4">
                                <a href="{{$Url}}">
                                <div class="card cardBackgroundColor">
                                    <div class="card-title">
                                      <img src="{{ asset('public/storage/folders/' . $folder->picture)}}" alt="logo-small" class="img-fluid" style="width: 100%; height: 200px;">
                                    </div>
                                    <div class="card-body">
                                      <p class="text-left courseTitleSetting">{{$folder->FolderName}}</p>
                                      <div class="mt-1">
                                        <a href="{{$Url}}" class="mt-2 courseOpenLinkSetting">
                                          @if($folder->completion_rate > 0 && $folder->completion_rate < 100)
                                          Resume Course
                                          @elseif($folder->completion_rate == 100)
                                          Review Course
                                          @else
                                          Start Course
                                          @endif
                                        </a>
                                      </div>
                                      @if($folder->required == 1)
                                      <div class="mt-1">
                                        <span class="badge badge-danger">Required</span>
                                      </div>
                                      @endif
                                      <div class="progress mt-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{round($folder->completion_rate) . '%'}}" aria-valuenow="{{$folder->completion_rate}}" aria-valuemin="0" aria-valuemax="100">{{round($folder->completion_rate)}}%</div>
                                      </div>
                                    </div>
                                </div>
                                </a>
                              </div>
                              @endforeach
                              @else
                                  <div class="col-md-3"></div>
                                  <div class="col-md-6 mt-4 mb-5">
                                      <h4 class="text-center" style="font-size: 16px;color: #b0b6b0;">Training Room is empty!</h4>
                                  </div>
                              @endif
                            </div>
                            <div class="row mt-5" id="searchResultsCourseDiv" style="display: none;"></div>
                            <!-- Training Room Folders -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
@endsection
