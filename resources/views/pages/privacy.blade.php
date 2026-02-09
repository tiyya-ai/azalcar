@extends('layouts.app')

@section('title', 'Privacy Policy - azalcars')

@section('content')
<div class="policy-page">
    <div class="container">
        <div class="policy-header">
            <h1>Privacy Policy</h1>
            <p>Last updated: January 23, 2026</p>
        </div>
        
        <div class="policy-content">
            <section>
                <h2>1. Introduction</h2>
                <p>At azalcars, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclosure, and safeguard your information when you visit our website.</p>
            </section>

            <section>
                <h2>2. Collection of Your Information</h2>
                <p>We may collect information about you in a variety of ways. The information we may collect on the Site includes:</p>
                <ul>
                    <li><strong>Personal Data:</strong> Personally identifiable information, such as your name, shipping address, email address, and telephone number.</li>
                    <li><strong>Derivative Data:</strong> Information our servers automatically collect when you access the Site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the Site.</li>
                </ul>
            </section>

            <section>
                <h2>3. Use of Your Information</h2>
                <p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the Site to:</p>
                <ul>
                    <li>Create and manage your account.</li>
                    <li>Process your transactions and deliveries.</li>
                    <li>Send you email regarding your account or order.</li>
                    <li>Enable user-to-user communications.</li>
                </ul>
            </section>

            <section>
                <h2>4. Disclosure of Your Information</h2>
                <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
                <p><strong>By Law or to Protect Rights:</strong> If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.</p>
            </section>

            <section>
                <h2>5. Contact Us</h2>
                <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
                <div class="contact-box">
                    <strong>azalcars Support</strong><br>
                    Email: privacy@azalcars.com<br>
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
