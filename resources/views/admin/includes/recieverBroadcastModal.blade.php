<!-- Broadcast Modal Window - Start -->
<div class="modal fade" id="recieverBroadcastModal" tabindex="200" role="dialog" aria-labelledby="recieverBroadcastModalLabel"
     aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <input type="hidden" name="reciever_id" id="recieverBroadcastUserId" value="" />
            <input type="hidden" name="broadcast_id" id="recieverBroadcastId" value="" />
            <input type="hidden" name="broadcast_id" id="recieverReadBroadcastId" value="" />
            <div class="modal-header">
                <h5 class="modal-title" id="recieverBroadcastModalLabel">Broadcast</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <p id="recieverBroadcast_message"></p>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success w-25" type="button" onclick="UpdateBroadcastReadStatus();">I got it</button>
            </div>
        </div>
    </div>
</div>
<!-- Broadcast Modal Window - End -->
