@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="BroadcastPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">ALL BROADCASTS</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
              @if($Role == 1)
                  <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                          onclick="openBroadcastToAll();">
                      <i class="fa fa-broadcast-tower mr-1"></i>
                      Send Broadcast To All
                  </button>
              @elseif($Role == 2)
                  <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                          onclick="openBroadcastToAll();">
                      <i class="fa fa-broadcast-tower mr-1"></i>
                      Send Broadcast To All
                  </button>
              @elseif($Role == 3)
                  <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                          onclick="openBroadcastToAll();">
                      <i class="fa fa-broadcast-tower mr-1"></i>
                      Send Broadcast To All
                  </button>
              @elseif($Role == 4)
                  <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                          onclick="openBroadcastToAll();">
                      <i class="fa fa-broadcast-tower mr-1"></i>
                      Send Broadcast To All
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
            <div class="col-12 col-md-2"></div>
            <div class="col-12 col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_broadcasts_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 80%;">Message</th>
                                    <th style="width: 15%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-2"></div>
        </div>
    </div>
    @include('admin.includes.userBroadcastToAllModal')
@endsection
