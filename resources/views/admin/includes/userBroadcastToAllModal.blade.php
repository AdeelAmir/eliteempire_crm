<div class="modal fade" id="userBroadcastToAllModal" tabindex="200" role="dialog" aria-labelledby="userBroadcastToAllModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            @if(Session::get('user_role') == 1)
            <form method="post" action="{{url('admin/broadcast/all/send')}}" id="sendBroadcastForm">
            @elseif(Session::get('user_role') == 2)
            <form method="post" action="{{url('global_manager/broadcast/all/send')}}" id="sendBroadcastForm">
            @elseif(Session::get('user_role') == 3)
            <form method="post" action="{{url('acquisition_manager/broadcast/all/send')}}" id="sendBroadcastForm">
            @elseif(Session::get('user_role') == 4)
            <form method="post" action="{{url('disposition_manager/broadcast/all/send')}}" id="sendBroadcastForm">
            @endif
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="sendBroadcastToAllModalLabel">Send Broadcast</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12">
                        <label for="broadcast_to_all_message">Message</label>
                        <textarea class="form-control" name="broadcast_message" id="broadcast_to_all_message" rows="5"></textarea>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Send</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
