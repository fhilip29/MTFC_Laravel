@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4">Gym Membership Plans</h1>
            <p class="lead">Access state-of-the-art gym facilities and equipment</p>
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
                    <h1 class="card-title pricing-card-title text-center">$15<small class="text-muted fw-light">/day</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Full gym access for one day</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Access to locker rooms</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Towel service included</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            @if($userHasActive)
                                <button class="w-100 btn btn-success" disabled>Currently Active</button>
                            @else
                                <form action="{{ route('subscription.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="gym">
                                    <input type="hidden" name="plan" value="daily">
                                    <input type="hidden" name="price" value="15.00">
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
                    <h1 class="card-title pricing-card-title text-center">$80<small class="text-muted fw-light">/month</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited gym access</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Free fitness assessment</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Access to group fitness classes</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Discounts on personal training</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            @if($userHasActive)
                                <button class="w-100 btn btn-success" disabled>Currently Active</button>
                            @else
                                <form action="{{ route('subscription.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="gym">
                                    <input type="hidden" name="plan" value="monthly">
                                    <input type="hidden" name="price" value="80.00">
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
                    <h4 class="my-0 fw-normal">Annual Membership</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <h1 class="card-title pricing-card-title text-center">$800<small class="text-muted fw-light">/year</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Save $160 compared to monthly</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>All monthly benefits included</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Two free personal training sessions</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Exclusive member events</li>
                    </ul>
                    <div class="mt-auto">
                        @if(Auth::check())
                            @if($userHasActive)
                                <button class="w-100 btn btn-success" disabled>Currently Active</button>
                            @else
                                <form action="{{ route('subscription.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="gym">
                                    <input type="hidden" name="plan" value="annual">
                                    <input type="hidden" name="price" value="800.00">
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
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <p>Looking for other memberships?</p>
            <div class="btn-group">
                <a href="{{ route('pricing.boxing') }}" class="btn btn-outline-primary">Boxing</a>
                <a href="{{ route('pricing.muay') }}" class="btn btn-outline-primary">Muay Thai</a>
                <a href="{{ route('pricing.jiu') }}" class="btn btn-outline-primary">Jiu Jitsu</a>
            </div>
        </div>
    </div>
</div>
@endsection
