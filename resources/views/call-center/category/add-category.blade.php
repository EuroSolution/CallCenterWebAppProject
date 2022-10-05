@extends('call-center.layout')
@section('title', (isset($content->id) ?  'Edit' : 'Add').' Category')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Category Form</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Category Form</li>
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
                                <h3 class="card-title">Category</h3>
                            </div>
                            <form class="category-form" method="post" action="{{!empty($content->id)?route('callCenter.editCategory',$content->id):route('callCenter.addCategory')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    @if(Session::has('msg'))
                                        <div class="alert alert-success">{{Session::get('msg')}}</div>
                                    @endif
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Main Category</label>
                                        <select class="form-control @if(Session::has('err')) is-invalid @endif" name="main-category" id="main-category">
                                            <option value="0">Select Category</option>
                                            @foreach($mainCategories as $category)
                                                <option {{(isset($content->parent_id) && $content->parent_id==$category->id)||old('main-category')==$category->id?'selected':''}} value="{{$category->id}}">{{$category->name ?? ''}}</option>
                                            @endforeach
                                        </select>
                                        @if(Session::has('err'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ Session::get('err') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Category Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$content->name?? old('name')}}" placeholder="Category" required>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="row">

                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Category Image</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="file" id="category-image">
                                                        <label class="custom-file-label" for="category-image">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" >
                                            <img src="{{isset($content->image) ? asset($content->image) : asset('admin/dist/img/placeholder.png')}}" alt="" id="img_0" style="height: 150px;width: 150px;">
                                        </div>

                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                    <a href="{{route('callCenter.categories')}}" class="btn btn-warning btn-md">Cancel</a>
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
