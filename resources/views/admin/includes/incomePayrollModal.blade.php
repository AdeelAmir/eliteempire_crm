<?php
$Url = "";
if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
    $Url = url('admin/payroll/income-details/store-update');
} else {
    $Url = url('manager/payroll/income-details/store-update');
}
?>
<div class="modal fade" id="incomePayrollModal" tabindex="200" role="dialog" aria-labelledby="incomePayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="incomePayrollModalForm">
                @csrf
                <input type="hidden" name="id" id="incomePayrollId" value="0" />
                <input type="hidden" name="MasterId" id="masterPayrollId" value=""/>
                <input type="hidden" name="startDate" id="incomeStartDate" value="" />
                <input type="hidden" name="endDate" id="incomeEndDate" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="incomePayrollModalLabel">Payroll Details</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="_hours">Hours</label>
                                <input type="number" step="any" class="form-control" name="_hours" id="_hours" placeholder="Hours" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="_tax">Tax (%)</label>
                            <input type="number" step="any" class="form-control" name="_tax" id="_tax" placeholder="Tax (%)" required>
                        </div>
                        <div class="col-md-6">
                            <label for="_drawBalance">Draw Balance</label>
                            <input type="number" step="any" class="form-control" name="_drawBalance" id="_drawBalance" placeholder="Draw Balance" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" id="StoreUpdateIncomePayrollBtn" onclick="StoreUpdateIncomeDetails();">Save</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" onclick="ClearIncomePayrollModalFields();">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>