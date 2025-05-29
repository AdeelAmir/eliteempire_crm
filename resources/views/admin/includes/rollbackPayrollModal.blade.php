<?php
$Url = "";
if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
    $Url = url('admin/payroll/submitted/rollback');
}
elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2){
    $Url = url('global_manager/payroll/submitted/rollback');
}
?>
<div class="modal fade" id="rollbackPayrollModal" tabindex="200" role="dialog" aria-labelledby="rollbackPayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="rollbackPayrollForm">
                @csrf
                <input type="hidden" name="id" id="rollbackSaleId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="rollbackPayrollModalLabel">Rollback Payroll</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to rollback payroll for this sale?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Rollback</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>