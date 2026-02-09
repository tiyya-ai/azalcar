@extends('layouts.app')

@section('title', 'Messages - azal Cars')

@section('content')
<div class="container">
    <div class="profile-layout">
        <!-- Profile Sidebar (Simplified for messages) -->
        <aside class="profile-sidebar">
            <nav class="profile-menu">
                <a href="{{ route('dashboard') }}" class="menu-item">
                    <i class="far fa-file-alt"></i> My ads
                </a>
                <a href="{{ route('messages.index') }}" class="menu-item active">
                    <i class="far fa-comment"></i> Messages
                </a>
                <a href="#" class="menu-item">
                    <i class="far fa-heart"></i> Favorites
                </a>
                <a href="#" class="menu-item">
                    <i class="far fa-bell"></i> Notifications
                </a>
                <div class="menu-divider"></div>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
        </aside>

        <!-- Messages Content -->
        <div class="profile-content">
            <h1 class="page-title mb-24">Messages</h1>

            <div class="messages-container">
                <!-- Chat List -->
                <div class="chat-list">
                    @forelse($conversations as $conversation)
                    <div class="chat-item {{ $loop->first ? 'active' : '' }}">
                        <div class="chat-avatar">
                            @if($conversation->listing && $conversation->listing->images)
                            <img src="{{ $conversation->listing->main_image ?? $conversation->listing->images[0] }}" alt="{{ $conversation->listing->title }}">
                            @else
                            <div class="avatar-init">{{ substr($conversation->otherUser->name, 0, 1) }}</div>
                            @endif
                        </div>
                        <div class="chat-info">
                            <div class="chat-name-row">
                                <span class="chat-name">{{ $conversation->otherUser->name }}</span>
                                <span class="chat-time">{{ $conversation->lastMessage ? $conversation->lastMessage->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <div class="chat-title">{{ $conversation->listing ? $conversation->listing->title : 'General' }}</div>
                            <div class="chat-last-msg">{{ $conversation->lastMessage ? Str::limit($conversation->lastMessage->message, 50) : 'No messages yet' }}</div>
                        </div>
                    </div>
                    @empty
                    <p>No conversations found.</p>
                    @endforelse
                </div>

                <!-- Chat View -->
                <div class="chat-view">
                    @if($currentConversation ?? null)
                    <div class="chat-header">
                        <div class="header-user">
                            <div class="chat-avatar-sm">
                                @if($currentConversation->listing && $currentConversation->listing->images)
                                <img src="{{ $currentConversation->listing->main_image ?? $currentConversation->listing->images[0] }}" alt="{{ $currentConversation->listing->title }}">
                                @else
                                <div class="avatar-init">{{ substr($currentConversation->otherUser->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="header-info">
                                <div class="header-name">{{ $currentConversation->otherUser->name }}</div>
                                <div class="header-status">Online</div>
                            </div>
                        </div>
                        <button class="btn btn-light btn-sm">Call</button>
                    </div>

                    <div class="chat-messages">
                        @foreach($currentConversation->messages as $message)
                        <div class="msg-group {{ $message->sender_id === auth()->id() ? 'msg-self' : 'msg-other' }}">
                            <div class="msg-bubble">
                                {{ $message->message }}
                                <span class="msg-time">{{ $message->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="chat-input-area">
                        <button class="icon-btn"><i class="fas fa-paperclip"></i></button>
                        <form action="{{ route('messages.send', $currentConversation) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="text" name="message" placeholder="Write a message..." class="chat-input" required>
                            <button type="submit" class="icon-btn send-btn"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                    @else
                    <div class="chat-placeholder">
                        <p>Select a conversation to start chatting</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection