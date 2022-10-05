@extends('call-center.layout')
@section('title', 'Edit Product')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Product</li>
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
                        <div class="card card-secondary">
                            <div class="card-header">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" aria-current="page" href="#product" role="tab" data-toggle="tab">Product Detail</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#sizes" role="tab" data-toggle="tab">Product Sizes</a>
                                    </li>
{{--                                    <li class="nav-item">--}}
{{--                                        <a class="nav-link" href="#attributes" role="tab" data-toggle="tab">Product Attributes</a>--}}
{{--                                    </li>--}}
{{--                                    <li class="nav-item">--}}
{{--                                        <a class="nav-link" href="#addons" role="tab" data-toggle="tab">Product Addons</a>--}}
{{--                                    </li>--}}
                                </ul>
                            </div>
                            <form class="category-form" method="post" action="{{route('callCenter.editProduct', $content->id)}}" enctype="multipart/form-data">
                                @csrf
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="product">
                                        <div class="card-body">
                                            @if(Session::has('msg'))
                                                <div class="alert alert-success">{{Session::get('msg')}}</div>
                                            @endif
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Select Category</label>
                                                <select class="form-control  @error('name') is-invalid @enderror" name="category" id="category">
                                                    <option value="">Select</option>
                                                    @foreach($categories as $category)
                                                        <option {{(old('category') == $category->id || $content->category_id == $category->id) ? 'selected' : ''}} value="{{$category->id}}">{{$category->name ?? ''}}</option>
                                                    @endforeach
                                                </select>
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{old('name') ?? $content->name ?? ''}}" placeholder="Product Name" required>
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="type">Type</label>
                                                <input type="text" class="form-control @error('type') is-invalid @enderror" name="type" id="type" value="{{old('type') ?? $content->type ?? ''}}" placeholder="Product Type" required>
                                                @error('type')
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="category">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" placeholder="Description" required>{{old('description') ?? $content->description ?? ''}}</textarea>
                                                @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="price">Price</label>
                                                <input type="text" step="0.01" class="form-control numberField" name="price" id="price" value="{{old('price') ?? $content->price ?? 0.00}}" placeholder="Product Price">
                                            </div>
                                            <div class="row">

                                                <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label for="exampleInputFile">Product Image</label>
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
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="sizes">
                                        <div class="card-body">
                                            <div class="col-md-12 text-right">
                                                <input type="button" class="btn btn-primary btn-sm" value="Add Size" onclick="addMoreSizes()" style="margin-bottom: 10px;"/>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                @php $sizeCount = 0; @endphp
                                                <tbody id="add_more_sizes">
                                                @if($content->productSizes != null && !empty($content->productSizes))
                                                    @foreach($content->productSizes as $sKey => $size)
                                                        @php $sizeCount++; @endphp
                                                        <tr id="row_size_{{$sKey}}" class="row_prod_size">
                                                            <td><input type="text" class="form-control" name="sizes[{{$sKey}}]" placeholder="Size" value="{{$size->size ?? ''}}"></td>
                                                            <td><input type="text" class="form-control numberField" name="size_prices[{{$sKey}}]" placeholder="Price" value="{{$size->price ?? '0.00'}}"></td>
                                                            <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeSizeRow({{$sKey}})"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="attributes">
                                        <div class="card-body">
                                            <div class="col-md-12 text-right">
                                                <input type="button" class="btn btn-primary btn-sm" value="Add Attribute" onclick="addMoreAttributes()" style="margin-bottom: 10px;"/>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Attribute</th>
                                                    <th>Attribute Item</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="add_more_attributes">
                                                @php $attrCount = 0; @endphp
                                                @if($content->productAttributes != null && !empty($content->productAttributes))
                                                    @foreach($content->productAttributes as $atKey => $productAttribute)
                                                        @php $attrCount++; @endphp
                                                        <tr id="row_attr_{{$atKey}}" class="row_prod_attr">
                                                            <td><select id="attribute_id_{{$atKey}}" class="form-control" name="attributes[]" onchange="getAttributeValues({{$atKey}}, this.value)">
                                                                    <option value="">Select</option>
                                                                    @foreach($attributes as $attribute)
                                                                        <option value="{{$attribute->id}}" @if($attribute->id == $productAttribute->attribute_id) selected @endif>{{$attribute->name}}</option>
                                                                    @endforeach
                                                                </select></td>
                                                            <td><select id="attribute_item_id_{{$atKey}}" class="form-control" name="attribute_items[]">
                                                                    @foreach($attributes as $attribute2)
                                                                        @foreach($attribute2->attributeItems as $attItem)
                                                                            <option value="{{$attItem->id}}" @if($attItem->id == $productAttribute->attribute_item_id) selected @endif>{{$attItem->name}}</option>
                                                                        @endforeach
                                                                    @endforeach
                                                                </select></td>
                                                            <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeAttributesRow({{$atKey}})"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="addons">
                                        <div class="card-body">
                                            <div class="col-md-12 text-right">
                                                <input type="button" class="btn btn-primary btn-sm" value="Add Addon" onclick="addMoreAddon()" style="margin-bottom: 10px;"/>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Addon</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="add_more_addons">
                                                @php $addonCount = 0; @endphp
                                                @if($content->addons != null && !empty($content->addons))
                                                    @foreach($content->addons as $aKey => $addon)
                                                        @php $addonCount++; @endphp
                                                        <tr id="row_addon_{{$aKey}}" class="row_prod_addon">
                                                            <td><select class="form-control" name="addons[{{$aKey}}]">
                                                                    <option value="">Select</option>
                                                                    @foreach($addonItems as $addonItem)
                                                                        <option value="{{$addonItem->id}}" @if($addonItem->id == $addon->addon_item_id) selected @endif>{{$addonItem->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control numberField" name="addon_prices[{{$aKey}}]" placeholder="Price" value="{{$addon->price??'0.00'}}"></td>
                                                            <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeAddonRow({{$aKey}})"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                    <a href="{{route('callCenter.products')}}" class="btn btn-warning btn-md">Cancel</a>
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
        var counter = {{$sizeCount??1}};
        function addMoreSizes(){
            $("#add_more_sizes").append(`<tr id="row_size_${counter}" class="row_prod_size">
                <td><input type="text" class="form-control" name="sizes[]" placeholder="Size"></td>
                <td><input type="text" class="form-control numberField" name="size_prices[]" placeholder="Price"></td>
                <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeSizeRow(${counter})"></td>
            </tr>`);
            counter++;
        }
        function removeSizeRow(index){
            $('#row_size_'+index).remove();
        }
    </script>
@endsection
