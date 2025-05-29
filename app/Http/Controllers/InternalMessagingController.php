<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Notification;
use App\Profile;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Message;
use App\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class InternalMessagingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function CalculateUnreadMessage(Request $request)
    {
        $GroupId = null;
        $TotalUnreadMessage = DB::table('messages')
            ->where('messages.deleted_at', '=', null)
            ->where('messages.read_status', '=', "unread")
            ->where('messages.receiver_id', '=', Auth::id())
            ->where('messages.group_id', '=', $GroupId)
            ->count();

        $MessagesSql = "SELECT * FROM messages INNER JOIN groups ON messages.group_id = groups.id WHERE ((FIND_IN_SET(:userId, messages.read_by) < 1) OR ISNULL(messages.read_by)) AND (FIND_IN_SET(:loggedUserId, groups.members) > 0) AND messages.sender_id != :senderId AND ISNULL(messages.receiver_id) AND ISNULL(messages.deleted_at) AND ISNULL(groups.deleted_at);";
        $MessageData = DB::select(DB::raw($MessagesSql), array(Auth::id(), Auth::id(), Auth::id()));

        $Data = array();
        $Data['unread'] = $TotalUnreadMessage;
        $Data['group_unread'] = count($MessageData);

        return json_encode($Data);
    }

    public function index()
    {
        $page = "messages";
        $Role = Session::get('user_role');
        $UserId = Auth::id();
        $ChatUsers = array();
        $ContactUsers = array();
        $TeamMembers = array();
        $Groups = array();
        // Logged in user profile details
        $UserProfile = DB::table('users')
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '=', Auth::id())
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->select('users.id AS id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.profile_picture')
            ->get();

        if ($Role == 1) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }

            $ContactUsers = $Users;
            $TeamMembers = $Users;
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 2) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }

            $ContactUsers = $Users;
            $TeamMembers = $Users;
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 3) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 4) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 5) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 6) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 7) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 3 || $user->role_id == 8) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 8) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 3 || $user->role_id == 7) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        } elseif ($Role == 9) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 2 || $user->role_id == 4) {
                    array_push($ContactUsers, $user);
                    array_push($TeamMembers, $user);
                }
            }

            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }
        }

        /* Groups Record */
        $GroupsSql = "SELECT * FROM groups WHERE ((FIND_IN_SET(:userId, members) > 0)) AND ISNULL(deleted_at) ORDER BY id DESC;";
        $GroupsData = DB::select(DB::raw($GroupsSql), array(Auth::id()));
        $Groups = array();
        foreach ($GroupsData as $group) {
            $_SubArray = array();
            $_SubArray['id'] = $group->id;
            $_SubArray['name'] = $group->name;
            $_SubArray['picture'] = $group->picture;
            $_SubArray['admins'] = $group->admins;
            $_SubArray['members'] = $group->members;
            $LastMessage = $this->GetLastMessage($group->id, null);
            $_SubArray['last_message_id'] = $LastMessage['message_id'];
            $_SubArray['last_message'] = $LastMessage['message'];
            $_SubArray['last_message_time'] = $LastMessage['time'];
            $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($group->id, null);
            array_push($Groups, $_SubArray);
        }
        if (count($Groups) > 0) {
            array_multisort(array_column($Groups, 'total_unread_message'), SORT_ASC, $Groups);
            array_multisort(array_column($Groups, 'last_message_id'), SORT_DESC, $Groups);
        }
        /* Groups Record */

        return view('admin.internal-messaging.index', compact('page', 'Role', 'UserId', 'UserProfile', 'ChatUsers', 'ContactUsers', 'Groups', 'TeamMembers'));
    }

    public function CheckUserChatAvaliableOrNot($GroupId, $ReceiverId)
    {
        $Chat = DB::table('messages')
            ->where('messages.deleted_at', '=', null)
            ->where('messages.group_id', '=', $GroupId)
            ->where(function ($query) use ($ReceiverId) {
                $query->where('messages.sender_id', '=', Auth::id());
                $query->where('messages.receiver_id', '=', $ReceiverId);
            })
            ->orWhere(function ($query) use ($ReceiverId) {
                $query->where('messages.sender_id', '=', $ReceiverId);
                $query->where('messages.receiver_id', '=', Auth::id());
            })
            ->count();

        return $Chat;
    }

    public function GetLastMessage($GroupId, $ReceiverId)
    {
        if ($GroupId == null) {
            $Chat = DB::table('messages')
                ->where('messages.deleted_at', '=', null)
                ->where('messages.group_id', '=', $GroupId)
                ->where(function ($query) use ($ReceiverId) {
                    $query->where('messages.sender_id', '=', Auth::id());
                    $query->where('messages.receiver_id', '=', $ReceiverId);
                })
                ->orWhere(function ($query) use ($ReceiverId) {
                    $query->where('messages.sender_id', '=', $ReceiverId);
                    $query->where('messages.receiver_id', '=', Auth::id());
                })
                ->select('messages.id', 'messages.message', 'messages.time')
                ->orderBy('messages.id', 'DESC')
                ->get();

            if ($Chat != "" && count($Chat)) {
                $_message = array();
                $_message['message_id'] = $Chat[0]->id;
                $_message['message'] = $Chat[0]->message;
                $_message['time'] = $Chat[0]->time;
                return $_message;
            } else {
                $_message = array();
                $_message['message_id'] = 0;
                $_message['message'] = "";
                $_message['time'] = "";
                return $_message;
            }
        } else {
            $Chat = DB::table('messages')
                ->where('messages.deleted_at', '=', null)
                ->where('messages.group_id', '=', $GroupId)
                ->select('messages.id', 'messages.message', 'messages.time')
                ->orderBy('messages.id', 'DESC')
                ->get();

            if ($Chat != "" && count($Chat)) {
                $_message = array();
                $_message['message_id'] = $Chat[0]->id;
                $_message['message'] = $Chat[0]->message;
                $_message['time'] = $Chat[0]->time;
                return $_message;
            } else {
                $_message = array();
                $_message['message_id'] = 0;
                $_message['message'] = "";
                $_message['time'] = "";
                return $_message;
            }
        }
    }

    public function GetTotalUnreadMessage($GroupId, $ReceiverId)
    {
        if ($GroupId == null) {
            $TotalUnreadMessage = DB::table('messages')
                ->where('messages.deleted_at', '=', null)
                ->where('messages.group_id', '=', $GroupId)
                ->where('messages.sender_id', '=', $ReceiverId)
                ->where('messages.receiver_id', '=', Auth::id())
                ->where('messages.read_status', '=', "unread")
                ->count();

            return $TotalUnreadMessage;
        } else {
            // $TotalUnreadMessage = DB::table('messages')
            //     ->where('messages.deleted_at', '=', null)
            //     ->where('messages.group_id', '=', $GroupId)
            //     ->where('messages.sender_id', '!=', Auth::id())
            //     ->where('messages.read_status', '=', "unread")
            //     ->count();

            $GroupsSql = "SELECT * FROM messages WHERE ((FIND_IN_SET(:userId, read_by) < 1 OR ISNULL(read_by)) AND group_id = :groupId
                  AND sender_id != :senderId) AND ISNULL(deleted_at);";
            $TotalUnreadMessage = DB::select(DB::raw($GroupsSql), array(Auth::id(), $GroupId, Auth::id()));
            return count($TotalUnreadMessage);
        }
    }

    public function SendMessage(Request $request)
    {
        $ReceiverId = $request['ReceiverId'];
        $GroupId = $request['GroupId'];
        $Message = $request['Message'];
        $Time = Carbon::now()->format('g:i a');
        if ($GroupId == "") {
            $GroupId = null;
        }

        if ($GroupId == null) {
            DB::beginTransaction();
            $affected = Message::create([
                'group_id' => $GroupId,
                'sender_id' => Auth::id(),
                'receiver_id' => $ReceiverId,
                'message' => $Message,
                'time' => $Time,
                'created_at' => Carbon::now(),
            ]);
            if ($affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Failed";
            }
        } else {
            DB::beginTransaction();
            $affected = Message::create([
                'group_id' => $GroupId,
                'sender_id' => Auth::id(),
                'message' => $Message,
                'time' => $Time,
                'created_at' => Carbon::now(),
            ]);
            if ($affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Failed";
            }
        }
    }

    public function LoadMessages(Request $request)
    {
        $ReceiverId = $request['ReceiverId'];
        $GroupId = $request['GroupId'];
        if ($GroupId == "") {
            $GroupId = null;
        }
        $TotalUnreadMessage = $this->CalculateReceiverUnreadMessage($ReceiverId, $GroupId);

        if ($GroupId == null) {
            $Chat = DB::table('messages')
                ->join('profiles', 'profiles.user_id', '=', 'messages.sender_id')
                ->where('messages.deleted_at', '=', null)
                ->where(function ($query) use ($GroupId) {
                    if ($GroupId != "") {
                        $query->where('messages.group_id', '=', $GroupId);
                    } else {
                        $query->where('messages.group_id', '=', null);
                    }
                })
                ->where(function ($query) use ($ReceiverId) {
                    $query->where('messages.sender_id', '=', Auth::id());
                    $query->where('messages.receiver_id', '=', $ReceiverId);
                })
                ->orWhere(function ($query) use ($ReceiverId) {
                    $query->where('messages.sender_id', '=', $ReceiverId);
                    $query->where('messages.receiver_id', '=', Auth::id());
                })
                ->select('messages.id', 'messages.sender_id', 'messages.receiver_id', 'messages.message', 'messages.time', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->orderBy('messages.id', 'ASC')
                ->get();

            $_chat = array();
            $_chat['total_unread_message'] = $TotalUnreadMessage;
            $_chat['chat'] = json_encode($Chat);
            return $_chat;
            // return json_encode($Chat);
        } else {
            $Chat = DB::table('messages')
                ->join('profiles', 'profiles.user_id', '=', 'messages.sender_id')
                ->where('messages.deleted_at', '=', null)
                ->where(function ($query) use ($GroupId) {
                    $query->where('messages.group_id', '=', $GroupId);
                })
                ->select('messages.id', 'messages.sender_id', 'messages.receiver_id', 'messages.message', 'messages.time', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->orderBy('messages.id', 'ASC')
                ->get();

            $_chat = array();
            $_chat['total_unread_message'] = $TotalUnreadMessage;
            $_chat['chat'] = json_encode($Chat);
            return $_chat;
            // return json_encode($Chat);
        }
    }

    public function MessagesReadAll(Request $request)
    {
        $ReceiverId = $request['ReceiverId'];
        $GroupId = $request['GroupId'];
        if ($GroupId == "") {
            $GroupId = null;
        }

        if ($GroupId == null) {
            DB::beginTransaction();
            $affected = DB::table('messages')
                ->where('sender_id', '=', $ReceiverId)
                ->where('receiver_id', '=', Auth::id())
                ->where('group_id', '=', $GroupId)
                ->update([
                    'read_status' => "read",
                    'updated_at' => Carbon::now()
                ]);
            if ($affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Failed";
            }
        } else {
            DB::beginTransaction();
            $MessageSql = "SELECT * FROM messages WHERE ((FIND_IN_SET(:userId, read_by) < 1 OR ISNULL(read_by)) AND group_id = :groupId) AND ISNULL(deleted_at);";
            $Messages = DB::select(DB::raw($MessageSql), array(Auth::id(), $GroupId));
            foreach ($Messages as $msg) {
                $ReadBy = array();
                if ($msg->read_by != "") {
                    $ReadBy = explode(",", $msg->read_by);
                }
                array_push($ReadBy, Auth::id());
                $ReadBy = implode(",", $ReadBy);
                DB::table('messages')
                    ->where('id', '=', $msg->id)
                    ->update([
                        "read_by" => $ReadBy,
                        'updated_at' => Carbon::now()
                    ]);
            }
            DB::commit();
            echo "Success";
        }
    }

    /* Refresh Chat List */
    public function LoadChatList()
    {
        $ChatUsers = array();
        $Role = Session::get('user_role');
        $Type = 'Chat';
        if ($Role == 1) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();


            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            $Type = 'Chat';
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-success ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 2) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();


            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 3) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 4) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 5) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 6) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 7) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 8) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        } elseif ($Role == 9) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    $_SubArray = array();
                    $_SubArray['id'] = $user->id;
                    $_SubArray['online_status'] = $user->online_status;
                    $_SubArray['role'] = $user->role;
                    $_SubArray['firstname'] = $user->firstname;
                    $_SubArray['middlename'] = $user->middlename;
                    $_SubArray['lastname'] = $user->lastname;
                    $_SubArray['phone'] = $user->phone;
                    $_SubArray['profile_picture'] = $user->profile_picture;
                    $LastMessage = $this->GetLastMessage($GroupId, $user->id);
                    $_SubArray['last_message_id'] = $LastMessage['message_id'];
                    $_SubArray['last_message'] = $LastMessage['message'];
                    $_SubArray['last_message_time'] = $LastMessage['time'];
                    $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($GroupId, $user->id);
                    array_push($ChatUsers, $_SubArray);
                }
            }
            if (count($ChatUsers) > 0) {
                array_multisort(array_column($ChatUsers, 'total_unread_message'), SORT_ASC, $ChatUsers);
                array_multisort(array_column($ChatUsers, 'last_message_id'), SORT_DESC, $ChatUsers);
            }

            $ChatList = "";
            $counter = 1;
            foreach ($ChatUsers as $chat_user) {
                $FullName = "";
                $ListId = "UserChatId" . $counter;
                if ($chat_user['middlename'] != "") {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname'];
                } else {
                    $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname'];
                }

                $ChatList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $chat_user['id'] . "_" . $FullName . "_" . $chat_user['role'] . "_" . $chat_user['profile_picture'] . "_" . $chat_user['online_status'] . "_" . $chat_user['phone'] . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ChatList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1)) . " </span>";
                /*if ($chat_user['profile_picture'] != "") {
                    $ChatList .= "<img src='" . asset('public/storage/profile-pics/' . $chat_user['profile_picture']) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ChatList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ChatList .= "" .
                    "      <div class='status ";
                if ($chat_user['online_status'] == 1) {
                    $ChatList .= "online";
                }
                $ChatList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "        <p class='text-muted tx-13'>" . substr($chat_user['last_message'], 0, 50) . "</p>" .
                    "      </div>" .
                    "      <div class='d-flex flex-column align-items-end'>" .
                    "        <p class='text-muted tx-13 mb-1'>" . $chat_user['last_message_time'] . "</p>";
                if ($chat_user['total_unread_message'] != 0) {
                    $ChatList .= "<div class='badge badge-pill badge-primary ml-auto'>" . $chat_user['total_unread_message'] . "</div>";
                }
                $ChatList .= "" .
                    "      </div>" .
                    "    </div>" .
                    "  </a>" .
                    "</li>";
                $counter++;
            }

            $ChatList .= "" .
                "<input type='hidden' name='totalchatusers' id='totalchatusers' value='" . $counter . "' />";

            return json_encode($ChatList);
        }
    }
    /* Refresh Chat List */

    /* Refresh Contact List */
    public function LoadContactList()
    {
        $ContactUsers = array();
        $Role = Session::get('user_role');

        if ($Role == 1) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $ContactUsers = $Users;

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 2) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $ContactUsers = $Users;

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 3) {
            $UserState = SiteHelper::GetCurrentUserState();
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= " class='img-xs rounded-circle'tyle='background: #15D16C; padding: 8px; color: #fff;'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 4) {
            $UserState = SiteHelper::GetCurrentUserState();
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 5) {
            $UserState = SiteHelper::GetCurrentUserState();
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 6) {
            $UserState = SiteHelper::GetCurrentUserState();
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 7) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 3 || $user->role_id == 8) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 8) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 3 || $user->role_id == 7) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        } elseif ($Role == 9) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                // check if this user chat is avaliable in messages table or not
                if ($this->CheckUserChatAvaliableOrNot($GroupId, $user->id) > 0) {
                    array_push($ContactUsers, $user);
                } elseif ($user->role_id == 2 || $user->role_id == 4) {
                    array_push($ContactUsers, $user);
                }
            }

            $ContactList = "";
            $counter = 1;
            $Type = 'Contact';
            foreach ($ContactUsers as $contact_user) {
                $FullName = "";
                $ListId = "UserContactId" . $counter;
                if ($contact_user->middlename != "") {
                    $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname;
                } else {
                    $FullName .= $contact_user->firstname . " " . $contact_user->lastname;
                }

                $ContactList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                    "    id='chatuser_" . $contact_user->id . "_" . $FullName . "_" . $contact_user->role . "_" . $contact_user->profile_picture . "_" . $contact_user->online_status . "_" . $contact_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $ContactList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1)) . " </span>";
                /*if ($contact_user->profile_picture != "") {
                    $ContactList .= "<img src='" . asset('public/storage/profile-pics/' . $contact_user->profile_picture) . "' class='img-xs rounded-circle' alt='user'>";
                } else {
                    $ContactList .= "<img src='" . asset('public/images/user.png') . "' class='img-xs rounded-circle' alt='user'>";
                }*/
                $ContactList .= "" .
                    "      <div class='status ";
                if ($contact_user->online_status == 1) {
                    $ContactList .= "online";
                }
                $ContactList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $ContactList .= "" .
                "<input type='hidden' name='totalcontactsusers' id='totalcontactsusers' value='" . $counter . "' />";

            return json_encode($ContactList);
        }
    }
    /* Refresh Contact List */

    /* Load Search Users List */
    public function LoadSearchUsersList(Request $request)
    {
        $searchTerm = $request['search'];
        $SearchUsers = null;
        $Role = Session::get('user_role');

        if ($Role == 1) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 2) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 3) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 4) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 5) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 6) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 7) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 8) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        } elseif ($Role == 9) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $SearchUsers = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm != "") {
                        $Search = explode(" ", $searchTerm);
                        foreach ($Search as $key => $value) {
                            if ($value != "") {
                                $query->orWhere('profiles.firstname', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.middlename', 'LIKE', '%' . $value . '%');
                                $query->orWhere('profiles.lastname', 'LIKE', '%' . $value . '%');
                            }
                        }
                    }
                })
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();

            $SearchUserList = "";
            $counter = 1;
            foreach ($SearchUsers as $search_user) {
                $FullName = "";
                $ListId = "SearchUserId" . $counter;
                if ($search_user->middlename != "") {
                    $FullName .= $search_user->firstname . " " . $search_user->middlename . " " . $search_user->lastname;
                } else {
                    $FullName .= $search_user->firstname . " " . $search_user->lastname;
                }

                $SearchUserList .= "" .
                    "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                    " <a href='javascript:;' onclick='OpenSearchUserChat(this.id);'" .
                    "    id='chatuser_" . $search_user->id . "_" . $FullName . "_" . $search_user->role . "_" . $search_user->profile_picture . "_" . $search_user->online_status . "_" . $search_user->phone . "_" . $ListId . "'" .
                    "    class='d-flex align-items-center'>" .
                    "    <figure class='mb-0 mr-2'>";
                $SearchUserList .= "<span class='img-xs rounded-circle tooltip1' style='background: #15D16C; padding: 8px; color: #fff;' data-toggle='tooltip' data-placement='bottom' title='" . $FullName . "'> " . strtoupper(substr($search_user->firstname, 0, 1) . substr($search_user->lastname, 0, 1)) . " </span>";
                $SearchUserList .= "" .
                    "      <div class='status ";
                if ($search_user->online_status == 1) {
                    $SearchUserList .= "online";
                }
                $SearchUserList .= "" .
                    "'></div>" .
                    "    </figure>" .
                    "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                    "      <div>" .
                    "        <p class='text-body font-weight-bold'>" . $FullName . "</p>" .
                    "      </div>" .
                    "    </div>" .
                    " </a>" .
                    "</li>";
                $counter++;
            }

            $SearchUserList .= "" .
                "<input type='hidden' name='totalsearchusers' id='totalsearchusers' value='" . $counter . "' />";

            return json_encode($SearchUserList);
        }
    }
    /* Load Search Users List */

    /* Group Chat - Start */
    public function AddNewGroup(Request $request)
    {
        $GroupName = $request['GroupName'];
        $GroupMembers = json_decode($request['GroupMembers']);
        $Admins = array();
        if (Auth::id() != 1) {
            array_push($Admins, Auth::id(), 1);
            array_push($GroupMembers, Auth::id(), 1);
        } else {
            array_push($Admins, Auth::id());
            array_push($GroupMembers, Auth::id());
        }
        $Admins = implode(",", $Admins);
        $GroupMembers = implode(",", $GroupMembers);

        DB::beginTransaction();
        $affected = Group::create([
            'name' => $GroupName,
            'admins' => $Admins,
            'members' => $GroupMembers,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        /* Send Notification To Members */
        $CreatedBy = DB::table('profiles')->where('user_id', '=', Auth::id())->get();
        foreach(json_decode($request['GroupMembers']) as $member){
            Notification::create([
                'sender_id' => Auth::id(),
                'reciever_id' => $member,
                'message' => $CreatedBy[0]->firstname . " " . $CreatedBy[0]->lastname . " added you in a chat group (" . $GroupName . ")",
                'created_at' => Carbon::now()
            ]);
        }

        if ($affected) {
            DB::commit();
            echo "Success";
        } else {
            DB::rollBack();
            echo "Failed";
        }
    }

    /* Group Chat - End */

    public function LoadGroupList()
    {
        $GroupsSql = "SELECT * FROM groups WHERE ((FIND_IN_SET(:userId, members) > 0)) AND ISNULL(deleted_at) ORDER BY id DESC;";
        $GroupsData = DB::select(DB::raw($GroupsSql), array(Auth::id()));
        $Groups = array();
        foreach ($GroupsData as $group) {
            $_SubArray = array();
            $_SubArray['id'] = $group->id;
            $_SubArray['name'] = $group->name;
            $_SubArray['picture'] = $group->picture;
            $_SubArray['admins'] = $group->admins;
            $_SubArray['members'] = $group->members;
            $LastMessage = $this->GetLastMessage($group->id, null);
            $_SubArray['last_message_id'] = $LastMessage['message_id'];
            $_SubArray['last_message'] = $LastMessage['message'];
            $_SubArray['last_message_time'] = $LastMessage['time'];
            $_SubArray['total_unread_message'] = $this->GetTotalUnreadMessage($group->id, null);
            array_push($Groups, $_SubArray);
        }
        if (count($Groups) > 0) {
            array_multisort(array_column($Groups, 'total_unread_message'), SORT_ASC, $Groups);
            array_multisort(array_column($Groups, 'last_message_id'), SORT_DESC, $Groups);
        }
        $GroupList = "";
        $counter = 1;
        $Type = 'Group';
        foreach ($Groups as $group) {
            $ListId = "GroupId" . $counter;
            $GroupList .= "" .
                "<li class='chat-item pr-1' id='" . $ListId . "'>" .
                " <a href='javascript:;' onclick='OpenUserChat(this.id, \"" . $Type . "\");'" .
                "    id='chatuser_" . $group['id'] . "_" . $group['name'] . "_" . $group['picture'] . "_" . $group['admins'] . "_" . $group['members'] . "_" . $ListId . "'" .
                "    class='d-flex align-items-center'>" .
                "    <figure class='mb-0 mr-2'>";
            $GroupNameArray = explode(' ', $group['name']);
            $GroupList .= "<span class='img-xs rounded-circle'  style='background: #15D16C; padding: 8px; color: #fff;'> " . strtoupper(substr($GroupNameArray[0], 0, 1) . substr(end($GroupNameArray), 0, 1)) . " </span>";
            $GroupList .= "" .
                "    </figure>" .
                "    <div class='d-flex justify-content-between flex-grow border-bottom'>" .
                "      <div>" .
                "        <p class='text-body font-weight-bold'>" . $group['name'] . "</p>" .
                "        <p class='text-muted tx-13'>" . substr($group['last_message'], 0, 50) . "</p>" .
                "      </div>" .
                "    </div>" .
                "    <div class='d-flex flex-column align-items-end'>" .
                "       <p class='text-muted tx-13 mb-1'>" . $group['last_message_time'] . "</p>";
            if ($group['total_unread_message'] != 0) {
                $GroupList .= "<div class='badge badge-pill badge-success ml-auto'>" . $group['total_unread_message'] . "</div>";
            }
            $GroupList .= "" .
                "</div>" .
                " </a>" .
                "</li>";
            $counter++;
        }

        $GroupList .= "" .
            "<input type='hidden' name='totalgroups' id='totalgroups' value='" . $counter . "' />";

        return json_encode($GroupList);
    }

    public function LoadGroupDetails(Request $request)
    {
        $Role = Session::get('user_role');
        $GroupId = $request['GroupId'];
        $TeamMembers = array();
        $GroupDetails = DB::table('groups')
            ->where("deleted_at", null)
            ->where("id", $GroupId)
            ->get();

        $GroupName = $GroupDetails[0]->name;
        $GroupMembers = $GroupDetails[0]->members;
        $GroupMembers = explode(",", $GroupMembers);

        if ($Role == 1) {
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();
            $TeamMembers = $Users;
        } elseif ($Role == 2) {
            $RolesExcluded = array(10, 11);
            $ChatUsers = array();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture')
                ->get();
            $TeamMembers = $Users;
        } elseif ($Role == 3) {
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 4) {
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 5) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if (($user->role_id == 3 || $user->role_id == 5) && ($user->state == $UserState)) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 6) {
            $RolesExcluded = array(10, 11);
            $UserState = SiteHelper::GetCurrentUserState();
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if (($user->role_id == 4 || $user->role_id == 6) && ($user->state == $UserState)) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 7) {
            $GroupId = null;
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if ($user->role_id == 3 || $user->role_id == 8) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 8) {
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if ($user->role_id == 3 || $user->role_id == 7) {
                    array_push($TeamMembers, $user);
                }
            }
        } elseif ($Role == 9) {
            $RolesExcluded = array(10, 11);
            $Users = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.id', '!=', Auth::id())
                ->whereNotIn('users.role_id', $RolesExcluded)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.online_status', 'users.role_id', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.middlename', 'profiles.lastname AS lastname', 'profiles.phone', 'profiles.profile_picture', 'profiles.state')
                ->get();

            foreach ($Users as $user) {
                if ($user->role_id == 2 || $user->role_id == 4) {
                    array_push($TeamMembers, $user);
                }
            }
        }

        $options = "";
        foreach ($TeamMembers as $member) {
            $FullName = "";
            if ($member->middlename != "") {
                $FullName .= $member->firstname . " " . $member->middlename . " " . $member->lastname;
            } else {
                $FullName .= $member->firstname . " " . $member->lastname;
            }

            if (in_array($member->id, $GroupMembers)) {
                $options .= "<option value='" . $member->id . "' selected>" . $FullName . "</option>";
            } else {
                $options .= "<option value='" . $member->id . "'>" . $FullName . "</option>";
            }
        }

        $group_details['name'] = $GroupName;
        $group_details['members'] = $options;
        echo json_encode($group_details);
    }

    public function UpdateGroup(Request $request)
    {
        $GroupId = $request['GroupId'];
        $GroupName = $request['GroupName'];
        $GroupMembers = json_decode($request['GroupMembers']);

        // Get Group Details
        $GroupDetails = DB::table('groups')
            ->where('deleted_at', '=', null)
            ->where('id', '=', $GroupId)
            ->get();

        $OldGroupMembers = explode(",", $GroupDetails[0]->members);
        foreach ($GroupMembers as $groupMember){
            if(!in_array($groupMember, $OldGroupMembers)){
                // New Member Here
                $CreatedBy = DB::table('profiles')->where('user_id', '=', Auth::id())->get();
                Notification::create([
                    'sender_id' => Auth::id(),
                    'reciever_id' => $groupMember,
                    'message' => $CreatedBy[0]->firstname . " " . $CreatedBy[0]->lastname . " added you in a chat group (" . $GroupName . ")",
                    'created_at' => Carbon::now()
                ]);
            }
        }

        $OldGroupAdmins = explode(",", $GroupDetails[0]->admins);
        foreach ($OldGroupAdmins as $key => $value) {
            array_push($GroupMembers, $value);
        }
        $GroupMembers = implode(",", $GroupMembers);

        DB::beginTransaction();
        $affected = DB::table('groups')
            ->where('id', '=', $GroupId)
            ->update([
                'name' => $GroupName,
                'members' => $GroupMembers,
                'updated_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            echo "Success";
        } else {
            DB::rollback();
            echo "Failed";
        }
    }

    public function CalculateReceiverUnreadMessage($ReceiverId, $GroupId)
    {
        if ($GroupId == "" || $GroupId == null) {
            $TotalUnreadMessage = DB::table('messages')
                ->where('messages.deleted_at', '=', null)
                ->where('messages.read_status', '=', "unread")
                ->where('messages.receiver_id', '=', Auth::id())
                ->where('messages.group_id', '=', $GroupId)
                ->count();

            return $TotalUnreadMessage;
        }
        else {
            $MessagesSql = "SELECT * FROM messages INNER JOIN groups ON messages.group_id = groups.id WHERE ((FIND_IN_SET(:userId, messages.read_by) < 1) OR ISNULL(messages.read_by)) AND (FIND_IN_SET(:loggedUserId, groups.members) > 0) AND messages.sender_id != :senderId AND ISNULL(messages.receiver_id) AND ISNULL(messages.deleted_at) AND ISNULL(groups.deleted_at);";
            $MessageData = DB::select(DB::raw($MessagesSql), array(Auth::id(), Auth::id(), Auth::id()));

            return count($MessageData);
        }
    }
}