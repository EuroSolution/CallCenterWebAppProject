@extends('call-center.layout')
@section('title', 'Create Order')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Create Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('callCenter.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Create Order</li>
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
                                        <a class="nav-link active" aria-current="page" href="#product" role="tab" data-toggle="tab">Customer Detail</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#sizes" role="tab" data-toggle="tab">Products</a>
                                    </li>
                                </ul>
                            </div>
                            <form class="order-form" method="post" action="{{route('callCenter.editOrder', $order->id)}}">
                                @csrf
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="product">
                                        <div class="card-body">
                                            @if(Session::has('msg'))
                                                <div class="alert alert-success">{{Session::get('msg')}}</div>
                                            @endif

                                            <div class="form-group">
                                                <label for="restaurant">Select Restaurant</label>
                                                <select class="form-control  @error('restaurant') is-invalid @enderror" name="restaurant" id="restaurant" disabled>
                                                    <option value="">Select</option>
                                                    @foreach($restaurants as $restaurant)
                                                        <option {{($order->restaurant_id == $restaurant->id || old('restaurant')==$restaurant->id) ? 'selected' : ''}} value="{{$restaurant->id}}">{{$restaurant->name ?? ''}}</option>
                                                    @endforeach
                                                </select>
                                                @error('restaurant')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$order->customer_name ?? old('name')}}" placeholder="Name">
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{$order->customer_phone ?? old('phone')}}" placeholder="Phone">
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" name="email" id="email" value="{{$order->customer_email ?? old('email')}}" placeholder="Email">
                                            </div>

                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Address" value="{{$order->address ?? old('address')}}">
                                                @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="city">City</label>
                                                        <input type="text" class="form-control" name="city" id="city" value="{{$order->city ?? old('city')}}" placeholder="City">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="state">State</label>
                                                        <input type="text" class="form-control" name="state" id="state" value="{{$order->state ?? old('state')}}" placeholder="State">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="notes">Notes</label>
                                                <textarea class="form-control" name="notes" id="notes" placeholder="Notes">{{$order->notes ?? old('notes')}}</textarea>
                                            </div>

                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="sizes">
                                        <div class="card-body">
                                            <div class="col-md-12 text-right">
                                                <input type="button" class="btn btn-primary btn-sm" value="Add Product" id="addProductBtn" style="margin-bottom: 10px;"/>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Size</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="add_more_products">
                                                @if(!empty($order->orderItems) && count($order->orderItems))
                                                    @foreach($order->orderItems as $okey => $orderItem)
                                                        @php $okey++; @endphp
                                                        <tr id="row_prod_{{$okey}}" class="row_prod_size">
                                                            <td><select class="form-control productsDd" name="products[]" id="products_{{$okey}}">
                                                                    @foreach($products as $product)
                                                                        <option value="{{$product->id}}" @if($product->id == $orderItem->product_id) selected @endif>{{$product->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control prodSizes" name="sizes[]">
                                                                    @foreach($orderItem->product->productSizes as $psKey => $prodSize)
                                                                        <option value="{{$prodSize->size}}" data-price="{{$prodSize->price}}" @if($orderItem->size == $prodSize->size) selected @endif>
                                                                            {{$prodSize->size}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="number" class="form-control" name="quantities[]" placeholder="Quantity" value="{{$orderItem->quantity}}"></td>
                                                            <td><input type="text" class="form-control numberField price" name="prices[]" placeholder="Price" value="{{$orderItem->price}}"></td>
                                                            <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeProductRow({{$okey}})"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- /.card-body -->

                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                    <a href="{{route('callCenter.orders')}}" class="btn btn-warning btn-md">Cancel</a>
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
        var counter = {{count($order->orderItems) ?? 1}};
        var products = "";
        getProducts({{$order->restaurant_id}})
        function addMoreProduct(productsHtml){
            $("#add_more_products").append(`<tr id="row_prod_${counter}" class="row_prod_size">
        <td><select class="form-control productsDd" name="products[]" id="products_${counter}">${productsHtml}</select></td>
        <td><select class="form-control prodSizes" name="sizes[]"></select></td>
        <td><input type="number" class="form-control" name="quantities[]" placeholder="Quantity" value="1"></td>
        <td><input type="text" class="form-control numberField price" name="prices[]" placeholder="Price"></td>
        <td><input type="button" class="btn btn-danger btn-md" value="-" onclick="removeProductRow(${counter})"></td>
        </tr>`);
            counter++;
        }

        $("#addProductBtn").on('click', function (){
            addMoreProduct(products);
        });

        function removeProductRow(index){
            $('#row_prod_'+index).remove();
        }

        $("#restaurant").on('change', function (){
            let restaurantId = $(this).val();
            getProducts(restaurantId);
        });

        function getProducts(restaurantId){
            $.ajax({
                type: "GET",
                url: "{{route('getProductsByRestaurantId', '')}}/"+restaurantId,
                success:function (data){
                    console.log(data);
                    var html = '<option value="">Select</option>';
                    $.each(data.products, function (i, o){
                        html += '<option value="'+o.id+'">'+o.name+'</option>';
                    })
                    //$(".productsDd").empty().append(html);
                    products = html;
                }
            });
            return products;
        }

        $("body").on('change', '.productsDd', function (){
            var $this = $(this);
            var prodId = $this.val();
            $.ajax({
                type: "GET",
                url: "{{route('getProductSizes','')}}/"+prodId,
                success: function (data){
                    console.log(data);
                    let html = "";
                    $.each(data, function (i,o){
                        html += '<option value="'+o.size+'" data-price="'+o.price+'">'+o.size+'</option>';
                        if (i == 0){
                            $this.parents('tr').find('.price').val(o.price);
                        }
                    });
                    $this.parents('tr').find(".prodSizes").append(html);
                }
            });
        });
        $("body").on('change', '.prodSizes', function (){
            var price = $(this).find(':selected').data('price');
            $(this).parents('tr').find(".price").val(price);
        });

    </script>
@endsection
