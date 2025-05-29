<div class="modal fade" id="deleteTrainingRoomFolderModal" tabindex="200" role="dialog" aria-labelledby="deleteTrainingRoomFolderModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
              <form method="post" action="{{url('admin/training-room/folder/delete')}}" id="deleteTrainingRoomFolderForm">
                @csrf
                <input type="hidden" name="id" id="deleteTrainingRoomFolderId" value=""/>
                <input type="hidden" name="delete_training_room_role_id" id="delete_training_room_role_id" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTrainingRoomFolderModalLabel">Delete</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to delete it?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
