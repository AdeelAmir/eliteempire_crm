@extends('admin.layouts.app')
@section('content')
    <style media="screen">
      .leadAnalysisTitleSize{
        font-size: 13px;
      }
    </style>
    <div class="page-content" id="KPIPage">
        <div class="row">
          <!-- Lead Source Pie Chart -->
          <div class="col-xl-6 d-flex">
              <div class="card flex-fill">
                  <div class="card-header">
                      <div class="d-flex justify-content-between align-items-center">
                          <h5 class="card-title">Lead Source</h5>
                          <div class="dropdown" data-toggle="dropdown">
                              <a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle" role="button" data-toggle="dropdown" id="leadsource-dropdown-value">
                                  All Time
                              </a>
                              <div class="dropdown-menu dropdown-menu-right">
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Recent Week</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Recent Month</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Recent Quarter</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Recent Semester</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Recent Year</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">All Time</a>
                                  <a href="javascript:void(0);" class="dropdown-item leadsource-dropdown-item">Range</a>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3"></div>

                        <div class="col-md-3 mb-3" id="LeadSourceCustomRangeStartDate" style="display: none;">
                            <label for="customLeadSourceStartDate">Start Date</label>
                            <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                 data-link-field="customLeadSourceStartDate">
                                <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-th"></span></span>
                            </div>
                            <input type="hidden" id="customLeadSourceStartDate" name="leadSourceStartDateFilter" />
                        </div>

                        <div class="col-md-3 mb-3" id="LeadSourceCustomRangeEndDate" style="display: none;">
                            <label for="customLeadSourceEndDate">End Date</label>
                            <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                 data-link-field="customLeadSourceEndDate">
                                <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-th"></span></span>
                            </div>
                            <input type="hidden" id="customLeadSourceEndDate" name="leadSourceEndDateFilter" />
                        </div>

                        <div class="col-md-3" id="LeadSourceFilterButtonSection" style="display: none;">
                          <button style="margin-top: 32px;" class="btn btn-primary" type="button" name="leadsourcebtn" id="leadsourcebtn" onclick="LoadLeadSourceAnalysis('Range');">Filter</button>
                        </div>
                      </div>
                  </div>
                  <div class="card-body">
                      <input type="hidden" id="D4D_leadsource" value="{{$D4D_leadsource}}" />
                      <input type="hidden" id="PropStream_leadsource" value="{{$PropStream_leadsource}}" />
                      <input type="hidden" id="Calling_leadsource" value="{{$Calling_leadsource}}" />
                      <input type="hidden" id="Text_leadsource" value="{{$Text_leadsource}}" />
                      <input type="hidden" id="Facebook_leadsource" value="{{$Facebook_leadsource}}" />
                      <input type="hidden" id="Instagram_leadsource" value="{{$Instagram_leadsource}}" />
                      <input type="hidden" id="Website_leadsource" value="{{$Website_leadsource}}" />
                      <input type="hidden" id="Zillow_leadsource" value="{{$Zillow_leadsource}}" />
                      <input type="hidden" id="Wholesaler_leadsource" value="{{$Wholesaler_leadsource}}" />
                      <input type="hidden" id="Realtor_leadsource" value="{{$Realtor_leadsource}}" />
                      <input type="hidden" id="Investor_leadsource" value="{{$Investor_leadsource}}" />
                      <input type="hidden" id="Radio_leadsource" value="{{$Radio_leadsource}}" />
                      <input type="hidden" id="JVPartner_leadsource" value="{{$JVPartner_leadsource}}" />
                      <input type="hidden" id="BandedSign_leadsource" value="{{$BandedSign_leadsource}}" />

                      <div id="leadsource_chart"></div>
                      <div class="text-center text-muted">
                          <div class="row no-gutters">
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-primary mr-1"></i>
                                          D4D
                                      </p>
                                      <h5 id="D4D_leadsourceDisplay">{{$D4D_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-secondary mr-1"></i>
                                          PropStream
                                      </p>
                                      <h5 id="PropStream_leadsourceDisplay">{{$PropStream_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-success mr-1"></i>
                                          Calling
                                      </p>
                                      <h5 id="Calling_leadsourceDisplay">{{$Calling_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-warning mr-1"></i>
                                          Text
                                      </p>
                                      <h5 id="Text_leadsourceDisplay">{{$Text_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #cc0099;"></i>
                                          Facebook
                                      </p>
                                      <h5 id="Facebook_leadsourceDisplay">{{$Facebook_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #9999ff;"></i>
                                          Instagram
                                      </p>
                                      <h5 id="Instagram_leadsourceDisplay">{{$Instagram_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #cc0000;"></i>
                                          Website
                                      </p>
                                      <h5 id="Website_leadsourceDisplay">{{$Website_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #ff9900;"></i>
                                          Zillow
                                      </p>
                                      <h5 id="Zillow_leadsourceDisplay">{{$Zillow_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #66ffff;"></i>
                                          Wholesaler
                                      </p>
                                      <h5 id="Wholesaler_leadsourceDisplay">{{$Wholesaler_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #336699;"></i>
                                          Realtor
                                      </p>
                                      <h5 id="Realtor_leadsourceDisplay">{{$Realtor_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #666699;"></i>
                                          Investor
                                      </p>
                                      <h5 id="Investor_leadsourceDisplay">{{$Investor_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #993300;"></i>
                                          Radio
                                      </p>
                                      <h5 id="Radio_leadsourceDisplay">{{$Radio_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #999966;"></i>
                                          JV Partner
                                      </p>
                                      <h5 id="JVPartner_leadsourceDisplay">{{$JVPartner_leadsource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #FFC0CB;"></i>
                                          Banded Sign
                                      </p>
                                      <h5 id="BandedSign_leadsourceDisplay">{{$BandedSign_leadsource}}</h5>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Data Source Pie Chart -->
          <div class="col-xl-6 d-flex">
              <div class="card flex-fill">
                  <div class="card-header">
                      <div class="d-flex justify-content-between align-items-center">
                          <h5 class="card-title">Data Source</h5>
                          <div class="dropdown" data-toggle="dropdown">
                              <a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle" role="button" data-toggle="dropdown" id="datasource-dropdown-value">
                                  All Time
                              </a>
                              <div class="dropdown-menu dropdown-menu-right">
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Recent Week</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Recent Month</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Recent Quarter</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Recent Semester</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Recent Year</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">All Time</a>
                                  <a href="javascript:void(0);" class="dropdown-item datasource-dropdown-item">Range</a>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3"></div>

                        <div class="col-md-3 mb-3" id="DataSourceCustomRangeStartDate" style="display: none;">
                            <label for="customDataSourceStartDate">Start Date</label>
                            <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                 data-link-field="customDataSourceStartDate">
                                <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-th"></span></span>
                            </div>
                            <input type="hidden" id="customDataSourceStartDate" name="dataSourceStartDateFilter" />
                        </div>

                        <div class="col-md-3 mb-3" id="DataSourceCustomRangeEndDate" style="display: none;">
                            <label for="customDataSourceEndDate">End Date</label>
                            <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                 data-link-field="customDataSourceEndDate">
                                <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-th"></span></span>
                            </div>
                            <input type="hidden" id="customDataSourceEndDate" name="dataSourceEndDateFilter" />
                        </div>

                        <div class="col-md-3" id="DataSourceFilterButtonSection" style="display: none;">
                          <button style="margin-top: 32px;" class="btn btn-primary" type="button" name="datasourcebtn" id="datasourcebtn" onclick="LoadDataSourceAnalysis('Range');">Filter</button>
                        </div>
                      </div>
                  </div>
                  <div class="card-body">
                      <input type="hidden" id="OnMarket_datasource" value="{{$OnMarket_datasource}}" />
                      <input type="hidden" id="Vacant_datasource" value="{{$Vacant_datasource}}" />
                      <input type="hidden" id="Liens_datasource" value="{{$Liens_datasource}}" />
                      <input type="hidden" id="PreForeclosures_datasource" value="{{$PreForeclosures_datasource}}" />
                      <input type="hidden" id="Auctions_datasource" value="{{$Auctions_datasource}}" />
                      <input type="hidden" id="BankOwned_datasource" value="{{$BankOwned_datasource}}" />
                      <input type="hidden" id="CashBuyers_datasource" value="{{$CashBuyers_datasource}}" />
                      <input type="hidden" id="HighEquity_datasource" value="{{$HighEquity_datasource}}" />
                      <input type="hidden" id="FreeClear_datasource" value="{{$FreeClear_datasource}}" />
                      <input type="hidden" id="Bankruptcy_datasource" value="{{$Bankruptcy_datasource}}" />
                      <input type="hidden" id="Divorce_datasource" value="{{$Divorce_datasource}}" />
                      <input type="hidden" id="TaxDelinquencies_datasource" value="{{$TaxDelinquencies_datasource}}" />
                      <input type="hidden" id="Flippers_datasource" value="{{$Flippers_datasource}}" />
                      <input type="hidden" id="FailedListings_datasource" value="{{$FailedListings_datasource}}" />
                      <input type="hidden" id="SeniorOwners_datasource" value="{{$SeniorOwners_datasource}}" />
                      <input type="hidden" id="VacantLand_datasource" value="{{$VacantLand_datasource}}" />
                      <input type="hidden" id="TiredLandlords_datasource" value="{{$TiredLandlords_datasource}}" />
                      <input type="hidden" id="PreProbate_datasource" value="{{$PreProbate_datasource}}" />
                      <input type="hidden" id="Others_datasource" value="{{$Others_datasource}}" />

                      <div id="datasource_chart"></div>
                      <div class="text-center text-muted">
                          <div class="row no-gutters">
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-primary mr-1"></i>
                                          On Market
                                      </p>
                                      <h5 id="OnMarket_datasourceDisplay">{{$OnMarket_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-secondary mr-1"></i>
                                          Vacant
                                      </p>
                                      <h5 id="Vacant_datasourceDisplay">{{$Vacant_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-success mr-1"></i>
                                          Liens
                                      </p>
                                      <h5 id="Liens_datasourceDisplay">{{$Liens_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle text-warning mr-1"></i>
                                          Pre-Foreclosures
                                      </p>
                                      <h5 id="PreForeclosures_datasourceDisplay">{{$PreForeclosures_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #cc0099;"></i>
                                          Auctions
                                      </p>
                                      <h5 id="Auctions_datasourceDisplay">{{$Auctions_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #9999ff;"></i>
                                          Bank Owned
                                      </p>
                                      <h5 id="BankOwned_datasourceDisplay">{{$BankOwned_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #cc0000;"></i>
                                          Cash Buyers
                                      </p>
                                      <h5 id="CashBuyers_datasourceDisplay">{{$CashBuyers_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #ff9900;"></i>
                                          High Equity
                                      </p>
                                      <h5 id="HighEquity_datasourceDisplay">{{$HighEquity_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #66ffff;"></i>
                                          Free & Clear
                                      </p>
                                      <h5 id="FreeClear_datasourceDisplay">{{$FreeClear_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #336699;"></i>
                                          Bankruptcy
                                      </p>
                                      <h5 id="Bankruptcy_datasourceDisplay">{{$Bankruptcy_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #666699;"></i>
                                          Divorce
                                      </p>
                                      <h5 id="Divorce_datasourceDisplay">{{$Divorce_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #993300;"></i>
                                          Tax Delinquencies
                                      </p>
                                      <h5 id="TaxDelinquencies_datasourceDisplay">{{$TaxDelinquencies_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #999966;"></i>
                                          Flippers
                                      </p>
                                      <h5 id="Flippers_datasourceDisplay">{{$Flippers_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #009999;"></i>
                                          Failed Listings
                                      </p>
                                      <h5 id="FailedListings_datasourceDisplay">{{$FailedListings_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #cc00ff;"></i>
                                          Senior Owners
                                      </p>
                                      <h5 id="SeniorOwners_datasourceDisplay">{{$SeniorOwners_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #666633;"></i>
                                          Vacant Land
                                      </p>
                                      <h5 id="VacantLand_datasourceDisplay">{{$VacantLand_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #990099;"></i>
                                          Tired Landlords
                                      </p>
                                      <h5 id="TiredLandlords_datasourceDisplay">{{$TiredLandlords_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #80bfff;"></i>
                                          Pre-Probate
                                      </p>
                                      <h5 id="PreProbate_datasourceDisplay">{{$PreProbate_datasource}}</h5>
                                  </div>
                              </div>
                              <div class="col-2">
                                  <div class="mt-4">
                                      <p class="mb-2 text-truncate">
                                          <i class="fas fa-circle mr-1" style="color: #80bfff;"></i>
                                          Others
                                      </p>
                                      <h5 id="Others_datasourceDisplay">{{$Others_datasource}}</h5>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <!-- Lead Status Bar Chart -->
          <div class="col-xl-12 d-flex mt-3">
                {{--Leads--}}
                <input type="hidden" id="leadinLeadsAnalysis" value="{{$LeadIn}}" />
                <input type="hidden" id="interestedLeadsAnalysis" value="{{$Interested}}" />
                <input type="hidden" id="notinterestedLeadsAnalysis" value="{{$NotInterested}}" />
                <input type="hidden" id="donotcallLeadsAnalysis" value="{{$DoNotCall}}" />
                <input type="hidden" id="noanswerLeadsAnalysis" value="{{$NoAnswer}}" />
                <input type="hidden" id="wrongnumberLeadsAnalysis" value="{{$WrongNumber}}" />
                <input type="hidden" id="offernotgivenLeadsAnalysis" value="{{$OfferNotGiven}}" />
                <input type="hidden" id="offernotaceptedLeadsAnalysis" value="{{$OfferNotAccepted}}" />
                <input type="hidden" id="acceptedLeadsAnalysis" value="{{$Accepted}}" />
                <input type="hidden" id="negotiatingwithsellerLeadsAnalysis" value="{{$NegotiatingWithSeller}}" />
                <input type="hidden" id="agreementsentLeadsAnalysis" value="{{$AgreementSent}}" />
                <input type="hidden" id="agreementreceivedLeadsAnalysis" value="{{$AgreementReceived}}" />
                <input type="hidden" id="sendtoinvestorLeadsAnalysis" value="{{$SendToInvestor}}" />
                <input type="hidden" id="negotiationwithinvestorLeadsAnalysis" value="{{$NegotiationWithInvestor}}" />
                <input type="hidden" id="senttotitleLeadsAnalysis" value="{{$SendToTitle}}" />
                <input type="hidden" id="sendcontracttoinvestorLeadsAnalysis" value="{{$SendContractToInvestor}}" />
                <input type="hidden" id="emdreceivedLeadsAnalysis" value="{{$EMDReceived}}" />
                <input type="hidden" id="emdnotreceivedLeadsAnalysis" value="{{$EMDNotReceived}}" />
                <input type="hidden" id="inspectionLeadsAnalysis" value="{{$Inspection}}" />
                <input type="hidden" id="closedOnLeadsAnalysis" value="{{$CloseOn}}" />
                <input type="hidden" id="closedWonLeadsAnalysis" value="{{$ClosedWon}}" />
                <input type="hidden" id="deallostLeadsAnalysis" value="{{$DealLost}}" />

                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Lead Status Analytics</h5>
                            <div class="dropdown" data-toggle="dropdown">
                                <a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle" role="button" data-toggle="dropdown" id="leadsstatus-dropdown-value">
                                    All Time
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Recent Week</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Recent Month</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Recent Quarter</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Recent Semester</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Recent Year</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">All Time</a>
                                    <a href="javascript:void(0);" class="dropdown-item leadsstatus-dropdown-item">Range</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4"></div>

                          <div class="col-md-2 mb-3" id="LeadStatusCustomRangeStartDate" style="display: none;">
                              <label for="customLeadStatusStartDate">Start Date</label>
                              <div class="input-group date startDateFilter" data-date-format="mm/dd/yyyy"
                                   data-link-field="customLeadStatusStartDate">
                                  <input class="form-control" size="16" type="text" id="startDateTextFilter1">
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                  <span class="input-group-addon"><span
                                              class="glyphicon glyphicon-th"></span></span>
                              </div>
                              <input type="hidden" id="customLeadStatusStartDate" name="leadStatusStartDateFilter" />
                          </div>

                          <div class="col-md-2 mb-3" id="LeadStatusCustomRangeEndDate" style="display: none;">
                              <label for="customLeadStatusEndDate">End Date</label>
                              <div class="input-group date endDateFilter" data-date-format="mm/dd/yyyy"
                                   data-link-field="customLeadStatusEndDate">
                                  <input class="form-control" size="16" type="text" id="startDateTextFilter2">
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                  <span class="input-group-addon"><span
                                              class="glyphicon glyphicon-th"></span></span>
                              </div>
                              <input type="hidden" id="customLeadStatusEndDate" name="leadStatusEndDateFilter" />
                          </div>

                          <div class="col-md-4" id="LeadStatusFilterButtonSection" style="display: none;">
                            <button style="margin-top: 32px;" class="btn btn-primary" type="button" name="leadstatusbtn" id="leadstatusbtn" onclick="LoadClosedWonInterestedAnalysis('Range');">Filter</button>
                          </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters text-center">
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Lead In</span>
                                <p class="h3 text-secondary" id="_leadinLeadsDisplay" style="font-size: large;">{{$LeadIn}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Interested</span>
                                <p class="h3 text-secondary" id="_interestedLeadsDisplay" style="font-size: large;">{{$Interested}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Not Interested</span>
                                <p class="h3 text-secondary" id="_notinterestedLeadsDisplay" style="font-size: large;">{{$NotInterested}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Do Not Call</span>
                                <p class="h3 text-secondary" id="_donotcallLeadsDisplay" style="font-size: large;">{{$DoNotCall}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">No Answer</span>
                                <p class="h3 text-secondary" id="_noanswerLeadsDisplay" style="font-size: large;">{{$NoAnswer}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Wrong Number</span>
                                <p class="h3 text-secondary" id="_wrongnumberLeadsDisplay" style="font-size: large;">{{$WrongNumber}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Offer Not Given</span>
                                <p class="h3 text-secondary" id="_offernotgivenLeadsDisplay" style="font-size: large;">{{$OfferNotGiven}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Offer Not Accepted</span>
                                <p class="h3 text-secondary" id="_offernotaceptedLeadsDisplay" style="font-size: large;">{{$OfferNotAccepted}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Accepted</span>
                                <p class="h3 text-secondary" id="_aceptedLeadsDisplay" style="font-size: large;">{{$Accepted}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Negotiating with Seller</span>
                                <p class="h3 text-secondary" id="_negotiatingwithsellerLeadsDisplay" style="font-size: large;">{{$NegotiatingWithSeller}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Agreement Sent</span>
                                <p class="h3 text-secondary" id="_agreementsentLeadsDisplay" style="font-size: large;">{{$AgreementSent}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Agreement Received</span>
                                <p class="h3 text-secondary" id="_agreementreceivedLeadsDisplay" style="font-size: large;">{{$AgreementReceived}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Send To Investor</span>
                                <p class="h3 text-secondary" id="_sendtoinvestorLeadsDisplay" style="font-size: large;">{{$SendToInvestor}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Negotiation with Investors</span>
                                <p class="h3 text-secondary" id="_negotiationwithinvestorLeadsDisplay" style="font-size: large;">{{$NegotiationWithInvestor}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Sent to Title</span>
                                <p class="h3 text-secondary" id="_senttotitleLeadsDisplay" style="font-size: large;">{{$SendToTitle}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Send Contract to Investor</span>
                                <p class="h3 text-secondary" id="_sendcontracttoinvestorLeadsDisplay" style="font-size: large;">{{$SendContractToInvestor}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">EMD Received</span>
                                <p class="h3 text-secondary" id="_emdreceivedLeadsDisplay" style="font-size: large;">{{$EMDReceived}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">EMD Not Received</span>
                                <p class="h3 text-secondary" id="_emdnotreceivedLeadsDisplay" style="font-size: large;">{{$EMDNotReceived}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Inspection</span>
                                <p class="h3 text-secondary" id="_inspectionLeadsDisplay" style="font-size: large;">{{$Inspection}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Close On</span>
                                <p class="h3 text-secondary" id="_closeOnLeadsDisplay" style="font-size: large;">{{$CloseOn}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Closed Won</span>
                                <p class="h3 text-primary" id="_closedWonLeadsDisplay" style="font-size: large;">{{$ClosedWon}}</p>
                            </div>
                            <div class="col-1">
                                <span class="leadAnalysisTitleSize">Deal Lost</span>
                                <p class="h3 text-secondary" id="_deallostLeadsDisplay" style="font-size: large;">{{$DealLost}}</p>
                            </div>
                          </div>
                        </div>
                        <div id="leadstatus_chart"></div>
                    </div>
                </div>
              </div>
            </div>
            <!-- Lead Status Bar Chart -->
        </div>
    </div>
@endsection
