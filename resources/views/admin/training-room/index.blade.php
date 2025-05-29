@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">ELITE EMPIRE - <span class="text-primary">Training Room</span></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            Details
                        </h6>
                        <div class="table-responsive">
                            <table id="admin_training_room_roles" class="table table-bordered text-center w-100">
                                <thead>
                                <tr>
                                    <th style="width: 50%;">Role</th>
                                    <th style="width: 50%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Acquisition Manager</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2" id="role_3_Acquisition Manager"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Disposition Manager</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2" id="role_4_Disposition Manager"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Acquisition Representative</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2"
                                                id="role_5_Acquisition Representative"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Disposition Representative</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2"
                                                id="role_6_Disposition Representative"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cold Caller</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2" id="role_7_Cold Caller"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Affiliate</td>
                                    <td>
                                        <button class="btn greenActionButtonTheme mr-2" id="role_8_Affiliate"
                                                onclick="openTrainingRoom(this.id);"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr style="background-color: #f2f2f2;">
                                    <td>Knowledge Zone</td>
                                    <td>
                                        <?php
                                        $Url = url('admin/training-room/faqs');
                                        ?>
                                        <button class="btn greenActionButtonTheme mr-2"
                                                onclick="window.location.href='{{$Url}}';"><i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
@endsection
