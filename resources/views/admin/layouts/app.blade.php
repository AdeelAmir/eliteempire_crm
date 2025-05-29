<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.partials.head')
    @stack('style')
    <style media="screen">
        @media (max-width: 991px) {
            .sidebar {
                z-index: 999;
                margin-left: -175px;
                visibility: inherit;
            }

            .main-wrapper .page-wrapper {
                margin-left: 70px !important;
                width: 80% !important;
            }
        }

        .select2-container {
            width: 100% !important;
        }

        .greenActionButtonTheme {
            background-color: #15D16C;
            color: black;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #15d16c !important;
            border-color: #15d16c !important;
        }

        .btn-primary.disabled, .swal2-modal .swal2-actions button.disabled.swal2-confirm, .wizard > .actions a.disabled, .btn-primary:disabled, .swal2-modal .swal2-actions button.swal2-confirm:disabled, .wizard > .actions a:disabled {
            color: #fff;
            background-color: #15D16C;
            border-color: #15D16C;
        }

        @media only screen and (max-width: 767px) {
          .dataTables_wrapper.dt-bootstrap4 .dataTables_filter {
              text-align: left;
              margin-left: 0;
          }
        }
    </style>
</head>
<body class="sidebar-dark">
<div class="main-wrapper">
    @include('admin.layouts.partials.sidebar')
    <div class="page-wrapper">
        @include('admin.layouts.partials.navbar')
        @yield('content')
        @include('admin.layouts.partials.footer')
    </div>
</div>
@include('admin.layouts.partials.footer-scripts')
@include('admin.includes.recieverBroadcastModal')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script type="text/javascript">
    let EditFaqAnswerEditor = null;
    if ($("#add_article_details").length) {
        ClassicEditor.create(document.querySelector('#add_article_details'), {
        ckfinder: {
            uploadUrl: 'http://localhost/eliteempire/public/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
        },
        link: {
            addTargetToExternalLinks: true
        },
        }).then(editor => {

        })
        .catch(error => {

        });
    }

    if ($("#faqAnswer").length) {
        ClassicEditor.create(document.querySelector('#faqAnswer'), {
        ckfinder: {
            uploadUrl: 'http://localhost/eliteempire/public/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
        },
        link: {
            addTargetToExternalLinks: true
        },
        }).then(editor => {

        })
        .catch(error => {

        });
    }

    if ($("#faqAnswer1").length) {
        ClassicEditor.create(document.querySelector('#faqAnswer1'), {
        ckfinder: {
            uploadUrl: 'http://localhost/eliteempire/public/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
        },
        link: {
            addTargetToExternalLinks: true
        },
        }).then(editor => {
            EditFaqAnswerEditor = editor;
        })
        .catch(error => {

        });
    }
</script>
<script>
    $(document).ready(function () {
        window.setInterval(function () {
            // Internal Messaging
            $.ajax({
                type: "post",
                url: "{{url('/message/unread')}}",
                data: {}
            }).done(function (data) {
                data = JSON.parse(data);
                if (parseInt(data.unread) + parseInt(data.group_unread) === 0) {
                    $("#message-indicator").css({backgroundColor: '#4fd36d'});
                    $("#message-indicator").html('').html(parseInt(data.unread) + parseInt(data.group_unread));
                } else {
                    $("#message-indicator").css({backgroundColor: 'red'});
                    $("#message-indicator").html('').html(parseInt(data.unread) + parseInt(data.group_unread));
                }
                if($("#group_messages_count").length > 0){
                    if(parseInt(data.group_unread) === 0){
                        $("#group_messages_count").hide();
                    }else{
                        $("#group_messages_count").show().html(parseInt(data.group_unread));
                    }
                }
            });

            // Notification
            $.ajax({
                type: "post",
                url: "{{url('/notification/all')}}",
                data: {}
            }).done(function (data) {
                data = JSON.parse(data);
                if (data.Total === 0) {
                    $("#notification-indicator").css({backgroundColor: '#4fd36d'});
                    $("#notification-indicator").html('').html(data.Total);
                    $("#totalnewnotification").html('').html(data.TotalNewNotifications);
                    $("#notification-dropdown-body").html('').html(data.Items);
                } else {
                    $("#notification-indicator").css({backgroundColor: 'red'});
                    $("#notification-indicator").html('').html(data.Total);
                    $("#totalnewnotification").html('').html(data.TotalNewNotifications);
                    $("#notification-dropdown-body").html('').html(data.Items);
                }
            });

            // Broadcast Notification
            let check = $("#recieverBroadcastUserId").val();
            if (check === '') {
                $.ajax({
                    type: "post",
                    url: "{{url('/broadcast/all')}}",
                    data: {}
                }).done(function (data) {
                    data = JSON.parse(data);
                    if (data.Total === 1) {
                        $("#recieverBroadcastId").val(data.BroadcastId);
                        $("#recieverReadBroadcastId").val(data.ReadBroadcastId);
                        $("#recieverBroadcastUserId").val(data.RecieverId);
                        $("#recieverBroadcast_message").html('').html(data.Message);
                        $("#recieverBroadcastModal").modal('toggle');
                    }
                });
            }
        }, 2500);
    });
</script>
<script src='https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js'></script>

@include('admin.includes.scripts')
@stack('scripts')

</body>
</html>
