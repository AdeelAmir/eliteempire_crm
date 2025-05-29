<?php

namespace App\Http\Controllers;

use App\Faqs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FaqsController extends Controller
{
    function index(){
        $page = "faq";
        $Role = Session::get('user_role');
        return view('admin.training-room.faq', compact('page', 'Role'));
    }

    public function load(Request $request)
    {
        $Role = Session::get('user_role');
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
            $fetch_data = DB::table('faqs')
                ->where('faqs.deleted_at', '=', null)
                ->select('faqs.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('faqs')
                ->where('faqs.deleted_at', '=', null)
                ->select('faqs.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('faqs')
                ->where('faqs.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('faqs.question', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('faqs.answer', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('faqs.*')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('faqs')
                ->where('faqs.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('faqs.question', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('faqs.answer', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('faqs.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox allFaqCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualFaqCheckbox();" />';
            $sub_array['id'] = $SrNo;
            $sub_array['question'] = '<span id="questionText_' . $item->id . '" data-text="' . $item->question . '">' . wordwrap($item->question, 70, '<br>') . '</span>';
            // $sub_array['answer'] = '<span id="answerText_' . $item->id . '" data-text="' . $item->answer . '">' . substr($item->answer, 0, 70) . '...' . '</span>';
            $sub_array['action'] = '<button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="EditFaq(this.id);"><i class="fas fa-edit"></i></button>';
            $sub_array['answer'] = '<span id="answerText_' . $item->id . '">' . substr($item->answer, 0, 70) . '...' . '</span>';
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

    public function getFaqDetails(Request $request)
    {
        $id = $request['Id'];
        $Details = DB::table('faqs')->where('id', $id)->get();
        return json_encode($Details);
    }

    public function store(Request $request)
    {
        $Question = $request['question'];
        $Answer = $request['answer'];

        DB::beginTransaction();
        $affected = Faqs::create([
            'question' => $Question,
            'answer' => $Answer,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($affected) {
            DB::commit();
            return redirect(url('admin/training-room/faqs'))->with('message', 'Question has been added successfully');
        } else {
            DB::rollback();
            return redirect(url('admin/training-room/faqs'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function delete(Request $request)
    {
        // $user_id = $request['id'];
        $Faqs = $request['checkAllBox'];
        DB::beginTransaction();
        $affected = null;
        foreach ($Faqs as $key => $id) {
          $affected = DB::table('faqs')
              ->where('id', $id)
              ->update([
                  'updated_at' => Carbon::now(),
                  'deleted_at' => Carbon::now(),
              ]);
        }
        if ($affected) {
            DB::commit();
            return redirect(url('admin/training-room/faqs'))->with('message', 'Faq has been deleted successfully');
        } else {
            DB::rollback();
            return redirect(url('admin/training-room/faqs'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function update(Request $request)
    {
        $Id = $request['id'];
        $Question = $request['question'];
        $Answer = $request['answer'];

        DB::beginTransaction();
        $affected = DB::table('faqs')
            ->where('id', $Id)
            ->update([
                'question' => $Question,
                'answer' => $Answer,
                'updated_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            return redirect(url('admin/training-room/faqs'))->with('message', 'Faq has been updated successfully');
        } else {
            DB::rollback();
            return redirect(url('admin/training-room/faqs'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    function Search(Request $request){
        $Text = $request->post('Text');
        $Words = explode(" ", $Text);
        $FinalWords = array();
        foreach ($Words as $word){
            if($word != ""){
                $FinalWords[] = $word;
            }
        }
        $Faqs = DB::table('faqs')
            ->where('faqs.deleted_at', '=', null)
            ->where(function ($query) use ($FinalWords) {
                foreach ($FinalWords as $finalWord){
                    $query->orWhere('faqs.question', 'LIKE', '%' . $finalWord . '%');
                    $query->orWhere('faqs.answer', 'LIKE', '%' . $finalWord . '%');
                }
            })
            ->select('faqs.*')
            ->orderBy('id', 'desc')
            ->get();

        echo json_encode($Faqs);
        exit();
    }
}
