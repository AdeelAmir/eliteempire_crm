@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="LeadFunnelPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">ELITE EMPIRE - <span class="text-primary">LEAD FUNNEL</span></h4>
            </div>
            <div class="dropdown" data-toggle="dropdown">
                <a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle" role="button" data-toggle="dropdown" id="leadfunnel-dropdown-value">
                    All Time
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Recent Week</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Recent Month</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Recent Quarter</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Recent Semester</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Recent Year</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">All Time</a>
                    <a href="javascript::void(0);" class="dropdown-item leadfunnel-dropdown-item">Range</a>
                </div>
            </div>
        </div>

        @if($StartDate != "" && $EndDate != "")
        <?php
          $StartDate = \Carbon\Carbon::parse($StartDate)->format('m/d/Y');
          $EndDate = \Carbon\Carbon::parse($EndDate)->format('m/d/Y');

          $_StartDate = \Carbon\Carbon::parse($StartDate);
          $_EndDate = \Carbon\Carbon::parse($EndDate);
        ?>
        <div class="row">
          <div class="col-md-4"></div>

          <div class="col-md-2 mb-3" id="LeadFunnelCustomRangeStartDate">
              <label for="customLeadFunnelStartDate">Start Date</label>
              <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                   data-link-field="customLeadFunnelStartDate">
                  <input class="form-control" size="16" type="text" id="startDateTextFilter1" value="{{$StartDate}}">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon"><span
                              class="glyphicon glyphicon-th"></span></span>
              </div>
              <input type="hidden" id="customLeadFunnelStartDate" name="leadFunnelStartDateFilter" value="{{$_StartDate}}" />
          </div>

          <div class="col-md-2 mb-3" id="LeadFunnelCustomRangeEndDate">
              <label for="customLeadFunnelEndDate">End Date</label>
              <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                   data-link-field="customLeadFunnelEndDate">
                  <input class="form-control" size="16" type="text" id="startDateTextFilter2" value="{{$EndDate}}">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon"><span
                              class="glyphicon glyphicon-th"></span></span>
              </div>
              <input type="hidden" id="customLeadFunnelEndDate" name="leadFunnelEndDateFilter" value="{{$_EndDate}}" />
          </div>

          <div class="col-md-4" id="LeadFunnelFilterButtonSection">
            <button style="margin-top: 32px;" class="btn btn-primary" type="button" name="leadfunnelbtn" id="leadfunnelbtn" onclick="LoadLeadFunnel('Range');">Filter</button>
          </div>
        </div>
        @else
        <div class="row">
          <div class="col-md-4"></div>

          <div class="col-md-2 mb-3" id="LeadFunnelCustomRangeStartDate" style="display: none;">
              <label for="customLeadFunnelStartDate">Start Date</label>
              <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                   data-link-field="customLeadFunnelStartDate">
                  <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon"><span
                              class="glyphicon glyphicon-th"></span></span>
              </div>
              <input type="hidden" id="customLeadFunnelStartDate" name="leadFunnelStartDateFilter" />
          </div>

          <div class="col-md-2 mb-3" id="LeadFunnelCustomRangeEndDate" style="display: none;">
              <label for="customLeadFunnelEndDate">End Date</label>
              <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                   data-link-field="customLeadFunnelEndDate">
                  <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon"><span
                              class="glyphicon glyphicon-th"></span></span>
              </div>
              <input type="hidden" id="customLeadFunnelEndDate" name="leadFunnelEndDateFilter" />
          </div>

          <div class="col-md-4" id="LeadFunnelFilterButtonSection" style="display: none;">
            <button style="margin-top: 32px;" class="btn btn-primary" type="button" name="leadfunnelbtn" id="leadfunnelbtn" onclick="LoadLeadFunnel('Range');">Filter</button>
          </div>
        </div>
        @endif

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

            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/lead/edit') . "/";
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                $Url = url('global_manager/lead/edit') . "/";
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                $Url = url('acquisition_manager/lead/edit') . "/";
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                $Url = url('disposition_manager/lead/edit') . "/";
            } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                $Url = url('acquisition_representative/lead/edit') . "/";
            } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                $Url = url('disposition_representative/lead/edit') . "/";
            } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                $Url = url('cold_caller/lead/edit') . "/";
            } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                $Url = url('affiliate/lead/edit') . "/";
            } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                $Url = url('realtor/lead/edit') . "/";
            }
            ?>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                        <tr>
                            <th style="width: 200px;">1-LEAD IN</th>
                            <th style="width: 200px;">2-ASSIGNED TO ACQUISITIONS</th>
                            <th style="width: 200px;">3-SELLER UNDER CONTRACT</th>
                            <th style="width: 200px;">4-ASSIGNED TO DISPOSITIONS</th>
                            <th style="width: 200px;">5-BUYER CONTRACT + EMD</th>
                            <th style="width: 200px;">6-SEND TO TITLE</th>
                            <th style="width: 200px;">7-CLOSED WON</th>
                            <th style="width: 200px;">8-DEAL LOST</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $LeadInSum = 0;
                        $AssignedToAcquisitionsSum = 0;
                        $SellerUnderContractSum = 0;
                        $AssignedToDispositionsSum = 0;
                        $BuyerContractSum = 0;
                        $SentToTitleSum = 0;
                        $ClosedWonSum = 0;
                        $DealLostSum = 0;
                        $LeadsController = new \App\Http\Controllers\LeadController();
                        ?>
                        @for($i = 0; $i < $MaxTotalRecords; $i++)
                            <tr>
                                <th style="width: 200px;">
                                    @if(isset($LeadIn[$i]))
                                        <?php
                                        $Value = $LeadIn[$i]->contract_amount != ''? '$' . number_format($LeadIn[$i]->contract_amount) : '$0' ;
                                        $LeadInSum += $LeadIn[$i]->contract_amount != ''? floatval($LeadIn[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $LeadIn[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$LeadIn[$i]->firstname . ' ' . $LeadIn[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($LeadIn[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($LeadIn[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($LeadIn[$i]->firstname, 0, 1) . substr($LeadIn[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($AssignedToAcquisitions[$i]))
                                        <?php
                                        $Value = $AssignedToAcquisitions[$i]->contract_amount != ''? '$' . number_format($AssignedToAcquisitions[$i]->contract_amount) : '$0' ;
                                        $AssignedToAcquisitionsSum += $AssignedToAcquisitions[$i]->contract_amount != ''? floatval($AssignedToAcquisitions[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $AssignedToAcquisitions[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$AssignedToAcquisitions[$i]->firstname . ' ' . $AssignedToAcquisitions[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($AssignedToAcquisitions[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($AssignedToAcquisitions[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($AssignedToAcquisitions[$i]->firstname, 0, 1) . substr($AssignedToAcquisitions[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($SellerUnderContract[$i]))
                                        <?php
                                        $Value = $SellerUnderContract[$i]->contract_amount != ''? '$' . number_format($SellerUnderContract[$i]->contract_amount) : '$0' ;
                                        $SellerUnderContractSum += $SellerUnderContract[$i]->contract_amount != ''? floatval($SellerUnderContract[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $SellerUnderContract[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$SellerUnderContract[$i]->firstname . ' ' . $SellerUnderContract[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($SellerUnderContract[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($SellerUnderContract[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($SellerUnderContract[$i]->firstname, 0, 1) . substr($SellerUnderContract[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($AssignedToDispositions[$i]))
                                        <?php
                                        $Value = $AssignedToDispositions[$i]->contract_amount != ''? '$' . number_format($AssignedToDispositions[$i]->contract_amount) : '$0' ;
                                        $AssignedToDispositionsSum += $AssignedToDispositions[$i]->contract_amount != ''? floatval($AssignedToDispositions[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $AssignedToDispositions[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$AssignedToDispositions[$i]->firstname . ' ' . $AssignedToDispositions[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($AssignedToDispositions[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($AssignedToDispositions[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($AssignedToDispositions[$i]->firstname, 0, 1) . substr($AssignedToDispositions[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($BuyerContract[$i]))
                                        <?php
                                        $Value = $BuyerContract[$i]->contract_amount != ''? '$' . number_format($BuyerContract[$i]->contract_amount) : '$0' ;
                                        $BuyerContractSum += $BuyerContract[$i]->contract_amount != ''? floatval($BuyerContract[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $BuyerContract[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$BuyerContract[$i]->firstname . ' ' . $BuyerContract[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($BuyerContract[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($BuyerContract[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($BuyerContract[$i]->firstname, 0, 1) . substr($BuyerContract[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($SentToTitle[$i]))
                                        <?php
                                        $Value = $SentToTitle[$i]->contract_amount != ''? '$' . number_format($SentToTitle[$i]->contract_amount) : '$0' ;
                                        $SentToTitleSum += $SentToTitle[$i]->contract_amount != ''? floatval($SentToTitle[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $SentToTitle[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$SentToTitle[$i]->firstname . ' ' . $SentToTitle[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($SentToTitle[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($SentToTitle[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($SentToTitle[$i]->firstname, 0, 1) . substr($SentToTitle[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($ClosedWon[$i]))
                                        <?php
                                        $Value = $ClosedWon[$i]->contract_amount != ''? '$' . number_format($ClosedWon[$i]->contract_amount) : '$0' ;
                                        $ClosedWonSum += $ClosedWon[$i]->contract_amount != ''? floatval($ClosedWon[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $ClosedWon[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$ClosedWon[$i]->firstname . ' ' . $ClosedWon[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($ClosedWon[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($ClosedWon[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($ClosedWon[$i]->firstname, 0, 1) . substr($ClosedWon[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                                <th style="width: 200px;">
                                    @if(isset($DealLost[$i]))
                                        <?php
                                        $Value = $DealLost[$i]->contract_amount != ''? '$' . number_format($DealLost[$i]->contract_amount) : '$0' ;
                                        $DealLostSum += $DealLost[$i]->contract_amount != ''? floatval($DealLost[$i]->contract_amount) : 0 ;
                                        $Url = "";
                                        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                                            $Url = url('admin/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                            $Url = url('global_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                            $Url = url('acquisition_manager/lead/edit') . "/";
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                            $Url = url('disposition_manager/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                            $Url = url('acquisition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                            $Url = url('disposition_representative/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                                            $Url = url('cold_caller/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                                            $Url = url('affiliate/lead/edit') . "/";
                                        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                                            $Url = url('realtor/lead/edit') . "/";
                                        }
                                        $Url .= $DealLost[$i]->id;
                                        ?>
                                        <div class="card" onclick="window.location.href='{{$Url}}';" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    {{$DealLost[$i]->firstname . ' ' . $DealLost[$i]->lastname}}
                                                </h6>
                                                <p class="mb-1" style="font-size: small;"><b>{{$Value}}</b></p>
                                                <p class="mb-1" style="font-size: small;"><b>{{\Carbon\Carbon::parse($DealLost[$i]->created_at)->format('m/d/Y')}}</b></p>
                                                <p class="mb-3" style="font-size: small;">{!! $LeadsController->GetLeadStatusColor($DealLost[$i]->lead_status) !!}</p>
                                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($DealLost[$i]->firstname, 0, 1) . substr($DealLost[$i]->lastname, 0, 1))}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </th>
                            </tr>
                        @endfor
                        </tbody>
                        <tfoot class="bg-white">
                        <tr>
                            <td style="width: 200px;">Total: <b>${{number_format($LeadInSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($AssignedToAcquisitionsSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($SellerUnderContractSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($AssignedToDispositionsSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($BuyerContractSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($SentToTitleSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($ClosedWonSum)}}</b></td>
                            <td style="width: 200px;">Total: <b>${{number_format($DealLostSum)}}</b></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
  @if(isset($Type))
    @if($Type != "")
    <script type="text/javascript">
        let leadfunnel_text = '<?= $Type; ?>';
        $("#leadfunnel-dropdown-value").text(leadfunnel_text + ' ');
    </script>
    @endif
  @endif
@endpush
