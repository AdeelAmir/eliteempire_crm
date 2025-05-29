<div class="modal fade" id="deleteExpensModal" tabindex="200" role="dialog" aria-labelledby="deleteExpenseModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/delete/expenses');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/delete/expenses');
            }
            ?>
            <form method="post" action="{{$Url}}" id="deleteUserForm">
                @csrf
                <input type="hidden" name="id" id="deleteExpenseId" value=""/>
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExpenseModalLabel">Delete Expenses</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to delete this Expenses?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>