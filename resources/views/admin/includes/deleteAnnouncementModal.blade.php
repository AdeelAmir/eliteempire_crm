<div class="modal fade" id="deleteAnnouncementModal" tabindex="200" role="dialog" aria-labelledby="deleteAnnouncementModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            @if(Session::get('user_role') == 1)
            <form method="post" action="{{url('admin/delete/announcement')}}" id="deleteAnnouncementForm">
            @endif
                @csrf
                <input type="hidden" name="id" id="deleteAnnouncementId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAnnouncementModalLabel">Delete Announcement</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to delete this announcement?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
