@extends('admin.layouts.app')
@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?key={{$_ENV['GOOGLE_MAPS_API_KEY']}}&libraries=places"></script>

    <div class="page-content" id="coverageAreaPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">ELITE EMPIRE - <span class="text-primary">COVERAGE AREA</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap"></div>
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

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Coverage Area
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                {{--Search Bar--}}
                                <div class="row no-gutters mb-2">
                                    <div class="col-12 p-1">
                                        <input type="text" class="form-control" name="searchLead" id="searchLead"
                                               placeholder="Search" onkeyup="SearchLeadInZoning(this.value);"/>
                                    </div>
                                </div>
                                {{--Search Bar--}}

                                {{--Content--}}
                                <div class="row no-gutters" id="results" style="overflow-y: scroll; max-height: 600px;">
                                    <?php
                                    $Count = 0;
                                    $LeadsController = new \App\Http\Controllers\LeadController();
                                    ?>
                                    @foreach($LeadLocations as $location)
                                        <?php
                                        $Value = $location->contract_amount != '' ? '$' . number_format($location->contract_amount) : '$0';
                                        ?>
                                        <div class="col-12 col-md-6 p-1">
                                            <div class="card"
                                                 onclick="SetMapCenter('{{$location->lat}}', '{{$location->long}}');"
                                                 style="cursor: pointer;">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-1">
                                                        {{$location->firstname . ' ' . $location->lastname}}
                                                        <p class="mt-1 mb-0"
                                                           style="font-size: small;">{{$location->lead_number}}</p>
                                                    </h6>
                                                    <p class="mb-1" style="font-size: x-small;">Amount:
                                                        <b>{{$Value}}</b></p>
                                                    <p class="mb-1" style="font-size: x-small;">Close Date:
                                                        <b>{{\Carbon\Carbon::parse($location->DateCreated)->format('m/d/Y')}}</b>
                                                    </p>
                                                    <p class="mb-1" style="font-size: x-small;">Address:
                                                        <b>{{$location->formatted_address}}</b></p>
                                                    <p class="mb-3"
                                                       style="font-size: x-small;">{!! $LeadsController->GetLeadStatusColor($location->lead_status) !!}</p>
                                                    <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">{{strtoupper(substr($location->firstname, 0, 1) . substr($location->lastname, 0, 1))}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{--<div class="col-12 col-md-6 pl-1 pr-1">
                                            <div id="smallMap_{{$Count}}" style="height: 150px; width: 100%;"></div>

                                            <p class="mt-1 mb-2"><b>{{$Value}}</b></p>
                                        </div>--}}
                                        <?php $Count++; ?>
                                        @if($Count == 10)
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--Content--}}

                                <div class="row no-gutters" id="searchResults"
                                     style="overflow-y: scroll; max-height: 600px; display: none;"></div>
                            </div>
                            <div class="col-md-8">
                                <div id="coverageAreaMap" style="height: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection