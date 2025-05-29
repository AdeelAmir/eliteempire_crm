@extends('admin.layouts.app')
@section('content')
    <style media="screen">
        .IconSetting {
            margin-top: -22px;
            color: green;
        }

        .parent {
            width: 100%;
            overflow: hidden;
        }

        .child {
            height: 100%;
            margin-bottom: -50px; /* maximum width of scrollbar */
            padding-bottom: 32px; /* maximum width of scrollbar */
            overflow-y: hidden;
            overflow-x: scroll;
        }

        #message {
            white-space: nowrap;
        }

        .announcementAlertSetting {
            background-color: #4fd36d;
            color: white;
        }

        @media only screen and (min-width:1025px) {
          div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
            margin-right: 185px;
          }
          .table-responsive {
            display: block;
            width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
          }
        }
    </style>
    @if($Role == 1 || $Role == 2 || $Role == 3 || $Role == 4 || $Role == 5 || $Role == 6 || $Role == 7 || $Role == 8 || $Role == 9)
        <div class="page-content" id="DashboardPage">
            <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
                <div>
                    <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
                </div>
            </div>

            @foreach($Announcement as $announcement)
                <?php
                $CheckUserAnnouncementReadStatus = \Illuminate\Support\Facades\DB::table('read_announcements')
                	  ->where('announcement_id', $announcement->id)
                    ->where('user_id', Auth::id())
                    ->count();
                ?>
                    @if($CheckUserAnnouncementReadStatus == 0)
                        @if(\Carbon\Carbon::parse($announcement->expiration) >= \Carbon\Carbon::now())
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="announcement_id" id="announcement_id"
                                           value="{{$announcement->id}}"/>
                                    <div class="alert parent announcementAlertSetting text-center" role="alert">
                                        {{$announcement->message}}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                                onclick="ReadAnnouncement();">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
            @endforeach

            <div class="row">
                <div class="col-lg-12 col-xl-12 stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline mb-2">
                                <h6 class="card-title mb-0 w-100">
                                    Leads
                                      <button class="btn greenActionButtonTheme float-right mb-3 mr-2 text-white"
                                      onclick="HandleAssignLead();" data-toggle="tooltip" title="Assign Leads">
                                      <i class="fas fa-arrow-alt-circle-right"></i>
                                      </button>

                                     <button type="button" class="btn btn-primary float-right mb-3 mr-2" name="multipleAssignBtn"
                                            id="multipleAssignBtn" onclick="AssignMultiple();"
                                            data-toggle="tooltip" title="Assign Selected Leads"
                                            style="display: none;"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-diagram-2" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5v-1zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1zM3 11.5A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z"/>
                                      </svg></button>
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <form action="{{url('leads/assign/selected')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <table id="dashboard_leads_table" class="table w-100 d-print-block d-print-table">
                                        <thead>
                                        <tr>
                                            <th style="width: 0; padding: 0;" class="assignLeadCheckBoxColumn">
                                                <input type="checkbox" name="checkAllBox" class="assignLeadCheckBox" id="checkAllBox"
                                                       onchange="CheckAllRecords(this);"/>
                                            </th>
                                            <th style="width: 5px;">#</th>
                                            <th style="width: 15%;"><?php echo wordwrap("Lead Header", 15, '<br>'); ?></th>
                                            <th style="width: 19%;"><?php echo wordwrap("Seller Information", 20, '<br>'); ?></th>
                                            <th style="width: 16%;">Last Comment</th>
                                            <th style="width: 15%;"><?php echo wordwrap("Follow Up", 12, '<br>'); ?></th>
                                            <th style="width: 10%;">Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    @include('admin.includes.assignSelectedLeadModal')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- row -->
        </div>
    @endif
    @include('admin.includes.leadHistoryNotesModal')
    @include('admin.includes.leadEvaluationModal')
    @include('admin.includes.leadUpdateStatusModal')
    @include('admin.includes.leadUpdateAppointmentTimeModal')
    @include('admin.includes.assignLeadModal')
    @include('admin.includes.deleteLeadModal')
@endsection
