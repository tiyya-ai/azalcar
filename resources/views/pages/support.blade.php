@extends('layouts.app')

@section('title', 'Help Center - azal Cars')

@section('content')
<div class="container py-48">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 32px; text-align: center;">How can we help you today?</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 48px;">
            <div style="background: white; padding: 32px; border-radius: 20px; border: 1px solid var(--border-color); text-align: center;">
                <i class="fas fa-shopping-cart" style="font-size: 32px; color: var(--azal-blue); margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 12px;">Buying on Azal Cars</h3>
                <p style="color: #7f8c8d; font-size: 14px;">Tips on finding and buying the perfect car safely.</p>
                <a href="#" style="color: var(--azal-blue); font-weight: 600; font-size: 14px; display: block; margin-top: 16px;">Learn more</a>
            </div>
            <div style="background: white; padding: 32px; border-radius: 20px; border: 1px solid var(--border-color); text-align: center;">
                <i class="fas fa-tag" style="font-size: 32px; color: #2ecc71; margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 12px;">Selling on azal</h3>
                <p style="color: #7f8c8d; font-size: 14px;">How to list your car and manage your advertisements.</p>
                <a href="#" style="color: var(--azal-blue); font-weight: 600; font-size: 14px; display: block; margin-top: 16px;">Learn more</a>
            </div>
        </div>

        <section style="background: #f8f9fa; padding: 40px; border-radius: 20px;">
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 24px;">Frequently Asked Questions</h2>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h4 style="font-weight: 700; margin-bottom: 8px;">How do I contact a seller?</h4>
                    <p style="color: #57606f; font-size: 14px;">You can use the "Write a message" or "Show phone number" buttons on the listing page.</p>
                </div>
                <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h4 style="font-weight: 700; margin-bottom: 8px;">Is it free to post an ad?</h4>
                    <p style="color: #57606f; font-size: 14px;">Basic ads are free, but we offer premium features to boost your visibility.</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
