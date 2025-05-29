@extends('admin.layouts.app')
@section('content')
    <style media="screen">

        div.dataTables_wrapper div.dataTables_filter label {
            font-weight: normal;
            white-space: nowrap;
            text-align: left;
            display: none;
        }

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 0.85em;
            white-space: nowrap;
            display: none;
        }

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

        .collapsible {
            background-color: #15D16C;
            color: white;
            cursor: pointer;
            padding: 10px;
            width: 96%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
            /*border-radius: 10px;*/
            margin-left: 1rem;
        }

        .active1, .collapsible:hover {
            background-color: #12bc61;
        }

        .collapsible:after {
            content: '\002B';
            color: white;
            font-weight: bold;
            float: right;
            margin-left: 5px;
        }

        .active1:after {
            content: "\2212";
        }

        .content {
            padding: 0 18px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
        }

        .SaveButtonSetting{
          width: 15%;
        }

        @media only screen and (max-width: 600px) {
          .btn, .fc .fc-button, .swal2-modal .swal2-actions button {
              font-size: 0.875rem;
              line-height: 1;
              padding: .4rem .8rem .2rem !important;
          }

          .SaveButtonSetting{
            width: 30% !important;
          }
        }
    </style>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Edit Lead</span></h4>
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
                    @elseif($Role == 8)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('affiliate/leads')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <div class="row" id="editLeadPage">
            <div class="col-12">
                @if(session()->has('message'))
                    <div class="alert alert-success  mb-3">
                        {{ session('message') }}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger  mb-3">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
        <?php
        $Url = "";
        if ($Role == 1) {
            $Url = url('admin/lead/update');
        } elseif ($Role == 2) {
            $Url = url('global_manager/lead/update');
        } elseif ($Role == 3) {
            $Url = url('acquisition_manager/lead/update');
        } elseif ($Role == 4) {
            $Url = url('disposition_manager/lead/update');
        } elseif ($Role == 5) {
            $Url = url('acquisition_representative/lead/update');
        } elseif ($Role == 6) {
            $Url = url('disposition_representative/lead/update');
        } elseif ($Role == 7) {
            $Url = url('cold_caller/lead/update');
        } elseif ($Role == 8) {
            $Url = url('affiliate/lead/update');
        } elseif ($Role == 9) {
            $Url = url('realtor/lead/update');
        }
        ?>
        <form action="{{$Url}}" method="post" id="editLeadForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$Lead[0]->id}}"/>
            {{--Lead Details--}}
            <input type="hidden" id="offer_range_low1" name="offer_range_low" value="0"/>
            <input type="hidden" id="offer_range_high1" name="offer_range_high" value="0"/>
            <input type="hidden" name="rehab_cost" id="rehab_cost1" value="{{$Lead[0]->rehab_cost}}" />
            <input type="hidden" name="arv_rehab_cost" id="arv_rehab_cost1" value="{{$Lead[0]->arv_rehab_cost}}" />
            <input type="hidden" name="arv_sales_closing_cost" id="arv_sales_closing_cost1" value="{{$Lead[0]->arv_sales_closingcost}}" />
            <input type="hidden" name="property_total_value" id="property_total_value1" value="{{$Lead[0]->property_total_value}}" />
            <input type="hidden" name="wholesales_closing_cost" id="wholesales_closing_cost1" value="{{$Lead[0]->wholesales_closing_cost}}" />
            <input type="hidden" name="all_in_cost" id="all_in_cost1" value="{{$Lead[0]->all_in_cost}}" />
            <input type="hidden" name="investor_profit" id="investor_profit1" value="{{$Lead[0]->investor_profit}}" />
            <input type="hidden" name="sales_price" id="sales_price1" value="{{$Lead[0]->sales_price}}" />
            <input type="hidden" name="m_a_o" id="m_a_o1" value="{{$Lead[0]->maximum_allow_offer}}" />

            {{--Seller and Property Details--}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row align-items-center" style="margin-left: 0; margin-right: 0;">
                                        <div class="col-md-2">
                                            <b>{{$Lead[0]->lead_number}}</b>
                                            <br>
                                            {{$Profile[0]->firstname . ' ' . $Profile[0]->lastname}}
                                            <br>
                                            {{\Illuminate\Support\Carbon::parse($Lead[0]->created_at)->format('m/d/Y')}}
                                            <br>
                                            {{\Illuminate\Support\Carbon::parse($Lead[0]->created_at)->format('g:i a')}}
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-3 text-right mt-2">
                                               @if($Lead[0]->offer_range_low < 0)
                                               <span id="offerRangeLowValue1" style="color:red;">
                                                   ${{number_format(round($Lead[0]->offer_range_low))}}
                                               </span>
                                               @else
                                               <span id="offerRangeLowValue1">
                                                   ${{number_format(round($Lead[0]->offer_range_low))}}
                                               </span>
                                               @endif
                                                </div>
                                                <div class="col-6">
                                                    <div class="slidecontainer mt-3">
                                                        <input type="range" min="1" max="100" step="0.1" value="50"
                                                               class="slider" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-3 text-left mt-2">
                                               @if($Lead[0]->offer_range_high < 0)
                                               <span id="offerRangeHighValue1" style="color:red;">
                                                   ${{number_format(round($Lead[0]->offer_range_high))}}
                                               </span>
                                               @else
                                               <span id="offerRangeHighValue1">
                                                   ${{number_format(round($Lead[0]->offer_range_high))}}
                                               </span>
                                               @endif
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <?php
                                                    $LeadsController = new \App\Http\Controllers\LeadController();
                                                    echo '<span class="cursor-pointer" id="leadupdatestatus_' . $Lead[0]->id . '_1_2" onclick="showLeadUpdateStatus(this.id);">' . $LeadsController->GetLeadStatusColor($Lead[0]->lead_status) .'</span>';
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <a href="tel:{{$Lead[0]->phone}}">
                                                <button class="btn greenActionButtonTheme float-right mr-1"
                                                        type="button" data-toggle="tooltip" title="Call">
                                                    <i class="fa fa-phone"></i>
                                                </button>
                                            </a>
                                            <button class="btn greenActionButtonTheme float-right mr-1"
                                                    id="leadUpdateAppointmentTime_{{$Lead[0]->id}}" type="button"
                                                    onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip"
                                                    title="Update Appointment Time"><i class="fa fa-calendar"></i>
                                            </button>
                                            <button class="btn greenActionButtonTheme float-right mr-1"
                                                    id="leadEvaluation_{{$Lead[0]->id}}" type="button"
                                                    onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip"
                                                    title="Evaluation"><i class="fas fa-eye"></i></button>
                                            <button class="btn greenActionButtonTheme float-right mr-1"
                                                    id="assign_{{$Lead[0]->id}}" type="button"
                                                    onclick="AssignLeadToUser(this.id);" data-toggle="tooltip"
                                                    title="Assign"><i class="fas fa-arrow-alt-circle-right"></i>
                                            </button>
                                            {{--<a href="tel:{{$Lead[0]->phone}}">
                                                <img src="{{asset('public/assets/images/phone-logo.png')}}" alt="Phone Logo"
                                                     class="img-fluid float-right" style="width: 30px;"/>
                                            </a>--}}
                                            {{--<i class="greenActionButtonTheme fas fa-edit cursor-pointer float-right mr-3 mt-2" id="leadUpdateAppointmentTime_{{$Lead[0]->id}}" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Update Appointment Time"></i>--}}
                                            {{--<i class="fas fa-eye cursor-pointer float-right mr-3 mt-2" id="leadEvaluation_{{$Lead[0]->id}}" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"></i>--}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-top: -15px;">
                                    <hr style="width: 96%;">
                                </div>

                                <div class="col-md-12">
                                    <button type="button" class="collapsible">Seller Information</button>
                                    <div class="content">
                                        <div class="row pt-2 pb-2">
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="firstName"><strong>First Name</strong></label>
                                                <input type="text" name="firstName" id="firstName"
                                                       value="{{$Lead[0]->firstname}}"
                                                       class="form-control" placeholder="First Name" required/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="middleName">Middle Name</label>
                                                <input type="text" name="middleName" id="middleName"
                                                       value="{{$Lead[0]->middlename}}"
                                                       class="form-control" placeholder="Middle Name"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="lastName"><strong>Last Name</strong></label>
                                                <input type="text" name="lastName" id="lastName"
                                                       value="{{$Lead[0]->lastname}}"
                                                       class="form-control" placeholder="Last Name" required/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="language">Language</label>
                                                <select name="language" id="language" class="form-control">
                                                    <option value="">Select Language</option>
                                                    <option value="english" <?php if ($Lead[0]->language == "english") {
                                                        echo "selected";
                                                    } ?> >English
                                                    </option>
                                                    <option value="spanish" <?php if ($Lead[0]->language == "spanish") {
                                                        echo "selected";
                                                    } ?> >Spanish
                                                    </option>
                                                    <option value="bilingual" <?php if ($Lead[0]->language == "bilingual") {
                                                        echo "selected";
                                                    } ?> >Bilingual
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="martial_status">Marital Status</label>
                                                <select name="martial_status" id="martial_status" class="form-control"
                                                        onchange="checkMaritalStatus();">
                                                    <option value="">Marital Status</option>
                                                    <option value="married" <?php if ($Lead[0]->martial_status == "married") {
                                                        echo "selected";
                                                    } ?> >Married
                                                    </option>
                                                    <option value="single" <?php if ($Lead[0]->martial_status == "single") {
                                                        echo "selected";
                                                    } ?> >Single
                                                    </option>
                                                    <option value="unknown" <?php if ($Lead[0]->martial_status == "unknown") {
                                                        echo "selected";
                                                    } ?> >Unknown
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2" id="_SpouceBlock" style="display:none;">
                                                <label class="w-100" for="spouce">Spouse</label>
                                                <input type="text" name="spouce" id="spouce"
                                                       value="{{$Lead[0]->spouce}}"
                                                       class="form-control" placeholder="Spouse Name"/>
                                            </div>
                                            <?php $PhoneCounter = 1; ?>
                                            <div class="col-md-3 mb-2" id="sellerPhone1">
                                                <label class="w-100" for="phone" class="w-100"><strong>Phone Number 1</strong><i
                                                            class="fa fa-plus-circle float-right"
                                                            style="cursor: pointer;"
                                                            onclick="AddPhoneField();"></i></label>
                                                <input type="number" step="any" name="phone" id="phone" class="form-control"
                                                       value="{{$Lead[0]->phone}}"
                                                       placeholder="Enter Your Phone Number" maxlength="20" required/>
                                            </div>
                                            @if($Lead[0]->phone2 != "")
                                                <?php $PhoneCounter++; ?>
                                                <div class="col-md-3 mb-2" id="sellerPhone2">
                                                    <label for="phone2" class="w-100">Phone Number 2
                                                        <span><i class="fa fa-trash deletePhoneField float-right"
                                                                 id="deletePhoneField_{{$PhoneCounter}}"
                                                                 style="cursor: pointer;"></i></span>
                                                    </label>
                                                    <input type="number" step="any" name="phone2" id="phone2" class="form-control"
                                                           value="{{$Lead[0]->phone2}}"
                                                           placeholder="Enter Your Phone Number" maxlength="20"/>
                                                </div>
                                            @endif
                                            @if($Lead[0]->phone3 != "")
                                                <?php $PhoneCounter++; ?>
                                                <div class="col-md-3 mb-2" id="sellerPhone3">
                                                    <label for="phone3" class="w-100">Phone Number 3
                                                        <span><i class="fa fa-trash deletePhoneField float-right"
                                                                 id="deletePhoneField_{{$PhoneCounter}}"
                                                                 style="cursor: pointer;"></i></span>
                                                    </label>
                                                    <input type="number" step="any" name="phone3" id="phone3" class="form-control"
                                                           value="{{$Lead[0]->phone3}}"
                                                           placeholder="Enter Your Phone Number" maxlength="20"/>
                                                </div>
                                            @endif
                                            @if($Lead[0]->phone4 != "")
                                                <?php $PhoneCounter++; ?>
                                                <div class="col-md-3 mb-2" id="sellerPhone4">
                                                    <label for="phone4" class="w-100">Phone Number 4
                                                        <span><i class="fa fa-trash deletePhoneField float-right"
                                                                 id="deletePhoneField_{{$PhoneCounter}}"
                                                                 style="cursor: pointer;"></i></span>
                                                    </label>
                                                    <input type="number" step="any" name="phone4" id="phone4" class="form-control"
                                                           value="{{$Lead[0]->phone4}}"
                                                           placeholder="Enter Your Phone Number" maxlength="20"/>
                                                </div>
                                            @endif
                                            @if($Lead[0]->phone5 != "")
                                                <?php $PhoneCounter++; ?>
                                                <div class="col-md-3 mb-2" id="sellerPhone5">
                                                    <label for="phone5" class="w-100">Phone Number 5
                                                        <span><i class="fa fa-trash deletePhoneField float-right"
                                                                 id="deletePhoneField_{{$PhoneCounter}}"
                                                                 style="cursor: pointer;"></i></span>
                                                    </label>
                                                    <input type="number" step="any" name="phone5" id="phone5" class="form-control"
                                                           value="{{$Lead[0]->phone5}}"
                                                           placeholder="Enter Your Phone Number" maxlength="20"/>
                                                </div>
                                            @endif
                                            <input type="hidden" name="phoneCountHidden" id="phoneCountHidden"
                                                   value="{{$PhoneCounter}}"/>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="email">Email</label>
                                                <input type="email" name="email" id="email" value="{{$Lead[0]->email}}"
                                                       class="form-control" placeholder="Email"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="ownersOccupy">Owner's Occupy</label>
                                                <select name="ownersOccupy" id="ownersOccupy" class="form-control">
                                                    <option value="">Owner's Occupy</option>
                                                    <option value="yes" <?php if ($Lead[0]->owner_occupy == "yes") {
                                                        echo "selected";
                                                    } ?> >Yes
                                                    </option>
                                                    <option value="no" <?php if ($Lead[0]->owner_occupy == "no") {
                                                        echo "selected";
                                                    } ?> >No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="occupancyStatus">Occupancy Status</label>
                                                <select name="occupancyStatus" id="occupancyStatus"
                                                        class="form-control">
                                                    <option value="">Occupancy Status</option>
                                                    <option value="vacant" <?php if ($Lead[0]->occupancy_status == "vacant") {
                                                        echo "selected";
                                                    } ?> >Vacant
                                                    </option>
                                                    <option value="occupied" <?php if ($Lead[0]->occupancy_status == "occupied") {
                                                        echo "selected";
                                                    } ?> >Occupied
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="collapsible mt-1">Property Information</button>
                                    <div class="content">
                                        <div class="row pt-2 pb-2" id="leadPropertyInformation">
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="street"><strong>Street</strong></label>
                                                <input type="text" name="street" id="street"
                                                       value="{{$Lead[0]->street}}"
                                                       class="form-control" placeholder="Street" required/>
                                            </div>
                                            @if($Lead[0]->city != "")
                                            <div class="col-md-3 mb-2">
                                                <label for="city">City</label>
                                                <select name="city" id="city" class="form-control">
                                                    <option value="" selected disabled="disabled">Select City</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->city}}" <?php if ($Lead[0]->city == $city->city) {
                                                            echo "selected";
                                                        } ?> >{{$city->city}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @else
                                            <div class="col-md-3 mb-2" id="AddLeadCitySection" style="display:none;">
                                                <label for="city">City</label>
                                                <select name="city" id="city" class="form-control">
                                                    <option value="" selected>Select City</option>
                                                </select>
                                            </div>
                                            @endif
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="state">State</label>
                                                <select class="form-control" name="state" id="state" onchange="LoadStateCountyCity();">
                                                    <option value="" selected>Select State</option>
                                                    @foreach($states as $state)
                                                        <option value="{{$state->name}}" <?php if ($Lead[0]->state == $state->name) {
                                                            echo "selected";
                                                        } ?> >{{$state->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="zipcode"><strong>Zip Code</strong></label>
                                                <input type="number" step="any" name="zipcode" id="zipcode"
                                                       class="form-control"
                                                       placeholder="Zip Code"
                                                       onkeypress="limitKeypress(event,this.value,5)"
                                                       onblur="limitZipCodeCheck();" value="{{$Lead[0]->zipcode}}"
                                                       required/>
                                            </div>
                                            <div class="col-md-3 mb-2" id="propertyClassificationDiv">
                                                <label class="w-100" for="propertyClassification">Property Classification</label>
                                                <select name="propertyClassification" id="propertyClassification"
                                                        class="form-control" onchange="AddPropertyType(this);CheckPropertyClassification(this);">
                                                    <option value="">Select</option>
                                                    <option value="residential" <?php if ($Lead[0]->property_classification == "residential") {
                                                        echo "selected";
                                                    } ?> >Residential
                                                    </option>
                                                    <option value="commercial" <?php if ($Lead[0]->property_classification == "commercial") {
                                                        echo "selected";
                                                    } ?> >Commercial
                                                    </option>
                                                    <option value="industrial" <?php if ($Lead[0]->property_classification == "industrial") {
                                                        echo "selected";
                                                    } ?> >Industrial
                                                    </option>
                                                    <option value="agricultural" <?php if ($Lead[0]->property_classification == "agricultural") {
                                                        echo "selected";
                                                    } ?> >Agricultural
                                                    </option>
                                                    <option value="vacant" <?php if ($Lead[0]->property_classification == "vacant") {
                                                        echo "selected";
                                                    } ?> >Vacant Lot
                                                    </option>
                                                </select>
                                            </div>
                                            @if ($Lead[0]->property_classification == "residential")
                                            <div class="col-md-3 mb-2" id="propertyTypeDiv">
                                                <label for="propertyType">Property Type</label>
                                                <select name="propertyType" id="propertyType" class="form-control" onchange="AddMultiFamily(this)" required>
                                                <option value="">Select</option>
                                                <option value="singleFamily" <?php if ($Lead[0]->property_type == "singleFamily") {
                                                  echo "selected";
                                                } ?> >Single Family</option>
                                                <option value="condominium" <?php if ($Lead[0]->property_type == "condominium") {
                                                  echo "selected";
                                                } ?> >Condominium</option>
                                                <option value="townhouse" <?php if ($Lead[0]->property_type == "townhouse") {
                                                  echo "selected";
                                                } ?> >Townhouse</option>
                                                <option value="multiFamily" <?php if ($Lead[0]->property_type == "multiFamily") {
                                                  echo "selected";
                                                } ?> >Multi family</option>
                                                <option value="mobile" <?php if ($Lead[0]->property_type == "mobile") {
                                                  echo "selected";
                                                } ?> >Mobile</option>
                                                <option value="manifactureHome" <?php if ($Lead[0]->property_type == "manifactureHome") {
                                                  echo "selected";
                                                } ?> >Manifacture Home</option>
                                                </select>
                                            </div>
                                            @endif
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="constructionType">Construction Type</label>
                                                <select name="constructionType" id="constructionType"
                                                        class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="wood" <?php if ($Lead[0]->construction_type == "wood") {
                                                        echo "selected";
                                                    } ?> >Wood
                                                    </option>
                                                    <option value="block" <?php if ($Lead[0]->construction_type == "block") {
                                                        echo "selected";
                                                    } ?> >Block
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="yearBuilt">Year Built</label>
                                                <input type="text" name="yearBuilt" id="yearBuilt"
                                                       value="{{$Lead[0]->year_built}}"
                                                       class="form-control" placeholder="Year Built"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="buildingSize">Livable Area</label>
                                                <input type="number" step="any" name="buildingSize" id="buildingSize1"
                                                       value="{{$Lead[0]->year_built}}"
                                                       class="form-control" placeholder="Livable Area (sqr ft)"
                                                       onchange="CalculateRehabCost1();" />
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="bedroom">Bedroom</label>
                                                <input type="number" step="any" name="bedroom" id="bedroom"
                                                       value="{{$Lead[0]->bedroom}}"
                                                       class="form-control" placeholder="Bedroom"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="bathroom">Bathroom</label>
                                                <input type="number" step="any" name="bathroom" id="bathroom"
                                                       value="{{$Lead[0]->bathroom}}"
                                                       class="form-control" placeholder="Bathroom"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="lotSize">Lot Square Footage</label>
                                                <input type="number" step="any" name="lotSize" id="lotSize"
                                                       value="{{$Lead[0]->lot_size}}"
                                                       class="form-control" placeholder="Lot Square Footage"/>
                                            </div>
                                            <?php
                                            $lead_features = explode(",", $Lead[0]->home_feature);
                                            ?>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="homeFeature">Home Feature</label>
                                                <select name="homeFeature[]" id="homeFeature" class="form-control"
                                                        multiple>
                                                    <option value="pool" <?php if (in_array("pool", $lead_features)) {
                                                        echo "selected";
                                                    } ?> >Pool
                                                    </option>
                                                    <option value="garage" <?php if (in_array("garage", $lead_features)) {
                                                        echo "selected";
                                                    } ?> >Garage
                                                    </option>
                                                    <option value="basement" <?php if (in_array("basement", $lead_features)) {
                                                        echo "selected";
                                                    } ?> >Basement
                                                    </option>
                                                    <option value="attic" <?php if (in_array("attic", $lead_features)) {
                                                        echo "selected";
                                                    } ?> >Attic
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="storiesNo">Number of Stories</label>
                                                <input type="number" step="any" name="storiesNo" id="storiesNo"
                                                       value="{{$Lead[0]->num_of_stories}}"
                                                       class="form-control" placeholder="Number of Stories"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="associationFee">Association Fee</label>
                                                <select name="associationFee" id="associationFee" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="HOA" <?php if ($Lead[0]->association_fee == "HOA") {
                                                        echo "selected";
                                                    } ?> >HOA
                                                    </option>
                                                    <option value="COA" <?php if ($Lead[0]->association_fee == "COA") {
                                                        echo "selected";
                                                    } ?> >COA
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="reasonsToSales">Reasons to Sales</label>
                                                <select name="reasonsToSales" id="reasonsToSales" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="relocations" <?php if ($Lead[0]->reason_to_sale == "relocations") {
                                                        echo "selected";
                                                    } ?> >Relocations
                                                    </option>
                                                    <option value="tired_landlord" <?php if ($Lead[0]->reason_to_sale == "tired_landlord") {
                                                        echo "selected";
                                                    } ?> >Tired landlord
                                                    </option>
                                                    <option value="payment_issue" <?php if ($Lead[0]->reason_to_sale == "payment_issue") {
                                                        echo "selected";
                                                    } ?> >Payment Issue
                                                    </option>
                                                    <option value="family_transfer" <?php if ($Lead[0]->reason_to_sale == "family_transfer") {
                                                        echo "selected";
                                                    } ?> >Family Transfer
                                                    </option>
                                                    <option value="divorced" <?php if ($Lead[0]->reason_to_sale == "divorced") {
                                                        echo "selected";
                                                    } ?> >Divorced
                                                    </option>
                                                    <option value="others" <?php if ($Lead[0]->reason_to_sale == "others") {
                                                        echo "selected";
                                                    } ?> >Others
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="conditions">Condition</label>
                                                <select name="conditions" id="conditions1" class="form-control"
                                                        onchange="CalculateRehabCost1();">
                                                    <option value="">Select</option>
                                                    <option value="basic" <?php if ($Lead[0]->lead_condition == "basic") {
                                                        echo "selected";
                                                    } ?> >Basic
                                                    </option>
                                                    <option value="light" <?php if ($Lead[0]->lead_condition == "light") {
                                                        echo "selected";
                                                    } ?> >Light
                                                    </option>
                                                    <option value="moderate" <?php if ($Lead[0]->lead_condition == "moderate") {
                                                        echo "selected";
                                                    } ?> >Moderate
                                                    </option>
                                                    <option value="full" <?php if ($Lead[0]->lead_condition == "full") {
                                                        echo "selected";
                                                    } ?> >Full
                                                    </option>
                                                    <option value="heavy" <?php if ($Lead[0]->lead_condition == "heavy") {
                                                        echo "selected";
                                                    } ?> >Heavy
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="leadSources">Lead Source</label>
                                                <select name="leadSources" id="leadSources" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="basic" <?php if ($Lead[0]->lead_source == "basic") {
                                                        echo "selected";
                                                    } ?> >D4D
                                                    </option>
                                                    <option value="propStream" <?php if ($Lead[0]->lead_source == "propStream") {
                                                        echo "selected";
                                                    } ?> >PropStream
                                                    </option>
                                                    <option value="calling" <?php if ($Lead[0]->lead_source == "calling") {
                                                        echo "selected";
                                                    } ?> >Calling
                                                    </option>
                                                    <option value="text" <?php if ($Lead[0]->lead_source == "text") {
                                                        echo "selected";
                                                    } ?> >Text
                                                    </option>
                                                    <option value="facebook" <?php if ($Lead[0]->lead_source == "facebook") {
                                                        echo "selected";
                                                    } ?> >Facebook
                                                    </option>
                                                    <option value="instagram" <?php if ($Lead[0]->lead_source == "instagram") {
                                                        echo "selected";
                                                    } ?> >Instagram
                                                    </option>
                                                    <option value="website" <?php if ($Lead[0]->lead_source == "website") {
                                                        echo "selected";
                                                    } ?> >Website
                                                    </option>
                                                    <option value="zillow" <?php if ($Lead[0]->lead_source == "zillow") {
                                                        echo "selected";
                                                    } ?> >Zillow
                                                    </option>
                                                    <option value="wholesaler" <?php if ($Lead[0]->lead_source == "wholesaler") {
                                                        echo "selected";
                                                    } ?> >Wholesaler
                                                    </option>
                                                    <option value="realtor" <?php if ($Lead[0]->lead_source == "realtor") {
                                                        echo "selected";
                                                    } ?> >Realtor
                                                    </option>
                                                    <option value="investor" <?php if ($Lead[0]->lead_source == "investor") {
                                                        echo "selected";
                                                    } ?> >Investor
                                                    </option>
                                                    <option value="radio" <?php if ($Lead[0]->lead_source == "radio") {
                                                        echo "selected";
                                                    } ?> >Radio
                                                    </option>
                                                    <option value="jv_partner" <?php if ($Lead[0]->lead_source == "jv_partner") {
                                                        echo "selected";
                                                    } ?> >JV Partner
                                                    </option>
                                                    <option value="banded_sign" <?php if ($Lead[0]->lead_source == "banded_sign") {
                                                        echo "selected";
                                                    } ?> >Banded Sign
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="askingPrice">Asking Price</label>
                                                <input type="number" step="any" name="askingPrice" id="askingPrice1"
                                                       value="{{$Lead[0]->asking_price}}" class="form-control"
                                                       placeholder="Asking Price"
                                                       onchange="PerformLeadCalculations1();"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="arv">ARV</label>
                                                <input type="number" step="any" name="arv" id="arv1"
                                                       value="{{$Lead[0]->arv}}"
                                                       class="form-control"
                                                       placeholder="After Repair Value"
                                                       onchange="PerformLeadCalculations1();"/>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="assignment_fee">Assignment</label>
                                                <input type="number" step="any" name="assignment_fee"
                                                       id="assignment_fee1"
                                                       value="{{$Lead[0]->assignment_fee}}" class="form-control"
                                                       placeholder="Assignment Fee"
                                                       onchange="PerformLeadCalculations1();"/>
                                            </div>
                                            <?php
                                            $DataSource = explode(",", $Lead[0]->data_source);
                                            ?>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="data_source">Data Source</label>
                                                <select name="data_source[]" id="data_source" class="form-control"
                                                        multiple>
                                                    <option value="">Select</option>
                                                    <option value="On Market" <?php if (in_array("On Market", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>On Market
                                                    </option>
                                                    <option value="Vacant" <?php if (in_array("Vacant", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Vacant
                                                    </option>
                                                    <option value="Liens" <?php if (in_array("Liens", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Liens
                                                    </option>
                                                    <option value="Pre-Foreclosures" <?php if (in_array("Pre-Foreclosures", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Pre-Foreclosures
                                                    </option>
                                                    <option value="Auctions" <?php if (in_array("Auctions", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Auctions
                                                    </option>
                                                    <option value="Bank Owned" <?php if (in_array("Bank Owned", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Bank Owned
                                                    </option>
                                                    <option value="Cash Buyers" <?php if (in_array("Cash Buyers", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Cash Buyers
                                                    </option>
                                                    <option value="High Equity" <?php if (in_array("High Equity", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>High Equity
                                                    </option>
                                                    <option value="Free & Clear" <?php if (in_array("Free & Clear", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Free & Clear
                                                    </option>
                                                    <option value="Bankruptcy" <?php if (in_array("Bankruptcy", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Bankruptcy
                                                    </option>
                                                    <option value="Divorce" <?php if (in_array("Divorce", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Divorce
                                                    </option>
                                                    <option value="Tax Delinquencies" <?php if (in_array("Tax Delinquencies", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Tax Delinquencies
                                                    </option>
                                                    <option value="Flippers" <?php if (in_array("Flippers", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Flippers
                                                    </option>
                                                    <option value="Failed Listings" <?php if (in_array("Failed Listings", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Failed Listings
                                                    </option>
                                                    <option value="Senior Owners" <?php if (in_array("Senior Owners", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Senior Owners
                                                    </option>
                                                    <option value="Vacant Land" <?php if (in_array("Vacant Land", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Vacant Land
                                                    </option>
                                                    <option value="Tired Landlords" <?php if (in_array("Tired Landlords", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Tired Landlords
                                                    </option>
                                                    <option value="Pre-Probate (Deceased Owner)" <?php if (in_array("Pre-Probate (Deceased Owner)", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Pre-Probate (Deceased Owner)
                                                    </option>
                                                    <option value="Others" <?php if (in_array("Others", $DataSource)) {
                                                        echo "selected";
                                                    } ?>>Others
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="w-100" for="pictureLink">Picture</label>
                                                <input type="text" name="pictureLink" id="pictureLink"
                                                       value="{{$Lead[0]->picture}}"
                                                       class="form-control" placeholder="Link"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3 text-center">
                                    <input type="submit" class="btn btn-primary SaveButtonSetting" value="Save" id="submitBtn" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--General Details--}}
                <div class="col-md-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <form>
                                <input type="hidden" name="id" id="id" value="{{$Lead[0]->id}}"/>
                                <div class="row">
                                    <div class="col-md-12 mb-3 mt-3">
                                        <label class="w-100" for="note">Comments</label>
                                        <textarea class="form-control" id="history_note" name="history_note"
                                                  rows="3"></textarea>
                                    </div>
                                    <div class="ml-3" style="color:green; font-size: 12px;display:none;"
                                         id="history_note_msg"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right mt-3">
                                        <button type="button" class="btn btn-primary"
                                                onclick="EditLeadSaveHistoryNote();">
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                Lead Comments
                            </h6>
                            <div class="table-responsive">
                                <table id="editlead_historynotes_table" class="table w-100">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th style="width: 12%;">User</th>
                                        <th style="width: 85%;">Comment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
            </div>
        </form>
    </div>
@endsection
@include('admin.includes.leadUpdateAppointmentTimeModal')
@include('admin.includes.leadEvaluationModal')
@include('admin.includes.assignLeadModal')
@include('admin.includes.leadUpdateStatusModal')

@push('scripts')
    <script>
        let coll = document.getElementsByClassName("collapsible");
        let i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active1");
                var content = this.nextElementSibling;
                if (content.style.maxHeight){
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            });
            coll[i].click();
        }
    </script>
@endpush
