<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TrainingCoverageLinksController extends Controller
{
    function index(){
        $page = "trainingLink";
        $Role = Session::get('user_role');
        return view('admin.TrainingLink.index', compact('page', 'Role'));
    }

    function load(Request $request){
        $Role = Session::get('user_role');
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
            $fetch_data = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_coverage_links.training_link', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_coverage_links.training_link', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['training_link'] = Str::limit($item->training_link, 60);
            $sub_array['updated_at'] = Carbon::parse($item->updated_at)->format('d-m-Y');
            if($Role == 1){
                $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/training-link/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
            }
            else{
                $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('manager/training-link/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
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

    function edit($Id){
        $page = "trainingLink";
        $TrainingLink = DB::table('training_coverage_links')
            ->where('id', '=', $Id)
            ->get();
        $Role = Session::get('user_role');
        return view('admin.TrainingLink.edit', compact('page', 'Role', 'TrainingLink', 'Id'));
    }

    function update(Request $request){
        $Role = Session::get('user_role');
        $Id = $request->post('id');
        $Link = $request->post('link');
        $Affected = DB::table('training_coverage_links')
            ->where('id', '=', $Id)
            ->update([
                'training_link' => $Link,
                'updated_at' => Carbon::now()
            ]);

        if ($Affected) {
            Session::flash('message', 'Training Link updated successfully!');
            DB::commit();
            if($Role == 1){
                return redirect()->route('admin-training-link');
            }
            elseif($Role == 2){
                return redirect()->route('manager-training-link');
            }
        } else {
            Session::flash('error', 'An unhandled exception occurred!');
            DB::rollBack();
            if($Role == 1){
                return redirect()->route('admin-training-link');
            }
            elseif($Role == 2){
                return redirect()->route('manager-training-link');
            }
        }
    }

    // Coverage File Routes
    function index_covergae(){
        $page = "coverageFile";
        $Role = Session::get('user_role');
        return view('admin.CoverageFile.index', compact('page', 'Role'));
    }

    function load_covergae(Request $request){
        $Role = Session::get('user_role');
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
            $fetch_data = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_coverage_links.training_link', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_coverage_links')
                ->where('training_coverage_links.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_coverage_links.training_link', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_coverage_links.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['coverage_file'] = Str::limit($item->coverage_file, 60);
            $sub_array['updated_at'] = Carbon::parse($item->updated_at)->format('d-m-Y');
            if($Role == 1){
                $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('admin/coverage-file/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
            }
            else{
                $sub_array['action'] = '<button class="btn btn-info mr-2" onclick="window.location.href=\'' . url('manager/coverage-file/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button>';
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

    function edit_covergae($Id){
        $page = "coverageFile";
        $TrainingLink = DB::table('training_coverage_links')
            ->where('id', '=', $Id)
            ->get();
        $Role = Session::get('user_role');
        return view('admin.CoverageFile.edit', compact('page', 'Role', 'TrainingLink', 'Id'));
    }

    function update_covergae(Request $request){
        $Role = Session::get('user_role');
        $Id = $request->post('id');
        $Link = $request->post('link');
        $Affected = DB::table('training_coverage_links')
            ->where('id', '=', $Id)
            ->update([
                'coverage_file' => $Link,
                'updated_at' => Carbon::now()
            ]);

        if ($Affected) {
            Session::flash('message', 'Coverage Link updated successfully!');
            DB::commit();
            if($Role == 1){
                return redirect()->route('admin-coverage-file');
            }
            elseif($Role == 2){
                return redirect()->route('manager-coverage-file');
            }
        } else {
            Session::flash('error', 'An unhandled exception occurred!');
            DB::rollBack();
            if($Role == 1){
                return redirect()->route('admin-coverage-file');
            }
            elseif($Role == 2){
                return redirect()->route('manager-coverage-file');
            }
        }
    }
}
