@extends('admin.layouts.app')
@section('content')
<div class="page-content" id="editUserPage">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Edit Product Form</span></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            @if($Role == 1)
            <button type="button" class="btn btn-primary"
            onclick="window.location.href='{{url('admin/products')}}';">
            <i class="fas fa-arrow-left mr-1"></i>
            Back
        </button>
        @elseif($Role == 2)
        <button type="button" class="btn btn-primary"
        onclick="window.location.href='{{url('admin/products')}}';">
        <i class="fas fa-arrow-left mr-1"></i>
        Back
    </button>
    @endif
</div>
</div>

@if(Session::get('user_role') == 1)
<form action="{{url('admin/product/update')}}" method="post" id="editProductForm" enctype="multipart/form-data">
    @elseif(Session::get('user_role') == 2)
    <form action="{{url('admin/product/update')}}" method="post" id="editProductForm" enctype="multipart/form-data">
        @endif
        @csrf
        <input type="hidden" name="id" id="_id" value="{{$product_id}}"/>

        <div class="row justify-content-center">
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
            <div class="col-md-6 grid-margin stretch-card">
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
                        @foreach($product_details as $product)
                        <div class="row">
                            <div class="col-md-12 mb-12 mt-12">
                                <label for="productname">Product Name*</label>
                                <input type="text" name="name" id="productname" class="form-control" value="{{$product->name}}" placeholder="Enter Product Name"  <?php if($Role != 1){ echo "disabled"; } ?> required/>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12 text-right mt-3">
                                <input type="submit" class="btn btn-primary " name="submitAddProductForm"
                                id="submitAddProductForm" value="Save"/>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
