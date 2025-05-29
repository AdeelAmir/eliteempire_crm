<?php
$Url = "";
if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
    if($page == 'submittedPayroll'){
        $Url = url('admin/payroll/submitted/reject');
    }
    else{
        $Url = url('admin/payroll/reject');
    }
}
else{
    $Url = url('manager/payroll/reject');
}
?>
<div class="modal fade" id="rejectPayrollModal" tabindex="200" role="dialog" aria-labelledby="rejectPayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="rejectPayrollForm">
                @csrf
                <input type="hidden" name="id" id="rejectPayrollId" value="" />
                <input type="hidden" name="startDate" id="rejectStartDate" value="" />
                <input type="hidden" name="endDate" id="rejectEndDate" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectPayrollModalLabel">Reject</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to reject this user payroll?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" id="rejectPayrollBtn" onclick="RejectPayroll();">Reject</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>