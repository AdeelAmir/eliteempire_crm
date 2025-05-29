<div class="modal fade" id="convertToLeadModal" tabindex="200" role="dialog" aria-labelledby="convertToLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            @if(Session::get('user_role') == 1)
            <form method="post" action="{{url('admin/call-request/convert')}}" id="convertCallRequestForm">
            @elseif(Session::get('user_role') == 2)
            <form method="post" action="{{url('general_manager/call-request/convert')}}" id="convertCallRequestForm">
            @elseif(Session::get('user_role') == 3)
            <form method="post" action="{{url('confirmationAgent/call-request/convert')}}" id="convertCallRequestForm">
            @endif
                @csrf
                <input type="hidden" name="convertCallRequestId" id="convertCallRequestId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="convertToLeadModalLabel">Convert to Lead</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to convert this Call Request to Lead?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Convert</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
