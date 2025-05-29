@extends('admin.layouts.app')
@section('content')
<style media="screen">
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
    <div class="page-content" id="LeadsPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">ELITE EMPIRE - <span class="text-primary">ALL LEADS</span></h4>
                <?php
                $ReferralLink = url('lead/add/') . '/' . base64_encode(\Illuminate\Support\Facades\Auth::id());
                ?>
                <h6 class="mt-2">Referral:&nbsp;&nbsp;<i class="fas fa-copy" style="cursor: pointer;" onclick="CopyReferralLink('{{$ReferralLink}}');"></i>&nbsp;&nbsp;<span id="linkCopied"></span></h6>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="Import"
                            onclick="window.location.href='{{url('admin/lead/import/view')}}';">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0 ml-2"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('admin/lead/add')}}';">
                        <i class="fas fa-plus-square ml-1"></i>
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="Import"
                            onclick="window.location.href='{{url('global_manager/lead/import/view')}}';">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0 ml-2"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('global_manager/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 3)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('acquisition_manager/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 4)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('disposition_manager/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 5)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('acquisition_representative/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 6)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('disposition_representative/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 7)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('cold_caller/lead/add')}}';">
                            <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 8)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="Import"
                            onclick="window.location.href='{{url('affiliate/lead/import/view')}}';">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                   <button type="button" class="btn btn-primary btn-icon-text mb-md-0 ml-2"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('affiliate/lead/add')}}';">
                            <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @elseif($Role == 9)
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0"
                            data-toggle="tooltip" title="Import"
                            onclick="window.location.href='{{url('realtor/lead/import/view')}}';">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-text mb-md-0 ml-2"
                            data-toggle="tooltip" title="New Lead"
                            onclick="window.location.href='{{url('realtor/lead/add')}}';">
                        <i class="fas fa-plus-square mr-1"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {!! session('message') !!}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif
                <div class="alert alert-success" id="SuccessAlert" style="display:none;">
                  Lead has been successfully assigned to the aquisition manager.
                </div>
                <div class="alert alert-danger" id="FailedAlert" style="display:none;">
                  <span id="_failedAlertText"></span>
                </div>
            </div>

            @if($Role == 1 || $Role == 2 || $Role == 3 || $Role == 4 || $Role == 5 || $Role == 6 || $Role == 7 || $Role == 8 || $Role == 9)
                <div class="col-md-12 grid-margin stretch-card" id="tablePage">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                Leads
                                <button class="btn btn-primary float-right mb-3" data-toggle="tooltip" title="Filter" onclick="FilterBackButton();">
                                  <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                {{--<button class="btn btn-primary float-right mb-3 mr-2" onclick="AssignLeads();">
                                    Assign
                                </button>--}}
                                @if($Role == 1 || $Role == 2 || $Role == 3 || $Role == 4 || $Role == 5 || $Role == 6 || $Role == 7 || $Role == 8 || $Role == 9)
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
                                @endif
                            </h6>
                            <div class="table-responsive">
                                <form action="{{url('leads/assign/selected')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @include('admin.includes.assignSelectedLeadModal')
                                    <table id="admin_leads_table" class="table w-100">
                                        <thead>
                                        <tr>
                                            <th style="width: 0; padding: 0;" class="assignLeadCheckBoxColumn">
                                                @if($Role == 1 || $Role == 2 || $Role == 3 || $Role == 4 || $Role == 5 || $Role == 6 || $Role == 7 || $Role == 8 || $Role == 9)
                                                    <input type="checkbox" name="checkAllBox" class="assignLeadCheckBox" id="checkAllBox"
                                                           onchange="CheckAllRecords(this);"/>
                                                @endif
                                            </th>
                                            <th style="width: 5%;">#</th>
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 grid-margin stretch-card" id="filterPage" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                Filter
                                <i class="fa fa-window-close float-right" style="font-size: 16px;
                                cursor: pointer;" aria-hidden="true" onclick="FilterCloseButton();"></i>
                            </h6>
                            <div class="row">
                                <div class="col-md-12 mb-3 mt-2">
                                    <label for="firstNameFilter">Seller Name</label>
                                    <input type="text" name="fullNameFilter" id="fullNameFilter"
                                           class="form-control"/>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="phone1Filter">Phone Number</label>
                                    <input type="text" name="phoneFilter" id="phoneFilter" class="form-control"/>
                                </div>

                                <div class="col-md-12 mb-3" id="_leadFilterCityBlock" style="display:none;">
                                    <label for="cityFilter">City</label>
                                    <select name="cityFilter" id="cityFilter" class="form-control">
                                        <option value="0" selected>Select City</option>
                                    </select>
                                </div>

                                <!-- <div class="col-md-12 mb-3">
                                    <label for="county">County</label>
                                    <select name="countyFilter" id="countyFilter" class="form-control county" required>
                                        <option value="0" selected>Select County</option>
                                    </select>
                                </div> -->

                                <div class="col-md-12 mb-3">
                                    <label for="state">State</label>
                                    <select class="state" name="stateFilter" id="stateFilter"
                                            class="form-control" onchange="CheckLeadFilterState(this);LoadFilterStateCountyCity();">
                                        <option value="0" selected>Select State</option>
                                        @foreach($States as $state)
                                            @if(\Illuminate\Support\Facades\Session::get('user_role') == 1 || \Illuminate\Support\Facades\Session::get('user_role') == 2)
                                                <option value="{{$state->name}}">{{$state->name}}</option>
                                            @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 3 || \Illuminate\Support\Facades\Session::get('user_role') == 4)
                                                @if($state->name == \App\Helpers\SiteHelper::GetCurrentUserState())
                                                    <option value="{{$state->name}}">{{$state->name}}</option>
                                                @endif
                                            @else
                                                <option value="{{$state->name}}">{{$state->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="zipcodeFilter">Zip code</label>
                                    <input type="number" name="zipcodeFilter" id="zipcodeFilter"
                                           class="form-control"
                                           onkeypress="limitKeypress(event,this.value,5)"
                                           placeholder="Enter Your Zip Code"/>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="leadSearch">Lead Search</label>
                                    <select name="leadSearch" id="leadSearch" class="form-control" onchange="LeadSearch(this.value);">
                                        <option value="0">Select</option>
                                        <option value="followUp">Follow Up</option>
                                        <option value="creationDate">Creation Date</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3" id="leadSearchDiv" style="display: none;"></div>

                                <div class="col-md-12 mb-3" id="customRangeStartDate" style="display: none;">
                                    <label for="customStartDate">Start Date</label>
                                    <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="customStartDate">
                                        <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="customStartDate" name="startDateFilter" required/>
                                </div>

                                <div class="col-md-12 mb-3" id="customRangeEndDate" style="display: none;">
                                    <label for="customEndDate">End Date</label>
                                    <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="customEndDate">
                                        <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="customEndDate" name="startDateFilter" required/>
                                </div>

                                <input type="hidden" name="searchStartDate" id="searchStartDate" value="" />
                                <input type="hidden" name="searchEndDate" id="searchEndDate" value="" />

                                {{--<div class="col-md-12 mb-3">
                                    <label for="appointmentDateFilter">Follow Up Date</label>
                                    <div class="input-group date appointmentDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="appointmentDateFilter">
                                        <input class="form-control" size="16" type="text" id="appointmentDateTextFilter" value="">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="appointmentDateFilter" name="appointmentDateFilter"
                                           value="" />
                                </div>--}}

                                <?php
                                  $SendingStartDate = \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y');
                                ?>

                                {{--<div class="col-md-12 mb-3">
                                    <label for="startDateFilter">Lead Creation Date</label>
                                    <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                         data-link-field="startDateFilter">
                                        <input class="form-control" size="16" type="text" id="startDateTextFilter">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="startDateFilter" name="startDateFilter" required/>
                                </div>--}}

                                <div class="col-md-12 mb-3">
                                    <label for="investorFilter">Business Account</label>
                                    <select name="investorFilter[]" id="investorFilter" class="form-control" multiple>
                                        <option value="0">Select Investor</option>
                                        @foreach($Investors as $investor)
                                          <option value="{{$investor->id}}">{{$investor->firstname}} {{$investor->middlename}} {{$investor->lastname}}</option>
                                        @endforeach
                                        @foreach($Realtors as $realtor)
                                            <option value="{{$realtor->id}}">{{$realtor->firstname}} {{$realtor->middlename}} {{$realtor->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{--<div class="col-md-12 mb-3">
                                    <label for="realtorFilter">Realtor</label>
                                    <select name="realtorFilter[]" id="realtorFilter" class="form-control" multiple>
                                        <option value="0">Select Realtor</option>
                                        @foreach($Realtors as $realtor)
                                          <option value="{{$realtor->id}}">{{$realtor->firstname}} {{$realtor->middlename}} {{$realtor->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>--}}

                                <div class="col-md-12 mb-3">
                                    <label for="realtorFilter">Title Company</label>
                                    <select name="titleCompanyFilter" id="titleCompanyFilter" class="form-control">
                                        <option value="0" selected>Select Title Company</option>
                                        @foreach($TitleCompanies as $titlecompany)
                                          <option value="{{$titlecompany->id}}">{{$titlecompany->firstname}} {{$titlecompany->middlename}} {{$titlecompany->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="leadSourceFilter">Lead Source</label>
                                    <select name="leadSourceFilter[]" id="leadSourceFilter" class="form-control" multiple>
                                        <option value="0">Select</option>
                                        <option value="basic">D4D</option>
                                        <option value="propStream">PropStream</option>
                                        <option value="calling">Calling</option>
                                        <option value="text">Text</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="website">Website</option>
                                        <option value="zillow">Zillow</option>
                                        <option value="wholesaler">Wholesaler</option>
                                        <option value="realtor">Realtor</option>
                                        <option value="investor">Investor</option>
                                        <option value="radio">Radio</option>
                                        <option value="jv_partner">JV Partner</option>
                                        <option value="jv_partner">Banded Sign</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="dataSourceFilter">Data Sources</label>
                                    <select name="dataSourceFilter[]" id="dataSourceFilter" class="form-control" multiple>
                                        <option value="0" disabled="disabled">Select</option>
                                        <option value="On Market">On Market</option>
                                        <option value="Vacant">Vacant</option>
                                        <option value="Liens">Liens</option>
                                        <option value="Pre-Foreclosures">Pre-Foreclosures</option>
                                        <option value="Auctions">Auctions</option>
                                        <option value="Bank Owned">Bank Owned</option>
                                        <option value="Cash Buyers">Cash Buyers</option>
                                        <option value="High Equity">High Equity</option>
                                        <option value="Free & Clear">Free & Clear</option>
                                        <option value="Bankruptcy">Bankruptcy</option>
                                        <option value="Divorce">Divorce</option>
                                        <option value="Tax Delinquencies">Tax Delinquencies</option>
                                        <option value="Flippers">Flippers</option>
                                        <option value="Failed Listings">Failed Listings</option>
                                        <option value="Senior Owners">Senior Owners</option>
                                        <option value="Vacant Land">Vacant Land</option>
                                        <option value="Tired Landlords">Tired Landlords</option>
                                        <option value="Pre-Probate (Deceased Owner)">Pre-Probate (Deceased Owner)</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <div class="col-md-12 text-center">
                                    <button class="btn btn-primary" onclick="FilterLeadsByStatus('1,2,3,4,5,6,7,8,9');">Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('admin.includes.leadHistoryNotesModal')
    @include('admin.includes.leadUpdateStatusModal')
    @include('admin.includes.leadUpdateAppointmentTimeModal')
    @include('admin.includes.assignLeadModal')
    @include('admin.includes.leadEvaluationModal')
    @include('admin.includes.deleteLeadModal')
@endsection
