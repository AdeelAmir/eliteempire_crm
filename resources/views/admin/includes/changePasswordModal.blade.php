<div class="modal fade" id="changePasswordModal" tabindex="200" role="dialog" aria-labelledby="changePasswordModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/user/changePassword');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/user/changePassword');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
                $Url = url('disposition_representative/user/changePassword');
            }
            ?>
            <form method="post" action="{{$Url}}" id="changePasswordForm">
                @csrf
                <input type="hidden" name="user_id" id="user_id" value="0"/>
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" class="form-control" minlength="8" name="newPassword" id="newPassword"
                                       placeholder="New Password" required/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="confirmPassword" style="width: 100%;">Confirm Password</label>
                                <input type="password" class="form-control" minlength="8" name="confirmPassword" id="confirmPassword"
                                       placeholder="New Password" required/>
                                <span id="changePasswordError"
                                      class="text-small text-danger">Passwords not matched</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
