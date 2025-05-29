@extends('admin.layouts.app')
@section('content')
<style media="screen">
.owl-carousel .owl-stage-outer {
  position: relative;
  overflow: hidden;
  -webkit-transform: translate3d(0,0,0);
  width: 150% !important;
}
</style>
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ASSIGN LEADS</span></h4>
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

            <div class="col-md-12 grid-margin stretch-card" id="filterPage">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Filter
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="startDateFilter">Sending Start Date</label>
                                <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="startDateFilter">
                                    <input class="form-control" size="16" type="text" value="">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="startDateFilter" name="startDateFilter" value="" required/>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="endDateFilter">Sending End Date</label>
                                <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                     data-link-field="endDateFilter">
                                    <input class="form-control" size="16" type="text" value="">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" id="endDateFilter" name="endDateFilter" value="" required/>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="typeFilter">Lead Type</label>
                                <select name="typeFilter" id="typeFilter" class="form-control">
                                    <option value="0">All</option>
                                    <option value="1">Lead</option>
                                    <option value="2">Call Request</option>
                                    <option value="3">Dispo Lead</option>
                                </select>
                            </div>

                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary" onclick="FilterAssignLeads();">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card" id="tablePage" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Leads
                            <button class="btn btn-primary float-right mb-3" onclick="FilterBackButton();">Back
                            </button>
                            <button class="btn btn-primary float-right mb-3 mr-2" onclick="AssignLeads();">
                                Assign
                            </button>
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_assign_leads_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;"><?php echo wordwrap("Lead Header", 15, '<br>'); ?></th>
                                    <th style="width: 20%;"><?php echo wordwrap("Home Owner and Address", 12, '<br>'); ?></th>
                                    <th style="width: 15%;"><?php echo wordwrap("Product and Appt", 12, '<br>'); ?></th>
                                    <th style="width: 15%;">Last Note</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.leadHistoryNotesModal')
    @include('admin.includes.assignLeadModal')
@endsection