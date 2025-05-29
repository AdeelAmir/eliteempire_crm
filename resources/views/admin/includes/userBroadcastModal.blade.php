<div class="modal fade" id="userBroadcastModal" tabindex="200" role="dialog" aria-labelledby="userBroadcastModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $Url = '';
            if(Session::get('user_role') == 1){
              $Url = url('admin/broadcast/send');
            } elseif(Session::get('user_role') == 2){
              $Url = url('global_manager/broadcast/send');
            } elseif(Session::get('user_role') == 3){
              $Url = url('acquisition_manager/broadcast/send');
            } elseif(Session::get('user_role') == 4){
              $Url = url('disposition_manager/broadcast/send');
            }
            ?>
            <input type="hidden" name="broadcastSelectedUsersFormUrl" id="broadcastSelectedUsersFormUrl" value="{{$Url}}"/>
            <input type="hidden" name="id" id="sendBroadcastUserId" value="" />
            <div class="modal-header">
                <h5 class="modal-title" id="sendBroadcastModalLabel">Send Broadcast</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <label for="broadcast_message">Message</label>
                    <textarea class="form-control" name="broadcast_message" id="broadcast_message" rows="5"></textarea>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Yes</button>
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
