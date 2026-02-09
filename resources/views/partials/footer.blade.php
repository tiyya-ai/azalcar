<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-column">
                <a href="{{ route('home') }}" class="footer-logo">
                    <img src="{{ asset('assets/images/logo-footer.png') }}" alt="AzalCars" style="height: 40px; width: auto;">
                </a>
                <p class="footer-tagline">Shop. Sell. Service.</p>
            </div>
            
            <div class="footer-column">
                <h4>Shop</h4>
                <ul>
                    <li><a href="{{ route('listings.search') }}">Cars for Sale</a></li>
                    <li><a href="{{ route('listings.search', ['condition' => 'new']) }}">New Cars</a></li>
                    <li><a href="{{ route('listings.search', ['condition' => 'used']) }}">Used Cars</a></li>
                    <li><a href="{{ route('news.index') }}">Car Rankings</a></li>
                    <li><a href="{{ route('news.index') }}">Buying Guides</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Sell</h4>
                <ul>
                    <li><a href="{{ route('listings.create') }}">Sell Your Car</a></li>
                    <li><a href="{{ route('listings.create') }}">Get an Offer</a></li>
                    <li><a href="{{ route('register') }}">Dealer Signup</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Financing</a></li>
                    <li><a href="#">Insurance</a></li>
                    <li><a href="{{ route('support') }}">Help Center</a></li>
                    <li><a href="{{ route('brands.index') }}">All Brands</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('pages.about') }}">About Us</a></li>
                    <li><a href="{{ route('pages.careers') }}">Careers</a></li>
                    <li><a href="{{ route('pages.privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('pages.terms') }}">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} azalcar. All rights reserved.</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* ========== FOOTER ========== */
    .main-footer {
        background: #111827;
        color: white;
        padding: 64px 0 32px;
        margin-top: 64px;
    }
    
    .footer-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }
    
    .footer-grid {
        display: grid;
        grid-template-columns: 2fr repeat(4, 1fr);
        gap: 48px;
        margin-bottom: 64px;
    }
    
    .footer-logo {
        font-family: 'DM Sans', sans-serif;
        font-size: 28px;
        font-weight: 800;
        color: white;
        text-decoration: none;
        display: block;
        margin-bottom: 8px;
    }
    
    .footer-tagline {
        color: #9ca3af;
        font-size: 14px;
    }
    
    .footer-column h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 24px;
        color: white;
    }
    
    .footer-column ul {
        list-style: none;
        padding: 0;
    }
    
    .footer-column li {
        margin-bottom: 12px;
    }
    
    .footer-column a {
        color: #d1d5db;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.2s;
    }
    
    .footer-column a:hover {
        color: white;
    }
    
    .footer-bottom {
        border-top: 1px solid #374151;
        padding-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .footer-bottom p {
        font-size: 13px;
        color: #9ca3af;
    }
    
    .footer-social {
        display: flex;
        gap: 16px;
    }
    
    .footer-social a {
        color: #9ca3af;
        font-size: 20px;
        transition: color 0.2s;
    }
    
    .footer-social a:hover {
        color: white;
    }

    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
