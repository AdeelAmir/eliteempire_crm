@extends('admin.layouts.app')
@section('content')
<style media="screen">
@media only screen and (min-width: 768px) {
  div.dataTables_wrapper div.dataTables_filter {
    text-align: right;
    margin-right: 170px;
  }
  .table-responsive {
    display: block;
    width: 100%;
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">Knowledge Zone</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
              <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2"
                      data-toggle="tooltip" title="Action" onclick="HandleFaqAction();">
                  <i class="fa fa-tasks mr-1"></i>
              </button>
              <!-- Delete -->
              <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0 mr-2" style="display:none;"
                      data-toggle="tooltip" title="Delete Selected Question"
                      onclick="DeleteMultipleFaq();" id="deleteAllFaqBtn">
                  <i class="fas fa-trash mr-1"></i>
              </button>
              <!-- Delete -->

              <button type="button" class="btn btn-primary btn-icon-text float-right mb-2 mb-md-0"
                  data-toggle="tooltip" title="Add New Question" onclick="OpenAddFaqModal();">
                  <i class="fas fa-plus-square mr-1"></i>
              </button>
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

            <div class="col-md-1"></div>
            <div class="col-md-10 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Questions
                        </h6>
                        <div class="table-responsive">
                          <form action="{{url('')}}" method="post" enctype="multipart/form-data" id="faqForm">
                            @csrf
                            @include('admin.includes.deleteFaqModal')
                            <table id="admin_training_room_faqs" class="table w-100">
                                <thead>
                                <tr>
                                    <th class="allFaqActionCheckBoxColumn">
                                        <input type="checkbox" name="checkAllBox" class="allFaqCheckBox" id="checkAllBox"
                                               onchange="CheckAllFaqRecords(this);"/>
                                    </th>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 35%;">Question</th>
                                    <th style="width: 50%;">Answer</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
    @include('admin.includes.addFaqModal')
    @include('admin.includes.editFaqModal')
@endsection
