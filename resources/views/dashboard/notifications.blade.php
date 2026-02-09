@extends('layouts.app')

@section('title', 'Notifications - Azal Cars')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Notifications</h4>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-bell text-primary fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                    <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                    <small class="text-muted">{{ $notification->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                <div class="ms-3">
                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">View</a>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No notifications yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
