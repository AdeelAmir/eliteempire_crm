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
            width: 15%;
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

        .grid-margin {
            margin-bottom: 0;
        }
        /* End of step form progress bar */
    </style>

    <div class="page-content" id="addNewUser">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">User Registration Form</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/users')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('global_manager/users')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        {{--Step Bar--}}
        <section class="contact-area">
            <div class="container">
                <div class="row" style="margin:auto;">
                    <div class="col-lg-12">
                        <div class="row bs-wizard" style="border-bottom:0;">
                            <div class="col-md-3 col-sm-3 col-xs-3"></div>

                            <div class="col-xs-12 bs-wizard-step step1 complete">
                                <div class="text-center bs-wizard-stepnum">Step 1</div>
                                <div class="progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <a href="#" class="bs-wizard-dot"></a>
                                <div class="bs-wizard-info text-center">User information</div>
                            </div>

                            <div class="col-xs-12 bs-wizard-step step2 disabled"><!-- complete -->
                                <div class="text-center bs-wizard-stepnum">Step 2</div>
                                <div class="progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <a href="#" class="bs-wizard-dot"></a>
                                <div class="bs-wizard-info text-center">User Contact information</div>
                            </div>

                            <div class="col-xs-12 bs-wizard-step step3 disabled"><!-- complete -->
                                <div class="text-center bs-wizard-stepnum">Step 3</div>
                                <div class="progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <a href="#" class="bs-wizard-dot"></a>
                                <div class="bs-wizard-info text-center">User Identification</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{--Step Bar--}}

        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/user/store');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/user/store');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 3) {
            $Url = url('acquisition_manager/user/store');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 4) {
            $Url = url('disposition_manager/user/store');
        }
        ?>

        <form action="{{$Url}}" method="post" id="addUserForm" enctype="multipart/form-data">
        @csrf
        <!-- Start Contact Area -->
            <section class="contact-area pb-5">
                <div class="container">
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
                            <div id="success-alert" class="alert alert-success" style="display: none;"></div>
                            <div id="error-alert" class="alert alert-danger" style="display: none;"></div>
                        </div>

                        {{--User Information Details--}}
                        <div class="col-md-12 grid-margin stretch-card" id="UserInformationBlock">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        User Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="firstname w-100"><strong>First Name</strong></label>
                                            <input type="text" name="firstname" id="firstname"
                                                   class="form-control"
                                                   placeholder="Enter Your First Name" required/>
                                                   <div style="margin-top: 7px;" id="f_name"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="middlename w-100">Middle Name</label>
                                            <input type="text" name="middlename" id="middlename"
                                                   class="form-control"
                                                   placeholder="Enter Your Middle Name"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="lastname w-100"><strong>Last Name</strong></label>
                                            <input type="text" name="lastname" id="lastname"
                                                   class="form-control"
                                                   placeholder="Enter Your Last Name" required/>
                                                   <div style="margin-top: 7px;" id="l_name"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="dob w-100"><strong>Date of Birth</strong></label>
                                            <input type="text" name="dob" id="dob"
                                                   class="form-control" placeholder="MM/DD/YYYY"
                                                   required/>
                                                   <div style="margin-top: 7px;" id="user_dob"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-3">
                                            <button type="button" class="btn btn-primary w-10"
                                                    name="submitAddUserForm"
                                                    onclick="displayUserContactInformationSection();">
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--User Contact information Details--}}
                        <div class="col-md-12 grid-margin stretch-card"
                            id="UserContactInformationBlock" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        User Contact Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="email w-100"><strong>Email Address</strong></label>
                                            <input type="email" name="email" id="email"
                                                   class="form-control"
                                                   placeholder="Enter Your Email Address" required/>
                                                   <div style="margin-top: 7px;" id="user_email"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="phone" class="w-100"><strong>Phone Number 1</strong><i
                                                        class="fa fa-plus-circle float-right" style="cursor: pointer;"
                                                        onclick="ShowPhone2();"></i></label>
                                            <input type="number" step="any" name="phone" id="phone"
                                                   class="form-control"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20"
                                                   required/>
                                                   <div style="margin-top: 7px;" id="user_phone"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="phone2Field" style="display: none;">
                                            <label for="phone2" class="w-100">Phone Number 2<i
                                                        class="fa fa-times-circle float-right" style="cursor: pointer;"
                                                        onclick="HidePhone2();"></i></label>
                                            <input type="number" step="any" name="phone2" id="phone2"
                                                   class="form-control"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="city">Street</label>
                                            <input type="text" name="street" id="street"
                                                   class="form-control"
                                                   placeholder="Enter Your Street"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="city">City</label>
                                            <select name="city" id="city" class="form-control">
                                                <option value="" selected>Select City</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="state">State</label>
                                            <select class="state" name="state" id="state"
                                                    class="form-control" onchange="LoadStateCountyCity(); CheckForUserState('submitAddUserForm');" required>
                                                <option value="" selected>Select State</option>
                                                @foreach($states as $state)
                                                    @if(\Illuminate\Support\Facades\Session::get('user_role') == 1 || \Illuminate\Support\Facades\Session::get('user_role') == 2)
                                                        <option value="{{$state->name}}">{{$state->name}}</option>
                                                    @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 3 || \Illuminate\Support\Facades\Session::get('user_role') == 4)
                                                        @if($state->name == \App\Helpers\SiteHelper::GetCurrentUserState())
                                                            <option value="{{$state->name}}" selected>{{$state->name}}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{$state->name}}">{{$state->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        {{--<div class="col-md-3 mb-3 mt-3">
                                            <label for="county">County</label>
                                            <select name="county" id="county" class="form-control county" required>
                                                <option value="" selected>Select County</option>
                                            </select>
                                        </div>--}}
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="zipcode" class="w-100">Zip code/Postal Code</label>
                                            <input type="number" name="zipcode" id="zipcode"
                                                   class="form-control"
                                                   onkeypress="limitKeypress(event,this.value,5)"
                                                   onblur="limitZipCodeCheck();"
                                                   placeholder="Enter Your Zip Code"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <button type="button"
                                                    class="btn btn-primary w-10 float-left"
                                                    name="submitAddUserForm"
                                                    onclick="displayBackUserInformationSection();">
                                                Back
                                            </button>
                                            <button type="button"
                                                    class="btn btn-primary float-right w-10"
                                                    name="submitAddUserForm"
                                                    onclick="displayUserIdentificationSection();">
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--User Identification Details--}}
                        <div class="col-md-12 grid-margin stretch-card" 
                            id="UserIdentificationBlock" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        User Identification
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="repeater-custom-show-hide">
                                                <div data-repeater-list="documents">
                                                    <div data-repeater-item="" style="" class="mb-3">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-4 mb-3 mt-3">
                                                                                <label class="w-100">Document Name</label>
                                                                                <input type="text" name="documentname" class="form-control"
                                                                                       placeholder="Enter Document Name" autocomplete="off" />
                                                                            </div>
                                                                            <div class="col-md-4 mb-3 mt-3">
                                                                                <label class="w-100">Document Number</label>
                                                                                <input type="number" name="documentnumbers" class="form-control"
                                                                                       placeholder="Enter Document Numbers" autocomplete="off" />
                                                                            </div>
                                                                            <div class="col-md-4 mb-3 mt-3">
                                                                                <label class="add_document_label">Document 1</label>
                                                                                <input type="file" name="documentFile" class="form-control"
                                                                                       accept="image/jpeg, image/png, image/jpg, application/pdf" />
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <span data-repeater-delete=""
                                                                                      class="btn btn-outline-danger btn-sm float-right deletePayeeBtn">
                                                                                    <span class="far fa-trash-alt mr-1"></span>&nbsp;
                                                                                    Delete
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="col-sm-12">
                                                        <span data-repeater-create=""
                                                              class="btn btn-outline-success btn-sm float-right">
                                                            <span class="fa fa-plus"></span>&nbsp;
                                                            Add
                                                        </span>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if(\Illuminate\Support\Facades\Session::get('user_role') == 1)
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="role">Role</label>
                                                <select class="form-control" name="role" id="role" onchange="CheckForUserState('submitAddUserForm');" required>
                                                    <option value="">Select Role</option>
                                                    @foreach($roles as $role)
                                                        @if(\Illuminate\Support\Facades\Session::get('user_role') == 1)
                                                            @if($role->id != 1 && $role->id != 9 && $role->id != 10 && $role->id != 11)
                                                                <option value="{{$role->id}}">{{$role->title}}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 2)
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="role">Role</label>
                                                <select class="form-control" name="role" id="role" onchange="CheckForUserState('submitAddUserForm');" required>
                                                    <option value="">Select Role</option>
                                                    @foreach($roles as $role)
                                                        @if(\Illuminate\Support\Facades\Session::get('user_role') == 2)
                                                            @if($role->id != 1 && $role->id != 2 && $role->id != 9 && $role->id != 10 && $role->id != 11)
                                                                <option value="{{$role->id}}">{{$role->title}}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 3)
                                            @foreach($roles as $role)
                                                @if($role->id == 5)
                                                    <input type="hidden" name="role" id="_role" value="{{$role->id}}" />
                                                @endif
                                            @endforeach
                                        @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 4)
                                            @foreach($roles as $role)
                                                @if($role->id == 6)
                                                    <input type="hidden" name="role" id="_role" value="{{$role->id}}" />
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <button type="button"
                                                    class="btn btn-primary w-10 float-left"
                                                    name="submitAddUserForm"
                                                    onclick="displayBackUserContactInformationSection();">
                                                Back
                                            </button>
                                            <input type="submit"
                                                   class="btn btn-primary float-right w-10"
                                                   name="submitAddUserForm"
                                                   id="submitAddUserForm" value="Add"/>
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
@endsection
