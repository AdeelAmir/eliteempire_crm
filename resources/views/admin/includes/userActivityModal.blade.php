<style media="screen">
  @media only screen and (min-width:1025px) {
    #user_activities_table_filter{
      text-align: right;
      margin-right: 142px;
    }
  }
  @media only screen and (max-width: 767px) {
    #user_activities_table_filter{
      text-align: left;
      margin-left: -11px;
    }
  }
</style>
<div class="modal fade" id="userActivityModal" tabindex="200" role="dialog" aria-labelledby="userActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;margin: 30px auto;" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="userActivityForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userActivityModalLabel">User Activities</h5>
                </div>
                <div class="modal-body">
                    <div class="row mt-3">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        Details
                                    </h6>
                                    <div class="table-responsive">
                                        <table id="user_activities_table" class="table w-100">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 5%;">User</th>
                                                    <th style="width: 90%;">Message</th>
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
