@extends('admin.layouts.app')
@section('content')
    <style>
        .cntr {
            display: table;
            width: 100%;
            height: 100%;
        }
        .cntr .cntr-innr {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        /*** STYLES ***/
        .search {
            display: inline-block;
            position: relative;
            height: 35px;
            width: 35px;
            box-sizing: border-box;
            /*margin: 0px 8px 7px 0px;*/
            padding: 3px 9px 0 9px;
            border: 3px solid #023A51;
            border-radius: 25px;
            transition: all 200ms ease;
            cursor: text;
        }
        .search:after {
            content: "";
            position: absolute;
            width: 3px;
            height: 20px;
            right: -5px;
            top: 21px;
            background: #023A51;
            border-radius: 3px;
            transform: rotate(-45deg);
            transition: all 200ms ease;
        }
        .search.active,
        .search:hover {
            width: 100%;
            margin-right: 0;
        }
        .search.active:after,
        .search:hover:after {
            height: 0;
        }
        .search input {
            width: 100%;
            border: none;
            box-sizing: border-box;
            font-family: Helvetica;
            font-size: 15px;
            color: inherit;
            background: transparent;
            outline-width: 0;
        }

        #searchFaq{
            width: 75%;
            border-radius: 50px;
            margin: 0 auto;
            padding-left: 30px;
        }

        .searchIcon1 {
            position: absolute;
            left: 90px;
            top: 11px;
        }

        .searchIcon2 {
            position: absolute;
            left: 25px;
            top: 11px;
        }

        #searchFaq.active,
        #searchFaq:hover {
            width: 100%;
        }
    </style>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">
                    ELITE EMPIRE - <span class="text-primary">Knowledge Zone</span>
                </h4>
            </div>
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                $Url = url('acquisition_manager/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                $Url = url('disposition_manager/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                $Url = url('acquisition_representative/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                $Url = url('disposition_representative/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
                $Url = url('cold_caller/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
                $Url = url('affiliate/training');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
                $Url = url('realtor/training');
            }
            ?>
            <button class="btn btn-secondary float-right" type="button" onclick="window.location.href='{{$Url}}';">Back</button>
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
            <div class="col-md-2"></div>
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <span style="font-size: large;">Knowledge Zone</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <i class="fa fa-search searchIcon searchIcon1"></i>
                                    <input type="text" class="form-control" name="searchFaq" id="searchFaq" onmouseenter="MoveFaqSearchIcon(1);" onmouseleave="MoveFaqSearchIcon(2);" onfocus="SearchFaqActive(this);" onfocusout="SearchFaqBlur(this);" onkeyup="SearchFaq(this);" />
                                </div>
                                <div class="col-md-2"></div>
                            </div>
                        </div>
                        <?php
                            $FaqsCount = sizeof($Faqs);
                            $Count = 1;
                        ?>
                        <div class="row d-none" id="mainFaqDiv">
                            @foreach($Faqs as $faq)
                                @if($FaqsCount == $Count)
                                    <div class="col-md-12">
                                        <p class="mb-1" style="font-size: 16px; font-weight: bold;">Q.&nbsp;&nbsp;{{$faq->question}}</p>
                                        <p style="font-size: 15px;">{!! $faq->answer !!}</p>
                                    </div>
                                @else
                                    <div class="col-md-12 mb-3">
                                        <p class="mb-1" style="font-size: 16px; font-weight: bold;">Q.&nbsp;&nbsp;{{$faq->question}}</p>
                                        <p style="font-size: 15px;">{!! $faq->answer !!}</p>
                                        {{--<p style="font-size: 15px;">A.&nbsp;&nbsp;{{$faq->answer}}</p>--}}
                                    </div>
                                @endif
                                <?php
                                    $Count++;
                                ?>
                            @endforeach
                        </div>

                        <div class="row" id="searchResultsFaqDiv" style="display: none;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    @include('admin.includes.questionAnswerModal')
@endsection
