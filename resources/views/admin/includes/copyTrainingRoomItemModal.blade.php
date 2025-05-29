<div class="modal fade" id="copyTrainingRoomItemModal" tabindex="200" role="dialog" aria-labelledby="copyTrainingRoomItemModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{url('admin/copy/training-room')}}" id="copyTrainingRoomItemForm">
                @csrf
                <input type="hidden" name="id" id="trainingRoomItemId" value="" />
                <input type="hidden" name="role" id="copy_training_room_role_id" value="" />
                <input type="hidden" name="folder_id" id="copy_training_room_folder_id" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="copyTrainingRoomItemModalLabel">Copy</h5>
                </div>
                <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12 mb-3">
                          <label for="firstname">Role</label>
                          <select class="form-control" name="role" id="_role" onchange="GetFolders(this.value);" required>
                            <option value="">Select Role</option>
                            <option value="3">Acquisition Manager</option>
                            <option value="4">Disposition Manager</option>
                            <option value="5">Acquisition Representative</option>
                            <option value="6">Disposition Representative</option>
                            <option value="7">Cold Caller</option>
                            <option value="8">Affiliate</option>
                          </select>
                      </div>
                      <div class="col-md-12">
                          <label for="copy_folder">Role</label>
                          <select class="form-control" name="folder" id="copy_folder" required>
                            <option value="">Select Folder</option>
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
