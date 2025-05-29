<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UsersReportController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

  public function index()
  {
	    $page = "users_report";
			$Role = Session::get('user_role');
			// All States
			$States = DB::table('states')
					->get();
	    return view('admin.users-report.index', compact('page', 'Role', 'States'));
  }

	public function LoadAllUserReportRecord(Request $request)
	{
			$Role = Session::get('user_role');
			$lead_type = 1;

			$limit = $request->post('length');
			$start = $request->post('start');
			$searchTerm = $request->post('search')['value'];

			// Filter Page
			$User = $request->post('User');
			$StateFilter = $request->post('StateFilter');
			$StartDate = $request->post('StartDate');
			$EndDate = $request->post('EndDate');

			$fetch_data = null;
			$recordsTotal = null;
			$recordsFiltered = null;

			if ($User == 7) {
				if ($searchTerm == '') {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
				} else {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
					}

					$data = array();
					$UserFullName = "";
					$TotalLeads = 0;
					$TotalInterested = 0;
					$TotalNotInterested = 0;
					$TotalDoNotCall = 0;
					$TotalNoAnswer = 0;
					$TotalAssignedToAquisition = 0;

					foreach ($fetch_data as $row => $item) {
							$UserFullName = "";

							if ($item->user_first != "") {
								$UserFullName .= $item->user_first;
							}
							if ($item->user_middlename != "") {
								$UserFullName .= " " . $item->user_middlename;
							}
							if ($item->user_last != "") {
								$UserFullName .= " " . $item->user_last;
							}

							$TotalLeads = $this->CalculateTotalLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalInterested = $this->CalculateTotalInterestedLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalNotInterested = $this->CalculateTotalNotInterestedLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalDoNotCall = $this->CalculateTotalDoNotCallLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalNoAnswer = $this->CalculateTotalNoAnswerLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalAssignedToAquisition = $this->CalculateTotalAssignedToAquisistion($item->id, $StartDate, $EndDate);

							$sub_array = array();
							$sub_array['full_name'] = wordwrap($UserFullName, 15, "<br>");
							$sub_array['total_leads'] = $TotalLeads;
							$sub_array['total_interested'] = $TotalInterested;
							$sub_array['total_not_interested'] = $TotalNotInterested;
							$sub_array['total_do_not_call'] = $TotalDoNotCall;
							$sub_array['total_no_answer'] = $TotalNoAnswer;
							$sub_array['total_assigned_to_aquisition'] = $TotalAssignedToAquisition;
							$data[] = $sub_array;
					}

					$json_data = array(
							"draw" => intval($request->post('draw')),
							"iTotalRecords" => $recordsTotal,
							"iTotalDisplayRecords" => $recordsFiltered,
							"aaData" => $data
					);

					echo json_encode($json_data);
			}
			elseif ($User == 8) {
				if ($searchTerm == '') {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
				} else {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
					}

					$data = array();
					$UserFullName = "";
					$TotalLeads = 0;
					$TotalUnderContract = 0;
					$TotalInterested = 0;
					$TotalNotInterested = 0;
					$TotalDoNotCall = 0;
					$TotalAssignedToAquisition = 0;
					$TotalClosedWON = 0;
					$TotalDealLost = 0;

					foreach ($fetch_data as $row => $item) {
							$UserFullName = "";

							if ($item->user_first != "") {
								$UserFullName .= $item->user_first;
							}
							if ($item->user_middlename != "") {
								$UserFullName .= " " . $item->user_middlename;
							}
							if ($item->user_last != "") {
								$UserFullName .= " " . $item->user_last;
							}

							$TotalLeads = $this->CalculateTotalLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalUnderContract = $this->CalculateTotalUnderContractLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalInterested = $this->CalculateTotalInterestedLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalNotInterested = $this->CalculateTotalNotInterestedLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalDoNotCall = $this->CalculateTotalDoNotCallLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalAssignedToAquisition = $this->CalculateTotalAssignedToAquisistion($item->id, $StartDate, $EndDate);
							$TotalClosedWON = $this->CalculateTotalClosedWonLeadsByUser($item->id, $StartDate, $EndDate);
							$TotalDealLost = $this->CalculateTotalDealLostLeadsByUser($item->id, $StartDate, $EndDate);

							$sub_array = array();
							$sub_array['full_name'] = wordwrap($UserFullName, 15, "<br>");
							$sub_array['total_leads'] = $TotalLeads;
							$sub_array['total_under_contract'] = $TotalUnderContract;
							$sub_array['total_interested'] = $TotalInterested;
							$sub_array['total_not_interested'] = $TotalNotInterested;
							$sub_array['total_do_not_call'] = $TotalDoNotCall;
							$sub_array['total_assigned_to_aquisition'] = $TotalAssignedToAquisition;
							$sub_array['total_closed_won'] = $TotalClosedWON;
							$sub_array['total_deal_lost'] = $TotalDealLost;
							$data[] = $sub_array;
					}

					$json_data = array(
							"draw" => intval($request->post('draw')),
							"iTotalRecords" => $recordsTotal,
							"iTotalDisplayRecords" => $recordsFiltered,
							"aaData" => $data
					);

					echo json_encode($json_data);
			}
			elseif ($User == 4) {
				if ($searchTerm == '') {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(4, 6));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(4, 6));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
				} else {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(4, 6));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(4, 6));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
					}

					$data = array();
					$UserFullName = "";
					$TotalLeads = 0;
					$TotalSendToInvestor = 0;
					$TotalNegotiating = 0;
					$TotalSentContractToInvestor = 0;
					$TotalSentToTitle = 0;
					$TotalEMDReceived = 0;
					$TotalEMDNotReceived = 0;
					$TotalInspection = 0;
					$TotalCloseOn = 0;
					$TotalClosedWON = 0;
					$TotalDealLost = 0;

					foreach ($fetch_data as $row => $item) {
							$UserFullName = "";

							if ($item->user_first != "") {
								$UserFullName .= $item->user_first;
							}
							if ($item->user_middlename != "") {
								$UserFullName .= " " . $item->user_middlename;
							}
							if ($item->user_last != "") {
								$UserFullName .= " " . $item->user_last;
							}

							$TotalLeads = $this->CalculateTotalLeadsByAquisitionDisposition($item->id, $StartDate, $EndDate);
							$TotalSendToInvestor = $this->CalculateTotalSendToInvestor($item->id, $StartDate, $EndDate);
							$TotalNegotiating = $this->CalculateTotalNegotiatingWithInvestor($item->id, $StartDate, $EndDate);
							$TotalSentContractToInvestor = $this->CalculateTotalSentContractToInvestor($item->id, $StartDate, $EndDate);
							$TotalSentToTitle = $this->CalculateTotalSentToTitle($item->id, $StartDate, $EndDate);
							$TotalEMDReceived = $this->CalculateTotalEMDReceived($item->id, $StartDate, $EndDate);
							$TotalEMDNotReceived = $this->CalculateTotalEMDNotReceived($item->id, $StartDate, $EndDate);
							$TotalInspection = $this->CalculateTotalInspection($item->id, $StartDate, $EndDate);
							$TotalCloseOn = $this->CalculateTotalCloseOn($item->id, $StartDate, $EndDate);
							$TotalClosedWON = $this->CalculateTotalDispositionClosedWON($item->id, $StartDate, $EndDate);
							$TotalDealLost = $this->CalculateTotalDispositionDealLost($item->id, $StartDate, $EndDate);

							$sub_array = array();
							$sub_array['full_name'] = wordwrap($UserFullName, 15, "<br>");
							$sub_array['total_leads'] = $TotalLeads;
							$sub_array['total_send_to_investor'] = $TotalSendToInvestor;
							$sub_array['total_negotiating'] = $TotalNegotiating;
							$sub_array['total_sent_contract_to_investor'] = $TotalSentContractToInvestor;
							$sub_array['total_sent_to_title'] = $TotalSentToTitle;
							$sub_array['total_emd_received'] = $TotalEMDReceived;
							$sub_array['total_emd_not_received'] = $TotalEMDNotReceived;
							$sub_array['total_inspection'] = $TotalInspection;
							$sub_array['total_close_on'] = $TotalCloseOn;
							$sub_array['total_closed_won'] = $TotalClosedWON;
							$sub_array['total_deal_lost'] = $TotalDealLost;
							$data[] = $sub_array;
					}

					$json_data = array(
							"draw" => intval($request->post('draw')),
							"iTotalRecords" => $recordsTotal,
							"iTotalDisplayRecords" => $recordsFiltered,
							"aaData" => $data
					);

					echo json_encode($json_data);
			}
			elseif ($User == 3) {
				if ($searchTerm == '') {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(3, 5));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(3, 5));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
				} else {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(3, 5));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->whereIn('users.role_id', array(3, 5));
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
					}

					$data = array();
					$UserFullName = "";
					$TotalLeads = 0;
					$TotalNotAccepted = 0;
					$TotalAccepted = 0;
					$TotalNegotiating = 0;
					$TotalAgreementSent = 0;
					$TotalAgreementReceived = 0;

					foreach ($fetch_data as $row => $item) {
							$UserFullName = "";

							if ($item->user_first != "") {
								$UserFullName .= $item->user_first;
							}
							if ($item->user_middlename != "") {
								$UserFullName .= " " . $item->user_middlename;
							}
							if ($item->user_last != "") {
								$UserFullName .= " " . $item->user_last;
							}

							$TotalLeads = $this->CalculateTotalLeadsByAquisitionDisposition($item->id, $StartDate, $EndDate);
							$TotalNotAccepted = $this->CalculateTotalNotAccepted($item->id, $StartDate, $EndDate);
							$TotalAccepted = $this->CalculateTotalAccepted($item->id, $StartDate, $EndDate);
							$TotalNegotiating = $this->CalculateTotalNegotiating($item->id, $StartDate, $EndDate);
							$TotalAgreementSent = $this->CalculateTotalAgreementSent($item->id, $StartDate, $EndDate);
							$TotalAgreementReceived = $this->CalculateTotalAgreementReceived($item->id, $StartDate, $EndDate);

							$sub_array = array();
							$sub_array['full_name'] = wordwrap($UserFullName, 15, "<br>");
							$sub_array['total_leads'] = $TotalLeads;
							$sub_array['total_not_accepted'] = $TotalNotAccepted;
							$sub_array['total_accepted'] = $TotalAccepted;
							$sub_array['total_negotiating'] = $TotalNegotiating;
							$sub_array['total_agreement_sent'] = $TotalAgreementSent;
							$sub_array['total_agreement_received'] = $TotalAgreementReceived;
							$data[] = $sub_array;
					}

					$json_data = array(
							"draw" => intval($request->post('draw')),
							"iTotalRecords" => $recordsTotal,
							"iTotalDisplayRecords" => $recordsFiltered,
							"aaData" => $data
					);

					echo json_encode($json_data);
			}
			elseif ($User == 9) {
				if ($searchTerm == '') {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
				} else {
						$fetch_data = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->offset($start)
								->limit($limit)
								->get();

						$recordsTotal = sizeof($fetch_data);
						$recordsFiltered = DB::table('users')
								->join('profiles', 'users.id', '=', 'profiles.user_id')
								->where('users.deleted_at', '=', null)
								->where(function ($query) use ($searchTerm) {
										$query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
										$query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
								})
								->where(function ($query) use ($User, $StateFilter, $StartDate, $EndDate) {
										if ($StateFilter != "0") {
												$query->where('profiles.state', '=', $StateFilter);
										}
										if ($User != 0) {
												$query->where('users.role_id', '=', $User);
										}
								})
								->select('users.id', 'profiles.firstname AS user_first', 'profiles.middlename AS user_middlename', 'profiles.lastname AS user_last')
								->count();
					}

					$data = array();
					$UserFullName = "";
					$IncomingTotalLeads = 0;
					$OutgoingTotalLeads = 0;
					$InTotalUnderContract = 0;
					$OutTotalUnderContract = 0;
					$InClosedWON = 0;
					$InDealLost = 0;
					$OutClosedWON = 0;
					$OutDealLost = 0;

					foreach ($fetch_data as $row => $item) {
							$UserFullName = "";

							if ($item->user_first != "") {
								$UserFullName .= $item->user_first;
							}
							if ($item->user_middlename != "") {
								$UserFullName .= " " . $item->user_middlename;
							}
							if ($item->user_last != "") {
								$UserFullName .= " " . $item->user_last;
							}

							$IncomingTotalLeads = $this->CalculateIncomingTotalLeads($item->id, $StartDate, $EndDate);
							$OutgoingTotalLeads = $this->CalculateOutgoingTotalLeads($item->id, $StartDate, $EndDate);
							$InTotalUnderContract = $this->CalculateInTotalUnderContract($item->id, $StartDate, $EndDate);
							$OutTotalUnderContract = $this->CalculateOutTotalUnderContract($item->id, $StartDate, $EndDate);
							$InClosedWON = $this->CalculateInClosedWON($item->id, $StartDate, $EndDate);
							$InDealLost = $this->CalculateInDealLost($item->id, $StartDate, $EndDate);
							$OutClosedWON = $this->CalculateOutClosedWON($item->id, $StartDate, $EndDate);
							$OutDealLost = $this->CalculateOutDealLost($item->id, $StartDate, $EndDate);

							$sub_array = array();
							$sub_array['full_name'] = wordwrap($UserFullName, 15, "<br>");
							$sub_array['incoming_total_leads'] = $IncomingTotalLeads;
							$sub_array['outgoing_total_leads'] = $OutgoingTotalLeads;
							$sub_array['in_total_under_contract'] = $InTotalUnderContract;
							$sub_array['out_total_under_contract'] = $OutTotalUnderContract;
							$sub_array['in_closed_won'] = $InClosedWON;
							$sub_array['in_deal_lost'] = $InDealLost;
							$sub_array['out_closed_won'] = $OutClosedWON;
							$sub_array['out_deal_lost'] = $OutDealLost;
							$data[] = $sub_array;
					}

					$json_data = array(
							"draw" => intval($request->post('draw')),
							"iTotalRecords" => $recordsTotal,
							"iTotalDisplayRecords" => $recordsFiltered,
							"aaData" => $data
					);

					echo json_encode($json_data);
			}
	}

	public function CalculateTotalLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalInterestedLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 1)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalNotInterestedLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 2)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalDoNotCallLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 4)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalNoAnswerLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 5)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalAssignedToAquisistion($UserId, $StartDate, $EndDate)
	{
			$TotalAssigntoAquisitionLeads = 0;
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->get();

			$total_lead_assignments = DB::table('lead_assignments')->get();

			foreach ($total_leads as $lead) {
				foreach ($total_lead_assignments as $assignment) {
					if ($lead->id == $assignment->lead_id) {
						$TotalAssigntoAquisitionLeads++;
					}
				}
			}

		  return $TotalAssigntoAquisitionLeads;
	}

	public function CalculateTotalUnderContractLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 12)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalClosedWonLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 21)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalDealLostLeadsByUser($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 22)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	/* Disposition and Acquisition Leads Count - Start */
	public function CalculateTotalLeadsByAquisitionDisposition($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalSendToInvestor($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 13)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalNegotiatingWithInvestor($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 14)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalSentContractToInvestor($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 16)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalSentToTitle($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 15)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalEMDReceived($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 17)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalEMDNotReceived($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 18)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalInspection($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 24)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalCloseOn($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 25)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalDispositionClosedWON($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 21)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalDispositionDealLost($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 22)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalNotAccepted($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 8)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalAccepted($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 9)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalNegotiating($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 10)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalAgreementSent($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 11)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateTotalAgreementReceived($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('leads.lead_status', '=', 12)
					->where(function ($query) use ($UserId) {
							$query->orWhere('leads.user_id', '=', $UserId);
							$query->orWhere('lead_assignments.user_id', '=', $UserId);
					})
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}
	/* Disposition and Acquisition Leads Count - End */

	/* Realtor Leads Count - Start */
	public function CalculateIncomingTotalLeads($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->join('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('lead_assignments.user_id', '=', $UserId)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateOutgoingTotalLeads($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateInTotalUnderContract($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 12)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateOutTotalUnderContract($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->join('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('lead_assignments.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 12)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateInClosedWON($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 21)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateInDealLost($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->where('leads.deleted_at', '=', null)
					->where('leads.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 22)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateOutClosedWON($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->join('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('lead_assignments.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 21)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}

	public function CalculateOutDealLost($UserId, $StartDate, $EndDate)
	{
			$total_leads = DB::table('leads')
					->join('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
					->where('leads.deleted_at', '=', null)
					->where('lead_assignments.user_id', '=', $UserId)
					->where('leads.lead_status', '=', 22)
					->where(function ($query) use ($StartDate, $EndDate) {
							if ($StartDate != "" && $EndDate != "") {
									$query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
							}
					})
					->count();

		  return $total_leads;
	}
	/* Realtor Leads Count - End */
}
