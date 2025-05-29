<div class="modal fade" id="deleteTrainingRoomModal" tabindex="200" role="dialog" aria-labelledby="deleteTrainingRoomModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
              <form method="post" action="{{url('admin/training-room/delete')}}" id="deleteTrainingRoomForm">
                @csrf
                <input type="hidden" name="id" id="deleteTrainingRoomId" value=""/>
                <input type="hidden" name="delete_training_room_role_id" id="delete_training_room_role_id" value="" />
                <input type="hidden" name="delete_training_room_folder_id" id="delete_training_room_folder_id" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTrainingRoomModalLabel">Delete</h5>
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
