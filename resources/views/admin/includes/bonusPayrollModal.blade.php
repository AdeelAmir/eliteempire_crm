<?php
$Url = "";
if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
    $Url = url('admin/payroll/bonus');
} else {
    $Url = url('manager/payroll/bonus');
}
?>
<div class="modal fade" id="bonusPayrollModal" tabindex="200" role="dialog" aria-labelledby="bonusPayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="bonusPayrollForm">
                @csrf
                <input type="hidden" name="id" id="bonusPayrollId" value=""/>
                <div class="modal-header">
                    <h5 class="modal-title" id="bonusPayrollModalLabel">Bonus</h5>
                </div>
                <div class="modal-body">
                    <input type="number" step="any" class="form-control" name="bonus" id="bonusAmount"
                           placeholder="Enter Bonus Amount" required/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Add</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
