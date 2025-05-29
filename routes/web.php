<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

Route::get('clear-cache', function () {
    Artisan::call('storage:link');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    //Create storage link on hosting
    $exitCode = Artisan::call('storage:link', []);
    echo $exitCode; // 0 exit code for no errors.
});

Route::get('/', function () {
    return redirect(url('/login'));
});

Auth::routes();
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::group(['middleware' => ['admin_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        /* Admin Routes */
        Route::get('admin/dashboard', 'DashboardController@LoadDashboard')->name('adminDashboard');
        // Buisness Account
        Route::get('admin/buissness_accounts', 'BuisnessAccountController@AdminAllBuisnessAccounts');
        Route::post('admin/buissness_accounts/all', 'BuisnessAccountController@LoadAdminAllBuisnessAccounts');
        // Investor Account
        Route::get('admin/investors', 'InvestorController@AdminAllInvestors');
        Route::get('admin/investor/add', 'InvestorController@AdminAddNewInvestor');
        Route::post('admin/investor/store', 'InvestorController@AdminInvestorStore');
        Route::post('admin/investor/all', 'InvestorController@LoadAdminAllInvestors');
        Route::post('admin/delete/investor', 'InvestorController@AdminDeleteInvestor');
        Route::post('admin/investor/edit', 'InvestorController@AdminEditInvestor');
        Route::post('admin/investor/update', 'InvestorController@AdminUpdateInvestor');
        Route::get('admin/investor/leads/{CompanyId}', 'InvestorController@ShowCompanyLeads');
        Route::post('admin/investor/companyLeads', 'InvestorController@CompanyLeads');
        // Title Company Account
        Route::get('admin/title_companies', 'TitleCompanyController@AdminAllTitleCompanies');
        Route::get('admin/title_company/add', 'TitleCompanyController@AdminAddNewTitleCompany');
        Route::post('admin/title_company/store', 'TitleCompanyController@AdminTitleCompanyStore');
        Route::post('admin/title_company/all', 'TitleCompanyController@LoadAdminAllTitleCompanies');
        Route::post('admin/delete/title_company', 'TitleCompanyController@AdminDeleteTitleCompany');
        Route::post('admin/title_company/edit', 'TitleCompanyController@AdminEditTitleCompany');
        Route::post('admin/title_company/update', 'TitleCompanyController@AdminUpdateTitleCompany');
        // Realtor Account
        Route::get('admin/realtor', 'RealtorController@AdminAllRealtors');
        Route::get('admin/realtor/add', 'RealtorController@AdminAddNewRealtor');
        Route::post('admin/realtor/store', 'RealtorController@AdminRealtorStore');
        Route::post('admin/realtor/all', 'RealtorController@LoadAdminAllRealtors');
        Route::post('admin/delete/realtor', 'RealtorController@AdminDeleteRealtor');
        Route::post('admin/realtor/edit', 'RealtorController@AdminEditRealtor');
        Route::post('admin/realtor/update', 'RealtorController@AdminUpdateRealtor');
        // Users Routes
        Route::get('admin/users', 'UserController@AdminAllUsers');
        Route::get('admin/add/user', 'UserController@AdminAddNewUsers');
        Route::post('admin/user/store', 'UserController@AdminUserStore');
        Route::post('admin/delete/user', 'UserController@AdminDeleteUser');
        Route::post('admin/edit/user', 'UserController@AdminEditUser');
        Route::post('admin/user/update', 'UserController@AdminUpdateUser');
        Route::post('admin/user/changePassword', 'UserController@ChangePassword');
        Route::post('admin/user-active', 'UserController@active');
        Route::post('admin/user-ban', 'UserController@ban');
        Route::get('admin/users/progress', 'UserController@UsersProgress');
        Route::post('admin/users/progress/all', 'UserController@UsersProgressAll');
        Route::post('admin/user/activity/all', 'UserController@UserActivitiesAll');
        Route::post('admin/user/upgrade/account', 'UserController@UserUpgradeAccount');
        // Teams Routes
        Route::get('admin/teams', 'TeamsController@AdminTeam')->name('admin-teams');
        Route::get('admin/teams/add', 'TeamsController@AdminTeamAdd');
        Route::post('admin/teams/store', 'TeamsController@AdminTeamStore');
        Route::post('admin/teams/delete', 'TeamsController@AdminTeamDelete');
        Route::get('admin/teams/edit/{id}', 'TeamsController@AdminTeamEdit');
        Route::post('admin/teams/update', 'TeamsController@AdminTeamUpdate');
        // Leads Routes
        Route::get('admin/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('admin/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('admin/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('admin/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('admin/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('admin/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('admin/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('admin/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('admin/lead/import', 'LeadController@ImportLeads');
        Route::post('admin/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('admin/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('admin/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('admin/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('admin/leads/assign', 'LeadController@AssignLeads');
        Route::post('admin/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('admin/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('admin/leads/details', 'LeadController@LeadDetails');
        Route::get('admin/leads/funnel', 'LeadController@leadFunnel');
        Route::get('admin/leads/funnel/{type}', 'LeadController@leadFunnel');
        Route::get('admin/leads/funnel/{type}/{startdate}/{enddate}', 'LeadController@leadFunnel');
        // Call Request Routes
        Route::get('admin/call-requests', 'CallRequestController@RepresentativeAllCallRequests')->name('admin-callrequests');
        Route::get('admin/call-request/add', 'CallRequestController@RepresentativeAddNewCallRequest');
        Route::post('admin/call-request/store', 'CallRequestController@RepresentativeCallRequestStore');
        Route::get('admin/call-request/edit/{Id}', 'CallRequestController@RepresentativeEditLead');
        Route::post('admin/call-request/update', 'CallRequestController@RepresentativeUpdateLead');
        Route::post('admin/call-request/convert', 'CallRequestController@AdminConvertCallRequest');
        // Dispo Lead Routes
        Route::get('admin/dispo-leads', 'DispoLeadController@RepresentativeAllDispoLeads')->name('admin-dispoleads');
        Route::get('admin/dispo-lead/edit/{Id}', 'DispoLeadController@RepresentativeEditDispoLead');
        Route::post('admin/dispo-lead/update', 'DispoLeadController@RepresentativeUpdateLead');
        Route::post('admin/dispo-lead/convert', 'DispoLeadController@AdminConvertDispoLead');
        // Training Link Routes
        Route::get('admin/training-link', 'TrainingCoverageLinksController@index')->name('admin-training-link');
        Route::get('admin/training-link/edit/{Id}', 'TrainingCoverageLinksController@edit');
        Route::post('admin/training-link/update', 'TrainingCoverageLinksController@update');
        // Coverage File Routes
        Route::get('admin/coverage-file', 'TrainingCoverageLinksController@index_covergae')->name('admin-coverage-file');
        Route::get('admin/coverage-file/edit/{Id}', 'TrainingCoverageLinksController@edit_covergae');
        Route::post('admin/coverage-file/update', 'TrainingCoverageLinksController@update_covergae');
        // Edit Profile Routes
        Route::get('admin/edit-profile', 'UserController@EditProfile');
        Route::post('admin/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('admin/update-account-security', 'UserController@UpdateAccountSecurity');
        // Sale
        Route::get('admin/sale/add', 'SaleController@add')->name('admin-add-sale');
        Route::post('admin/sale/store', 'SaleController@LeadSaleStore');
        Route::get('admin/sales', 'SaleController@AllSales')->name('AllSales');
        Route::post('admin/sales/view', 'SaleController@ViewSaleDetails');
        // Payroll
        Route::get('admin/payroll/approve', 'PayrollController@index')->name('admin-payroll-approve');
        Route::post('admin/payroll-all', 'PayrollController@loadPayroll');
        Route::post('admin/payroll/submit', 'PayrollController@Submit');
        Route::post('admin/payroll/reject', 'PayrollController@rejectPayroll');
        Route::post('admin/payroll/bonus', 'PayrollController@addBonusPayroll');
        Route::post('admin/payroll/approved', 'PayrollController@approvedPayroll');
        Route::post('admin/payroll/edit/earning', 'PayrollController@editEarningPayroll');
        Route::post('admin/rejected/payroll-all', 'PayrollController@loadRejectedPayroll');
        Route::post('admin/payroll/breakdowns', 'PayrollController@loadPayrollBreakdowns');
        Route::post('admin/payroll/income-details', 'PayPeriodsController@ViewIncomeDetails');
        Route::post('admin/payroll/income-details/store-update', 'PayPeriodsController@StoreUpdateIncomeDetails');
        // Payroll Submitted
        Route::get('admin/payroll/submitted', 'PayPeriodsController@SubmittedPayroll')->name('admin-payroll-submitted');
        Route::post('admin/payroll/submitted/all', 'PayPeriodsController@loadSubmittedPayroll');
        Route::post('admin/payroll/submitted/breakdowns', 'PayPeriodsController@loadSubmittedPayrollBreakdowns');
        Route::post('admin/payroll/submitted/edit-pay-period', 'PayPeriodsController@EditPayPeriodEarning');
        Route::post('admin/payroll/submitted/update-pay-period', 'PayPeriodsController@UpdatePayPeriodEarning');
        Route::post('admin/payroll/submitted/approve', 'PayPeriodsController@GeneratePayroll');
        Route::post('admin/payroll/submitted/rollback', 'PayPeriodsController@Rollback');
        // Earning Routes
        Route::get('admin/earning', 'EarningController@AdminEarningRecord');
        // Settings
        Route::get('admin/settings', 'SettingsController@index')->name('admin-settings');
        Route::post('admin/settings/hoursPrice', 'SettingsController@UpdateHoursPrice');
        //Produts Route
        Route::get('admin/products', 'ProductController@AdminAllProducts');
        Route::get('admin/add/product', 'ProductController@AdminAddNewProduct');
        Route::post('products/all', 'ProductController@LoadAdminAllProducts');
        Route::post('admin/product/store', 'ProductController@AdminProductStore');
        Route::post('admin/edit/product', 'ProductController@AdminEditProduct');
        Route::post('admin/product/update', 'ProductController@AdminUpdateProduct');
        //Expense Route
        Route::get('admin/expenses', 'ExpenseController@AdminAllExpense');
        Route::get('admin/add/expenses', 'ExpenseController@AdminAddNewExpense');
        Route::post('expenses/all', 'ExpenseController@LoadAdminAllExpense');
        Route::post('admin/delete/expenses', 'ExpenseController@AdminDeleteExpense');
        Route::post('admin/expenses/store', 'ExpenseController@AdminExpenseStore');
        Route::post('admin/edit/expenses', 'ExpenseController@AdminEditExpense');
        Route::post('admin/expenses/update', 'ExpenseController@AdminUpdateExpense');
        // Payout Routes
        Route::get('admin/payout', 'PayOutController@AdminAllPayOut');
        Route::post('payout/all', 'PayOutController@LoadAdminAllPayout');
        Route::post('admin/edit/payout', 'PayOutController@AdminEditPayout');
        Route::post('admin/payout/update', 'PayOutController@AdminUpdatePayout');
        // Marketing Report
        Route::get('admin/marketing-report', 'MarketingReportController@index');
        // Training Room
        Route::get('admin/training-room', 'TrainingRoomController@index');
        Route::get('admin/training-room/folders/{id}', 'TrainingRoomController@OpenTrainingRoomFolders');
        Route::post('admin/training-room/folders/all', 'TrainingRoomController@LoadAllTrainingRoomFolders');
        Route::get('admin/training-room/folder/add/{id}', 'TrainingRoomController@AddTrainingRoomFolder');
        Route::post('admin/training-room/folder/store', 'TrainingRoomController@StoreTrainingRoomFolder');
        Route::get('admin/training-room/folder/edit/{folderId}/{RoleId}', 'TrainingRoomController@EditTrainingRoomFolder');
        Route::post('admin/training-room/folder/update', 'TrainingRoomController@UpdateTrainingRoomFolder');
        Route::post('admin/training-room/folder/delete', 'TrainingRoomController@DeleteTrainingRoomFolder');
        Route::get('admin/training-room/folder/order/up/{Id}/{Role}', 'TrainingRoomController@TrainingRoomFolderOrderUp');
        Route::get('admin/training-room/folder/order/down/{Id}/{Role}', 'TrainingRoomController@TrainingRoomFolderOrderDown');
        Route::post('admin/training-room/folder/copy', 'TrainingRoomController@CopyTrainingRoomFolder');
        Route::post('admin/training-room/folders/get', 'TrainingRoomController@GetTrainingRoomFolders');
        Route::get('admin/training-room/folder/details/{id}/{RoleId}', 'TrainingRoomController@OpenTrainingRoomDetails');
        // Training Room
        Route::post('admin/training-room/all', 'TrainingRoomController@LoadAllTrainingRoom');
        Route::post('admin/training-room/delete', 'TrainingRoomController@TrainingRoomDelete');
        Route::post('admin/copy/training-room', 'TrainingRoomController@TrainingRoomCopy');
        // Videos
        Route::get('admin/training-room/video/add/{FolderId}/{RoleId}', 'TrainingRoomController@AddTrainingRoomVideo');
        Route::post('admin/training-room/video/store', 'TrainingRoomController@TrainingRoomVideoStore');
        Route::get('admin/training-room/video/edit/{VideoId}/{FolderId}/{RoleId}', 'TrainingRoomController@EditTrainingRoomVideo');
        Route::post('admin/training-room/video/update', 'TrainingRoomController@TrainingRoomVideoUpdate');
        // Articles
        Route::get('admin/training-room/article/add/{FolderId}/{RoleId}', 'TrainingRoomController@AddTrainingRoomArticle');
        Route::post('admin/training-room/article/store', 'TrainingRoomController@TrainingRoomArticleStore');
        Route::get('admin/training-room/article/edit/{ArticleId}/{FolderId}/{RoleId}', 'TrainingRoomController@EditTrainingRoomArticle');
        Route::post('admin/training-room/article/update', 'TrainingRoomController@TrainingRoomArticleUpdate');
        // Quiz
        Route::get('admin/training-room/quiz/add/{FolderId}/{RoleId}', 'TrainingRoomController@AddTrainingRoomQuiz');
        Route::post('admin/training-room/quiz/store', 'TrainingRoomController@TrainingRoomQuizStore');
        Route::get('admin/training-room/quiz/edit/{ArticleId}/{FolderId}/{RoleId}', 'TrainingRoomController@EditTrainingRoomQuiz');
        Route::post('admin/training-room/quiz/update', 'TrainingRoomController@TrainingRoomQuizUpdate');
        Route::get('admin/training-room/order/up/{Id}/{FolderId}/{Role}', 'TrainingRoomController@TrainingRoomOrderUp');
        Route::get('admin/training-room/order/down/{Id}/{FolderId}/{Role}', 'TrainingRoomController@TrainingRoomOrderDown');
        // Faqs
        Route::get('admin/training-room/faqs', 'FaqsController@index');
        Route::post('admin/training-room/faqs/all', 'FaqsController@load');
        Route::post('admin/training-room/faqs/add', 'FaqsController@store');
        Route::post('admin/training-room/faqs/delete', 'FaqsController@delete');
        Route::post('admin/training-room/faqs/update', 'FaqsController@update');
        // Import Leads
        Route::get('admin/lead/import/view', 'LeadController@ImportLeadsView');
        Route::post('admin/import/leads', 'LeadController@ImportLeadsStore');
        // Faqs
        Route::get('admin/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('admin/training/faqs/search', 'FaqsController@Search');
        // KPI
        Route::get('admin/kpi', 'KPIController@index');
        Route::post('admin/kpi/leadsource-analysis', 'KPIController@GetLeadSourceAnalytics');
        Route::post('admin/kpi/datasource-analysis', 'KPIController@GetDataSourceAnalytics');
        Route::post('admin/kpi/leadstatus-analysis', 'KPIController@GetLeadStatusAnalytics');
        // Users Report
        Route::get('admin/users-report', 'UsersReportController@index');
        Route::post('admin/users-report/all', 'UsersReportController@LoadAllUserReportRecord');
        // User Department Filter Routes
        Route::get('admin/user/state/filter/{id}', 'UserController@UserStateFilterPage');
        Route::post('admin/user/state/filter/store', 'UserController@UserDepartmentFilterStore');
        // Announcement Routes
        Route::get('admin/announcements', 'AnnouncementController@index');
        Route::get('admin/add/announcement', 'AnnouncementController@AddNewAnnouncement');
        Route::post('admin/announcement/store', 'AnnouncementController@Store');
        Route::post('admin/announcements/all', 'AnnouncementController@LoadAdminAllAnnouncements');
        Route::post('admin/announcement-active', 'AnnouncementController@active');
        Route::post('admin/announcement-deactive', 'AnnouncementController@deactive');
        Route::post('admin/edit/announcement', 'AnnouncementController@AdminEditAnnouncement');
        Route::post('admin/announcement/update', 'AnnouncementController@Update');
        Route::post('admin/delete/announcement', 'AnnouncementController@Delete');
        Route::get('admin/announcement/details/{id}', 'AnnouncementController@ViewDetails');
        Route::post('admin/announcements/details/all', 'AnnouncementController@LoadAdminAllAnnouncementDetails');
        // Broadcast Routes
        Route::get('admin/broadcasts', 'BroadcastController@index');
        Route::post('admin/broadcasts/all', 'BroadcastController@LoadAllBroadcasts');
        Route::post('admin/broadcast/send', 'BroadcastController@Store');
        Route::post('admin/broadcast/all/send', 'BroadcastController@SendBroadcastToAll');
        Route::get('admin/broadcast/details/{id}', 'BroadcastController@ViewDetails');
        Route::post('admin/broadcast/details/all', 'BroadcastController@LoadAllBroadcastDetails');
        // Constants
        Route::get('admin/magicnumber', 'DashboardController@ConstantValues');
        Route::post('admin/magicnumber/update', 'DashboardController@SetConstantValues');
        // Internal Messaging
        Route::get('admin/messaging', 'InternalMessagingController@index');
        // Coverage Area
        Route::get('admin/coverage-area', 'LeadController@CoverageArea');
    });
});

Route::group(['middleware' => ['global_manager_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        /* Global Manager Routes */
        Route::get('global_manager/dashboard', 'DashboardController@LoadDashboard')->name('globalManagerDashboard');
        // Buisness Account
        Route::get('global_manager/buissness_accounts', 'BuisnessAccountController@AdminAllBuisnessAccounts');
        Route::post('global_manager/buissness_accounts/all', 'BuisnessAccountController@LoadAdminAllBuisnessAccounts');
        // Investor Account
        Route::get('global_manager/investors', 'InvestorController@AdminAllInvestors');
        Route::get('global_manager/investor/add', 'InvestorController@AdminAddNewInvestor');
        Route::post('global_manager/investor/store', 'InvestorController@AdminInvestorStore');
        Route::post('global_manager/investor/all', 'InvestorController@LoadAdminAllInvestors');
        Route::post('global_manager/delete/investor', 'InvestorController@AdminDeleteInvestor');
        Route::post('global_manager/investor/edit', 'InvestorController@AdminEditInvestor');
        Route::post('global_manager/investor/update', 'InvestorController@AdminUpdateInvestor');
        Route::get('global_manager/investor/leads/{CompanyId}', 'InvestorController@ShowCompanyLeads');
        Route::post('global_manager/investor/companyLeads', 'InvestorController@CompanyLeads');
        // Title Company Account
        Route::get('global_manager/title_companies', 'TitleCompanyController@AdminAllTitleCompanies');
        Route::get('global_manager/title_company/add', 'TitleCompanyController@AdminAddNewTitleCompany');
        Route::post('global_manager/title_company/store', 'TitleCompanyController@AdminTitleCompanyStore');
        Route::post('global_manager/title_company/all', 'TitleCompanyController@LoadAdminAllTitleCompanies');
        Route::post('global_manager/delete/title_company', 'TitleCompanyController@AdminDeleteTitleCompany');
        Route::post('global_manager/title_company/edit', 'TitleCompanyController@AdminEditTitleCompany');
        Route::post('global_manager/title_company/update', 'TitleCompanyController@AdminUpdateTitleCompany');
        // Realtor Account
        Route::get('global_manager/realtor', 'RealtorController@AdminAllRealtors');
        Route::get('global_manager/realtor/add', 'RealtorController@AdminAddNewRealtor');
        Route::post('global_manager/realtor/store', 'RealtorController@AdminRealtorStore');
        Route::post('global_manager/realtor/all', 'RealtorController@LoadAdminAllRealtors');
        Route::post('global_manager/delete/realtor', 'RealtorController@AdminDeleteRealtor');
        Route::post('global_manager/realtor/edit', 'RealtorController@AdminEditRealtor');
        Route::post('global_manager/realtor/update', 'RealtorController@AdminUpdateRealtor');
        // Edit Profile Routes
        Route::get('global_manager/edit-profile', 'UserController@EditProfile');
        Route::post('global_manager/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('global_manager/update-account-security', 'UserController@UpdateAccountSecurity');
        // Users Routes
        Route::get('global_manager/users', 'UserController@AdminAllUsers');
        Route::get('global_manager/add/user', 'UserController@AdminAddNewUsers');
        Route::post('global_manager/user/store', 'UserController@AdminUserStore');
        Route::post('global_manager/delete/user', 'UserController@AdminDeleteUser');
        Route::post('global_manager/edit/user', 'UserController@AdminEditUser');
        Route::post('global_manager/user/update', 'UserController@AdminUpdateUser');
        Route::post('global_manager/user/changePassword', 'UserController@ChangePassword');
        Route::post('global_manager/user-active', 'UserController@active');
        Route::post('global_manager/user-ban', 'UserController@ban');
        Route::get('global_manager/users/progress', 'UserController@UsersProgress');
        Route::post('global_manager/users/progress/all', 'UserController@UsersProgressAll');
        Route::post('global_manager/user/activity/all', 'UserController@UserActivitiesAll');
        Route::post('global_manager/user/upgrade/account', 'UserController@UserUpgradeAccount');
        // Buisness Account
        Route::get('global_manager/buissness_accounts', 'BuisnessAccountController@AdminAllBuisnessAccounts');
        Route::post('global_manager/buissness_accounts/all', 'BuisnessAccountController@LoadAdminAllBuisnessAccounts');
        // Investor Account
        Route::get('global_manager/investors', 'InvestorController@AdminAllInvestors');
        Route::get('global_manager/investor/add', 'InvestorController@AdminAddNewInvestor');
        Route::post('global_manager/investor/store', 'InvestorController@AdminInvestorStore');
        Route::post('global_manager/investor/all', 'InvestorController@LoadAdminAllInvestors');
        Route::post('global_manager/delete/investor', 'InvestorController@AdminDeleteInvestor');
        Route::post('global_manager/investor/edit', 'InvestorController@AdminEditInvestor');
        Route::post('global_manager/investor/update', 'InvestorController@AdminUpdateInvestor');
        Route::get('global_manager/investor/leads/{CompanyId}', 'InvestorController@ShowCompanyLeads');
        Route::post('global_manager/investor/companyLeads', 'InvestorController@CompanyLeads');
        // Title Company Account
        Route::get('global_manager/title_companies', 'TitleCompanyController@AdminAllTitleCompanies');
        Route::get('global_manager/title_company/add', 'TitleCompanyController@AdminAddNewTitleCompany');
        Route::post('global_manager/title_company/store', 'TitleCompanyController@AdminTitleCompanyStore');
        Route::post('global_manager/title_company/all', 'TitleCompanyController@LoadAdminAllTitleCompanies');
        Route::post('global_manager/delete/title_company', 'TitleCompanyController@AdminDeleteTitleCompany');
        Route::post('global_manager/title_company/edit', 'TitleCompanyController@AdminEditTitleCompany');
        Route::post('global_manager/title_company/update', 'TitleCompanyController@AdminUpdateTitleCompany');
        // Realtor Account
        Route::get('global_manager/realtor', 'TitleCompanyController@AdminAllRealtors');
        Route::get('global_manager/realtor/add', 'TitleCompanyController@AdminAddNewRealtor');
        Route::post('global_manager/realtor/store', 'TitleCompanyController@AdminRealtorStore');
        Route::post('global_manager/realtor/all', 'TitleCompanyController@LoadAdminAllRealtors');
        Route::post('global_manager/delete/realtor', 'TitleCompanyController@AdminDeleteRealtor');
        Route::post('global_manager/realtor/edit', 'TitleCompanyController@AdminEditRealtor');
        Route::post('global_manager/realtor/update', 'TitleCompanyController@AdminUpdateRealtor');
        //Expense Route
        Route::get('global_manager/expenses', 'ExpenseController@AdminAllExpense');
        Route::get('global_manager/add/expenses', 'ExpenseController@AdminAddNewExpense');
        Route::post('global_manager/all', 'ExpenseController@LoadAdminAllExpense');
        Route::post('global_manager/delete/expenses', 'ExpenseController@AdminDeleteExpense');
        Route::post('global_manager/expenses/store', 'ExpenseController@AdminExpenseStore');
        Route::post('global_manager/edit/expenses', 'ExpenseController@AdminEditExpense');
        Route::post('global_manager/expenses/update', 'ExpenseController@AdminUpdateExpense');
        // Leads Routes
        Route::get('global_manager/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('global_manager/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('global_manager/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('global_manager/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('global_manager/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('global_manager/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('global_manager/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('global_manager/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('global_manager/lead/import', 'LeadController@ImportLeads');
        Route::post('global_manager/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('global_manager/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('global_manager/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('global_manager/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('global_manager/leads/assign', 'LeadController@AssignLeads');
        Route::post('global_manager/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('global_manager/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('global_manager/leads/details', 'LeadController@LeadDetails');
        Route::get('global_manager/leads/funnel', 'LeadController@leadFunnel');
        // Sale
        Route::get('global_manager/sale/add', 'SaleController@add')->name('admin-add-sale');
        Route::post('global_manager/sale/store', 'SaleController@LeadSaleStore');
        Route::get('global_manager/sales', 'SaleController@AllSales')->name('AllSales');
        Route::post('global_manager/sales/view', 'SaleController@ViewSaleDetails');
        // Payroll
        Route::get('global_manager/payroll/approve', 'PayrollController@index')->name('admin-payroll-approve');
        Route::post('global_manager/payroll-all', 'PayrollController@loadPayroll');
        Route::post('global_manager/payroll/submit', 'PayrollController@Submit');
        Route::post('global_manager/payroll/reject', 'PayrollController@rejectPayroll');
        Route::post('global_manager/payroll/bonus', 'PayrollController@addBonusPayroll');
        Route::post('global_manager/payroll/approved', 'PayrollController@approvedPayroll');
        Route::post('global_manager/payroll/edit/earning', 'PayrollController@editEarningPayroll');
        Route::post('global_manager/rejected/payroll-all', 'PayrollController@loadRejectedPayroll');
        Route::post('global_manager/payroll/breakdowns', 'PayrollController@loadPayrollBreakdowns');
        Route::post('global_manager/payroll/income-details', 'PayPeriodsController@ViewIncomeDetails');
        Route::post('global_manager/payroll/income-details/store-update', 'PayPeriodsController@StoreUpdateIncomeDetails');
        // Payroll Submitted
        Route::get('global_manager/payroll/submitted', 'PayPeriodsController@SubmittedPayroll')->name('admin-payroll-submitted');
        Route::post('global_manager/payroll/submitted/all', 'PayPeriodsController@loadSubmittedPayroll');
        Route::post('global_manager/payroll/submitted/breakdowns', 'PayPeriodsController@loadSubmittedPayrollBreakdowns');
        Route::post('global_manager/payroll/submitted/edit-pay-period', 'PayPeriodsController@EditPayPeriodEarning');
        Route::post('global_manager/payroll/submitted/update-pay-period', 'PayPeriodsController@UpdatePayPeriodEarning');
        Route::post('global_manager/payroll/submitted/approve', 'PayPeriodsController@GeneratePayroll');
        Route::post('global_manager/payroll/submitted/rollback', 'PayPeriodsController@Rollback');
        // Import Leads
        Route::get('global_manager/lead/import/view', 'LeadController@ImportLeadsView');
        Route::post('global_manager/import/leads', 'LeadController@ImportLeadsStore');
        // Faqs
        Route::get('global_manager/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('global_manager/training/faqs/search', 'FaqsController@Search');
        // KPI
        Route::get('global_manager/kpi', 'KPIController@index');
        Route::post('global_manager/kpi/leadsource-analysis', 'KPIController@GetLeadSourceAnalytics');
        Route::post('global_manager/kpi/datasource-analysis', 'KPIController@GetDataSourceAnalytics');
        Route::post('global_manager/kpi/leadstatus-analysis', 'KPIController@GetLeadStatusAnalytics');
        // Broadcast Routes
        Route::get('global_manager/broadcasts', 'BroadcastController@index');
        Route::post('global_manager/broadcasts/all', 'BroadcastController@LoadAllBroadcasts');
        Route::post('global_manager/broadcast/send', 'BroadcastController@Store');
        Route::post('global_manager/broadcast/all/send', 'BroadcastController@SendBroadcastToAll');
        Route::get('global_manager/broadcast/details/{id}', 'BroadcastController@ViewDetails');
        Route::post('global_manager/broadcast/details/all', 'BroadcastController@LoadAllBroadcastDetails');
        // Internal Messaging
        Route::get('global_manager/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['acquisition_manager_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        /* Global Manager Routes */
        Route::get('acquisition_manager/dashboard', 'DashboardController@LoadDashboard')->name('acquisitionManagerDashboard');
        // Edit Profile Routes
        Route::get('acquisition_manager/edit-profile', 'UserController@EditProfile');
        Route::post('acquisition_manager/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('acquisition_manager/update-account-security', 'UserController@UpdateAccountSecurity');
        /* Trainee Routes */
        Route::get('acquisition_manager/dashboard', 'DashboardController@TraineeDashboard')->name('acquisitionRepresentativeDashboard');
        Route::get('acquisition_manager/training', 'DashboardController@Training')->name('acquisitionRepresentativeTraining');
        Route::get('acquisition_manager/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('acquisitionManagerTrainingCourse');
        Route::get('acquisition_manager/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('acquisition_manager/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('acquisition_manager/training/faqs/search', 'FaqsController@Search');
        // Users Routes
        Route::get('acquisition_manager/users', 'UserController@AdminAllUsers');
        Route::get('acquisition_manager/add/user', 'UserController@AdminAddNewUsers');
        Route::post('acquisition_manager/user/store', 'UserController@AdminUserStore');
        Route::post('acquisition_manager/delete/user', 'UserController@AdminDeleteUser');
        Route::post('acquisition_manager/edit/user', 'UserController@AdminEditUser');
        Route::post('acquisition_manager/user/update', 'UserController@AdminUpdateUser');
        Route::post('acquisition_manager/user/activity/all', 'UserController@UserActivitiesAll');
        Route::post('acquisition_manager/user/upgrade/account', 'UserController@UserUpgradeAccount');
        // Leads Routes
        Route::get('acquisition_manager/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('acquisition_manager/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('acquisition_manager/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('acquisition_manager/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('acquisition_manager/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('acquisition_manager/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('acquisition_manager/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('acquisition_manager/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('acquisition_manager/lead/import', 'LeadController@ImportLeads');
        Route::post('acquisition_manager/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('acquisition_manager/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('acquisition_manager/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('acquisition_manager/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('acquisition_manager/leads/assign', 'LeadController@AssignLeads');
        Route::post('acquisition_manager/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('acquisition_manager/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('acquisition_manager/leads/details', 'LeadController@LeadDetails');
        Route::get('acquisition_manager/leads/funnel', 'LeadController@leadFunnel');
        // Broadcast Routes
        Route::get('acquisition_manager/broadcasts', 'BroadcastController@index');
        Route::post('acquisition_manager/broadcasts/all', 'BroadcastController@LoadAllBroadcasts');
        Route::post('acquisition_manager/broadcast/send', 'BroadcastController@Store');
        Route::post('acquisition_manager/broadcast/all/send', 'BroadcastController@SendBroadcastToAll');
        Route::get('acquisition_manager/broadcast/details/{id}', 'BroadcastController@ViewDetails');
        Route::post('acquisition_manager/broadcast/details/all', 'BroadcastController@LoadAllBroadcastDetails');
        // Internal Messaging
        Route::get('acquisition_manager/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['disposition_manager_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        /* Global Manager Routes */
        Route::get('disposition_manager/dashboard', 'DashboardController@LoadDashboard')->name('dispositionManagerDashboard');
        // Edit Profile Routes
        Route::get('disposition_manager/edit-profile', 'UserController@EditProfile');
        Route::post('disposition_manager/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('disposition_manager/update-account-security', 'UserController@UpdateAccountSecurity');
        /* Trainee Routes */
        Route::get('disposition_manager/dashboard', 'DashboardController@TraineeDashboard')->name('acquisitionRepresentativeDashboard');
        Route::get('disposition_manager/training', 'DashboardController@Training')->name('acquisitionRepresentativeTraining');
        Route::get('disposition_manager/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('dispositionRepresentativeTrainingCourse');
        Route::get('disposition_manager/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('disposition_manager/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('disposition_manager/training/faqs/search', 'FaqsController@Search');
        // Users Routes
        Route::get('disposition_manager/users', 'UserController@AdminAllUsers');
        Route::get('disposition_manager/add/user', 'UserController@AdminAddNewUsers');
        Route::post('disposition_manager/user/store', 'UserController@AdminUserStore');
        Route::post('disposition_manager/delete/user', 'UserController@AdminDeleteUser');
        Route::post('disposition_manager/edit/user', 'UserController@AdminEditUser');
        Route::post('disposition_manager/user/update', 'UserController@AdminUpdateUser');
        Route::post('disposition_manager/user/activity/all', 'UserController@UserActivitiesAll');
        Route::post('disposition_manager/user/upgrade/account', 'UserController@UserUpgradeAccount');
        // Leads Routes
        Route::get('disposition_manager/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('disposition_manager/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('disposition_manager/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('disposition_manager/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('disposition_manager/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('disposition_manager/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('disposition_manager/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('disposition_manager/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('disposition_manager/lead/import', 'LeadController@ImportLeads');
        Route::post('disposition_manager/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('disposition_manager/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('disposition_manager/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('disposition_manager/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('disposition_manager/leads/assign', 'LeadController@AssignLeads');
        Route::post('disposition_manager/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('disposition_manager/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('disposition_manager/leads/details', 'LeadController@LeadDetails');
        Route::get('disposition_manager/leads/funnel', 'LeadController@leadFunnel');
        // Broadcast Routes
        Route::get('disposition_manager/broadcasts', 'BroadcastController@index');
        Route::post('disposition_manager/broadcasts/all', 'BroadcastController@LoadAllBroadcasts');
        Route::post('disposition_manager/broadcast/send', 'BroadcastController@Store');
        Route::post('disposition_manager/broadcast/all/send', 'BroadcastController@SendBroadcastToAll');
        Route::get('disposition_manager/broadcast/details/{id}', 'BroadcastController@ViewDetails');
        Route::post('disposition_manager/broadcast/details/all', 'BroadcastController@LoadAllBroadcastDetails');
        // Internal Messaging
        Route::get('disposition_manager/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['acquisition_representative_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('acquisition_representative/dashboard', 'DashboardController@TraineeDashboard')->name('acquisitionRepresentativeDashboard');
        // Edit Profile Routes
        Route::get('acquisition_representative/edit-profile', 'UserController@EditProfile');
        Route::post('acquisition_representative/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('acquisition_representative/update-account-security', 'UserController@UpdateAccountSecurity');
        // Trainee Routes
        Route::get('acquisition_representative/training', 'DashboardController@Training')->name('acquisitionRepresentativeTraining');
        Route::get('acquisition_representative/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('acquisitionRepresentativeTrainingCourse');
        Route::get('acquisition_representative/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('acquisition_representative/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('acquisition_representative/training/faqs/search', 'FaqsController@Search');
        // Leads Routes
        Route::get('acquisition_representative/leads', 'LeadController@RepresentativeAllLeads')->name('acquisitionRepresentative-leads');
        Route::get('acquisition_representative/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('acquisition_representative/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('acquisition_representative/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('acquisition_representative/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('acquisition_representative/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('acquisition_representative/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('acquisition_representative/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('acquisition_representative/lead/import', 'LeadController@ImportLeads');
        Route::post('acquisition_representative/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('acquisition_representative/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('acquisition_representative/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('acquisition_representative/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('acquisition_representative/leads/assign', 'LeadController@AssignLeads');
        Route::post('acquisition_representative/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('acquisition_representative/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('acquisition_representative/leads/details', 'LeadController@LeadDetails');
        Route::get('acquisition_representative/leads/funnel', 'LeadController@leadFunnel');
        // Internal Messaging
        Route::get('acquisition_representative/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['disposition_representative_route_validation']], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('disposition_representative/dashboard', 'DashboardController@TraineeDashboard')->name('dispositionRepresentativeDashboard');
        // Buisness Account
        Route::get('disposition_representative/buissness_accounts', 'BuisnessAccountController@AdminAllBuisnessAccounts');
        Route::post('disposition_representative/buissness_accounts/all', 'BuisnessAccountController@LoadAdminAllBuisnessAccounts');
        // Investor Account
        Route::get('disposition_representative/investors', 'InvestorController@AdminAllInvestors');
        Route::get('disposition_representative/investor/add', 'InvestorController@AdminAddNewInvestor');
        Route::post('disposition_representative/investor/store', 'InvestorController@AdminInvestorStore');
        Route::post('disposition_representative/investor/all', 'InvestorController@LoadAdminAllInvestors');
        Route::post('disposition_representative/delete/investor', 'InvestorController@AdminDeleteInvestor');
        Route::post('disposition_representative/investor/edit', 'InvestorController@AdminEditInvestor');
        Route::post('disposition_representative/investor/update', 'InvestorController@AdminUpdateInvestor');
        Route::get('disposition_representative/investor/leads/{CompanyId}', 'InvestorController@ShowCompanyLeads');
        Route::post('disposition_representative/investor/companyLeads', 'InvestorController@CompanyLeads');
        // Title Company Account
        Route::get('disposition_representative/title_companies', 'TitleCompanyController@AdminAllTitleCompanies');
        Route::get('disposition_representative/title_company/add', 'TitleCompanyController@AdminAddNewTitleCompany');
        Route::post('disposition_representative/title_company/store', 'TitleCompanyController@AdminTitleCompanyStore');
        Route::post('disposition_representative/title_company/all', 'TitleCompanyController@LoadAdminAllTitleCompanies');
        Route::post('disposition_representative/delete/title_company', 'TitleCompanyController@AdminDeleteTitleCompany');
        Route::post('disposition_representative/title_company/edit', 'TitleCompanyController@AdminEditTitleCompany');
        Route::post('disposition_representative/title_company/update', 'TitleCompanyController@AdminUpdateTitleCompany');
        // Realtor Account
        Route::get('disposition_representative/realtor', 'RealtorController@AdminAllRealtors');
        Route::get('disposition_representative/realtor/add', 'RealtorController@AdminAddNewRealtor');
        Route::post('disposition_representative/realtor/store', 'RealtorController@AdminRealtorStore');
        Route::post('disposition_representative/realtor/all', 'RealtorController@LoadAdminAllRealtors');
        Route::post('disposition_representative/delete/realtor', 'RealtorController@AdminDeleteRealtor');
        Route::post('disposition_representative/realtor/edit', 'RealtorController@AdminEditRealtor');
        Route::post('disposition_representative/realtor/update', 'RealtorController@AdminUpdateRealtor');
        // Edit Profile Routes
        Route::get('disposition_representative/edit-profile', 'UserController@EditProfile');
        Route::post('disposition_representative/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('disposition_representative/update-account-security', 'UserController@UpdateAccountSecurity');
        // User Routes
        // Users Routes
        Route::get('disposition_representative/users', 'UserController@AdminAllUsers');
        Route::get('disposition_representative/add/user', 'UserController@AdminAddNewUsers');
        Route::post('disposition_representative/user/store', 'UserController@AdminUserStore');
        Route::post('disposition_representative/delete/user', 'UserController@AdminDeleteUser');
        Route::post('disposition_representative/edit/user', 'UserController@AdminEditUser');
        Route::post('disposition_representative/user/update', 'UserController@AdminUpdateUser');
        Route::post('disposition_representative/user/changePassword', 'UserController@ChangePassword');
        Route::post('disposition_representative/user-active', 'UserController@active');
        Route::post('disposition_representative/user-ban', 'UserController@ban');
        Route::get('disposition_representative/users/progress', 'UserController@UsersProgress');
        Route::post('disposition_representative/users/progress/all', 'UserController@UsersProgressAll');
        Route::post('disposition_representative/user/activity/all', 'UserController@UserActivitiesAll');
        Route::post('disposition_representative/user/upgrade/account', 'UserController@UserUpgradeAccount');
        // Training Routes
        Route::get('disposition_representative/training', 'DashboardController@Training')->name('dispositionRepresentativeTraining');
        Route::get('disposition_representative/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('dispositionRepresentativeTrainingCourse');
        Route::get('disposition_representative/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('disposition_representative/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('disposition_representative/training/faqs/search', 'FaqsController@Search');
        // Leads Routes
        Route::get('disposition_representative/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('disposition_representative/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('disposition_representative/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('disposition_representative/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('disposition_representative/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('disposition_representative/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('disposition_representative/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('disposition_representative/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('disposition_representative/lead/import', 'LeadController@ImportLeads');
        Route::post('disposition_representative/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('disposition_representative/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('disposition_representative/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('disposition_representative/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('disposition_representative/leads/assign', 'LeadController@AssignLeads');
        Route::post('disposition_representative/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('disposition_representative/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('disposition_representative/leads/details', 'LeadController@LeadDetails');
        Route::get('disposition_representative/leads/funnel', 'LeadController@leadFunnel');
        // Internal Messaging
        Route::get('disposition_representative/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['cold_caller_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('cold_caller/dashboard', 'DashboardController@TraineeDashboard')->name('coldCallerDashboard');
        // Edit Profile Routes
        Route::get('cold_caller/edit-profile', 'UserController@EditProfile');
        Route::post('cold_caller/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('cold_caller/update-account-security', 'UserController@UpdateAccountSecurity');
        // Trainee Routes
        Route::get('cold_caller/training', 'DashboardController@Training')->name('coldCallerTraining');
        Route::get('cold_caller/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('coldCallerTrainingCourse');
        Route::get('cold_caller/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('cold_caller/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('cold_caller/training/faqs/search', 'FaqsController@Search');
        // Leads Routes
        Route::get('cold_caller/leads', 'LeadController@RepresentativeAllLeads')->name('admin-leads');
        Route::get('cold_caller/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('cold_caller/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('cold_caller/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('cold_caller/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('cold_caller/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('cold_caller/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('cold_caller/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('cold_caller/lead/import', 'LeadController@ImportLeads');
        Route::post('cold_caller/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('cold_caller/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('cold_caller/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('cold_caller/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('cold_caller/leads/assign', 'LeadController@AssignLeads');
        Route::post('cold_caller/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('cold_caller/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('cold_caller/leads/details', 'LeadController@LeadDetails');
        // Internal Messaging
        Route::get('cold_caller/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['affiliate_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('affiliate/dashboard', 'DashboardController@TraineeDashboard')->name('affiliateDashboard');
        /* Trainee Routes */
        Route::get('affiliate/training', 'DashboardController@Training')->name('affiliateTraining');
        Route::get('affiliate/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('affliateTrainingCourse');
        Route::get('affiliate/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('affiliate/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('affiliate/training/faqs/search', 'FaqsController@Search');
        // Edit Profile Routes
        Route::get('affiliate/edit-profile', 'UserController@EditProfile');
        Route::post('affiliate/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('affiliate/update-account-security', 'UserController@UpdateAccountSecurity');
        // Leads Routes
        Route::get('affiliate/leads', 'LeadController@RepresentativeAllLeads')->name('affiliate-leads');
        Route::get('affiliate/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('affiliate/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('affiliate/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('affiliate/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('affiliate/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('affiliate/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('affiliate/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('affiliate/lead/import', 'LeadController@ImportLeads');
        Route::get('affiliate/lead/import/view', 'LeadController@ImportLeadsView');
        Route::post('affiliate/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('affiliate/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('affiliate/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('affiliate/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('affiliate/leads/assign', 'LeadController@AssignLeads');
        Route::post('affiliate/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('affiliate/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('affiliate/leads/details', 'LeadController@LeadDetails');
        Route::post('import/leads', 'LeadController@ImportLeadsStore')->name('import');
        //  Import
        Route::get('affiliate/leads/import', 'LeadController@AssignLeads');
        // Internal Messaging
        Route::get('affiliate/messaging', 'InternalMessagingController@index');
    });
});

Route::group(['middleware' => ['realtor_route_validate']], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('realtor/dashboard', 'DashboardController@LoadDashboard')->name('realtorDashboard');
        /* Trainee Routes */
        Route::get('realtor/training', 'DashboardController@Training')->name('realtorTraining');
        Route::get('realtor/training/course/{CourseId}', 'DashboardController@TrainingCourse')->name('realtorTrainingCourse');
        Route::get('realtor/training/faqs', 'TrainingRoomController@ViewFaqs');
        Route::post('realtor/training/assignment/complete', 'TrainingRoomController@MarkAssignmentAsComplete');
        Route::post('realtor/training/faqs/search', 'FaqsController@Search');
        // Edit Profile Routes
        Route::get('realtor/edit-profile', 'UserController@EditProfile');
        Route::post('realtor/update-personal-details', 'UserController@UpdatePersonalDetails');
        Route::post('realtor/update-account-security', 'UserController@UpdateAccountSecurity');
        // Leads Routes
        Route::get('realtor/leads', 'LeadController@RepresentativeAllLeads')->name('affiliate-leads');
        Route::get('realtor/lead/add', 'LeadController@RepresentativeAddNewLead');
        Route::post('realtor/lead/store', 'LeadController@RepresentativeLeadStore');
        Route::get('realtor/lead/edit/{Id}', 'LeadController@RepresentativeEditLead');
        Route::post('realtor/lead/update', 'LeadController@RepresentativeUpdateLead');
        Route::get('realtor/lead/change/status/{Id}', 'LeadController@AdminChangeLeadStatus');
        Route::post('realtor/lead/update/status', 'LeadController@AdminUpdateLeadStatus');
        Route::post('realtor/lead/update/appointmentTime', 'LeadController@AdminUpdateLeadAppointmentTime');
        Route::get('realtor/lead/import', 'LeadController@ImportLeads');
        Route::get('realtor/lead/import/view', 'LeadController@ImportLeadsView');
        Route::post('realtor/leads/import/store', 'LeadController@ImportLeadsStore');
        Route::post('realtor/dispoleads/import/store', 'LeadController@ImportDispoLeadsStore');
        Route::post('realtor/lead/assignedUsers', 'LeadController@AssignedUsersToLead');
        Route::post('realtor/lead/assignToUsers', 'LeadController@AssignLeadToUser');
        Route::get('realtor/leads/assign', 'LeadController@AssignLeads');
        Route::post('realtor/leads/assign/all', 'LeadController@LoadAllAssignLeads');
        Route::post('realtor/leads/assign/assignToUsers', 'LeadController@AssignLeadsToUsers');
        Route::post('realtor/leads/details', 'LeadController@LeadDetails');
        //  Import
        Route::get('realtor/leads/import', 'LeadController@AssignLeads');
        // Import Leads
        Route::get('realtor/lead/import/view', 'LeadController@ImportLeadsView');
        Route::post('realtor/import/leads', 'LeadController@ImportLeadsStore');
        // Internal Messaging
        Route::get('realtor/messaging', 'InternalMessagingController@index');
    });
});

Route::middleware(['auth'])->group(function () {
    /* Common Routes */
    Route::get('dashboard', 'DashboardController@index');
    Route::post('/user/all', 'UserController@LoadAdminAllUsers');
    Route::post('/user/state/check', 'UserController@UserStateCheck');
    Route::post('/load/counties', 'UserController@LoadCounties');
    Route::post('/load/cities', 'UserController@LoadCities');
    Route::post('/expenses/all', 'ExpenseController@LoadAdminAllExpense');
    Route::post('/teams/all', 'TeamsController@AdminTeamsload');
    Route::post('/leads/all', 'LeadController@LoadRepresentativeAllLeads');
    Route::post('/lead/closeddate', 'LeadController@GetLeadClosedDate');
    Route::post('leads/dashboard/all', 'LeadController@LoadDashboardAllLeads');
    Route::post('leads/leadnumber/all', 'LeadController@LoadAllLeadsByLeadNumber');
    Route::post('leads/leadphonenumber/all', 'LeadController@LoadAllLeadsByLeadPhoneNumber');
    Route::post('leads/delete', 'LeadController@Delete');
    Route::post('/dispo-lead/all', 'DispoLeadController@LoadRepresentativeAllDispoLeads');
    Route::post('/training-link/all', 'TrainingCoverageLinksController@load');
    Route::post('/coverage-file/all', 'TrainingCoverageLinksController@load_covergae');
    Route::post('sale/sales-all', 'SaleController@loadSales')->name('loadSales');
    Route::post('historynote/store', 'LeadController@HistoryNoteStore');
    Route::post('historynote/all', 'LeadController@LoadHistoryNote');
    Route::post('historycallnote/store', 'CallRequestController@HistoryCallNoteStore');
    Route::post('history_note/all', 'CallRequestController@LoadCallHistoryNote');
    Route::post('/marketing-report/all', 'MarketingReportController@LoadAllMarketingReportRecord');
    Route::post('/notification/all', 'NotificationController@UnreadUserNotifications');
    Route::post('/notification/read/all', 'NotificationController@ReadUserNotifications');
    Route::get('/notification/load/all', 'NotificationController@LoadAllUserNotifications');
    Route::post('/notification/markasread', 'NotificationController@MarkAsReadNotification');
    Route::post('/notification/clear/all', 'NotificationController@ClearAllNotification');
    Route::post('/broadcast/all', 'BroadcastController@GetUserBroadcast');
    Route::post('/broadcast/status/update', 'BroadcastController@UpdateReadStatus');
    Route::post('announcement/read', 'AnnouncementController@Read');
    // Chat
    Route::post('message/send', 'InternalMessagingController@SendMessage');
    Route::post('messages/load', 'InternalMessagingController@LoadMessages');
    Route::post('messages/read', 'InternalMessagingController@MessagesReadAll');
    Route::post('load/chat/list', 'InternalMessagingController@LoadChatList');
    Route::post('load/contact/list', 'InternalMessagingController@LoadContactList');
    Route::post('load/search/users', 'InternalMessagingController@LoadSearchUsersList');
    Route::post('message/unread', 'InternalMessagingController@CalculateUnreadMessage');
    // Feed - Group
    Route::post('add/new/group', 'InternalMessagingController@AddNewGroup');
    Route::post('load/group/list', 'InternalMessagingController@LoadGroupList');
    Route::post('load/group/details', 'InternalMessagingController@LoadGroupDetails');
    Route::post('update/group', 'InternalMessagingController@UpdateGroup');
    // Assign Selected Leads
    Route::post('leads/assign/selected', 'LeadController@AssignSelectedLeadsToUsers');
    // Search Zoning Leads
    Route::post('coverage-area/lead/search', 'LeadController@ZoningSearchLead')->name('zoningSearch');
    // Training Room Searching
    Route::post('training-room/course/search', 'TrainingRoomController@SearchCourse')->name('trainingRoomCourseSearch');
    // Faq
    Route::post('faq/details', 'FaqsController@getFaqDetails');
});

// Referral Link
Route::get('lead/add/{id}', 'ReferralLink@ReferralLink')->name('ReferralLink');
Route::post('/load/counties/1', 'ReferralLink@LoadCounties');
Route::post('/load/cities/1', 'ReferralLink@LoadCities');
Route::post('/referral/lead/store', 'ReferralLink@LeadStore');
