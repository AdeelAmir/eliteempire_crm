<style>
    /*Slider*/
    .slidecontainer {
        width: 100%;
        margin-top: -5px;
    }

    .slider {
        -webkit-appearance: none;
        width: 100%;
        height: 1px;
        border-radius: 5px;
        background: #000;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
    }

    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #15D16C;
        cursor: pointer;
    }

    .slider::-moz-range-thumb {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #15D16C;
        cursor: pointer;
    }

    .form-control:disabled {
        background-color: #fff !important;
    }

    /*Slider*/

    /*#offer_range_low_value, #offer_range_high_value{
      padding-top: 13px;
    }*/
</style>

<div class="modal fade" id="leadEvaluationModal" tabindex="200" role="dialog" aria-labelledby="leadEvaluationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addFaqModalLabel">Evaluation Details</h5>
                </div>
                <div class="modal-body">
                    {{--Calculation Details--}}
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                {{--<h6 class="card-title">
                                    Evaluation
                                </h6>--}}
                                <div class="row">
                                    {{--Offer Range Low Slider--}}
                                    <div class="col-md-12">
                                        <label for="offer_range_slider">Offer Range</label>
                                    </div>
                                    <div class="col-3" id="offer_range_low_value">
                                        1
                                    </div>
                                    <div class="col-6">
                                        <div class="slidecontainer mt-2">
                                            <input type="range" min="1" max="100" step="0.1" value="50"
                                                   id="offer_range_slider" class="slider w-100" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-3 text-right" id="offer_range_high_value">
                                        100
                                    </div>

                                    <div class="col-md-12">
                                        <hr>
                                    </div>

                                    {{--Evaluation Fields--}}
                                    <div class="col-md-6 mb-2">
                                        <label for="askingPrice" class="w-100">Asking Price</label>
                                        <input type="number" step="any" name="askingPrice" id="askingPrice"
                                               value="" class="form-control"
                                               placeholder="Asking Price" disabled/>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="arv" class="w-100">ARV</label>
                                        <input type="number" step="any" name="arv" id="arv" value=""
                                               class="form-control"
                                               placeholder="After Repair Value" disabled/>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="assignment_fee" class="w-100">Assignment</label>
                                        <input type="number" step="any" name="assignment_fee" id="assignment_fee"
                                               value="" class="form-control"
                                               placeholder="Fee" disabled/>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="rehab_cost" class="w-100">Rehab Cost</label>
                                        <input type="number" step="any" name="rehab_cost" id="rehab_cost"
                                               value="" class="form-control"
                                               placeholder="Rehab Cost" disabled/>
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1 || \Illuminate\Support\Facades\Auth::user()->role_id == 2)
                                        <div class="col-md-6 mb-2">
                                            <label for="arv_rehab_cost" class="w-100">ARV-Rehab Cost</label>
                                            <input type="number" step="any" name="arv_rehab_cost" id="arv_rehab_cost"
                                                   value="" class="form-control"
                                                   placeholder="ARV-Rehab Cost" disabled/>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="arv_sales_closing_cost" class="w-100">ARV</label>
                                            <input type="number" step="any" name="arv_sales_closing_cost"
                                                   id="arv_sales_closing_cost" value=""
                                                   class="form-control" placeholder="ARV (Sales + Closing Cost)"
                                                   disabled/>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="property_total_value" class="w-100">Property Total Value</label>
                                            <input type="number" step="any" name="property_total_value"
                                                   id="property_total_value" value=""
                                                   class="form-control" placeholder="Property Total Value" disabled />
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="wholesales_closing_cost" class="w-100">Wholesales Closing
                                                Cost</label>
                                            <input type="number" step="any" name="wholesales_closing_cost"
                                                   id="wholesales_closing_cost"
                                                   value=""
                                                   class="form-control" placeholder="Wholesales Closing Cost" disabled/>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="all_in_cost" class="w-100">All In Cost</label>
                                            <input type="number" step="any" name="all_in_cost" id="all_in_cost"
                                                   value=""
                                                   class="form-control" placeholder="All In Cost" disabled/>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="investor_profit" class="w-100">Investor Profit</label>
                                            <input type="number" step="any" name="investor_profit" id="investor_profit"
                                                   value=""
                                                   class="form-control" placeholder="Investor Profit" disabled/>
                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-2">
                                        <label for="sales_price" class="w-100">Sales Price</label>
                                        <input type="number" step="any" name="sales_price" id="sales_price"
                                               value=""
                                               class="form-control" placeholder="Sales Price" disabled/>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="m_a_o" class="w-100">M.A.O</label>
                                        <input type="number" step="any" name="m_a_o" id="m_a_o"
                                               value=""
                                               class="form-control" placeholder="Maximum Allow Offer" disabled/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
