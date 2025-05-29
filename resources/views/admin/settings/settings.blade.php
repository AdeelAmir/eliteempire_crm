@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="viewTeam">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Settings</span></h4>
            </div>
        </div>

        <div class="row" id="viewTeamPage">
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

            {{--Hours Price--}}
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
                            Hours Price
                        </h6>
                        <form action="{{url('/admin/settings/hoursPrice')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3 mt-3">
                                    <label for="hoursPrice">Per Hour Price*</label>
                                    <input type="number" step="any" name="hoursPrice" id="hoursPrice" class="form-control"
                                           placeholder="Per Hour Price" value="{{$Settings->hours_price}}" required/>
                                </div>
                                <div class="col-md-12 text-center mt-3">
                                    <input type="submit" class="btn btn-primary" value="Save" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection