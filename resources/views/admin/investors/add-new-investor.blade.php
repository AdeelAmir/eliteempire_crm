@extends('admin.layouts.app')
@section('content')
    <style media="screen">
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
    <div class="page-content" id="addNewInvestorPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Add Investor Form</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/buissness_accounts')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('global_manager/buissness_accounts')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 6)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('disposition_representative/buissness_accounts')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <form method="post" id="addInvestorForm" enctype="multipart/form-data">
            @csrf
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
                                    <div class="bs-wizard-info text-center">General information</div>
                                </div>

                                <div class="col-xs-12 bs-wizard-step step2 disabled"><!-- complete -->
                                    <div class="text-center bs-wizard-stepnum">Step 2</div>
                                    <div class="progress">
                                        <div class="progress-bar"></div>
                                    </div>
                                    <a href="#" class="bs-wizard-dot"></a>
                                    <div class="bs-wizard-info text-center">Buying Criteria</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {{--Step Bar--}}

            <section class="contact-area pb-5">
                <div class="container">
                    <div class="row">

                        {{--General Details--}}
                        <div class="col-md-12 grid-margin stretch-card" id="GeneralInformationBlock">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        General Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label class="w-100" for="firstname">First Name</label>
                                            <input type="text" name="firstname" id="firstname"
                                                   class="form-control"
                                                   placeholder="Enter Your First Name" />
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label class="w-100" for="middlename">Middle Name</label>
                                            <input type="text" name="middlename" id="middlename"
                                                   class="form-control"
                                                   placeholder="Enter Your Middle Name"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label class="w-100" for="lastname">Last Name</label>
                                            <input type="text" name="lastname" id="lastname"
                                                   class="form-control"
                                                   placeholder="Enter Your Last Name" />
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <div class="form-group">
                                                <label class="w-100" for="buisness_name"><strong>Buisness Name</strong></label>
                                                <input type="text" name="buisness_name" id="buisness_name"
                                                       class="form-control" placeholder="Enter Buisness Name" />
                                                <div style="margin-top: 7px;" id="b_name"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <div class="form-group">
                                                <label class="w-100" for="buisness_address">Buisness Address</label>
                                                <input type="text" name="buisness_address" id="buisness_address"
                                                       class="form-control" placeholder="Enter Buisness Address" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="phone" class="w-100"><strong>Phone Number 1</strong><i
                                                        class="fa fa-plus-circle float-right" style="cursor: pointer;"
                                                        onclick="ShowPhone2();"></i></label>
                                            <input type="number" step="any" name="phone" id="phone"
                                                   class="form-control"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20" />
                                            <div style="margin-top: 7px;" id="p_phone1"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="phone2Field" style="display: none;">
                                            <label for="phone2" class="w-100">Phone Number 2<i
                                                        class="fa fa-times-circle float-right" style="cursor: pointer;"
                                                        onclick="HidePhone2();"></i></label>
                                            <input type="number" step="any" name="phone2" id="phone2"
                                                   class="form-control"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20" />
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <div class="form-group">
                                                <label for="email" class="w-100"><strong>Email Address</strong><i
                                                            class="fa fa-plus-circle float-right"
                                                            style="cursor: pointer;"
                                                            onclick="ShowSecondaryEmail();"></i></label>
                                                <input type="email" name="email" id="email" class="form-control"
                                                       placeholder="Enter Your Email Address" />
                                                <div style="margin-top: 7px;" id="e_email1"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="SecondaryEmailField" style="display: none;">
                                            <div class="form-group">
                                                <label for="email" class="w-100">Secondary Email Address<i
                                                            class="fa fa-times-circle float-right"
                                                            style="cursor: pointer;"
                                                            onclick="HideSecondaryEmail();"></i></label>
                                                <input type="email" name="secondary_email" id="secondary_email"
                                                       class="form-control"
                                                       placeholder="Enter Your Email Address"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <button type="button" class="btn btn-primary w-10"
                                                    name="nextAddInvestorForm"
                                                    id="nextAddInvestorForm"
                                                    onclick="displayBuyingCriteriaSection();">
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Buying Criteria Details--}}
                        <div class="col-md-12 grid-margin stretch-card" id="BuyingCriteriaBlock" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        Buying Criteria
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12" id="ServingLocationBlock">

                                        </div>
                                        <div class="col-md-12 mb-3 mt-3">
                                            <span data-repeater-create="" class="btn btn-outline-success btn-sm float-right" onclick="MakeServingLocation();">
                                                <span class="fa fa-plus"></span>&nbsp;Add Serving Location
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <button type="button"
                                                    class="btn btn-primary w-10 float-left"
                                                    name="backAddInvestorForm"
                                                    id="backAddInvestorForm"
                                                    onclick="displayBackGeneralInformationSection();">
                                                Back
                                            </button>
                                            <input type="submit"
                                                   class="btn btn-primary float-right w-10"
                                                   name="submitAddInvestorForm"
                                                   id="submitAddInvestorForm" value="Add"/>
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

    <script type="text/javascript">
        let states = JSON.parse('<?= $_states; ?>');
    </script>
@endsection
