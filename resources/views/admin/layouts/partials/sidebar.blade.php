<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <img src="{{ asset('public/storage/logo/logo.png')}}" alt="logo-small" style="width: 150px; height: 30px;">
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            {{-- Admin User --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 1)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/admin/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>
                @if($page == 'users')
                    <li class="nav-item active">
                        <a href="{{url('/admin/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @endif

                @if($page == 'buissness_account')
                    <li class="nav-item active">
                        <a href="{{url('/admin/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @endif

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/admin/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="{{url('/admin/coverage-area')}}" class="nav-link">
                        <i class="link-icon" data-feather="triangle"></i>
                        <span class="link-title">Zoning</span>
                    </a>
                </li>

                @if($page == 'kpi')
                    <li class="nav-item active">
                        <a href="{{url('/admin/kpi')}}" class="nav-link">
                            <i class="link-icon" data-feather="pie-chart"></i>
                            <span class="link-title">K.P.I</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/kpi')}}" class="nav-link">
                            <i class="link-icon" data-feather="pie-chart"></i>
                            <span class="link-title">K.P.I</span>
                        </a>
                    </li>
                @endif

                @if($page == 'users_report')
                    <li class="nav-item active">
                        <a href="{{url('/admin/users-report')}}" class="nav-link">
                            <i class="link-icon fa fa-file-contract"></i>
                            <span class="link-title">Users Report</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/users-report')}}" class="nav-link">
                            <i class="link-icon fa fa-file-contract"></i>
                            <span class="link-title">Users Report</span>
                        </a>
                    </li>
                @endif

                @if($page == 'expense')
                    <li class="nav-item active">
                        <a href="{{url('/admin/expenses')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Expense Report</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/expenses')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Expense Report</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/admin/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @if($page == 'sales')
                    <li class="nav-item active">
                        <a href="{{url('/admin/sales')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Sales</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/sales')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Sales</span>
                        </a>
                    </li>
                @endif

                @if($page == 'constants')
                    <li class="nav-item active">
                        <a href="{{url('/admin/magicnumber')}}" class="nav-link">
                            <i class="link-icon" data-feather="hash"></i>
                            <span class="link-title">Magic Number</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/magicnumber')}}" class="nav-link">
                            <i class="link-icon" data-feather="hash"></i>
                            <span class="link-title">Magic Number</span>
                        </a>
                    </li>
                @endif

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/admin/training-room')}}" class="nav-link">
                            <i class="link-icon" data-feather="book-open"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/admin/training-room')}}" class="nav-link">
                            <i class="link-icon" data-feather="book-open"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('admin/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('admin/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Admin User --}}

            {{-- Global Manager User --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 2)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item nav-category">Manage</li>
                @if($page == 'users')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @endif

                @if($page == 'buissness_account')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @endif

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @if($page == 'kpi')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/kpi')}}" class="nav-link">
                            <i class="link-icon" data-feather="pie-chart"></i>
                            <span class="link-title">K.P.I</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/kpi')}}" class="nav-link">
                            <i class="link-icon" data-feather="pie-chart"></i>
                            <span class="link-title">K.P.I</span>
                        </a>
                    </li>
                @endif

                @if($page == 'expense')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/expenses')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Expense Report</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/expenses')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Expense Report</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @if($page == 'sales')
                    <li class="nav-item active">
                        <a href="{{url('/global_manager/sales')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Sales</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/global_manager/sales')}}" class="nav-link">
                            <i class="link-icon" data-feather="dollar-sign"></i>
                            <span class="link-title">Sales</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('global_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('global_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Global Manager User --}}

            <?php
              $CheckTrainingRoomIsRequired = \Illuminate\Support\Facades\DB::table('training_assignment_folders')
                  ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
                  ->where('training_assignment_folders.user_id', '=', \Illuminate\Support\Facades\Auth::id())
                  ->where('training_assignment_folders.completion_rate', '<', 100)
                  ->where('folders.required', '=', 1)
                  ->count();
            ?>

            {{-- Acquisition Manager User --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 3)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'users')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @endif

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Acquisition Manager User --}}

            {{-- Disposition Manager User --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 4)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'users')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/users')}}" class="nav-link">
                            <i class="link-icon" data-feather="user-plus"></i>
                            <span class="link-title">Users</span>
                        </a>
                    </li>
                @endif

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                            <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_manager/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Disposition Manager User --}}

            {{-- Acquisition Representative User --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 5)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_representative/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_representative/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_representative/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_representative/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_representative/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_representative/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_representative/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_representative/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/acquisition_representative/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/acquisition_representative/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Acquisition Representative User --}}

            {{-- Disposition Representative Dashboard --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 6)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'buissness_account')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/buissness_accounts')}}" class="nav-link">
                            <i class="link-icon fa fa-handshake"></i>
                            <span class="link-title">Business Account</span>
                        </a>
                    </li>
                @endif

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @if($page == 'lead-funnel')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/leads/funnel')}}" class="nav-link">
                            <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Lead Funnel</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/disposition_representative/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/disposition_representative/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Disposition Representative Dashboard --}}

            {{-- Cold Caller Dashboard --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 7)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/cold_caller/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/cold_caller/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/cold_caller/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/cold_caller/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/cold_caller/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/cold_caller/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/cold_caller/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/cold_caller/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Cold Caller Dashboard --}}

            {{-- Affiliate Dashboard --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 8)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/affiliate/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/affiliate/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/affiliate/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/affiliate/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/affiliate/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/affiliate/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/affiliate/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/affiliate/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Affiliate Dashboard  --}}

            {{-- Realtor Dashboard --}}
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 9)
                <li class="nav-item nav-category">Main</li>
                @if($page == 'dashboard')
                    <li class="nav-item active">
                        <a href="{{url('/realtor/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/realtor/dashboard')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-category">Manage</li>

                <!-- If all required courses are completed then sidebar will display - Start -->
                @if($CheckTrainingRoomIsRequired == 0)

                @if($page == 'leads')
                    <li class="nav-item active">
                        <a href="{{url('/realtor/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/realtor/leads')}}" class="nav-link">
                            <i class="link-icon" data-feather="trending-up"></i>
                            <span class="link-title">Leads</span>
                        </a>
                    </li>
                @endif

                @endif
                <!-- If all required courses are completed then sidebar will display - End -->

                @if($page == 'training_room')
                    <li class="nav-item active">
                        <a href="{{url('/realtor/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/realtor/training')}}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">Training Room</span>
                        </a>
                    </li>
                @endif

                @if($page == 'faq')
                    <li class="nav-item active">
                        <a href="{{url('/realtor/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{url('/realtor/training/faqs')}}" class="nav-link">
                            <i class="link-icon" data-feather="help-circle"></i>
                            <span class="link-title">Knowledge Zone</span>
                        </a>
                    </li>
                @endif
            @endif
            {{-- Affiliate Dashboard  --}}
        </ul>
    </div>
</nav>
