@extends('main')
@section('main_content')
    <div class="container">
    <div class="row">
        <div class="col-md-4">
            <div>
                <div class="head-gr">
                    <div class="h4">My certificates</div>
                    <div>To activate certificate enter the ID</div>
                </div>
                <form id="active_certificate_form" action="certificates/edit" method="POST">
                    @csrf
                    <label for="identity">Certificate ID</label><br>
                    <input type="text" name="identity" class="w-75" required>
                    <div class="w-75 pt-3 text-end">
                        <input type="submit" class="bs-green border-0" value="Activate">
                    </div>
                </form>
            </div>
            @foreach($certificates as $certificate)
                <div class="cert-bl bs-green">
                    <div class="h4">Certificate: #{{$certificate->identity}}</div>
                    <div>For: {{$certificate->user_name . ' ' . $certificate->last_name}}</div>
                    <div>{{$certificate->product_name . ' ' . $certificate->product_count}}</div>
                    <div>A plain: standart</div>
                    <div>The amount: {{$certificate->total_price}}</div>
                </div>
            @endforeach
        </div>
        <div class="col-md-8">
            <div class="head-gr">
                <div class="h4">Present a certificate</div>
                <div>To give a certificate fill in all the details of this person</div>
            </div>
            <div>
                <form id="create_certificate_form" action="certificates" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Name</label><br>
                            <input type="text" name="name" class="w-100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name">Last Name</label><br>
                            <input type="text" name="last_name" class="w-100" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="email">Email</label><br>
                            <input type="text" name="email" class="w-100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_id">Plantation, Year</label><br>
                            <select name="product_id" class="w-100 padding-select" required>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" data-price{{$product->id}}="{{$product->price}}">{{$product->place . ' ' . $product->implementation_time->format('Y')}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="w-100 bg-light" style="height: 160px"></div>
                    <div class="quantity_inner w-50">
                        <input type="text" class="quantity w-81" name="number_of_trees" value="1">
                        <button class="bt_minus" type="button">-</button>
                        <button class="bt_plus" type="button">+</button>
                    </div>
                    <div class="row mar-for-sub">
                        <div class="col-md-6">To be paid: <span class="total_price">{{$products[0]->price}} &#8364;</span></div>
                        <div class="col-md-6 text-end"><input type="submit" class="bs-green border-0" value="Bye a tree now"></div>
                    </div>
                </form>
                @include('errors/list')
            </div>
            <div class="alert-div"></div>
        </div>
    </div>
    </div>
@stop
