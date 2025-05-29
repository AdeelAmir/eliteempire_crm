<div class="modal fade" id="leadUpdateStatusModal" tabindex="200" role="dialog"
     aria-labelledby="leadUpdateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px; margin: 30px auto;" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="leadUpdateStatusForm">
                <input type="hidden" name="lead_update_status_id" id="_lead_update_status_id" value="0"/>
                <input type="hidden" name="lead_update_status_type" id="_lead_update_status_type"/>
                <div class="modal-header">
                    <h5 class="modal-title" id="leadUpdateStatusModalLabel">Lead Update Status</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <input type="hidden" name="team" id="team" value=""/>
                                        @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1 || \Illuminate\Support\Facades\Auth::user()->role_id == 2 || \Illuminate\Support\Facades\Auth::user()->role_id == 8 || \Illuminate\Support\Facades\Auth::user()->role_id == 9)
                                            <div class="col-md-6 mb-3 mt-3">
                                                <label for="lead_status_type" class="w-100">Lead Status Type</label>
                                                <select name="lead_status_type" id="lead_status_type" class="form-control"
                                                        onchange="checkLeadStatusType();" required>
                                                    <option value="" selected>Select Status</option>
                                                    <option value="Call Status">Cold Caller</option>
                                                    <option value="Offer Status">Acquisition</option>
                                                    <option value="Dispo Status">Disposition</option>
                                                </select>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-3 mt-3" id="dispoStatusSection" style="display:none;">
                                            <label for="dispo_lead_status" class="w-100">Lead Status*</label>
                                            <select name="dispo_lead_status" id="dispo_lead_status"
                                                    class="form-control" onchange="checkDispoLeadStatus();" required>
                                                <option value="" selected>Select Status</option>
                                                <option value="13">Send To Investor</option>
                                                <option value="14">Negotiation with Investors</option>
                                                <option value="15">Sent to Title</option>
                                                <option value="16">Send Contract to Investor</option>
                                                <option value="17">EMD Received</option>
                                                <option value="18">EMD Not Received</option>
                                                <option value="24">Inspection</option>
                                                <option value="25">Close On</option>
                                                <option value="21">Closed WON</option>
                                                <option value="22">Deal Lost</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="offerStatusSection" style="display:none;">
                                            <label for="offer_lead_status" class="w-100">Lead Status</label>
                                            <select name="offer_lead_status" id="offer_lead_status"
                                                    class="form-control" onchange="checkOfferLeadStatus();" required>
                                                <option value="" selected>Select Status</option>
                                                <option value="7">Offer Not Given</option>
                                                <option value="8">Offer Not Accepted</option>
                                                <option value="9">Accepted</option>
                                                <option value="10">Negotiating with Seller</option>
                                                <option value="11">Agreement Sent</option>
                                                <option value="12">Agreement Received</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="callStatusSection" style="display:none;">
                                            <label for="call_lead_status" class="w-100">Lead Status</label>
                                            <select name="call_lead_status" id="call_lead_status" class="form-control"
                                                    onchange="checkCallLeadStatus();" required>
                                                <option value="" selected>Select Status</option>
                                                <option value="1">Interested</option>
                                                <option value="2">Not Interested</option>
                                                <option value="4">Do Not Call</option>
                                                <option value="5">No Answer</option>
                                                <option value="23">Wrong Number</option>
                                            </select>
                                        </div>

                                        {{--Dispo Lead Status Block--}}
                                        <div class="col-md-6 mb-3 mt-3" id="sendToInvestor" style="display:none;">
                                            <label for="investorUsers" class="w-100">Investors</label>
                                            <select name="investorUsers" id="investorUsers" class="form-control" multiple>
                                                <?php
                                                $Users = array();
                                                if(\Illuminate\Support\Facades\Auth::user()->role_id == 6)
                                                {
                                                  $Users = \Illuminate\Support\Facades\DB::table('users')
                                                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                    ->whereIn('users.role_id', array(9, 10))
                                                    ->where('users.status', '=', 1)
                                                    ->where('users.parent_id', '=', \Illuminate\Support\Facades\Auth::user()->id)
                                                    ->where('users.deleted_at', '=', null)
                                                    ->select('users.id', 'profiles.firstname', 'profiles.lastname')
                                                    ->get();
                                                  foreach ($Users as $user){
                                                     echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                  }
                                                }
                                                else
                                                {
                                                  $Users = \Illuminate\Support\Facades\DB::table('users')
                                                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                    ->whereIn('users.role_id', array(9, 10))
                                                    ->where('users.status', '=', 1)
                                                    ->where('users.deleted_at', '=', null)
                                                    ->select('users.id', 'profiles.firstname', 'profiles.lastname')
                                                    ->get();
                                                  foreach ($Users as $user){
                                                     echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                  }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="sendToTitle" style="display:none;">
                                            <label for="companyUsers" class="w-100">Company</label>
                                            <select name="companyUsers" id="companyUsers" class="form-control">
                                                <?php
                                                $Users = \Illuminate\Support\Facades\DB::table('users')
                                                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                    ->where('users.role_id', '=', 11)
                                                    ->where('users.status', '=', 1)
                                                    ->where('users.deleted_at', '=', null)
                                                    ->select('users.id', 'profiles.firstname', 'profiles.lastname')
                                                    ->get();
                                                foreach ($Users as $user){
                                                    echo '<option value="' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="emdReceived" style="display:none;">
                                            <label for="emdAmount" class="w-100">Amount</label>
                                            <input type="number" step="any" name="emdAmount" id="emdAmount" value="0" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="emdNotReceived" style="display:none;">
                                            <label for="closingDays" class="w-100">Closing no. of Days</label>
                                            <input type="number" step="any" name="closingDays" id="closingDays" value="0" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="closedOn" style="display: none;">
                                            <label for="leadCloseOnDate" class="w-100">Close Date</label>
                                            <div class="input-group date leadCloseDate" data-date-format="mm/dd/yyyy"
                                                 data-link-field="leadCloseOnDate">
                                                <input class="form-control" size="16" type="text">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="leadCloseOnDate" name="leadCloseOnDate" />
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="closedWon" style="display: none;">
                                            <label for="leadCloseDate" class="w-100">Close Date</label>
                                            <div class="input-group date leadCloseDate" data-date-format="mm/dd/yyyy"
                                                 data-link-field="leadCloseDate">
                                                <input class="form-control" size="16" type="text" id="_leadCloseDate">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="leadCloseDate" name="leadCloseDate" />
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="closedWonCost" style="display: none;">
                                            <label for="closeWonCost" class="w-100">Cost</label>
                                            <input type="number" step="any" name="closeWonCost" id="closeWonCost" value="0" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="sendContracttoInvestorPurchaseAmount" style="display: none;">
                                            <label for="purchaseAmount" class="w-100">Purchase Amount</label>
                                            <input type="number" step="any" name="purchaseAmount" id="purchaseAmount" value="0" class="form-control" />
                                        </div>
                                        {{--Dispo Lead Status Block--}}

                                        {{--Offer Lead Status Block--}}
                                        <div class="col-md-6 mb-3 mt-3" id="agreementReceived" style="display:none;">
                                            <label for="contractAmount" class="w-100">Contract Amount</label>
                                            <input type="number" name="contractAmount" id="contractAmount" step="any" value="0" class="form-control" />
                                        </div>
                                        {{--Offer Lead Status Block--}}

                                        {{--Call Lead Status Block--}}
                                        <div class="col-md-12 mt-3" id="notInterested" style="display:none;">
                                            <label for="notInterestedComments" class="w-100">Comments</label>
                                            <textarea class="form-control" id="notInterestedComments"
                                                      name="notInterestedComments" rows="3"></textarea>
                                            <div class="mt-2" id="notInterestedCommentsMessage" style="color: red; display: none;">Field is required!</div>
                                        </div>
                                        <div class="col-md-12 mt-3" id="interested" style="display:none;">
                                            <label for="interestedComments" class="w-100">Comments</label>
                                            <textarea class="form-control" id="interestedComments"
                                                      name="interestedComments" rows="3"></textarea>
                                            <div class="mt-2" id="interestedCommentsMessage" style="color: red; display: none;">Field is required!</div>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3" id="_followUpBlock" style="display:none;">
                                            <label for="appointmenttime" class="w-100">Appointment Time</label>
                                            <div class="input-group date form_datetime"
                                                 data-date-format="mm/dd/yyyy - HH:ii p"
                                                 data-link-field="appointmenttime">
                                                <input class="form-control" size="16" type="text" value="">
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-th"></span></span>
                                            </div>
                                            <input type="hidden" id="appointmenttime" name="appointmenttime" value=""
                                                   required/>
                                        </div>
                                        <div class="col-md-6 mt-3" id="_inspectionPeriodBlock" style="display:none;">
                                            <label for="inspectionperiod" class="w-100">Inspection period</label>
                                            <select class="form-control" id="inspectionperiod" name="inspectionperiod" onchange="CheckInspectionPeriod(this.value);">
                                                <option value="Yes">Yes</option>
                                                <option value="No" selected>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mt-3" id="_inspectionNumberofDaysBlock" style="display:none;">
                                            <label for="inspection_numberofdays" class="w-100">Number of days</label>
                                            <input type="number" class="form-control" id="inspection_numberofdays" value="1" name="inspection_numberofdays" min="1">
                                        </div>
                                        <div class="col-md-12 mt-3" id="generalComments" style="display:none;">
                                            <label for="__comments" class="w-100">Comments</label>
                                            <textarea class="form-control" id="__comments"
                                                      name="__comments" rows="3"></textarea>
                                            <div class="mt-2" id="__commentsMessage" style="color: red; display: none;">Field is required!</div>
                                        </div>
                                        {{--Call Lead Status Block--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success float-right" type="button" onclick="UpdateLeadStatus();">Update
                    </button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $(".leadCloseDate").datetimepicker({
                minView: 2,
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                dateFormat: 'mm/dd/yyyy',
            });
        })
    </script>
@endpush
