<div class="modal fade" id="leadUpdateAppointmentTimeModal" tabindex="200" role="dialog"
     aria-labelledby="leadUpdateAppointmentTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 500px;margin: 30px auto;" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="leadUpdateAppointmentTimeForm">
                <input type="hidden" name="leadUpdateAppointmentTimeId" id="leadUpdateAppointmentTimeId" value="0"/>
                <div class="modal-header">
                    <h5 class="modal-title" id="leadUpdateAppointmentTimeModalLabel">Add New Follow Up</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mt-3">
                                            <label for="_appointmentTime">Follow Up Time</label>
                                            <div class="input-group date form_datetime"
                                                 data-date-format="mm/dd/yyyy - HH:ii p"
                                                 data-link-field="_appointmentTime">
                                                <input class="form-control" size="16" type="text" value="">
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="_appointmentTime" name="_appointmentTime" value=""
                                                   required/>
                                        </div>
                                        <div class="col-md-12 mb-3 mt-3">
                                            <label for="appointmentTimeComments">Comments</label>
                                            <textarea class="form-control" id="appointmentTimeComments"
                                                      name="appointmentTimeComments" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success float-right" type="button" onclick="LeadUpdateAppointmentTime();">
                        Update
                    </button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
