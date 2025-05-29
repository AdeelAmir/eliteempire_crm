@extends('admin.layouts.app')
@section('content')
<style>
  .img_round{
    height:40px; width: 40px;float: left;
  }
  .alertSetting{
    margin-left: 70px;
    margin-top: 3px;
    background: #15D16C;
    width: auto;
    padding: 8px;
    border: none;
    border-radius: 7px;
    color: white;
  }
</style>
<br>
<br>
<div class="row mt-5">
  <div class="col-12">
    @foreach($user_notifications as $notification)
      <div class="row mt-4 ml-3">
        <div class="col-9">
          <img class="img-fluid img_round" src="{{asset('public/images/notification.png')}}">
          <div class="alertSetting">
            {{$notification->message}}
          </div>
        </div>
        <div class="col-3">
          <p class="pt-2">{{$notification->created_at}}</p>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
