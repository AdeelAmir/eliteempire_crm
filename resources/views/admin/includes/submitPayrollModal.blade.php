<?php
$Url = "";
if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
    $Url = url('admin/payroll/submit');
}
else{
    $Url = url('manager/payroll/submit');
}
?>
<div class="modal fade" id="submitPayrollModal" tabindex="200" role="dialog" aria-labelledby="submitPayrollModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{$Url}}" id="submitPayrollForm">
                @csrf
                <input type="hidden" name="id" id="submitPayrollId" value="" />
                <input type="hidden" name="startDate" id="submitStartDate" value="" />
                <input type="hidden" name="endDate" id="submitEndDate" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="submitPayrollModalLabel">Submit</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to submit this user payroll?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" id="submitPayrollBtn" onclick="SubmitPayroll();">Submit</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>