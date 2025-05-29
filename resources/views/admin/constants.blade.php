@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">EDIT MAGIC NUMBERS</span></h4>
            </div>
        </div>

        <div class="row" id="updateProfilePage">
            <div class="col-12">
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
            <div class="col-md-3"></div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Constants
                        </h6>
                        <?php
                        $Url = "";
                        if ($Role == 1) {
                            $Url = url('admin/magicnumber/update');
                        }
                        ?>
                        <form action="{{$Url}}" method="post" id="updateConstantsForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100" for="ARV_SALES_CLOSING_COST_CONSTANT">ARV SALES CLOSING COST</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="ARV_SALES_CLOSING_COST_CONSTANT"
                                               id="ARV_SALES_CLOSING_COST_CONSTANT"
                                               value="{{$Constants[0]->value}}" disabled required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100" for="WHOLESALES_CLOSING_COST_CONSTANT">WHOLESALES CLOSING COST</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="WHOLESALES_CLOSING_COST_CONSTANT"
                                               id="WHOLESALES_CLOSING_COST_CONSTANT"
                                               value="{{$Constants[1]->value}}" disabled required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100" for="INVESTOR_PROFIT_CONSTANT">INVESTOR PROFIT</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="INVESTOR_PROFIT_CONSTANT"
                                               id="INVESTOR_PROFIT_CONSTANT"
                                               value="{{$Constants[2]->value}}" disabled required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100" for="OFFER_LOWER_RANGE_CONSTANT">OFFER LOWER RANGE</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="OFFER_LOWER_RANGE_CONSTANT"
                                               id="OFFER_LOWER_RANGE_CONSTANT"
                                               value="{{$Constants[3]->value}}" disabled required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100" for="OFFER_HIGHER_RANGE_CONSTANT">OFFER HIGHER RANGE</label>
                                        <input type="number"
                                               step="any"
                                               class="form-control"
                                               name="OFFER_HIGHER_RANGE_CONSTANT"
                                               id="OFFER_HIGHER_RANGE_CONSTANT"
                                               value="{{$Constants[4]->value}}" disabled required />
                                    </div>
                                </div>

                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary" id="SaveChangesBtn" style="display:none;">
                                        <i class="fa fa-check"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-danger"  id="EditMagicNumberBtn"onclick="EditConstantValues();">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>

    @include('admin.includes.confirmEditMagicNumbersModal')
@endsection
