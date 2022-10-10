<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Call Center Panel</title>
    <meta name="csrf-token" content="{{csrf_token()}}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('admin/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{asset('admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{asset('admin/plugins/jqvmap/jqvmap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin/dist/css/adminlte.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{asset('admin/plugins/daterangepicker/daterangepicker.css')}}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{asset('admin/plugins/summernote/summernote-bs4.min.css')}}">

    <link href="{{ asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{asset('admin/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
    @yield('css')
    <style>
        .cell-image{
            border-radius: 1.5rem;
        }

    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Preloader -->
    @if(isset($setting->logo))
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{$setting->logo}}" alt="" height="60" width="60">
    </div>
    @endif

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{url('dashboard')}}" class="nav-link">
                    @if(isset($setting->logo))
                        <img src="{{asset($setting->logo)}}" alt="" class="brand-image img-circle elevation-3" style="opacity: .8; width: 50px">
                    @endif
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block" style="margin-top: 6px;">
                <a href="{{route('callCenter.dashboard')}}" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block" style="margin-top: 6px;">
                <a href="{{route('callCenter.orders')}}" class="nav-link">Orders</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block" style="margin-top: 6px;">
                <a href="#" class="nav-link">Queries</a>
            </li>

        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{route('logout')}}" role="button">
                    <i class="fa fa-lock"></i>
                </a>
            </li>
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">{{count($notifications)}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">{{count($notifications)}} Notifications</span>
                    <div class="dropdown-divider"></div>
                    @foreach($notifications as $notif)
                        <a href="javascript:void(0);" data-href="#"
                           class="dropdown-item notif-row" data-id="{{$notif->id}}">
                            <i class="fas fa-file mr-2"></i> {{ $notif->title }}
                            <span class="float-right text-muted text-sm">{{$notif->created_at->diffForHumans()}}</span>
                            <p class="text-muted text-sm"><span>{{ $notif->data }}</span></p>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">--}}
{{--                    <i class="fas fa-th-large"></i>--}}
{{--                </a>--}}
{{--            </li>--}}
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
     <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{url('dashboard')}}" class="brand-link">
            @if(isset($setting->logo))
                <img src="{{asset($setting->logo)}}" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
            @endif
            <span class="brand-text font-weight-light">{{$setting->title ?? ''}}</span>
        </a>

        <!-- Sidebar -->
        @include('call-center.partials.sidebar')
        <!-- /.sidebar -->
    </aside>

    @yield('content')

    <footer class="main-footer">
        <strong>Copyright &copy; {{date('Y')}} <a href="javascript:void(0);">{{$setting->title??''}}</a>.</strong>
        All rights reserved.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('admin/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('admin/plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{asset('admin/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('admin/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('admin/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('admin/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('admin/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.js')}}"></script>
<script src="{{asset('admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/plugins/toastr/toastr.min.js')}}"></script>
@yield('script')

@if(Session::has('success'))
    <script type="text/javascript">
        toastr.success('{{ Session::get('success')}}');
    </script>
@endif
@if(Session::has('error'))
    <script type="text/javascript">
        toastr.error('{{ Session::get('error')}}');
    </script>
@endif
<script>
    $(".notif-row").on('click', function (){
        var notifId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: "{{route('admin.updateNotification')}}",
            data: {_token: '{{csrf_token()}}', 'id': notifId},
            success:function(resp) {
                console.log(resp);
            }
        });
        window.location.href = $(this).data('href');
    });

    $(document).ready(function() {
        $(".numberField").click(function() {
            var $input = $(this);
            var $inputVal = ($input.val() === '') ? 0 : $input.val();
            var count = parseFloat($inputVal);
            if (count < 0 || isNaN(count)) {
                count = 1;
            }
            $input.val(count);
            return false;
        });
        $(".numberField").focusout(function() {
            var $input = $(this);
            var $inputVal = ($input.val() === '') ? 0 : $input.val();
            var count = parseFloat($inputVal);
            if (count < 0 || isNaN(count)) {
                count = 1;
            }
            $input.val(count);
            return false;
        });
    });
</script>

<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: "AIzaSyAGX31HWcnrxSIycYLwwWpbhyFQTD9SrXc",
        authDomain: "pizzeriaroma-ed2dd.firebaseapp.com",
        projectId: "pizzeriaroma-ed2dd",
        storageBucket: "pizzeriaroma-ed2dd.appspot.com",
        messagingSenderId: "945625842302",
        appId: "1:945625842302:web:2fbb4b5d6d535e517d8e2f"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function () {
            return messaging.getToken()
        }).then(function(token) {

            axios.post("{{ route('fcmToken') }}",{
                _method:"PATCH",
                token
            }).then(({data})=>{
                console.log(data)
            }).catch(({response:{data}})=>{
                console.error(data)
            })

        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();

    messaging.onMessage(function({data:{body,title}}){
        new Notification(title, {body});
    });
</script>
</body>
</html>
