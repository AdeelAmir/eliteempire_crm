@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="addNewAnnouncementPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Edit Announcement Form</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/announcements')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <form action="{{url('admin/announcement/update')}}" method="post" id="editAnnouncementForm"
              enctype="multipart/form-data">
            <input type="hidden" name="announcement_id" value="{{$AnnouncementId}}"/>
            @csrf
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

                {{--General Details--}}
                <div class="col-md-3"></div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                General
                            </h6>
                            <div class="row">
                                <input type="hidden" name="type" id="announcement_type"
                                       value="{{$announcement_details[0]->type}}"/>
                                <div class="col-md-12 mb-3 mt-3">
                                    <label for="announcement_message">Message</label>
                                    <textarea name="message" id="announcement_message" rows="5" cols="80"
                                              class="form-control"
                                              required>{{$announcement_details[0]->message}}</textarea>
                                </div>

                                <div class="col-md-5">
                                    <label for="_appointmentTime" class="w-100">Expiration Date and Time</label>
                                    <div class="input-group date form_datetime"
                                         data-date-format="mm/dd/yyyy - HH:ii p"
                                         data-link-field="_appointmentTime">
                                        <input class="form-control" size="16" type="text" value="" required>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="_appointmentTime" name="_appointmentTime" value=""
                                           required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <input type="submit" class="btn btn-primary w-15"
                                           name="submitUpdateAnnouncementForm"
                                           id="submitUpdateAnnouncementForm" value="Update"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </form>
    </div>
@endsection