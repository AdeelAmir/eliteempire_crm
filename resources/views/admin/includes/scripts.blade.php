<script type="text/javascript">
    let ScrollCount = 1;
    let map = null;

    $(document).ready(function () {
        $("#toggleSidebar")[0].click();
        $.mask.definitions['~'] = '[+-]';

        MakeUsersTable();
        MakeDashboardLeadsTable();
        MakeLeadsTable();
        MakeLeadSalesTable();
        MakeEditLeadHistoryNotesTable();
        MakeSubmittedPayrollTable();
        MakeExpenseTable();
        MakeTrainingRoomTable();
        MakeTrainingRoomFolderTable();
        MakeTrainingRoomFaqsTable();
        MakeBuissnessAccountTable();
        InitializeCarousel();
        MakeUsersProgressTable();
        MakeAnnouncementTable();
        MakeAnnouncementDetailsTable();
        MakeBroadcastsTable();
        MakeBroadcastDetailsTable();
        InitializeCustomFaqsSearch();
        RefreshChatlist();
        RefreshContactlist();
        RefreshUserChat();
        RefreshGrouplist();
        MakeCoverageAreaMap();

        /*Update Lead Status Type*/
        <?php
        if (isset(\Illuminate\Support\Facades\Auth::user()->role_id)) {
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 3 || \Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            ?>
                $('#offerStatusSection').show();
                $('#dispoStatusSection').hide();
                $('#callStatusSection').hide();
                HideAllBlocks();
            <?php
            }
            elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4 || \Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            ?>
                $('#offerStatusSection').hide();
                $('#dispoStatusSection').show();
                $('#callStatusSection').hide();
                HideAllBlocks();
            <?php
            }
            elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            ?>
                $('#offerStatusSection').hide();
                $('#dispoStatusSection').hide();
                $('#callStatusSection').show();
                HideAllBlocks();
            <?php
            }
        }
        ?>

        // Marketing Report Page
        if ($("#marketingReport").length) {
            $("#stateFilter").select2();
            $("#companyFilter").select2();
            $("#userFilter").select2();
            $("#searchBy").select2();
            $(".startDateFilter").datetimepicker({
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
            $(".endDateFilter").datetimepicker({
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
        }

        // Add User Page
        if ($("#addNewUser").length) {
            // $('#phone').mask('999-999-9999');
            $("#county").select2();
            $("#city").select2();
            $("#state").select2();
            $("#role").select2();
            @if(\Illuminate\Support\Facades\Session::get('user_role') == 3 || \Illuminate\Support\Facades\Session::get('user_role') == 4)
            $("#state").trigger('change');
            @endif
        }

        // Edit User Page
        if ($("#editUserPage").length) {
            // $('#phone').mask('999-999-9999');
            $("#county").select2();
            $("#city").select2();
            $("#state").select2();
            $("#role").select2();
        }

        // All users page
        if ($("#usersPage").length) {
            $("#filter_city").select2();
            $("#filter_state").select2();
            $("#filter_status").select2();
            $("#filter_role").select2();
        }

        // Add Buissness Account
        if ($("#buissnessAccountPage").length) {
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
        }

        if ($("#addNewInvestorPage").length) {
            MakeServingLocation();
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
            $(".propertyClassification").select2();
            $(".propertyType").select2();
            $(".multiFamilyType").select2();
            $(".constructionType").select2();
        }

        if ($("#editInvestorPage").length) {
            $(".propertyClassification").select2();
            $(".propertyType").select2();
            $(".multiFamilyType").select2();
            $(".constructionType").select2();
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
        }

        if ($("#addNewTitleCompanyPage").length) {
            MakeTitleCompanyServingLocation();
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
        }

        if ($("#editTitleCompanyPage").length) {
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
        }

        if ($("#addNewRealtorPage").length) {
            MakeServingLocation();
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
            $(".propertyClassification").select2();
            $(".propertyType").select2();
            $(".multiFamilyType").select2();
            $(".constructionType").select2();
        }

        if ($("#editRealtorPage").length) {
            $(".propertyClassification").select2();
            $(".propertyType").select2();
            $(".multiFamilyType").select2();
            $(".constructionType").select2();
            $(".states").select2();
            $(".cities").select2();
            $(".counties").select2();
        }

        // Lead Update Status
        if ($("#LeadsPage").length) {
            $("#lead_status_type").select2();
            $("#dispo_lead_status").select2();
            $("#offer_lead_status").select2();
            $("#call_lead_status").select2();
            $("#stateFilter").select2();
            $("#cityFilter").select2();
            $("#countyFilter").select2();
            $("#investorFilter").select2();
            $("#realtorFilter").select2();
            $("#titleCompanyFilter").select2();
            $("#leadSearch").select2();
            $("#leadSourceFilter").select2();
            $("#dataSourceFilter").select2();
            FilterLeadsByStatus('1,2,3,4,5,6,7,8,9');
        }

        if ($("#DashboardPage").length) {
          $("#lead_status_type").select2();
          $("#dispo_lead_status").select2();
          $("#offer_lead_status").select2();
          $("#call_lead_status").select2();
        }

        if ($("#TrainingRoomDetailsPage").length) {
          $("#_role").select2();
        }

        // Add Sale Page
        if ($("#SalesPage").length) {
            // $('#lead_phone_number').mask('999-999-9999');
        }

        // Internal Messaging System
        if ($("#InternalMessagingPage").length) {
          $("#add_group_members").select2();
          $("#edit_group_members").select2();
        }

        // User Change Password
        $("form#changePasswordForm").submit(function (e) {
            // Check for Password Match
            let NewPassword = $("#newPassword").val();
            let ConfirmPassword = $("#confirmPassword").val();
            if (NewPassword === ConfirmPassword) {
                $("#changePasswordError").hide();
            } else {
                $("#changePasswordError").show();
                e.preventDefault(e);
            }
        });

        /* Expense Form */
        if ($(".expenseDate").length) {
            $(".expenseDate").datetimepicker({
                minView: 2,
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                dateFormat: 'mm/dd/yyyy',
            }).datetimepicker("update", $("#expenseDate").val());
        }

        if ($("#total").length) {
            $("#total").change(function () {
                let d = this.value;
                if (!parseFloat(d)) {
                    this.value = "";
                }
            });
        }

        if ($("#rate").length) {
            $("#rate").change(function () {
                let d = this.value;
                if (!parseFloat(d)) {
                    this.value = "";
                }
            });
        }

        if ($("#payrollFilterPage").length) {
            $(".startDateFilter").datetimepicker({
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
            $(".endDateFilter").datetimepicker({
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
        }

        if ($("#filterPage").length) {
            $("#phone1Filter").mask('999-999-9999');
            $("#phone2Filter").mask('999-999-9999');
            $("#stateFilter").select2();
            $("#companyFilter").select2();
            $("#userFilter").select2();
            $("#statusFilter").select2();
            $(".startDateFilter").datetimepicker({
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
            $(".endDateFilter").datetimepicker({
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
            $('.appointmentDateFilter').datetimepicker({
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
        }

        // Add Lead Page
        if ($("#addLeadPage").length) {
            $(document).on("click", ".deletePhoneField", function (e) {
                PhoneField--;
                let id = this.id.split('_')[1];
                $("#sellerPhone" + id).remove();
            });

            $("#ownersOccupy").select2();
            $("#occupancyStatus").select2();
            $("#martial_status").select2();
            $("#language").select2();
            $("#city").select2();
            $("#state").select2();
            $("#propertyClassification").select2();
            $("#constructionType").select2();
            $("#homeFeature").select2();
            $("#associationFee").select2();
            $("#reasonsToSales").select2();
            $("#conditions").select2();
            $("#leadSources").select2();
            $("#data_source").select2();
        }

        // Edit Lead Page
        if ($("#editLeadPage").length) {
            // $('#phone').mask('999-999-9999');
            // $('#phone2').mask('999-999-9999');
            $(document).on("click", ".deletePhoneField", function (e) {
                PhoneField--;
                let id = this.id.split('_')[1];
                $("#sellerPhone" + id).remove();
            });

            $("#ownersOccupy").select2();
            $("#occupancyStatus").select2();
            $("#martial_status").select2();
            $("#language").select2();
            $("#city").select2();
            $("#state").select2();
            $("#propertyClassification").select2();
            $("#propertyType").select2();
            $("#constructionType").select2();
            $("#homeFeature").select2();
            $("#associationFee").select2();
            $("#reasonsToSales").select2();
            $("#conditions").select2();
            $("#conditions1").select2();
            $("#leadSources").select2();

            $("#lead_status_type").select2();
            $("#dispo_lead_status").select2();
            $("#offer_lead_status").select2();
            $("#call_lead_status").select2();
            $("#data_source").select2();
            PhoneField = $("#phoneCountHidden").val();
        }

        // Assigned Lead Page
        if ($("#totaltasks").length) {
            let TotalTasks = $("#totaltasks").val();
            for (let i = 0; i < TotalTasks; i++) {
                $('#state' + i).select2();
            }
        }

        // Add Team Page
        if ($("#addTeamPage").length) {
            $("#addTeamLead").select2();
            $("#addTeamMembers").select2();
            $("#addConfirmationAgentTeamMembers").select2();
            $("#addTeamManager").select2();
            $("#addTeamSupervisor").select2();
            $("#addTeamConfirmationAgentSupervisor").select2();
            $("#addConfirmationAgentTeamMembers").select2();
            $("#addTeamManager").select2();
        }

        // Edit Team Page
        if ($("#editTeamPage").length) {
            $("#editTeamLead").select2();
            $("#editTeamMembers").select2();
            $("#editConfirmationAgentTeamMembers").select2();
            $("#addConfirmationAgentTeamMembers").select2();
            $("#editTeamManager").select2();
            $("#editTeamSupervisor").select2();
        }

        // View Team (Supervisor)
        if ($("#viewTeam").length) {
            $("#editTeamSupervisor").select2();
            $("#editTeamLead").select2();
            $("#editTeamMembers").select2();
        }

        // Call Request New
        if ($("#addNewCallRequest").length) {
        }

        // Call Request
        if ($("#editCallRequest").length) {
        }

        //Edit Coverage File Page
        if ($("#editCoverageFilePage").length) {
            $("#file").on("change", function (e) {
                let fileName = document.getElementById("file").value;
                let idxDot = fileName.lastIndexOf(".") + 1;
                let extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
                if (extFile === "xlsx" || extFile === "xls") {
                    // Ok
                } else {
                    $("#file").val('');
                }
            });
        }

        // Update Profile Page
        if ($("#updateProfilePage").length) {
            $("#phone").mask('999-999-9999');
            @if(isset($Profile[0]))
            let today = new Date();
            let DOB = new Date('{{$Profile[0]->dob}}').toISOString().split('T')[0];
            $("#dob").val(DOB);
            @endif

            $("#userProfileUpdate").on("change", function (e) {
                let fileName = document.getElementById("userProfileUpdate").value;
                let idxDot = fileName.lastIndexOf(".") + 1;
                let extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
                if (extFile === "jpeg" || extFile === "png" || extFile === "jpg" || extFile === "JPEG" || extFile === "PNG" || extFile === "JPG") {
                    $("#userProfileUpdatePreview").attr('src', URL.createObjectURL(e.target.files[0]));
                } else {
                    $("#userProfileUpdate").val('');
                }
            });
        }

        $('.form_datetime').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            dateFormat: 'mm/dd/yyyy - HH:ii p',
        });

        if ($("#addSaleForm").length) {
            $(".deletePayeeBtn").trigger('click');
        }

        // KPI Page
        if ($("#KPIPage").length) {
          $(".startDateFilter").datetimepicker({
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
          $(".endDateFilter").datetimepicker({
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
        }

        if ($("#KPIPage").length) {
          LoadLeadSourcePieChart();
          LoadDataSourcePieChart();
          LoadClosedWonInterestedPieChart();
        }

        // Lead Funnel
        if ($("#LeadFunnelPage").length) {
          $(".startDateFilter").datetimepicker({
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
          $(".endDateFilter").datetimepicker({
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
        }
        $(".leadfunnel-dropdown-item").click(function (e) {
            $("#leadfunnel-dropdown-value").text(this.text + ' ');
            if (this.text === 'Range') {
              $("#LeadFunnelCustomRangeStartDate").show();
              $("#LeadFunnelCustomRangeEndDate").show();
              $("#LeadFunnelFilterButtonSection").show();
            }
            else {
              $("#LeadFunnelCustomRangeStartDate").hide();
              $("#LeadFunnelCustomRangeEndDate").hide();
              $("#LeadFunnelFilterButtonSection").hide();
              LoadLeadFunnel(this.text);
            }
        });

        // KPI Charts
        $(".leadsource-dropdown-item").click(function (e) {
            $("#leadsource-dropdown-value").text(this.text + ' ');
            if (this.text === 'Range') {
              $("#LeadSourceCustomRangeStartDate").show();
              $("#LeadSourceCustomRangeEndDate").show();
              $("#LeadSourceFilterButtonSection").show();
            }
            else {
              $("#LeadSourceCustomRangeStartDate").hide();
              $("#LeadSourceCustomRangeEndDate").hide();
              $("#LeadSourceFilterButtonSection").hide();
              LoadLeadSourceAnalysis(this.text);
            }
        });

        $(".datasource-dropdown-item").click(function (e) {
            $("#datasource-dropdown-value").text(this.text + ' ');
            if (this.text === 'Range') {
              $("#DataSourceCustomRangeStartDate").show();
              $("#DataSourceCustomRangeEndDate").show();
              $("#DataSourceFilterButtonSection").show();
            }
            else {
              $("#DataSourceCustomRangeStartDate").hide();
              $("#DataSourceCustomRangeEndDate").hide();
              $("#DataSourceFilterButtonSection").hide();
              LoadDataSourceAnalysis(this.text);
            }
        });

        $(".leadsstatus-dropdown-item").click(function (e) {
            $("#leadsstatus-dropdown-value").text(this.text + ' ');
            if (this.text === 'Range') {
              $("#LeadStatusCustomRangeStartDate").show();
              $("#LeadStatusCustomRangeEndDate").show();
              $("#LeadStatusFilterButtonSection").show();
            }
            else {
              $("#LeadStatusCustomRangeStartDate").hide();
              $("#LeadStatusCustomRangeEndDate").hide();
              $("#LeadStatusFilterButtonSection").hide();
              LoadClosedWonInterestedAnalysis(this.text);
            }
        });

        // User State Filter Page
        if ($("#userStateFilterPage").length) {
            $("#state").select2();
            $("#lead_status").select2();
            $(".startDateFilter").datetimepicker({
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
            $(".endDateFilter").datetimepicker({
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

            @if(isset($UserDepartmentFilterDetails[0]))
                @if($UserDepartmentFilterDetails[0]->start_date != '')
                    let date = new Date('{{$UserDepartmentFilterDetails[0]->start_date}}');
                    $(".startDateFilter").datetimepicker('setDate', date);
                @endif
            @endif

            @if(isset($UserDepartmentFilterDetails[0]))
                @if($UserDepartmentFilterDetails[0]->end_date != '')
                    date = new Date('{{$UserDepartmentFilterDetails[0]->end_date}}');
                    $(".endDateFilter").datetimepicker('setDate', date);
                @endif
            @endif
        }

        // Training Room
        if ($("#addTrainingRoomFolderPage").length) {
            $("#required_status").select2();
        }

        if ($("#editTrainingRoomFolderPage").length) {
            $("#required_status").select2();
        }

        if ($("#TrainingRoomDetailsPage").length) {
            $("#copy_folder").select2();
        }

        // Hide Alerts
        setInterval(function(){ $("#_dangerAlert").hide(); }, 5000);
        setInterval(function(){ $("#SuccessAlert").hide(); }, 5000);
        setInterval(function(){ $("#FailedAlert").hide(); }, 5000);
        setInterval(function(){ $(".alert-success").hide(); }, 5000);
        setInterval(function(){ $(".alert-danger").hide(); }, 5000);

        /*Edit Announcement*/
        @if(isset($announcement_details[0]))
        let datetime = new Date('{{$announcement_details[0]->expiration}}');
        $('.form_datetime').datetimepicker('setDate', datetime);
        @endif
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /*New Script File Starts Here*/

    function LoadLeadFunnel(Type) {
        let StartDate = '';
        let EndDate = '';
        if (Type === 'Range') {
          StartDate = $("#customLeadFunnelStartDate").val();
          EndDate = $("#customLeadFunnelEndDate").val();
          window.location.href = "{{ url('admin/leads/funnel/Range/')}}" + "/" + StartDate + "/" + EndDate;
        }
        else if (Type === 'Recent Week') {
          window.location.href = "{{ url('admin/leads/funnel/Recent Week')}}";
        }
        else if (Type === 'Recent Month') {
          window.location.href = "{{ url('admin/leads/funnel/Recent Month')}}";
        }
        else if (Type === 'Recent Quarter') {
          window.location.href = "{{ url('admin/leads/funnel/Recent Quarter')}}";
        }
        else if (Type === 'Recent Semester') {
          window.location.href = "{{ url('admin/leads/funnel/Recent Semester')}}";
        }
        else if (Type === 'Recent Year') {
          window.location.href = "{{ url('admin/leads/funnel/Recent Year')}}";
        }
        else if (Type === 'All Time') {
          window.location.href = "{{ url('admin/leads/funnel/All Time')}}";
        }
    }

    /* KPI Graphs - Start */
    function LoadLeadSourceAnalysis(Type) {
        <?php
          $Url = "";
          if ((\Illuminate\Support\Facades\Auth::user()->role_id == 1)) {
            $Url = url('admin/kpi/leadsource-analysis');
          } elseif ((\Illuminate\Support\Facades\Auth::user()->role_id == 2)) {
            $Url = url('global_manager/kpi/leadsource-analysis');
          }
        ?>
        let StartDate = '';
        let EndDate = '';
        if (Type === 'Range') {
          StartDate = $("#customLeadSourceStartDate").val();
          EndDate = $("#customLeadSourceEndDate").val();
        }
        $.ajax({
            type: "post",
            url: "{{ $Url }}",
            data: { type : Type, StartDate : StartDate, EndDate : EndDate }
        }).done(function (data) {
            data = JSON.parse(data);
            $("#D4D_leadsource").val(data.d4d);
            $("#PropStream_leadsource").val(data.PropStream);
            $("#Calling_leadsource").val(data.Calling);
            $("#Text_leadsource").val(data.Text);
            $("#Facebook_leadsource").val(data.Facebook);
            $("#Instagram_leadsource").val(data.Instagram);
            $("#Website_leadsource").val(data.Website);
            $("#Zillow_leadsource").val(data.Zillow);
            $("#Wholesaler_leadsource").val(data.Wholesaler);
            $("#Realtor_leadsource").val(data.Realtor);
            $("#Investor_leadsource").val(data.Investor);
            $("#Radio_leadsource").val(data.Radio);
            $("#JVPartner_leadsource").val(data.JV_Partner);
            $("#BandedSign_leadsource").val(data.Banded_Sign);

            $("#D4D_leadsourceDisplay").text(data.d4d);
            $("#PropStream_leadsourceDisplay").text(data.PropStream);
            $("#Calling_leadsourceDisplay").text(data.Calling);
            $("#Text_leadsourceDisplay").text(data.Text);
            $("#Facebook_leadsourceDisplay").text(data.Facebook);
            $("#Instagram_leadsourceDisplay").text(data.Instagram);
            $("#Website_leadsourceDisplay").text(data.Website);
            $("#Zillow_leadsourceDisplay").text(data.Zillow);
            $("#Wholesaler_leadsourceDisplay").text(data.Wholesaler);
            $("#Realtor_leadsourceDisplay").text(data.Realtor);
            $("#Investor_leadsourceDisplay").text(data.Investor);
            $("#Radio_leadsourceDisplay").text(data.Radio);
            $("#JVPartner_leadsourceDisplay").text(data.JV_Partner);
            $("#BandedSign_leadsourceDisplay").text(data.Banded_Sign);

            LoadLeadSourcePieChart();
        });
    }

    function LoadLeadSourcePieChart() {
        //Pie Chart
        $("#leadsource_chart").html('');
        let pieCtx = document.getElementById("leadsource_chart"),
            pieConfig = {
                colors: ['#286088', '#6c757d', '#22cc62', '#ffbc34', '#cc0099', '#9999ff', '#cc0000', '#ff9900', '#66ffff', '#336699', '#666699', '#993300', '#999966', '#FFC0CB'],
                series: [Math.round($("#D4D_leadsource").val()), Math.round($("#PropStream_leadsource").val()), Math.round($("#Calling_leadsource").val()), Math.round($("#Text_leadsource").val()), Math.round($("#Facebook_leadsource").val()), Math.round($("#Instagram_leadsource").val()), Math.round($("#Website_leadsource").val()), Math.round($("#Zillow_leadsource").val()), Math.round($("#Wholesaler_leadsource").val()), Math.round($("#Realtor_leadsource").val()), Math.round($("#Investor_leadsource").val()), Math.round($("#Radio_leadsource").val()), Math.round($("#JVPartner_leadsource").val()), Math.round($("#BandedSign_leadsource").val())],
                chart: {
                    fontFamily: 'Poppins, sans-serif',
                    height: 230,
                    type: 'donut',
                },
                labels: ['D4D', 'PropStream', 'Calling', 'Text', 'Facebook', 'Instagram', 'Website', 'Zillow', 'Wholesaler', 'Realtor', 'Investor', 'Radio', 'JV Partner', 'Banded Sign'],
                legend: {show: false},
                responsive: [{
                    breakpoint: 480,
                    options: {
                        /*chart: {
                            width: 200
                        },*/
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        let pieChart = new ApexCharts(pieCtx, pieConfig);
        pieChart.render();
    }

    function LoadDataSourceAnalysis(Type) {
        <?php
          $Url = "";
          if ((\Illuminate\Support\Facades\Auth::user()->role_id == 1)) {
            $Url = url('admin/kpi/datasource-analysis');
          } elseif ((\Illuminate\Support\Facades\Auth::user()->role_id == 2)) {
            $Url = url('global_manager/kpi/datasource-analysis');
          }
        ?>
        let StartDate = '';
        let EndDate = '';
        if (Type === 'Range') {
          StartDate = $("#customDataSourceStartDate").val();
          EndDate = $("#customDataSourceEndDate").val();
        }
        $.ajax({
            type: "post",
            url: "{{ $Url }}",
            data: { type : Type, StartDate : StartDate, EndDate : EndDate }
        }).done(function (data) {
            data = JSON.parse(data);
            $("#OnMarket_datasource").val(data.OnMarket);
            $("#Vacant_datasource").val(data.Vacant);
            $("#Liens_datasource").val(data.Liens);
            $("#PreForeclosures_datasource").val(data.PreForeclosures);
            $("#Auctions_datasource").val(data.Auctions);
            $("#BankOwned_datasource").val(data.BankOwned);
            $("#CashBuyers_datasource").val(data.CashBuyers);
            $("#HighEquity_datasource").val(data.HighEquity);
            $("#FreeClear_datasource").val(data.FreeClear);
            $("#Bankruptcy_datasource").val(data.Bankruptcy);
            $("#Divorce_datasource").val(data.Divorce);
            $("#TaxDelinquencies_datasource").val(data.TaxDelinquencies);
            $("#Flippers_datasource").val(data.Flippers);
            $("#FailedListings_datasource").val(data.FailedListings);
            $("#SeniorOwners_datasource").val(data.SeniorOwners);
            $("#VacantLand_datasource").val(data.VacantLand);
            $("#TiredLandlords_datasource").val(data.TiredLandlords);
            $("#PreProbate_datasource").val(data.PreProbate);
            $("#Others_datasource").val(data.Others);

            $("#OnMarket_datasourceDisplay").text(data.OnMarket);
            $("#Vacant_datasourceDisplay").text(data.Vacant);
            $("#Liens_datasourceDisplay").text(data.Liens);
            $("#PreForeclosures_datasourceDisplay").text(data.PreForeclosures);
            $("#Auctions_datasourceDisplay").text(data.Auctions);
            $("#BankOwned_datasourceDisplay").text(data.BankOwned);
            $("#CashBuyers_datasourceDisplay").text(data.CashBuyers);
            $("#HighEquity_datasourceDisplay").text(data.HighEquity);
            $("#FreeClear_datasourceDisplay").text(data.FreeClear);
            $("#Bankruptcy_datasourceDisplay").text(data.Bankruptcy);
            $("#Divorce_datasourceDisplay").text(data.Divorce);
            $("#TaxDelinquencies_datasourceDisplay").text(data.TaxDelinquencies);
            $("#Flippers_datasourceDisplay").text(data.Flippers);
            $("#FailedListings_datasourceDisplay").text(data.FailedListings);
            $("#SeniorOwners_datasourceDisplay").text(data.SeniorOwners);
            $("#VacantLand_datasourceDisplay").text(data.VacantLand);
            $("#TiredLandlords_datasourceDisplay").text(data.TiredLandlords);
            $("#PreProbate_datasourceDisplay").text(data.PreProbate);
            $("#Others_datasourceDisplay").text(data.Others);

            LoadDataSourcePieChart();
        });
    }

    function LoadDataSourcePieChart() {
        //Pie Chart
        $("#datasource_chart").html('');
        let pieCtx = document.getElementById("datasource_chart"),
            pieConfig = {
                colors: ['#286088', '#6c757d', '#22cc62', '#ffbc34', '#cc0099', '#9999ff', '#cc0000', '#ff9900', '#66ffff', '#336699', '#666699', '#993300', '#999966', '#009999', '#cc00ff', '#666633', '#990099', '#80bfff', '#80bfff'],
                series: [Math.round($("#OnMarket_datasource").val()), Math.round($("#Vacant_datasource").val()), Math.round($("#Liens_datasource").val()), Math.round($("#PreForeclosures_datasource").val()), Math.round($("#Auctions_datasource").val()), Math.round($("#BankOwned_datasource").val()), Math.round($("#CashBuyers_datasource").val()), Math.round($("#HighEquity_datasource").val()), Math.round($("#FreeClear_datasource").val()), Math.round($("#Bankruptcy_datasource").val()), Math.round($("#Divorce_datasource").val()), Math.round($("#TaxDelinquencies_datasource").val()), Math.round($("#Flippers_datasource").val()), Math.round($("#FailedListings_datasource").val()), Math.round($("#SeniorOwners_datasource").val()), Math.round($("#VacantLand_datasource").val()), Math.round($("#TiredLandlords_datasource").val()), Math.round($("#PreProbate_datasource").val()), Math.round($("#Others_datasource").val())],
                chart: {
                    fontFamily: 'Poppins, sans-serif',
                    height: 230,
                    type: 'donut',
                },
                labels: ['On Market', 'Vacant', 'Liens', 'Pre-Foreclosures', 'Auctions', 'Bank Owned', 'Cash Buyers', 'High Equity', 'Free & Clear', 'Bankruptcy', 'Divorce', 'Tax Delinquencies', 'Flippers', 'Failed Listings', 'Senior Owners', 'Vacant Land', 'Tired Landlords', 'Pre-Probate (Deceased Owner)', 'Others'],
                legend: {show: false},
                responsive: [{
                    breakpoint: 480,
                    options: {
                        /*chart: {
                            width: 200
                        },*/
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        let pieChart = new ApexCharts(pieCtx, pieConfig);
        pieChart.render();
    }

    function LoadClosedWonInterestedAnalysis(Type) {
        <?php
          $Url = "";
          if ((\Illuminate\Support\Facades\Auth::user()->role_id == 1)) {
            $Url = url('admin/kpi/leadstatus-analysis');
          } elseif ((\Illuminate\Support\Facades\Auth::user()->role_id == 2)) {
            $Url = url('global_manager/kpi/leadstatus-analysis');
          }
        ?>
        let StartDate = '';
        let EndDate = '';
        if (Type === 'Range') {
          StartDate = $("#customLeadStatusStartDate").val();
          EndDate = $("#customLeadStatusEndDate").val();
        }
        $.ajax({
            type: "post",
            url: "{{ $Url }}",
            data: { type : Type, StartDate : StartDate, EndDate : EndDate }
        }).done(function (data) {
            data = JSON.parse(data);
            $("#closedWonLeadsAnalysis").val(data.ClosedWon);
            $("#interestedLeadsAnalysis").val(data.Interested);
            $("#notinterestedLeadsAnalysis").val(data.NotInterested);
            $("#leadinLeadsAnalysis").val(data.LeadIn);
            $("#donotcallLeadsAnalysis").val(data.DoNotCall);
            $("#noanswerLeadsAnalysis").val(data.NoAnswer);
            $("#wrongnumberLeadsAnalysis").val(data.WrongNumber);
            $("#followupLeadsAnalysis").val(data.FollowUp);
            $("#offernotgivenLeadsAnalysis").val(data.OfferNotGiven);
            $("#offernotaceptedLeadsAnalysis").val(data.OfferNotAccepted);
            $("#acceptedLeadsAnalysis").val(data.Accepted);
            $("#negotiatingwithsellerLeadsAnalysis").val(data.NegotiatingWithSeller);
            $("#agreementsentLeadsAnalysis").val(data.AgreementSent);
            $("#agreementreceivedLeadsAnalysis").val(data.AgreementReceived);
            $("#sendtoinvestorLeadsAnalysis").val(data.SendToInvestor);
            $("#negotiationwithinvestorLeadsAnalysis").val(data.NegotiationWithInvestor);
            $("#senttotitleLeadsAnalysis").val(data.SendToTitle);
            $("#sendcontracttoinvestorLeadsAnalysis").val(data.SendContractToInvestor);
            $("#emdreceivedLeadsAnalysis").val(data.EMDReceived);
            $("#emdnotreceivedLeadsAnalysis").val(data.EMDNotReceived);
            $("#inspectionLeadsAnalysis").val(data.Inspection);
            $("#closedOnLeadsAnalysis").val(data.CloseOn);
            $("#deallostLeadsAnalysis").val(data.DealLost);

            $("#_closedWonLeadsDisplay").text(data.ClosedWon);
            $("#_interestedLeadsDisplay").text(data.Interested);
            $("#_notinterestedLeadsDisplay").text(data.NotInterested);
            $("#_leadinLeadsDisplay").text(data.LeadIn);
            $("#_donotcallLeadsDisplay").text(data.DoNotCall);
            $("#_noanswerLeadsDisplay").text(data.NoAnswer);
            $("#_wrongnumberLeadsDisplay").text(data.WrongNumber);
            $("#_followupLeadsDisplay").text(data.FollowUp);
            $("#_offernotgivenLeadsDisplay").text(data.OfferNotGiven);
            $("#_offernotaceptedLeadsDisplay").text(data.OfferNotAccepted);
            $("#_aceptedLeadsDisplay").text(data.Accepted);
            $("#_negotiatingwithsellerLeadsDisplay").text(data.NegotiatingWithSeller);
            $("#_agreementsentLeadsDisplay").text(data.AgreementSent);
            $("#_agreementreceivedLeadsDisplay").text(data.AgreementReceived);
            $("#_sendtoinvestorLeadsDisplay").text(data.SendToInvestor);
            $("#_negotiationwithinvestorLeadsDisplay").text(data.NegotiationWithInvestor);
            $("#_senttotitleLeadsDisplay").text(data.SendToTitle);
            $("#_sendcontracttoinvestorLeadsDisplay").text(data.SendContractToInvestor);
            $("#_emdreceivedLeadsDisplay").text(data.EMDReceived);
            $("#_emdnotreceivedLeadsDisplay").text(data.EMDNotReceived);
            $("#_inspectionLeadsDisplay").text(data.Inspection);
            $("#_closeOnLeadsDisplay").text(data.CloseOn);
            $("#_deallostLeadsDisplay").text(data.DealLost);

            LoadClosedWonInterestedPieChart();
        });
    }

    function LoadClosedWonInterestedPieChart() {
        // Column chart
        $("#leadstatus_chart").html('');
        var columnCtx = document.getElementById("leadstatus_chart"),
            columnConfig = {
                colors: [
                  '#fec354',
                  '#3f9b4d',
                  '#db3c45',
                  '#2075f2',
                  '#FFC0CB',
                  '#cc0066',
                  '#FFA500',
                  '#6d757e',
                  '#00ff00',
                  '#ff9999',
                  '#ccff33',
                  '#ffff99',
                  '#ff9933',
                  '#666633',
                  '#009999',
                  '#990099',
                  '#ff9900',
                  '#ff66ff',
                  '#3366cc',
                  '#1ac6ff',
                  '#66ffcc',
                  '#cc0066'
                ],
                series: [
                    {
                        name: "Lead In",
                        type: "column",
                        data: [parseInt($("#leadinLeadsAnalysis").val())]
                    },
                    {
                        name: "Interested",
                        type: "column",
                        data: [parseInt($("#interestedLeadsAnalysis").val())]
                    },
                    {
                        name: "Not Interested",
                        type: "column",
                        data: [parseInt($("#notinterestedLeadsAnalysis").val())]
                    },
                    {
                        name: "Do Not Call",
                        type: "column",
                        data: [parseInt($("#donotcallLeadsAnalysis").val())]
                    },
                    {
                        name: "No Answer",
                        type: "column",
                        data: [parseInt($("#noanswerLeadsAnalysis").val())]
                    },
                    {
                        name: "Wrong Number",
                        type: "column",
                        data: [parseInt($("#wrongnumberLeadsAnalysis").val())]
                    },
                    {
                        name: "Offer Not Given",
                        type: "column",
                        data: [parseInt($("#offernotgivenLeadsAnalysis").val())]
                    },
                    {
                        name: "Offer Not Accepted",
                        type: "column",
                        data: [parseInt($("#offernotaceptedLeadsAnalysis").val())]
                    },
                    {
                        name: "Accepted",
                        type: "column",
                        data: [parseInt($("#acceptedLeadsAnalysis").val())]
                    },
                    {
                        name: "Negotiating With Seller",
                        type: "column",
                        data: [parseInt($("#negotiatingwithsellerLeadsAnalysis").val())]
                    },
                    {
                        name: "Agreement Sent",
                        type: "column",
                        data: [parseInt($("#agreementsentLeadsAnalysis").val())]
                    },
                    {
                        name: "Agreement Received",
                        type: "column",
                        data: [parseInt($("#agreementreceivedLeadsAnalysis").val())]
                    },
                    {
                        name: "Send To Investor",
                        type: "column",
                        data: [parseInt($("#sendtoinvestorLeadsAnalysis").val())]
                    },
                    {
                        name: "Negotiation With Investor",
                        type: "column",
                        data: [parseInt($("#negotiationwithinvestorLeadsAnalysis").val())]
                    },
                    {
                        name: "Send To Title",
                        type: "column",
                        data: [parseInt($("#senttotitleLeadsAnalysis").val())]
                    },
                    {
                        name: "Send Contract To Investor",
                        type: "column",
                        data: [parseInt($("#sendcontracttoinvestorLeadsAnalysis").val())]
                    },
                    {
                        name: "EMD Received",
                        type: "column",
                        data: [parseInt($("#emdreceivedLeadsAnalysis").val())]
                    },
                    {
                        name: "EMD Not Received",
                        type: "column",
                        data: [parseInt($("#emdnotreceivedLeadsAnalysis").val())]
                    },
                    {
                        name: "Inspection",
                        type: "column",
                        data: [parseInt($("#inspectionLeadsAnalysis").val())]
                    },
                    {
                        name: "Close On",
                        type: "column",
                        data: [parseInt($("#closedOnLeadsAnalysis").val())]
                    },
                    {
                        name: "Closed Won",
                        type: "column",
                        data: [parseInt($("#closedWonLeadsAnalysis").val())]
                    },
                    {
                        name: "Deal Lost",
                        type: "column",
                        data: [parseInt($("#deallostLeadsAnalysis").val())]
                    }
                ],
                chart: {
                    type: 'bar',
                    fontFamily: 'Poppins, sans-serif',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '100%',
                        /*endingShape: 'rounded'*/
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Lead In','Interested','Not Interested','Do Not Call','Closed Won','No Answer','Wrong Number','Offer Not Given','Offer Not Accepted','Accepted','Negotiating With Seller','Agreement Sent','Agreement Received','Send To Investor','Negotiation With Investor','Send To Title','Send Contract To Investor','EMD Received','EMD Not Received','Inspection','Close On','Closed WON','Deal Lost'],
                },
                yaxis: {
                    title: {
                        text: 'Count'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val
                        }
                    }
                }
            };
        var columnChart = new ApexCharts(columnCtx, columnConfig);
        columnChart.render();
    }
    /* KPI Graphs - End */

    /*Lead*/
    let PhoneField = 1;
    let ServingLocationCounter = 0;

    function ShowPropertyInformation() {
        if ($('#firstName').val() && $('#lastName').val() && $('#phone').val()) {
          $("#leadSellerInformation").hide();
          $("#leadPropertyInformation").show();
          $("#backBtn").show();
          $("#continueBtn").hide();
          $("#submitBtn").show();
          $(window).scrollTop(0);
          $("#leadStepHeading").text("Property Information");
          $(".step2").removeClass("disabled");
          $(".step2").addClass("complete");
        }
        else {
          // First Name
          if ($('#firstName').val()) {
              $('#f_name').hide();
          }
          else {
              $("#firstName").keyup(function(){
                  $('#f_name').hide();
              });
              $('#f_name').show();
              $("#f_name").html("First Name is required !").css("color","red");
          }
          // Last Name
          if ($('#lastName').val())
          {
              $('#l_name').hide();
          }
          else
          {
              $("#lastName").keyup(function(){
                  $('#l_name').hide();
              });
              $('#l_name').show();
              $("#l_name").html("Last Name is required !").css("color","red");
          }
          // Phone Number 1
          if ($('#phone').val() !== '')
          {
              $('#p_phone1').hide();
          }
          else
          {
              $("#phone").keyup(function(){
                  $('#p_phone1').hide();
              });
              $('#p_phone1').show();
              $("#p_phone1").html("Phone Number 1 is required !").css("color","red");
          }
        }
    }

    function ShowSellerInformation() {
        $("#leadSellerInformation").show();
        $("#leadPropertyInformation").hide();
        $("#backBtn").hide();
        $("#continueBtn").show();
        $("#submitBtn").hide();
        $(window).scrollTop(0);
        $("#leadStepHeading").text("Seller Information");
        $(".step2").removeClass("complete");
        $(".step2").addClass("disabled");
    }

    function AddPhoneField() {
        if (PhoneField === 5) {
            return;
        }
        PhoneField++;
        let Field = '<div class="col-md-3 mb-2" id="sellerPhone' + PhoneField + '">' +
            '           <label class="w-100" for="phone' + PhoneField + '">Phone Number ' + PhoneField + '<span><i class="fa fa-trash deletePhoneField float-right" id="deletePhoneField_' + PhoneField + '" style="cursor: pointer;"></i></span></label>' +
            '           <input type="number" step="any" name="phone' + PhoneField + '" id="phone' + PhoneField + '" class="form-control" placeholder="Phone Number" maxlength="20" required/>' +
            '       </div>';
        $("#sellerPhone" + (PhoneField - 1)).after(Field);
    }

    function AddPropertyType(e) {
        if (e.value === 'residential') {
            let Field = '<div class="col-md-3 mb-2" id="propertyTypeDiv">' +
                '           <label for="propertyType">Property Type</label>' +
                '           <select name="propertyType" id="propertyType" class="form-control" onchange="AddMultiFamily(this)" required>' +
                '           <option value="">Select</option>' +
                '           <option value="singleFamily">Single Family</option>' +
                '           <option value="condominium">Condominium</option>' +
                '           <option value="townhouse">Townhouse</option>' +
                '           <option value="multiFamily">Multi family</option>' +
                '           <option value="mobile">Mobile</option>' +
                '           <option value="manifactureHome">Manifacture Home</option>' +
                '       </div>';
            $("#propertyClassificationDiv").after(Field);
            $("#propertyType").select2();
        } else {
            $("#propertyTypeDiv").remove();
            $("#multiFamilyTypeDiv").remove();
        }
    }

    function CheckPropertyClassification(e) {
        if (e.value === 'vacant') {
          $("#bedroom").val(0);
          $("#bathroom").val(0);
          $("#storiesNo").val(0);
        }
    }

    function AddMultiFamily(e) {
        if (e.value === 'multiFamily') {
            let Field = '<div class="col-md-3 mb-2" id="multiFamilyTypeDiv">' +
                '           <label for="multiFamilyType">Multi-Family</label>' +
                '           <select name="multiFamilyType" id="multiFamilyType" class="form-control" required>' +
                '           <option value="">Select</option>' +
                '           <option value="duplexes">Duplexes</option>' +
                '           <option value="3_4_unit_or_5_plus">3-4 Unit or 5 plus</option>' +
                '       </div>';
            $("#propertyTypeDiv").after(Field);
            $("#multiFamilyType").select2();
        } else {
            $("#multiFamilyTypeDiv").remove();
        }
    }

    function DisplayOfferRangeLowValue(e) {
        $("#offer_range_low_value").text(e.value);
    }

    function DisplayOfferRangeHighValue(e) {
        $("#offer_range_high_value").text(e.value);
    }

    function CalculateRehabCost() {
        let BuildingSize = $("#buildingSize").val();
        let conditions = $("#conditions").val();
        if (BuildingSize !== '' && conditions !== '') {
            if (conditions === 'basic') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 15).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'light') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 20).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'moderate') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 25).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'full') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 30).trigger('change');
                PerformLeadCalculations();
            } else if (conditions === 'heavy') {
                $("#rehab_cost").val(parseFloat(BuildingSize) * 35).trigger('change');
                PerformLeadCalculations();
            } else {
                $("#rehab_cost").val(null).trigger('change');
                PerformLeadCalculations();
            }
        } else {
            $("#rehab_cost").val(null).trigger('change');
            PerformLeadCalculations();
        }
    }

    function CalculateRehabCost1() {
        let BuildingSize = $("#buildingSize1").val();
        let conditions = $("#conditions1").val();
        if (BuildingSize !== '' && conditions !== '') {
            if (conditions === 'basic') {
                $("#rehab_cost1").val(parseFloat(BuildingSize) * 15);
                PerformLeadCalculations1();
            } else if (conditions === 'light') {
                $("#rehab_cost1").val(parseFloat(BuildingSize) * 20);
                PerformLeadCalculations1();
            } else if (conditions === 'moderate') {
                $("#rehab_cost1").val(parseFloat(BuildingSize) * 25);
                PerformLeadCalculations1();
            } else if (conditions === 'full') {
                $("#rehab_cost1").val(parseFloat(BuildingSize) * 30);
                PerformLeadCalculations1();
            } else if (conditions === 'heavy') {
                $("#rehab_cost1").val(parseFloat(BuildingSize) * 35);
                PerformLeadCalculations1();
            } else {
                $("#rehab_cost1").val(null);
                PerformLeadCalculations1();
            }
        } else {
            $("#rehab_cost1").val(null);
            PerformLeadCalculations1();
        }
    }

    function PerformLeadCalculations1() {
        let ARV_SALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('ARV_SALES_CLOSING_COST_CONSTANT')}}");
        let WHOLESALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('WHOLESALES_CLOSING_COST_CONSTANT')}}");
        let INVESTOR_PROFIT_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('INVESTOR_PROFIT_CONSTANT')}}");
        let OFFER_LOWER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_LOWER_RANGE_CONSTANT')}}");
        let OFFER_HIGHER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_HIGHER_RANGE_CONSTANT')}}");

        let AskingPrice = $("#askingPrice1").val();
        let ARV = $("#arv1").val();
        let AssignmentFee = $("#assignment_fee1").val();
        let RehabCost = $("#rehab_cost1").val();

        if (AskingPrice !== '' && ARV !== '' && AssignmentFee !== '' && RehabCost !== '') {
            let ARV_REHAB_COST = parseFloat(ARV) - parseFloat(RehabCost);
            ARV_REHAB_COST = Math.round(ARV_REHAB_COST * 100.0) / 100.0;
            $("#arv_rehab_cost1").val(ARV_REHAB_COST);
            let ARV_SALES_CLOSING_COST = (ARV_SALES_CLOSING_COST_CONSTANT * parseFloat(ARV)) / 100;
            ARV_SALES_CLOSING_COST = Math.round(ARV_SALES_CLOSING_COST * 100.0) / 100.0;
            $("#arv_sales_closing_cost1").val(ARV_SALES_CLOSING_COST);
            let PropertyValue = ARV_REHAB_COST - ARV_SALES_CLOSING_COST;
            PropertyValue = Math.round(PropertyValue * 100.0) / 100.0;
            $("#property_total_value1").val(PropertyValue);
            let WholeSales_Closing_Cost = (WHOLESALES_CLOSING_COST_CONSTANT * parseFloat(PropertyValue)) / 100;
            WholeSales_Closing_Cost = Math.round(WholeSales_Closing_Cost * 100.0) / 100.0;
            $("#wholesales_closing_cost1").val(WholeSales_Closing_Cost);
            let All_In_Cost = parseFloat(RehabCost) + parseFloat(PropertyValue) + parseFloat(WholeSales_Closing_Cost);
            All_In_Cost = Math.round(All_In_Cost * 100.0) / 100.0;
            $("#all_in_cost1").val(All_In_Cost);
            let InvestorProfit = (INVESTOR_PROFIT_CONSTANT * parseFloat(All_In_Cost)) / 100;
            InvestorProfit = Math.round(InvestorProfit * 100.0) / 100.0;
            $("#investor_profit1").val(InvestorProfit);
            let SalesPrice = PropertyValue - InvestorProfit;
            SalesPrice = Math.round(SalesPrice * 100.0) / 100.0;
            $("#sales_price1").val(SalesPrice);
            let MAO = SalesPrice - parseFloat(AssignmentFee);
            MAO = Math.round(MAO * 100.0) / 100.0;
            let OfferLowerRange = parseFloat(MAO) - OFFER_LOWER_RANGE_CONSTANT;
            OfferLowerRange = Math.round(OfferLowerRange * 100.0) / 100.0;
            let OfferHigherRange = parseFloat(MAO) - OFFER_HIGHER_RANGE_CONSTANT;
            OfferHigherRange = Math.round(OfferHigherRange * 100.0) / 100.0;
            $("#m_a_o1").val(MAO);
            $("#offer_range_low1").val(OfferLowerRange);
            $("#offer_range_low_value1").text(OfferLowerRange);
            $("#offer_range_high1").val(OfferHigherRange);
            $("#offer_range_high_value1").text(OfferHigherRange);
            $("#offerRangeLowValue1").text("$" + Math.round(OfferLowerRange));
            $("#offerRangeHighValue1").text("$" + Math.round(OfferHigherRange));
        } else {
            $("#arv_rehab_cost1").val(null);
            $("#arv_sales_closing_cost1").val(null);
            $("#property_total_value1").val(null);
            $("#wholesales_closing_cost1").val(null);
            $("#all_in_cost1").val(null);
            $("#investor_profit1").val(null);
            $("#sales_price1").val(null);
            $("#m_a_o1").val(null);
            $("#offer_range_low1").val(1);
            $("#offer_range_low_value1").text(1);
            $("#offer_range_high1").val(100);
            $("#offer_range_high_value1").text(100);
        }
    }

    function PerformLeadCalculations() {
        let ARV_SALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('ARV_SALES_CLOSING_COST_CONSTANT')}}");
        let WHOLESALES_CLOSING_COST_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('WHOLESALES_CLOSING_COST_CONSTANT')}}");
        let INVESTOR_PROFIT_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('INVESTOR_PROFIT_CONSTANT')}}");
        let OFFER_LOWER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_LOWER_RANGE_CONSTANT')}}");
        let OFFER_HIGHER_RANGE_CONSTANT = parseFloat("{{\App\Helpers\SiteHelper::GetConstantValue('OFFER_HIGHER_RANGE_CONSTANT')}}");

        let AskingPrice = $("#askingPrice").val();
        let ARV = $("#arv").val();
        let AssignmentFee = $("#assignment_fee").val();
        let RehabCost = $("#rehab_cost").val();

        if (AskingPrice !== '' && ARV !== '' && AssignmentFee !== '' && RehabCost !== '') {
            let ARV_REHAB_COST = parseFloat(ARV) - parseFloat(RehabCost);
            ARV_REHAB_COST = Math.round(ARV_REHAB_COST * 100.0) / 100.0;
            $("#arv_rehab_cost").val(ARV_REHAB_COST);
            let ARV_SALES_CLOSING_COST = (ARV_SALES_CLOSING_COST_CONSTANT * parseFloat(ARV)) / 100;
            ARV_SALES_CLOSING_COST = Math.round(ARV_SALES_CLOSING_COST * 100.0) / 100.0;
            $("#arv_sales_closing_cost").val(ARV_SALES_CLOSING_COST);
            let PropertyValue = ARV_REHAB_COST - ARV_SALES_CLOSING_COST;
            PropertyValue = Math.round(PropertyValue * 100.0) / 100.0;
            $("#property_total_value").val(PropertyValue);
            let WholeSales_Closing_Cost = (WHOLESALES_CLOSING_COST_CONSTANT * parseFloat(PropertyValue)) / 100;
            WholeSales_Closing_Cost = Math.round(WholeSales_Closing_Cost * 100.0) / 100.0;
            $("#wholesales_closing_cost").val(WholeSales_Closing_Cost);
            let All_In_Cost = parseFloat(RehabCost) + parseFloat(PropertyValue) + parseFloat(WholeSales_Closing_Cost);
            All_In_Cost = Math.round(All_In_Cost * 100.0) / 100.0;
            $("#all_in_cost").val(All_In_Cost);
            let InvestorProfit = (INVESTOR_PROFIT_CONSTANT * parseFloat(All_In_Cost)) / 100;
            InvestorProfit = Math.round(InvestorProfit * 100.0) / 100.0;
            $("#investor_profit").val(InvestorProfit);
            let SalesPrice = PropertyValue - InvestorProfit;
            SalesPrice = Math.round(SalesPrice * 100.0) / 100.0;
            $("#sales_price").val(SalesPrice);
            let MAO = SalesPrice - parseFloat(AssignmentFee);
            MAO = Math.round(MAO * 100.0) / 100.0;
            let OfferLowerRange = parseFloat(MAO) - OFFER_LOWER_RANGE_CONSTANT;
            OfferLowerRange = Math.round(OfferLowerRange * 100.0) / 100.0;
            let OfferHigherRange = parseFloat(MAO) - OFFER_HIGHER_RANGE_CONSTANT;
            OfferHigherRange = Math.round(OfferHigherRange * 100.0) / 100.0;
            $("#m_a_o").val(MAO);
            $("#offer_range_low").val(OfferLowerRange);
            $("#offer_range_low_value").text(OfferLowerRange);
            $("#offer_range_high").val(OfferHigherRange);
            $("#offer_range_high_value").text(OfferHigherRange);
            $("#offerRangeLowValue").text("$" + Math.round(OfferLowerRange));
            $("#offerRangeHighValue").text("$" + Math.round(OfferHigherRange));
        } else {
            /*Reset All Fields*/
            $("#arv_rehab_cost").val(null);
            $("#arv_sales_closing_cost").val(null);
            $("#property_total_value").val(null);
            $("#wholesales_closing_cost").val(null);
            $("#all_in_cost").val(null);
            $("#investor_profit").val(null);
            $("#sales_price").val(null);
            $("#m_a_o").val(null);
            $("#offer_range_low").val(1);
            $("#offer_range_low_value").text(1);
            $("#offer_range_high").val(100);
            $("#offer_range_high_value").text(100);
        }
    }

    function LeadEvaluationModal(id) {
        <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 3) {
                $Url = url('acquisition_manager/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 4) {
                $Url = url('disposition_manager/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 5) {
                $Url = url('acquisition_representative/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
                $Url = url('disposition_representative/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 7) {
                $Url = url('cold_caller/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 8) {
                $Url = url('affiliate/leads/details');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 9) {
                $Url = url('realtor/leads/details');
            }
            ?>
        id = id.split('_')[1];
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                Id: id
            }
        }).done(function (data) {
            data = JSON.parse(data);
            if(parseFloat(Math.round(data[0].offer_range_low)) < 0){
                $("#offer_range_low_value").text("$" + Math.round(data[0].offer_range_low)).attr('style', 'color: red !important');
            }
            else{
                $("#offer_range_low_value").text("$" + Math.round(data[0].offer_range_low)).attr('style', 'color: black !important');
            }

            if(parseFloat(Math.round(data[0].offer_range_high)) < 0){
                $("#offer_range_high_value").text("$" + Math.round(data[0].offer_range_high)).attr('style', 'color: red !important');
            }
            else{
                $("#offer_range_high_value").text("$" + Math.round(data[0].offer_range_high)).attr('style', 'color: black !important');
            }

            if(parseFloat(data[0].asking_price) < 0){
                $("#askingPrice").val(data[0].asking_price).attr('style', 'background-color: white !important');
            }
            else{
                $("#askingPrice").val(data[0].asking_price).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].arv) < 0){
                $("#arv").val(data[0].arv).attr('style', 'background-color: red !important');
            }
            else{
                $("#arv").val(data[0].arv).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].assignment_fee) < 0){
                $("#assignment_fee").val(data[0].assignment_fee).attr('style', 'background-color: white !important');
            }
            else{
                $("#assignment_fee").val(data[0].assignment_fee).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].rehab_cost) < 0){
                $("#rehab_cost").val(data[0].rehab_cost).attr('style', 'background-color: white !important');
            }
            else{
                $("#rehab_cost").val(data[0].rehab_cost).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].arv_rehab_cost) < 0){
                $("#arv_rehab_cost").val(data[0].arv_rehab_cost).attr('style', 'background-color: white !important');
            }
            else{
                $("#arv_rehab_cost").val(data[0].arv_rehab_cost).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].arv_sales_closingcost) < 0){
                $("#arv_sales_closing_cost").val(data[0].arv_sales_closingcost).attr('style', 'background-color: white !important');
            }
            else{
                $("#arv_sales_closing_cost").val(data[0].arv_sales_closingcost).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].property_total_value) < 0){
                $("#property_total_value").val(data[0].property_total_value).attr('style', 'background-color: white !important');
            }
            else{
                $("#property_total_value").val(data[0].property_total_value).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].wholesales_closing_cost) < 0){
                $("#wholesales_closing_cost").val(data[0].wholesales_closing_cost).attr('style', 'background-color: white !important');
            }
            else{
                $("#wholesales_closing_cost").val(data[0].wholesales_closing_cost).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].all_in_cost) < 0){
                $("#all_in_cost").val(data[0].all_in_cost).attr('style', 'background-color: white !important');
            }
            else{
                $("#all_in_cost").val(data[0].all_in_cost).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].investor_profit) < 0){
                $("#investor_profit").val(data[0].investor_profit).attr('style', 'background-color: white !important');
            }
            else{
                $("#investor_profit").val(data[0].investor_profit).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].sales_price) < 0){
                $("#sales_price").val(data[0].sales_price).attr('style', 'background-color: white !important');
            }
            else{
                $("#sales_price").val(data[0].sales_price).attr('style', 'background-color: white !important');
            }

            if(parseFloat(data[0].maximum_allow_offer) < 0){
                $("#m_a_o").val(data[0].maximum_allow_offer).attr('style', 'background-color: white !important');
            }
            else{
                $("#m_a_o").val(data[0].maximum_allow_offer).attr('style', 'background-color: white !important');
            }
            $("#leadEvaluationModal").modal('toggle');
        });
    }

    /*New Script File Ends Here*/

    // Refresh Dashboard Leads
    setInterval(function() {
      MakeDashboardLeadsTable();
    }, 60000);

    function checkMaritalStatus() {
        let marital_status = $('#martial_status option:selected').val();
        if (marital_status === 'married') {
            $('#_SpouceBlock').show();
        } else {
            $('#_SpouceBlock').hide();
        }
    }

    function checkTaskMaritalStatus(index) {
        let marital_status = $('#martial_status' + index + ' option:selected').val();
        if (marital_status === 'married') {
            $('#_SpouceBlock' + index).show();
        } else {
            $('#_SpouceBlock' + index).hide();
        }
    }

    function ResetEndDateFilter() {
      $("#endDateFilter").val('');
      $("#_endDateFilter").val('');
    }

    /* Admin Users Section - Start */
    function addDashes(id) {
        let f_val = $('#' + id).val();
        f_val = f_val.replace(/\D[^\.]/g, "");
        let value = f_val.slice(0, 3) + "-" + f_val.slice(3, 6) + "-" + f_val.slice(6);
        $('#' + id).val(value);
    }

    function updatePhoneDashes(id) {
        let f_val = $('#' + id).val();
        f_val = f_val.replace(/-/g, '');
        f_val = f_val.replace(/\D[^\.]/g, "");
        let value = f_val.slice(0, 3) + "-" + f_val.slice(3, 6) + "-" + f_val.slice(6);
        $('#' + id).val(value);
    }

    function ShowSecondaryEmail() {
        $("#SecondaryEmailField").show();
    }

    function HideSecondaryEmail() {
        $("#SecondaryEmailField").hide();
    }



    //delte expense
    function deleteExpense(e) {
        let id = e.split('_')[1];
        $("#deleteExpenseId").val(id);
        $("#deleteExpensModal").modal('toggle');
    }
    //end delete expense

    //edit expense
    function editExpenses(e) {
            <?php
            $Url = '';
            if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
                $Url = url('admin/edit/expenses');
            } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
                $Url = url('global_manager/edit/expenses');
            }
            ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    //end

    // Product Datatable
    function MakeProductTable() {
        if ($("#admin_product_table").length) {
            $("#admin_product_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/products/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'action', orderable: false},
                ],
            });
        }
    }

    // end product table

    //Expense Datatable
    function checkExpenseCurrency() {
        let Currency = $("#currency option:selected").val();
        if (Currency == "USD") {
            $("#_currencyNameBlock").hide();
            $("#_exchangeRateBlock").hide();
        } else if (Currency == "Others") {
            $("#_currencyNameBlock").show();
            $("#_exchangeRateBlock").show();
        }
    }

    function MakeExpenseTable() {
        if ($("#admin_expense_table").length) {
            $("#admin_expense_table").dataTable().fnDestroy();
            let StartDate = $("#expense_start_date").val();
            let EndDate = $("#expense_end_date").val();
            $("#admin_expense_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/expenses/all')}}",
                    "type": "POST",
                    "data": {
                        "StartDate": StartDate,
                        "EndDate": EndDate,
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'description'},
                    {data: 'total'},
                    {data: 'expense_date'},
                    {data: 'vendor'},
                    {data: 'location'},
                    {data: 'note'},
                    {data: 'action', orderable: false},
                ],
            });
        }
    }

    // End expense table

    function MakeUsersTable() {
        if ($("#admin_users_table").length) {
            let city = $("#filter_city option:selected").val();;
            let state = $("#filter_state option:selected").val();
            let status = $("#filter_status option:selected").val();
            let role = $("#filter_role option:selected").val();
            $("#admin_users_table").dataTable().fnDestroy();
            $("#admin_users_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/user/all')}}",
                    "type": "POST",
                    "data": {
                      'city': city,
                      'state': state,
                      'status': status,
                      'role': role
                    }
                },
                'columns': [
                    {data: 'checkbox', orderable: false},
                    {data: 'id'},
                    {data: 'user_information'},
                    {data: 'contact'},
                    {data: 'status'},
                    {data: 'action', orderable: false},
                ],
                'order': [1, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).on('page.dt', function () {
                $("#allUsersCheckBox").prop('checked', false);
                $("#deleteAllUsersBtn").hide();
                $("#upgradeAllUsersBtn").hide();
                $("#broadcastAllUsersBtn").hide();
            }).on('length.dt', function (e, settings, len) {
                $("#allUsersCheckBox").prop('checked', false);
                $("#deleteAllUsersBtn").hide();
                $("#upgradeAllUsersBtn").hide();
                $("#broadcastAllUsersBtn").hide();
            }).on('draw.dt', function () {
                $(".allUsersCheckBox").addClass('d-none');
                $(".allUsersActionCheckBoxColumn").addClass('w-0');
            });
        }
    }

    /* User Filter - Start */
    function UserFilterBackButton() {
        $("#filterPage").show();
        $("#beforeTablePage").removeClass("col-md-1");
        $("#filterPage").removeClass("col-md-1");
        $("#filterPage").addClass("col-md-2");
    }

    function UserFilterCloseButton() {
        $("#filterPage").hide();
        $("#beforeTablePage").addClass("col-md-1");
        $("#filterPage").removeClass("col-md-2");
        $("#filterPage").addClass("col-md-1");
    }
    /* User Filter - End */

    /* User action in bulk (Delete, Upgrade, Broadcast) - Start */
    let userActionCheckboxCounter = 0;
    function HandleUserAction() {
      if (userActionCheckboxCounter === 0) {
        $(".allUsersCheckBox").removeClass('d-none');
        $(".allUsersActionCheckBoxColumn").removeClass('w-0');
        $(".allUsersActionCheckBoxColumn").attr('style', 'padding', '10px');
        userActionCheckboxCounter = 1;
      } else {
        $(".allUsersCheckBox").addClass('d-none');
        $(".allUsersActionCheckBoxColumn").addClass('w-0');
        $(".allUsersActionCheckBoxColumn").attr('style', 'padding', '0');
        $("#deleteAllUsersBtn").hide();
        $("#upgradeAllUsersBtn").hide();
        $("#broadcastAllUsersBtn").hide();
        userActionCheckboxCounter = 0;
      }
    }

    function CheckAllUserRecords(e) {
        let Status = $(e).prop('checked');
        if(Status){
            /*check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', true);
            });
            $("#deleteAllUsersBtn").show();
            $("#upgradeAllUsersBtn").show();
            $("#broadcastAllUsersBtn").show();
        }
        else{
            /*un check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', false);
            });
            $("#deleteAllUsersBtn").hide();
            $("#upgradeAllUsersBtn").hide();
            $("#broadcastAllUsersBtn").hide();
        }
    }

    function CheckIndividualUserCheckbox() {
        let count = 0;
        $(".checkAllBox").each(function (i, obj) {
            if($(obj).prop('checked')){
                count++;
            }
        });
        if(count === 0){
            /*Not Selected*/
            $("#deleteAllUsersBtn").hide();
            $("#upgradeAllUsersBtn").hide();
            $("#broadcastAllUsersBtn").hide();
        }
        else{
            /*Some Selected*/
            $("#deleteAllUsersBtn").show();
            $("#upgradeAllUsersBtn").show();
            $("#broadcastAllUsersBtn").show();
        }
    }

    function DeleteMultipleUsers() {
        $("#deleteUserModal").modal('toggle');
        let deleteSelectedUsersFormUrl = $("#deleteSelectedUsersFormUrl").val();
        $('#usersForm').attr('action', deleteSelectedUsersFormUrl);
    }

    function UpgradeMultipleUsers() {
        $("#upgradeUserAccountModal").modal('toggle');
        let upgradeSelectedUsersFormUrl = $("#upgradeSelectedUsersFormUrl").val();
        $('#usersForm').attr('action', upgradeSelectedUsersFormUrl);
    }

    function BroadcastMultipleUsers() {
        $("#userBroadcastModal").modal('toggle');
        let broadcastSelectedUsersFormUrl = $("#broadcastSelectedUsersFormUrl").val();
        $('#usersForm').attr('action', broadcastSelectedUsersFormUrl);
    }
    /* User action in bulk (Delete, Upgrade, Broadcast) - End */

    function CheckForUserState(id) {
        let Role = $("#role option:selected").val();
        let State = $("#state option:selected").val();
        if(Role !== '' && State !== ''){
            if(Role === '3' || Role === '4'){
                <?php
                $Url = url('/user/state/check');
                ?>
                $.ajax({
                    type: "post",
                    url: "{{$Url}}",
                    data: { State: State, Role : Role}
                }).done(function (data) {
                    if(data === 'failed'){
                        /*User Already Exists in the given state*/
                        if(Role === '3'){
                            $("#error-alert").html('<b>' + State + ' have a Acquisition Manager assigned to it.Please change the role or the location.' + '</b>').show();
                        }
                        else if(Role === '4'){
                            $("#error-alert").html('<b>' + State + ' have a Disposition Manager assigned to it.Please change the role or the location.' + '</b>').show();
                        }
                        $("#" + id).attr('disabled', true);
                    }
                    else{
                        /*New User in state*/
                        $("#error-alert").html('').hide();
                        $("#" + id).attr('disabled', false);
                    }
                });
            }
            else{
                $("#error-alert").html('').hide();
                $("#" + id).attr('disabled', false);
            }
        }
        else{
            $("#error-alert").html('').hide();
            $("#" + id).attr('disabled', false);
        }
    }

    function CheckForUserState2(id) {
        let SelectedRole = "";
        let SelectedState = "";
            <?php
            if (isset($user_details)) {
                ?>
                SelectedRole = "<?php echo $user_details[0]->role; ?>";
                SelectedState = "<?php echo $user_details[0]->state; ?>";
                <?php
            }
            ?>
        let Role = $("#role option:selected").val();
        let State = $("#state option:selected").val();
        if(Role === SelectedRole && State === SelectedState){
            return;
        }
        if(Role !== '' && State !== ''){
            if(Role === '3' || Role === '4'){
                <?php
                $Url = url('/user/state/check');
                ?>
                $.ajax({
                    type: "post",
                    url: "{{$Url}}",
                    data: { State: State, Role : Role}
                }).done(function (data) {
                    if(data === 'failed'){
                        /*User Already Exists in the given state*/
                        if(Role === '3'){
                            $("#error-alert").html('<b>' + State + ' have a Acquisition Manager assigned to it.Please change the role or the location.' + '</b>').show();
                        }
                        else if(Role === '4'){
                            $("#error-alert").html('<b>' + State + ' have a Disposition Manager assigned to it.Please change the role or the location.' + '</b>').show();
                        }
                        $("#" + id).attr('disabled', true);
                    }
                    else{
                        /*New User in state*/
                        $("#error-alert").html('').hide();
                        $("#" + id).attr('disabled', false);
                    }
                });
            }
            else{
                $("#error-alert").html('').hide();
                $("#" + id).attr('disabled', false);
            }
        }
        else{
            $("#error-alert").html('').hide();
            $("#" + id).attr('disabled', false);
        }
    }

    function ShowPhone2() {
        $("#phone2Field").css('display', 'initial');
    }

    function HidePhone2() {
        $("#phone2Field").css('display', 'none');
    }


    function ShowEmail2() {
        $("#email2").css('display', 'initial');
    }

    function HideEmail2() {
        $("#email2").css('display', 'none');
        $("#secondary_email").val('');
    }

    if($('#secondary_email').val())
    {
        $("#email2").css('display', 'initial');
    }

    function MakeUsersProgressTable() {
        if ($("#users_progress_table").length) {
            $("#users_progress_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/users/progress/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'role'},
                    {data: 'userId'},
                    {data: 'name'},
                    {data: 'progress', orderable: false}
                ],
                'order': [0, 'desc']
            });
        }
    }

    function deleteUser(e) {
        let id = e.split('_')[1];
        $("#deleteUserId").val(id);
        $("#deleteUserModal").modal('toggle');
    }

    function editUser(e) {
        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/edit/user');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/edit/user');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 3) {
            $Url = url('acquisition_manager/edit/user');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 4) {
            $Url = url('disposition_manager/edit/user');
        }
        ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    function editInvestor(e) {
        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/investor/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/investor/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
            $Url = url('disposition_representative/investor/edit');
        }
        ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    function editRealtor(e) {
        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/realtor/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/realtor/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
            $Url = url('disposition_representative/realtor/edit');
        }
        ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    function UpgradeUserAccount(e) {
      let id = e.split('_')[1];
      $("#upgradeUserAccountId").val(id);
      $("#upgradeUserAccountModal").modal('toggle');
    }

    //edit product
    function editProduct(e) {
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{url('admin/edit/product')}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    //end

    function editManagerUser(e) {
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{url('global_manager/edit/user')}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    function ChangePassword(e) {
        let Id = e.id.split('_')[1];
        $("#user_id").val(Id);
        $("#changePasswordError").hide();
        $("#changePasswordModal").modal('toggle');
    }

    function LoadStateCountyCity() {
      let state = '';
      if ($("#state").length) {
          state = $("#state option:selected").val();
      }
      if ($("#filter_state").length) {
          state = $("#filter_state option:selected").val();
          if (state !== '') {
              $("#filterCityBlock").show();
          } else {
              $("#filterCityBlock").hide();
          }
      }
      if ($("#county").length) {
        LoadCounties(state);
      }
      if ($("#AddLeadCitySection").length) {
        $("#AddLeadCitySection").show();
      }
      LoadCities(state);
    }

    function LoadFilterStateCountyCity() {
        let state = $("#stateFilter option:selected").val();
        if ($("#county").length) {
            LoadCounties(state);
        }
        LoadCities(state);
    }

    function CheckLeadFilterState(e) {
      if (e.value === '0') {
        $("#_leadFilterCityBlock").hide();
      } else {
        $("#_leadFilterCityBlock").show();
      }
    }

    function LoadCounties(state) {
      <?php
        $Url = url('load/counties');
      ?>
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {State: state}
      }).done(function (data) {
          data = JSON.parse(data);
          if ($("#county").length) {
            $("#county").html('').html(data);
          }
          if ($("#countyFilter").length) {
            $("#countyFilter").html('').html(data);
          }
      });
    }

    function LoadCities(state) {
      <?php
        $Url = url('load/cities');
      ?>
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {State: state}
      }).done(function (data) {
          data = JSON.parse(data);
          if ($("#city").length) {
            $("#city").html('').html(data);
          }
          if ($("#cityFilter").length) {
            $("#cityFilter").html('').html(data);
          }
          if ($("#filter_city").length) {
            $("#filter_city").html('').html(data);
          }
      });
    }

    function LoadServingLocationStateCountyCity(id) {
      let values = id.split("_");
      let state = $("#state_"+values[1]+" option:selected").val();
      LoadServingLocationCounties(state,values[1]);
      LoadServingLocationCities(state,values[1]);
    }

    function LoadServingLocationCounties(state, id) {
      <?php
        $Url = url('load/counties');
      ?>
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {State: state}
      }).done(function (data) {
          data = JSON.parse(data);
          $("#county_"+id).html('').html(data);
      });
    }

    function LoadServingLocationCities(state, id) {
      <?php
        $Url = url('load/cities');
      ?>
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {State: state}
      }).done(function (data) {
          data = JSON.parse(data);
          $("#city_"+id).html('').html(data);
      });
    }

    /* Admin Users Section - End */

    /* Admin Buissness Account Section - Start */

    function deleteBuissnessAccount(e) {
        let id = e.split('_')[1];
        $("#deleteBuissnessAccountId").val(id);
        $("#deleteBuissnessAccountModal").modal('toggle');
    }

    function editBuissnessAccount(e) {
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{url('admin/buissnessaccount/edit')}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    /* Admin Buissness Account Section - End */

    /* Manager Teams Section - Start */
    function checkTeamType() {
        let TeamType = $("#addTeamType option:selected").val();
        if (TeamType === '') {
            $('#addTeamSubmitButton').prop('disabled', true);
            $("#_confirmationAgentBlock").hide();
            $("#_representativeBlock").hide();
            $("#_supervisorBlock").hide();
            $("#_confirmationAgentSupervisorBlock").hide();
        } else if (TeamType === '1') {
            $("#_representativeBlock").show();
            $("#_confirmationAgentSupervisorBlock").hide();
            $("#_confirmationAgentBlock").hide();
            $("#_supervisorBlock").show();
            $('#addTeamSubmitButton').prop('disabled', false);
        } else if (TeamType === '2') {
            $("#_confirmationAgentSupervisorBlock").show();
            $("#_confirmationAgentBlock").show();
            $("#_representativeBlock").hide();
            $("#_supervisorBlock").hide();
            $('#addTeamSubmitButton').prop('disabled', false);
        }
    }

    function MakeTeamsTable() {
        if ($("#admin_teams_table").length) {
            $("#admin_teams_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/teams/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'team_type'},
                    {data: 'title'},
                    {data: 'team_supervisor'},
                    {data: 'team_confirmationagentSupervisor'},
                    {data: 'members'},
                    {data: 'created_at'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function EditTeam(id) {
        window.open('{{url('admin/teams/edit')}}' + "/" + id.split('_')[1], '_self');
    }

    function EditManagerTeam(id) {
        window.open('{{url('global_manager/teams/edit')}}' + "/" + id.split('_')[1], '_self');
    }

    function DeleteTeam(id) {
        $("#deleteTeamId").val(id.split('_')[1]);
        $("#deleteTeamModal").modal('toggle');
    }

    /* Manager Teams Section - End */

    /* Supervisor Teams Section - Start */
    function MakeSupervisorTeamsTable() {
        if ($("#supervisor_teams_table").length) {
            $("#supervisor_teams_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('supervisor/teams/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'title'},
                    {data: 'team_supervisor'},
                    {data: 'team_confirmationagentSupervisor'},
                    {data: 'members'},
                    {data: 'created_at'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    /* Supervisor Teams Section - End */

    /* Lead - Start */
    function LeadSearch(value) {
        $("#customRangeStartDate").hide();
        $("#customRangeEndDate").hide();

        if(value === 'followUp'){
            $("#tomorrowSearchOption").show();
            $("#nextWeekSearchOption").show();
            $("#nextMonthSearchOption").show();
            $("#leadSearchDiv")
                .html(' <label for="searchBy">Search By</label>' +
                '       <select name="searchBy" id="searchBy" class="form-control" onchange="CalculateDates(this.value);">' +
                '           <option value="0">Select</option>' +
                '           <option value="customRange">Custom Range</option>' +
                '           <option value="yesterday">Yesterday</option>' +
                '           <option value="today">Today</option>' +
                '           <option value="tomorrow">Tomorrow</option>' +
                '           <option value="lastWeek">Last Week</option>' +
                '           <option value="currentWeek">Current Week</option>' +
                '           <option value="nextWeek">Next Week</option>' +
                '           <option value="lastMonth">Last Month</option>' +
                '           <option value="currentMonth">Current Month</option>' +
                '           <option value="nextMonth">Next Month</option>' +
                '           <option value="lastYear">Last Year</option>' +
                '           <option value="CurrentYear">Current Year</option>' +
                '       </select>').show();
            $("#searchBy").select2();
        }
        else if(value === 'creationDate'){
            $("#tomorrowSearchOption").hide();
            $("#nextWeekSearchOption").hide();
            $("#nextMonthSearchOption").hide();
            $("#leadSearchDiv")
                .html(' <label for="searchBy">Search By</label>' +
                    '       <select name="searchBy" id="searchBy" class="form-control" onchange="CalculateDates(this.value);">' +
                    '           <option value="0">Select</option>' +
                    '           <option value="customRange">Custom Range</option>' +
                    '           <option value="yesterday">Yesterday</option>' +
                    '           <option value="today">Today</option>' +
                    '           <option value="lastWeek">Last Week</option>' +
                    '           <option value="currentWeek">Current Week</option>' +
                    '           <option value="lastMonth">Last Month</option>' +
                    '           <option value="currentMonth">Current Month</option>' +
                    '           <option value="lastYear">Last Year</option>' +
                    '           <option value="CurrentYear">Current Year</option>' +
                    '       </select>').show();
            $("#searchBy").select2();
        }
        else{
            $("#leadSearchDiv").hide();
            /*$("#searchStartDate").val('');
            $("#searchEndDate").val('');*/
        }
    }

    function CalculateDates(value) {
        $("#customRangeStartDate").hide();
        $("#customRangeEndDate").hide();

        if(value === 'customRange'){
            $("#customRangeStartDate").show();
            $("#customRangeEndDate").show();
        }
        else if(value === 'yesterday'){
            let today = new Date();
            let yesterday = new Date();
            yesterday.setDate(today. getDate() - 1);
            yesterday = yesterday.getFullYear() + "-" + parseInt(yesterday.getMonth() + 1) + "-" + yesterday.getDate();
            $("#searchStartDate").val(yesterday);
        }
        else if(value === 'today'){
            let today = new Date();
            today = today.getFullYear() + "-" + parseInt(today.getMonth() + 1) + "-" + today.getDate();
            $("#searchStartDate").val(today);
        }
        else if(value === 'tomorrow'){
            let today = new Date();
            let tomorrow = new Date();
            tomorrow.setDate(today. getDate() + 1);
            tomorrow = tomorrow.getFullYear() + "-" + parseInt(tomorrow.getMonth() + 1) + "-" + tomorrow.getDate();
            $("#searchStartDate").val(tomorrow);
        }
        else if(value === 'lastWeek'){
            let today=new Date();
            let Monday = new Date();
            Monday.setDate(today.getDate() - today.getDay() - 6);
            let Sunday = new Date();
            Sunday.setDate(today.getDate() - today.getDay() - 7 + 7);
            Monday = Monday.getFullYear() + "-" + parseInt(Monday.getMonth() + 1) + "-" + Monday.getDate();
            Sunday = Sunday.getFullYear() + "-" + parseInt(Sunday.getMonth() + 1) + "-" + Sunday.getDate();
            $("#searchStartDate").val(Monday);
            $("#searchEndDate").val(Sunday);
        }
        else if(value === 'currentWeek'){
            let d = new Date();
            let day = d.getDay(), diff = d.getDate() - day + (day === 0? -6:1); // adjust when day is sunday
            let Monday = new Date(d.setDate(diff));
            let Sunday = new Date();
            Sunday.setDate(Monday.getDate() - Monday.getDay() + 7);
            Monday = Monday.getFullYear() + "-" + parseInt(Monday.getMonth() + 1) + "-" + Monday.getDate();
            Sunday = Sunday.getFullYear() + "-" + parseInt(Sunday.getMonth() + 1) + "-" + Sunday.getDate();
            $("#searchStartDate").val(Monday);
            $("#searchEndDate").val(Sunday);
        }
        else if(value === 'nextWeek'){
            let Monday = new Date();
            let day = 1; // Monday
            if (day > 6 || day < 0)
                day = 0;
            while (Monday.getDay() !== day) {
                Monday.setDate(Monday.getDate() + 1);
            }
            let Sunday = new Date();
            Sunday.setDate(Monday.getDate() - Monday.getDay() + 7);
            Monday = Monday.getFullYear() + "-" + parseInt(Monday.getMonth() + 1) + "-" + Monday.getDate();
            Sunday = Sunday.getFullYear() + "-" + parseInt(Sunday.getMonth() + 1) + "-" + Sunday.getDate();
            $("#searchStartDate").val(Monday);
            $("#searchEndDate").val(Sunday);
        }
        else if(value === 'lastMonth'){
            let today = new Date();
            let FirstDate = new Date(today);
            let LastDate = new Date(today);
            if(FirstDate.getMonth() === 0){
                FirstDate = new Date(FirstDate.getFullYear() - 1, 11, 1);
            }
            else{
                FirstDate = new Date(FirstDate.getFullYear(), FirstDate.getMonth() - 1, 1);
            }
            LastDate.setMonth(LastDate.getMonth(), 0);
            FirstDate = FirstDate.getFullYear() + "-" + parseInt(FirstDate.getMonth() + 1) + "-" + FirstDate.getDate();
            LastDate = LastDate.getFullYear() + "-" + parseInt(LastDate.getMonth() + 1) + "-" + LastDate.getDate();
            $("#searchStartDate").val(FirstDate);
            $("#searchEndDate").val(LastDate);
        }
        else if(value === 'currentMonth'){
            let today = new Date();
            let FirstDate = new Date(today);
            let LastDate = new Date(today);
            FirstDate = new Date(FirstDate.getFullYear(), FirstDate.getMonth(), 1);
            LastDate.setMonth(LastDate.getMonth() + 1, 0);
            FirstDate = FirstDate.getFullYear() + "-" + parseInt(FirstDate.getMonth() + 1) + "-" + FirstDate.getDate();
            LastDate = LastDate.getFullYear() + "-" + parseInt(LastDate.getMonth() + 1) + "-" + LastDate.getDate();
            $("#searchStartDate").val(FirstDate);
            $("#searchEndDate").val(LastDate);
        }
        else if(value === 'nextMonth'){
            let today = new Date();
            let FirstDate = new Date(today);
            let LastDate = new Date(today);
            FirstDate = new Date(FirstDate.getFullYear(), FirstDate.getMonth() + 1, 1);
            LastDate.setMonth(LastDate.getMonth() + 2, 0);
            FirstDate = FirstDate.getFullYear() + "-" + parseInt(FirstDate.getMonth() + 1) + "-" + FirstDate.getDate();
            LastDate = LastDate.getFullYear() + "-" + parseInt(LastDate.getMonth() + 1) + "-" + LastDate.getDate();
            $("#searchStartDate").val(FirstDate);
            $("#searchEndDate").val(LastDate);
        }
        else if(value === 'lastYear'){
            let today = new Date();
            let FirstDate = new Date(today);
            let LastDate = new Date(today);
            FirstDate = new Date(FirstDate.getFullYear() -1, 0, 1);
            LastDate = new Date(LastDate.getFullYear()-1, 12, 0);
            FirstDate = FirstDate.getFullYear() + "-" + parseInt(FirstDate.getMonth() + 1) + "-" + FirstDate.getDate();
            LastDate = LastDate.getFullYear() + "-" + parseInt(LastDate.getMonth() + 1) + "-" + LastDate.getDate();
            $("#searchStartDate").val(FirstDate);
            $("#searchEndDate").val(LastDate);
        }
        else if(value === 'CurrentYear'){
            let today = new Date();
            let FirstDate = new Date(today);
            let LastDate = new Date(today);
            FirstDate = new Date(FirstDate.getFullYear(), 0, 1);
            LastDate = new Date(LastDate.getFullYear(), 12, 0);
            FirstDate = FirstDate.getFullYear() + "-" + parseInt(FirstDate.getMonth() + 1) + "-" + FirstDate.getDate();
            LastDate = LastDate.getFullYear() + "-" + parseInt(LastDate.getMonth() + 1) + "-" + LastDate.getDate();
            $("#searchStartDate").val(FirstDate);
            $("#searchEndDate").val(LastDate);
        }
    }

    function MakeLeadsTable() {
        if ($("#representative_leads_table").length) {
            $("#representative_leads_table").dataTable().fnDestroy();
            $("#representative_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/leads/all')}}",
                    "type": "POST",
                    "data": {LeadStatus: $("#statusFilter option:selected").val()}
                },
                'columns': [
                    {data: 'id'},
                    {data: 'lead_header'},
                    {data: 'seller_information'},
                    {data: 'last_comment'},
                    {data: 'follow_up'},
                    {data: 'lead_type', orderable: false},
                    {data: 'action', orderable: false},
                ],
            });
        }
    }

    function MakeAdminLeadsTable() {
        if ($("#admin_leads_table").length) {
            /*Lead Search START*/
            let SearchType = $("#leadSearch option:selected").val();
            let SearchSubType = $("#searchBy option:selected").val();
            let LeadSearchStartDate = '';
            let LeadSearchEndDate = '';
            if (typeof SearchSubType !== 'undefined') {
                if (SearchSubType === 'customRange') {
                    LeadSearchStartDate = $("#customStartDate").val();
                    LeadSearchEndDate = $("#customEndDate").val();
                } else {
                    LeadSearchStartDate = $("#searchStartDate").val();
                    LeadSearchEndDate = $("#searchEndDate").val();
                }
            }
            /*Lead Search END*/
            if ($("#appointmentDateTextFilter").val() === '') {
                $("#appointmentDateFilter").val('');
            }
            if ($("#startDateTextFilter").val() === '') {
                $("#startDateFilter").val('');
            }
            $("#admin_leads_table").dataTable().fnDestroy();
            $("#admin_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/leads/all')}}",
                    "type": "POST",
                    "data": {
                        FullName: $("#fullNameFilter").val(),
                        Phone: $("#phoneFilter").val(),
                        CityFilter: $("#cityFilter option:selected").val(),
                        StateFilter: $("#stateFilter option:selected").val(),
                        ZipcodeFilter: $("#zipcodeFilter").val(),
                        FollowUpTime: "",
                        LeadCreationDate: "",
                        Investor: JSON.stringify($("#investorFilter").val()),
                        Realtor: "",
                        TitleCompany: $("#titleCompanyFilter option:selected").val(),
                        LeadSource: JSON.stringify($("#leadSourceFilter").val()),
                        DataSource: JSON.stringify($("#dataSourceFilter").val()),
                        SearchType: SearchType,
                        SearchSubType: SearchSubType,
                        LeadSearchStartDate: LeadSearchStartDate,
                        LeadSearchEndDate: LeadSearchEndDate
                    }
                },
                'columns': [
                    {data: 'checkbox', orderable: false},
                    {data: 'id'},
                    {data: 'lead_header', orderable: false},
                    {data: 'seller_information', orderable: false},
                    {data: 'last_comment', orderable: false},
                    {data: 'appointment_time'},
                    {data: 'lead_type', orderable: false},
                    {data: 'action', orderable: false},
                ],
                'order': [5, 'desc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).on('page.dt', function () {
                $("#checkAllBox").prop('checked', false);
                $("#multipleAssignBtn").hide();
            }).on('length.dt', function (e, settings, len) {
                $("#checkAllBox").prop('checked', false);
                $("#multipleAssignBtn").hide();
            }).on('draw.dt', function () {
                  $(".assignLeadCheckBox").addClass('d-none');
                  $(".assignLeadCheckBoxColumn").addClass('w-0');
            });
        }
    }

    let assignLeadCheckboxCounter = 0;
    function HandleAssignLead() {
      if (assignLeadCheckboxCounter === 0) {
        $(".assignLeadCheckBox").removeClass('d-none');
        $(".assignLeadCheckBoxColumn").removeClass('w-0');
        $(".assignLeadCheckBoxColumn").attr('style', 'padding', '10px');
        assignLeadCheckboxCounter = 1;
      } else {
        $(".assignLeadCheckBox").addClass('d-none');
        $(".assignLeadCheckBoxColumn").addClass('w-0');
        $(".assignLeadCheckBoxColumn").attr('style', 'padding', '0');
        assignLeadCheckboxCounter = 0;
      }
    }

    /*Assign Selected Leads*/
    function CheckAllRecords(e) {
        let Status = $(e).prop('checked');
        if(Status){
            /*check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', true);
            });
            $("#multipleAssignBtn").show();
        }
        else{
            /*un check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', false);
            });
            $("#multipleAssignBtn").hide();
        }
    }

    function CheckIndividualCheckbox() {
        let count = 0;
        $(".checkAllBox").each(function (i, obj) {
            if($(obj).prop('checked')){
                count++;
            }
        });
        if(count === 0){
            /*Not Selected*/
            $("#multipleAssignBtn").hide();
        }
        else{
            /*Some Selected*/
            $("#multipleAssignBtn").show();
        }
    }

    function AssignMultiple() {
        AssignSelectedLeads();
    }

    function AssignSelectedLeads() {
        $("#__assignUsers").select2();
        $("#assignSelectedLeadModal").modal('toggle');
    }

    $('.__SelectAllUsers').on("click", function (e) {
        $(".___assignUsers > option").prop("selected", "selected").trigger("change");
    });

    $('.__RemoveAllUsers').on("click", function (e) {
        $(".___assignUsers > option").prop("selected", "").trigger("change");
    });
    /*Assign Selected Leads*/

    function DeleteLead(e) {
      let id = e.split('_')[1];
        $("#deleteLeadId").val(id);
        $("#deleteLeadModal").modal('toggle');
    }

    function ConfirmDeleteLead(state, id) {
      <?php
        $Url = url('leads/delete');
      ?>
      let LeadId = $("#deleteLeadId").val();
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {LeadId: LeadId}
      }).done(function (data) {
          $("#deleteLeadModal").modal('toggle');
          MakeAdminLeadsTable();
          MakeDashboardLeadsTable();
      });
    }
    /* Lead - End */

    function MakeDashboardLeadsTable() {
        if ($("#dashboard_leads_table").length) {
            $("#dashboard_leads_table").dataTable().fnDestroy();
            $("#dashboard_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('leads/dashboard/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'checkbox', orderable: false},
                    {data: 'id'},
                    {data: 'lead_header', orderable: false},
                    {data: 'seller_information', orderable: false},
                    {data: 'last_comment', orderable: false},
                    {data: 'appointment_time'},
                    {data: 'lead_type', orderable: false},
                    {data: 'action', orderable: false},
                ],
                'order': [5, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).on('page.dt', function () {
                $("#checkAllBox").prop('checked', false);
                $("#multipleAssignBtn").hide();
            }).on('length.dt', function (e, settings, len) {
                $("#checkAllBox").prop('checked', false);
                $("#multipleAssignBtn").hide();
            }).on('draw.dt', function () {
                  $(".assignLeadCheckBox").addClass('d-none');
                  $(".assignLeadCheckBoxColumn").addClass('w-0');
            });
        }
    }

    function checkProduct() {
        let product = $('#product option:selected').text();
        if (product === 'Solar') {
            $('#_electricbillblock').show();
            $('#_ProductDescriptionBlock').hide();
            $('#_WindowsDoorsCountBlock').hide();
            $('#_OldRoofDurationBlock').hide();
        } else if (product === 'Windows/Doors') {
            $('#_electricbillblock').hide();
            $('#_ProductDescriptionBlock').hide();
            $('#_WindowsDoorsCountBlock').show();
            $('#_OldRoofDurationBlock').hide();
        } else if (product === 'Roof') {
            $('#_electricbillblock').hide();
            $('#_ProductDescriptionBlock').hide();
            $('#_WindowsDoorsCountBlock').hide();
            $('#_OldRoofDurationBlock').show();
        } else if (product === 'Others') {
            $('#_electricbillblock').hide();
            $('#_ProductDescriptionBlock').show();
        } else {
            $('#_electricbillblock').hide();
            $('#_ProductDescriptionBlock').hide();
        }
    }

    function checkTaskProduct(index) {
        let product = $('#product' + index + ' option:selected').text();
        if (product === 'Solar') {
            $('#_electricbillblock' + index).show();
            $('#_ProductDescriptionBlock' + index).hide();
            $('#_WindowsDoorsCountBlock' + index).hide();
            $('#_OldRoofDurationBlock' + index).hide();
        } else if (product === 'Windows/Doors') {
            $('#_electricbillblock' + index).hide();
            $('#_ProductDescriptionBlock' + index).hide();
            $('#_WindowsDoorsCountBlock' + index).show();
            $('#_OldRoofDurationBlock' + index).hide();
        } else if (product === 'Roof') {
            $('#_electricbillblock' + index).hide();
            $('#_ProductDescriptionBlock' + index).hide();
            $('#_WindowsDoorsCountBlock' + index).hide();
            $('#_OldRoofDurationBlock' + index).show();
        } else if (product === 'Others') {
            $('#_electricbillblock' + index).hide();
            $('#_ProductDescriptionBlock' + index).show();
            $('#_WindowsDoorsCountBlock' + index).hide();
            $('#_OldRoofDurationBlock' + index).hide();
        } else {
            $('#_electricbillblock' + index).hide();
            $('#_ProductDescriptionBlock' + index).hide();
            $('#_WindowsDoorsCountBlock' + index).hide();
            $('#_OldRoofDurationBlock' + index).hide();
        }
    }

    function limitKeypress(event, value, maxLength) {
        if (value !== undefined && value.toString().length >= maxLength) {
            event.preventDefault();
        }
    }

    function limitZipCodeCheck() {
        let value = $('#zipcode').val();
        if (value.toString().length < 5) {
            $('#zipcode').focus();
        }
    }

    function limitServingLocationZipCodeCheck(id) {
        let value = $('#'+id).val();
        if (value.toString().length < 5) {
            $('#'+id).focus();
        }
    }

    function limitTaskZipCodeCheck(index) {
        let value = $('#zipcode' + index).val();
        if (value.toString().length < 5) {
            $('#zipcode' + index).focus();
        }
    }

    function ViewTeam(id) {
        window.open('{{url('supervisor/teams/view')}}' + "/" + id.split('_')[1], '_self');
    }

    function checkLeadStatusType() {
        let value = $('#lead_status_type option:selected').val();
        if (value === "Dispo Status") {
            $('#dispoStatusSection').show();
            $('#offerStatusSection').hide();
            $('#callStatusSection').hide();
            HideAllBlocks();
        } else if (value === "Offer Status") {
            $('#offerStatusSection').show();
            $('#dispoStatusSection').hide();
            $('#callStatusSection').hide();
            HideAllBlocks();
        } else if (value === "Call Status") {
            $('#callStatusSection').show();
            $('#offerStatusSection').hide();
            $('#dispoStatusSection').hide();
            HideAllBlocks();
        }
        else{
            $('#dispoStatusSection').hide();
            $('#offerStatusSection').hide();
            $('#callStatusSection').hide();
            $("#dispo_lead_status").val("").trigger('change');
            $("#offer_lead_status").val("").trigger('change');
            $("#call_lead_status").val("").trigger('change');
            HideAllBlocks();
        }
    }

    function HideAllBlocks() {
        $("#sendToInvestor").hide();
        $("#sendToTitle").hide();
        $("#emdReceived").hide();
        $("#emdNotReceived").hide();
        $("#_inspectionPeriodBlock").hide();
        $("#_inspectionNumberofDaysBlock").hide();
        $("#closedOn").hide();
        $("#closedWon").hide();
        $("#agreementReceived").hide();
        $("#notInterested").hide();
        $("#interested").hide();
        $("#generalComments").hide();
        $('#_followUpBlock').hide();
        $('#closedWonCost').hide();
        $('#sendContracttoInvestorPurchaseAmount').hide();
        $("#notInterestedCommentsMessage").hide();
        $("#interestedCommentsMessage").hide();
        $("#__commentsMessage").hide();
    }

    function checkDispoLeadStatus() {
        let value = $('#dispoStatusSection option:selected').val();
        if (value === '13') {
            HideAllBlocks();
            $("#sendToInvestor").show();
            $("#investorUsers").select2();
        } else if (value === '14') {
            HideAllBlocks();
            $("#_followUpBlock").show();
            $("#generalComments").show();
        } else if (value === '15') {
            HideAllBlocks();
            $("#sendToTitle").show();
            $("#companyUsers").select2();
        } else if (value === '16') {
            HideAllBlocks();
            $("#sendToInvestor").show();
            $("#investorUsers").select2();
            $('#sendContracttoInvestorPurchaseAmount').show();
        } else if (value === '17') {
            HideAllBlocks();
            $("#emdReceived").show();
        } else if (value === '18') {
            HideAllBlocks();
            $("#emdNotReceived").show();
            $("#_followUpBlock").show();
            $("#generalComments").show();
        } else if (value === '19') {
            HideAllBlocks();
        } else if (value === '20') {
            HideAllBlocks();
        } else if (value === '21') {
            HideAllBlocks();
            GetLeadClosedDate();
            $("#closedWon").show();
            $("#closedWonCost").show();
        } else if (value === '22') {
            HideAllBlocks();
            $("#generalComments").show();
        } else if (value === '24') {
            HideAllBlocks();
            $("#_inspectionPeriodBlock").show();
            $("#inspectionperiod").select2();
        } else if (value === '25') {
            HideAllBlocks();
            $("#closedOn").show();
        } else{
            HideAllBlocks();
        }
    }

    function checkOfferLeadStatus() {
        let value = $('#offer_lead_status option:selected').val();
        if (value === '7') {
            HideAllBlocks();
            $("#generalComments").show();
        } else if (value === '8') {
            HideAllBlocks();
            $("#generalComments").show();
        } else if (value === '9') {
            HideAllBlocks();
        } else if (value === '10') {
            HideAllBlocks();
            $("#generalComments").show();
        } else if (value === '11') {
            HideAllBlocks();
        } else if (value === '12') {
            HideAllBlocks();
            $("#agreementReceived").show();
        } else{
            HideAllBlocks();
        }
    }

    function checkCallLeadStatus() {
        let value = $('#call_lead_status option:selected').val();
        if (value === '1') {
            HideAllBlocks();
            $("#interested").show();
        } else if (value === '2') {
            HideAllBlocks();
            $("#notInterested").show();
        } else if (value === '3') {
            HideAllBlocks();
        } else if (value === '4') {
            HideAllBlocks();
        } else if (value === '5') {
            HideAllBlocks();
            $("#_followUpBlock").show();
            $("#generalComments").show();
        } else if (value === '6') {
            HideAllBlocks();
            $('#_followUpBlock').show();
        } else{
            HideAllBlocks();
        }
    }

    function CheckInspectionPeriod(period) {
      if (period === 'Yes') {
        $('#_inspectionNumberofDaysBlock').show();
      } else {
        $('#_inspectionNumberofDaysBlock').hide();
      }
    }

    function MakeTrainingLinkTable() {
        if ($("#admin_training_table").length) {
            $("#admin_training_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/training-link/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'training_link'},
                    {data: 'updated_at'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    function MakeTCoverageFileTable() {
        if ($("#admin_coverage_file_table").length) {
            $("#admin_coverage_file_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/coverage-file/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'coverage_file'},
                    {data: 'updated_at'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    /* Add Sale Section - Start */
    function SearchByLeadNumber() {
        let lead_number = $('#lead_number').val();
        if (lead_number !== '') {
            MakeSearchByLeadNumberTable(lead_number);
        } else {
            alert('Lead number is missing!');
        }
    }

    function MakeSearchByLeadNumberTable(lead_number) {
        if ($("#search_leads_table").length) {
            $("#search_leads_table").dataTable().fnDestroy();
            $("#search_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('leads/leadnumber/all')}}",
                    "type": "POST",
                    "data": {
                        lead_number: lead_number,
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'lead_number'},
                    {data: 'firstname'},
                    {data: 'lastname'},
                    {data: 'phone'},
                    {data: 'is_duplicated'},
                    {data: 'lead_status', orderable: false},
                    {data: 'add_sale', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function SearchByPhoneNumber() {
        let phone_number = $('#lead_phone_number').val();
        if (phone_number !== '') {
            MakeSearchByLeadPhoneNumberTable(phone_number);
        } else {
            alert('Phone number is missing!');
        }
    }

    function MakeSearchByLeadPhoneNumberTable(phone_number) {
        if ($("#search_leads_table").length) {
            $("#search_leads_table").dataTable().fnDestroy();
            $("#search_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('leads/leadphonenumber/all')}}",
                    "type": "POST",
                    "data": {
                        phone_number: phone_number,
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'lead_number'},
                    {data: 'firstname'},
                    {data: 'lastname'},
                    {data: 'phone'},
                    {data: 'is_duplicated'},
                    {data: 'lead_status', orderable: false},
                    {data: 'add_sale', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function AddSaleForm(id) {
        let values = id.split('_');
        $("#leadId").val(values[1]);
        $("#leadLeadNumber").val(values[2]);
        $("#addsale_leadnumber").val(values[2]);
        $("#addsale_contractamount").val(values[3]);
        $("#addSaleModal").modal('toggle');
    }

    function MakeLeadSalesTable() {
        if ($("#lead_sales_table").length) {
            $("#lead_sales_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('sale/sales-all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'UserId'},
                    {data: 'lead_number'},
                    {data: 'sale_type'},
                    {data: 'contract_amount'},
                    {data: 'contract_date'},
                    {data: 'net_profit'},
                    {data: 'net_profit_amount'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function AddSaleFormFields() {
        if($("#SalesPage").length){
            $(".__addsale_payees").select2();
            $(".__addsale_amountType").select2();
        }
    }

    /* Add Sale Section - End */

    /* Payroll - Start */
    function MakeApprovePayrollTable() {
        if ($("#approve_payroll_table").length) {
            let StartDate = $('#startDateFilter').val();
            let EndDate = $('#endDateFilter').val();
            if (StartDate !== '' && EndDate !== '') {
                $('#_error').text('');
                $('#payrollFilterPage').hide();
                $('#tablePayrollPage').show();
                $("#approve_payroll_table").dataTable().fnDestroy();
                $("#approve_payroll_table").DataTable({
                    "processing": true,
                    "serverSide": true,
                    "paging": true,
                    "bPaginate": true,
                    "ordering": true,
                    "pageLength": 50,
                    "lengthMenu": [
                        [50, 100, 200, 400],
                        ['50', '100', '200', '400']
                    ],
                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
                    "ajax": {
                        "url": "{{url('admin/payroll-all')}}",
                        "type": "POST",
                        "data": {
                            StartDate: StartDate,
                            EndDate: EndDate,
                        }
                    },
                    @else
                    "ajax": {
                        "url": "{{url('global_manager/payroll-all')}}",
                        "type": "POST",
                        "data": {
                            StartDate: StartDate,
                            EndDate: EndDate,
                        }
                    },
                    @endif
                    'columns': [
                        {data: 'id', orderable: false},
                        {data: 'name', orderable: false},
                        {data: 'account', orderable: false},
                        {data: 'earning', orderable: false},
                        {data: 'bonus', orderable: false},
                        {data: 'view', orderable: false},
                        {data: 'income', orderable: false},
                        {data: 'submit', orderable: false},
                        {data: 'cancel', orderable: false},
                    ],
                    'order': [0, 'desc']
                });
            } else {
                $('#_error').text('Start date or end date is missing!');
            }
        }
    }

    function PayrollFilterBackButton() {
        $('#tablePayrollPage').hide();
        $('#payrollFilterPage').show();
    }

    function RejectEarningPayroll(e) {
        let id = e.split('_')[1];
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        $("#rejectPayrollId").val(id);
        $("#rejectStartDate").val(StartDate);
        $("#rejectEndDate").val(EndDate);
        $("#rejectPayrollModal").modal('toggle');
    }

    function AddBonusEarningPayroll(e) {
        let id = e.split('_')[1];
        $("#bonusPayrollId").val(id);
        // $("#bonusPayrollModal").modal('toggle');
        $("#PayrollBreakdowns").hide();
        $("#EditBonus").show();
    }

    function CancelEditBonus() {
        $("#EditBonus").hide();
        $("#PayrollBreakdowns").show();
    }

    function SubmitEarningPayroll(e) {
        let id = e.split('_')[1];
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        $("#submitPayrollId").val(id);
        $("#submitStartDate").val(StartDate);
        $("#submitEndDate").val(EndDate);
        $("#submitPayrollModal").modal('toggle');
    }

    function ApproveEarningPayroll(e) {
        let id = e.split('_')[1];
        $("#approvePayrollId").val(id);
        $("#approvePayrollModal").modal('toggle');
    }

    function EditEarningPayroll(e) {
        let id = e.split('_')[1];
        $("#editEarningPayrollId").val(id);
        // $("#editEarningPayrollModal").modal('toggle');
        $("#PayrollBreakdowns").hide();
        $("#EditEarning").show();
    }

    function CancelEditEarning() {
        $("#EditEarning").hide();
        $("#PayrollBreakdowns").show();
        $('#editEarningAmount').val('');
    }

    function MakeRejectedPayrollTable() {
        if ($("#rejected_payroll_table").length) {
            $("#rejected_payroll_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('admin/rejected/payroll-all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'account'},
                    {data: 'lead_number'},
                    {data: 'payout_type'},
                    {data: 'earning'},
                    {data: 'bonus'},
                    {data: 'edit_earning'},
                    {data: 'add_bonus'},
                    {data: 'approve'},
                    {data: 'reject'},
                ],
            });
        }
    }

    function ViewEarningPayrollDetails(id) {
        let startDate = "";
        let endDate = "";
        let values = id.split("_");
        $("#_userEarningMasterId").val(id);
        @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
        $.ajax({
            type: "post",
            url: "{{url('/admin/payroll/breakdowns')}}",
            data: {EarningMasterId: values[1], StartDate: startDate, EndDate: endDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#PayrollBreakdowns").html(data);
            $('#PayrollBreakdowns').show();
            $('#EditEarning').hide();
            $('#EditBonus').hide();
            $("#userPayrollBreakdownModal").modal('toggle');
        });
        @else
        $.ajax({
            type: "post",
            url: "{{url('/global_manager/payroll/breakdowns')}}",
            data: {EarningMasterId: values[1], StartDate: startDate, EndDate: endDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#PayrollBreakdowns").html(data);
            $('#PayrollBreakdowns').show();
            $('#EditEarning').hide();
            $('#EditBonus').hide();
            $("#userPayrollBreakdownModal").modal('toggle');
        });
        @endif
    }

    function UpdateEarningPayrollDetails(id) {
        let startDate = "";
        let endDate = "";
        let values = id.split("_");
        $("#_userEarningMasterId").val(id);
        @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
        $.ajax({
            type: "post",
            url: "{{url('/admin/payroll/breakdowns')}}",
            data: {EarningMasterId: values[1], StartDate: startDate, EndDate: endDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#PayrollBreakdowns").html(data);
            $('#PayrollBreakdowns').show();
            $('#EditEarning').hide();
            $('#EditBonus').hide();
        });
        @else
        $.ajax({
            type: "post",
            url: "{{url('/global_manager/payroll/breakdowns')}}",
            data: {EarningMasterId: values[1], StartDate: startDate, EndDate: endDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#PayrollBreakdowns").html(data);
            $('#PayrollBreakdowns').show();
            $('#EditEarning').hide();
            $('#EditBonus').hide();
        });
        @endif
    }

    function UpdateEarningAmount() {
        let editEarningPayrollId = $('#editEarningPayrollId').val();
        let editEarningAmount = $('#editEarningAmount').val();
        if (editEarningAmount !== '') {
            $('#earningAmount').text('');
            @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
            $.ajax({
                type: "post",
                url: "{{url('/admin/payroll/edit/earning')}}",
                data: {id: editEarningPayrollId, editEarningAmount: editEarningAmount}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('#editEarningAmount').val('');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                } else {
                    $('#editEarningAmount').val('');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                }
            });
            @else
            $.ajax({
                type: "post",
                url: "{{url('/global_manager/payroll/edit/earning')}}",
                data: {id: editEarningPayrollId, editEarningAmount: editEarningAmount}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('#editEarningAmount').val('');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                } else {
                    $('#editEarningAmount').val('');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                }
            });
            @endif
        } else {
            $('#earningAmount').text('Error! Please enter earning amount');
        }
    }

    function UpdateBonusAmount() {
        let bonusPayrollId = $('#bonusPayrollId').val();
        let bonusAmountValue = $('#bonusAmountValue').val();
        if (bonusAmountValue !== '') {
            $('#bonusAmount').text('');
            @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
            $.ajax({
                type: "post",
                url: "{{url('/admin/payroll/bonus')}}",
                data: {id: bonusPayrollId, bonus: bonusAmountValue}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('#bonusAmountValue').val('');
                    // $("#userPayrollBreakdownModal").modal('toggle');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                } else {
                    $('#bonusAmountValue').val('');
                    // $("#userPayrollBreakdownModal").modal('toggle');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                }
            });
            @else
            $.ajax({
                type: "post",
                url: "{{url('/global_manager/payroll/bonus')}}",
                data: {id: bonusPayrollId, bonus: bonusAmountValue}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('#bonusAmountValue').val('');
                    // $("#userPayrollBreakdownModal").modal('toggle');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                } else {
                    $('#bonusAmountValue').val('');
                    // $("#userPayrollBreakdownModal").modal('toggle');
                    $('#approve_payroll_table').DataTable().ajax.reload();
                    let _userEarningMasterId = $("#_userEarningMasterId").val();
                    UpdateEarningPayrollDetails(_userEarningMasterId);
                }
            });
            @endif
        } else {
            $('#bonusAmount').text('Error! Please enter bonus amount');
        }
    }

    /* Payroll - End */

    function MakeCompanyLeadsTable() {
        if ($("#admin_company_leads_table").length) {
            $("#admin_company_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/buissness_accounts/companyLeads')}}",
                    "type": "POST",
                    "data": {company: $("#companyId").val()}
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_id'},
                    {data: 'lead_number'},
                    {data: 'firstname'},
                    {data: 'lastname'},
                    {data: 'ProductName'},
                    {data: 'appointment_time'},
                    {data: 'is_duplicated'},
                    {data: 'lead_type', orderable: false},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc']
            });
        }
    }

    /* Payroll - End */

    function FilterLeadsByStatus(Role) {
        if (Role === '1,2,3,4,5,6,7,8,9') {
            // Role 1,2,3,4,5,6,7,8,9
            // $("#filterPage").hide();
            // $("#tablePage").show();
            $('#admin_leads_table').DataTable().clear().destroy();
            MakeAdminLeadsTable();
        }
        // else {
        //     // Role 4,5
        //     $('#representative_leads_table').DataTable().clear().destroy();
        //     MakeLeadsTable();
        // }
    }

    function FilterBackButton() {
        // $("#filterPage").show();
        // $("#tablePage").removeClass("col-md-12");
        // $("#tablePage").addClass("col-md-10");
        $("#filterPage").show();
        $("#beforeTablePage").removeClass("col-md-1");
        $("#filterPage").removeClass("col-md-1");
        $("#filterPage").addClass("col-md-2");
    }

    function FilterCloseButton() {
        // $("#filterPage").hide();
        // $("#tablePage").removeClass("col-md-10");
        // $("#tablePage").addClass("col-md-12");
        $("#filterPage").hide();
        $("#beforeTablePage").addClass("col-md-1");
        $("#filterPage").removeClass("col-md-2");
        $("#filterPage").addClass("col-md-1");
    }

    /* Lead Update Status - Start */
    function showLeadUpdateStatus(id) {
        let values = id.split('_')[1];
        let LeadTeamId = id.split('_')[2];
        let LeadUpdateStatusType = id.split('_')[3];
        $("#_lead_update_status_id").val(values);
        $("#team").val(LeadTeamId);
        $("#_lead_update_status_type").val(LeadUpdateStatusType);
        $("#leadUpdateStatusModal").modal('toggle');
    }

    function showTaskLeadUpdateStatus(id) {
        let values = id.split('_')[1];
        let LeadTeamId = id.split('_')[2];
        let LeadStatusFieldIndex = id.split('_')[3];
        $("#_lead_update_status_id").val(values);
        $("#team").val(LeadTeamId);
        $("#_lead_update_status_field_index").val(LeadStatusFieldIndex);
        $("#leadTaskUpdateStatusModal").modal('toggle');
    }

    function UpdateLeadStatus() {
        let LeadStatusType = null;
            <?php
        $Url = "";
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
            $Url = url('/admin/lead/update/status');
            ?>
            LeadStatusType = $('#lead_status_type option:selected').val();
        <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2){
            $Url = url('/global_manager/lead/update/status');
            ?>
            LeadStatusType = $('#lead_status_type option:selected').val();
        <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 3){
            $Url = url('/acquisition_manager/lead/update/status');
            ?>
            LeadStatusType = 'Offer Status';
        <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 4){
            $Url = url('/disposition_manager/lead/update/status');
            ?>
            LeadStatusType = 'Dispo Status';
        <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5){
            $Url = url('/acquisition_representative/lead/update/status');
            ?>
            LeadStatusType = 'Offer Status';
            <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6){
            $Url = url('/disposition_representative/lead/update/status');
                ?>
                LeadStatusType = 'Dispo Status';
            <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7){
            $Url = url('/cold_caller/lead/update/status');
                ?>
                LeadStatusType = 'Call Status'
            <?php
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8){
            $Url = url('/affiliate/lead/update/status');
                ?>
                LeadStatusType = $('#lead_status_type option:selected').val();
            <?php

        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9){
            $Url = url('/realtor/lead/update/status');
                ?>
                LeadStatusType = $('#lead_status_type option:selected').val();
            <?php
        }
        ?>
        let id = $('#_lead_update_status_id').val();
        let LeadUpdateStatusType = $("#_lead_update_status_type").val();
        let LeadStatus = 0;
        let Investors = [];
        let Company = "";
        let Amount = "";
        let Days = "";
        let ClosedOnDate = "";
        let ClosedDate = "";
        let CloseWonCost = "";
        let PurchaseAmount = "";
        let ContractAmount = 0;
        let InterestedComments = "";
        let NotInterestedComments = "";
        let FollowUpTime = "";
        let InspectionPeriod = "";
        let InspectionNumberofDays = "";
        let _Comments = "";

        if(LeadStatusType === 'Dispo Status'){
            let DispoLeadStatus = $('#dispo_lead_status option:selected').val();
            LeadStatus = DispoLeadStatus;
            if(DispoLeadStatus === '13'){
                Investors = $("#investorUsers").val();
                if(Investors === ''){
                    return;
                }
            }
            else if(DispoLeadStatus === '14'){
                FollowUpTime = $('#appointmenttime').val();
                _Comments = $("#__comments").val();
            }
            else if(DispoLeadStatus === '15'){
                Company = $("#companyUsers option:selected").val();
                if(Company === ''){
                    return;
                }
            }
            else if(DispoLeadStatus === '16'){
                Investors = $("#investorUsers").val();
                PurchaseAmount = $("#purchaseAmount").val();
            }
            else if(DispoLeadStatus === '17'){
                Amount = $("#emdAmount").val();
                if(Amount === ''){
                    return;
                }
            }
            else if(DispoLeadStatus === '18'){
                Days = $("#closingDays").val();
                FollowUpTime = $('#appointmenttime').val();
                _Comments = $("#__comments").val();
            }
            else if(DispoLeadStatus === '21'){
                ClosedDate = $('#leadCloseDate').val();
                CloseWonCost = $('#closeWonCost').val();
            }
            else if(DispoLeadStatus === '22'){
                _Comments = $("#__comments").val();
            }
            else if(DispoLeadStatus === '24'){
                InspectionPeriod = $("#inspectionperiod").val();
                InspectionNumberofDays = $("#inspection_numberofdays").val();
            }
            else if(DispoLeadStatus === '25'){
                ClosedOnDate = $("#leadCloseOnDate").val();
            }
        }
        else if(LeadStatusType === 'Offer Status'){
            let OfferLeadStatus = $('#offer_lead_status option:selected').val();
            LeadStatus = OfferLeadStatus;
            if(OfferLeadStatus === '7'){
                _Comments = $("#__comments").val();
            }
            if(OfferLeadStatus === '8'){
                _Comments = $("#__comments").val();
            }
            if(OfferLeadStatus === '10'){
                _Comments = $("#__comments").val();
            }
            if(OfferLeadStatus === '12'){
                ContractAmount = $("#contractAmount").val();
                if(ContractAmount === ''){
                    return;
                }
            }
        }
        else if(LeadStatusType === 'Call Status'){
            let CallLeadStatus = $('#call_lead_status option:selected').val();
            LeadStatus = CallLeadStatus;
            if(CallLeadStatus === '1'){
                InterestedComments = $("#interestedComments").val();
                if(InterestedComments === ''){
                    $("#interestedCommentsMessage").show();
                    return;
                }
                $("#interestedCommentsMessage").hide();
            }
            else if(CallLeadStatus === '2'){
                NotInterestedComments = $("#notInterestedComments").val();
                if(NotInterestedComments === ''){
                    $("#notInterestedCommentsMessage").show();
                    return;
                }
                $("#notInterestedCommentsMessage").hide();
            }
            else if(CallLeadStatus === '5'){
                // No Answer
                FollowUpTime = $('#appointmenttime').val();
                _Comments = $("#__comments").val();
            }
            else if(CallLeadStatus === '6'){
                FollowUpTime = $('#appointmenttime').val();
                if(FollowUpTime === ''){
                    return;
                }
            }
        }

        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: id,
                LeadStatusType: LeadStatusType,
                LeadStatus: LeadStatus,
                Investors: JSON.stringify(Investors),
                Company: Company,
                Amount: Amount,
                Days: Days,
                ClosedDate: ClosedDate,
                ClosedOnDate: ClosedOnDate,
                CloseWonCost: CloseWonCost,
                PurchaseAmount: PurchaseAmount,
                ContractAmount: ContractAmount,
                InterestedComments: InterestedComments,
                NotInterestedComments: NotInterestedComments,
                FollowUpTime: FollowUpTime,
                InspectionPeriod: InspectionPeriod,
                InspectionNumberofDays: InspectionNumberofDays,
                _Comments : _Comments
            }
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                $("#leadUpdateStatusModal").modal('toggle');
                if (LeadUpdateStatusType === '1') {
                    $('#dashboard_leads_table').DataTable().ajax.reload();
                } else {
                    $('#admin_leads_table').DataTable().ajax.reload();
                }
            } else {
                $("#leadUpdateStatusModal").modal('toggle');
                if (LeadUpdateStatusType === '1') {
                    $('#dashboard_leads_table').DataTable().ajax.reload();
                } else {
                    $('#admin_leads_table').DataTable().ajax.reload();
                }
            }
            $("#lead_status_type").val('').trigger('change');
            $("#dispo_lead_status").val('').trigger('change');
            $("#offer_lead_status").val('').trigger('change');
            $("#call_lead_status").val('').trigger('change');
            $('#dispoStatusSection').hide();
            $('#offerStatusSection').hide();
            $('#callStatusSection').hide();
            HideAllBlocks();
        });
    }

    function GetLeadClosedDate() {
        <?php
        $Url = url('/lead/closeddate');
        ?>
        let LeadId = $('#_lead_update_status_id').val();
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                LeadId: LeadId
            }
        }).done(function (data) {
            $("#leadCloseDate").val(data);
            $("#_leadCloseDate").val(data);
        });
    }

    function LeadEditAppointmentTime(id) {
        id = id.split('_')[1];
        $("#appointmentTimeComments").val('');
        $("#leadUpdateAppointmentTimeId").val(id);
        $("#leadUpdateAppointmentTimeModal").modal('toggle');
    }

    function LeadUpdateAppointmentTime() {
        <?php
        $Url = "";
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
            $Url = url('/admin/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2){
            $Url = url('/global_manager/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 3){
            $Url = url('/acquisition_manager/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 4){
            $Url = url('/disposition_manager/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5){
            $Url = url('/acquisition_representative/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6){
            $Url = url('/disposition_representative/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7){
            $Url = url('/cold_caller/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8){
            $Url = url('/affiliate/lead/update/appointmentTime');
        }
        elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9){
            $Url = url('/realtor/lead/update/appointmentTime');
        }
        ?>
        let LeadId = $("#leadUpdateAppointmentTimeId").val();
        let FollowUpTime = $('#_appointmentTime').val();
        let FollowUpNotes = $('#appointmentTimeComments').val();
        if(FollowUpTime === ''){
            return;
        }
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: LeadId,
                FollowUpTime: FollowUpTime,
                FollowUpNotes: FollowUpNotes
            }
        }).done(function (data) {
            $("#leadUpdateAppointmentTimeModal").modal('toggle');
            if ($("#dashboard_leads_table").length) {
                $('#dashboard_leads_table').DataTable().ajax.reload();
            }
            if ($("#admin_leads_table").length) {
                $('#admin_leads_table').DataTable().ajax.reload();
            }
            if ($("#editlead_historynotes_table").length) {
                $('#editlead_historynotes_table').DataTable().ajax.reload();
            }
        });
    }

    function UpdateTaskLeadStatus() {
        let id = $('#_lead_update_status_id').val();
        let lead_status = $('#lead_status option:selected').val();
        let confirmation_reason = $('#confirmation_reason').val();
        let cancellation_reason = $('#cancellation_reason').val();
        let lead_company = $('#lead_company').val();
        let lead_contract_amount = $('#_salesContractAmount').val();
        let lead_update_status_field_index = $("#_lead_update_status_field_index").val();

        if (lead_status == 2 && cancellation_reason == '') {
            $('#cancellation_reason_error').html('').html('Cancellation reason is missing!');
        } else {
            $('#cancellation_reason_error').html('');
            @if(\Illuminate\Support\Facades\Auth::user()->role_id == 2)
            $.ajax({
                type: "post",
                url: "{{url('/global_manager/lead/update/status')}}",
                data: {
                    id: id,
                    lead_status: lead_status,
                    confirmation_reason: confirmation_reason,
                    cancellation_reason: cancellation_reason,
                    lead_company: lead_company,
                    lead_contract_amount: lead_contract_amount
                }
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $("#leadTaskUpdateStatusModal").modal('toggle');
                    $("#leadStatusSuccessAlert").show();
                    setInterval(function () {
                        $("#leadStatusSuccessAlert").hide();
                    }, 5000);
                    $("#lead_historynotes_table" + lead_update_status_field_index).DataTable().ajax.reload();
                    $("#markascompleted" + lead_update_status_field_index).prop('disabled', false);
                    if (lead_status === '1') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Confirm</span>');
                    } else if (lead_status === '2') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-danger">Cancelled</span>');
                    } else if (lead_status === '3') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning">Pending</span>');
                    } else if (lead_status === '4') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-primary">Approve Sales</span>');
                    } else if (lead_status === '5') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning" style="background-color:pink;color:white;">Bank Turn Down</span>');
                    } else if (lead_status === '6') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning" style="background-color:orange;">Out of coverage area</span>');
                    } else if (lead_status === '7') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-secondary">Not interested</span>');
                    } else if (lead_status === '8') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Demo</span>');
                    } else if (lead_status === '9') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">1 Legger</span>');
                    } else if (lead_status === '10') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Not Home</span>');
                    } else if (lead_status === '11') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Pending Sales</span>');
                    }
                } else {
                    $("#leadTaskUpdateStatusModal").modal('toggle');
                    $("#leadStatusFailedAlert").show();
                    setInterval(function () {
                        $("#leadStatusFailedAlert").hide();
                    }, 5000);
                    $("#lead_historynotes_table" + lead_update_status_field_index).DataTable().ajax.reload();
                }
            });
            @else
            $.ajax({
                type: "post",
                url: "{{url('/confirmationAgent/lead/update/status')}}",
                data: {
                    id: id,
                    lead_status: lead_status,
                    confirmation_reason: confirmation_reason,
                    cancellation_reason: cancellation_reason,
                    lead_company: lead_company,
                    lead_contract_amount: lead_contract_amount
                }
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $("#leadTaskUpdateStatusModal").modal('toggle');
                    $("#leadStatusSuccessAlert").show();
                    setInterval(function () {
                        $("#leadStatusSuccessAlert").hide();
                    }, 5000);
                    $("#lead_historynotes_table" + lead_update_status_field_index).DataTable().ajax.reload();
                    $("#markascompleted" + lead_update_status_field_index).prop('disabled', false);
                    if (lead_status === '1') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Confirm</span>');
                    } else if (lead_status === '2') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-danger">Cancelled</span>');
                    } else if (lead_status === '3') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning">Pending</span>');
                    } else if (lead_status === '4') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-primary">Approve Sales</span>');
                    } else if (lead_status === '5') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning" style="background-color:pink;color:white;">Bank Turn Down</span>');
                    } else if (lead_status === '6') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-warning" style="background-color:orange;">Out of coverage area</span>');
                    } else if (lead_status === '7') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-secondary">Not interested</span>');
                    } else if (lead_status === '8') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Demo</span>');
                    } else if (lead_status === '9') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">1 Legger</span>');
                    } else if (lead_status === '10') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Not Home</span>');
                    } else if (lead_status === '11') {
                        $("#_task_lead_status_" + lead_update_status_field_index).html('').html('<span class="badge badge-success">Pending Sales</span>');
                    }
                } else {
                    $("#leadTaskUpdateStatusModal").modal('toggle');
                    $("#leadStatusFailedAlert").show();
                    setInterval(function () {
                        $("#leadStatusFailedAlert").hide();
                    }, 5000);
                    $("#lead_historynotes_table" + lead_update_status_field_index).DataTable().ajax.reload();
                }
            });
            @endif
        }
    }

    /* Lead Update Status - End */

    /* History Note - Start */
    function showHistoryPreviousNotes(id) {
        let values = id.split('_')[1];
        $("#_lead_history_note_id").val(values);
        $("#leadHistoryNotesModal").modal('toggle');
        MakeLeadHistoryDashboardNotesTable(values);
    }

    function SaveLeadHistoryDashboardNote() {
        let LeadId = $('#_lead_history_note_id').val();
        let HistoryNote = $('textarea#_lead_history_note').val();
        if (LeadId != "" && HistoryNote != "") {
            $.ajax({
                type: "post",
                url: "{{url('/historynote/store')}}",
                data: {LeadId: LeadId, HistoryNote: HistoryNote}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('textarea#_lead_history_note').val("");
                    MakeLeadHistoryDashboardNotesTable(LeadId);
                } else {
                    $('textarea#_lead_history_note').val("");
                    MakeLeadHistoryDashboardNotesTable(LeadId);
                }
            });
        }
    }

    function SaveHistoryNote() {
        let LeadId = $('#id').val();
        let HistoryNote = $('textarea#history_note').val();
        if (LeadId != "" && HistoryNote != "") {
            $.ajax({
                type: "post",
                url: "{{url('/historynote/store')}}",
                data: {LeadId: LeadId, HistoryNote: HistoryNote}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('textarea#history_note').val("");
                    $('#history_note_msg').show();
                    $('#history_note_msg').text('History note is added successfully');
                    $('#lead_historynotes_table').DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg").text("");
                        $('#history_note_msg').hide();
                    }, 2500);
                } else {
                    $('textarea#history_note').val("");
                    $('#history_note_msg').show();
                    $('#history_note_msg').text('Error! An unhandled exception occurred');
                    $('#lead_historynotes_table').DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg").text("");
                        $('#history_note_msg').hide();
                    }, 2500);
                }
            });
        }
    }

    function EditLeadSaveHistoryNote() {
        let LeadId = $('#id').val();
        let HistoryNote = $('textarea#history_note').val();
        if (LeadId != "" && HistoryNote != "") {
            $.ajax({
                type: "post",
                url: "{{url('/historynote/store')}}",
                data: {LeadId: LeadId, HistoryNote: HistoryNote}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('textarea#history_note').val("");
                    $('#history_note_msg').show();
                    $('#history_note_msg').text('History note is added successfully');
                    $('#editlead_historynotes_table').DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg").text("");
                        $('#history_note_msg').hide();
                    }, 2500);
                } else {
                    $('textarea#history_note').val("");
                    $('#history_note_msg').show();
                    $('#history_note_msg').text('Error! An unhandled exception occurred');
                    $('#editlead_historynotes_table').DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg").text("");
                        $('#history_note_msg').hide();
                    }, 2500);
                }
            });
        }
    }

    /* Tasks History Notes - Start */

    function MakeLeadTaskHistoryNotesTable() {
        if ($("#totaltasks").length) {
            let TotalTasks = $("#totaltasks").val();
            for (let i = 0; i < TotalTasks; i++) {
                if ($("#lead_historynotes_table" + i).length) {
                    let LeadId = $('#id' + i).val();
                    $("#lead_historynotes_table" + i).DataTable({
                        "processing": true,
                        "serverSide": true,
                        "paging": true,
                        "bPaginate": true,
                        "ordering": true,
                        "pageLength": 5,
                        "lengthMenu": [
                            [5, 10, 15, 20],
                            ['5', '10', '15', '20']
                        ],
                        "ajax": {
                            "url": "{{url('/history_note/all')}}",
                            "type": "POST",
                            "data": {LeadId: LeadId},
                        },
                        'columns': [
                            {data: 'id'},
                            {data: 'user_id'},
                            {data: 'history_note'},
                            {data: 'created_at'},
                        ],
                        'order': [0, 'desc']
                    });
                }
            }
        }
    }

    function SaveTaskHistoryNote(id) {
        let values = id.split("_");
        let LeadId = $("#id" + values[1]).val();
        let HistoryNote = $('textarea#history_note' + values[1]).val();
        if (LeadId != "" && HistoryNote != "") {
            $.ajax({
                type: "post",
                url: "{{url('/historynote/store')}}",
                data: {LeadId: LeadId, HistoryNote: HistoryNote}
            }).done(function (data) {
                if (jQuery.trim(data) === 'Success') {
                    $('textarea#history_note' + values[1]).val("");
                    $('#history_note_msg' + values[1]).show();
                    $('#history_note_msg' + values[1]).text('History note is added successfully');
                    $('#lead_historynotes_table' + values[1]).DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg" + values[1]).text("");
                        $('#history_note_msg' + values[1]).hide();
                    }, 2500);
                    $('#markascompleted' + values[1]).prop('disabled', false);
                } else {
                    $('textarea#history_note' + values[1]).val("");
                    $('#history_note_msg' + values[1]).show();
                    $('#history_note_msg' + values[1]).text('Error! An unhandled exception occurred');
                    $('#lead_historynotes_table' + values[1]).DataTable().ajax.reload();
                    setTimeout(function () {
                        $("#history_note_msg" + values[1]).text("");
                        $('#history_note_msg' + values[1]).hide();
                    }, 2500);
                }
            });
        }
    }

    /* Tasks History Notes - End */

    /* Call History Note - Start */
    function SaveCallHistoryNote() {
        let LeadId = $('#id').val();
        let HistoryNote = $('textarea#history_note').val();
        $.ajax({
            type: "post",
            url: "{{url('/historycallnote/store')}}",
            data: {LeadId: LeadId, HistoryNote: HistoryNote}
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                $('textarea#history_note').val("");
                $('#history_note_msg').show();
                $('#history_note_msg').text('History note is added successfully');
                $('#lead_historynotes_table').DataTable().ajax.reload();
                setTimeout(function () {
                    $("#history_note_msg").text("");
                    $('#history_note_msg').hide();
                }, 2500);
            } else {
                $('textarea#history_note').val("");
                $('#history_note_msg').show();
                $('#history_note_msg').text('Error! An unhandled exception occurred');
                $('#lead_historynotes_table').DataTable().ajax.reload();
                setTimeout(function () {
                    $("#history_note_msg").text("");
                    $('#history_note_msg').hide();
                }, 2500);
            }
        });
    }

    function MakeLeadHistoryNotesTable() {
        if ($("#lead_historynotes_table").length) {
            let LeadId = $('#id').val();
            $("#lead_historynotes_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/history_note/all')}}",
                    "type": "POST",
                    "data": {LeadId: LeadId},
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_id'},
                    {data: 'history_note'},
                    {data: 'created_at'},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function MakeEditLeadHistoryNotesTable() {
        if ($("#editlead_historynotes_table").length) {
            let LeadId = $('#id').val();
            $("#editlead_historynotes_table").dataTable().fnDestroy();
            $("#editlead_historynotes_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 5,
                "lengthMenu": [
                    [5, 10, 15, 20],
                    ['5', '10', '15', '20']
                ],
                "ajax": {
                    "url": "{{url('/history_note/all')}}",
                    "type": "POST",
                    "data": {LeadId: LeadId},
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_id'},
                    {data: 'history_note'},
                    // {data: 'created_at'},
                ],
                'order': [0, 'desc']
            });
        }
    }

    /*Call  History Note - End */

    function MakeLeadHistoryDashboardNotesTable(id) {
        if ($("#lead_historynotesdashboard_table").length) {
            let LeadId = id;
            $("#lead_historynotesdashboard_table").dataTable().fnDestroy();
            $("#lead_historynotesdashboard_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/historynote/all')}}",
                    "type": "POST",
                    "data": {LeadId: LeadId},
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_id'},
                    {data: 'history_note'},
                    // {data: 'created_at'},
                ],
                'order': [0, 'desc']
            });
        }
    }

    function MakeLeadHistoryNotesTable() {
        if ($("#lead_historynotes_table").length) {
            let LeadId = $('#id').val();
            $("#lead_historynotes_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/historynote/all')}}",
                    "type": "POST",
                    "data": {LeadId: LeadId},
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_id'},
                    {data: 'history_note'},
                    {data: 'created_at'},
                ],
                'order': [0, 'desc']
            });
        }
    }

    /* History Note - End */

    /* User Ban/Active - Start */
    function banUser(id) {
      let values = id.split('_');
      $("#banUserId").val(values[1]);
      $("#userBanModal").modal('toggle');
    }

    function activeUser(id) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/user-active');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/user-active');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/user-active');
        }
        ?>
        let values = id.split('_');
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {UserId: values[1]}
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                if ($("#admin_users_table").length) {
                  $('#admin_users_table').DataTable().ajax.reload();
                }
                else if ($("#admin_investor_table").length) {
                  $('#admin_investor_table').DataTable().ajax.reload();
                }
                else if ($("#admin_title_company_table").length) {
                  $('#admin_title_company_table').DataTable().ajax.reload();
                }
                else if ($("#admin_buissness_account_table").length) {
                  $('#admin_buissness_account_table').DataTable().ajax.reload();
                }
            } else {
                if ($("#admin_users_table").length) {
                  $('#admin_users_table').DataTable().ajax.reload();
                }
                else if ($("#admin_investor_table").length) {
                  $('#admin_investor_table').DataTable().ajax.reload();
                }
                else if ($("#admin_title_company_table").length) {
                  $('#admin_title_company_table').DataTable().ajax.reload();
                }
                else if ($("#admin_buissness_account_table").length) {
                  $('#admin_buissness_account_table').DataTable().ajax.reload();
                }
            }
        });
    }

    function MakeSubmittedPayrollTable() {
        <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/payroll/submitted/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                $Url = url('global_manager/payroll/submitted/all');
            }
            ?>
        if ($("#submitted_payroll_table").length) {
            $("#submitted_payroll_table").dataTable().fnDestroy();
            $("#submitted_payroll_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id', orderable: false},
                    {data: 'Earnings'},
                    {data: 'Bonus'},
                    {data: 'GrossIncome'},
                    {data: 'TaxAmount'},
                    {data: 'DrawBalance'},
                    {data: 'NetIncome'},
                    {data: 'view', orderable: false},
                    {data: 'approve', orderable: false}
                ],
                'order': [0, 'desc']
            });
        }
    }

    function ApproveSubmittedEarningPayroll(e) {
        let id = e.split('_')[1];
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        $("#approvePayrollId").val(id);
        $("#approveStartDate").val(StartDate);
        $("#approveEndDate").val(EndDate);
        $("#approvePayrollModal").modal('toggle');
    }

    function ViewSubmittedEarningPayrollDetails(Id) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/payroll/submitted/breakdowns');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/payroll/submitted/breakdowns');
        }
        ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: { Id: Id }
        }).done(function (data) {
            data = JSON.parse(data);
            $("#PayrollBreakdowns").html(data).show();
            $('#EditEarning').hide();
            $('#EditBonus').hide();
            $("#userPayrollBreakdownModal").modal('toggle');
        });
    }

    function ViewIncomeDetails(e) {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/payroll/income-details');
            } else {
                $Url = url('global_manager/payroll/income-details');
            }
            ?>
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        let values = e.split("_")[1];
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {id: values, StartDate: StartDate, EndDate: EndDate}
        }).done(function (data) {
            data = JSON.parse(data);
            if (data.length > 0) {
                $("#incomePayrollId").val(data[0].id);
                $("#masterPayrollId").val(values);
                $("#incomeStartDate").val(StartDate);
                $("#incomeEndDate").val(EndDate);
                $("#_hours").val(data[0].hours);
                $("#_tax").val(data[0].tax);
                $("#_drawBalance").val(data[0].draw_balance);
                $("#incomePayrollModal").modal('toggle');
            } else {
                $("#masterPayrollId").val(values);
                $("#incomeStartDate").val(StartDate);
                $("#incomeEndDate").val(EndDate);
                $("#incomePayrollModal").modal('toggle');
            }
        });
    }

    function StoreUpdateIncomeDetails() {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/payroll/income-details/store-update');
            } else {
                $Url = url('global_manager/payroll/income-details/store-update');
            }
            ?>
        let PayPeriodId = $("#incomePayrollId").val();
        let MasterId = $("#masterPayrollId").val();
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        let Hours = $('#_hours').val();
        let Tax = $('#_tax').val();
        let DrawBalance = $('#_drawBalance').val();
        if (Hours === '' || Tax === '' || DrawBalance === '') {
            return;
        }
        $("#StoreUpdateIncomePayrollBtn").attr('disabled', true);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: PayPeriodId,
                MasterId: MasterId,
                startDate: StartDate,
                endDate: EndDate,
                hours: Hours,
                tax: Tax,
                drawBalance: DrawBalance
            }
        }).done(function (data) {
            data = JSON.parse(data);
            $("#StoreUpdateIncomePayrollBtn").attr('disabled', false);
            $("#incomePayrollModal").modal('toggle');
            $('#hours').val('');
            $('#tax').val('');
            $('#drawBalance').val('');
            MakeApprovePayrollTable();
        });
    }

    function SubmitPayroll() {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/payroll/submit');
            } else {
                $Url = url('global_manager/payroll/submit');
            }
            ?>
        let MasterId = $("#submitPayrollId").val();
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        $("#submitPayrollBtn").attr('disabled', true);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {id: MasterId, startDate: StartDate, endDate: EndDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#submitPayrollBtn").attr('disabled', false);
            $("#submitPayrollModal").modal('toggle');
            MakeApprovePayrollTable();
        });
    }

    function RejectPayroll() {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                if ($page == 'submittedPayroll') {
                    $Url = url('admin/payroll/submitted/reject');
                } else {
                    $Url = url('admin/payroll/reject');
                }
            } else {
                $Url = url('global_manager/payroll/reject');
            }
            ?>
        let MasterId = $("#rejectPayrollId").val();
        let StartDate = $('#startDateFilter').val();
        let EndDate = $('#endDateFilter').val();
        $("#rejectPayrollBtn").attr('disabled', true);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {id: MasterId, startDate: StartDate, endDate: EndDate}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#rejectPayrollBtn").attr('disabled', false);
            $("#rejectPayrollModal").modal('toggle');
            MakeApprovePayrollTable();
        });
    }

    function ClearIncomePayrollModalFields() {
        $('#_hours').val('');
        $('#_tax').val('');
        $('#_drawBalance').val('');
    }

    function EditPayPeriodEarning(PayPeriodId) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/payroll/submitted/edit-pay-period');
        } else {
            $Url = url('global_manager/payroll/submitted/edit-pay-period');
        }
        ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {PayPeriodId: PayPeriodId}
        }).done(function (data) {
            data = JSON.parse(data);
            if (data.length > 0) {
                $("#_payPeriodId").val(PayPeriodId);
                $("#earnings").val(data[0].earnings);
                $("#bonus").val(data[0].bonus);
                $("#tax").val(data[0].tax);
                $("#drawBalance").val(data[0].draw_balance);
                $("#EditPayPeriod").show();
                $("#PayrollBreakdowns").hide();
            } else {
                $("#_payPeriodId").val(PayPeriodId);
                $("#EditPayPeriod").show();
                $("#PayrollBreakdowns").hide();
            }
        });
    }

    function CancelPayPeriod() {
        $("#EditPayPeriod").hide();
        $("#PayrollBreakdowns").show();
    }

    function UpdatePayPeriod() {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/payroll/submitted/update-pay-period');
            } else {
                $Url = url('global_manager/payroll/submitted/update-pay-period');
            }
            ?>
        let PayPeriodId = $("#_payPeriodId").val();
        let Earnings = $('#earnings').val();
        let Bonus = $('#bonus').val();
        let Tax = $('#tax').val();
        let DrawBalance = $('#drawBalance').val();
        if (Earnings === '' || Bonus === '' || Tax === '' || DrawBalance === '') {
            return;
        }
        $("#updatePayRollBtn").attr('disabled', true);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {id: PayPeriodId, Earnings: Earnings, Bonus: Bonus, tax: Tax, drawBalance: DrawBalance}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#updatePayRollBtn").attr('disabled', false);
            $("#EditPayPeriod").hide();
            $("#PayrollBreakdowns").show();
            $('#earnings').val('');
            $('#bonus').val('');
            $('#tax').val('');
            $('#drawBalance').val('');
            $("#userPayrollBreakdownModal").modal('toggle');
            MakeSubmittedPayrollTable();
        });
    }

    function GeneratePayroll(Id) {
        Id = Id.split('_')[1];
        $("#generatePayPeriodId").val(Id);
        $("#approvePayrollModal").modal('toggle');
    }

    function Rollback(Id) {
        Id = Id.split('_')[1];
        $("#rollbackSaleId").val(Id);
        $("#rollbackPayrollModal").modal('toggle');
    }

    function ViewSaleDetails(SaleId) {
        <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/sales/view');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                $Url = url('global_manager/sales/view');
            }
            ?>
        SaleId = SaleId.split('_')[1];
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {SaleId: SaleId}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#viewSaleDetails").html(data);
            $("#viewSaleModal").modal('toggle');
        });
    }

    /*Assign Users to Lead*/
    $('.SelectAllUsers').on("click", function (e) {
        $("._assignUsers > option").prop("selected", "selected").trigger("change");
    });

    $('.RemoveAllUsers').on("click", function (e) {
        $("._assignUsers > option").prop("selected", "").trigger("change");
    });

    $('#assignLeadModal').on('hidden.bs.modal', function () {
        $(".RemoveAllUsers").click();
    });

    function AssignLeadToUser(LeadId) {
        <?php
          $Url = "";
          if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
              $Url = url('admin/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
              $Url = url('global_manager/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
              $Url = url('acquisition_manager/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
              $Url = url('disposition_manager/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
              $Url = url('acquisition_representative/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
              $Url = url('disposition_representative/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
              $Url = url('cold_caller/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
              $Url = url('affiliate/lead/assignedUsers');
          } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
              $Url = url('realtor/lead/assignedUsers');
          }
        ?>
        LeadId = LeadId.split('_')[1];
        $("#assignLeadId").val(LeadId);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {LeadId: LeadId}
        }).done(function (data) {
            data = JSON.parse(data);
            for (let k = 0; k < data.length; k++) {
                $("#assignUsers").find("option[value=" + data[k].user_id + "]").prop("selected", "selected");
            }
            $("#assignUsers").select2();
            $("#AssignSingleLead").show();
            $("#AssignMultipleLeads").hide();
            $("#assignLeadModal").modal('toggle');
        });
    }

    function ConfirmAssignLeadToUser(e) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            $Url = url('cold_caller/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
            $Url = url('affiliate/lead/assignToUsers');
        } elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
            $Url = url('realtor/lead/assignToUsers');
        }
        ?>
        $(e).attr('disabled', true);
        let LeadId = $("#assignLeadId").val();
        let Users = [];
        $("#assignUsers").find('option:selected').each(function () {
            Users.push($(this).val());
        });
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {LeadId: LeadId, Users: JSON.stringify(Users)}
        }).done(function (data) {
            data = JSON.parse(data);
            $(e).attr('disabled', false);
            $("#assignLeadModal").modal('toggle');
            $('#admin_leads_table').DataTable().clear().destroy();
            MakeAdminLeadsTable();
        });
    }

    function ColdCallerAssignLeadToUser(LeadId) {
        <?php
        $Url = "";
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
           $Url = url('cold_caller/lead/assignToUsers');
        }
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
           $Url = url('affiliate/lead/assignToUsers');
        }
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
           $Url = url('realtor/lead/assignToUsers');
        }
        ?>
        LeadId = LeadId.split('_')[1];
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {LeadId: LeadId}
        }).done(function (data) {
            data = JSON.parse(data);
            if (data.message === "success") {
              $("#SuccessAlert").show();
            } else {
              $("#FailedAlert").show();
              $("#_failedAlertText").html(data.message);
            }
        });
    }

    function InitializeCarousel() {
        if ($(".wrap").length) {
            $('.wrap').owlCarousel({
                nav: true,
                items: 1,
                center: true,
                dots: true,
                singleItem: true,
                margin: 175
            });
        }
    }

    function MakeLeadAsComplete(e, Count, LeadId) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/lead/markLeadAsComplete');
        } else {
            $Url = url('confirmationAgent/lead/markLeadAsComplete');
        }
        ?>
        $(e).attr('disabled', true);
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {LeadId: LeadId}
        }).done(function (data) {
            data = JSON.parse(data);
            if (data.message === 'success') {
                $(".owl-carousel").trigger('remove.owl.carousel', [Count]).trigger('refresh.owl.carousel');
                CalculateProgress();
            }
            $(e).attr('disabled', false);
        });
    }

    function CalculateProgress() {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/lead/calculateProgress');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('confirmationAgent/lead/calculateProgress');
        }
        ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {}
        }).done(function (data) {
            $('#totalCompletedProgress').html('').html(data);
        });
    }

    /*Assign Users to Lead*/

    /* Payout Setting - Start */
    function MakePayOutTable() {
        if ($("#admin_payout_table").length) {
            $("#admin_payout_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/payout/all')}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'role_title'},
                    {data: 'payout_type'},
                    {data: 'amount'},
                    {data: 'percentage'},
                    {data: 'action', orderable: false},
                ],
            });
        }
    }

    function editPayout(e) {
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{url('admin/edit/payout')}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    /* Payout Setting - End */

    function FilterAssignLeads() {
        $("#filterPage").hide();
        $("#tablePage").show();
        $('#admin_assign_leads_table').DataTable().clear().destroy();
        AssignLeadsTable();
    }

    function AssignLeadsTable() {
        if ($("#admin_assign_leads_table").length) {
            $("#admin_assign_leads_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('admin/leads/assign/all')}}",
                    "type": "POST",
                    "data": {
                        LeadStatus: $("#typeFilter option:selected").val(),
                        StartDate: $("#startDateFilter").val(),
                        EndDate: $("#endDateFilter").val(),
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'lead_header'},
                    {data: 'homeowner_address'},
                    {data: 'product_appt'},
                    {data: 'last_note'},
                    {data: 'lead_type', orderable: false},
                    {data: 'action', orderable: false},
                ],
            });
        }
    }

    function AssignLeads() {
        $("#assignUsers").select2();
        $("#AssignSingleLead").hide();
        $("#AssignMultipleLeads").show();
        $("#assignLeadModal").modal('toggle');
    }

    function AssignLeadsToUsers(e) {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('admin/leads/assign/assignToUsers');
            } else {
                $Url = url('global_manager/leads/assign/assignToUsers');
            }
            ?>
        let Users = [];
        $("#assignUsers").find('option:selected').each(function () {
            Users.push($(this).val());
        });
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                FirstName: $("#firstNameFilter").val(),
                LastName: $("#lastNameFilter").val(),
                Phone1: $("#phone1Filter").val(),
                Phone2: $("#phone2Filter").val(),
                StateFilter: $("#stateFilter option:selected").val(),
                Company: $("#companyFilter option:selected").val(),
                User: $("#userFilter option:selected").val(),
                LeadStatus: $("#statusFilter option:selected").val(),
                StartDate: $("#startDateFilter").val(),
                EndDate: $("#endDateFilter").val(),
                AppointmentTime: $("#appointmentDateFilter").val(),
                LeadType: $("#typeFilter option:selected").val(),
                Users: JSON.stringify(Users)
            }
        }).done(function (data) {
            data = JSON.parse(data);
            $(e).attr('disabled', false);
            $("#assignLeadModal").modal('toggle');
        });
    }

    /* Users Report - Start */
    function FilterUsersReport() {
        $("#filterUsersReportPage").hide();
        $("#tableUsersReportPage").show();
        $('#admin_users_report_table').DataTable().clear().destroy();
        MakeUsersReportTable();
    }

    function UsersReportBackButton() {
        $("#filterUsersReportPage").show();
        $("#tableUsersReportPage").hide();
    }

    function MakeUsersReportTable() {
        let User = $("#userFilter option:selected").val();
        let SearchSubType = $("#searchBy option:selected").val();
        let LeadSearchStartDate = '';
        let LeadSearchEndDate = '';
        if (typeof SearchSubType !== 'undefined') {
            if (SearchSubType === 'customRange') {
                LeadSearchStartDate = $("#customStartDate").val();
                LeadSearchEndDate = $("#customEndDate").val();
            } else {
                LeadSearchStartDate = $("#searchStartDate").val();
                LeadSearchEndDate = $("#searchEndDate").val();
            }
        }

        if (User === '7') {
          $("#table_header").html('').append('<tr><td>Full Name</td><td>TotalLeads</td><td>Interested</td><td>Not Interested</td><td>Do Not Call</td><td>No Answer</td><td>Assigned to Acquisition</td>');
          if ($("#admin_users_report_table").length) {
              $("#admin_users_report_table").DataTable({
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "bPaginate": true,
                  "ordering": true,
                  "pageLength": 50,
                  "lengthMenu": [
                      [50, 100, 200, 400],
                      ['50', '100', '200', '400']
                  ],
                  "ajax": {
                      "url": "{{url('/admin/users-report/all')}}",
                      "type": "POST",
                      "data": {
                          User: $("#userFilter option:selected").val(),
                          StateFilter: $("#stateFilter option:selected").val(),
                          StartDate: LeadSearchStartDate,
                          EndDate: LeadSearchEndDate,
                      }
                  },
                  'columns': [
                      {data: 'full_name'},
                      {data: 'total_leads'},
                      {data: 'total_interested'},
                      {data: 'total_not_interested'},
                      {data: 'total_do_not_call'},
                      {data: 'total_no_answer'},
                      {data: 'total_assigned_to_aquisition'},
                  ],
              });
           }
        }
        else if (User === '8') {
          $("#table_header").html('').append('<tr><td>Full Name</td><td>TotalLeads</td><td>Total Under Contract</td><td>Interested</td><td>Not Interested</td><td>Do Not Call</td><td>Assigned to Acquisition</td><td>Closed WON</td><td>Deal Lost</td>');
          if ($("#admin_users_report_table").length) {
              $("#admin_users_report_table").DataTable({
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "bPaginate": true,
                  "ordering": true,
                  "pageLength": 50,
                  "lengthMenu": [
                      [50, 100, 200, 400],
                      ['50', '100', '200', '400']
                  ],
                  "ajax": {
                      "url": "{{url('/admin/users-report/all')}}",
                      "type": "POST",
                      "data": {
                          User: $("#userFilter option:selected").val(),
                          StateFilter: $("#stateFilter option:selected").val(),
                          StartDate: LeadSearchStartDate,
                          EndDate: LeadSearchEndDate,
                      }
                  },
                  'columns': [
                      {data: 'full_name'},
                      {data: 'total_leads'},
                      {data: 'total_under_contract'},
                      {data: 'total_interested'},
                      {data: 'total_not_interested'},
                      {data: 'total_do_not_call'},
                      {data: 'total_assigned_to_aquisition'},
                      {data: 'total_closed_won'},
                      {data: 'total_deal_lost'},
                  ],
              });
           }
        }
        else if (User === '4') {
          let TotalLeads = "Total Leads";
          let SendToInvestor = '<?= wordwrap("Send To Investor", 15, "<br>"); ?>';
          let Negotiating = "Negotiating";
          let SentContracttoInvestor = '<?= wordwrap("Sent Contract to Investor", 15, "<br>"); ?>';
          let SenttoTitle = '<?= wordwrap("Sent to Title", 10, "<br>"); ?>';
          let EMDReceived = '<?= wordwrap("EMD Received", 10, "<br>"); ?>';
          let EMDNotReceived = '<?= wordwrap("EMD Not Received", 15, "<br>"); ?>';
          let Inspection = "Inspection";
          let CloseOn = "Close On";
          let ClosedWON = "Closed WON";
          let DealLost = "Deal Lost";

          $("#table_header").html('').append('<tr><td>Full Name</td><td>'+ TotalLeads +'</td><td>'+ SendToInvestor +'</td><td>'+ Negotiating +'</td><td>'+ SentContracttoInvestor +'</td><td>'+ SenttoTitle +'</td><td>'+ EMDReceived +'</td><td>'+ EMDNotReceived +'</td><td>' + Inspection + '</td><td>' + CloseOn + '</td><td>' + ClosedWON +'</td><td>'+ DealLost +'</td>');
          if ($("#admin_users_report_table").length) {
              $("#admin_users_report_table").DataTable({
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "bPaginate": true,
                  "ordering": true,
                  "pageLength": 50,
                  "lengthMenu": [
                      [50, 100, 200, 400],
                      ['50', '100', '200', '400']
                  ],
                  "ajax": {
                      "url": "{{url('/admin/users-report/all')}}",
                      "type": "POST",
                      "data": {
                          User: $("#userFilter option:selected").val(),
                          StateFilter: $("#stateFilter option:selected").val(),
                          StartDate: LeadSearchStartDate,
                          EndDate: LeadSearchEndDate,
                      }
                  },
                  'columns': [
                      {data: 'full_name'},
                      {data: 'total_leads'},
                      {data: 'total_send_to_investor'},
                      {data: 'total_negotiating'},
                      {data: 'total_sent_contract_to_investor'},
                      {data: 'total_sent_to_title'},
                      {data: 'total_emd_received'},
                      {data: 'total_emd_not_received'},
                      {data: 'total_inspection'},
                      {data: 'total_close_on'},
                      {data: 'total_closed_won'},
                      {data: 'total_deal_lost'},
                  ],
              });
           }
        }
        else if (User === '3') {
          let TotalLeads = '<?= wordwrap("Total Leads", 15, "<br>"); ?>';
          let NotAccepted = '<?= wordwrap("Not Accepted", 15, "<br>"); ?>';
          let Accepted = '<?= wordwrap("Accepted", 15, "<br>"); ?>';
          let Negotiating = '<?= wordwrap("Negotiating", 15, "<br>"); ?>';
          let AgreementSent = '<?= wordwrap("Agreement Sent", 15, "<br>"); ?>';
          let AgreementReceived = '<?= wordwrap("Agreement Received", 25, "<br>"); ?>';

          $("#table_header").html('').append('<tr><td>Full Name</td><td>'+ TotalLeads +'</td><td>'+ NotAccepted +'</td><td>'+ Accepted +'</td><td>'+ Negotiating +'</td><td>'+ AgreementSent +'</td><td>'+ AgreementReceived +'</td>');
          if ($("#admin_users_report_table").length) {
              $("#admin_users_report_table").DataTable({
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "bPaginate": true,
                  "ordering": true,
                  "pageLength": 50,
                  "lengthMenu": [
                      [50, 100, 200, 400],
                      ['50', '100', '200', '400']
                  ],
                  "ajax": {
                      "url": "{{url('/admin/users-report/all')}}",
                      "type": "POST",
                      "data": {
                          User: $("#userFilter option:selected").val(),
                          StateFilter: $("#stateFilter option:selected").val(),
                          StartDate: LeadSearchStartDate,
                          EndDate: LeadSearchEndDate,
                      }
                  },
                  'columns': [
                      {data: 'full_name'},
                      {data: 'total_leads'},
                      {data: 'total_not_accepted'},
                      {data: 'total_accepted'},
                      {data: 'total_negotiating'},
                      {data: 'total_agreement_sent'},
                      {data: 'total_agreement_received'}
                  ],
              });
           }
        }
        else if (User === '9') {
          let IncomingTotalLeads = '<?= wordwrap("Incoming Total Leads", 15, "<br>"); ?>';
          let OutgoingTotalLeads = '<?= wordwrap("Outgoing Total Leads", 15, "<br>"); ?>';
          let InTotalUnderContract = '<?= wordwrap("In-Total Under Contract", 15, "<br>"); ?>';
          let OutTotalUnderContract = '<?= wordwrap("Out-Total Under Contract", 15, "<br>"); ?>';
          let InClosedWON = '<?= wordwrap("In-Closed WON", 15, "<br>"); ?>';
          let InDealLost = '<?= wordwrap("In-Deal Lost", 15, "<br>"); ?>';
          let OutClosedWON = '<?= wordwrap("Out-Closed WON", 15, "<br>"); ?>';
          let OutDealLost = '<?= wordwrap("Out-Deal Lost", 15, "<br>"); ?>';

          $("#table_header").html('').append('<tr><td>Full Name</td><td>'+ IncomingTotalLeads +'</td><td>'+ OutgoingTotalLeads +'</td><td>'+ InTotalUnderContract +'</td><td>'+ OutTotalUnderContract +'</td><td>'+ InClosedWON +'</td><td>'+ InDealLost +'</td><td>'+ OutClosedWON +'</td><td>'+ OutDealLost +'</td>');
          if ($("#admin_users_report_table").length) {
              $("#admin_users_report_table").DataTable({
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "bPaginate": true,
                  "ordering": true,
                  "pageLength": 50,
                  "lengthMenu": [
                      [50, 100, 200, 400],
                      ['50', '100', '200', '400']
                  ],
                  "ajax": {
                      "url": "{{url('/admin/users-report/all')}}",
                      "type": "POST",
                      "data": {
                          User: $("#userFilter option:selected").val(),
                          StateFilter: $("#stateFilter option:selected").val(),
                          StartDate: LeadSearchStartDate,
                          EndDate: LeadSearchEndDate,
                      }
                  },
                  'columns': [
                      {data: 'full_name'},
                      {data: 'incoming_total_leads'},
                      {data: 'outgoing_total_leads'},
                      {data: 'in_total_under_contract'},
                      {data: 'out_total_under_contract'},
                      {data: 'in_closed_won'},
                      {data: 'in_deal_lost'},
                      {data: 'out_closed_won'},
                      {data: 'out_deal_lost'},
                  ],
              });
           }
        }
    }

    /* Users Report - End */

    /* Add New User - Step Form - Start */
    function displayUserContactInformationSection() {
        if ($('#firstname').val() && $('#lastname').val() && $('#dob').val()) {
          $(window).scrollTop(0);
          $("#UserInformationBlock").hide();
          $("#UserContactInformationBlock").show();
          $(".step2").removeClass("disabled");
          $(".step2").addClass("complete");
        }
        else {
          if ($('#firstname').val())
          {
              $('#f_name').hide();
          }
          else
          {
              $("#firstname").keyup(function(){
                  $('#f_name').hide();
              });
              $('#f_name').show();
              $("#f_name").html("First Name is required !").css("color","red");
          }
          // Last Name
          if ($('#lastname').val())
          {
              $('#l_name').hide();
          }
          else
          {
              $("#lastname").keyup(function(){
                  $('#l_name').hide();
              });
              $('#l_name').show();
              $("#l_name").html("Last Name is required !").css("color","red");
          }
          // DOB
          if ($('#dob').val())
          {
              $('#user_dob').hide();
          }
          else
          {
              $("#dob").keyup(function(){
                  $('#user_dob').hide();
              });
              $('#user_dob').show();
              $("#user_dob").html("Date of birth is required !").css("color","red");
          }
       }
    }

    /* Add New User - Step Form - Start */
   /* Add New User - Step Form - Start */
       function displayUserIdentificationSection() {
        if ($('#phone').val() && $('#email').val())
            {
            $(window).scrollTop(0);
            $("#UserContactInformationBlock").hide();
            $("#UserIdentificationBlock").show();
            $(".step3").removeClass("disabled");
            $(".step3").addClass("complete");
            }
            else
            {
            if ($('#email').val())
            {
                $('#user_email').hide();
            }
            else
            {
                $("#email").keyup(function(){
                $('#user_email').hide();
                });
                $("#user_email").html("Email is required !").css("color","red");
            }
            // L Name
            if ($('#phone').val())
            {
                $('#user_phone').hide();
            }
            else
            {
                $("#phone").keyup(function(){
                $('#user_phone').hide();
                });
                $("#user_phone").html("Phone Number 1 is required !").css("color","red");
            }

            }
        }

    function displayBackUserInformationSection() {
        $(window).scrollTop(0);
        $("#UserContactInformationBlock").hide();
        $("#UserInformationBlock").show();
        $(".step2").removeClass("complete");
        $(".step2").addClass("disabled");
    }

    function displayBackUserContactInformationSection() {
        $(window).scrollTop(0);
        $("#UserIdentificationBlock").hide();
        $("#UserContactInformationBlock").show();
        $(".step3").removeClass("complete");
        $(".step3").addClass("disabled");
    }

    function AddDocumentField() {
        $("#_IdentityDocument2").show();
    }

    function RemoveDocumentField() {
        $("#_IdentityDocument2").hide();
        $("#identificationdocument2").val("");
    }

    /* Add New User - Step Form - End */

    /* Training Room - Start */
    function openTrainingRoom(id) {
        let values = id.split("_");
        window.location.href = "{{ url('/admin/training-room/folders/')}}" + "/" + values[1];
    }

    // function openTrainingRoom(id) {
    //     let values = id.split("_");
    //     window.location.href = "{{ url('/admin/training-room/details/')}}" + "/" + values[1];
    // }

    function MakeTrainingRoomFolderTable() {
        if ($("#admin_training_room_folders").length) {
            let TrainingRoomRoleId = $("#training_room_role_id").val();
            $("#admin_training_room_folders").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/training-room/folders/all')}}",
                    "type": "POST",
                    "data": {
                        "TrainingRoomRoleId": TrainingRoomRoleId
                    }
                },
                'columns': [
                    {data: 'id', orderable: false},
                    {data: 'name', orderable: false},
                    {data: 'picture', orderable: false},
                    {data: 'required', orderable: false},
                    {data: 'action', orderable: false},
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    function deleteTrainingRoomFolder(id) {
        let values = id.split('_');
        let training_room_role_id = $("#training_room_role_id").val();
        $("#delete_training_room_role_id").val(training_room_role_id);
        $("#deleteTrainingRoomFolderId").val(values[1]);
        $("#deleteTrainingRoomFolderModal").modal('toggle');
    }

    function copyTrainingRoomFolder(id) {
        let values = id.split('_');
        let training_room_role_id = $("#training_room_role_id").val();
        $("#copy_training_room_role_id").val(training_room_role_id);
        $("#trainingRoomFolderId").val(values[1]);
        $("#copyTrainingRoomFolderModal").modal('toggle');
    }

    function MakeTrainingRoomTable() {
        if ($("#admin_training_room").length) {
            $("#admin_training_room").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/training-room/all')}}",
                    "type": "POST",
                    "data": {
                        "TrainingRoomFolderId": $("#training_room_folder_id").val(),
                        "TrainingRoomRoleId": $("#training_room_role_id").val()
                    }
                },
                'columns': [
                    {data: 'id', orderable: false},
                    {data: 'type', orderable: false},
                    {data: 'title', orderable: false},
                    {data: 'action', orderable: false},
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    function copyTrainingRoomItem(id) {
        let values = id.split('_');
        $("#trainingRoomItemId").val(values[1]);
        $("#copy_training_room_role_id").val($("#training_room_role_id").val());
        $("#copy_training_room_folder_id").val($("#training_room_folder_id").val());
        $("#copyTrainingRoomItemModal").modal('toggle');
    }

    function GetFolders(RoleId) {
        <?php $Url = url('admin/training-room/folders/get'); ?>
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                Role: RoleId
            }
        }).done(function (data) {
            let s = data;
            s = s.replace(/\\n/g, "\\n")
                .replace(/\\'/g, "\\'")
                .replace(/\\"/g, '\\"')
                .replace(/\\&/g, "\\&")
                .replace(/\\r/g, "\\r")
                .replace(/\\t/g, "\\t")
                .replace(/\\b/g, "\\b")
                .replace(/\\f/g, "\\f");
            // remove non-printable and other non-valid JSON chars
            s = s.replace(/[\u0000-\u0019]+/g,"");
            let Details = JSON.parse(s);
            $("#copy_folder").html('').html(Details);
        });
    }

    function openTrainingRoomTypeModal(e) {
        $("#trainingRoomTypeModal").modal('toggle');
    }

    function deleteTrainingRoom(id) {
        let values = id.split('_');
        $("#delete_training_room_role_id").val($("#training_room_role_id").val());
        $("#delete_training_room_folder_id").val($("#training_room_folder_id").val());
        $("#deleteTrainingRoomId").val(values[1]);
        $("#deleteTrainingRoomModal").modal('toggle');
    }

    function NumberQuizQuestions() {
        let QuestionNumber = 0;
        const elements = document.querySelectorAll('.add_quiz_question_label');
        Array.from(elements).forEach((element, index) => {
            // conditional logic here.. access element
            QuestionNumber++;
            element.innerHTML = "Question " + QuestionNumber;
        });
    }

    function NumberDocumentNumbers() {
        let QuestionNumber = 0;
        const elements = document.querySelectorAll('.add_document_label');
        Array.from(elements).forEach((element, index) => {
            // conditional logic here.. access element
            QuestionNumber++;
            element.innerHTML = "Document " + QuestionNumber;
        });
    }

    // FAQ Section - Start
    function MakeTrainingRoomFaqsTable() {
        if ($("#admin_training_room_faqs").length) {
            $("#admin_training_room_faqs").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('admin/training-room/faqs/all')}}",
                    "type": "POST",
                    "data": {}
                },
                'columns': [
                    {data: 'checkbox', orderable: false},
                    {data: 'id'},
                    {data: 'question'},
                    {data: 'answer'},
                    {data: 'action', orderable: false},
                ],
                'order': [1, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).on('page.dt', function () {
                $("#allFaqCheckBox").prop('checked', false);
                $("#deleteAllQuestionBtn").hide();
            }).on('length.dt', function (e, settings, len) {
                $("#allFaqCheckBox").prop('checked', false);
                $("#deleteAllQuestionBtn").hide();
            }).on('draw.dt', function () {
                $(".allFaqCheckBox").addClass('d-none');
                $(".allFaqActionCheckBoxColumn").addClass('w-0');
            });
        }
    }

    /* Knowledge Zone action in bulk (Delete) - Start */
    let faqActionCheckboxCounter = 0;
    function HandleFaqAction() {
      if (faqActionCheckboxCounter === 0) {
        $(".allFaqCheckBox").removeClass('d-none');
        $(".allFaqActionCheckBoxColumn").removeClass('w-0');
        $(".allFaqActionCheckBoxColumn").attr('style', 'padding', '10px');
        faqActionCheckboxCounter = 1;
      } else {
        $(".allFaqCheckBox").addClass('d-none');
        $(".allFaqActionCheckBoxColumn").addClass('w-0');
        $(".allFaqActionCheckBoxColumn").attr('style', 'padding', '0');
        $("#deleteAllFaqBtn").hide();
        faqActionCheckboxCounter = 0;
      }
    }

    function CheckAllFaqRecords(e) {
        let Status = $(e).prop('checked');
        if(Status){
            /*check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', true);
            });
            $("#deleteAllFaqBtn").show();
        }
        else{
            /*un check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', false);
            });
            $("#deleteAllFaqBtn").hide();
        }
    }

    function CheckIndividualFaqCheckbox() {
        let count = 0;
        $(".checkAllBox").each(function (i, obj) {
            if($(obj).prop('checked')){
                count++;
            }
        });
        if(count === 0){
            /*Not Selected*/
            $("#deleteAllFaqBtn").hide();
        }
        else{
            /*Some Selected*/
            $("#deleteAllFaqBtn").show();
        }
    }

    function DeleteMultipleFaq() {
        let deleteSelectedFaqFormUrl = $("#deleteSelectedFaqFormUrl").val();
        $('#faqForm').attr('action', deleteSelectedFaqFormUrl);
        $("#deleteFaqModal").modal('toggle');
    }

    function OpenAddFaqModal() {
        $("#addFaqModal").modal('toggle');
    }
    /* Knowledge Zone action in bulk (Delete) - End */

    function DeleteFaq(id) {
        id = id.split('_')[1];
        $("#deleteFaqId").val(id);
        $("#deleteFaqModal").modal('toggle');
    }

    function EditFaq(id) {
        id = id.split('_')[1];
        $("#editFaqId").val(id);
        $.ajax({
            type: "post",
            url: "{{url('/faq/details')}}",
            data: {Id: id}
        }).done(function (data) {
            data = JSON.parse(data);
            $("#question1").val(data[0].question);
            EditFaqAnswerEditor.destroy();
            if ($("#faqAnswer1").length) {
                ClassicEditor.create(document.querySelector('#faqAnswer1'), {
                    ckfinder: {
                        uploadUrl: 'http://localhost/eliteempire/public/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
                    },
                    link: {
                        addTargetToExternalLinks: true
                    },
                })
                .then(editor => {
                    EditFaqAnswerEditor = editor;
                    editor.setData(data[0].answer);
                })
                .catch(error => {

                });
            }
            $("#editFaqModal").modal('toggle');
        });
    }

    function SearchFaqActive(e) {
        $(e).addClass('active');
        $(".searchIcon").removeClass('searchIcon1').addClass('searchIcon2');
    }

    function SearchFaqBlur(e) {
        if($(e).val() === ''){
            $(e).removeClass('active');
            $(".searchIcon").removeClass('searchIcon2').addClass('searchIcon1');
        }
    }

    function MoveFaqSearchIcon(e) {
        if (e === 1) {
            $(".searchIcon").removeClass('searchIcon1').addClass('searchIcon2');
        } else {
            if(!$("#searchFaq").focus()){
                $(".searchIcon").removeClass('searchIcon2').addClass('searchIcon1');
            }
        }
    }

    function OpenQuestionAnswerModal(id) {
      ClassicEditor.defaultConfig = {
        toolbar: {
          items: [
          ]
        },
        image: {
          toolbar: [
            'imageStyle:full','imageStyle:side','|','imageTextAlternative'
          ]
        },
        table: {
          contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
        },
        language: 'en'
      };
      $.ajax({
          type: "post",
          url: "{{url('/faq/details')}}",
          data: {Id: id}
      }).done(function (data) {
          data = JSON.parse(data);
          $("#question1").val(data[0].question);
          Answer = data;
          EditFaqAnswerEditor.destroy();
          if ($("#faqAnswer1").length) {
              ClassicEditor.create(document.querySelector('#faqAnswer1'), {
                  ckfinder: {
                      uploadUrl: 'http://localhost/eliteempire/public/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
                  },
                  link: {
                      addTargetToExternalLinks: true
                  },
              })
              .then(editor => {
                  EditFaqAnswerEditor = editor;
                  editor.setData(data[0].answer);
              })
              .catch(error => {

              });
          }
          $("#editFaqModal").modal('toggle');
      });
    }
    // FAQ Section - End

    function MarkVideoAsComplete(StepCount) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            $Url = url('cold_caller/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
            $Url = url('affiliate/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
            $Url = url('realtor/training/assignment/complete');
        }
        ?>
        let AssignmentId = $("#assignmentId_" + StepCount).val();
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: AssignmentId,
                courseid: $("#training_course_id").val()
            }
        }).done(function (data) {
            data = JSON.parse(data);
            window.location.reload();
        });
    }

    function MarkArticleAsComplete(StepCount) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            $Url = url('cold_caller/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
            $Url = url('affiliate/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
            $Url = url('realtor/training/assignment/complete');
        }
        ?>
        let AssignmentId = $("#assignmentId_" + StepCount).val();
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: AssignmentId,
                courseid: $("#training_course_id").val()
            }
        }).done(function (data) {
            data = JSON.parse(data);
            window.location.reload();
        });
    }

    function MarkQuizAsComplete(StepCount) {
        let AssignmentId = $("#assignmentId_" + StepCount).val();
        let TotalQuestions = parseInt($("#questionsCount" + StepCount).val());
        let CorrectCount = 0;
        for (let i = 0; i < TotalQuestions; i++){
            let Selected = $("input[name=question" + StepCount + i + "]:checked").val();
            let Answer = $("#questionAnswer" + StepCount + i).val();
            if(Selected === undefined){
                $("#quizQuestionDiv" + StepCount + i).css('border', '1px solid red');
            }
            else{
                if(Selected === Answer){
                    $("#quizQuestionDiv" + StepCount + i).css('border', 'none');
                    CorrectCount++;
                }
                else{
                    $("#quizQuestionDiv" + StepCount + i).css('border', '1px solid red');
                }
            }
        }

        let Percentage = ((CorrectCount) / TotalQuestions) * 100;
        if(Percentage >= 70){
            let success_message = [];
            success_message[0] = "✅Yes that's right, keep it up!";
            success_message[1] = "✅Correct! You nailed it.";
            success_message[2] = "✅Perfect! Your hard work is paying off.";
            let random = getRndInteger(0, 3);
            if (random > 2) {
              random = 2;
            }
            $("#quizResultsModalImg").attr("src", "{{asset('public/assets/images/trophy.png')}}");
            // $("#resultStatusMessage").text("CONGRATULATIONS!");
            $("#resultStatusMessage").text(success_message[random]);
            $("#continueBtn").css("display", "initial");
            $("#againBtn").css("display", "none");
        }
        else{
            let error_message = [];
            error_message[0] = "❌Not quite right";
            error_message[1] = "❌Keep trying- mistakes can help us grow.";
            let random = getRndInteger(0, 2);
            if (random > 1) {
              random = 1;
            }
            $("#quizResultsModalImg").attr("src", "{{asset('public/assets/images/sad-emoji.png')}}");
            // $("#resultStatusMessage").text("FAILED!");
            $("#resultStatusMessage").text(error_message[random]);
            $("#continueBtn").css("display", "none");
            $("#againBtn").css("display", "initial");
        }
        $("#resultPercentage").text(Math.round(parseFloat(Percentage)) + "%.");
        $("#quizAssignmentId").val(AssignmentId);
        $("#quizResultsModal").modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    function getRndInteger(min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    }

    function ResultContinue() {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            $Url = url('cold_caller/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
            $Url = url('affiliate/training/assignment/complete');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
            $Url = url('realtor/training/assignment/complete');
        }
        ?>
        let AssignmentId = $("#quizAssignmentId").val();
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {
                id: AssignmentId,
                courseid: $("#training_course_id").val()
            }
        }).done(function (data) {
            data = JSON.parse(data);
            window.location.reload();
        });
    }

    function ResultTryAgain() {
        location.reload();
    }

    $(document).ready(function(){
        shuffle();
    });

    function shuffle() {
        let container = document.getElementsByClassName("question-options-div");
        for(let k = 0; k < container.length; k++){
            let elementsArray = Array.prototype.slice.call(container[k].getElementsByClassName('question-option-label'));
            elementsArray.forEach(function(element){
                container[k].removeChild(element);
            });
            shuffleArray(elementsArray);
            elementsArray.forEach(function(element){
                container[k].appendChild(element);
            });
        }
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));
            let temp = array[i];
            array[i] = array[j];
            array[j] = temp;
        }
        return array;
    }

    function SearchFaq(e) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 7) {
            $Url = url('cold_caller/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 8) {
            $Url = url('affiliate/training/faqs/search');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 9) {
            $Url = url('realtor/training/faqs/search');
        }
        ?>
        let Value = $(e).val();
        if(Value !== ''){
            $("#mainFaqDiv").css('display', 'none');
            $.ajax({
                type: "post",
                url: "{{$Url}}",
                data: {
                    Text: Value
                }
            }).done(function (data) {
                data = JSON.parse(data);
                let Rows = '';
                for(let k = 0; k < data.length; k++){
                    if(data.length === k+1){
                        Rows += '<div class="col-md-12">' +
                            // '       <p class="mb-1" onclick="OpenQuestionAnswerModal(this);" style="font-size: 16px; font-weight: bold; cursor: pointer;" data-answer="' + data[k].answer + '">Q.&nbsp;&nbsp;' + data[k].question + '</p>' +
                            '       <p class="mb-1" onclick="OpenQuestionAnswerModal('+ data[k].id +');" style="font-size: 16px; font-weight: bold; cursor: pointer;">Q.&nbsp;&nbsp;' + data[k].question + '</p>' +
                            '   </div>';
                    }
                    else{
                        Rows += '<div class="col-md-12 mb-3">' +
                            // '       <p class="mb-1" onclick="OpenQuestionAnswerModal(this);" style="font-size: 16px; font-weight: bold; cursor: pointer;" data-answer="' + data[k].answer + '">Q.&nbsp;&nbsp;' + data[k].question + '</p>' +
                            '       <p class="mb-1" onclick="OpenQuestionAnswerModal('+ data[k].id +');" style="font-size: 16px; font-weight: bold; cursor: pointer;">Q.&nbsp;&nbsp;' + data[k].question + '</p>' +
                            '   </div>';
                    }
                }
                $("#searchResultsFaqDiv").css('display', 'block').html('').html(Rows);
            });
        }
        else{
            $("#mainFaqDiv").css('display', 'block');
            $("#searchResultsFaqDiv").css('display', 'none');
        }
    }

    function InitializeCustomFaqsSearch() {
        $("#searchFaq").on('focus', function () {
            $(this).parent('label').addClass('active');
        }).on('blur', function () {
            if($(this).val().length === 0)
                $(this).parent('label').removeClass('active');
        });
    }

    function SearchFolder(e) {
        <?php
          $Url = url('training-room/course/search');
        ?>
        let Value = $(e).val();
        if(Value !== ''){
            $("#TrainingRoomFolders").css('display', 'none');
            $.ajax({
                type: "post",
                url: "{{$Url}}",
                data: {
                    Text: Value
                }
            }).done(function (data) {
              let s = data;
              s = s.replace(/\\n/g, "\\n")
                  .replace(/\\'/g, "\\'")
                  .replace(/\\"/g, '\\"')
                  .replace(/\\&/g, "\\&")
                  .replace(/\\r/g, "\\r")
                  .replace(/\\t/g, "\\t")
                  .replace(/\\b/g, "\\b")
                  .replace(/\\f/g, "\\f");
              // remove non-printable and other non-valid JSON chars
              s = s.replace(/[\u0000-\u0019]+/g,"");
              let Record = JSON.parse(s);
              if (Record.total_record > 0) {
                let Courses = JSON.parse(Record.courses);
                $("#searchResultsCourseDiv").css('display', 'flex').html('').html(Courses);
              }
              else {
                $("#searchResultsCourseDiv").css('display', 'none');
                $("#TrainingRoomFolders").css('display', 'flex');
              }
            });
        }
        else{
            $("#searchResultsCourseDiv").css('display', 'none');
            $("#TrainingRoomFolders").css('display', 'flex');
        }
    }
    /* Training Room - End */

    /* User Activities - Start */
    function MakeUserActivityTable(id) {
        $("#userActivityModal").modal('toggle');
        if ($("#user_activities_table").length) {
            $('#user_activities_table').DataTable().clear().destroy();
            let values = id.split("_");
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('/admin/user/activity/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                $Url = url('/global_manager/user/activity/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                $Url = url('/acquisition_manager/user/activity/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                $Url = url('/disposition_manager/user/activity/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
                $Url = url('/disposition_representative/user/activity/all');
            }
            ?>
            $("#user_activities_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST",
                    "data": {
                      "UserId": values[1]
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user'},
                    {data: 'message'},
                ],
                'order': [0, 'desc']
            });
        }
    }
    /* User Activities - End */

    /* Buisness Account Section - Start */
    function MakeBuissnessAccountTable() {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('/admin/buissness_accounts/all');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('/global_manager/buissness_accounts/all');
        } elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
            $Url = url('disposition_representative/buissness_accounts/all');
        }
        ?>
        if ($("#admin_buissness_account_table").length) {
            $("#admin_buissness_account_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'checkbox', orderable: false},
                    {data: 'id'},
                    {data: 'user_information'},
                    {data: 'contact'},
                    {data: 'status'},
                    {data: 'action', orderable: false},
                ],
                'order': [1, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }).on('page.dt', function () {
                $("#allAccountsCheckBox").prop('checked', false);
                $("#deleteAllAccountsBtn").hide();
            }).on('length.dt', function (e, settings, len) {
                $("#allAccountsCheckBox").prop('checked', false);
                $("#deleteAllAccountsBtn").hide();
            }).on('draw.dt', function () {
                $(".allAccountsCheckBox").addClass('d-none');
                $(".allAccountsActionCheckBoxColumn").addClass('w-0');
            });
        }
    }

    function checkBuisnessAccountType() {
      $("#buisnessAccountTypeModal").modal('toggle');
    }

    /* Accounts action in bulk (Delete) - Start */
    let accountActionCheckboxCounter = 0;
    function HandleBuisnessAccountAction() {
      if (accountActionCheckboxCounter === 0) {
        $(".allAccountsCheckBox").removeClass('d-none');
        $(".allAccountsActionCheckBoxColumn").removeClass('w-0');
        $(".allAccountsActionCheckBoxColumn").attr('style', 'padding', '10px');
        accountActionCheckboxCounter = 1;
      } else {
        $(".allAccountsCheckBox").addClass('d-none');
        $(".allAccountsActionCheckBoxColumn").addClass('w-0');
        $(".allAccountsActionCheckBoxColumn").attr('style', 'padding', '0');
        $("#deleteAllAccountsBtn").hide();
        accountActionCheckboxCounter = 0;
      }
    }

    function CheckAllAccountsRecord(e) {
        let Status = $(e).prop('checked');
        if(Status){
            /*check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', true);
            });
            $("#deleteAllAccountsBtn").show();
        }
        else{
            /*un check all*/
            $(".checkAllBox").each(function (i, obj) {
                $(obj).prop('checked', false);
            });
            $("#deleteAllAccountsBtn").hide();
        }
    }

    function CheckIndividualAccountCheckbox() {
        let count = 0;
        $(".checkAllBox").each(function (i, obj) {
            if($(obj).prop('checked')){
                count++;
            }
        });
        if(count === 0){
            /*Not Selected*/
            $("#deleteAllAccountsBtn").hide();
        }
        else{
            /*Some Selected*/
            $("#deleteAllAccountsBtn").show();
        }
    }

    function DeleteMultipleAccounts() {
        let deleteSelectedUsersFormUrl = $("#deleteSelectedUsersFormUrl").val();
        $('#accountsForm').attr('action', deleteSelectedUsersFormUrl);
        $("#deleteUserModal").modal('toggle');
    }
    /* Accounts action in bulk (Delete) - End */

    /* Buisness Account Section - End */

    /* Investors Section - Start */
    function displayBuyingCriteriaSection() {
        if ($('#buisness_name').val() && $('#phone').val() && $('#email').val()) {
          $(window).scrollTop(0);
          $("#GeneralInformationBlock").hide();
          $("#BuyingCriteriaBlock").show();
          $(".step2").removeClass("disabled");
          $(".step2").addClass("complete");
        } else {
          // Buisness Name
          if ($('#buisness_name').val()) {
              $('#b_name').hide();
          }
          else {
              $("#buisness_name").keyup(function(){
                  $('#b_name').hide();
              });
              $('#b_name').show();
              $("#b_name").html("Buisness name is required !").css("color","red");
          }
          // Phone Number 1
          if ($('#phone').val() !== '') {
              $('#p_phone1').hide();
          }
          else {
              $("#phone").keyup(function(){
                  $('#p_phone1').hide();
              });
              $('#p_phone1').show();
              $("#p_phone1").html("Phone number 1 is required !").css("color","red");
          }
          // Email Address
          if ($('#email').val() !== '') {
              $('#e_email1').hide();
          }
          else {
              $("#email").keyup(function(){
                  $('#e_email1').hide();
              });
              $('#e_email1').show();
              $("#e_email1").html("Email address is required !").css("color","red");
          }
       }
    }

    function displayBackGeneralInformationSection() {
        $(window).scrollTop(0);
        $("#BuyingCriteriaBlock").hide();
        $("#GeneralInformationBlock").show();
        $(".step2").removeClass("complete");
        $(".step2").addClass("disabled");
    }

    function MakeInvestorsTable() {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('/admin/investor/all');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('/global_manager/investor/all');
        }
        ?>
        if ($("#admin_investor_table").length) {
            $("#admin_investor_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_information'},
                    {data: 'contact'},
                    {data: 'status'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }
    /* Investors Section - End */

    /* Title Companies Section - Start */
    function MakeTitleCompaniesTable() {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('/admin/title_company/all');
        } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('/global_manager/title_company/all');
        }
        ?>
        if ($("#admin_title_company_table").length) {
            $("#admin_title_company_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST"
                },
                'columns': [
                    {data: 'id'},
                    {data: 'user_information'},
                    {data: 'contact'},
                    {data: 'status'},
                    {data: 'action', orderable: false},
                ],
                'order': [0, 'desc'],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    function editTitleCompany(e) {
        <?php
        $Url = '';
        if (\Illuminate\Support\Facades\Session::get('user_role') == 1) {
            $Url = url('admin/title_company/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 2) {
            $Url = url('global_manager/title_company/edit');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
            $Url = url('disposition_representative/title_company/edit');
        }
        ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }
    /* Title Companies Section - End */

    /* Serving Locations Section - Start */
    function MakeServingLocation() {
      ServingLocationCounter++;
      let servinglocation =
      '<div class="card mt-3" id="servinglocation_'+ ServingLocationCounter +'">'+
        '<div class="card-body">'+
          '<h6 class="card-title">'+
              'Serving Location'+
          '</h6>'+
          '<div class="row">'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="propertyClassification_'+ ServingLocationCounter +'">Property Classification</label>'+
                '<select name="propertyClassification[]" id="propertyClassification_'+ ServingLocationCounter +'" class="form-control propertyClassification"  multiple>'+
                    '<option value="">Select</option>'+
                    '<option value="residential">Residential</option>'+
                    '<option value="commercial">Commercial</option>'+
                    '<option value="industrial">Industrial</option>'+
                    '<option value="agricultural">Agricultural</option>'+
                    '<option value="vacant">Vacant Lot</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="propertyType_'+ ServingLocationCounter +'">Property Type</label>'+
                '<select name="propertyType[]" id="propertyType_'+ ServingLocationCounter +'" class="form-control propertyType" multiple>'+
                    '<option value="">Select Property Type</option>'+
                    '<option value="singleFamily">Single Family</option>'+
                    '<option value="condominium">Condominium</option>'+
                    '<option value="townhouse">Townhouse</option>'+
                    '<option value="multiFamily">Multi family</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="multiFamilyType_'+ ServingLocationCounter +'">Multi-Family</label>'+
                '<select name="multiFamilyType[]" id="multiFamilyType_'+ ServingLocationCounter +'" class="form-control multiFamilyType"  multiple>'+
                  '<option value="">Select</option>'+
                  '<option value="duplexes">Duplexes</option>'+
                  '<option value="3_4_unit_or_5_plus">3-4 Unit or 5 plus</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="constructionType_'+ ServingLocationCounter +'">Construction Type</label>'+
                '<select name="constructionType[]" id="constructionType_'+ ServingLocationCounter +'" class="form-control constructionType" multiple>'+
                    '<option value="">Select</option>'+
                    '<option value="wood">Wood</option>'+
                    '<option value="block">Block</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="state_'+ ServingLocationCounter +'">State</label>'+
                '<select class="form-control states" name="serving_state[]" id="state_'+ ServingLocationCounter +'" class="form-control" onchange="LoadServingLocationStateCountyCity(this.id);" >'+
                    '<option value="" selected>Select State</option>';
                    for(let i=0; i < states.length; i++)
                    {
                        servinglocation += '<option value="'+states[i]['name']+'">'+ states[i]['name'] +'</option>';
                    }
                servinglocation +=
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="county_'+ ServingLocationCounter +'">County</label>'+
                '<select class="form-control counties" name="serving_county[]" id="county_'+ ServingLocationCounter +'" multiple>'+
                    '<option value="">Select County</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="city_'+ ServingLocationCounter +'">City</label>'+
                '<select name="serving_city[]" id="city_'+ ServingLocationCounter +'" class="form-control cities" multiple>'+
                    '<option value="">Select City</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="zipcode_'+ ServingLocationCounter +'">Zip code</label>'+
                '<input type="text" name="serving_zipcode[]" id="zipcode_'+ ServingLocationCounter +'" class="form-control" placeholder="Enter Your Zip Code"/>'+
            '</div>'+
            '<div class="col-md-12 mb-3 mt-3">'+
              '<span data-repeater-create="" class="btn btn-outline-danger btn-sm float-right" id="remove_'+ ServingLocationCounter +'" onclick="RemoveServingLocation(this.id);">'+
                '<span class="fa fa-trash"></span>&nbsp;Delete</span>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      $("#ServingLocationBlock").append(servinglocation);
      $(".states").select2();
      $(".cities").select2();
      $(".counties").select2();
      $(".propertyClassification").select2();
      $(".propertyType").select2();
      $(".multiFamilyType").select2();
      $(".constructionType").select2();
    }

    function MakeEditServingLocation() {
      let _ServingLocationCounter = $("#_servingLocationCounter").val();
      _ServingLocationCounter++;
      let servinglocation =
      '<div class="card mt-3" id="servinglocation_'+ _ServingLocationCounter +'">'+
        '<div class="card-body">'+
          '<h6 class="card-title">'+
              'Serving Location'+
          '</h6>'+
          '<div class="row">'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="propertyClassification_'+ _ServingLocationCounter +'">Property Classification</label>'+
                '<select name="propertyClassification[]" id="propertyClassification_'+ _ServingLocationCounter +'" class="form-control propertyClassification"  multiple>'+
                    '<option value="">Select</option>'+
                    '<option value="residential">Residential</option>'+
                    '<option value="commercial">Commercial</option>'+
                    '<option value="industrial">Industrial</option>'+
                    '<option value="agricultural">Agricultural</option>'+
                    '<option value="vacant">Vacant Lot</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="propertyType_'+ _ServingLocationCounter +'">Property Type</label>'+
                '<select name="propertyType[]" id="propertyType_'+ _ServingLocationCounter +'" class="form-control propertyType" multiple>'+
                    '<option value="">Select Property Type</option>'+
                    '<option value="singleFamily">Single Family</option>'+
                    '<option value="condominium">Condominium</option>'+
                    '<option value="townhouse">Townhouse</option>'+
                    '<option value="multiFamily">Multi family</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="multiFamilyType_'+ _ServingLocationCounter +'">Multi-Family</label>'+
                '<select name="multiFamilyType[]" id="multiFamilyType_'+ _ServingLocationCounter +'" class="form-control multiFamilyType"  multiple>'+
                  '<option value="">Select</option>'+
                  '<option value="duplexes">Duplexes</option>'+
                  '<option value="3_4_unit_or_5_plus">3-4 Unit or 5 plus</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-2 mt-3">'+
                '<label class="w-100" for="constructionType_'+ _ServingLocationCounter +'">Construction Type</label>'+
                '<select name="constructionType[]" id="constructionType_'+ _ServingLocationCounter +'" class="form-control constructionType" multiple>'+
                    '<option value="">Select</option>'+
                    '<option value="wood">Wood</option>'+
                    '<option value="block">Block</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="state_'+ _ServingLocationCounter +'">State</label>'+
                '<select class="form-control states" name="serving_state[]" id="state_'+ _ServingLocationCounter +'" class="form-control" onchange="LoadServingLocationStateCountyCity(this.id);">'+
                    '<option value="" selected>Select State</option>';
                    for(let i=0; i < states.length; i++)
                    {
                        servinglocation += '<option value="'+states[i]['name']+'">'+ states[i]['name'] +'</option>';
                    }
                servinglocation +=
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="city_'+ _ServingLocationCounter +'">City</label>'+
                '<select name="serving_city[]" id="city_'+ _ServingLocationCounter +'" class="form-control cities" multiple>'+
                    '<option value="">Select City</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="county_'+ _ServingLocationCounter +'">County</label>'+
                '<select class="form-control counties" name="serving_county[]" id="county_'+ _ServingLocationCounter +'" multiple>'+
                    '<option value="">Select County</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="zipcode_'+ _ServingLocationCounter +'">Zip code</label>'+
                '<input type="text" name="serving_zipcode[]" id="zipcode_'+ _ServingLocationCounter +'" class="form-control" placeholder="Enter Your Zip Code"/>'+
            '</div>'+
            '<div class="col-md-12 mb-3 mt-3">'+
              '<span class="btn btn-outline-danger btn-sm float-right" id="remove_'+ _ServingLocationCounter +'" onclick="RemoveServingLocation(this.id);">'+
                '<span class="fa fa-trash"></span>&nbsp;Delete</span>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      $("#ServingLocationBlock").append(servinglocation);
      $("#_servingLocationCounter").val(_ServingLocationCounter);
      $(".states").select2();
      $(".cities").select2();
      $(".counties").select2();
      $(".propertyClassification").select2();
      $(".propertyType").select2();
      $(".multiFamilyType").select2();
      $(".constructionType").select2();
    }

    function RemoveServingLocation(id)
    {
      let values = id.split("_");
      $('#servinglocation_' + values[1]).remove();
    }

    $("#addInvestorForm").submit(function(e){
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/investor/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/investor/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/investor/store');
        }
      ?>
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let PropertyClassification = [];
      let PropertyType = [];
      let MultiFamily = [];
      let ConstructionType = [];
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (ServingLocationCounter + 1); i++) {
        let subPropertyClassificationArray = [];
        let subPropertyTypeArray = [];
        let subMultiFamilyArray = [];
        let subConstructionTypeArray = [];
        let subCountyArray = [];
        let subCityArray = [];
        let _PropertyClassification = $("#propertyClassification_" + i).val();
        let _PropertyType = $("#propertyType_" + i).val();
        let _MultiFamilyType = $("#multiFamilyType_" + i).val();
        let _ConstructionType = $("#constructionType_" + i).val();
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_PropertyClassification || _PropertyType || _MultiFamilyType || _ConstructionType || _State || _County || _City || _ZipCode) {

          if (_PropertyClassification) {
            for (let b = 0; b < _PropertyClassification.length; b++) {
              subPropertyClassificationArray.push(_PropertyClassification[b]);
            }
          }

          if (_PropertyType) {
            for (let b = 0; b < _PropertyType.length; b++) {
              subPropertyTypeArray.push(_PropertyType[b]);
            }
          }

          if (_MultiFamilyType) {
            for (let b = 0; b < _MultiFamilyType.length; b++) {
              subMultiFamilyArray.push(_MultiFamilyType[b]);
            }
          }

          if (_ConstructionType) {
            for (let b = 0; b < _ConstructionType.length; b++) {
              subConstructionTypeArray.push(_ConstructionType[b]);
            }
          }

          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }

          PropertyClassification.push(subPropertyClassificationArray);
          PropertyType.push(subPropertyTypeArray);
          MultiFamily.push(subMultiFamilyArray);
          ConstructionType.push(subConstructionTypeArray);
          State.push(_State);
          County.push(subCountyArray);
          City.push(subCityArray);
          ZipCode.push(_ZipCode);
        }
      }

      PropertyClassification = JSON.stringify(PropertyClassification);
      PropertyType = JSON.stringify(PropertyType);
      MultiFamily = JSON.stringify(MultiFamily);
      ConstructionType = JSON.stringify(ConstructionType);
      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='propertyClassification' value='" + PropertyClassification + "' type='hidden'>");
        $(form).append("<input name='propertyType' value='" + PropertyType + "' type='hidden'>");
        $(form).append("<input name='multiFamilyType' value='" + MultiFamily + "' type='hidden'>");
        $(form).append("<input name='constructionType' value='" + ConstructionType + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });

    $("#editInvestorForm").submit(function(e) {
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/investor/update');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/investor/update');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/investor/update');
        }
      ?>
      let _ServingLocationCounter = $("#_servingLocationCounter").val();
      _ServingLocationCounter = parseInt(_ServingLocationCounter);
      let Id = $("#_id").val();
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let OldEmail = $("#old_email").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let PropertyClassification = [];
      let PropertyType = [];
      let MultiFamily = [];
      let ConstructionType = [];
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (_ServingLocationCounter + 1); i++) {
        let subPropertyClassificationArray = [];
        let subPropertyTypeArray = [];
        let subMultiFamilyArray = [];
        let subConstructionTypeArray = [];
        let subCountyArray = [];
        let subCityArray = [];
        let _PropertyClassification = $("#propertyClassification_" + i).val();
        let _PropertyType = $("#propertyType_" + i).val();
        let _MultiFamilyType = $("#multiFamilyType_" + i).val();
        let _ConstructionType = $("#constructionType_" + i).val();
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_PropertyClassification || _PropertyType || _MultiFamilyType || _ConstructionType || _State || _County || _City || _ZipCode) {
          if (_PropertyClassification) {
            for (let b = 0; b < _PropertyClassification.length; b++) {
              subPropertyClassificationArray.push(_PropertyClassification[b]);
            }
          }

          if (_PropertyType) {
            for (let b = 0; b < _PropertyType.length; b++) {
              subPropertyTypeArray.push(_PropertyType[b]);
            }
          }

          if (_MultiFamilyType) {
            for (let b = 0; b < _MultiFamilyType.length; b++) {
              subMultiFamilyArray.push(_MultiFamilyType[b]);
            }
          }

          if (_ConstructionType) {
            for (let b = 0; b < _ConstructionType.length; b++) {
              subConstructionTypeArray.push(_ConstructionType[b]);
            }
          }

          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }

          PropertyClassification.push(subPropertyClassificationArray);
          PropertyType.push(subPropertyTypeArray);
          MultiFamily.push(subMultiFamilyArray);
          ConstructionType.push(subConstructionTypeArray);
          State.push(_State);
          County.push(subCountyArray);
          City.push(subCityArray);
          ZipCode.push(_ZipCode);
        }
      }

      PropertyClassification = JSON.stringify(PropertyClassification);
      PropertyType = JSON.stringify(PropertyType);
      MultiFamily = JSON.stringify(MultiFamily);
      ConstructionType = JSON.stringify(ConstructionType);
      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + Id + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='old_email' value='" + OldEmail + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='propertyClassification' value='" + PropertyClassification + "' type='hidden'>");
        $(form).append("<input name='propertyType' value='" + PropertyType + "' type='hidden'>");
        $(form).append("<input name='multiFamilyType' value='" + MultiFamily + "' type='hidden'>");
        $(form).append("<input name='constructionType' value='" + ConstructionType + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });
    /* Serving Locations Section - End */

    /* Title Company Section - Start */
    function MakeTitleCompanyServingLocation() {
      ServingLocationCounter++;
      let servinglocation =
      '<div class="card mt-3" id="servinglocation_'+ ServingLocationCounter +'">'+
        '<div class="card-body">'+
          '<h6 class="card-title">'+
              'Serving Location'+
          '</h6>'+
          '<div class="row">'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="state_'+ ServingLocationCounter +'">State</label>'+
                '<select class="form-control states" name="serving_state[]" id="state_'+ ServingLocationCounter +'" class="form-control" onchange="LoadServingLocationStateCountyCity(this.id);">'+
                    '<option value="">Select State</option>';
                    for(let i=0; i < states.length; i++)
                    {
                        servinglocation += '<option value="'+states[i]['name']+'">'+ states[i]['name'] +'</option>';
                    }
                servinglocation +=
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="county_'+ ServingLocationCounter +'">County</label>'+
                '<select class="form-control counties" name="serving_county[]" id="county_'+ ServingLocationCounter +'" multiple>'+
                    '<option value="">Select County</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="city_'+ ServingLocationCounter +'">City</label>'+
                '<select name="serving_city[]" id="city_'+ ServingLocationCounter +'" class="form-control cities" multiple>'+
                    '<option value="">Select City</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="zipcode_'+ ServingLocationCounter +'">Zip code</label>'+
                '<input type="text" name="serving_zipcode[]" id="zipcode_'+ ServingLocationCounter +'" class="form-control" placeholder="Enter Your Zip Code"/>'+
            '</div>'+
            '<div class="col-md-12 mb-3 mt-3">'+
              '<span data-repeater-create="" class="btn btn-outline-danger btn-sm float-right" id="remove_'+ ServingLocationCounter +'" onclick="RemoveServingLocation(this.id);">'+
                '<span class="fa fa-trash"></span>&nbsp;Delete</span>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      $("#ServingLocationBlock").append(servinglocation);
      $(".states").select2();
      $(".cities").select2();
      $(".counties").select2();
      $(".propertyClassification").select2();
      $(".propertyType").select2();
      $(".multiFamilyType").select2();
      $(".constructionType").select2();
    }

    function MakeTitleCompanyEditServingLocation() {
      let _ServingLocationCounter = $("#_servingLocationCounter").val();
      _ServingLocationCounter++;
      let servinglocation =
      '<div class="card mt-3" id="servinglocation_'+ _ServingLocationCounter +'">'+
        '<div class="card-body">'+
          '<h6 class="card-title">'+
              'Serving Location'+
          '</h6>'+
          '<div class="row">'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="state_'+ _ServingLocationCounter +'">State</label>'+
                '<select class="form-control states" name="serving_state[]" id="state_'+ _ServingLocationCounter +'" class="form-control" onchange="LoadServingLocationStateCountyCity(this.id);">'+
                    '<option value="">Select State</option>';
                    for(let i=0; i < states.length; i++)
                    {
                        servinglocation += '<option value="'+states[i]['name']+'">'+ states[i]['name'] +'</option>';
                    }
                servinglocation +=
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="city_'+ _ServingLocationCounter +'">City</label>'+
                '<select name="serving_city[]" id="city_'+ _ServingLocationCounter +'" class="form-control cities" multiple>'+
                    '<option value="">Select City</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="county_'+ _ServingLocationCounter +'">County</label>'+
                '<select class="form-control counties" name="serving_county[]" id="county_'+ _ServingLocationCounter +'" multiple>'+
                    '<option value="">Select County</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-3 mb-3 mt-3">'+
                '<label class="w-100" for="zipcode_'+ _ServingLocationCounter +'">Zip code</label>'+
                '<input type="text" name="serving_zipcode[]" id="zipcode_'+ _ServingLocationCounter +'" class="form-control" placeholder="Enter Your Zip Code"/>'+
            '</div>'+
            '<div class="col-md-12 mb-3 mt-3">'+
              '<span class="btn btn-outline-danger btn-sm float-right" id="remove_'+ _ServingLocationCounter +'" onclick="RemoveServingLocation(this.id);">'+
                '<span class="fa fa-trash"></span>&nbsp;Delete</span>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      $("#ServingLocationBlock").append(servinglocation);
      $("#_servingLocationCounter").val(_ServingLocationCounter);
      $(".states").select2();
      $(".cities").select2();
      $(".counties").select2();
      $(".propertyClassification").select2();
      $(".propertyType").select2();
      $(".multiFamilyType").select2();
      $(".constructionType").select2();
    }

    $("#addTitleCompanyForm").submit(function(e) {
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/title_company/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/title_company/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/title_company/store');
        }
      ?>
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let PropertyClassification = [];
      let PropertyType = [];
      let MultiFamily = [];
      let ConstructionType = [];
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (ServingLocationCounter + 1); i++) {
        let subCountyArray = [];
        let subCityArray = [];
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_County || _City) {
          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }
        }

        State.push(_State);
        County.push(subCountyArray);
        City.push(subCityArray);
        ZipCode.push(_ZipCode);
      }

      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });

    $("#editTitleCompanyForm").submit(function(e) {
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/title_company/update');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/title_company/update');
        }
        elseif (\Illuminate\Support\Facades\Session::get('user_role') == 6) {
            $Url = url('disposition_representative/title_company/update');
        }
      ?>
      let _ServingLocationCounter = $("#_servingLocationCounter").val();
      _ServingLocationCounter = parseInt(_ServingLocationCounter);
      let Id = $("#_id").val();
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let OldEmail = $("#old_email").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (_ServingLocationCounter + 1); i++) {
        let subCountyArray = [];
        let subCityArray = [];
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_State || _County || _City || _ZipCode) {
          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }

          State.push(_State);
          County.push(subCountyArray);
          City.push(subCityArray);
          ZipCode.push(_ZipCode);
        }
      }

      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + Id + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='old_email' value='" + OldEmail + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });
    /* Title Company Section - End */

    /* Realtor Section - Start */
    $("#addRealtorForm").submit(function(e){
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/realtor/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/realtor/store');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/realtor/store');
        }
      ?>
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let PropertyClassification = [];
      let PropertyType = [];
      let MultiFamily = [];
      let ConstructionType = [];
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (ServingLocationCounter + 1); i++) {
        let subPropertyClassificationArray = [];
        let subPropertyTypeArray = [];
        let subMultiFamilyArray = [];
        let subConstructionTypeArray = [];
        let subCountyArray = [];
        let subCityArray = [];
        let _PropertyClassification = $("#propertyClassification_" + i).val();
        let _PropertyType = $("#propertyType_" + i).val();
        let _MultiFamilyType = $("#multiFamilyType_" + i).val();
        let _ConstructionType = $("#constructionType_" + i).val();
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_PropertyClassification || _PropertyType || _MultiFamilyType || _ConstructionType || _State || _County || _City || _ZipCode) {

          if (_PropertyClassification) {
            for (let b = 0; b < _PropertyClassification.length; b++) {
              subPropertyClassificationArray.push(_PropertyClassification[b]);
            }
          }

          if (_PropertyType) {
            for (let b = 0; b < _PropertyType.length; b++) {
              subPropertyTypeArray.push(_PropertyType[b]);
            }
          }

          if (_MultiFamilyType) {
            for (let b = 0; b < _MultiFamilyType.length; b++) {
              subMultiFamilyArray.push(_MultiFamilyType[b]);
            }
          }

          if (_ConstructionType) {
            for (let b = 0; b < _ConstructionType.length; b++) {
              subConstructionTypeArray.push(_ConstructionType[b]);
            }
          }

          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }

          PropertyClassification.push(subPropertyClassificationArray);
          PropertyType.push(subPropertyTypeArray);
          MultiFamily.push(subMultiFamilyArray);
          ConstructionType.push(subConstructionTypeArray);
          State.push(_State);
          County.push(subCountyArray);
          City.push(subCityArray);
          ZipCode.push(_ZipCode);
        }
      }

      PropertyClassification = JSON.stringify(PropertyClassification);
      PropertyType = JSON.stringify(PropertyType);
      MultiFamily = JSON.stringify(MultiFamily);
      ConstructionType = JSON.stringify(ConstructionType);
      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='propertyClassification' value='" + PropertyClassification + "' type='hidden'>");
        $(form).append("<input name='propertyType' value='" + PropertyType + "' type='hidden'>");
        $(form).append("<input name='multiFamilyType' value='" + MultiFamily + "' type='hidden'>");
        $(form).append("<input name='constructionType' value='" + ConstructionType + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });

    $("#editRealtorForm").submit(function(e) {
      e.preventDefault();
      <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/realtor/update');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $Url = url('global_manager/realtor/update');
        }
        elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/realtor/update');
        }
      ?>
      let _ServingLocationCounter = $("#_servingLocationCounter").val();
      _ServingLocationCounter = parseInt(_ServingLocationCounter);
      let Id = $("#_id").val();
      let FirstName = $("#firstname").val();
      let MiddleName = $("#middlename").val();
      let LastName = $("#lastname").val();
      let BuisnessName = $("#buisness_name").val();
      let BuisnessAddress = $("#buisness_address").val();
      let Phone1 = $("#phone").val();
      let Phone2 = $("#phone2").val();
      let OldEmail = $("#old_email").val();
      let Email = $("#email").val();
      let SecondaryEmail = $("#secondary_email").val();
      let PropertyClassification = [];
      let PropertyType = [];
      let MultiFamily = [];
      let ConstructionType = [];
      let State = [];
      let County = [];
      let City = [];
      let ZipCode = [];

      for (let i = 1; i < (_ServingLocationCounter + 1); i++) {
        let subPropertyClassificationArray = [];
        let subPropertyTypeArray = [];
        let subMultiFamilyArray = [];
        let subConstructionTypeArray = [];
        let subCountyArray = [];
        let subCityArray = [];
        let _PropertyClassification = $("#propertyClassification_" + i).val();
        let _PropertyType = $("#propertyType_" + i).val();
        let _MultiFamilyType = $("#multiFamilyType_" + i).val();
        let _ConstructionType = $("#constructionType_" + i).val();
        let _State = $("#state_" + i).val();
        let _County = $("#county_" + i).val();
        let _City = $("#city_" + i).val();
        let _ZipCode = $("#zipcode_" + i).val();

        if (_PropertyClassification || _PropertyType || _MultiFamilyType || _ConstructionType || _State || _County || _City || _ZipCode) {
          if (_PropertyClassification) {
            for (let b = 0; b < _PropertyClassification.length; b++) {
              subPropertyClassificationArray.push(_PropertyClassification[b]);
            }
          }

          if (_PropertyType) {
            for (let b = 0; b < _PropertyType.length; b++) {
              subPropertyTypeArray.push(_PropertyType[b]);
            }
          }

          if (_MultiFamilyType) {
            for (let b = 0; b < _MultiFamilyType.length; b++) {
              subMultiFamilyArray.push(_MultiFamilyType[b]);
            }
          }

          if (_ConstructionType) {
            for (let b = 0; b < _ConstructionType.length; b++) {
              subConstructionTypeArray.push(_ConstructionType[b]);
            }
          }

          if (_County) {
            for (let b = 0; b < _County.length; b++) {
              subCountyArray.push(_County[b]);
            }
          }

          if (_City) {
            for (let b = 0; b < _City.length; b++) {
              subCityArray.push(_City[b]);
            }
          }

          PropertyClassification.push(subPropertyClassificationArray);
          PropertyType.push(subPropertyTypeArray);
          MultiFamily.push(subMultiFamilyArray);
          ConstructionType.push(subConstructionTypeArray);
          State.push(_State);
          County.push(subCountyArray);
          City.push(subCityArray);
          ZipCode.push(_ZipCode);
        }
      }

      PropertyClassification = JSON.stringify(PropertyClassification);
      PropertyType = JSON.stringify(PropertyType);
      MultiFamily = JSON.stringify(MultiFamily);
      ConstructionType = JSON.stringify(ConstructionType);
      State = JSON.stringify(State);
      County = JSON.stringify(County);
      City = JSON.stringify(City);
      ZipCode = JSON.stringify(ZipCode);

      // Check if lastname or companyname and email is filled
      if ((LastName !== '' || BuisnessName !== '') && (Email !== '')) {
        // Now submit form
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + Id + "' type='hidden'>");
        $(form).append("<input name='firstname' value='" + FirstName + "' type='hidden'>");
        $(form).append("<input name='middlename' value='" + MiddleName + "' type='hidden'>");
        $(form).append("<input name='lastname' value='" + LastName + "' type='hidden'>");
        $(form).append("<input name='buisness_name' value='" + BuisnessName + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='buisness_address' value='" + BuisnessAddress + "' type='hidden'>");
        $(form).append("<input name='phone' value='" + Phone1 + "' type='hidden'>");
        $(form).append("<input name='phone2' value='" + Phone2 + "' type='hidden'>");
        $(form).append("<input name='old_email' value='" + OldEmail + "' type='hidden'>");
        $(form).append("<input name='email' value='" + Email + "' type='hidden'>");
        $(form).append("<input name='secondary_email' value='" + SecondaryEmail + "' type='hidden'>");
        $(form).append("<input name='propertyClassification' value='" + PropertyClassification + "' type='hidden'>");
        $(form).append("<input name='propertyType' value='" + PropertyType + "' type='hidden'>");
        $(form).append("<input name='multiFamilyType' value='" + MultiFamily + "' type='hidden'>");
        $(form).append("<input name='constructionType' value='" + ConstructionType + "' type='hidden'>");
        $(form).append("<input name='serving_state' value='" + State + "' type='hidden'>");
        $(form).append("<input name='serving_county' value='" + County + "' type='hidden'>");
        $(form).append("<input name='serving_city' value='" + City + "' type='hidden'>");
        $(form).append("<input name='serving_zipcode' value='" + ZipCode + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
      } else {
        $("#_dangerAlert").show();
        return;
      }
    });
    /* Realtor Section - End */

    /* Notification Section - Start */
    function MarkAsRead(id){
      let values = id.split('_');
      $.ajax({
          type: "post",
          url: "{{url('/notification/markasread')}}",
          data: {NotificationId: values[1]}
      }).done(function (data) {

      });
    }

    function ClearAllNotification(){
      $.ajax({
          type: "post",
          url: "{{url('/notification/clear/all')}}",
          data: {}
      }).done(function (data) {

      });
    }
    /* Notification Section - Start */

    /* Announcement - Start */
    function MakeAnnouncementTable() {
        if ($("#admin_announcements_table").length) {
            $("#admin_announcements_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/announcements/all')}}",
                    "type": "POST",
                },
                'columns': [
                    {data: 'sr_no'},
                    // {data: 'announcement_type'},
                    {data: 'message'},
                    {data: 'expiration'},
                    {data: 'status'},
                    {data: 'action'},
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    function activeAnnouncement(id) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/announcement-active');
        }
        ?>
        let values = id.split('_');
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {AnnouncementId: values[1]}
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                $('#admin_announcements_table').DataTable().ajax.reload();
            } else {
                $('#admin_announcements_table').DataTable().ajax.reload();
            }
        });
    }

    function deactiveAnnouncement(id) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/announcement-deactive');
        }
        ?>
        let values = id.split('_');
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {AnnouncementId: values[1]}
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                $('#admin_announcements_table').DataTable().ajax.reload();
            } else {
                $('#admin_announcements_table').DataTable().ajax.reload();
            }
        });
    }

    function editAnnouncement(e) {
        <?php
        $Url = "";
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $Url = url('admin/edit/announcement');
        }
        ?>
        let id = e.split('_')[1];
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', "{{$Url}}");
        let csrfVar = $('meta[name="csrf-token"]').attr('content');
        $(form).append("<input name='_token' value='" + csrfVar + "' type='hidden'>");
        $(form).append("<input name='id' value='" + id + "' type='hidden'>");
        document.body.appendChild(form);
        form.submit();
    }

    function deleteAnnouncement(e) {
        let id = e.split('_')[1];
        $("#deleteAnnouncementId").val(id);
        $("#deleteAnnouncementModal").modal('toggle');
    }

    function ReadAnnouncement() {
      <?php
        $Url = url('announcement/read');
      ?>
      let AnnouncementId = $("#announcement_id").val();
      $.ajax({
          type: "post",
          url: "{{$Url}}",
          data: {AnnouncementId: AnnouncementId}
      }).done(function (data) {
          //
      });
    }

    function MakeAnnouncementDetailsTable() {
        if ($("#admin_announcements_details_table").length) {
            let AnnouncementId = $("#details_announcement_id").val();
            $("#admin_announcements_details_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{url('/admin/announcements/details/all')}}",
                    "type": "POST",
                    "data": {AnnouncementId: AnnouncementId}
                },
                'columns': [
                    {data: 'sr_no'},
                    {data: 'user'},
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }
    /* Announcement - End */

    /* Broadcast - Start */
    function MakeBroadcastsTable() {
        if ($("#admin_broadcasts_table").length) {
          <?php
          $Url = "";
          if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
              $Url = url('/admin/broadcasts/all');
          } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
              $Url = url('/global_manager/broadcasts/all');
          } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
              $Url = url('/acquisition_manager/broadcasts/all');
          } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
              $Url = url('/disposition_manager/broadcasts/all');
          }
          ?>
          $("#admin_broadcasts_table").DataTable({
              "processing": true,
              "serverSide": true,
              "paging": true,
              "bPaginate": true,
              "ordering": true,
              "pageLength": 50,
              "lengthMenu": [
                  [50, 100, 200, 400],
                  ['50', '100', '200', '400']
              ],
              "ajax": {
                  "url": "{{$Url}}",
                  "type": "POST",
              },
              'columns': [
                  {data: 'sr_no'},
                  {data: 'message'},
                  {data: 'action'},
              ],
              "drawCallback": function( settings ) {
                  $('[data-toggle="tooltip"]').tooltip();
              }
          });
        }
    }

    function openBroadcast(e) {
        let id = e.split('_')[1];
        $("#sendBroadcastUserId").val(id);
        $("#userBroadcastModal").modal('toggle');
    }

    function UpdateBroadcastReadStatus() {
        <?php
            $Url = url('broadcast/status/update');
        ?>
        let broadcast_id = $("#recieverBroadcastId").val();
        let read_broadcast_id = $("#recieverReadBroadcastId").val();
        let broadcast_reciever_id = $("#recieverBroadcastUserId").val();
        $.ajax({
            type: "post",
            url: "{{$Url}}",
            data: {BroadcastId: broadcast_id, ReadBroadcastId: read_broadcast_id, BroadcastRecieverId: broadcast_reciever_id}
        }).done(function (data) {
            if (jQuery.trim(data) === 'Success') {
                $("#recieverBroadcastId").val('');
                $("#recieverReadBroadcastId").val('');
                $("#recieverBroadcastUserId").val('');
                $("#recieverBroadcastModal").modal('toggle');
            } else {
                $("#recieverBroadcastId").val('');
                $("#recieverReadBroadcastId").val('');
                $("#recieverBroadcastUserId").val('');
                $("#recieverBroadcastModal").modal('toggle');
            }
        });
    }

    function openBroadcastToAll() {
        $("#userBroadcastToAllModal").modal('toggle');
    }

    function MakeBroadcastDetailsTable() {
        if ($("#admin_broadcasts_details_table").length) {
            <?php
            $Url = "";
            if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
                $Url = url('/admin/broadcast/details/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
                $Url = url('/global_manager/broadcast/details/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
                $Url = url('/acquisition_manager/broadcast/details/all');
            } elseif (\Illuminate\Support\Facades\Auth::user()->role_id == 4) {
                $Url = url('/disposition_manager/broadcast/details/all');
            }
            ?>
            let BroadcastId = $("#details_broadcast_id").val();
            $("#admin_broadcasts_details_table").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{$Url}}",
                    "type": "POST",
                    "data": {BroadcastId: BroadcastId}
                },
                'columns': [
                    {data: 'sr_no'},
                    {data: 'user'},
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }
    /* Broadcast - End */

    /* Constant Values - Start */
    function EditConstantValues() {
        $("#confirmEditMagicNumbersModal").modal('toggle');
    }

    function ConfirmEditMagicNumbers() {
        $("#ARV_SALES_CLOSING_COST_CONSTANT").prop("disabled", false);
        $("#WHOLESALES_CLOSING_COST_CONSTANT").prop("disabled", false);
        $("#INVESTOR_PROFIT_CONSTANT").prop("disabled", false);
        $("#OFFER_LOWER_RANGE_CONSTANT").prop("disabled", false);
        $("#OFFER_HIGHER_RANGE_CONSTANT").prop("disabled", false);
        $("#EditMagicNumberBtn").hide();
        $("#SaveChangesBtn").show();
        $("#confirmEditMagicNumbersModal").modal('toggle');
    }
    /* Constant Values - End */

    /* Emoji Picker */
    const emojiPicker = new FgEmojiPicker({
        trigger: ['button[name="emoji_picker"]'],
        removeOnSelection: true,
        closeButton: true,
        position: ['top', 'right'],
        preFetch: true,
        insertInto: document.querySelector('input[name="message"]'),
        emit(obj, triggerElement) {
            // display details of the object
        }
    });
    /* Emoji Picker */

    /* Internal Messaging System - Start */
    function HandleFormSubmit(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    }

    function VerifyMessage(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        } else {
            let message = $("#chat_message").val();
            newTemp = message.replace(/"/g, '\'');
            $("#chat_message").val(newTemp);
        }
    }

    function VerifyTextField(event, id) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        } else {
            let message = $("#" + id).val();
            newTemp = message.replace(/"/g, '\'');
            $("#" + id).val(newTemp);
        }
    }

    function SendMessageWhenEnter(event) {
        if (event.keyCode == 13) {
            SendMessage();
        }
    }

    function scrollToBottom(id) {
        $("#" + id).removeClass('ps--active-y');
        $("#" + id).removeClass('ps--scrolling-y');
        const messages = document.getElementById(id);
        messages.scrollTop = messages.scrollHeight;
    }

    function ShowChatBlocks() {
        $("#StartChatAlert").addClass('d-none');
        $("#ChatListUserDetails").removeClass('d-none');
        $("#UserPhoneNumber").removeClass('d-none');
        $("#chatuser_messages").removeClass('d-none');
        $("#ChatForm").removeClass('d-none');
        $("#ChatFormEmoji").removeClass('d-none');
        $("#ChatFormField").removeClass('d-none');
        $("#ChatFormSendButton").removeClass('d-none');
    }

    function OpenUserChat(id, type) {
        ScrollCount = 1;
        if (type === 'Chat' || type === 'Contact') {
            ShowChatBlocks();
            let values = id.split("_");
            let profile_picture = '';
            let total_chat_users = '';
            let total_contact_users = '';
            let nameArray = values[2].split(' ');
            profile_picture = '<span class="img-xs rounded-circle" style="background: #15D16C; padding: 8px; color: #fff;">' + nameArray[0].charAt(0) + nameArray[nameArray.length - 1].charAt(0) + '</span>';
            let UserDetails = '';
            UserDetails += '' +
                '<i data-feather="corner-up-left" id="backToChatList" class="icon-lg mr-2 ml-n2 text-muted d-lg-none"></i>' +
                '<figure class="mb-0 mr-2">' + profile_picture;
            if (values[5] === '1') {
                UserDetails += '<div class="status online"></div>';
            } else {
                UserDetails += '<div class="status"></div>';
            }
            UserDetails +=
                '</figure>' +
                '<div>' +
                ' <p>' + values[2] + '</p>' +
                '  <p class="text-muted tx-13">' + values[3] + '</p>' +
                '</div>';
            $("a.ChatUserPhoneNumber").show();
            $("a.ChatUserPhoneNumber").attr("href", "tel:" + values[6]);
            $("a.EditGroup").hide();
            $("#receiver_id").val(values[1]);
            $("#ChatListUserDetails").html('').html(UserDetails);
            $("#group_id").val('');
            LoadMessages();
            ReadAll();
        } else if (type === 'Group') {
            ShowChatBlocks();
            let values = id.split("_");
            let profile_picture = '';
            let total_groups = '';
            let nameArray = values[2].split(' ');
            profile_picture = '<span class="img-xs rounded-circle" style="background: #15D16C; padding: 8px; color: #fff;">' + nameArray[0].charAt(0) + nameArray[nameArray.length - 1].charAt(0) + '</span>';
            let UserDetails = '';
            UserDetails += '' +
                '<i data-feather="corner-up-left" id="backToChatList" class="icon-lg mr-2 ml-n2 text-muted d-lg-none"></i>' +
                '<figure class="mb-0 mr-2">' + profile_picture;
            UserDetails +=
                '</figure>' +
                '<div>' +
                ' <p>' + values[2] + '</p>' +
                '</div>';
            $("a.ChatUserPhoneNumber").hide();
            let logged_user_id = $("#logged_user_id").val();
            let group_admins = values[4];
            group_admins = group_admins.split(",");
            let check_permission = 0;
            for (let i = 0; i < group_admins.length; i++) {
                if (logged_user_id == group_admins[i]) {
                    check_permission = 1;
                }
            }
            if (check_permission) {
                $("a.EditGroup").show();
            } else {
                $("a.EditGroup").hide();
            }
            $("#receiver_id").val('');
            $("#group_id").val(values[1]);
            $("#receiver_id").val('');
            $("#ChatListUserDetails").html('').html(UserDetails);
            LoadMessages();
            ReadAll();
        }
    }

    function SendMessage() {
        ScrollCount = 1;
        let receiver_id = $("#receiver_id").val();
        let group_id = $("#group_id").val();
        let message = $("#chat_message").val();
        if (message !== '') {
            $.ajax({
                type: "post",
                url: "{{url('message/send')}}",
                data: {ReceiverId: receiver_id, GroupId: group_id, Message: message}
            }).done(function (data) {
                if (data === "Success") {
                    // refresh user chat
                    $("#chat_message").val('');
                    LoadMessages();
                } else {
                    // no need to refresh chat
                }
            });
        }
    }

    function LoadMessages() {
            <?php
            $SenderId = \Illuminate\Support\Facades\Auth::user()->id;
            ?>
        let sender_id = '<?= $SenderId; ?>';
        let receiver_id = $("#receiver_id").val();
        let group_id = $("#group_id").val();
        let profile_picture = '';
        $.ajax({
            type: "post",
            url: "{{url('messages/load')}}",
            data: {ReceiverId: receiver_id, GroupId: group_id}
        }).done(function (data) {
            let s = data;
            // s = s.replace(/\\n/g, "\\n")
            //     .replace(/\\'/g, "\\'")
            //     .replace(/\\"/g, '\\"')
            //     .replace(/\\&/g, "\\&")
            //     .replace(/\\r/g, "\\r")
            //     .replace(/\\t/g, "\\t")
            //     .replace(/\\b/g, "\\b")
            //     .replace(/\\f/g, "\\f");
            // // remove non-printable and other non-valid JSON chars
            // s = s.replace(/[\u0000-\u0019]+/g, "");
            // s = JSON.parse(s);
            let Chat = JSON.parse(s.chat);
            let TotalUnreadMessage = s.total_unread_message;
            let messages = '';
            // Configure chat html
            for (let i = 0; i < Chat.length; i++) {
                if (sender_id == Chat[i]['sender_id']) {
                    profile_picture = '<span class="img-xs rounded-circle tooltip1" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="' + Chat[i]['firstname'] + " " + Chat[i]['lastname'] + '">' + Chat[i]['firstname'].charAt(0) + Chat[i]['lastname'].charAt(0) + '</span>';
                    messages += '' +
                        '<li class="message-item me">' +
                        profile_picture +
                        '  <div class="content">' +
                        '    <div class="message">' +
                        '      <div class="bubble">' +
                        '        <p>' + Chat[i]['message'] + '</p>' +
                        '      </div>' +
                        '      <span>' + Chat[i]['time'] + '</span>' +
                        '    </div>' +
                        '  </div>' +
                        '</li>';
                } else if (receiver_id == Chat[i]['sender_id']) {
                    profile_picture = '<span class="img-xs rounded-circle tooltip1" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="' + Chat[i]['firstname'] + " " + Chat[i]['lastname'] + '">' + Chat[i]['firstname'].charAt(0) + Chat[i]['lastname'].charAt(0) + '</span>';
                    messages += '' +
                        '<li class="message-item friend">' +
                        profile_picture +
                        '  <div class="content">' +
                        '    <div class="message">' +
                        '      <div class="bubble">' +
                        '        <p>' + Chat[i]['message'] + '</p>' +
                        '      </div>' +
                        '      <span>' + Chat[i]['time'] + '</span>' +
                        '    </div>' +
                        '  </div>' +
                        '</li>';
                } else {
                    profile_picture = '<span class="img-xs rounded-circle tooltip1" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="' + Chat[i]['firstname'] + " " + Chat[i]['lastname'] + '">' + Chat[i]['firstname'].charAt(0) + Chat[i]['lastname'].charAt(0) + '</span>';
                    messages += '' +
                        '<li class="message-item friend">' +
                        profile_picture +
                        '  <div class="content">' +
                        '    <div class="message">' +
                        '      <div class="bubble">' +
                        '        <p>' + Chat[i]['message'] + '</p>' +
                        '      </div>' +
                        '      <span>' + Chat[i]['time'] + '</span>' +
                        '    </div>' +
                        '  </div>' +
                        '</li>';
                }
            }
            $(".tooltip1").tooltip('hide');
            $("#chatuser_messages").html('').html(messages);
            $(".tooltip1").tooltip();
            if (parseInt(TotalUnreadMessage) > 0) {
                ScrollCount = 1;
            }
            if(ScrollCount >= 1 && ScrollCount <= 2){
                scrollToBottom('chat-body');
            }
            ScrollCount++;
        });
    }

    function ReadAll() {
        let receiver_id = $("#receiver_id").val();
        let group_id = $("#group_id").val();
        // if (receiver_id !== '') {
        $.ajax({
            type: "post",
            url: "{{url('messages/read')}}",
            data: {ReceiverId: receiver_id, GroupId: group_id}
        }).done(function (data) {
            if (data === "Success") {
                // update chat users sidebar
            } else {
                // no need to refresh chat
            }
        });
        // }
    }

    function RefreshChatlist() {
        if ($("#InternalMessagingPage").length) {
            let refreshChatListTimer = setInterval(function () {
                if ($("#InternalMessagingPage").length) {
                    $.ajax({
                        type: "post",
                        url: "{{url('load/chat/list')}}",
                        data: {}
                    }).done(function (data) {
                        // update chat list in sidebar
                        let s = data;
                        s = s.replace(/\\n/g, "\\n")
                            .replace(/\\'/g, "\\'")
                            .replace(/\\"/g, '\\"')
                            .replace(/\\&/g, "\\&")
                            .replace(/\\r/g, "\\r")
                            .replace(/\\t/g, "\\t")
                            .replace(/\\b/g, "\\b")
                            .replace(/\\f/g, "\\f");
                        // remove non-printable and other non-valid JSON chars
                        s = s.replace(/[\u0000-\u0019]+/g, "");
                        let Chat = JSON.parse(s);
                        $(".tooltip1").tooltip('hide');
                        $("#ChatList").html('').html(Chat);
                        $(".tooltip1").tooltip();
                        ReadAll();
                    });
                } else {
                    clearInterval(refreshChatListTimer);
                }
            }, 3500);
        }
    }

    function RefreshContactlist() {
        if ($("#InternalMessagingPage").length) {
            let refreshContactListTimer = setInterval(function () {
                if ($("#InternalMessagingPage").length) {
                    $.ajax({
                        type: "post",
                        url: "{{url('load/contact/list')}}",
                        data: {}
                    }).done(function (data) {
                        // update chat list in sidebar
                        let s = data;
                        s = s.replace(/\\n/g, "\\n")
                            .replace(/\\'/g, "\\'")
                            .replace(/\\"/g, '\\"')
                            .replace(/\\&/g, "\\&")
                            .replace(/\\r/g, "\\r")
                            .replace(/\\t/g, "\\t")
                            .replace(/\\b/g, "\\b")
                            .replace(/\\f/g, "\\f");
                        // remove non-printable and other non-valid JSON chars
                        s = s.replace(/[\u0000-\u0019]+/g, "");
                        let Contacts = JSON.parse(s);
                        $(".tooltip1").tooltip('hide');
                        $("#ContactList").html('').html(Contacts);
                        $(".tooltip1").tooltip();
                    });
                } else {
                    clearInterval(refreshContactListTimer);
                }
            }, 5000);
        }
    }

    function RefreshUserChat() {
        if ($("#InternalMessagingPage").length) {
            let refreshUserChatTimer = setInterval(function () {
                if ($("#InternalMessagingPage").length) {
                    LoadMessages();
                } else {
                    clearInterval(refreshUserChatTimer);
                }
            }, 1500);
        }
    }

    function SearchUserForChat(search) {
        if ($("#searchForm").val() !== '') {
            $("#ChatList").hide();
            $("#SearchUsersList").show();
            $.ajax({
                type: "post",
                url: "{{url('load/search/users')}}",
                data: {search: $("#searchForm").val()}
            }).done(function (data) {
                // update chat list in sidebar
                let s = data;
                s = s.replace(/\\n/g, "\\n")
                    .replace(/\\'/g, "\\'")
                    .replace(/\\"/g, '\\"')
                    .replace(/\\&/g, "\\&")
                    .replace(/\\r/g, "\\r")
                    .replace(/\\t/g, "\\t")
                    .replace(/\\b/g, "\\b")
                    .replace(/\\f/g, "\\f");
                // remove non-printable and other non-valid JSON chars
                s = s.replace(/[\u0000-\u0019]+/g, "");
                let Users = JSON.parse(s);
                $(".tooltip1").tooltip('hide');
                $("#SearchUsersList").html('').html(Users);
                $(".tooltip1").tooltip();
            });
        } else {
            $("#SearchUsersList").hide();
            $("#ChatList").show();
        }
    }

    function OpenSearchUserChat(id) {
        ShowChatBlocks();
        let values = id.split("_");
        let profile_picture = '';
        let total_search_users = $("#totalsearchusers").val();

        for (let i = 0; i < total_search_users; i++) {
            $("#SearchUserId" + i).removeClass("activeUserChat");
        }
        let nameArray = values[2].split(' ');
        profile_picture = '<span class="img-xs rounded-circle tooltip1" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="' + values[2] + '">' + nameArray[0].charAt(0) + nameArray[nameArray.length - 1].charAt(0) + '</span>';
        let UserDetails = '';
        UserDetails += '' +
            '<i data-feather="corner-up-left" id="backToChatList" class="icon-lg mr-2 ml-n2 text-muted d-lg-none"></i>' +
            '<figure class="mb-0 mr-2">' + profile_picture;
        if (values[5] === '1') {
            UserDetails += '<div class="status online"></div>';
        } else {
            UserDetails += '<div class="status"></div>';
        }
        UserDetails +=
            '</figure>' +
            '<div>' +
            ' <p>' + values[2] + '</p>' +
            '  <p class="text-muted tx-13">' + values[3] + '</p>' +
            '</div>';
        $("#" + values[7]).addClass("activeUserChat");
        $("a.ChatUserPhoneNumber").attr("href", "tel:" + values[6]);
        $("#receiver_id").val(values[1]);
        $("#ChatListUserDetails").html('').html(UserDetails);
        LoadMessages();
        ReadAll();
        $("#SearchUsersList").hide();
        $("#ChatList").show();
    }
    /* Internal Messaging System - End */

    /* Referral Link - Start */
    function CopyReferralLink(Link) {
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val(Link).select();
        document.execCommand("copy");
        $temp.remove();
    }
    /* Referral Link - End */

    /* Group Chat - Start */
    function OpenAddNewGroupModal()
    {
      $("#addNewGroupModal").modal('toggle');
    }

    function CreateGroup()
    {
      let groupName = $("#add_group_name").val();
      let groupMembers = $("#add_group_members").val();
      if (groupName !== '' && groupMembers.length > 0) {
        $("#errorGroupName").hide();
        $("#errorGroupMember").hide();
        $.ajax({
            type: "post",
            url: "{{url('/add/new/group')}}",
            data: {GroupName: groupName, GroupMembers: JSON.stringify(groupMembers)}
        }).done(function (data) {
            $("#addNewGroupModal").modal('toggle');
            $("#add_group_name").val('');
            $("#add_group_members").empty('').val('');
        });
      }
      else {
        if (groupName === '') {
          $("#errorGroupName").show();
          $("#errorGroupName").html('').html('Group name is missing');
        }
        if (groupMembers.length === 0) {
          $("#errorGroupMember").show();
          $("#errorGroupMember").html('').html('Please select atleast one member');
        }
      }
    }

    function HandleChange(id) {
      $("#"+id).hide();
    }

    function RefreshGrouplist()
    {
      if ($("#InternalMessagingPage").length) {
        let refreshGroupListTimer = setInterval(function(){
          if ($("#InternalMessagingPage").length) {
            $.ajax({
                  type: "post",
                  url: "{{url('load/group/list')}}",
                  data: {}
              }).done(function (data) {
                    let s = data;
                    s = s.replace(/\\n/g, "\\n")
                        .replace(/\\'/g, "\\'")
                        .replace(/\\"/g, '\\"')
                        .replace(/\\&/g, "\\&")
                        .replace(/\\r/g, "\\r")
                        .replace(/\\t/g, "\\t")
                        .replace(/\\b/g, "\\b")
                        .replace(/\\f/g, "\\f");
                    // remove non-printable and other non-valid JSON chars
                    s = s.replace(/[\u0000-\u0019]+/g,"");
                    let Groups = JSON.parse(s);
                    $("#GroupsList").html('').html(Groups);
              });
          }
          else {
            clearInterval(refreshGroupListTimer);
          }
        }, 5000);
      }
    }

    function OpenEditGroupModal() {
      let groupId = $("#group_id").val();
      $.ajax({
          type: "post",
          url: "{{url('load/group/details')}}",
          data: {GroupId: groupId}
      }).done(function (data) {
          let s = data;
          s = s.replace(/\\n/g, "\\n")
              .replace(/\\'/g, "\\'")
              .replace(/\\"/g, '\\"')
              .replace(/\\&/g, "\\&")
              .replace(/\\r/g, "\\r")
              .replace(/\\t/g, "\\t")
              .replace(/\\b/g, "\\b")
              .replace(/\\f/g, "\\f");
          // remove non-printable and other non-valid JSON chars
          s = s.replace(/[\u0000-\u0019]+/g,"");
          let GroupDetails = JSON.parse(s);
          $("#edit_group_name").val(GroupDetails.name);
          $("#edit_group_members").html('').html(GroupDetails.members);
          $("#editGroupModal").modal('toggle');
      });
    }

    function UpdateGroup()
    {
      let groupId = $("#group_id").val();
      let groupName = $("#edit_group_name").val();
      let groupMembers = $("#edit_group_members").val();
      if (groupName !== '' && groupMembers.length > 0) {
        $("#errorEditGroupName").hide();
        $("#errorEditGroupMember").hide();
        $.ajax({
            type: "post",
            url: "{{url('/update/group')}}",
            data: {GroupId: groupId, GroupName: groupName, GroupMembers: JSON.stringify(groupMembers)}
        }).done(function (data) {
            $("#editGroupModal").modal('toggle');
            $("#edit_group_name").val('');
            $("#edit_group_members").empty('').val('');
        });
      }
      else {
        if (groupName === '') {
          $("#errorEditGroupName").show();
          $("#errorEditGroupName").html('').html('Group name is missing');
        }
        if (groupMembers.length === 0) {
          $("#errorEditGroupMember").show();
          $("#errorEditGroupMember").html('').html('Please select atleast one member');
        }
      }
    }
    /* Group Chat - End */

    /*Coverage Area*/
    /* Dashboard Google Maps - Start */
    function MakeCoverageAreaMap() {
        if($("#coverageAreaPage").length === 0){
            return;
        }
        let Coordinates = [];
        let SubCoordinates = [];
        @if(isset($LeadLocations))
            @foreach($LeadLocations as $location)
                SubCoordinates = {
                    'lat' : '{{$location->lat}}',
                    'long' : '{{$location->long}}',
                    'address' : '{{$location->formatted_address}}'
                };
                Coordinates.push(SubCoordinates);
            @endforeach
        @endif
        let Latitudes = 0;
        let Longitudes = 0;
        for (let k = 0; k < Coordinates.length; k++) {
            Latitudes += parseFloat(Coordinates[k]['lat']);
            Longitudes += parseFloat(Coordinates[k]['long']);
        }
        Latitudes = Latitudes / Coordinates.length;
        Longitudes = Longitudes / Coordinates.length;

        map = new google.maps.Map(document.getElementById('coverageAreaMap'), {
            zoom: 10,
            center: new google.maps.LatLng(Latitudes, Longitudes),
            mapTypeId: google.maps.MapTypeId.HYBRID
        });

        let marker;
        let infowindow = new google.maps.InfoWindow();

        for (let i = 0; i < Coordinates.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(Coordinates[i]['lat'], Coordinates[i]['long']),
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(Coordinates[i]['address']);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }

        /*Left Side Maps*/
        /*for (let j = 0; j < Coordinates.length; j++){
            let lat = parseFloat(Coordinates[j]['lat']);
            let lng = parseFloat(Coordinates[j]['long']);
            let panorama = new google.maps.StreetViewPanorama(
                document.getElementById('smallMap_' + j), {
                    position: {lat: lat, lng: lng},
                    pov: {
                        heading: 24,
                        pitch: 10,
                    }
                });
            /!*Display only 10 leads coverage area in left side*!/
            if(j === 10){
                break;
            }
        }*/
    }

    function SetMapCenter(Lat, Long) {
        let Center = new google.maps.LatLng(parseFloat(Lat), parseFloat(Long));
        map.setCenter(Center);
        map.setZoom(20);
    }

    function SearchLeadInZoning(value) {
        if (value !== '') {
            $("#coverageAreaMap").attr('style', 'height: 600px; position: relative; overflow: hidden;');
            $("#results").hide();
            $("#searchResults").show();
            SearchLeadInZoning1(value);
        } else {
            $("#coverageAreaMap").attr('style', 'height: 100%; position: relative; overflow: hidden; display: block;');
            $("#results").show();
            $("#searchResults").hide();
        }
    }

    function SearchLeadInZoning1(value) {
        $.ajax({
            type: "post",
            url: "{{ route('zoningSearch') }}",
            data: { search : value }
        }).done(function (data) {
            data = atob(data);
            $("#searchResults").html(data);
        });
    }
    /*Coverage Area*/
</script>
