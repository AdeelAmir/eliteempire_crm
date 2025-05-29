@extends('admin.layouts.app')
@section('content')

    <div class="page-content" id="addNewUser">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Dynamic Empire - <span class="text-primary">Expenses Form</span></h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
                @if($Role == 1)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('admin/expenses')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @elseif($Role == 2)
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{url('global_manager/expenses')}}';">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </button>
                @endif
            </div>
        </div>

        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/expenses/store');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/expenses/store');
        }
        ?>

        <form action="{{$Url}}" method="post" id="addUserForm" enctype="multipart/form-data">
            @csrf
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
                                <div class="col-md-3 mb-3">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control"
                                           placeholder="Description" required/>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="total">Total</label>
                                    <input type="text" name="total" id="total" class="form-control"
                                           placeholder="Total" required/>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date">Invoice Date</label>
                                    <div class="input-group date expenseDate" data-date-format="mm/dd/yyyy"
                                         data-link-field="startDateFilter">
                                        <input class="form-control" id="date" name="date" size="16" type="text"
                                               value="">
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                    <input type="hidden" id="expenseDate" name="expenseDate" value="" required/>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="vendor">Vendor</label>
                                    <input type="text" name="vendor" id="vendor" class="form-control"
                                           placeholder="Vendor" required/>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="location">Location</label>
                                    <input type="text" name="location" id="location" class="form-control"
                                           placeholder="Location" required/>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="currency">Currency</label>
                                    <select class="form-control" name="currency" id="currency"
                                            onchange="checkExpenseCurrency();" required>
                                        <option value="USD">USD</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3" id="_currencyNameBlock" style="display:none;">
                                    <label for="other_currency_name">Curreny Name</label>
                                    <input type="text" name="other_currency_name" id="other_currency_name"
                                           class="form-control" placeholder="Currency Name"/>
                                </div>
                                <div class="col-md-3 mb-3" id="_exchangeRateBlock" style="display:none;">
                                    <label for="rate">Exchange Rate</label>
                                    <input type="text" name="rate" id="rate" class="form-control"
                                           placeholder="Exchange Rate"/>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" placeholder="Notes"
                                              rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md-12 text-right mt-5">
                                    <input type="submit" class="btn btn-primary " name="submitAddExpenseForm"
                                           id="submitAddUserForm" value="Add"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection