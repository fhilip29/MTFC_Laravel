@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Subscriptions</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($subscriptions->isEmpty())
                <div class="alert alert-info">
                    You don't have any subscriptions yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{ ucfirst($subscription->type) }}</td>
                                    <td>{{ ucfirst($subscription->plan) }}</td>
                                    <td>${{ number_format($subscription->price, 2) }}</td>
                                    <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($subscription->end_date)
                                            {{ $subscription->end_date->format('M d, Y') }}
                                        @else
                                            Per-session
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->isActive())
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->isActive())
                                            <form action="{{ route('subscription.cancel', $subscription->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this subscription?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('pricing') }}" class="btn btn-primary">Browse Memberships</a>
            </div>
        </div>
    </div>
</div>
@endsection 