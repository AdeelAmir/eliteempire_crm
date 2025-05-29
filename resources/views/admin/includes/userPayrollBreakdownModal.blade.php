<style media="screen">
    .modal-dialog {
        max-width: 1050px;
        margin: 30px auto;
    }
</style>
<?php
$Url = "";
if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
    $Url = url('admin/payroll/edit/earning');
} else {
    $Url = url('global_manager/payroll/edit/earning');
}
?>
<?php
$BonusUrl = "";
if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
    $BonusUrl = url('admin/payroll/bonus');
} else {
    $BonusUrl = url('global_manager/payroll/bonus');
}
?>
<div class="modal fade" id="userPayrollBreakdownModal" tabindex="200" role="dialog"
     aria-labelledby="userPayrollBreakdownModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <input type="hidden" name="_EarningMasterId" id="_userEarningMasterId" value="">
            <!-- Payroll Breakdowns -->
            <div class="" id="PayrollBreakdowns"></div>

            <!-- Edit Earning -->
            <div id="EditEarning" style="display:none;">
                <form method="post" action="#" id="editEarningPayrollModalForm">
                    <input type="hidden" name="id" id="editEarningPayrollId" value=""/>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEarningPayrollModalLabel">Edit Earning</h5>
                    </div>
                    <div class="modal-body">
                        <input type="number" class="form-control" name="editEarningAmount" id="editEarningAmount"
                               placeholder="Enter Earning Amount" step="any" min="0"/>
                    </div>
                    <div id="earningAmount" style="font-size: 12px; color:red;margin-top: -5px;" class="ml-3"></div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="button" onclick="UpdateEarningAmount();">Add</button>
                        <button class="btn btn-outline-secondary" type="button" onclick="CancelEditEarning();">Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit Bonus -->
            <div class="" id="EditBonus" style="display:none;">
                <form method="post" action="#" id="bonusPayrollForm">
                    @csrf
                    <input type="hidden" name="id" id="bonusPayrollId" value=""/>
                    <div class="modal-header">
                        <h5 class="modal-title" id="bonusPayrollModalLabel">Bonus</h5>
                    </div>
                    <div class="modal-body">
                        <input type="number" step="any" class="form-control" name="bonus" id="bonusAmountValue"
                               placeholder="Enter Bonus Amount" required/>
                    </div>
                    <div id="bonusAmount" style="font-size: 12px; color:red;margin-top: -5px;" class="ml-3"></div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="button" onclick="UpdateBonusAmount();">Add</button>
                        <button class="btn btn-outline-secondary" type="button" onclick="CancelEditBonus();">Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit Pay Period Entry -->
            <div class="" id="EditPayPeriod" style="display:none;">
                <form method="post" action="#" id="payPeriodEditForm">
                    @csrf
                    <input type="hidden" name="id" id="_payPeriodId" value=""/>
                    <div class="modal-header">
                        <h5 class="modal-title">Edit</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="earnings">Earnings</label>
                                    <input type="number" step="any" class="form-control" name="earnings" id="earnings"
                                           placeholder="Earnings" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus">Bonus</label>
                                    <input type="number" step="any" class="form-control" name="bonus" id="bonus"
                                           placeholder="Bonus" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="hours">Tax (%)</label>
                                <input type="number" step="any" class="form-control" name="tax" id="tax"
                                       placeholder="Tax (%)" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hours">Draw Balance</label>
                                <input type="number" step="any" class="form-control" name="drawBalance" id="drawBalance"
                                       placeholder="Draw Balance" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="button" id="updatePayRollBtn"
                                onclick="UpdatePayPeriod();">Save
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="CancelPayPeriod();">Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>