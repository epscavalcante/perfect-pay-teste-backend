@extends('layouts.shopping')

@section('content')
<h5 class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-primary">Order #{{$order['id']}} </span>
    <span class="badge text-bg-success">{{$order['status']}}</span>
</h5>

<ul class="list-group my-3">
    @foreach ($order['items'] as $key => $product)
    <li class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="my-0">{{$product['name']}}</h6>
                    <small class="text-body-secondary">
                        R$ {{$product['price']}} x {{$product['quantity']}}
                    </small>
                </div>
            </div>

            <span class="fw-bold">R$ {{$product['total']}}</span>
        </div>
    </li>
    @endforeach

    <li class="list-group-item text-end">
        <span class="fw-bold">Total: R$ {{$order['total']}}</span>
    </li>
</ul>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Customer</h5>
        <ul class="mb-0">
            <li>Nome: {{$order['customer']['name']}}
            <li>Email: {{$order['customer']['email']}}
            <li>DocumentNumber: {{$order['customer']['documentNumber']}}
        </ul>
    </div>
</div>

@endsection
