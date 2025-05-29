<div class="modal fade" id="deleteLeadModal" tabindex="200" role="dialog" aria-labelledby="deleteLeadModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="deleteLeadForm">
                @csrf
                <input type="hidden" name="id" id="deleteLeadId" value=""/>
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLeadModalLabel">Delete Lead</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" onclick="ConfirmDeleteLead();">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
