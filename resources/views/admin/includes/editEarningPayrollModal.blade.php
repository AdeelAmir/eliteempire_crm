<?php
$Url = "";
if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
    $Url = url('admin/payroll/edit/earning');
} else {
    $Url = url('manager/payroll/edit/earning');
}
?>
<div class="modal fade" id="editEarningPayrollModal" tabindex="200" role="dialog"
     aria-labelledby="editEarningPayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="editEarningPayrollModalForm">
                @csrf
                <input type="hidden" name="id" id="editEarningPayrollId" value=""/>
                <div class="modal-header">
                    <h5 class="modal-title" id="editEarningPayrollModalLabel">Edit Earning</h5>
                </div>
                <div class="modal-body">
                    <input type="number" class="form-control" name="editEarningAmount" id="editEarningAmount"
                           placeholder="Enter Earning Amount" step="any" min="0" required/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Add</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>