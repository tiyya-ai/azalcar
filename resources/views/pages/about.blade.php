@extends('layouts.app')

@section('title', 'About Us - azalcars')

@section('content')
<div class="about-page">
    <!-- Hero Section -->
    <div class="about-hero">
        <div class="container">
            <h1>Driving the Future of Car Buying</h1>
            <p>We're on a mission to make buying and selling cars as simple and transparent as possible.</p>
        </div>
    </div>

    <!-- Mission Section -->
    <section class="section mission-section">
        <div class="container">
            <div class="grid-2">
                <div class="mission-content">
                    <span class="badgee">Our Mission</span>
                    <h2>Reinventing the marketplace for a digital world.</h2>
                    <p>At azalcars, we believe that finding your dream car shouldn't be a hassle. We connect buyers and sellers through a seamless, secure, and intuitive platform designed for the modern age.</p>
                    <p>Whether you're looking for a reliable commuter, a luxury cruiser, or a vintage classic, our extensive inventory and powerful search tools make it easy to find exactly what you need.</p>
                </div>
                <div class="mission-image">
                    <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Our Mission">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>1M+</h3>
                    <p>Active Listings</p>
                </div>
                <div class="stat-item">
                    <h3>500k+</h3>
                    <p>Happy Customers</p>
                </div>
                <div class="stat-item">
                    <h3>50+</h3>
                    <p>Cities Covered</p>
                </div>
                <div class="stat-item">
                    <h3>24/7</h3>
                    <p>Support Team</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section values-section">
        <div class="container">
            <div class="section-header">
                <h2>Our Core Values</h2>
                <p>The principles that guide everything we do.</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="icon-box">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Trust & Security</h3>
                    <p>We prioritize the safety of our users with verified listings and secure communication channels.</p>
                </div>
                <div class="value-card">
                    <div class="icon-box">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We're constantly evolving our platform with new features to improve the user experience.</p>
                </div>
                <div class="value-card">
                    <div class="icon-box">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community</h3>
                    <p>We build connections between car enthusiasts, buyers, and sellers across the country.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Scoped Styles for About Page */
    .about-page {
        background: #fff;
    }

    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .section {
        padding: 80px 0;
    }

    /* Hero */
    .about-hero {
        background: linear-gradient(135deg, #111827 0%, #000000 100%);
        color: white;
        padding: 120px 0;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 24px;
        font-family: 'DM Sans', sans-serif;
    }

    .about-hero p {
        font-size: 20px;
        color: #9ca3af;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Mission */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 64px;
        align-items: center;
    }

    .badgee {
        display: inline-block;
        padding: 8px 16px;
        background: #eff6ff;
        color: #6041E0;
        font-weight: 700;
        border-radius: 50px;
        font-size: 14px;
        margin-bottom: 24px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mission-content h2 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 24px;
        line-height: 1.2;
    }

    .mission-content p {
        font-size: 18px;
        color: #4b5563;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .mission-image img {
        width: 100%;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    /* Stats */
    .stats-section {
        background: #6041E0;
        color: white;
        padding: 64px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px;
        text-align: center;
    }

    .stat-item h3 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .stat-item p {
        font-size: 18px;
        opacity: 0.9;
    }

    /* Values */
    .section-header {
        text-align: center;
        margin-bottom: 64px;
    }

    .section-header h2 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .section-header p {
        font-size: 18px;
        color: #6b7280;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    .value-card {
        padding: 40px;
        background: #f9fafb;
        border-radius: 24px;
        transition: transform 0.3s;
    }

    .value-card:hover {
        transform: translateY(-8px);
    }

    .icon-box {
        width: 64px;
        height: 64px;
        background: #ffffff;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6041E0;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .value-card h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .value-card p {
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .about-hero h1 { font-size: 36px; }
        .grid-2 { grid-template-columns: 1fr; gap: 40px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; }
        .values-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
