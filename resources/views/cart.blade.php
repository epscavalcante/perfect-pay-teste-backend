@extends('layouts.shopping')

@section('content')

<form action="{{route('placeOrder')}}" method="POST">
    @csrf

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title">Your cart</h4>
                <span class="badge bg-primary rounded-pill">{{count($products)}}</span>
            </div>

            <ul class="list-group">
                @foreach ($products as $key => $product)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <h6 class="my-0">{{$product['name']}}</h6>
                                <p class="text-muted mb-0" style="font-size: 12px">Some product description</p>
                            </div>
                        </div>

                        <div class="input-group" style="max-width: 190px;">
                            <span class="input-group-text" style="width: 100%; max-width: 90px;">R$ {{$product['price']}} x</span>
                            <input type="hidden" name="items[{{$key}}][product_id]" value="{{$product['id']}}" >
                            <input type="number" name="items[{{$key}}][quantity]" value="1" min="1" class="form-control" placeholder="Qtd.">
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h4 class="card-title mb-4">Customer</h4>
            <div class="mb-3 row">
                <label for="customerName" class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-9">
                  <input
                        id="customerName"
                        type="text"
                        class="form-control @error('customer.name') is-invalid @enderror"
                        name="customer[name]"
                        value="{{old('customer.name', fake()->name()) }}"
                    >
                </div>
            </div>
            <div class="mb-3 row">
                <label for="customerEmail" class="col-sm-3 col-form-label">E-mail</label>
                <div class="col-sm-9">
                    <input
                        id="customerEmail"
                        type="email"
                        class="form-control @error('customer.email') is-invalid @enderror"
                        name="customer[email]"
                        value="{{old('customer.email', fake()->email()) }}"
                    >
                    @error('customer.email')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="mb-3 row">
                <label for="customerDocumentNumber" class="col-sm-3 col-form-label">Document</label>
                <div class="col-sm-9">
                    <input
                        id="customerDocumentNumber"
                        type="text"
                        class="form-control @error('customer.document_number') is-invalid @enderror"
                        name="customer[document_number]"
                        value="{{old('customer.document_number', fake('pt_BR')->cpf(false)) }}"
                    >
                    @error('customer.document_number')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <button class="w-100 btn btn-primary btn-lg" type="submit">
        Process payment
    </button>
</form>
@endsection
