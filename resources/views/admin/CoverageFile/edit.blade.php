@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="editCallRequest">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">DYNAMIC EMPIRE - <span class="text-primary">EDIT COVERAGE FILE</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/coverage-file')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('manager/coverage-file')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>
        @if(Session::get('user_role') == 1)
        <form action="{{url('admin/coverage-file/update')}}" method="post" id="editCoverageFileForm" enctype="multipart/form-data">
        @elseif(Session::get('user_role') == 2)
        <form action="{{url('manager/coverage-file/update')}}" method="post" id="editCoverageFileForm" enctype="multipart/form-data">
        @endif
            @csrf
            <input type="hidden" name="id" id="id" value="{{$TrainingLink[0]->id}}"/>
            {{--<input type="hidden" name="oldFile" id="oldFile" value="{{$TrainingLink[0]->coverage_file}}"/>--}}
            <div class="row" id="editCoverageFilePage">
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
                                Coverage File
                            </h6>
                            {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                            {{--<label for="file">Upload File*</label>--}}
                            {{--<input type="file" class="form-control" name="file" id="file" accept=".xlsx, .xls" required />--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="link">Link*</label>
                                    <textarea name="link" id="link" class="form-control" placeholder="Paste Link Here"
                                              rows="3" required>{{$TrainingLink[0]->coverage_file}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <input type="submit" class="btn btn-primary w-10" value="Save"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
