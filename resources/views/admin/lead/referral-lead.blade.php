<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.partials.head')
    <style media="screen">
        .select2-container {
            width: 100% !important;
        }

        .btn-primary.disabled, .swal2-modal .swal2-actions button.disabled.swal2-confirm, .wizard > .actions a.disabled, .btn-primary:disabled, .swal2-modal .swal2-actions button.swal2-confirm:disabled, .wizard > .actions a:disabled {
            color: #fff;
            background-color: #15D16C;
            border-color: #15D16C;
        }

        /*Slider*/
        .slidecontainer {
            width: 100%;
            margin-top: -5px;
        }

        .slider {
            -webkit-appearance: none;
            width: 100%;
            height: 1px;
            border-radius: 5px;
            background: #000;
            outline: none;
            opacity: 0.7;
            -webkit-transition: .2s;
            transition: opacity .2s;
        }

        .slider:hover {
            opacity: 1;
        }

        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #15D16C;
            cursor: pointer;
        }

        .slider::-moz-range-thumb {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #15D16C;
            cursor: pointer;
        }

        /*Slider*/

        /* Step Form Progress bar start */
        .bs-wizard {
            margin-top: 20px;
        }

        .bs-wizard {
            border-bottom: solid 1px #e0e0e0;
            padding: 0 0 10px 0;
        }

        .bs-wizard > .bs-wizard-step {
            padding: 0;
            position: relative;
            width: 18%;
        }

        .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {
            color: #595959;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .bs-wizard > .bs-wizard-step .bs-wizard-info {
            color: #999;
            font-size: 14px;
        }

        .bs-wizard > .bs-wizard-step > .bs-wizard-dot {
            position: absolute;
            width: 30px;
            height: 30px;
            display: block;
            background: #15D16C;
            top: 45px;
            left: 50%;
            margin-top: -15px;
            margin-left: -15px;
            border-radius: 50%;
        }

        .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {
            content: ' ';
            width: 14px;
            height: 14px;
            background: #ffffff;
            border-radius: 50px;
            position: absolute;
            top: 8px;
            left: 8px;
        }

        .bs-wizard > .bs-wizard-step > .progress {
            position: relative;
            border-radius: 0px;
            height: 8px;
            box-shadow: none;
            margin: 20px 0;
        }

        .bs-wizard > .bs-wizard-step > .progress > .progress-bar {
            width: 0px;
            box-shadow: none;
            background: #15D16C;
        }

        .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {
            width: 100%;
        }

        .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {
            width: 50%;
        }

        .bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {
            width: 0%;
        }

        .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {
            width: 100%;
        }

        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {
            background-color: #f5f5f5;
        }

        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {
            opacity: 0;
        }

        .bs-wizard > .bs-wizard-step:first-child > .progress {
            left: 50%;
            width: 50%;
        }

        .bs-wizard > .bs-wizard-step:last-child > .progress {
            width: 50%;
        }

        .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot {
            pointer-events: none;
        }

        .StepBar {
            margin-top: -50px;
        }

        form div {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
<div class="container">
    {{--Header--}}
    <div class="row">
        <div class="col-12 m-auto py-4">
            <div class="logo text-center mb-2">
                <img src="{{ asset('public/storage/logo/logo.png')}}" alt="logo-small" style="width: 250px;" class="img-fluid">
            </div>
        </div>
    </div>
    <?php
    $Url = url('/referral/lead/store');
    ?>
    <form action="{{$Url}}" method="post" id="addLeadForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" id="user_id" value="{{$UserId}}" />
        <div class="row" id="addLeadPage">
            <div class="col-md-1"></div>
            <div class="col-10">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {!! session('message') !!}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif
            </div>
            <div class="col-md-1"></div>
        </div>

        <input type="hidden" id="offer_range_low" name="offer_range_low" value="0"/>
        <input type="hidden" id="offer_range_high" name="offer_range_high" value="0"/>
        <input type="hidden" step="any" name="rehab_cost" id="rehab_cost" class="form-control"
               placeholder="Rehab Cost" onchange="PerformLeadCalculations();" required/>
        <input type="hidden" step="any" name="arv_rehab_cost" id="arv_rehab_cost" class="form-control"
               placeholder="ARV-Rehab Cost" required/>
        <input type="hidden" step="any" name="arv_sales_closing_cost" id="arv_sales_closing_cost"
               class="form-control"
               placeholder="ARV (Sales + Closing Cost)" required/>
        <input type="hidden" step="any" name="property_total_value" id="property_total_value" class="form-control"
               placeholder="Property Total Value" required/>
        <input type="hidden" step="any" name="wholesales_closing_cost" id="wholesales_closing_cost"
               class="form-control"
               placeholder="Wholesales Closing Cost" required/>
        <input type="hidden" step="any" name="all_in_cost" id="all_in_cost" class="form-control"
               placeholder="All In Cost" required/>
        <input type="hidden" step="any" name="investor_profit" id="investor_profit" class="form-control"
               placeholder="Investor Profit" required/>
        <input type="hidden" step="any" name="sales_price" id="sales_price" class="form-control"
               placeholder="Sales Price" required/>
        <input type="hidden" step="any" name="m_a_o" id="m_a_o" class="form-control"
               placeholder="M.A.O" required/>

        {{--Step Bar--}}
        <div class="row mb-3" style="margin:auto;">
            <div class="col-md-1"></div>
            <div class="col-lg-10">
                <div class="row bs-wizard" style="border-bottom:0;">
                    <div class="col-md-4 col-sm-3 col-xs-3"></div>

                    <div class="col-xs-12 bs-wizard-step step1 complete">
                        <div class="text-center bs-wizard-stepnum">Step 1</div>
                        <div class="progress">
                            <div class="progress-bar"></div>
                        </div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Seller Information</div>
                    </div>

                    <div class="col-xs-12 bs-wizard-step step2 disabled"><!-- complete -->
                        <div class="text-center bs-wizard-stepnum">Step 2</div>
                        <div class="progress">
                            <div class="progress-bar"></div>
                        </div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Property Information</div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
        {{--Step Bar--}}

        {{--Seller and Property Details--}}
        <div class="row mb-3">
            <div class="col-md-1"></div>
            <div class="col-md-10 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title" id="leadStepHeading">
                            Seller Information
                        </h6>
                        <div class="row" id="leadSellerInformation">
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="firstName"><strong>First Name</strong></label>
                                <input type="text" name="firstName" id="firstName" class="form-control"
                                       placeholder="First Name" required/>
                                <div style="margin-top: 7px;" id="f_name"></div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="middleName">Middle Name</label>
                                <input type="text" name="middleName" id="middleName" class="form-control"
                                       placeholder="Middle Name"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="lastName"><strong>Last Name</strong></label>
                                <input type="text" name="lastName" id="lastName" class="form-control"
                                       placeholder="Last Name" required/>
                                <div style="margin-top: 7px;" id="l_name"></div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="language">Language</label>
                                <select name="language" id="language" class="form-control">
                                    <option value="">Select Language</option>
                                    <option value="english" selected>English</option>
                                    <option value="spanish">Spanish</option>
                                    <option value="bilingual">Bilingual</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="martial_status">Marital Status</label>
                                <select name="martial_status" id="martial_status" class="form-control"
                                        onchange="checkMaritalStatus();">
                                    <option value="">Marital Status</option>
                                    <option value="married">Married</option>
                                    <option value="single">Single</option>
                                    <option value="unknown" selected >Unknown</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2" id="_SpouceBlock" style="display:none;">
                                <label class="w-100" for="spouce">Spouse</label>
                                <input type="text" name="spouce" id="spouce" class="form-control"
                                       placeholder="Spouse Name"/>
                            </div>
                            <div class="col-md-3 mb-2" id="sellerPhone1">
                                <label for="phone" class="w-100"><strong>Phone Number 1</strong><i
                                            class="fa fa-plus-circle float-right" style="cursor: pointer;"
                                            onclick="AddPhoneField();"></i></label>
                                <input type="number" step="any" name="phone" id="phone" class="form-control"
                                       placeholder="Enter Your Phone Number" maxlength="20" required/>
                                <div style="margin-top: 7px;" id="p_phone1"></div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                       placeholder="Email"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="ownersOccupy">Owner's Occupy</label>
                                <select name="ownersOccupy" id="ownersOccupy" class="form-control">
                                    <option value="">Owner's Occupy</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="occupancyStatus">Occupancy Status</label>
                                <select name="occupancyStatus" id="occupancyStatus" class="form-control" onchange="if(this.value === 'vacant') { document.getElementById('buildingSize').value = 0; } else { document.getElementById('buildingSize').value = ''; } ">
                                    <option value="">Occupancy Status</option>
                                    <option value="vacant">Vacant</option>
                                    <option value="occupied">Occupied</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" id="leadPropertyInformation" style="display: none;">
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="street"><strong>Street</strong></label>
                                <input type="text" name="street" id="street" class="form-control"
                                       placeholder="Street" required/>
                                <div style="margin-top: 7px;" id="s_street"></div>
                            </div>
                            <div class="col-md-3 mb-3" id="AddLeadCitySection" style="display:none;">
                                <label for="city">City</label>
                                <select name="city" id="city" class="form-control">
                                    <option value="" selected>Select City</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="state">State</label>
                                <select class="form-control" name="state" id="state" onchange="LoadStateCountyCity();">
                                    <option value="" selected>Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{$state->name}}">{{$state->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="zipcode"><strong>Zip Code</strong></label>
                                <input type="number" step="any" name="zipcode" id="zipcode"
                                       class="form-control"
                                       placeholder="Zip Code" onkeypress="limitKeypress(event,this.value,5)"
                                       onblur="limitZipCodeCheck();" required/>
                                <div style="margin-top: 7px;" id="z_zipcode"></div>
                            </div>
                            <div class="col-md-3 mb-2" id="propertyClassificationDiv">
                                <label class="w-100" for="propertyClassification">Property Classification</label>
                                <select name="propertyClassification" id="propertyClassification"
                                        class="form-control" onchange="AddPropertyType(this);CheckPropertyClassification(this);">
                                    <option value="">Select</option>
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="agricultural">Agricultural</option>
                                    <option value="vacant">Vacant Lot</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Manufactured">Manufactured</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="constructionType">Construction Type</label>
                                <select name="constructionType" id="constructionType" class="form-control">
                                    <option value="">Select</option>
                                    <option value="wood">Wood</option>
                                    <option value="block">Block</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="yearBuilt">Year Built</label>
                                <input type="text" name="yearBuilt" id="yearBuilt" class="form-control"
                                       placeholder="Year Built"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="buildingSize">Livable Area</label>
                                <input type="number" step="any" name="buildingSize" id="buildingSize"
                                       class="form-control" placeholder="Livable Area (sqr ft)"
                                       onchange="CalculateRehabCost();"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="bedroom">Bedroom</label>
                                <input type="number" step="any" name="bedroom" id="bedroom"
                                       class="form-control" placeholder="Bedroom"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="bathroom">Bathroom</label>
                                <input type="number" step="any" name="bathroom" id="bathroom"
                                       class="form-control" placeholder="Bathroom"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="lotSize">Lot Square Footage</label>
                                <input type="number" step="any" name="lotSize" id="lotSize"
                                       class="form-control" placeholder="Lot Square Footage"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="homeFeature">Home Feature</label>
                                <select name="homeFeature[]" id="homeFeature" class="form-control" multiple>
                                    <option value="pool">Pool</option>
                                    <option value="garage">Garage</option>
                                    <option value="basement">Basement</option>
                                    <option value="attic">Attic</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="storiesNo">Number of Stories</label>
                                <input type="number" step="any" name="storiesNo" id="storiesNo"
                                       class="form-control" placeholder="Number of Stories"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="associationFee">Association Fee</label>
                                <select name="associationFee" id="associationFee" class="form-control">
                                    <option value="">Select</option>
                                    <option value="HOA">HOA</option>
                                    <option value="COA">COA</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="reasonsToSales">Reasons to Sales</label>
                                <select name="reasonsToSales" id="reasonsToSales" class="form-control">
                                    <option value="">Select</option>
                                    <option value="relocations">Relocations</option>
                                    <option value="tired_landlord">Tired landlord</option>
                                    <option value="payment_issue">Payment Issue</option>
                                    <option value="family_transfer">Family Transfer</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="conditions">Condition</label>
                                <select name="conditions" id="conditions" class="form-control"
                                        onchange="CalculateRehabCost();">
                                    <option value="">Select</option>
                                    <option value="basic">Basic</option>
                                    <option value="light">Light</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="full">Full</option>
                                    <option value="heavy">Heavy</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="leadSources">Lead Sources</label>
                                <select name="leadSources" id="leadSources" class="form-control">
                                    <option value="">Select</option>
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
                                    <option value="banded_sign">Banded Sign</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="askingPrice">Asking Price</label>
                                <input type="number" step="any" name="askingPrice" id="askingPrice"
                                       class="form-control"
                                       placeholder="Asking Price" onchange="PerformLeadCalculations();"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="arv">ARV</label>
                                <input type="number" step="any" name="arv" id="arv" class="form-control"
                                       placeholder="After Repair Value"
                                       onchange="PerformLeadCalculations();"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="assignment_fee">Assignment Fee</label>
                                <input type="number" step="any" name="assignment_fee" id="assignment_fee"
                                       class="form-control"
                                       placeholder="Assignment Fee" onchange="PerformLeadCalculations();"/>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="data_source">Data Sources</label>
                                <select name="data_source[]" id="data_source" class="form-control" multiple>
                                    <option value="" disabled="disabled">Select</option>
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
                            <div class="col-md-3 mb-2">
                                <label class="w-100" for="pictureLink">Picture</label>
                                <input type="text" name="pictureLink" id="pictureLink" class="form-control"
                                       placeholder="Link" />
                            </div>
                        </div>

                        {{--Submit Button--}}
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                <input type="button" class="btn btn-primary w-10 float-right" value="Next"
                                       onclick="ShowPropertyInformation();" id="continueBtn"/>
                                <input type="button" class="btn btn-primary w-10 float-left" value="Back"
                                       style="display: none;" onclick="ShowSellerInformation();"
                                       id="backBtn"/>
                                <input type="submit" class="btn btn-primary w-10 float-right" value="Submit"
                                       style="display: none;" id="submitBtn"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </form>
</div>

{{--Lead Sent Modal--}}
<div class="modal fade" id="leadSentModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="card">
                <div class="card-body text-center">
                    <img id="leadSentModalImg" src="{{asset('public/storage/logo/checked.png')}}" alt="" style="width: 100px;" />
                    <h2 class="text-center mt-2">Awesome!</h2>
                    <p class="mt-2 mb-0">
                        Your lead has been sent.
                    </p>
                    <p class="text-center mt-2 mb-5">
                        @if(session()->has('leadStore'))
                            Lead Number: <b>{{session()->get('leadStore')}}</b>
                        @endif
                    </p>
                    <button class="btn btn-primary w-100" type="button" onclick="window.location.reload();">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.partials.footer-scripts')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function () {
        @if(session()->has('leadStore'))
        $("#leadSentModal").modal('toggle');
        @endif

        $(document).on("click", ".deletePhoneField", function (e) {
            PhoneField--;
            let id = this.id.split('_')[1];
            $("#sellerPhone" + id).remove();
        });
        $("#ownersOccupy").select2();
        $("#occupancyStatus").select2();
        $("#martial_status").select2();
        $("#language").select2();
        $("#city").select2();
        $("#state").select2();
        $("#propertyClassification").select2();
        $("#constructionType").select2();
        $("#homeFeature").select2();
        $("#associationFee").select2();
        $("#reasonsToSales").select2();
        $("#conditions").select2();
        $("#leadSources").select2();
        $("#data_source").select2();
    });


    let PhoneField = 1;
    function PerformLeadCalculations() {
        let ARV_SALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('ARV_SALES_CLOSING_COST_CONSTANT')}}");
        let WHOLESALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('WHOLESALES_CLOSING_COST_CONSTANT')}}");
        let INVESTOR_PROFIT_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('INVESTOR_PROFIT_CONSTANT')}}");
        let OFFER_LOWER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_LOWER_RANGE_CONSTANT')}}");
        let OFFER_HIGHER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_HIGHER_RANGE_CONSTANT')}}");

        let AskingPrice = $("#askingPrice").val();
        let ARV = $("#arv").val();
        let AssignmentFee = $("#assignment_fee").val();
        let RehabCost = $("#rehab_cost").val();

        if (AskingPrice !== '' && ARV !== '' && AssignmentFee !== '' && RehabCost !== '') {
            let ARV_REHAB_COST = parseFloat(ARV) - parseFloat(RehabCost);
            ARV_REHAB_COST = Math.round(ARV_REHAB_COST * 100.0) / 100.0;
            $("#arv_rehab_cost").val(ARV_REHAB_COST);
            let ARV_SALES_CLOSING_COST = (ARV_SALES_CLOSING_COST_CONSTANT * parseFloat(ARV)) / 100;
            ARV_SALES_CLOSING_COST = Math.round(ARV_SALES_CLOSING_COST * 100.0) / 100.0;
            $("#arv_sales_closing_cost").val(ARV_SALES_CLOSING_COST);
            let PropertyValue = ARV_REHAB_COST - ARV_SALES_CLOSING_COST;
            PropertyValue = Math.round(PropertyValue * 100.0) / 100.0;
            $("#property_total_value").val(PropertyValue);
            let WholeSales_Closing_Cost = (WHOLESALES_CLOSING_COST_CONSTANT * parseFloat(PropertyValue)) / 100;
            WholeSales_Closing_Cost = Math.round(WholeSales_Closing_Cost * 100.0) / 100.0;
            $("#wholesales_closing_cost").val(WholeSales_Closing_Cost);
            let All_In_Cost = parseFloat(RehabCost) + parseFloat(PropertyValue) + parseFloat(WholeSales_Closing_Cost);
            All_In_Cost = Math.round(All_In_Cost * 100.0) / 100.0;
            $("#all_in_cost").val(All_In_Cost);
            let InvestorProfit = (INVESTOR_PROFIT_CONSTANT * parseFloat(All_In_Cost)) / 100;
            InvestorProfit = Math.round(InvestorProfit * 100.0) / 100.0;
            $("#investor_profit").val(InvestorProfit);
            let SalesPrice = PropertyValue - InvestorProfit;
            SalesPrice = Math.round(SalesPrice * 100.0) / 100.0;
            $("#sales_price").val(SalesPrice);
            let MAO = SalesPrice - parseFloat(AssignmentFee);
            MAO = Math.round(MAO * 100.0) / 100.0;
            let OfferLowerRange = parseFloat(MAO) - OFFER_LOWER_RANGE_CONSTANT;
            OfferLowerRange = Math.round(OfferLowerRange * 100.0) / 100.0;
            let OfferHigherRange = parseFloat(MAO) - OFFER_HIGHER_RANGE_CONSTANT;
            OfferHigherRange = Math.round(OfferHigherRange * 100.0) / 100.0;
            $("#m_a_o").val(MAO);
            $("#offer_range_low").val(OfferLowerRange);
            $("#offer_range_low_value").text(OfferLowerRange);
            $("#offer_range_high").val(OfferHigherRange);
            $("#offer_range_high_value").text(OfferHigherRange);
            $("#offerRangeLowValue").text("$" + Math.round(OfferLowerRange));
            $("#offerRangeHighValue").text("$" + Math.round(OfferHigherRange));
        } else {
            /*Reset All Fields*/
            $("#arv_rehab_cost").val(null);
            $("#arv_sales_closing_cost").val(null);
            $("#property_total_value").val(null);
            $("#wholesales_closing_cost").val(null);
            $("#all_in_cost").val(null);
            $("#investor_profit").val(null);
            $("#sales_price").val(null);
            $("#m_a_o").val(null);
            $("#offer_range_low").val(1);
            $("#offer_range_low_value").text(1);
            $("#offer_range_high").val(100);
            $("#offer_range_high_value").text(100);
        }
    }


    function AddPhoneField() {
        if (PhoneField === 5) {
            return;
        }
        PhoneField++;
        let Field = '<div class="col-md-3 mb-2" id="sellerPhone' + PhoneField + '">' +
            '           <label class="w-100" for="phone' + PhoneField + '">Phone Number ' + PhoneField + '<span><i class="fa fa-trash deletePhoneField float-right" id="deletePhoneField_' + PhoneField + '" style="cursor: pointer;"></i></span></label>' +
            '           <input type="number" step="any" name="phone' + PhoneField + '" id="phone' + PhoneField + '" class="form-control" placeholder="Phone Number" maxlength="20" required/>' +
            '       </div>';
        $("#sellerPhone" + (PhoneField - 1)).after(Field);
    }

    function LoadStateCountyCity() {
        let state = $("#state option:selected").val();
        if ($("#county").length) {
            LoadCounties(state);
        }
        if ($("#AddLeadCitySection").length) {
            $("#AddLeadCitySection").show();
        }
        LoadCities(state);
    }

    function LoadCounties(state) {
        <?php
        $Url = url('load/counties/1');
        ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {State: state}
        }).done(function (data) {
            data = JSON.parse(data);
            if ($("#county").length) {
                $("#county").html('').html(data);
            }
            if ($("#countyFilter").length) {
                $("#countyFilter").html('').html(data);
            }
        });
    }

    function LoadCities(state) {
        <?php
        $Url = url('load/cities/1');
        ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {State: state}
        }).done(function (data) {
            data = JSON.parse(data);
            if ($("#city").length) {
                $("#city").html('').html(data);
            }
            if ($("#cityFilter").length) {
                $("#cityFilter").html('').html(data);
            }
        });
    }

    function limitKeypress(event, value, maxLength) {
        if (value !== undefined && value.toString().length >= maxLength) {
            event.preventDefault();
        }
    }

    function limitZipCodeCheck() {
        let value = $('#zipcode').val();
        if (value.toString().length < 5) {
            $('#zipcode').focus();
        }
    }

    function AddPropertyType(e) {
        if (e.value === 'residential') {
            let Field = '<div class="col-md-3 mb-2" id="propertyTypeDiv">' +
                '           <label for="propertyType">Property Type</label>' +
                '           <select name="propertyType" id="propertyType" class="form-control" onchange="AddMultiFamily(this)" required>' +
                '           <option value="">Select</option>' +
                '           <option value="singleFamily">Single Family</option>' +
                '           <option value="condominium">Condominium</option>' +
                '           <option value="townhouse">Townhouse</option>' +
                '           <option value="multiFamily">Multi family</option>' +
                '           <option value="mobile">Mobile</option>' +
                '           <option value="manifactureHome">Manifacture Home</option>' +
                '       </div>';
            $("#propertyClassificationDiv").after(Field);
            $("#propertyType").select2();
        } else {
            $("#propertyTypeDiv").remove();
            $("#multiFamilyTypeDiv").remove();
        }
    }

    function CheckPropertyClassification(e) {
        if (e.value === 'vacant') {
            $("#bedroom").val(0);
            $("#bathroom").val(0);
            $("#storiesNo").val(0);
        }
    }

    function CalculateRehabCost() {
        let BuildingSize = $("#buildingSize").val();
        let conditions = $("#conditions").val();
        if (BuildingSize !== '' && conditions !== '') {
            if (conditions === 'basic') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 15).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'light') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 20).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'moderate') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 25).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'full') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 30).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'heavy') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 35).trigger('change');
                PerformLeadCalculations();
            } else {
                $("#rehab_cost").val(null).trigger('change');
                PerformLeadCalculations();
            }
        } else {
            $("#rehab_cost").val(null).trigger('change');
            PerformLeadCalculations();
        }
    }

    function ShowPropertyInformation() {
        if ($('#firstName').val() && $('#lastName').val() && $('#phone').val()) {
            $("#leadSellerInformation").hide();
            $("#leadPropertyInformation").show();
            $("#backBtn").show();
            $("#continueBtn").hide();
            $("#submitBtn").show();
            $(window).scrollTop(0);
            $("#leadStepHeading").text("Property Information");
            $(".step2").removeClass("disabled");
            $(".step2").addClass("complete");
        } else {
            // First Name
            if ($('#firstName').val()) {
                $('#f_name').hide();
            } else {
                $("#firstName").keyup(function () {
                    $('#f_name').hide();
                });
                $('#f_name').show();
                $("#f_name").html("First Name is required !").css("color", "red");
            }
            // Last Name
            if ($('#lastName').val()) {
                $('#l_name').hide();
            } else {
                $("#lastName").keyup(function () {
                    $('#l_name').hide();
                });
                $('#l_name').show();
                $("#l_name").html("Last Name is required !").css("color", "red");
            }
            // Phone Number 1
            if ($('#phone').val() !== '') {
                $('#p_phone1').hide();
            } else {
                $("#phone").keyup(function () {
                    $('#p_phone1').hide();
                });
                $('#p_phone1').show();
                $("#p_phone1").html("Phone Number 1 is required !").css("color", "red");
            }
        }
    }

    function ShowSellerInformation() {
        $("#leadSellerInformation").show();
        $("#leadPropertyInformation").hide();
        $("#backBtn").hide();
        $("#continueBtn").show();
        $("#submitBtn").hide();
        $(window).scrollTop(0);
        $("#leadStepHeading").text("Seller Information");
        $(".step2").removeClass("complete");
        $(".step2").addClass("disabled");
    }

    function AddMultiFamily(e) {
        if (e.value === 'multiFamily') {
            let Field = '<div class="col-md-3 mb-2" id="multiFamilyTypeDiv">' +
                '           <label for="multiFamilyType">Multi-Family</label>' +
                '           <select name="multiFamilyType" id="multiFamilyType" class="form-control" required>' +
                '           <option value="">Select</option>' +
                '           <option value="duplexes">Duplexes</option>' +
                '           <option value="3_4_unit_or_5_plus">3-4 Unit or 5 plus</option>' +
                '       </div>';
            $("#propertyTypeDiv").after(Field);
            $("#multiFamilyType").select2();
        } else {
            $("#multiFamilyTypeDiv").remove();
        }
    }
</script>
</body>
</html>