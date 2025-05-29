<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Elite Empire</title>
<!-- core:css -->
<link rel="stylesheet" href="{{asset('public/assets/vendors/core/core.css')}}" type="text/css">
<!-- endinject -->
<!-- plugin css for this page -->
<link rel="stylesheet" href="{{asset('public/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}"
      type="text/css">
<!-- end plugin css for this page -->
<!-- inject:css -->
<link rel="stylesheet" href="{{asset('public/assets/fonts/feather-font/css/iconfont.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/vendors/fontawesome-free/css/all.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/vendors/select2/select2.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/vendors/datatables/dataTables.bootstrap4.css')}}" type="text/css">
<!-- endinject -->
<!-- Layout styles -->
<link rel="stylesheet" href="{{asset('public/assets/css/style.css')}}" type="text/css">

<!-- End layout styles -->
<link rel="shortcut icon" href="{{ asset('public/storage/logo/verum_favicon.png')}}" type="img/png">
<link href="{{asset('public/assets/bootstrap/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" media="screen">

{{-- OWL CAROUSEL --}}
<link rel="stylesheet" href="{{asset('public/assets/vendors/owl.carousel/owl.carousel.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('public/assets/vendors/owl.carousel/owl.theme.default.min.css')}}" type="text/css">
{{-- CK EDITOR 5 --}}
<script src="{{asset('public/js/ckeditor5-build-classic/ckeditor.js')}}"></script>
<script src="{{asset('public/js/ckfinder/ckfinder.js')}}"></script>

<style>
      form label {
  display: inline-block;
  width: 100px;
}

form div {
  margin-bottom: 10px;
}

.error {
  color: red;
  margin-left: 5px;
}

label.error {
  display: inline;
}
</style>

@yield('style')
