@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="AnnouncementPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">ANNOUNCEMENT DETAILS</span></h4>
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
            <input type="hidden" name="details_announcement_id" id="details_announcement_id" value="{{$AnnouncementId}}" />
            <div class="col-12 col-md-3"></div>
            <div class="col-12 col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_announcements_details_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">#</th>
                                    <th style="width: 90%;">User</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3"></div>
        </div>
    </div>
@endsection
