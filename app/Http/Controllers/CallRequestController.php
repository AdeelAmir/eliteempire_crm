<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Lead;
use App\HistoryNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CallRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function RepresentativeAllCallRequests()
    {
        $page = "call_request";
        $Role = Session::get('user_role');
        return view('admin.callrequest.callrequests', compact('page', 'Role'));
    }

    public function RepresentativeAddNewCallRequest()
    {
        $page = "call_request";
        $Role = Session::get('user_role');
        $user_id = Auth::id();

        // Get All Products
        $products = DB::table('products')
            ->where('deleted_at', '=', null)
            ->get();

        $SplitOptions = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->whereIn('users.role_id', array(4, 5))
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '<>', $user_id)
            ->select('users.*', 'profiles.firstname', 'profiles.lastname')
            ->get();

        $TeamId = 0;
        if ($Role == 5) {
            // Finding Team Id (Only for representatives)
            $TeamsSql = "SELECT * FROM teams WHERE (FIND_IN_SET(:userId, members) > 0) AND ISNULL(deleted_at);";
            $Team = DB::select(DB::raw($TeamsSql), array($user_id));
            $TeamId = 0;
            foreach ($Team as $item) {
                $TeamId = $item->id;
            }
        } else {
            $TeamId = 0;
        }

        return view('admin.callrequest.add-new-callrequest', compact('page', 'TeamId', 'SplitOptions', 'products', 'Role'));
    }

    public function RepresentativeCallRequestStore(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadType = 2;
        $user_id = Auth::id();
        $LeadNumber = rand(1000000, 9999999);
        $team_id = $request['team'];
        $FirstName = $request['firstName'];
        $LastName = $request['lastName'];
        $Phone = $request['phone'];
        $AppointmentTime = $request['appointmenttime'];
        $Split = $request['split'];
        $Note = $request['note'];
        $IsDuplicated = 0;
        $CurrentDate = date('Y-m-d');
        $Product = $request['product'];
        $ElectricityBill = $request['electricbill'];
        $ProductDescription = null;
        $_FileName = "";
        $WindowsDoorsCount = null;
        $OldRoof = null;

        // if ($Product == 6) {
        //     $ProductDescription = $request['product_desc'];
        // }
        if ($Product == 1) {
            $WindowsDoorsCount = $request['windows_doors_count'];
        }
        elseif ($Product == 2) {
            $OldRoof = $request['old_roof_duration'];
        }
        elseif ($Product == 6) {
            $ProductDescription = $request['product_desc'];
        }

        if ($request->hasFile('electricbill')) {
            $FileStoragePath = '/public/leads/';
            $Extension = $request->file('electricbill')->extension();
            $file = $request->file('electricbill')->getClientOriginalName();
            $FileName = pathinfo($file, PATHINFO_FILENAME);
            $OnlyFileName = $FileName;
            $FileName = $FileName . '-' . date('Y-m-d') . rand(100, 1000) . '.' . $Extension;
            $result = $request->file('electricbill')->storeAs($FileStoragePath, $FileName);
            $_FileName = $FileName;
        }

        // Checking for duplicated call requests
        $Check = DB::table('leads')
            ->whereIn('lead_type', array(1, 2, 3))
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($Phone) {
                if ($Phone != "") {
                    $query->orWhere('leads.phone', '=', $Phone);
                    $query->orWhere('leads.phone2', '=', $Phone);
                }
            })->get();

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $LeadNumber = $Check[0]->lead_number;
        }

        DB::beginTransaction();
        $affected = Lead::create([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'lead_number' => $LeadNumber,
            'firstname' => $FirstName,
            'lastname' => $LastName,
            'phone' => $Phone,
            'split' => $Split,
            'appointment_time' => $AppointmentTime,
            'product' => $Product,
            'product_desc' => $ProductDescription,
            'windows_doors_count' => $WindowsDoorsCount,
            'old_roof' => $OldRoof,
            'electricbill' => $_FileName,
            'note' => $Note,
            'lead_type' => $LeadType,
            'is_duplicated' => $IsDuplicated,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if ($affected) {
            DB::commit();
            if (sizeof($Check) > 0) {
                if ($Role == 1) {
                    return redirect(url('/admin/call-request/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 2) {
                    return redirect(url('/general_manager/call-request/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/call-request/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 4) {
                    return redirect(url('/supervisor/call-request/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 5) {
                    return redirect(url('/representative/call-request/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                }
            } else {
                if ($Role == 1) {
                    return redirect(url('/admin/call-request/add'))->with('message', 'Call request has been sent successfully');
                } elseif ($Role == 2) {
                    return redirect(url('/general_manager/call-request/add'))->with('message', 'Call request has been sent successfully');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/call-request/add'))->with('message', 'Call request has been sent successfully');
                } elseif ($Role == 4) {
                    return redirect(url('/supervisor/call-request/add'))->with('message', 'Call request has been sent successfully');
                } elseif ($Role == 5) {
                    return redirect(url('/representative/call-request/add'))->with('message', 'Call request has been sent successfully');
                }
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/call-request/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/general_manager/call-request/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/confirmationAgent/call-request/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 4) {
                return redirect(url('/supervisor/call-request/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 5) {
                return redirect(url('/representative/call-request/add'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function LoadRepresentativeAllCallRequests(Request $request)
    {
        $Role = Session::get('user_role');
        $user_id = Auth::id();
        if ($Role == 1 || $Role == 2 || $Role == 3) {
            $FilterLeadType = $request->post('lead_type');
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;

            if ($FilterLeadType == "")
            {
              if ($searchTerm == '') {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [2, 3])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [2, 3])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              } else {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [2, 3]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [2, 3]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              }
            }
            elseif ($FilterLeadType == 2)
            {
              if ($searchTerm == '') {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [2])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [2])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              } else {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [2]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [2]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              }
            }
            elseif ($FilterLeadType == 3)
            {
              if ($searchTerm == '') {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [3])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where('leads.deleted_at', '=', null)
                      ->whereIn('leads.lead_type', [3])
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              } else {
                  $fetch_data = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [3]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                      ->orderBy($columnName, $columnSortOrder)
                      ->offset($start)
                      ->limit($limit)
                      ->get();
                  $recordsTotal = sizeof($fetch_data);
                  $recordsFiltered = DB::table('leads')
                      ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                      ->where(function ($query) {
                          $query->where([
                              ['leads.deleted_at', '=', null],
                          ]);
                          $query->whereIn('leads.lead_type', [3]);
                      })
                      ->where(function ($query) use ($searchTerm) {
                          $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                          $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                      })
                      ->select('leads.*')
                      ->orderBy($columnName, $columnSortOrder)
                      ->count();
              }
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $sub_array = array();
                if($item->lead_type == 2){
                    /* Call Request */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    // $sub_array['lastname'] = $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    if ($item->is_duplicated == 1) {
                        $sub_array['is_duplicated'] = '<span class="badge badge-warning">Yes</span>';
                    } else {
                        $sub_array['is_duplicated'] = '<span class="badge badge-success">No</span>';
                    }
                    $sub_array['type'] = '<span class="badge badge-success">Call Request</span>';
                    if ($Role == 1) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    } elseif ($Role == 2) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('general_manager/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    } elseif ($Role == 3) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('confirmationAgent/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    }
                }
                else{
                    /* Dispo Lead */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    } else {
                        $sub_array['appointment_time'] = '';
                    }
                    if ($item->is_duplicated == 1) {
                        $sub_array['is_duplicated'] = '<span class="badge badge-warning">Yes</span>';
                    } else {
                        $sub_array['is_duplicated'] = '<span class="badge badge-success">No</span>';
                    }
                    $sub_array['type'] = '<span class="badge badge-danger">Dispo</span>';
                    if ($Role == 1) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/dispo-lead/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertDispoLeadsToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    } elseif ($Role == 2) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('general_manager/dispo-lead/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertDispoLeadsToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    } elseif ($Role == 3) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('confirmationAgent/dispo-lead/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                        $sub_array['converttolead'] = '<button class="btn btn-info mr-2" id="convert_' . $item->id . '" onclick="ConvertDispoLeadsToLead(this.id);"><i class="fas fa-exchange-alt"></i></button>';
                    }
                }
                $SrNo++;
                $data[] = $sub_array;
            }

            $json_data = array(
                "draw" => intval($request->post('draw')),
                "iTotalRecords" => $recordsTotal,
                "iTotalDisplayRecords" => $recordsFiltered,
                "aaData" => $data
            );

            echo json_encode($json_data);
        } elseif ($Role == 4) {
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];
            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_type', [2, 3])
                    ->where('leads.user_id', '=', $user_id)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_type', [2, 3])
                    ->where('leads.user_id', '=', $user_id)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.user_id', '=', $user_id)
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                        $query->whereIn('leads.lead_type', [2, 3]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.user_id', '=', $user_id)
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                        $query->whereIn('leads.lead_type', [2, 3]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $sub_array = array();
                if($item->lead_type == 2){
                    /* Call Request */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    if ($Role == 1) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 2) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('general_manager/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 3) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('confirmationAgent/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 4) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('supervisor/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 5) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('representative/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    }
                }
                else{
                    /* Dispo Lead */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    } else {
                        $sub_array['appointment_time'] = '';
                    }
                    $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('supervisor/dispo-lead/edit' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                }
                $SrNo++;
                $data[] = $sub_array;
            }

            $json_data = array(
                "draw" => intval($request->post('draw')),
                "iTotalRecords" => $recordsTotal,
                "iTotalDisplayRecords" => $recordsFiltered,
                "aaData" => $data
            );

            echo json_encode($json_data);
        } elseif ($Role == 5) {
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_type', [2, 3])
                    ->where('leads.user_id', '=', $user_id)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_type', [2, 3])
                    ->where('leads.user_id', '=', $user_id)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.user_id', '=', $user_id)
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                        $query->whereIn('leads.lead_type', [2, 3]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.user_id', '=', $user_id)
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                        $query->whereIn('leads.lead_type', [2, 3]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $sub_array = array();
                if($item->lead_type == 2) {
                    /* Call Request */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    if ($Role == 1) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 2) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('general_manager/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 3) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('confirmationAgent/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 4) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('supervisor/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    } elseif ($Role == 5) {
                        $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('representative/call-request/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                    }
                }
                else{
                    /* Dispo Lead */
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_id'] = $item->user_first . ' ' . $item->user_last;
                    $sub_array['lead_number'] = $item->lead_number;
                    $sub_array['name'] = $item->firstname . " " . $item->lastname;
                    $sub_array['phone'] = $item->phone;
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a');
                    } else {
                        $sub_array['appointment_time'] = '';
                    }
                    $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('representative/dispo-lead/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
                }
                $SrNo++;
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

    public function RepresentativeEditLead($Id)
    {
        $Role = Session::get('user_role');
        $page = "call_request";
        $user_id = Auth::id();

        $Lead = DB::table('leads')
            ->where('id', '=', $Id)
            ->get();

        // Get All Products
        $products = DB::table('products')
            ->where('deleted_at', '=', null)
            ->get();

        $SplitOptions = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->whereIn('users.role_id', array(4, 5))
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '<>', $user_id)
            ->select('users.*', 'profiles.firstname', 'profiles.lastname')
            ->get();

        $AppointmentTime = Carbon::parse($Lead[0]->appointment_time)->format('m/d/Y - g:i a');
        return view('admin.callrequest.edit-callrequest', compact('page', 'Lead', 'SplitOptions', 'products', 'Role', 'AppointmentTime'));
    }

    public function RepresentativeUpdateLead(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadId = $request['id'];
        $FirstName = $request['firstName'];
        $LastName = $request['lastName'];
        $Phone = $request['phone'];
        $AppointmentTime = $request['appointmenttime'];
        $Split = $request['split'];
        $Note = $request['note'];
        $IsDuplicated = 0;
        $LeadNumber = "";
        $ProductDescription = null;
        $Electricbill = $request['electricbill_Old'];
        $Product = $request['product'];
        $ProductDescription = null;
        $WindowsDoorsCount = null;
        $OldRoof = null;

        // if ($Product == 6) {
        //     $ProductDescription = $request['product_desc'];
        // }
        if ($Product == 1) {
            $WindowsDoorsCount = $request['windows_doors_count'];
        }
        elseif ($Product == 2) {
            $OldRoof = $request['old_roof_duration'];
        }
        elseif ($Product == 6) {
            $ProductDescription = $request['product_desc'];
        }

        if ($request->hasFile('electricbill')) {
            if ($Electricbill != "") {
                unlink(base_path() . '/public/storage/leads/' . $Electricbill);
            }
            $FileStoragePath = '/public/leads/';
            $Extension = $request->file('electricbill')->extension();
            $file = $request->file('electricbill')->getClientOriginalName();
            $FileName = pathinfo($file, PATHINFO_FILENAME);
            $OnlyFileName = $FileName;
            $FileName = $FileName . '-' . date('Y-m-d') . rand(100, 1000) . '.' . $Extension;
            $result = $request->file('electricbill')->storeAs($FileStoragePath, $FileName);
            $Electricbill = $FileName;
        }

        // Checking for duplicated call requests
        $Check = DB::table('leads')
            ->whereIn('lead_type', array(1, 2, 3))
            ->where('deleted_at', '=', null)
            ->where('id', '<>', $LeadId)
            ->where(function ($query) use ($Phone) {
                if ($Phone != "") {
                    $query->orWhere('leads.phone', '=', $Phone);
                    $query->orWhere('leads.phone2', '=', $Phone);
                }
            })->get();

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $LeadNumber = $Check[0]->lead_number;
        }

        // Updating Record
        DB::beginTransaction();
        if ($IsDuplicated == 1) {
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_number' => $LeadNumber,
                    'firstname' => $FirstName,
                    'lastname' => $LastName,
                    'phone' => $Phone,
                    'appointment_time' => $AppointmentTime,
                    'product' => $Product,
                    'product_desc' => $ProductDescription,
                    'windows_doors_count' => $WindowsDoorsCount,
                    'old_roof' => $OldRoof,
                    'electricbill' => $Electricbill,
                    'split' => $Split,
                    'note' => $Note,
                    'is_duplicated' => $IsDuplicated,
                    'updated_at' => Carbon::now()
                ]);
        } else {
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'firstname' => $FirstName,
                    'lastname' => $LastName,
                    'phone' => $Phone,
                    'appointment_time' => $AppointmentTime,
                    'product' => $Product,
                    'product_desc' => $ProductDescription,
                    'windows_doors_count' => $WindowsDoorsCount,
                    'old_roof' => $OldRoof,
                    'electricbill' => $Electricbill,
                    'split' => $Split,
                    'note' => $Note,
                    'is_duplicated' => $IsDuplicated,
                    'updated_at' => Carbon::now()
                ]);
        }

        if ($Affected) {
            DB::commit();
            if (sizeof($Check) > 0) {
                if ($Role == 1) {
                    return redirect(url('/admin/call-requests'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 2) {
                    return redirect(url('/general_manager/call-requests'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/call-requests'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 4) {
                    return redirect(url('/supervisor/call-requests'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 5) {
                    return redirect(url('/representative/call-requests'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                }
            } else {
                if ($Role == 1) {
                    return redirect(url('/admin/call-requests'))->with('message', 'Call Request has been updated successfully');
                } elseif ($Role == 2) {
                    return redirect(url('/general_manager/call-requests'))->with('message', 'Call Request has been updated successfully');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/call-requests'))->with('message', 'Call Request has been updated successfully');
                } elseif ($Role == 4) {
                    return redirect(url('/supervisor/call-requests'))->with('message', 'Call Request has been updated successfully');
                } elseif ($Role == 5) {
                    return redirect(url('/representative/call-requests'))->with('message', 'Call Request has been updated successfully');
                }
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/general_manager/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/confirmationAgent/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 4) {
                return redirect(url('/supervisor/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 5) {
                return redirect(url('/representative/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function AdminConvertCallRequest(Request $request)
    {
        $Role = Session::get('user_role');
        $CallRequestId = $request['convertCallRequestId'];
        $CurrentDate = date('Y-m-d');
        $HistoryNote = "Call request is converted into full lead";

        DB::beginTransaction();
        $Affected = DB::table('leads')
            ->where('id', '=', $CallRequestId)
            ->update([
                'lead_type' => 1,
                'updated_at' => Carbon::now()
            ]);

        $Affected1 = HistoryNote::create([
            'user_id' => Auth::id(),
            'lead_id' => $CallRequestId,
            'history_note' => $HistoryNote,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($Affected && $Affected1) {
            DB::commit();
            if ($Role == 1) {
                return redirect(url('/admin/call-requests'))->with('message', 'Call Request has been converted to lead successfully');
            } elseif ($Role == 2) {
                return redirect(url('/general_manager/call-requests'))->with('message', 'Call Request has been converted to lead successfully');
            } elseif ($Role == 3) {
                return redirect(url('/confirmationAgent/call-requests'))->with('message', 'Call Request has been converted to lead successfully');
            } elseif ($Role == 4) {
                return redirect(url('/supervisor/call-requests'))->with('message', 'Call Request has been converted to lead successfully');
            } elseif ($Role == 5) {
                return redirect(url('/representative/call-requests'))->with('message', 'Call Request has been converted to lead successfully');
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/general_manager/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/confirmationAgent/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 4) {
                return redirect(url('/supervisor/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 5) {
                return redirect(url('/representative/call-requests'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function HistoryCallNoteStore(Request $request)
    {
        $LeadId = $request['LeadId'];
        $HistoryNote = $request['HistoryNote'];
        $user_id = Auth::id();

        DB::beginTransaction();
        $affected = HistoryNote::create([
            'user_id' => $user_id,
            'lead_id' => $LeadId,
            'history_note' => $HistoryNote,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($affected) {
            DB::commit();
            echo "Success";
        } else {
            DB::rollback();
            echo "Error";
        }
    }

    public function LoadCallHistoryNote(Request $request)
    {
        $LeadId = $request['LeadId'];
        $user_id = Auth::id();
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('history_notes.history_note', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('history_notes.created_at', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('history_notes.history_note', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('history_notes.created_at', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['user_id'] = '<span>' . $item->firstname . " " . $item->lastname . '<br><br>' . Carbon::parse($item->created_at)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->created_at)->format('g:i A');
            $sub_array['history_note'] = '<span>' . wordwrap($item->history_note, 20, "<br>") . '</span>'; 
            $SrNo++;
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
