@extends('layouts.base')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4">Boxing Membership Plans</h1>
            <p class="lead">Train with professional boxing coaches in our dedicated boxing facilities</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="my-0 fw-normal">Daily Pass</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">$25<small class="text-muted fw-light">/day</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Single day boxing class</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Gloves and wraps rental included</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Gym access included</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            @if($userHasActive)
                                <button class="w-100 btn btn-success" disabled>Currently Active</button>
                            @else
                                <form action="{{ route('subscription.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="boxing">
                                    <input type="hidden" name="plan" value="daily">
                                    <input type="hidden" name="price" value="25.00">
                                    <button type="submit" class="w-100 btn btn-primary">Subscribe Now</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-100 btn btn-outline-primary">Login to Subscribe</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="my-0 fw-normal">Monthly Membership</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">$150<small class="text-muted fw-light">/month</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited boxing classes</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>One-on-one technique coaching</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Sparring opportunities</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Full gym access included</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            @if($userHasActive)
                                <button class="w-100 btn btn-success" disabled>Currently Active</button>
                            @else
                                <form action="{{ route('subscription.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="boxing">
                                    <input type="hidden" name="plan" value="monthly">
                                    <input type="hidden" name="price" value="150.00">
                                    <button type="submit" class="w-100 btn btn-primary">Subscribe Now</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-100 btn btn-outline-primary">Login to Subscribe</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="my-0 fw-normal">Per-Session Pass</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">$20<small class="text-muted fw-light">/session</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Single boxing session</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Equipment rental available</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Perfect for trying it out</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            <form action="{{ route('subscription.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="boxing">
                                <input type="hidden" name="plan" value="per-session">
                                <input type="hidden" name="price" value="20.00">
                                <button type="submit" class="w-100 btn btn-primary">Buy Now</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="w-100 btn btn-outline-primary">Login to Subscribe</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <p>Looking for other memberships?</p>
            <div class="btn-group">
                <a href="{{ route('pricing.gym') }}" class="btn btn-outline-primary">Gym</a>
                <a href="{{ route('pricing.muay') }}" class="btn btn-outline-primary">Muay Thai</a>
                <a href="{{ route('pricing.jiu') }}" class="btn btn-outline-primary">Jiu Jitsu</a>
            </div>
        </div>
    </div>
</div>
@endsection
