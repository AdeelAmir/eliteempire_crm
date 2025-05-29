@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="editUserPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Edit User Form</span></h4>
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
                @elseif($Role == 4)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('disposition_manager/users')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/user/update');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/user/update');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 3) {
            $Url = url('acquisition_manager/user/update');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 4) {
            $Url = url('disposition_manager/user/update');
        }
        ?>
        <form action="{{$Url}}" method="post" id="editUserForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="_id" value="{{$user_id}}"/>
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
                                    @foreach($user_details as $u_details)
                                    {{-- {{ dd($u_details) }} --}}
                                        <input type="hidden" name="userRole_Old" id="userRole_Old"
                                               value="{{$u_details->role}}"/>
                                        <div class="row">
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="firstname"><b>First Name</b></label>
                                                <input type="text" name="firstname" id="firstname"
                                                       value="{{$u_details->firstname}}"
                                                       class="form-control"
                                                       placeholder="Enter Your First Name"
                                                       <?php if ($Role != 1) {
                                                           echo "disabled";
                                                       } ?> required/>
                                                <div style="margin-top: 7px;" id="f_name"></div>
                                            </div>
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="middlename">Middle Name</label>
                                                <input type="text" name="middlename" id="middlename"
                                                       value="{{$u_details->middlename}}"
                                                       class="form-control"
                                                       placeholder="Enter Your Middle Name" <?php if ($Role != 1) {
                                                    echo "disabled";
                                                } ?> />
                                            </div>
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="lastname"><b>Last Name</b></label>
                                                <input type="text" name="lastname" id="lastname"
                                                       class="form-control"
                                                       value="{{$u_details->lastname}}"
                                                       <?php if ($Role != 1) {
                                                           echo "disabled";
                                                       } ?> placeholder="Enter Your Last Name"
                                                       required/>
                                            </div>
                                            <div class="col-md-3 mb-3 mt-3">
                                                <label for="dob"><b>Date of Birth</b></label>
                                                <input type="text" name="dob" id="dob" class="form-control"
                                                       placeholder="MM/DD/YYYY" <?php if ($Role != 1) {
                                                    echo "disabled";
                                                } ?> value="{{$u_details->dob}}" required/>
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
                                    @endforeach
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
                                            <label for="phone" class="w-100"><b>Phone Number 1</b><i
                                                        class="fa fa-plus-circle float-right" style="cursor: pointer;"
                                                        onclick="ShowPhone2();"></i></label>
                                            <input type="number" step="any" name="phone" id="phone"
                                                   class="form-control"
                                                   value="{{$u_details->phone}}"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20" autocomplete="off" required/>
                                            <div style="margin-top: 7px;" id="user_phone"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3" id="phone2Field" style="display: none;">
                                            <label for="phone2" class="w-100">Phone Number 2<i
                                                        class="fa fa-times-circle float-right" style="cursor: pointer;"
                                                        onclick="HidePhone2();"></i></label>
                                            <input type="number" step="any" name="phone2" id="phone2"
                                                   class="form-control"
                                                   placeholder="Enter Your Phone Number"
                                                   maxlength="20" autocomplete="off" />
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="email" class="w-100"><b>Email Address</b><i
                                                class="fa fa-plus-circle float-right" style="cursor: pointer;"
                                                onclick="ShowEmail2();"></i></label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                   placeholder="Enter Your Email Address"
                                                   value="{{$u_details->email}}" autocomplete="off" />
                                            <div style="margin-top: 7px;" id="user_email"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3"  id="email2" style="display: none;">
                                            <label for="secondary_email" class="w-100">Secondary Email<i
                                                class="fa fa-times-circle float-right" style="cursor: pointer;"
                                                onclick="HideEmail2();"></i></label>
                                            <input type="email" name="secondary_email" id="secondary_email" class="form-control"
                                                   placeholder="Enter Your Secondary Email Address"
                                                   value="{{$u_details->secondary_email}}" autocomplete="off" />
                                            <div style="margin-top: 7px;" id="user_email"></div>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="city">Street</label>
                                            <input type="text" name="street" id="street"
                                                   value="{{$u_details->street}}" class="form-control"
                                                   placeholder="Enter Your Street"/>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="city">City</label>
                                            <select name="city" id="city" class="form-control">
                                                <option value="" selected disabled>Select City</option>
                                                @foreach($cities as $city)
                                                    <option value="{{$city->city}}" <?php if ($u_details->city == $city->city) {
                                                        echo "selected";
                                                    } ?> >{{$city->city}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="state">State</label>
                                            <select class="state" name="state" id="state"
                                                    class="form-control" onchange="LoadStateCountyCity(); CheckForUserState2('submitAddUserForm');" required>
                                                <option value="">Select State</option>
                                                @foreach($states as $state)
                                                    @if(\Illuminate\Support\Facades\Session::get('user_role') == 1 || \Illuminate\Support\Facades\Session::get('user_role') == 2)
                                                        <option value="{{$state->name}}" <?php if ($u_details->state == $state->name) {
                                                            echo "selected";
                                                        } ?> >{{$state->name}}</option>
                                                    @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 3 || \Illuminate\Support\Facades\Session::get('user_role') == 4)
                                                        @if($state->name == \App\Helpers\SiteHelper::GetCurrentUserState())
                                                            <option value="{{$state->name}}" <?php if ($u_details->state == $state->name) {
                                                                echo "selected";
                                                            } ?> >{{$state->name}}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{$state->name}}" <?php if ($u_details->state == $state->name) {
                                                            echo "selected";
                                                        } ?> >{{$state->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="county">County</label>
                                            <select name="county" id="county" class="form-control county">
                                                <option value="" selected disabled>Select County</option>
                                                @foreach($counties as $county)
                                                    <option value="{{$county->county_name}}" <?php if ($u_details->county == $county->county_name) {
                                                        echo "selected";
                                                    } ?> >{{$county->county_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 mt-3">
                                            <label for="city  text-truncate" class="w-100">Zip code/Postal</label>
                                            <input type="number" value="{{$u_details->zipcode}}"
                                                   name="zipcode" id="zipcode" class="form-control"
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
                        <div class="col-md-12 grid-margin stretch-card" id="UserIdentificationBlock"
                             style="display:none;">
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
                                                                                <input type="text" name="documentname"
                                                                                       class="form-control"
                                                                                       placeholder="Enter Document Name"
                                                                                       autocomplete="off"/>
                                                                            </div>
                                                                            <div class="col-md-4 mb-3 mt-3">
                                                                                <label class="w-100">Document Number</label>
                                                                                <input type="number"
                                                                                       name="documentnumbers"
                                                                                       class="form-control"
                                                                                       placeholder="Enter Document Numbers"
                                                                                       autocomplete="off"/>
                                                                            </div>
                                                                            <div class="col-md-4 mb-3 mt-3">
                                                                                <label class="add_document_label w-100">Document
                                                                                    1</label>
                                                                                <input type="file" name="documentFile"
                                                                                       class="form-control"
                                                                                       accept="image/jpeg, image/png, image/jpg, application/pdf"/>
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
                                    <div class="col-md-3 mb-3 mt-3">
                                        <label for="role">Role</label>
                                        <select class="form-control" name="role" id="role" onchange="CheckForUserState2('submitAddUserForm');" required>
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                @if(\Illuminate\Support\Facades\Session::get('user_role') == 1)
                                                    @if($role->id != 1 && $role->id != 9 && $role->id != 10 && $role->id != 11)
                                                        <option value="{{$role->id}}" <?php if ($u_details->role == $role->id) {
                                                            echo "selected";
                                                        } ?>>
                                                            {{$role->title}}
                                                        </option>
                                                    @endif
                                                @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 2)
                                                    @if($role->id != 1 && $role->id != 2 && $role->id != 9 && $role->id != 10 && $role->id != 11)
                                                        <option value="{{$role->id}}" <?php if ($u_details->role == $role->id) {
                                                            echo "selected";
                                                        } ?>>
                                                            {{$role->title}}
                                                        </option>
                                                    @endif
                                                @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 3)
                                                    @if($role->id == 5)
                                                        <option value="{{$role->id}}" <?php if ($u_details->role == $role->id) {
                                                            echo "selected";
                                                        } ?>>
                                                            {{$role->title}}
                                                        </option>
                                                    @endif
                                                @elseif(\Illuminate\Support\Facades\Session::get('user_role') == 4)
                                                    @if($role->id == 6)
                                                        <option value="{{$role->id}}" <?php if ($u_details->role == $role->id) {
                                                            echo "selected";
                                                        } ?>>
                                                            {{$role->title}}
                                                        </option>
                                                    @endif
                                                @else
                                                    @if($role->id != 1 && $role->id != 2 && $role->id != 9 && $role->id != 10 && $role->id != 11)
                                                        <option value="{{$role->id}}" <?php if ($u_details->role == $role->id) {
                                                            echo "selected";
                                                        } ?>>
                                                            {{$role->title}}
                                                        </option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
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
                                                   id="submitAddUserForm" value="Update"/>
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

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".deletePayeeBtn").trigger('click');
        });

        function DeleteOldDocument(RowCount, File) {
            $("#oldDocumentsRow" + RowCount).remove();
            NumberDocumentNumbers();
            $("#editUserForm").append("<input name='fileToDelete[]' value='" + File + "' type='hidden' />")
        }
    </script>
@endpush
