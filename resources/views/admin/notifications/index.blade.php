@extends('layouts/contentNavbarLayout')

@section('title', 'Notifications - Admin')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">System /</span> Notifications
</h4>

<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">All Notifications</h5>
      @if($notifications->count() > 0)
        <a href="{{ route('admin.notifications.readAll') }}" class="btn btn-outline-primary btn-sm">
            <i class="ti tabler-mail-opened me-1"></i> Mark All as Read
        </a>
      @endif
  </div>
  <div class="card-body p-0">
    @if($notifications->isEmpty())
        <div class="text-center p-5">
            <i class="ti tabler-bell-off fs-1 text-muted mb-3"></i>
            <p class="mb-0">No notifications found.</p>
        </div>
    @else
        <div class="list-group list-group-flush">
            @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer {{ $notification->read_at ? '' : 'bg-label-primary bg-opacity-10' }}">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle {{ $notification->read_at ? 'bg-label-secondary' : 'bg-label-primary' }}">
                                <i class="ti tabler-bell"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                             <h6 class="mb-0">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                             <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                        @if(isset($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-link p-0">View Details</a>
                        @endif
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-text-secondary rounded-pill btn-sm" title="Delete">
                                <i class="ti tabler-x"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="p-4">
            {{ $notifications->links() }}
        </div>
    @endif
  </div>
</div>
@endsection
