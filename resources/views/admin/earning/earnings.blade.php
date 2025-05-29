@extends('admin.layouts.app')
@section('content')
    <div class="page-content" id="earningsPage">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class=" mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">ALL Earnings</span></h4>
            </div>
        </div>
        @if($Role == 1)
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Total Sales</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">{{$TotalSales}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart4" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Net Amount</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">${{$NetAmount}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Total Lost</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">${{$Totallost}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Total Profit</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">${{$TotalProfit}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($Role == 2)
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">You Earned</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">
                                        $<?php echo isset($Earning) ? number_format($Earning, 2) : 0;?></h3>
                                </div>
                                {{--<div class="col-4">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($Role == 3)
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">You Earned</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">
                                        $<?php echo isset($Earning) ? number_format($Earning, 2) : 0;?></h3>
                                </div>
                                {{--<div class="col-4">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($Role == 4 || $Role == 5)
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">New Leads</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">{{$NewLeads}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart1" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Total Leads</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">{{$TotalLeads}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart2" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">Total Confirmed Leads</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">{{$TotalConfirmedLeads}}</h3>
                                </div>
                                {{--<div class="col-6 col-md-12 col-xl-7">--}}
                                    {{--<div id="apexChart3" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0 mt-1">You Earned</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mt-2">
                                        $<?php echo isset($Earning) ? number_format($Earning, 2) : 0;?></h3>
                                </div>
                                {{--<div class="col-4">--}}
                                    {{--<div id="apexChart5" class="mt-md-3 mt-xl-0"></div>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection