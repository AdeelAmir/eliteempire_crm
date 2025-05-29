<div class="modal fade" id="deleteUserModal" tabindex="200" role="dialog" aria-labelledby="deleteUserModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/delete/user');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/delete/user');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
                $Url = url('disposition_representative/delete/user');
            }
            ?>
            <input type="hidden" name="deleteSelectedUsersFormUrl" id="deleteSelectedUsersFormUrl" value="{{$Url}}"/>
            <input type="hidden" name="id" id="deleteUserId" value=""/>
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
            </div>
            <div class="modal-body">
                <p>Sure you want to delete this user?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="submit">Delete</button>
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
