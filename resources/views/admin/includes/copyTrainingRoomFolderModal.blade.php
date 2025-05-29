<div class="modal fade" id="copyTrainingRoomFolderModal" tabindex="200" role="dialog" aria-labelledby="copyTrainingRoomFolderModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{url('admin/training-room/folder/copy')}}" id="copyTrainingRoomFolderForm">
                @csrf
                <input type="hidden" name="id" id="trainingRoomFolderId" value="" />
                <input type="hidden" name="copy_training_room_role_id" id="copy_training_room_role_id" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="copyTrainingRoomFolderModalLabel">Copy</h5>
                </div>
                <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12 mb-3">
                          <label for="firstname">Role</label>
                          <select class="form-control" name="role" id="_role" required>
                            <option value="">Select Role</option>
                            <option value="3">Acquisition Manager</option>
                            <option value="4">Disposition Manager</option>
                            <option value="5">Acquisition Representative</option>
                            <option value="6">Disposition Representative</option>
                            <option value="7">Cold Caller</option>
                            <option value="8">Affiliate</option>
                          </select>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Submit</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
