<style media="screen">
    .navbar .navbar-content .navbar-nav .nav-item .nav-link .indicator {
        position: absolute;
        top: -7px;
        right: 0;
        background-color: red;
        border-radius: 4em;
        padding: 0 4px 0;
        color: white;
        font-size: 10px;
    }
</style>
<nav class="navbar">
    <a href="#" class="sidebar-toggler" id="toggleSidebar" style="visibility: hidden;">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <ul class="navbar-nav">
            @if(Illuminate\Support\Facades\Session::get('user_role') == 1)
                <li class="nav-item dropdown nav-notifications">
                    <a class="nav-link" href="{{url('admin/lead/add')}}" id="addNewLeadNavBar" role="button"
                       data-toggle="tooltip" data-placement="bottom" title="New Lead">
                        <i data-feather="plus"></i>
                    </a>
                </li>
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 2)
                <?php
                $UserProgress = 0;
                $TotalAssignedLeads = \Illuminate\Support\Facades\DB::table('lead_assignments')
                    ->where('user_id', Auth::id())
                    ->count();

                if ($TotalAssignedLeads > 0) {
                    // Total User Completed Leads
                    $TotalCompletedAssignedLeads = \Illuminate\Support\Facades\DB::table('lead_assignments')
                        ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('status', '=', 1)
                        ->count();

                    $UserProgress = (($TotalCompletedAssignedLeads / $TotalAssignedLeads) * 100);
                    $UserProgress = bcdiv($UserProgress, 1, 0) . "%";
                } else {
                    $UserProgress = bcdiv($UserProgress, 1, 0) . "%";
                }

                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                <li class="nav-item dropdown nav-notifications">
                    <a class="nav-link" href="{{url('global_manager/lead/add')}}" id="addNewLeadNavBar" role="button"
                       data-toggle="tooltip" data-placement="bottom" title="New Lead">
                        <i data-feather="plus"></i>
                    </a>
                </li>
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 3)
                <?php
                $UserProgress = 0;
                $TotalAssignedLeads = \Illuminate\Support\Facades\DB::table('lead_assignments')
                    ->where('user_id', Auth::id())
                    ->count();

                if ($TotalAssignedLeads > 0) {
                    // Total User Completed Leads
                    $TotalCompletedAssignedLeads = \Illuminate\Support\Facades\DB::table('lead_assignments')
                        ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('status', '=', 1)
                        ->count();

                    $UserProgress = (($TotalCompletedAssignedLeads / $TotalAssignedLeads) * 100);
                    $UserProgress = bcdiv($UserProgress, 1, 0) . "%";
                } else {
                    $UserProgress = bcdiv($UserProgress, 1, 0) . "%";
                }

                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                <li class="nav-item dropdown nav-notifications">
                    <a class="nav-link" href="{{url('acquisition_manager/lead/add')}}" id="addNewLeadNavBar"
                       role="button" data-toggle="tooltip" data-placement="bottom" title="New Lead">
                        <i data-feather="plus"></i>
                    </a>
                </li>
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 4)
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                <li class="nav-item dropdown nav-notifications">
                    <a class="nav-link" href="{{url('disposition_manager/lead/add')}}" id="addNewLeadNavBar"
                       role="button" data-toggle="tooltip" data-placement="bottom" title="New Lead">
                        <i data-feather="plus"></i>
                    </a>
                </li>
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 5)
                <?php
                $TrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
                    ->where('user_id', Auth::id())
                    ->where('status', 0)
                    ->count();
                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                @if($TrainingAssignment == 0)
                    <li class="nav-item dropdown nav-notifications">
                        <a class="nav-link" href="{{url('acquisition_representative/lead/add')}}" id="addNewLeadNavBar"
                           role="button" data-toggle="tooltip" data-placement="bottom" title="New Lead">
                            <i data-feather="plus"></i>
                        </a>
                    </li>
                @endif
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 6)
                <?php
                $TrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
                    ->where('user_id', Auth::id())
                    ->where('status', 0)
                    ->count();
                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                @if($TrainingAssignment == 0)
                    <li class="nav-item dropdown nav-notifications">
                        <a class="nav-link" href="{{url('disposition_representative/lead/add')}}" id="addNewLeadNavBar"
                           role="button" data-toggle="tooltip" data-placement="bottom" title="New Lead">
                            <i data-feather="plus"></i>
                        </a>
                    </li>
                @endif
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 7)
                <?php
                $TrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
                    ->where('user_id', Auth::id())
                    ->where('status', 0)
                    ->count();
                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                @if($TrainingAssignment == 0)
                    <li class="nav-item dropdown nav-notifications">
                        <a class="nav-link" href="{{url('cold_caller/lead/add')}}" id="addNewLeadNavBar" role="button"
                           data-toggle="tooltip" data-placement="bottom" title="New Lead">
                            <i data-feather="plus"></i>
                        </a>
                    </li>
                @endif
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 8)
                <?php
                $TrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
                    ->where('user_id', Auth::id())
                    ->where('status', 0)
                    ->count();
                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                @if($TrainingAssignment == 0)
                    <li class="nav-item dropdown nav-notifications">
                        <a class="nav-link" href="{{url('affiliate/lead/add')}}" id="addNewLeadNavBar" role="button"
                           data-toggle="tooltip" data-placement="bottom" title="New Lead">
                            <i data-feather="plus"></i>
                        </a>
                    </li>
                @endif
            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 9)
                <?php
                $TrainingAssignment = \Illuminate\Support\Facades\DB::table('training_assignments')
                    ->where('user_id', Auth::id())
                    ->where('status', 0)
                    ->count();
                ?>
                <li class="nav-item dropdown nav-notifications">
                    <span><span {{--id="totalCompletedProgress"--}}><b><?php echo \App\Helpers\SiteHelper::GetCurrentUserState(); ?></b></span></span>
                </li>
                @if($TrainingAssignment == 0)
                    <li class="nav-item dropdown nav-notifications">
                        <a class="nav-link" href="{{url('realtor/lead/add')}}" id="addNewLeadNavBar" role="button"
                           data-toggle="tooltip" data-placement="bottom" title="New Lead">
                            <i data-feather="plus"></i>
                        </a>
                    </li>
                @endif
            @endif

            <li class="nav-item dropdown nav-notifications">
                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="bell"></i>
                    <div class="indicator" id="notification-indicator" style="background-color: #4fd36d;">
                        <!-- <div class="circle"></div> -->
                        0
                    </div>
                </a>
                <div class="dropdown-menu" aria-labelledby="notificationDropdown">
                    <div class="dropdown-header d-flex align-items-center justify-content-between">
                        <p class="mb-0 font-weight-medium" id="totalnewnotification">0 New Notifications</p>
                        <a href="javascript:void(0);" onclick="ClearAllNotification();" class="text-muted">Clear all</a>
                    </div>
                    <div class="dropdown-body" id="notification-dropdown-body"
                         style="overflow-x: hidden;overflow-y:scroll;">
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="icon">
                                <i class="far fa-bell"></i>
                            </div>
                            <div class="content">
                                <p>We have zero notification</p>
                                <p class="sub-text text-muted">0 min ago</p>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-footer d-flex align-items-center justify-content-center">
                        <a href="{{url('notification/load/all')}}">View all</a>
                    </div>
                </div>
            </li>

            <!-- Messaging Icon -->
            <?php
            $Url = "";
            if (Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 3) {
                $Url = url('acquisition_manager/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 4) {
                $Url = url('disposition_manager/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 5) {
                $Url = url('acquisition_representative/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 6) {
                $Url = url('disposition_representative/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 7) {
                $Url = url('cold_caller/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 8) {
                $Url = url('affiliate/messaging');
            } elseif (Illuminate\Support\Facades\Session::get('user_role') == 9) {
                $Url = url('realtor/messaging');
            }
            ?>
            <li class="nav-item dropdown nav-notifications">
                <a class="nav-link" href="{{$Url}}" id="addNewLeadNavBar"
                   role="button" data-toggle="tooltip" data-placement="bottom" title="Messenger">
                    <i data-feather="message-circle"></i>
                    <div class="indicator" id="message-indicator" style="background-color: #4fd36d;">
                        0
                    </div>
                </a>
            </li>
            <!-- Messaging Icon -->
            @php
                $Profile = \Illuminate\Support\Facades\DB::table('profiles')->where('user_id', '=', \Illuminate\Support\Facades\Auth::id())->get();
                $ProfilePic = asset('public/storage/profile-pics/admin_12345.jpg');
                if($Profile[0]->profile_picture != null){
                    $ProfilePic = asset('public/storage/profile-pics') . '/' . $Profile[0]->profile_picture;
                }
                $Name = $Profile[0]->firstname . " " . $Profile[0]->lastname;
            @endphp
            <li class="nav-item dropdown nav-profile">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{$ProfilePic}}" alt="profile">
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    <div class="dropdown-header d-flex flex-column align-items-center">
                        <div class="figure mb-3">
                            <img src="{{$ProfilePic}}" alt="">
                        </div>
                        <div class="info text-center">
                            <p class="name font-weight-bold mb-0">{{$Name}}</p>
                            <p class="email text-muted mb-3">{{\Illuminate\Support\Facades\Auth::user()->email}}</p>
                        </div>
                    </div>
                    <div class="dropdown-body">
                        <ul class="profile-nav p-0 pt-3">
                            @if(Illuminate\Support\Facades\Session::get('user_role') == 1)
                                <li class="nav-item">
                                    <a href="{{url('admin/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 2)
                                <li class="nav-item">
                                    <a href="{{url('global_manager/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 3)
                                <li class="nav-item">
                                    <a href="{{url('acquisition_manager/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 4)
                                <li class="nav-item">
                                    <a href="{{url('disposition_manager/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 5)
                                <li class="nav-item">
                                    <a href="{{url('acquisition_representative/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 6)
                                <li class="nav-item">
                                    <a href="{{url('disposition_representative/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 7)
                                <li class="nav-item">
                                    <a href="{{url('cold_caller/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 8)
                                <li class="nav-item">
                                    <a href="{{url('affiliate/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @elseif(Illuminate\Support\Facades\Session::get('user_role') == 9)
                                <li class="nav-item">
                                    <a href="{{url('realtor/edit-profile')}}" class="nav-link">
                                        <i data-feather="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i data-feather="log-out"></i>
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>