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

<form action="{{route('processPayment', $order['id'])}}" method="POST">

    @csrf
    @if ($order['status'] === 'created')
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Payment </h5>

            <ul class="list-group">
                <li class="list-group-item">
                    <input class="form-check-input me-1" type="radio" name="payment_method" value="pix" id="pixPaymentMethod" {{old('payment_method', 'pix') === 'pix' ? "checked" : null}}>
                    <label class="form-check-label" for="pixPaymentMethod">Pix</label>
                </li>
                <li class="list-group-item">
                    <input class="form-check-input me-1" type="radio" name="payment_method" value="boleto" id="boletoPaymentMethod" {{old('payment_method') === 'boleto' ? "checked" : null}}>
                    <label class="form-check-label" for="boletoPaymentMethod">Boleto</label>
                </li>
                <li class="list-group-item">
                    <input class="form-check-input me-1" type="radio" name="payment_method" value="credit_card" id="creditCardPaymentMethod" {{old('payment_method') === 'credit_card' ? "checked" : null}}>
                    <label class="form-check-label" for="creditCardPaymentMethod">Credit card</label>

                    <section class="my-3">
                        <div class="mb-3 row">
                            <label for="creditCardHolderName" class="col-sm-3 col-form-label">Holder Name</label>
                            <div class="col-sm-9">
                                <input
                                    id="creditCardHolderName"
                                    type="text"
                                    class="form-control @error('credit_card.holder_name') is-invalid @enderror"
                                    name="credit_card[holder_name]"
                                    value="{{old('credit_card.name', fake()->name()) }}"
                                >
                                @error('credit_card.holder_name')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="creditCardNumber" class="col-sm-3 col-form-label">Card Number</label>
                            <div class="col-sm-9">
                                <input
                                    id="creditCardNumber"
                                    type="text"
                                    class="form-control @error('credit_card.number') is-invalid @enderror"
                                    name="credit_card[number]"
                                    aria-describedby="credit_card_more_detail"
                                    value="{{old('credit_card.number', fake()->creditCardNumber()) }}"
                                >
                                <small id="credit_card_more_detail" class="form-text">
                                    Use os números 5184019740373151 ou  4916561358240741 para simular erro
                                </small>
                                @error('credit_card.number')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="creditCardExpirationDate" class="col-sm-3 col-form-label">Expiration</label>
                            <div class="col-sm-9">
                                <input
                                    id="creditCardExpirationDate"
                                    type="text"
                                    placeholder="10/30"
                                    class="form-control @error('credit_card.expiration_date') is-invalid @enderror"
                                    name="credit_card[expiration_date]"
                                    value="{{old('credit_card.expiration_date', fake('pt_BR')->creditCardExpirationDateString()) }}"
                                >
                                @error('credit_card.expiration_date')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="creditCardCvv" class="col-sm-3 col-form-label">CVV</label>
                            <div class="col-sm-9">
                                <input
                                    id="creditCardCvv"
                                    type="text"
                                    placeholder="123"
                                    class="form-control @error('credit_card.cvv') is-invalid @enderror"
                                    name="credit_card[cvv]"
                                    value="{{old('credit_card.cvv', fake('pt_BR')->numerify('###')) }}"
                                >
                                @error('credit_card.cvv')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <hr />

                    <section class="my-3">
                        <div class="mb-3 row">
                            <label for="holderName" class="col-sm-3 col-form-label">Holder Name</label>
                            <div class="col-sm-9">
                                <input
                                    id="holderName"
                                    type="text"
                                    class="form-control @error('holder.name') is-invalid @enderror"
                                    name="holder[name]"
                                    value="{{old('holder.name', fake()->name()) }}"
                                >
                                @error('holder.name')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="holderDocumentNumber" class="col-sm-3 col-form-label">Document number</label>
                            <div class="col-sm-9">
                                <input
                                    id="holderDocumentNumber"
                                    type="text"
                                    class="form-control @error('holder.document_number') is-invalid @enderror"
                                    name="holder[document_number]"
                                    value="{{old('holder.document_number', fake('pt_BR')->cpf(false)) }}"
                                >
                                @error('holder.document_number')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="holderEmail" class="col-sm-3 col-form-label">E-mail</label>
                            <div class="col-sm-9">
                                <input
                                    id="holderEmail"
                                    type="email"
                                    class="form-control @error('holder.email') is-invalid @enderror"
                                    name="holder[email]"
                                    value="{{old('holder.email', fake()->email()) }}"
                                >
                                @error('holder.email')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="holderPhone" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9">
                                <input
                                    id="holderPhone"
                                    type="text"
                                    placeholder="11999999999"
                                    class="form-control @error('holder.phone') is-invalid @enderror"
                                    name="holder[phone]"
                                    value="{{old('holder.phone', fake('pt_BR')->cellphoneNumber(false)) }}"
                                >
                                @error('holder.phone')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="holderPostalCode" class="col-sm-3 col-form-label">Postal code</label>
                            <div class="col-sm-9">
                                <input
                                    id="holderPostalCode"
                                    type="text"
                                    placeholder="78000000"
                                    class="form-control @error('holder.postalCode') is-invalid @enderror"
                                    name="holder[postalCode]"
                                    value="{{old('holder.postalCode', '70150900') }}"
                                >
                                @error('holder.postalCode')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </section>
                </li>
            </ul>
        </div>
    </div>

    <button class="w-100 btn btn-primary btn-lg" type="submit">
        Process Payment
    </button>
    @endif

    @if ($order['status'] === 'waiting_payment')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="h5 mb-0">Payment: {{$order['lastPayment']['paymentMethod']}}</h5>
            <span class="badge">Status</span>
        </div>
        <div class="card-body ">
            @if ($order['lastPayment']['paymentMethod'] === 'pix')
            <div class="row">
                <div class="col-6">
                    <img src="data:image/png;base64, {{$order['lastPayment']['pixQrCode']}}" alt="Pix QR Code" style="max-width: 250px"/>
                </div>
                <div class="col-6">
                    <span class="p-3 d-flex flex-column" >
                        <code>
                        {{$order['lastPayment']['pixCopiaCola']}}
                        <code>
                        <br />
                        <button class="btn btn-dark" style="width: 100%">Copy</button>
                    </span>
                </div>
            </div>
            @endif

            @if ($order['lastPayment']['paymentMethod'] === 'boleto')
            <a href="{{$order['lastPayment']['boletoUrl']}}" target="_blank"> Accessar boleto {{$order['lastPayment']['boletoUrl']}}
            @endif
        </div>
    </div>
    @endif

    @if ($order['status'] === 'paid')
        <div class="card my-3">
            <div class="card-body">
                <h5 class="card-title">Payment</h5>

                <ul class="mb-0">
                    <li> Payment Method: {{$order['lastPayment']['paymentMethod']}}</li>
                    <li> Status: {{$order['lastPayment']['status']}}</li>
                    <li> Card: {{$order['lastPayment']['cardBrand']}} - {{$order['lastPayment']['cardLastDigits']}}
                </ul>
            </div>

        </div>

        <a class="w-100 btn btn-primary btn-lg" href="{{route('cart')}}">
            Go to Shopping
        </a>
    @endif
</form>
@endsection
