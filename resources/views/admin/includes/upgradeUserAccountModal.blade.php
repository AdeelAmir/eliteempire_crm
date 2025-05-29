<div class="modal fade" id="upgradeUserAccountModal" tabindex="200" role="dialog" aria-labelledby="upgradeUserAccountModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/user/upgrade/account');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/user/upgrade/account');
            }
            ?>
            <input type="hidden" name="upgradeSelectedUsersFormUrl" id="upgradeSelectedUsersFormUrl" value="{{$Url}}"/>
            <input type="hidden" name="id" id="upgradeUserAccountId" value=""/>
            <div class="modal-header">
                <h5 class="modal-title" id="upgradeUserAccountModalLabel">Upgrade Account</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to turned off traning room for selected users?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Yes</button>
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
