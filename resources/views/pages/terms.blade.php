@extends('layouts.app')

@section('title', 'Terms of Service - azalcars')

@section('content')
<div class="policy-page">
    <div class="container">
        <div class="policy-header">
            <h1>Terms of Service</h1>
            <p>Last updated: January 23, 2026</p>
        </div>
        
        <div class="policy-content">
            <section>
                <h2>1. Agreement to Terms</h2>
                <p>These Terms of Service constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and azalcars ("we," "us" or "our"), concerning your access to and use of the azalcars website as well as any other media form, media channel, mobile website or mobile application related, linked, or otherwise connected thereto (collectively, the "Site").</p>
            </section>

            <section>
                <h2>2. Intellectual Property Rights</h2>
                <p>Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics on the Site (collectively, the "Content") and the trademarks, service marks, and logos contained therein (the "Marks") are owned or controlled by us or licensed to us, and are protected by copyright and trademark laws.</p>
            </section>

            <section>
                <h2>3. User Representations</h2>
                <p>By using the Site, you represent and warrant that:</p>
                <ul>
                    <li>All registration information you submit will be true, accurate, current, and complete.</li>
                    <li>You will maintain the accuracy of such information and promptly update such registration information as necessary.</li>
                    <li>You have the legal capacity and you agree to comply with these Terms of Service.</li>
                    <li>You are not a minor in the jurisdiction in which you reside.</li>
                </ul>
            </section>

            <section>
                <h2>4. Prohibited Activities</h2>
                <p>You may not access or use the Site for any purpose other than that for which we make the Site available. The Site may not be used in connection with any commercial endeavors except those that are specifically endorsed or approved by us.</p>
            </section>

            <section>
                <h2>5. Contact Us</h2>
                <p>To resolve a complaint regarding the Site or to receive further information regarding use of the Site, please contact us at:</p>
                <div class="contact-box">
                    <strong>azalcars Legal</strong><br>
                    Email: legal@azalcars.com<br>
                    Phone: +1 555 123 4567
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    .policy-page {
        background: #fff;
        padding: 80px 0;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .policy-header {
        text-align: center;
        margin-bottom: 64px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 40px;
    }

    .policy-header h1 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 16px;
        color: #111827;
    }

    .policy-header p {
        color: #6b7280;
        font-size: 16px;
    }

    .policy-content section {
        margin-bottom: 48px;
    }

    .policy-content h2 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #111827;
    }

    .policy-content p {
        font-size: 16px;
        line-height: 1.8;
        color: #4b5563;
        margin-bottom: 16px;
    }

    .policy-content ul {
        list-style-type: disc;
        padding-left: 24px;
        margin-bottom: 24px;
    }

    .policy-content li {
        margin-bottom: 12px;
        color: #4b5563;
        line-height: 1.6;
    }

    .contact-box {
        background: #f9fafb;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-top: 16px;
        color: #374151;
        line-height: 1.6;
    }
</style>
@endsection
