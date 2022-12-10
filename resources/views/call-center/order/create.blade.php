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
                            <form class="order-form" method="post" action="{{route('callCenter.addOrder')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="product">
                                        <div class="card-body">
                                            @if(Session::has('msg'))
                                                <div class="alert alert-success">{{Session::get('msg')}}</div>
                                            @endif

                                                <div class="form-group">
                                                    <label for="restaurant">Select Restaurant</label>
                                                    <select class="form-control  @error('restaurant') is-invalid @enderror" name="restaurant" id="restaurant">
                                                        <option value="">Select</option>
                                                        @foreach($restaurants as $restaurant)
                                                            <option {{(old('restaurant')==$restaurant->id) ? 'selected' : ''}} value="{{$restaurant->id}}">{{$restaurant->name ?? ''}}</option>
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
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{old('name')}}" placeholder="Name">
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{old('phone')}}" placeholder="Phone">
                                                    @error('phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{old('email')}}" placeholder="Email">
                                                </div>

                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Address" value="{{old('address')}}">
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
                                                            <input type="text" class="form-control" name="city" id="city" value="{{old('city')}}" placeholder="City">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="state">State</label>
                                                            <input type="text" class="form-control" name="state" id="state" value="{{old('state')}}" placeholder="State">
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="form-group">
                                                <label for="notes">Notes</label>
                                                <textarea class="form-control" name="notes" id="notes" placeholder="Notes">{{old('notes')}}</textarea>
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
        var counter = 1;
        var products = "";
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
        });

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

        $("#phone").keyup(function (){
            let val = $(this).val();
            let length = val.length
            if (length > 4){
                $.ajax({
                    type:'GET',
                    url: '{{route('callCenter.searchOrder')}}',
                    data: 'phone='+val,
                    success: function (resp){
                        console.log(resp);
                        if (resp.status === true){
                            $("#name").val(resp.data.customer_name);
                            $("#phone").val(resp.data.customer_phone);
                            $("#email").val(resp.data.customer_email);
                            $("#address").val(resp.data.address);
                            $("#city").val(resp.data.city);
                            $("#state").val(resp.data.state);
                        }
                    }
                })
            }
        })
    </script>
@endsection
