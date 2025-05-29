@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="editInvestorPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Edit Investor Form</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{url('admin/buissness_accounts')}}';">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </button>
                @elseif($Role == 2)
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{url('admin/buissness_accounts')}}';">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </button>
                @endif
            </div>
        </div>

        <form method="post" id="editInvestorForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="_id" value="{{$user_id}}" />
            <input type="hidden" name="old_email" id="old_email" value="{{$u_details[0]->email}}" />
            <div class="row">
                <div class="col-12 mb-1">
                    @if(session()->has('message'))
                      <div class="alert alert-success">
                          {{ session('message') }}
                      </div>
                    @elseif(session()->has('error'))
                      <div class="alert alert-danger">
                          {{ session('error') }}
                      </div>
                    @endif
                    <div class="alert alert-danger" id="_dangerAlert" style="display:none;">
                        Error! Email address, company name or last name is missing.
                    </div>
                </div>
            </div>

            <section class="contact-area pb-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card" id="InvestorGeneralInformationBlock">
                          <div class="card">
                            <div class="card-body">
                              <h6 class="card-title">
                                  Investor Information
                              </h6>
                              <div class="row">
                                  <div class="col-md-3 mt-3">
                                      <label class="w-100" for="firstname"><strong>First Name</strong></label>
                                      <input type="text" name="firstname" id="firstname"
                                             class="form-control" value="{{$u_details[0]->firstname}}"
                                             placeholder="Enter Your First Name" />
                                  </div>
                                  <div class="col-md-3 mt-3">
                                      <label class="w-100" for="middlename">Middle Name</label>
                                      <input type="text" name="middlename" id="middlename"
                                             class="form-control" value="{{$u_details[0]->middlename}}"
                                             placeholder="Enter Your Middle Name" />
                                  </div>
                                  <div class="col-md-3 mt-3">
                                      <label class="w-100" for="lastname"><strong>Last Name</strong></label>
                                      <input type="text" name="lastname" id="lastname"
                                             class="form-control" value="{{$u_details[0]->lastname}}"
                                             placeholder="Enter Your Last Name" />
                                  </div>
                                  <div class="col-md-3 mt-3">
                                    <div class="form-group">
                                      <label class="w-100" for="buisness_name"><strong>Buisness Name</strong></label>
                                      <input type="text" name="buisness_name" id="buisness_name" value="{{$u_details[0]->buisnesss_name}}"
                                             class="form-control" placeholder="Enter Buisness Name" />
                                    </div>
                                  </div>
                                  <div class="col-md-3 mt-3">
                                      <label class="w-100" for="phone"><strong>Phone Number 1</strong></label>
                                      <input type="number" step="any" name="phone" id="phone"
                                             class="form-control" value="{{$u_details[0]->phone}}"
                                             placeholder="Enter Your Phone Number"
                                             maxlength="20" />
                                  </div>
                                  <div class="col-md-3 mt-3" id="phone2Field">
                                      <label class="w-100" for="phone2">Phone Number 2</label>
                                      <input type="number" step="any" name="phone2" id="phone2"
                                             class="form-control" value="{{$u_details[0]->phone2}}"
                                             placeholder="Enter Your Phone Number"
                                             maxlength="20" />
                                  </div>
                                  <div class="col-md-3 mt-3">
                                      <div class="form-group">
                                        <label class="w-100" for="email"><strong>Email Address</strong></label>
                                        <input type="email" name="email" id="email"
                                        class="form-control" value="{{$u_details[0]->email}}"
                                        placeholder="Enter Your Email Address" />
                                      </div>
                                  </div>
                                  <div class="col-md-3 mt-3">
                                      <div class="form-group">
                                        <label class="w-100" for="email">Secondary Email Address</label>
                                        <input type="email" name="secondary_email" id="secondary_email"
                                          class="form-control" value="{{$u_details[0]->secondary_email}}"
                                          placeholder="Enter Your Email Address" />
                                      </div>
                                  </div>
                              </div>
                              <?php
                                $ServingLocationCounter = 0;
                              ?>
                              <div class="row">
                                <div class="col-md-12" id="ServingLocationBlock">
                                  @foreach($serving_locations as $s_loc)
                                    <?php
                                      $ServingLocationCounter++;
                                      $_PropertyClassification = explode(",", $s_loc->property_classification);
                                      $_PropertyType = explode(",", $s_loc->property_type);
                                      $_MultiFamily = explode(",", $s_loc->multi_family);
                                      $_ConstructionType = explode(",", $s_loc->construction_type);
                                      $_County = explode(",", $s_loc->county);
                                      $_City = explode(",", $s_loc->city);

                                      // Counties list
                                      $counties = \Illuminate\Support\Facades\DB::table('locations')
                                          ->where('state_name', '=', $s_loc->state)
                                          ->orderBy("county_name", "ASC")
                                          ->get()
                                          ->unique("county_name");

                                      // Cities list
                                      $cities = \Illuminate\Support\Facades\DB::table('locations')
                                          ->where('state_name', '=', $s_loc->state)
                                          ->orderBy("city", "ASC")
                                          ->get()
                                          ->unique("city");
                                    ?>
                                    <div class="card mt-3" id="servinglocation_{{$ServingLocationCounter}}">
                                      <div class="card-body">
                                        <h6 class="card-title">
                                            Serving Location
                                        </h6>
                                        <div class="row">
                                          <div class="col-md-3 mb-2 mt-3">
                                              <label class="w-100" for="propertyClassification_{{$ServingLocationCounter}}">Property Classification</label>
                                              <select name="propertyClassification[]" id="propertyClassification_{{$ServingLocationCounter}}" class="form-control propertyClassification" multiple>
                                                  <option value="">Select</option>
                                                  <option value="residential" <?php if(in_array("residential", $_PropertyClassification)){echo "selected";} ?> >Residential</option>
                                                  <option value="commercial" <?php if(in_array("commercial", $_PropertyClassification)){echo "selected";} ?> >Commercial</option>
                                                  <option value="industrial" <?php if(in_array("industrial", $_PropertyClassification)){echo "selected";} ?> >Industrial</option>
                                                  <option value="agricultural" <?php if(in_array("agricultural", $_PropertyClassification)){echo "selected";} ?> >Agricultural</option>
                                                  <option value="vacant" <?php if(in_array("vacant", $_PropertyClassification)){echo "selected";} ?> >Vacant Lot</option>
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-2 mt-3">
                                              <label class="w-100" for="propertyType_{{$ServingLocationCounter}}">Property Type</label>
                                              <select name="propertyType[]" id="propertyType_{{$ServingLocationCounter}}" class="form-control propertyType" multiple>
                                                  <option value="">Select Property Type</option>
                                                  <option value="singleFamily" <?php if(in_array("singleFamily", $_PropertyType)){echo "selected";} ?> >Single Family</option>
                                                  <option value="condominium" <?php if(in_array("condominium", $_PropertyType)){echo "selected";} ?> >Condominium</option>
                                                  <option value="townhouse" <?php if(in_array("townhouse", $_PropertyType)){echo "selected";} ?> >Townhouse</option>
                                                  <option value="multiFamily" <?php if(in_array("multiFamily", $_PropertyType)){echo "selected";} ?> >Multi family</option>
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-2 mt-3">
                                              <label class="w-100" for="multiFamilyType_{{$ServingLocationCounter}}">Multi-Family</label>
                                              <select name="multiFamilyType[]" id="multiFamilyType_{{$ServingLocationCounter}}" class="form-control multiFamilyType"  multiple>
                                                <option value="">Select</option>
                                                <option value="3_4_unit_or_5_plus" <?php if(in_array("3_4_unit_or_5_plus", $_MultiFamily)){echo "selected";} ?> >3-4 Unit or 5 plus</option>
                                                <option value="duplexes" <?php if(in_array("duplexes", $_MultiFamily)){echo "selected";} ?> >Duplexes</option>
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-2 mt-3">
                                              <label class="w-100" for="constructionType_{{$ServingLocationCounter}}">Construction Type</label>
                                              <select name="constructionType[]" id="constructionType_{{$ServingLocationCounter}}" class="form-control constructionType" multiple>
                                                  <option value="">Select</option>
                                                  <option value="wood" <?php if(in_array("wood", $_ConstructionType)){echo "selected";} ?> >Wood</option>
                                                  <option value="block" <?php if(in_array("block", $_ConstructionType)){echo "selected";} ?> >Block</option>
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-3 mt-3">
                                              <label class="w-100" for="state_{{$ServingLocationCounter}}">State</label>
                                              <select class="form-control states" name="serving_state[]" id="state_{{$ServingLocationCounter}}" class="form-control" onchange="LoadServingLocationStateCountyCity(this.id);">
                                                  <option value="">Select State</option>
                                                  @foreach($states as $state)
                                                    <option value="{{$state->name}}" <?php if($s_loc->state == $state->name){echo "selected";} ?> >{{$state->name}}</option>;
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-3 mt-3">
                                              <label for="city_{{$ServingLocationCounter}}">City</label>
                                              <select name="serving_city[]"
                                                      id="city_{{$ServingLocationCounter}}"
                                                      class="form-control cities" multiple>
                                                  <option value="">Select City</option>
                                                  @foreach($cities as $city)
                                                      <option value="{{$city->city}}" <?php if (in_array($city->city, $_City)) {
                                                          echo "selected";
                                                      } ?> >{{$city->city}}</option>;
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-3 mt-3">
                                              <label for="county_{{$ServingLocationCounter}}">County</label>
                                              <select class="form-control counties"
                                                      name="serving_county[]"
                                                      id="county_{{$ServingLocationCounter}}"
                                                      multiple>
                                                  <option value="">Select County</option>
                                                  @foreach($counties as $county)
                                                      <option value="{{$county->county_name}}" <?php if (in_array($county->county_name, $_County)) {
                                                          echo "selected";
                                                      } ?> >{{$county->county_name}}</option>;
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="col-md-3 mb-3 mt-3">
                                              <label class="w-100" for="zipcode_{{$ServingLocationCounter}}">Zip code</label>
                                              <input type="text" name="serving_zipcode[]" value="{{$s_loc->zipcode}}" id="zipcode_{{$ServingLocationCounter}}" class="form-control" placeholder="Enter Your Zip Code"/>
                                          </div>
                                          <div class="col-md-12 mb-3 mt-3">
                                            <span data-repeater-create="" class="btn btn-outline-danger btn-sm float-right" id="remove_{{$ServingLocationCounter}}" onclick="RemoveServingLocation(this.id);">
                                              <span class="fa fa-trash"></span>&nbsp;Delete</span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  @endforeach
                                  <input type="hidden" name="_servingLocationCounter" id="_servingLocationCounter" value="{{$ServingLocationCounter}}" />
                                </div>
                                <div class="col-md-12 mb-3 mt-3">
                                  <span data-repeater-create="" class="btn btn-outline-success btn-sm float-right" onclick="MakeEditServingLocation();">
                                    <span class="fa fa-plus"></span>&nbsp;Add Serving Location</span>
                                </div>
                              </div>

                              <div class="row">
                                  <div class="col-md-12 text-right mt-3">
                                    <input type="submit"
                                           class="btn btn-primary float-right w-10"
                                           name="submitUpdateInvestorForm"
                                           id="submitUpdateInvestorForm" value="Update"/>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script type="text/javascript">
      let states = JSON.parse('<?= $_states; ?>');
    </script>
@endsection
