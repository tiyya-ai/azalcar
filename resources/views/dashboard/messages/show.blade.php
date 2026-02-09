@extends('layouts.app')

@section('title', 'Chat - Azal Cars')

@push('styles')
<style>
    /* Dashboard specific overrides */
    body { background-color: #F8F9FB !important; overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    .main-content { margin-top: 0 !important; height: 100vh !important; width: 100vw !important; max-width: 100vw !important; padding: 0 !important; }
    .modern-dashboard-layout { 
        display: grid; 
        grid-template-columns: 260px 1fr; 
        gap: 0; 
        height: 100vh; 
        width: 100vw; 
        overflow: hidden; 
    }
    .modern-dashboard-content { 
        padding: 40px; 
        background: #F8F9FB; 
        overflow-y: auto; 
        height: 100vh; 
        position: relative;
    }
    .navbar { display: none !important; }
    .footer { display: none !important; }
    


    /* Messaging Specific Styles */
    .message-inbox-wrapper {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        height: calc(100vh - 180px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .conv-sidebar {
        width: 300px;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
    }
    .conv-sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 800;
        color: #1a1a1a;
        font-size: 15px;
    }
    .conv-list {
        flex: 1;
        overflow-y: auto;
    }
    .conv-item {
        display: flex;
        gap: 12px;
        padding: 16px 20px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.2s;
        position: relative;
    }
    .conv-item:hover {
        background: #f8fafc;
    }
    .conv-item.active {
        background: #f0f4ff;
        border-left: 3px solid #6041E0;
    }
    .avatar-circle {
        width: 44px;
        height: 44px;
        background: #6041E0;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
        font-size: 15px;
    }
    
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
    }
    .chat-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-messages-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: #fbfbfc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .message-row {
        display: flex;
        width: 100%;
    }
    .message-row.me { justify-content: flex-end; }
    .message-row.other { justify-content: flex-start; }
    
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
    }
    .message-row.me .message-bubble {
        background: #6041E0;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message-row.other .message-bubble {
        background: #f1f5f9;
        color: #1a1a1a;
        border-bottom-left-radius: 4px;
    }
    .message-time {
        font-size: 10px;
        margin-top: 4px;
        opacity: 0.7;
    }
    .chat-input-area {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid #f1f5f9;
    }
    .chat-form {
        display: flex;
        gap: 12px;
    }
    .chat-input {
        flex: 1;
        padding: 12px 20px;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        outline: none;
        font-size: 14px;
    }
    .chat-send-btn {
        background: #6041E0;
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
    }
</style>
@endpush

@section('content')
<div class="modern-dashboard-layout">
    <!-- Left Sidebar -->
    @include('dashboard.sidebar')

    <!-- Main Content Area -->
    <div class="modern-dashboard-content">
        <!-- Modern Header -->
        <div class="modern-welcome-section">
            <div class="modern-welcome-text">
                <h1>Chat</h1>
                <p>Private conversation about your marketplace activity.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        <div class="message-inbox-wrapper">
            <!-- Conversation List (Left) -->
            <div class="conv-sidebar d-none d-md-flex">
                <div class="conv-sidebar-header">Recent Chats</div>
                <div class="conv-list">
                    @php
                        $conversations = \App\Models\Conversation::where('buyer_id', auth()->id())
                            ->orWhere('seller_id', auth()->id())
                            ->latest('updated_at')
                            ->get();
                    @endphp
                    @foreach($conversations as $c)
                        @php
                            $isSel = $c->seller_id == auth()->id();
                            $otherU = $isSel ? $c->buyer : $c->seller;
                        @endphp
                        <a href="{{ route('messages.show', $c) }}" class="conv-item {{ $c->id == $conversation->id ? 'active' : '' }}">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($otherU->name, 0, 1)) }}
                            </div>
                            <div class="conv-info">
                                <div class="conv-header">
                                    <div class="conv-name text-truncate">{{ $otherU->name }}</div>
                                </div>
                                <div style="font-size: 11px; color: #94a3b8;" class="text-truncate">{{ $c->listing->title ?? 'Listing' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Active Chat Area -->
            <div class="chat-area">
                @php
                    $isSeller = $conversation->seller_id == auth()->id();
                    $otherUser = $isSeller ? $conversation->buyer : $conversation->seller;
                @endphp
                <div class="chat-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <a href="{{ route('messages.index') }}" class="d-md-none" style="color:#64748b;"><i class="fas fa-arrow-left"></i></a>
                        <div class="avatar-circle" style="width:40px; height:40px; font-size:14px;">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 15px;">{{ $otherUser->name }}</div>
                            <div style="font-size:11px; color:#94a3b8;">
                                @if($conversation->listing)
                                    Re: <a href="{{ route('listings.show', $conversation->listing->slug) }}" style="color:#6041E0; font-weight:700;">{{ $conversation->listing->title }}</a>
                                @else
                                    <span>Listing removed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="font-weight: 800; color: #1a1a1a; font-size: 16px;">
                        @if($conversation->listing)
                            ₽ {{ number_format($conversation->listing->price) }}
                        @endif
                    </div>
                </div>

                <!-- Messages List -->
                <div id="message-body" class="chat-messages-body">
                    @foreach($conversation->messages as $msg)
                        @php $isMe = $msg->sender_id == auth()->id(); @endphp
                        <div class="message-row {{ $isMe ? 'me' : 'other' }}">
                            <div class="message-bubble">
                                {{ $msg->message }}
                                <div class="message-time" style="text-align: {{ $isMe ? 'right' : 'left' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Input Area -->
                <div class="chat-input-area">
                    <form action="{{ route('messages.reply', $conversation) }}" method="POST" class="chat-form">
                        @csrf
                        <input type="text" name="message" placeholder="Type your message..." class="chat-input" required autofocus autocomplete="off">
                        <button type="submit" class="chat-send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    @stack('scripts')
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.getElementById('message-body');
        if(body) body.scrollTop = body.scrollHeight;
    });
</script>
@endsection
