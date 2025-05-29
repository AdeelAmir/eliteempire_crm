<?php
$Url = "";
if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
    $Url = url('admin/sale/store');
}
elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2){
    $Url = url('global_manager/sale/store');
}
?>

<div class="modal fade" id="addSaleModal" tabindex="200" role="dialog" aria-labelledby="addSaleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="addSaleForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="leadId" id="leadId" value=""/>
                <input type="hidden" name="leadLeadNumber" id="leadLeadNumber" value=""/>
                <input type="hidden" name="leadProductId" id="leadProductId" value="0"/>
                <input type="hidden" name="addsale_type" id="addsale_type" value="Closed Won"/>

                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Add Sale</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addsale_leadnumber">Lead Number</label>
                        <input type="text" name="addsale_leadnumber" id="addsale_leadnumber" maxlength="10"
                               disabled class="form-control"/>
                    </div>
                    {{--<div class="form-group">
                        <label for="addsale_type">Type</label>
                        <select class="form-control" name="addsale_type" id="addsale_type" required>
                            <option value="Closed Won">Closed Won</option>
                        </select>
                    </div>--}}
                    <div class="form-group">
                        <label class="w-100" for="addsale_contractamount">Contract Amount</label>
                        <input type="number" name="addsale_contractamount" id="addsale_contractamount"
                               class="form-control" min="0" required/>
                    </div>
                    <div class="form-group">
                        <label for="addsale_contractdate">Contract Date</label>
                        <input type="date" name="addsale_contractdate" id="addsale_contractdate"
                               class="form-control" required/>
                    </div>
                    {{--<div class="form-group">
                        <label for="addsale_product">Product</label>
                        <input type="text" name="addsale_product" id="addsale_product" class="form-control"
                               disabled/>
                    </div>--}}
                    <div class="form-group">
                        <label for="addsale_netprofit">Net Profit(%)</label>
                        <input type="number" name="addsale_netprofit" id="addsale_netprofit"
                               class="form-control" min="0" max="100" required/>
                    </div>

                    <div class="repeater-custom-show-hide">
                        <div data-repeater-list="payee">
                            <div data-repeater-item="" style="">
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="addsale_payees">Select Payee</label>
                                                    <select name="addsale_payees" id="addsale_payees"
                                                            class="form-control __addsale_payees" required>
                                                        <option value="" selected disabled="disabled">Select</option>
                                                        @foreach($payee_users as $user)
                                                            <option value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="addsale_amountType">Amount Type</label>
                                                    <select name="addsale_amountType" id="addsale_amountType"
                                                            class="form-control __addsale_amountType" required>
                                                        <option value="" selected disabled="disabled">Select</option>
                                                        <option value="flat">Flat Amount</option>
                                                        <option value="percentage">Percentage</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="addsale_totalpayeesamount">Total Payee</label>
                                                    <input type="number" step="any" name="addsale_totalpayeesamount"
                                                           id="addsale_totalpayeesamount" class="form-control" min="0"
                                                           required/>
                                                </div>
                                                <span data-repeater-delete=""
                                                      class="btn btn-outline-danger btn-sm float-right deletePayeeBtn">
                                                    <span class="far fa-trash-alt mr-1"></span> Delete
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-12">
                                <span data-repeater-create="" class="btn btn-outline-success btn-sm float-right">
                                    <span class="fa fa-plus"></span> Add Payee
                                </span>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Add</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>