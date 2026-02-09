@extends('layouts.app')

@section('title', 'Careers - azalcars')

@section('content')
<div class="careers-page">
    <div class="careers-hero">
        <div class="container">
            <h1>Build the Future of Automotive</h1>
            <p>Join a team of passionate individuals working to transform the way people buy and sell cars.</p>
            <a href="#positions" class="hero-btn">View Open Positions</a>
        </div>
    </div>

    <section class="section reasons-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Join azalcars?</h2>
            </div>
            <div class="reasons-grid">
                <div class="reason-card">
                    <i class="fas fa-rocket"></i>
                    <h3>High Impact</h3>
                    <p>Work on products used by millions of people every month to make important life decisions.</p>
                </div>
                <div class="reason-card">
                    <i class="fas fa-heart"></i>
                    <h3>Great Culture</h3>
                    <p>We value collaboration, transparency, and empathy. We support each other to do our best work.</p>
                </div>
                <div class="reason-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>Modern Tech</h3>
                    <p>We use the latest technologies and tools to build fast, reliable, and beautiful experiences.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="positions" class="section positions-section">
        <div class="container">
            <div class="section-header">
                <h2>Open Positions</h2>
            </div>
            
            <div class="positions-list">
                <!-- Engineering -->
                <div class="department-group">
                    <h3>Engineering</h3>
                    <div class="job-card">
                        <div class="job-info">
                            <h4>Senior Full Stack Developer</h4>
                            <span>Remote • Full-time</span>
                        </div>
                        <a href="#" class="apply-btn">Apply Now</a>
                    </div>
                     <div class="job-card">
                        <div class="job-info">
                            <h4>Backend Engineer (PHP/Laravel)</h4>
                            <span>New York, NY • Full-time</span>
                        </div>
                        <a href="#" class="apply-btn">Apply Now</a>
                    </div>
                </div>

                <!-- Product -->
                <div class="department-group">
                    <h3>Product & Design</h3>
                    <div class="job-card">
                        <div class="job-info">
                            <h4>Product Designer</h4>
                            <span>Remote • Full-time</span>
                        </div>
                        <a href="#" class="apply-btn">Apply Now</a>
                    </div>
                </div>
                
                <!-- Marketing -->
                <div class="department-group">
                    <h3>Marketing</h3>
                    <div class="job-card">
                        <div class="job-info">
                            <h4>Content Marketing Manager</h4>
                            <span>Los Angeles, CA • Full-time</span>
                        </div>
                        <a href="#" class="apply-btn">Apply Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .careers-page {
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
    .careers-hero {
        background: #6041E0;
        color: white;
        padding: 120px 0;
        text-align: center;
    }

    .careers-hero h1 {
        font-size: 56px;
        font-weight: 800;
        margin-bottom: 24px;
        line-height: 1.1;
    }

    .careers-hero p {
        font-size: 20px;
        max-width: 600px;
        margin: 0 auto 40px;
        opacity: 0.9;
    }

    .hero-btn {
        display: inline-block;
        background: white;
        color: #6041E0;
        padding: 16px 32px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hero-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* Reasons */
    .section-header {
        text-align: center;
        margin-bottom: 64px;
    }

    .section-header h2 {
        font-size: 36px;
        font-weight: 800;
    }

    .reasons-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    .reason-card {
        text-align: center;
        padding: 40px;
        background: #f9fafb;
        border-radius: 20px;
    }

    .reason-card i {
        font-size: 40px;
        color: #6041E0;
        margin-bottom: 24px;
    }

    .reason-card h3 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .reason-card p {
        color: #6b7280;
        line-height: 1.6;
    }

    /* Positions */
    .positions-section {
        background: #fdfdfd;
        border-top: 1px solid #f3f4f6;
    }

    .positions-list {
        max-width: 800px;
        margin: 0 auto;
    }

    .department-group {
        margin-bottom: 48px;
    }

    .department-group h3 {
        font-size: 20px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 24px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 12px;
    }

    .job-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .job-card:hover {
        border-color: #6041E0;
        box-shadow: 0 4px 12px rgba(96, 65, 224, 0.05);
    }

    .job-info h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #111827;
    }

    .job-info span {
        font-size: 14px;
        color: #6b7280;
    }

    .apply-btn {
        padding: 8px 24px;
        border: 1px solid #111827;
        border-radius: 50px;
        color: #111827;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }

    .apply-btn:hover {
        background: #111827;
        color: white;
    }

    @media (max-width: 768px) {
        .careers-hero h1 { font-size: 40px; }
        .reasons-grid { grid-template-columns: 1fr; }
        .job-card { flex-direction: column; align-items: flex-start; gap: 16px; }
        .apply-btn { width: 100%; text-align: center; }
    }
</style>
@endsection
