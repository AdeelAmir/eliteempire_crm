<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use Illuminate\Http\Request;
use App\TrainingRoom;
use App\TrainingQuiz;
use App\Folder;
use App\Notification;
use App\TrainingAssignment;
use App\TrainingAssignmentFolder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TrainingRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        return view('admin.training-room.index', compact('page', 'Role'));
    }

    public function OpenTrainingRoomFolders($RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRole = DB::table('roles')
            ->where('id', '=', $RoleId)
            ->get()[0]->title;
        return view('admin.training-room.folders', compact('page', 'Role', 'RoleId', 'TrainingRoomRole'));
    }

    public function OpenTrainingRoomDetails($FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRole = DB::table('roles')
            ->where('id', '=', $RoleId)
            ->get()[0]->title;
        return view('admin.training-room.details', compact('page', 'FolderId', 'Role', 'RoleId', 'TrainingRoomRole'));
    }

    public function LoadAllTrainingRoom(Request $request)
    {
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $request->post('TrainingRoomRoleId');
        $TrainingRoomFolderId = $request->post('TrainingRoomFolderId');
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
            $fetch_data = DB::table('training_rooms')
                ->where('training_rooms.role_id', '=', $TrainingRoomRoleId)
                ->where('training_rooms.folder_id', '=', $TrainingRoomFolderId)
                ->where('training_rooms.deleted_at', '=', null)
                ->select('training_rooms.*')
                ->orderBy('order_no', 'ASC')
                ->offset($start)
                ->limit($limit)
                ->get();

            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_rooms')
                ->where('training_rooms.role_id', '=', $TrainingRoomRoleId)
                ->where('training_rooms.folder_id', '=', $TrainingRoomFolderId)
                ->where('training_rooms.deleted_at', '=', null)
                ->select('training_rooms.*')
                ->orderBy('order_no', 'ASC')
                ->count();
        } else {
            $fetch_data = DB::table('training_rooms')
                ->where('training_rooms.role_id', '=', $TrainingRoomRoleId)
                ->where('training_rooms.folder_id', '=', $TrainingRoomFolderId)
                ->where('training_rooms.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_rooms.type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('training_rooms.title', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_rooms.*')
                ->orderBy('order_no', 'ASC')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('training_rooms')
                ->where('training_rooms.role_id', '=', $TrainingRoomRoleId)
                ->where('training_rooms.folder_id', '=', $TrainingRoomFolderId)
                ->where('training_rooms.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('training_rooms.type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('training_rooms.title', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('training_rooms.*')
                ->orderBy('order_no', 'ASC')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['type'] = ucwords($item->type);
            $sub_array['title'] = '<span>' . wordwrap($item->title, 40, '<br>') . '</span>';
            $Url = url('admin/training-room/order/up') . '/' . $item->id . '/' . $item->folder_id . '/' . $item->role_id;
            $Url2 = url('admin/training-room/order/down') . '/' . $item->id . '/' . $item->folder_id . '/' . $item->role_id;
            if ($item->type == "video") {
                $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/training-room/video/edit/' . $item->id . '/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId) . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteTrainingRoom(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></button><button class="btn greenActionButtonTheme mr-2" id="copy_' . $item->id . '" onclick="copyTrainingRoomItem(this.id);" data-toggle="tooltip" title="Copy"><i class="fas fa-copy"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderUp_' . $item->id . '" onclick="window.location.href=\'' . $Url . '\';" data-toggle="tooltip" title="Order Up"><i class="fas fa-arrow-up"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderDown_' . $item->id . '" onclick="window.location.href=\'' . $Url2 . '\';" data-toggle="tooltip" title="Order Down"><i class="fas fa-arrow-down"></i></button>';
            } elseif ($item->type == "article") {
                $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/training-room/article/edit/' . $item->id . '/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId) . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteTrainingRoom(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></button><button class="btn greenActionButtonTheme mr-2" id="copy_' . $item->id . '" onclick="copyTrainingRoomItem(this.id);" data-toggle="tooltip" title="Copy"><i class="fas fa-copy"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderUp_' . $item->id . '" onclick="window.location.href=\'' . $Url . '\';" data-toggle="tooltip" title="Order Up"><i class="fas fa-arrow-up"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderDown_' . $item->id . '" onclick="window.location.href=\'' . $Url2 . '\';" data-toggle="tooltip" title="Order Down"><i class="fas fa-arrow-down"></i></button>';
            } elseif ($item->type == "quiz") {
                $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/training-room/quiz/edit/' . $item->id . '/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId) . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteTrainingRoom(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></button><button class="btn greenActionButtonTheme mr-2" id="copy_' . $item->id . '" onclick="copyTrainingRoomItem(this.id);" data-toggle="tooltip" title="Copy"><i class="fas fa-copy"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderUp_' . $item->id . '" onclick="window.location.href=\'' . $Url . '\';" data-toggle="tooltip" title="Order Up"><i class="fas fa-arrow-up"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderDown_' . $item->id . '" onclick="window.location.href=\'' . $Url2 . '\';" data-toggle="tooltip" title="Order Down"><i class="fas fa-arrow-down"></i></button>';
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

    // LOAD TRAINING ROOM FOLDERS
    public function LoadAllTrainingRoomFolders(Request $request)
    {
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $request->post('TrainingRoomRoleId');
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
            $fetch_data = DB::table('folders')
                ->where('folders.role_id', '=', $TrainingRoomRoleId)
                ->where('folders.deleted_at', '=', null)
                ->select('folders.*')
                ->orderBy('order_no', 'ASC')
                ->offset($start)
                ->limit($limit)
                ->get();

            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('folders')
                ->where('folders.role_id', '=', $TrainingRoomRoleId)
                ->where('folders.deleted_at', '=', null)
                ->select('folders.*')
                ->orderBy('order_no', 'ASC')
                ->count();
        } else {
            $fetch_data = DB::table('folders')
                ->where('folders.role_id', '=', $TrainingRoomRoleId)
                ->where('folders.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('folders.name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('folders.*')
                ->orderBy('order_no', 'ASC')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('folders')
                ->where('folders.role_id', '=', $TrainingRoomRoleId)
                ->where('folders.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('folders.name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('folders.*')
                ->orderBy('order_no', 'ASC')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = '<span>' . wordwrap($item->name, 50, '<br>') . '</span>';
            $sub_array['picture'] = '<span><img src="'. asset('public/storage/folders/' . $item->picture) .'" class="img-fluid" style="width: 50px;height: 50px;" /></span>';
            if ($item->required == 0) {
              $sub_array['required'] = '<span><span class="badge badge-danger">Not Required</span></span>';
            } elseif ($item->required == 1) {
              $sub_array['required'] = '<span><span class="badge badge-success">Required</span></span>';
            }
            $Url = url('admin/training-room/folder/order/up') . '/' . $item->id . '/' . $item->role_id;
            $Url2 = url('admin/training-room/folder/order/down') . '/' . $item->id . '/' . $item->role_id;
            $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/training-room/folder/details/' . $item->id . '/' . $TrainingRoomRoleId) . '\'" data-toggle="tooltip" title="Open Folder"><i class="fas fa-eye"></i></button><button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/training-room/folder/edit/' . $item->id . '/' . $TrainingRoomRoleId) . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteTrainingRoomFolder(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></button><button class="btn greenActionButtonTheme mr-2" id="copy_' . $item->id . '" onclick="copyTrainingRoomFolder(this.id);" data-toggle="tooltip" title="Copy"><i class="fas fa-copy"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderUp_' . $item->id . '" onclick="window.location.href=\'' . $Url . '\';" data-toggle="tooltip" title="Order Up"><i class="fas fa-arrow-up"></i></button><button class="btn greenActionButtonTheme mr-2" id="orderDown_' . $item->id . '" onclick="window.location.href=\'' . $Url2 . '\';" data-toggle="tooltip" title="Order Down"><i class="fas fa-arrow-down"></i></button>';
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

    // TRAINING ROOM FOLDERS SECTION - START
    public function AddTrainingRoomFolder($RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $RoleId;
        return view('admin.training-room.add-new-folder', compact('page', 'Role', 'RoleId', 'TrainingRoomRoleId'));
    }

    public function StoreTrainingRoomFolder(Request $request)
    {
        $UserRole = Session::get('user_role');
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $Name = $request['name'];
        $Picture = $request['picture'];
        $Required = $request['required_status'];

        //Storing folder picture
        $Filename = "Folder-" . $TrainingRoomRoleId;
        $Extension = $request->file('picture')->extension();
        $Filename = $Filename . '-' . mt_rand(1000000, 9999999) . '.' . $Extension;
        $result = $request->file('picture')->storeAs('/public/folders/', $Filename);

        DB::beginTransaction();
        $affected = Folder::create([
            'role_id' => $TrainingRoomRoleId,
            'order_no' => SiteHelper::GetNewFolderOrderNumber($TrainingRoomRoleId),
            'name' => $Name,
            'picture' => $Filename,
            'required' => $Required,
            'created_at' => Carbon::now(),
        ]);

        // get list of all the users of this role and assign this folder and send notification
        $AllUsers = DB::table('users')->where('role_id', $TrainingRoomRoleId)->get();
        foreach ($AllUsers as $key => $user) {
            $affected1 = TrainingAssignmentFolder::create([
                'user_id' => $user->id,
                'folder_id' => $affected->id,
                'completion_rate' => 0,
                'created_at' => Carbon::now(),
            ]);

            $affected2 = Notification::create([
                'sender_id' => Auth::id(),
                'reciever_id' => $user->id,
                'message' => "New training is assigned to you 🤗",
                'type' => 2,
                'read_status' => 0,
                'created_at' => Carbon::now()
            ]);
        }

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('message', 'Folder has been added into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function EditTrainingRoomFolder($FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $folder = Folder::find($FolderId);
        return view('admin.training-room.edit-folder', compact('page', 'Role', 'RoleId', 'folder'));
    }

    public function UpdateTrainingRoomFolder(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $Name = $request['name'];
        $Required = $request['required_status'];
        $oldFolderPicture = $request['oldFolderPicture'];
        $NewFolderPicture = "";

        if ($request->file('picture')) {
            //Storing new file
            $Filename = "Folder" . $TrainingRoomRoleId;
            $Extension = $request->file('picture')->extension();
            $Filename = $Filename . '-' . mt_rand(1000000, 9999999) . '.' . $Extension;
            $NewFolderPicture = $Filename;
            $result = $request->file('picture')->storeAs('/public/folders/', $Filename);
        } else {
            $NewFolderPicture = $oldFolderPicture;
        }

        DB::beginTransaction();
        $affected = DB::table('folders')
            ->where('id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->update([
                'name' => $Name,
                'picture' => $NewFolderPicture,
                'required' => $Required,
                'updated_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('message', 'Folder has been updated into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function DeleteTrainingRoomFolder(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['delete_training_room_role_id'];

        DB::beginTransaction();
        $affected = DB::table('folders')
            ->where('id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->delete();

        $affected1 = DB::table('training_rooms')
            ->where('folder_id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->delete();

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('message', 'Folder has been removed from the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    function TrainingRoomFolderOrderUp($Id, $Role){
        $Folder = DB::table('folders')
            ->where('id', '=', $Id)
            ->get();
        $PreviousRecord = DB::table('folders')
            ->where('order_no', '<', $Folder[0]->order_no)
            ->where('role_id', '=', $Role)
            ->where('deleted_at', '=', null)
            ->orderBy('order_no', 'DESC')
            ->limit(1)
            ->get();
        if(sizeof($PreviousRecord) > 0){
            DB::beginTransaction();
            DB::table('folders')
                ->where('id', '=', $Folder[0]->id)
                ->update([
                    'order_no' => $PreviousRecord[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::table('folders')
                ->where('id', '=', $PreviousRecord[0]->id)
                ->update([
                    'order_no' => $Folder[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        }
        return redirect(url('/admin/training-room/folders/' . $Role))->with('message', 'Record order changed successfully');
    }

    function TrainingRoomFolderOrderDown($Id, $Role){
        $Folder = DB::table('folders')
            ->where('id', '=', $Id)
            ->get();
        $NextRecord = DB::table('folders')
            ->where('order_no', '>', $Folder[0]->order_no)
            ->where('role_id', '=', $Role)
            ->where('deleted_at', '=', null)
            ->orderBy('order_no', 'ASC')
            ->limit(1)
            ->get();
        if(sizeof($NextRecord) > 0){
            DB::beginTransaction();
            DB::table('folders')
                ->where('id', '=', $Folder[0]->id)
                ->update([
                    'order_no' => $NextRecord[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::table('folders')
                ->where('id', '=', $NextRecord[0]->id)
                ->update([
                    'order_no' => $Folder[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        }
        return redirect(url('/admin/training-room/folders/' . $Role))->with('message', 'Record order changed successfully');
    }

    function CopyTrainingRoomFolder(Request $request)
    {
        $TrainingRoomRoleId = $request['copy_training_room_role_id'];
        $TrainingRoomFolderId = $request['id'];
        $Role = $request['role'];

        // Create new training room folder for user
        $Folder = DB::table('folders')
            ->where('id', $TrainingRoomFolderId)
            ->where('deleted_at', '=', null)
            ->get();

        $TrainingRoom = DB::table('training_rooms')
            ->where('folder_id', $TrainingRoomFolderId)
            ->where('deleted_at', '=', null)
            ->get();

        // Create folder
        DB::beginTransaction();
        $affected = Folder::create([
            'role_id' => $Role,
            'order_no' => SiteHelper::GetNewFolderOrderNumber($TrainingRoomRoleId),
            'name' => $Folder[0]->name,
            'picture' => $Folder[0]->picture,
            'required' => $Folder[0]->required,
            'created_at' => Carbon::now(),
        ]);

        foreach ($TrainingRoom as $room) {
          if ($room->type == "quiz") {
              $Affected = TrainingRoom::create([
                  'role_id' => $Role,
                  'folder_id' => $affected->id,
                  'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
                  'type' => $room->type,
                  'title' => $room->title,
                  'video_url' => $room->video_url,
                  'article_details' => $room->article_details,
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now()
              ]);

              // Get Quiz Options
              $TrainingQuizzez = DB::table('training_quizzes')
                  ->where('topic_id', $room->id)
                  ->where('deleted_at', '=', null)
                  ->get();

              foreach ($TrainingQuizzez as $quiz) {
                  $Affected1 = TrainingQuiz::create([
                      'topic_id' => $Affected->id,
                      'question' => $quiz->question,
                      'choice1' => $quiz->choice1,
                      'choice2' => $quiz->choice2,
                      'choice3' => $quiz->choice3,
                      'choice4' => $quiz->choice4,
                      'answer' => $quiz->answer,
                      'created_at' => Carbon::now(),
                      'updated_at' => Carbon::now()
                  ]);
              }
          } else {
              $Affected = TrainingRoom::create([
                  'role_id' => $Role,
                  'folder_id' => $affected->id,
                  'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
                  'type' => $room->type,
                  'title' => $room->title,
                  'video_url' => $room->video_url,
                  'article_details' => $room->article_details,
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now()
              ]);
          }
        }

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('message', 'Folder has been copied from the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folders/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }
    // TRAINING ROOM FOLDERS SECTION - END

    /* Training Videos Section - Start */
    public function AddTrainingRoomVideo($FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $RoleId;
        return view('admin.training-room.add-new-video', compact('page', 'FolderId', 'Role', 'RoleId', 'TrainingRoomRoleId'));
    }

    public function TrainingRoomVideoStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        $Link = $request['link'];
        $Type = "video";

        DB::beginTransaction();
        $affected = TrainingRoom::create([
            'role_id' => $TrainingRoomRoleId,
            'folder_id' => $TrainingRoomFolderId,
            'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
            'type' => $Type,
            'title' => $Title,
            'video_url' => $Link,
            'created_at' => Carbon::now(),
        ]);

        // get list of all the users of this role and add new item in the training assignment
        $AllUsers = DB::table('users')->where('role_id', $TrainingRoomRoleId)->get();
        foreach ($AllUsers as $key => $user) {
            $TrainingAssignmentFolderDetails = DB::table('training_assignment_folders')
                                               ->where('user_id', $user->id)
                                               ->where('folder_id', $TrainingRoomFolderId)
                                               ->get();

            $affected1 = TrainingAssignment::create([
                'user_id' => $user->id,
                'assignment_type' => "video",
                'training_assignment_folder_id' => $TrainingAssignmentFolderDetails[0]->id,
                'assignment_id' => $affected->id,
                'created_at' => Carbon::now()
            ]);
        }

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Video has been added into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function EditTrainingRoomVideo($VideoId, $FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $video = TrainingRoom::find($VideoId);
        return view('admin.training-room.edit-video', compact('page', 'FolderId', 'Role', 'RoleId', 'video'));
    }

    public function TrainingRoomVideoUpdate(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        $Link = $request['link'];

        DB::beginTransaction();
        $affected = DB::table('training_rooms')
            ->where('id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->where('folder_id', $TrainingRoomFolderId)
            ->update([
                'title' => $Title,
                'video_url' => $Link,
                'updated_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Video has been updated into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }
    /* Training Videos Section - End */

    /* Training Articles Section - Start */
    public function AddTrainingRoomArticle($FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $RoleId;
        return view('admin.training-room.add-new-article', compact('page', 'FolderId', 'Role', 'RoleId', 'TrainingRoomRoleId'));
    }

    public function TrainingRoomArticleStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        $ArticleDetails = $request['add_article_details'];
        $Type = "article";

        DB::beginTransaction();
        $affected = TrainingRoom::create([
            'role_id' => $TrainingRoomRoleId,
            'folder_id' => $TrainingRoomFolderId,
            'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
            'type' => $Type,
            'title' => $Title,
            'article_details' => $ArticleDetails,
            'created_at' => Carbon::now(),
        ]);

        // get list of all the users of this role and add new item in the training assignment
        $AllUsers = DB::table('users')->where('role_id', $TrainingRoomRoleId)->get();
        foreach ($AllUsers as $key => $user) {
            $TrainingAssignmentFolderDetails = DB::table('training_assignment_folders')
                                               ->where('user_id', $user->id)
                                               ->where('folder_id', $TrainingRoomFolderId)
                                               ->get();

            $affected1 = TrainingAssignment::create([
                'user_id' => $user->id,
                'assignment_type' => "article",
                'training_assignment_folder_id' => $TrainingAssignmentFolderDetails[0]->id,
                'assignment_id' => $affected->id,
                'created_at' => Carbon::now()
            ]);
        }

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Article has been added into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function EditTrainingRoomArticle($ArticleId, $FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $article = TrainingRoom::find($ArticleId);
        return view('admin.training-room.edit-article', compact('page', 'FolderId', 'Role', 'RoleId', 'article'));
    }

    public function TrainingRoomArticleUpdate(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        $ArticleDetails = $request['add_article_details'];

        DB::beginTransaction();
        $affected = DB::table('training_rooms')
            ->where('id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->where('folder_id', $TrainingRoomFolderId)
            ->update([
                'title' => $Title,
                'article_details' => $ArticleDetails,
                'updated_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Article has been updated into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }
    /* Training Articles Section - End */

    /* Training Quiz Section - Start */
    public function AddTrainingRoomQuiz($FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $TrainingRoomRoleId = $RoleId;
        return view('admin.training-room.add-new-quiz', compact('page', 'FolderId', 'Role', 'RoleId', 'TrainingRoomRoleId'));
    }

    public function TrainingRoomQuizStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        $Type = "quiz";

        DB::beginTransaction();
        $Affected = TrainingRoom::create([
            'role_id' => $TrainingRoomRoleId,
            'folder_id' => $TrainingRoomFolderId,
            'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
            'type' => $Type,
            'title' => $Title,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $TopicId = $Affected->id;
        $Affected2 = null;
        foreach ($request->post('questions') as $question) {
            $Affected2 = TrainingQuiz::create([
                'topic_id' => $TopicId,
                'question' => $question['add_quiz_question'],
                'choice1' => $question['add_quiz_choice1'],
                'choice2' => $question['add_quiz_choice2'],
                'choice3' => $question['add_quiz_choice3'],
                'choice4' => $question['add_quiz_choice4'],
                'answer' => $question['add_quiz_answer'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // get list of all the users of this role and add new item in the training assignment
        $AllUsers = DB::table('users')->where('role_id', $TrainingRoomRoleId)->get();
        foreach ($AllUsers as $key => $user) {
            $TrainingAssignmentFolderDetails = DB::table('training_assignment_folders')
                                               ->where('user_id', $user->id)
                                               ->where('folder_id', $TrainingRoomFolderId)
                                               ->get();

            $affected1 = TrainingAssignment::create([
                'user_id' => $user->id,
                'assignment_type' => $Type,
                'training_assignment_folder_id' => $TrainingAssignmentFolderDetails[0]->id,
                'assignment_id' => $Affected->id,
                'created_at' => Carbon::now()
            ]);
        }

        if ($Affected && $Affected2) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Quiz has been added into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function EditTrainingRoomQuiz($QuizId, $FolderId, $RoleId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');
        $quiz = TrainingRoom::find($QuizId);
        $Questions = DB::table('training_quizzes')
            ->where('topic_id', '=', $QuizId)
            ->get();
        $Data = array();
        $Data[] = $QuizId;
        $Data[] = $quiz['title'];
        $Data[] = $RoleId;
        $Data[] = $Questions;
        return view('admin.training-room.edit-quiz', compact('page', 'FolderId', 'Role', 'RoleId', 'quiz', 'Data'));
    }

    public function TrainingRoomQuizUpdate(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['training_room_role_id'];
        $TrainingRoomFolderId = $request['training_room_folder_id'];
        $Title = $request['title'];
        DB::beginTransaction();
        $Affected = DB::table('training_rooms')
            ->where('id', '=', $Id)
            ->where('role_id', '=', $TrainingRoomRoleId)
            ->where('folder_id', '=', $TrainingRoomFolderId)
            ->update([
                'role_id' => $TrainingRoomRoleId,
                'title' => $Title,
                'updated_at' => Carbon::now()
            ]);

        $Affected2 = null;
        DB::table('training_quizzes')
            ->where('topic_id', '=', $Id)
            ->delete();
        foreach ($request->post('questions') as $question) {
            $Affected2 = TrainingQuiz::create([
                'topic_id' => $Id,
                'question' => $question['add_quiz_question'],
                'choice1' => $question['add_quiz_choice1'],
                'choice2' => $question['add_quiz_choice2'],
                'choice3' => $question['add_quiz_choice3'],
                'choice4' => $question['add_quiz_choice4'],
                'answer' => $question['add_quiz_answer'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        if ($Affected && $Affected2) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Quiz has been updated into the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    /* Training Quiz Section - End */

    public function TrainingRoomDelete(Request $request)
    {
        $Id = $request['id'];
        $TrainingRoomRoleId = $request['delete_training_room_role_id'];
        $TrainingRoomFolderId = $request['delete_training_room_folder_id'];

        DB::beginTransaction();
        $affected = DB::table('training_rooms')
            ->where('id', $Id)
            ->where('role_id', $TrainingRoomRoleId)
            ->where('folder_id', $TrainingRoomFolderId)
            ->update([
                'updated_at' => Carbon::now(),
                'deleted_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Record has been deleted from the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    function ViewFaqs()
    {
        $page = 'faq';
        $Role = Session::get('user_role');
        $Faqs = DB::table('faqs')
            ->where('deleted_at', '=', null)
            ->get();
        return view('admin.training-room.view-faq', compact('page', 'Role', 'Faqs'));
    }

    function MarkAssignmentAsComplete(Request $request)
    {
        $AssignmentId = $request->post('id');
        $CourseId = $request->post('courseid');
        $Affected = DB::table('training_assignments')
            ->where('id', '=', $AssignmentId)
            ->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);

        // Update Folder Completion Rate
        $TotalCompleted = 0;
        $TotalRecord = 0;
        $TotalAssignmentRecord = DB::table('training_assignments')
            ->where('training_assignment_folder_id', '=', $CourseId)
            ->where('user_id', '=', Auth::id())
            ->get();

        $TotalRecord = count($TotalAssignmentRecord);
        foreach ($TotalAssignmentRecord as $record) {
          if ($record->status == 1) {
            $TotalCompleted++;
          }
        }

        $TotalCompletionRate = (($TotalCompleted / $TotalRecord) * 100);
        $Affected1 = DB::table('training_assignment_folders')
            ->where('id', '=', $CourseId)
            ->update([
                'completion_rate' => $TotalCompletionRate,
                'updated_at' => Carbon::now()
            ]);

        echo json_encode(["message" => "success"]);
        exit();
    }

    function TrainingRoomCopy(Request $request)
    {
        $TrainingRoomItemId = $request['id'];
        $TrainingRoomRoleId = $request['role'];
        $TrainingRoomFolderId = $request['folder_id'];

        $Affected = null;
        $Affected1 = null;
        DB::beginTransaction();
        // Create new training room for user
        $TrainingRoom = DB::table('training_rooms')
            ->where('id', $TrainingRoomItemId)
            ->where('deleted_at', '=', null)
            ->get();

        if ($TrainingRoom[0]->type == "quiz") {
            foreach ($TrainingRoom as $room) {
                $Affected = TrainingRoom::create([
                    'role_id' => $TrainingRoomRoleId,
                    'folder_id' => $TrainingRoomFolderId,
                    'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
                    'type' => $room->type,
                    'title' => $room->title,
                    'video_url' => $room->video_url,
                    'article_details' => $room->article_details,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // Get Quiz Options
                $TrainingQuizzez = DB::table('training_quizzes')
                    ->where('topic_id', $TrainingRoomItemId)
                    ->where('deleted_at', '=', null)
                    ->get();

                foreach ($TrainingQuizzez as $quiz) {
                    $Affected1 = TrainingQuiz::create([
                        'topic_id' => $Affected->id,
                        'question' => $quiz->question,
                        'choice1' => $quiz->choice1,
                        'choice2' => $quiz->choice2,
                        'choice3' => $quiz->choice3,
                        'choice4' => $quiz->choice4,
                        'answer' => $quiz->answer,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        } else {
            foreach ($TrainingRoom as $room) {
                $Affected = TrainingRoom::create([
                    'role_id' => $TrainingRoomRoleId,
                    'folder_id' => $TrainingRoomFolderId,
                    'order_no' => SiteHelper::GetNewOrderNumber($TrainingRoomRoleId, $TrainingRoomFolderId),
                    'type' => $room->type,
                    'title' => $room->title,
                    'video_url' => $room->video_url,
                    'article_details' => $room->article_details,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        if ($Affected) {
            DB::commit();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('message', 'Record has been copy from the training room successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/training-room/folder/details/' . $TrainingRoomFolderId . '/' . $TrainingRoomRoleId))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    function TrainingRoomOrderUp($Id, $FolderId, $Role){
        $TrainingRoom = DB::table('training_rooms')
            ->where('id', '=', $Id)
            ->get();
        $PreviousRecord = DB::table('training_rooms')
            ->where('order_no', '<', $TrainingRoom[0]->order_no)
            ->where('role_id', '=', $Role)
            ->where('folder_id', '=', $FolderId)
            ->where('deleted_at', '=', null)
            ->orderBy('order_no', 'DESC')
            ->limit(1)
            ->get();
        if(sizeof($PreviousRecord) > 0){
            DB::beginTransaction();
            DB::table('training_rooms')
                ->where('id', '=', $TrainingRoom[0]->id)
                ->update([
                    'order_no' => $PreviousRecord[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::table('training_rooms')
                ->where('id', '=', $PreviousRecord[0]->id)
                ->update([
                    'order_no' => $TrainingRoom[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        }
        return redirect(url('/admin/training-room/folder/details/' . $FolderId . '/' . $Role))->with('message', 'Record order changed successfully');
    }

    function TrainingRoomOrderDown($Id, $FolderId, $Role){
        $TrainingRoom = DB::table('training_rooms')
            ->where('id', '=', $Id)
            ->get();
        $NextRecord = DB::table('training_rooms')
            ->where('order_no', '>', $TrainingRoom[0]->order_no)
            ->where('role_id', '=', $Role)
            ->where('folder_id', '=', $FolderId)
            ->where('deleted_at', '=', null)
            ->orderBy('order_no', 'ASC')
            ->limit(1)
            ->get();
        if(sizeof($NextRecord) > 0){
            DB::beginTransaction();
            DB::table('training_rooms')
                ->where('id', '=', $TrainingRoom[0]->id)
                ->update([
                    'order_no' => $NextRecord[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::table('training_rooms')
                ->where('id', '=', $NextRecord[0]->id)
                ->update([
                    'order_no' => $TrainingRoom[0]->order_no,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        }
        return redirect(url('/admin/training-room/folder/details/' . $FolderId . '/' . $Role))->with('message', 'Record order changed successfully');
    }

    public function GetTrainingRoomFolders(Request $request)
    {
        $TrainingRoomRoleId = $request['Role'];
        $Folders = DB::table('folders')
            ->where('role_id', '=', $TrainingRoomRoleId)
            ->get();

        $options = "<option value=''>Select Folder</option>";
        foreach ($Folders as $room) {
          $options .= "<option value='". $room->id ."'>". $room->name ."</option>";
        }
        return json_encode($options);
    }

    public function SearchCourse(Request $request)
    {
      $Text = $request->post('Text');
      $Words = explode(" ", $Text);
      $FinalWords = array();
      foreach ($Words as $word){
          if($word != ""){
              $FinalWords[] = $word;
          }
      }

      $AllTrainingAssignmentFolders = \Illuminate\Support\Facades\DB::table('training_assignment_folders')
          ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
          ->where('training_assignment_folders.user_id', '=', Auth::id())
          ->where(function ($query) use ($FinalWords) {
              foreach ($FinalWords as $finalWord){
                  $query->orWhere('folders.name', 'LIKE', '%' . $finalWord . '%');
              }
          })
          ->select('training_assignment_folders.*', 'folders.name AS FolderName', 'folders.picture', 'folders.required')
          ->get();

      $Courses = '';
      $Url = "";

      foreach($AllTrainingAssignmentFolders as $folder)
      {
        if (Auth::user()->role_id == 3) {
            $Url = url('acquisition_manager/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 4) {
            $Url = url('disposition_manager/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 5) {
            $Url = url('acquisition_representative/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 6) {
            $Url = url('disposition_representative/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 7) {
            $Url = url('cold_caller/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 8) {
            $Url = url('affiliate/training/course/' . $folder->id);
        } elseif (Auth::user()->role_id == 9) {
            $Url = url('realtor/training/course/' . $folder->id);
        }

        $Picture = asset('public/storage/folders/' . $folder->picture);

        $Courses .= ''.
        '<div class="col-md-4">'.
        '  <div class="card cardBackgroundColor">'.
        '      <div class="card-title">'.
        '        <img src="'. $Picture .'" alt="logo-small" class="img-fluid" style="width: 100%; height: 200px;">'.
        '      </div>'.
        '      <div class="card-body">'.
        '        <p class="text-left courseTitleSetting">'. $folder->FolderName .'</p>'.
        '        <div class="mt-1">'.
        '          <a href="'. $Url .'" class="mt-2 courseOpenLinkSetting">';
                    if($folder->completion_rate > 0){
                      $Courses . 'Resume Course';
                    }
                    else{
                      $Courses .= 'Start Course';
                    }
        $Courses .= ''.
        '          </a>'.
        '        </div>';
                if($folder->required == 1) {
                  $Courses . ''.
                  '<div class="mt-1">'.
                  '  <span class="badge badge-danger">Required</span>'.
                  '</div>';
                }
        $Courses .= ''.
        '        <div class="progress mt-3">'.
        '          <div class="progress-bar" role="progressbar" style="width: '.$folder->completion_rate.'%; " aria-valuenow="'.$folder->completion_rate.'" aria-valuemin="0" aria-valuemax="100">'. $folder->completion_rate .'%</div>'.
        '        </div>'.
        '      </div>'.
        '  </div>'.
        '</div>';
      }

      $data = array();
      $data['total_record'] = count($AllTrainingAssignmentFolders);
      $data['courses'] = json_encode($Courses);
      echo json_encode($data);
      exit();
    }
}
