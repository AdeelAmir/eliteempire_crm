<div class="modal fade" id="userBanModal" tabindex="200" role="dialog" aria-labelledby="userBanModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/user-ban');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/user-ban');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
                $Url = url('disposition_representative/user-ban');
            }
            ?>
            <form method="post" action="{{$Url}}" id="userBanForm">
                @csrf
                <input type="hidden" name="UserId" id="banUserId" value=""/>
                <div class="modal-header">
                    <h5 class="modal-title" id="userBanModalLabel">Ban User</h5>
                </div>
                <div class="modal-body">
                    <label for="ban_reason">Reason</label>
                    <textarea class="form-control" name="ban_reason" id="ban_reason" required></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Ban</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>