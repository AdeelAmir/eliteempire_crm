<div class="modal fade" id="deleteBuissnessAccountModal" tabindex="200" role="dialog" aria-labelledby="deleteBuissnessAccountModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{url('admin/delete/buissness_account')}}" id="deleteBuissnessAccountForm">
                @csrf
                <input type="hidden" name="id" id="deleteBuissnessAccountId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Delete Buissness Account</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to delete this buissness account?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
