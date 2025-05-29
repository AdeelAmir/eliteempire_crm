<div class="modal fade" id="buisnessAccountTypeModal" tabindex="200" role="dialog" aria-labelledby="buisnessAccountTypeModalLabel"
     aria-hidden="true" style="margin-top: 10%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buisnessAccountTypeModalLabel">Account Type</h5>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_investor_btn" id="add_investor_btn"
                                onclick="window.location.href='{{url('admin/investor/add')}}';" style="width: 70%;">Investor</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_titlecompany_btn" id="add_titlecompany_btn"
                                onclick="window.location.href='{{url('admin/title_company/add')}}';" style="width: 70%;">Title Company</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_realtor_btn" id="add_realtor_btn"
                                onclick="window.location.href='{{url('admin/realtor/add')}}';" style="width: 70%;">Realtor</button>
                    @elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2)
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_investor_btn" id="add_investor_btn"
                                onclick="window.location.href='{{url('global_manager/investor/add')}}';" style="width: 70%;">Investor</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_titlecompany_btn" id="add_titlecompany_btn"
                                onclick="window.location.href='{{url('global_manager/title_company/add')}}';" style="width: 70%;">Title Company</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_realtor_btn" id="add_realtor_btn"
                                onclick="window.location.href='{{url('global_manager/realtor/add')}}';" style="width: 70%;">Realtor</button>
                    @elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6)
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_investor_btn" id="add_investor_btn"
                                onclick="window.location.href='{{url('disposition_representative/investor/add')}}';" style="width: 70%;">Investor</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_titlecompany_btn" id="add_titlecompany_btn"
                                onclick="window.location.href='{{url('disposition_representative/title_company/add')}}';" style="width: 70%;">Title Company</button>
                        <br>
                        <br>
                        <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_realtor_btn" id="add_realtor_btn"
                                onclick="window.location.href='{{url('disposition_representative/realtor/add')}}';" style="width: 70%;">Realtor</button>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>