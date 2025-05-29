<div class="modal fade" id="deleteTeamModal" tabindex="200" role="dialog" aria-labelledby="deleteTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            @if(Session::get('user_role') == 1)
            <form method="post" action="{{url('admin/teams/delete')}}" id="deleteTeamForm">
            @elseif(Session::get('user_role') == 2)
            <form method="post" action="{{url('general_manager/teams/delete')}}" id="deleteTeamForm">
            @endif
                @csrf
                <input type="hidden" name="id" id="deleteTeamId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTeamModalLabel">Delete Team</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to delete this Team?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
