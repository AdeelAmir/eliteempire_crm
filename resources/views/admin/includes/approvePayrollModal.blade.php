<?php
$Url = "";
if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
    $Url = url('admin/payroll/submitted/approve');
}
elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2){
    $Url = url('global_manager/payroll/submitted/approve');
}
?>
<div class="modal fade" id="approvePayrollModal" tabindex="200" role="dialog" aria-labelledby="approvePayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="approvePayrollForm">
                @csrf
                <input type="hidden" name="id" id="generatePayPeriodId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="approvePayrollModalLabel">Generate Payroll</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to generate payroll for this sale?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Generate</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>