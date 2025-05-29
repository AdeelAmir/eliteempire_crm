<div class="modal fade" id="leadTaskUpdateStatusModal" tabindex="200" role="dialog" aria-labelledby="leadTaskUpdateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="leadTaskUpdateStatusForm">
                <input type="hidden" name="lead_update_status_id" id="_lead_update_status_id" value="0" />
                <input type="hidden" name="lead_update_status_field_index" id="_lead_update_status_field_index" />
                <div class="modal-header">
                    <h5 class="modal-title" id="leadUpdateStatusModalLabel">Lead Update Status</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12 grid-margin stretch-card">
                          <div class="card">
                              <div class="card-body">
                                  <div class="row">
                                    <input type="hidden" name="team" id="team" value="" />
                                    <div class="col-md-4 mb-3 mt-3">
                                        <label for="lead_status">Lead Status*</label>
                                        <select name="lead_status" id="lead_status" class="form-control" onchange="checkLeadStatus();" required>
                                          <option value="" selected>Select Status</option>
                                          <option value="1">Confirmed</option>
                                          <option value="2">Cancelled</option>
                                          <option value="3">Pending</option>
                                          <option value="6">Out of Coverage Area</option>
                                          <option value="7">Not Interested</option>
                                          <option value="8">Demo</option>
                                          <option value="9">1 Legger</option>
                                          <option value="10">Not Home</option>
                                          <option value="11">Pending Sales</option>
                                      </select>
                                    </div>
                                    <div class="col-md-4 mb-3 mt-3" id="_contractAmountBlock" style="display:none;">
                                      <label for="lead_company">Contract Amount</label>
                                      <input type="number" name="salesContractAmount" id="_salesContractAmount" value="0" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3 mt-3" id="_cancellationReasonBlock" style="display:none;">
                                      <label for="cancellation_reason">Cancellation Reason (Mandatory)</label>
                                      <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3"></textarea>
                                      <div class="pt-2" id="cancellation_reason_error" style="color:red;font-size:14px;"></div>
                                    </div>
                                    <div class="col-md-12 mb-3 mt-3" id="_confirmationReasonBlock" style="display:none;">
                                        <label for="confirmation_reason">Confirmation Reason</label>
                                        <textarea class="form-control" id="confirmation_reason" name="confirmation_reason" rows="3"></textarea>
                                    </div>
                                  </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success float-right" type="button" onclick="UpdateTaskLeadStatus();">Update</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
