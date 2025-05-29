@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="addSalePage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Elite Empire - <span class="text-primary">Add Sale</span></h4>
            </div>
        </div>

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

            {{--General Details--}}
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <br>
                        @endif
                        <h6 class="card-title">
                            General
                        </h6>
                        <div class="row">
                            <div class="col-md-3 mb-3 mt-3">
                                <label for="firstname">Lead Number</label>
                                <input type="text" name="lead_number" id="lead_number" class="form-control"
                                       placeholder="Enter Lead Number"/>
                            </div>
                            <div class="col-md-1 mb-3 mt-5">
                                <button class="btn btn-primary" name="search_by_leadnumber" id="search_by_leadnumber"
                                        onclick="SearchByLeadNumber();">Search
                                </button>
                            </div>

                            <div class="col-md-4"></div>

                            <div class="col-md-3 mb-3 mt-3">
                                <label for="firstname">Phone Number</label>
                                <input type="text" name="lead_phone_number" id="lead_phone_number" maxlength="20"
                                       class="form-control"
                                       placeholder="Enter Phone Number"/>
                            </div>
                            <div class="col-md-1 mb-3 mt-5">
                                <button class="btn btn-primary" name="search_by_leadnumber" id="search_by_leadnumber"
                                        onclick="SearchByPhoneNumber();">Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Search Lead Table - Start -->
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="search_leads_table" class="table w-100">
                                <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Lead Number</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone Number</th>
                                    <th>Duplicated</th>
                                    <th>Status</th>
                                    <th>Add Sale</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add Search Lead Table - End -->
        </div>
    </div>
    @include('admin.includes.addSaleModal')
@endsection