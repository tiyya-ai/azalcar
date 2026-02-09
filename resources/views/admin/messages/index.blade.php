@extends('layouts/contentNavbarLayout')

@section('title', 'Messages - Admin')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-style')
<style>
    .app-chat {
        position: relative;
        height: calc(100vh - 11rem);
        min-height: 500px;
        display: flex;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
        overflow: hidden;
    }

    /* Sidebar */
    .app-chat-sidebar {
        width: 370px;
        height: 100%;
        border-right: 1px solid #dbdade;
        display: flex;
        flex-direction: column;
    }

    .chat-sidebar-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dbdade;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-sidebar-list {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .chat-contact-item {
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
        text-decoration: none;
        color: inherit;
    }

    .chat-contact-item:hover {
        background-color: #f8f8f9;
    }

    .chat-contact-item.active {
        background: linear-gradient(72.47deg, #7367f0 22.16%, rgba(115, 103, 240, 0.7) 76.47%);
        color: #fff !important;
    }

    .chat-contact-item.active h6, 
    .chat-contact-item.active p, 
    .chat-contact-item.active small,
    .chat-contact-item.active .text-muted {
        color: #fff !important;
    }

    .chat-contact-info {
        flex: 1;
        min-width: 0;
        margin-left: 1rem;
    }

    .chat-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }
    
    .chat-avatar {
        position: relative;
    }
    
    .chat-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    
    .status-online { background-color: #28c76f; }
    .status-offline { background-color: #82868b; }

    /* Main History Area */
    .app-chat-history {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: #f8f7fa;
    }

    .chat-history-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 2rem;
    }

    .chat-history-icon {
        width: 80px;
        height: 80px;
        background: rgba(115, 103, 240, 0.1);
        color: #7367f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2.5rem;
    }

    @media (max-width: 768px) {
        .app-chat { height: calc(100vh - 6rem); }
        .app-chat-sidebar { width: 100%; border-right: none; }
        .app-chat-history { display: none; }
        
        .app-chat.viewing-conversation .app-chat-sidebar { display: none; }
        .app-chat.viewing-conversation .app-chat-history { display: flex; }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="py-3 mb-0">
        <span class="text-muted fw-light">Moderation /</span> Messages
    </h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">
        <i class="ti tabler-plus me-1"></i> New Message
    </button>
</div>

<div class="app-chat">
    <!-- Sidebar -->
    <div class="app-chat-sidebar">
        <div class="chat-sidebar-header">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                     <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="ti tabler-messages"></i>
                     </span>
                </div>
                <h6 class="mb-0">Chats</h6>
            </div>
            <!-- Search could go here -->
        </div>
        
        <ul class="chat-sidebar-list">
            @forelse($conversations as $conv)
            <li>
                <a href="{{ route('admin.messages.show', $conv->id) }}" class="chat-contact-item">
                    <div class="chat-avatar">
                        @if(!empty($conv->listing->images))
                             <img src="{{ $conv->listing->images[0] }}" alt="" class="rounded-circle object-fit-cover" width="38" height="38">
                        @else
                             <span class="avatar-initial rounded-circle bg-label-secondary" style="width: 38px; height: 38px;">
                                {{ substr($conv->listing->title ?? 'U', 0, 1) }}
                             </span>
                        @endif
                    </div>
                    <div class="chat-contact-info">
                        <div class="chat-meta">
                            <h6 class="mb-0 text-truncate" style="max-width: 140px;">{{ $conv->listing->title ?? 'Direct Message' }}</h6>
                            <small class="text-muted">{{ $conv->updated_at->shortAbsoluteDiffForHumans() }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0 small text-truncate text-muted" style="max-width: 180px;">
                                {{ $conv->buyer->name ?? 'Unknown' }} & {{ $conv->seller->name ?? 'Unknown' }}
                            </p>
                            @if($conv->messages_count > 0)
                                <span class="badge badge-center rounded-pill bg-primary w-px-20 h-px-20 font-size-10">{{ $conv->messages_count }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </li>
            @empty
            <li class="p-4 text-center text-muted">No conversations found.</li>
            @endforelse
            
            <li class="p-2 text-center border-top">
                {{ $conversations->links() }}
            </li>
        </ul>
    </div>

    <!-- Main Chat Area (Empty State) -->
    <div class="app-chat-history">
        <div class="chat-history-empty">
            <div class="chat-history-icon">
                <i class="ti tabler-message-circle-2"></i>
            </div>
            <h4>Select a conversation</h4>
            <p class="text-muted">Choose a conversation from the sidebar to view details <br>or start a new message.</p>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                Start Conversation
            </button>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">Start New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.messages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="user_id" class="form-label">Recipient</label>
                            <select id="user_id" name="user_id" class="select2 form-select" required>
                                <option value="">Select a user...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="4" placeholder="Type your message..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is loaded (required for select2)
    if (typeof $ !== 'undefined') {
        $('#newMessageModal').on('shown.bs.modal', function () {
            $('#user_id').select2({
                dropdownParent: $('#newMessageModal'),
                placeholder: 'Search for a user...',
                ajax: {
                    url: '{{ route("admin.messages.search-users") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(user) {
                                return {
                                    id: user.id,
                                    text: user.name + ' (' + user.email + ')'
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
        });
    }
});
</script>
@endsection
