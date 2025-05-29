<style media="screen">
  .errorDiv{
    color: red;
    font-size: 12px;
  }
</style>
<div class="modal fade" id="editGroupModal" tabindex="200" role="dialog" aria-labelledby="editGroupModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="addNewGroupForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalLabel">Edit Group</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_group_name">Group Name</label>
                        <input type="text" name="edit_group_name" id="edit_group_name" class="form-control" onkeypress="VerifyTextField(event, this.id);" onkeyup="VerifyTextField(event, this.id);HandleChange('errorEditGroupName');" required />
                        <div class="mt-2 errorDiv" id="errorEditGroupName" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="add_group_name">Members</label>
                        <select class="form-control" name="edit_group_members[]" id="edit_group_members" onchange="HandleChange('errorEditGroupMember');" multiple required>

                        </select>
                        <div class="mt-2 errorDiv" id="errorEditGroupMember" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" onclick="UpdateGroup();">Update</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
