@extends('admin.layouts.app')
@section('content')
    <style media="screen">
        .select2-container {
            width: 100% !important;
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
            margin-top: 40px;
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
          margin-bottom: 0px;
        }

        /* End of step form progress bar */
    </style>
    <div class="page-content" id="addNewLeadPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">New Lead</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('global_manager/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 3)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('acquisition_manager/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 4)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('disposition_manager/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 5)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('acquisition_representative/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 6)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('disposition_representative/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 7)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('cold_caller/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>
        <?php
        $Url = "";
        if ($Role == 1) {
            $Url = url('admin/lead/store');
        } elseif ($Role == 2) {
            $Url = url('global_manager/lead/store');
        } elseif ($Role == 3) {
            $Url = url('acquisition_manager/lead/store');
        } elseif ($Role == 4) {
            $Url = url('disposition_manager/lead/store');
        } elseif($Role == 5) {
            $Url = url('acquisition_representative/lead/store');
        } elseif($Role == 6) {
            $Url = url('disposition_representative/lead/store');
        } elseif($Role == 7) {
            $Url = url('cold_caller/lead/store');
        } elseif($Role == 8) {
            $Url = url('affiliate/lead/store');
        } elseif($Role == 9) {
            $Url = url('realtor/lead/store');
        }
        ?>
        <form action="{{$Url}}" method="post" id="addLeadForm" enctype="multipart/form-data">
            @csrf
            <div class="row" id="addLeadPage">
                <div class="col-12">
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
            <section class="contact-area mb-3">
                <div class="container">
                    <div class="row" style="margin:auto;">
                        <div class="col-lg-12">
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
                    </div>
                </div>
            </section>
            {{--Step Bar--}}

            {{--Seller and Property Details--}}
            <section class="contact-area pb-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
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
                                            <input type="button" class="btn btn-primary w-15 float-right" value="Next"
                                                   onclick="ShowPropertyInformation();" id="continueBtn"/>
                                            <input type="button" class="btn btn-primary w-15 float-left" value="Back"
                                                   style="display: none;" onclick="ShowSellerInformation();"
                                                   id="backBtn"/>
                                            <input type="submit" class="btn btn-primary w-15 float-right" value="Submit"
                                                   style="display: none;" id="submitBtn"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>

    @include('admin.includes.leadSentModal')
@endsection

@push('scripts')
    @if(session()->has('leadStore'))
        <script>
            $(document).ready(function () {
                $("#leadSentModal").modal('toggle');
            });
        </script>
    @endif
@endpush
