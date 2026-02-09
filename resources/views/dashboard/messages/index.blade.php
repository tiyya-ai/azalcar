@extends('layouts.app')

@section('title', 'My Inbox - Azal Cars')

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
    .conv-info {
        flex: 1;
        overflow: hidden;
    }
    .conv-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    .conv-name {
        font-weight: 700;
        font-size: 14px;
        color: #1a1a1a;
    }
    .conv-time {
        font-size: 11px;
        color: #94a3b8;
    }
    .conv-msg {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .unread-badge {
        background: #ef4444;
        color: white;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        margin-left: 8px;
    }
    .chat-placeholder {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fbfbfc;
    }
    .placeholder-content {
        text-align: center;
        color: #94a3b8;
        max-width: 300px;
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
                <h1>Messages</h1>
                <p>Chat with buyers and sellers about your listings.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        <div class="message-inbox-wrapper">
            <!-- Conversation List -->
            <div class="conv-sidebar">
                <div class="conv-sidebar-header">Recent Chats</div>
                
                <div class="conv-list">
                    @forelse($conversations as $conv)
                        @php
                            $isSeller = $conv->seller_id == auth()->id();
                            $otherUser = $isSeller ? $conv->buyer : $conv->seller;
                            $unreadCount = $conv->messages()->where('receiver_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('messages.show', $conv) }}" class="conv-item">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div class="conv-info">
                                <div class="conv-header">
                                    <div class="conv-name text-truncate">{{ $otherUser->name }}</div>
                                    <div class="conv-time">{{ $conv->updated_at->format('H:i') }}</div>
                                </div>
                                <div class="conv-msg text-truncate">
                                    {{ $conv->latestMessage->message ?? 'No messages yet' }}
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="font-size: 11px; color: #94a3b8;" class="text-truncate">
                                        <i class="fas fa-car-side me-1"></i> {{ $conv->listing->title ?? 'Listing' }}
                                    </div>
                                    @if($unreadCount > 0)
                                        <div class="unread-badge">{{ $unreadCount }}</div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div style="padding: 60px 20px; text-align: center; color: #94a3b8;">
                            <i class="far fa-comment-dots" style="font-size: 40px; margin-bottom: 16px; display: block; color: #e2e8f0;"></i>
                            <p>No messages found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Area (Placeholder) -->
            <div class="chat-placeholder">
                <div class="placeholder-content">
                    <i class="far fa-comments" style="font-size: 64px; margin-bottom: 24px; color: #e2e8f0; display: block;"></i>
                    <h3 style="color: #1a1a1a; font-size: 20px; margin-bottom: 12px;">Your Messages</h3>
                    <p>Select a conversation from the list to start chatting.</p>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
