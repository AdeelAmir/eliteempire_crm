<style media="screen">
  .errorDiv{
    color: red;
    font-size: 12px;
  }
</style>
<div class="modal fade" id="addNewGroupModal" tabindex="200" role="dialog" aria-labelledby="addNewGroupModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{url('add/new/group')}}" id="addNewGroupForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewGroupModalLabel">Add New Group</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_group_name">Group Name</label>
                        <input type="text" name="add_group_name" id="add_group_name" class="form-control" onkeypress="VerifyTextField(event, this.id);" onkeyup="VerifyTextField(event, this.id);HandleChange('errorGroupName');" required />
                        <div class="mt-2 errorDiv" id="errorGroupName" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="add_group_name">Members</label>
                        <select class="form-control" name="add_group_members[]" id="add_group_members" onchange="HandleChange('errorGroupMember');" multiple required>
                          @foreach($TeamMembers as $member)
                          @php
                          $FullName = "";
                          @endphp
                          @if($member->middlename != "")
                            @php $FullName .= $member->firstname . " " . $member->middlename . " " . $member->lastname; @endphp
                          @else
                            @php $FullName .= $member->firstname . " " . $member->lastname; @endphp
                          @endif
                          <option value="{{$member->id}}">{{$FullName}}</option>
                          @endforeach
                        </select>
                        <div class="mt-2 errorDiv" id="errorGroupMember" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" onclick="CreateGroup();">Create</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
