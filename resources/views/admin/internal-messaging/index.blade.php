@extends('admin.layouts.app')
@section('content')
    <style media="screen">
        .activeUserChat {
            background-color: #ebebeb;
        }

        .chatForm {
            position: absolute;
            bottom: 0px;
            width: 98%;
        }

        .chat-wrapper .chat-content .chat-body {
            position: relative;
            max-height: calc(100vh - 340px);
            margin-top: 20px;
            margin-bottom: 60px;
        }

        .no-margin-bottom {
            margin-bottom: 0px;
        }

        .d-none {
            display: none;
        }

        .chat-wrapper .chat-content .chat-body .messages .message-item.me span{
            -webkit-order: 2;
            order: 2;
            margin-left: 15px;
        }

        .chat-wrapper .chat-content .chat-body .messages .message-item span{
            -webkit-order: 2;
            order: 2;
            margin-right: 15px;
        }
    </style>
    <div class="page-content" id="InternalMessagingPage">
        <div class="row chat-wrapper">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row position-relative" style="min-height: 460px;">
                            <div class="col-lg-4 chat-aside border-lg-right">
                                <div class="aside-content">
                                    <div class="aside-header">
                                        <div class="d-flex justify-content-between align-items-center pb-2 mb-2">
                                            @if(isset($UserProfile))
                                                <div class="d-flex align-items-center">
                                                    <figure class="mr-2 mb-0">
                                                        @if($UserProfile[0]->profile_picture != "")
                                                            <img src="{{asset('public/storage/profile-pics/' . $UserProfile[0]->profile_picture)}}"
                                                                 class="img-sm rounded-circle" alt="profile">
                                                        @else
                                                            <img src="{{asset('public/storage/profile-pics/admin_12345.jpg')}}"
                                                                 class="img-sm rounded-circle" alt="profile">
                                                        @endif
                                                        <div class="status online"></div>
                                                    </figure>
                                                    <div>
                                                        @php
                                                            $FullName = "";
                                                        @endphp
                                                        @if($UserProfile[0]->middlename != "")
                                                            @php $FullName .= $UserProfile[0]->firstname . " " . $UserProfile[0]->middlename . " " . $UserProfile[0]->lastname; @endphp
                                                        @else
                                                            @php $FullName .= $UserProfile[0]->firstname . " " . $UserProfile[0]->lastname; @endphp
                                                        @endif
                                                        <h6>{{$FullName}}</h6>
                                                        <p class="text-muted tx-13">{{$UserProfile[0]->role}}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <form class="search-form">
                                            <div class="input-group border rounded-sm">
                                                <div class="input-group-prepend no-margin-bottom">
                                                    <div class="input-group-text border-0 rounded-sm">
                                                        <i data-feather="search" class="icon-md cursor-pointer"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control border-0 rounded-sm"
                                                       id="searchForm" placeholder="Search here..."
                                                       onkeypress="HandleFormSubmit(event);"
                                                       onkeyup="HandleFormSubmit(event);SearchUserForChat(this.value);">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="aside-body">
                                        <ul class="nav nav-tabs mt-3" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="chats-tab" data-toggle="tab"
                                                   href="#chats" role="tab" aria-controls="chats" aria-selected="true">
                                                    <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center">
                                                        <i data-feather="message-square"
                                                           class="icon-sm mr-sm-2 mr-lg-0 mr-xl-2 mb-md-1 mb-xl-0"></i>
                                                        <p class="d-none d-sm-block">Chats</p>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="contacts-tab" data-toggle="tab" href="#contacts"
                                                   role="tab" aria-controls="contacts" aria-selected="true">
                                                    <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center">
                                                        <i data-feather="phone"
                                                           class="icon-sm mr-sm-2 mr-lg-0 mr-xl-2 mb-md-1 mb-xl-0"></i>
                                                        <p class="d-none d-sm-block">Contacts</p>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="groups-tab" data-toggle="tab" href="#groups"
                                                   role="tab" aria-controls="groups" aria-selected="true">
                                                    <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center">
                                                        <i data-feather="users"
                                                           class="icon-sm mr-sm-2 mr-lg-0 mr-xl-2 mb-md-1 mb-xl-0"></i>
                                                        <p class="d-none d-sm-block">Groups <span class="badge badge-danger" id="group_messages_count" style="border-radius: 60px; display: none;">5</span></p>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mt-3">
                                            <div class="tab-pane fade show active" id="chats" role="tabpanel"
                                                 aria-labelledby="chats-tab">
                                                <div>
                                                    <p class="text-muted mb-1">Recent chats</p>
                                                    <ul class="list-unstyled chat-list px-1" id="SearchUsersList"
                                                        style="display:none;">

                                                    </ul>
                                                    <ul class="list-unstyled chat-list px-1" id="ChatList">
                                                        @php $counter = 1; @endphp
                                                        @foreach($ChatUsers as $chat_user)
                                                            @php
                                                                $FullName = "";
                                                                $ListId = "UserChatId" . $counter;
                                                            @endphp
                                                            @if($chat_user['middlename'] != "")
                                                                @php $FullName .= $chat_user['firstname'] . " " . $chat_user['middlename'] . " " . $chat_user['lastname']; @endphp
                                                            @else
                                                                @php $FullName .= $chat_user['firstname'] . " " . $chat_user['lastname']; @endphp
                                                            @endif
                                                            <li class="chat-item pr-1" id="{{$ListId}}">
                                                                <a href="javascript:void(0);"
                                                                   onclick="OpenUserChat(this.id, 'Chat');"
                                                                   id="chatuser_{{$chat_user['id']}}_{{$FullName}}_{{$chat_user['role']}}_{{$chat_user['profile_picture']}}_{{$chat_user['online_status']}}_{{$chat_user['phone']}}_{{$ListId}}"
                                                                   class="d-flex align-items-center">
                                                                    <figure class="mb-0 mr-2">
                                                                        <span class="img-xs rounded-circle" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="{{$FullName}}">{{strtoupper(substr($chat_user['firstname'], 0, 1) . substr($chat_user['lastname'], 0, 1))}}</span>
                                                                        <div class="status <?php if ($chat_user['online_status'] == 1) {
                                                                            echo "online";
                                                                        } ?>"></div>
                                                                    </figure>
                                                                    <div class="d-flex justify-content-between flex-grow border-bottom">
                                                                        <div>
                                                                            <p class="text-body font-weight-bold">{{$FullName}}</p>
                                                                            <p class="text-muted tx-13">{{substr($chat_user['last_message'],0, 50)}}</p>
                                                                        </div>
                                                                        <div class="d-flex flex-column align-items-end">
                                                                            <p class="text-muted tx-13 mb-1">{{$chat_user['last_message_time']}}</p>
                                                                            @if($chat_user['total_unread_message'] != 0)
                                                                                <div class="badge badge-pill badge-success ml-auto">{{$chat_user['total_unread_message']}}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            @php $counter++; @endphp
                                                        @endforeach
                                                        <input type="hidden" name="totalchatusers" id="totalchatusers"
                                                               value="{{$counter}}"/>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Contacts Tab -->
                                            <div class="tab-pane fade show" id="contacts" role="tabpanel"
                                                 aria-labelledby="contacts-tab">
                                                <div>
                                                    <p class="text-muted mb-1">Contacts</p>
                                                    <ul class="list-unstyled chat-list px-1" id="ContactList">
                                                        @php $counter = 1; @endphp
                                                        @foreach($ContactUsers as $contact_user)
                                                            @php
                                                                $FullName = "";
                                                                $ListId = "UserContactId" . $counter;
                                                            @endphp
                                                            @if($contact_user->middlename != "")
                                                                @php $FullName .= $contact_user->firstname . " " . $contact_user->middlename . " " . $contact_user->lastname; @endphp
                                                            @else
                                                                @php $FullName .= $contact_user->firstname . " " . $contact_user->lastname; @endphp
                                                            @endif
                                                            <li class="chat-item pr-1" id="{{$ListId}}">
                                                                <a href="javascript:;"
                                                                   onclick="OpenUserChat(this.id, 'Contact');"
                                                                   id="chatuser_{{$contact_user->id}}_{{$FullName}}_{{$contact_user->role}}_{{$contact_user->profile_picture}}_{{$contact_user->online_status}}_{{$contact_user->phone}}_{{$ListId}}"
                                                                   class="d-flex align-items-center">
                                                                    <figure class="mb-0 mr-2">
                                                                        <span class="img-xs rounded-circle" style="background: #15D16C; padding: 8px; color: #fff;" data-toggle="tooltip" data-placement="bottom" title="{{$FullName}}">{{strtoupper(substr($contact_user->firstname, 0, 1) . substr($contact_user->lastname, 0, 1))}}</span>
                                                                        <div class="status <?php if ($contact_user->online_status == 1) {
                                                                            echo "online";
                                                                        } ?>"></div>
                                                                    </figure>
                                                                    <div class="d-flex justify-content-between flex-grow border-bottom">
                                                                        <div>
                                                                            <p class="text-body font-weight-bold">{{$FullName}}</p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            @php $counter++; @endphp
                                                        @endforeach
                                                        <input type="hidden" name="totalcontactsusers"
                                                               id="totalcontactsusers" value="{{$counter}}"/>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Contacts Tab -->

                                            <!-- Groups Tab -->
                                            <div class="tab-pane fade show" id="groups" role="tabpanel"
                                                 aria-labelledby="groups-tab">
                                                <div>
                                                    <p class="text-muted mb-1">Groups
                                                        <i data-feather="plus-circle"
                                                           class="icon-md cursor-pointer float-right mr-4"
                                                           data-toggle="tooltip" title="Add New Group"
                                                           onclick="OpenAddNewGroupModal();"></i>
                                                    </p>
                                                    <ul class="list-unstyled chat-list px-1" id="GroupsList">
                                                        @php $counter = 1; @endphp
                                                        @foreach($Groups as $group)
                                                            @php
                                                                $ListId = "GroupId" . $counter;
                                                            @endphp
                                                            <li class="chat-item pr-1" id="{{$ListId}}">
                                                                <a href="javascript:void(0);"
                                                                   onclick="OpenUserChat(this.id, 'Group');"
                                                                   id="group_{{$group['id']}}_{{$group['name']}}_{{$group['picture']}}_{{$group['admins']}}_{{$group['members']}}_{{$ListId}}"
                                                                   class="d-flex align-items-center">
                                                                    <figure class="mb-0 mr-2">
                                                                        <?php
                                                                        $GroupNameArray = explode(' ', $group['name']);
                                                                        ?>
                                                                        <span class="img-xs rounded-circle" style="background: #15D16C; padding: 8px; color: #fff;">{{strtoupper(substr($GroupNameArray[0], 0, 1) . substr(end($GroupNameArray), 0, 1))}}</span>
                                                                    </figure>
                                                                    <div class="d-flex justify-content-between flex-grow border-bottom">
                                                                        <div>
                                                                            <p class="text-body font-weight-bold">{{$group['name']}}</p>
                                                                            <p class="text-muted tx-13">{{substr($group['last_message'],0, 50)}}</p>
                                                                        </div>
                                                                        <div class="d-flex flex-column align-items-end">
                                                                            <p class="text-muted tx-13 mb-1">{{$group['last_message_time']}}</p>
                                                                            @if($group['total_unread_message'] != 0)
                                                                                <div class="badge badge-pill badge-success ml-auto">{{$group['total_unread_message']}}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            @php $counter++; @endphp
                                                        @endforeach
                                                        <input type="hidden" name="totalgroups" id="totalgroups"
                                                               value="{{$counter}}"/>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Groups Tab -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 chat-content">
                                <div class="chat-header border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <!-- Chat User Details -->
                                        <div class="d-flex align-items-center d-none" id="ChatListUserDetails">
                                            <i data-feather="corner-up-left" id="backToChatList"
                                               class="icon-lg mr-2 ml-n2 text-muted d-lg-none"></i>
                                        </div>
                                        <div class="d-flex align-items-center mr-n1 d-none" id="UserPhoneNumber">
                                            <a href="#" class="ChatUserPhoneNumber" style="display:none;">
                                                <i data-feather="phone-call" class="icon-lg text-muted mr-0 mr-sm-3"
                                                   data-toggle="tooltip" title="Start voice call"></i>
                                            </a>
                                            <a href="javascript::void(0);" onclick="OpenEditGroupModal();"
                                               class="EditGroup" style="display:none;">
                                                <i data-feather="edit" class="icon-lg text-muted mr-0 mr-sm-3"
                                                   data-toggle="tooltip" title="Edit Group"></i>
                                            </a>
                                        </div>
                                        <!-- Chat User Details -->
                                    </div>
                                </div>
                                <div class="chat-body" id="chat-body">
                                    <div class="text-center" id="StartChatAlert">
                                        <img src="{{asset('public/assets/images/chat.png')}}" alt=""
                                             style="margin-top: 10em;width:80px;height:80px;">
                                        <h4 class="text-center mt-2" style="color:#cccccc;">Start chat with your
                                            team.</h4>
                                    </div>
                                    <ul class="messages d-none" id="chatuser_messages">

                                    </ul>
                                </div>
                                <div class="chat-footer chatForm d-flex d-none" id="ChatForm">
                                    <div id="ChatFormEmoji" class="d-none">
                                        <button type="button" class="btn border btn-icon rounded-circle mr-2"
                                                data-toggle="tooltip" title="Emoji" name="emoji_picker">
                                            <i data-feather="smile" class="text-muted"></i>
                                        </button>
                                    </div>
                                    <form action="#" class="search-form flex-grow mr-2 d-none" id="ChatFormField">
                                        <div class="input-group">
                                            <input type="hidden" name="receiver_id" id="receiver_id" value=""/>
                                            <input type="hidden" name="group_id" id="group_id" value=""/>
                                            <input type="hidden" name="logged_user_id" id="logged_user_id"
                                                   value="{{$UserId}}"/>
                                            <input type="text" class="form-control rounded-pill" name="message"
                                                   id="chat_message" placeholder="Type a message"
                                                   onkeypress="VerifyMessage(event);"
                                                   onkeyup="VerifyMessage(event); SendMessageWhenEnter(event);">
                                        </div>
                                    </form>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-icon rounded-circle d-none"
                                                id="ChatFormSendButton" onclick="SendMessage();">
                                            <i data-feather="send"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.AddNewGroupModal')
    @include('admin.includes.EditGroupModal')
@endsection