<style rel="stylesheet">
    .select2-container {
        width: 100% !important;
    }
</style>

<div class="modal fade" id="assignLeadModal" tabindex="200" role="dialog" aria-labelledby="assignLeadModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="#" id="assignLeadModalForm">
                @csrf
                <input type="hidden" name="id" id="assignLeadId" value="0"/>
                <div class="modal-header">
                    <h5 class="modal-title" id="assignLeadModalLabel">Assign Lead</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="assignUsers" class="w-100">Users: <span
                                            class="RemoveAllUsers float-right badge badge-info ml-2"
                                            style="cursor: pointer;">Clear</span> <span
                                            class="SelectAllUsers float-right badge badge-info"
                                            style="cursor: pointer;">Select All</span></label>
                                <select multiple class="form-control _assignUsers" name="assignUsers[]" id="assignUsers"
                                        required>
                                    <?php
                                    $Users = array();
                                    if (\Illuminate\Support\Facades\Auth::user()->role_id == 1 || \Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                                        $Users = \Illuminate\Support\Facades\DB::table('users')
                                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                            ->where('users.status', '=', 1)
                                            ->where(function ($query) {
                                                $query->orWhereIn('users.role_id', array(3, 4, 5, 6, 9));
                                            })
                                            ->select('users.id', 'users.parent_id', 'profiles.firstname', 'profiles.lastname', 'profiles.state')
                                            ->get();
                                    } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                                        $Users = \Illuminate\Support\Facades\DB::table('users')
                                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                            ->where('users.status', '=', 1)
                                            ->where(function ($query) {
                                                $query->orWhereIn('users.role_id', array(5));
                                            })
                                            ->select('users.id', 'users.parent_id', 'profiles.firstname', 'profiles.lastname', 'profiles.state')
                                            ->get();
                                    } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                                        $Users = \Illuminate\Support\Facades\DB::table('users')
                                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                            ->where('users.status', '=', 1)
                                            ->where(function ($query) {
                                                $query->orWhereIn('users.role_id', array(6));
                                            })
                                            ->select('users.id', 'users.parent_id', 'profiles.firstname', 'profiles.lastname', 'profiles.state')
                                            ->get();
                                    } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
                                        $Users = \Illuminate\Support\Facades\DB::table('users')
                                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                            ->where('users.status', '=', 1)
                                            ->where(function ($query) {
                                                $query->orWhereIn('users.role_id', array(3));
                                            })
                                            ->select('users.id', 'users.parent_id', 'profiles.firstname', 'profiles.lastname', 'profiles.state')
                                            ->get();
                                    } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                                        $Users = \Illuminate\Support\Facades\DB::table('users')
                                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                            ->where('users.status', '=', 1)
                                            ->where(function ($query) {
                                                $query->orWhereIn('users.role_id', array(4));
                                            })
                                            ->select('users.id', 'users.parent_id', 'profiles.firstname', 'profiles.lastname', 'profiles.state')
                                            ->get();
                                    }
                                    ?>
                                    @foreach($Users as $user)
                                        @if (\Illuminate\Support\Facades\Auth::user()->role_id == 1)
                                            <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                        @elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2)
                                            <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                        @elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3)
                                            @if($user->parent_id == \Illuminate\Support\Facades\Auth::user()->id || $user->state == \App\Helpers\SiteHelper::GetCurrentUserState())
                                                <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                            @endif
                                        @elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4)
                                            @if($user->parent_id == \Illuminate\Support\Facades\Auth::user()->id || $user->state == \App\Helpers\SiteHelper::GetCurrentUserState())
                                                <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                            @endif
                                        @elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5)
                                            @if($user->state == \App\Helpers\SiteHelper::GetUserState(\Illuminate\Support\Facades\Auth::user()->id))
                                                <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                            @endif
                                        @elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6)
                                            @if($user->state == \App\Helpers\SiteHelper::GetUserState(\Illuminate\Support\Facades\Auth::user()->id))
                                                <option value="{{$user->id}}">{{$user->firstname . ' ' . $user->lastname}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" id="AssignSingleLead"
                            onclick="ConfirmAssignLeadToUser(this);">Save
                    </button>
                    <button class="btn btn-success" type="button" id="AssignMultipleLeads" style="display: none;"
                            onclick="AssignLeadsToUsers(this);">Save
                    </button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
