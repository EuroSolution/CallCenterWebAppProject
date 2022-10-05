@extends('admin.layout')
@section('title', (isset($content->id) ?  'Edit' : 'Add').' Call Center User')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Call Center User</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Call Center User</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{(isset($content->id) ? 'Edit' : 'Add')}} Call Center User</h3>
                            </div>
                            <form class="category-form" method="post" action="{{!empty($content->id)?route('admin.staffEdit',$content->id):route('admin.addStaff')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    @if(Session::has('msg'))
                                        <div class="alert alert-success">{{Session::get('msg')}}</div>
                                    @endif

                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$content->name?? old('name')}}" placeholder="Name" required>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{$content->email?? old('email')}}" placeholder="Email" required>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{$content->phone?? old('phone')}}" placeholder="Phone Number" required>
                                        @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    @if (isset($content))
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="editCheck" id="editCheck" value="no">
                                            <label for="editCheck">Edit Password?</label>
                                        </div>
                                        <div class="form-group" id="editPassword" style="display: none;">
                                            <label for="password">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" value="{{old('password')}}" placeholder="New Password">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" value="{{old('password')}}" placeholder="password">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    @endif

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                    <a href="{{route('admin.staffMember')}}" class="btn btn-warning btn-md">Cancel</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endsection
    @section('script')
    <script>
        $(document).ready(function(){
            console.log();
            if($('#editCheck').is(":checked")){
                $('#editCheck').val('yes');
                $('#editPassword').fadeIn();
            }else{
                $('#editCheck').val('no');
                $('#editPassword').fadeOut();
            }
        });
        $('#editCheck').change(function(){
           let checkValue =   $('#editCheck').val();
           console.log('before'+checkValue);
           if(checkValue == 'no'){
                $('#editCheck').val('yes');
                $('#editPassword').fadeIn();
           }else{
                $('#editCheck').val('no');
                $('#editPassword').fadeOut();
           }
        });
    </script>
    @endsection
