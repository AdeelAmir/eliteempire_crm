<div class="modal fade" id="leadHistoryNotesModal" tabindex="200" role="dialog" aria-labelledby="leadHistoryNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;margin: 30px auto;" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="leadHistoryNotesForm">
                <input type="hidden" name="lead_history_note_id" id="_lead_history_note_id" value="0" />
                <div class="modal-header">
                    <h5 class="modal-title" id="leadHistoryNotesModalLabel">Lead Comments</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="_lead_history_note">Comments</label>
                                <textarea name="lead_history_note" id="_lead_history_note" rows="5" cols="80" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <button class="btn btn-success float-right" type="button" onclick="SaveLeadHistoryDashboardNote();">Add</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        Lead Comments
                                    </h6>
                                    <div class="table-responsive">
                                        <table id="lead_historynotesdashboard_table" class="table w-100">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 10%;">User</th>
                                                    <th style="width: 85%;">Comment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
