@extends('layouts/contentNavbarLayout')

@section('title', 'Chat - Admin')

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

    /* Main History Area */
    .app-chat-history {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: #f8f7fa;
    }

    .chat-history-header {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid #dbdade;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-history-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }

    .chat-history-footer {
        padding: 0.75rem 1.5rem;
        background: #fff;
        border-top: 1px solid #dbdade;
    }

    .chat-message {
        display: flex;
        margin-bottom: 1.5rem;
    }

    .chat-message.chat-message-right {
        justify-content: flex-end;
    }

    .chat-message-wrapper {
        max-width: 75%;
        display: flex;
        flex-direction: column;
    }

    .chat-message-right .chat-message-wrapper {
        align-items: flex-end;
    }

    .chat-message-text {
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3);
        margin-bottom: 0.25rem;
        color: #5d596c;
        position: relative;
    }

    .chat-message-right .chat-message-text {
        background-color: #7367f0;
        color: #fff;
        border-bottom-right-radius: 0;
        box-shadow: 0 0.125rem 0.25rem rgba(115, 103, 240, 0.4);
    }

    .chat-message-left .chat-message-text {
        border-bottom-left-radius: 0;
    }

    @media (max-width: 900px) {
        .app-chat-sidebar { width: 300px; }
    }
    @media (max-width: 768px) {
        .app-chat { height: calc(100vh - 6rem); }
        .app-chat-sidebar { display: none; } /* Hide list on mobile when viewing chat */
        
        .chat-history-header .btn-back { display: block !important; }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="py-3 mb-0">
        <span class="text-muted fw-light">Moderation /</span> Messages
    </h4>
</div>

<div class="app-chat">
    <!-- Sidebar -->
    <div class="app-chat-sidebar">
        <div class="chat-sidebar-header">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center me-3">
                     <span class="avatar-initial rounded-circle bg-label-primary p-2">
                        <i class="ti tabler-messages"></i>
                     </span>
                </div>
                <h6 class="mb-0">Chats</h6>
            </div>
        </div>
        
        <ul class="chat-sidebar-list">
            @foreach($conversations as $conv)
            <li>
                <a href="{{ route('admin.messages.show', $conv->id) }}" class="chat-contact-item {{ $conv->id === $conversation->id ? 'active' : '' }}">
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
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
             <li class="p-2 text-center border-top">
                {{ $conversations->links() }}
            </li>
        </ul>
    </div>

    <!-- Main Chat Area (Active Chat) -->
    <div class="app-chat-history">
        <!-- Header -->
        <div class="chat-history-header">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.messages.index') }}" class="btn btn-icon btn-text-secondary rounded-pill me-2 btn-back d-none d-md-none">
                    <i class="ti tabler-arrow-left"></i>
                </a>
                <div class="avatar avatar-md me-3">
                    @if(!empty($conversation->listing->images))
                        <img src="{{ $conversation->listing->images[0] }}" alt="" class="rounded-circle object-fit-cover">
                    @else
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="ti tabler-message-2"></i>
                        </span>
                    @endif
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $conversation->listing->title ?? 'Direct Message' }}</h6>
                    <small class="text-muted">
                        Buyer: {{ $conversation->buyer->name ?? 'Unknown' }} | Seller: {{ $conversation->seller->name ?? 'Unknown' }}
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($conversation->listing)
                <a href="{{ route('listings.show', $conversation->listing->slug) }}" target="_blank" class="btn btn-icon btn-text-secondary rounded-pill" title="View Listing">
                    <i class="ti tabler-external-link"></i>
                </a>
                @endif
                <form action="{{ route('admin.messages.destroy', $conversation->id) }}" method="POST" onsubmit="return confirm('Delete this conversation history?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-text-danger rounded-pill" title="Delete">
                        <i class="ti tabler-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Body -->
        <div class="chat-history-body" id="chat-history">
             @foreach($conversation->messages as $msg)
                @php
                    $isMe = $msg->sender_id === auth()->id();
                    $isInbound = !$isMe;
                    $senderName = $msg->sender->name ?? 'Unknown';
                    $roleLabel = '';
                    if ($msg->sender_id === $conversation->seller_id) $roleLabel = '(Seller)';
                    elseif ($msg->sender_id === $conversation->buyer_id) $roleLabel = '(Buyer)';
                    else $roleLabel = '(Admin)';
                @endphp
                
                <div class="chat-message {{ $isMe ? 'chat-message-right' : 'chat-message-left' }}">
                    <div class="chat-message-wrapper">
                         @if(!$isMe)
                         <span class="text-muted small mb-1 ms-1">{{ $senderName }} <span class="fw-bold" style="font-size: 0.75em">{{ $roleLabel }}</span></span>
                         @endif
                         
                        <div class="chat-message-text">
                            <p class="mb-0">{{ $msg->message }}</p>
                        </div>
                        <div class="text-muted small mt-1 {{ $isMe ? 'text-end me-1' : 'ms-1' }}">
                            {{ $msg->created_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
             @endforeach
        </div>

        <!-- Footer -->
        <div class="chat-history-footer">
             <form action="{{ route('admin.messages.reply', $conversation->id) }}" method="POST">
                @csrf
                <div class="d-flex align-items-center gap-2 mb-2">
                     <small>To:</small>
                     @php
                        $autoRecipientId = null;
                        if ($conversation->buyer_id === auth()->id()) $autoRecipientId = $conversation->seller_id;
                        elseif ($conversation->seller_id === auth()->id()) $autoRecipientId = $conversation->buyer_id;
                     @endphp
                     <select class="form-select form-select-sm" name="recipient_id" style="width: auto; max-width: 300px;" required>
                         @if(!$autoRecipientId)
                            <option value="" selected disabled>Select Recipient...</option>
                         @endif
                         @if($conversation->buyer && $conversation->buyer_id !== auth()->id())
                            <option value="{{ $conversation->buyer_id }}" {{ $autoRecipientId == $conversation->buyer_id ? 'selected' : '' }}>{{ $conversation->buyer->name }} (Buyer)</option>
                         @endif
                         @if($conversation->seller && $conversation->seller_id !== auth()->id())
                            <option value="{{ $conversation->seller_id }}" {{ $autoRecipientId == $conversation->seller_id ? 'selected' : '' }}>{{ $conversation->seller->name }} (Seller)</option>
                         @endif
                     </select>
                </div>
                
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Type your message here..." required>
                    <button class="btn btn-primary" type="submit">
                        <i class="ti tabler-send me-1"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var objDiv = document.getElementById("chat-history");
        if(objDiv) {
            objDiv.scrollTop = objDiv.scrollHeight;
        }
    });
</script>
@endsection
