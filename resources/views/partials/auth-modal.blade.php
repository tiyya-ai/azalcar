<!-- Auth Modal -->
<div id="auth-modal" class="auth-modal">
    <div class="auth-modal-backdrop" id="auth-modal-backdrop"></div>
    <div class="auth-modal-content">
        <button class="auth-modal-close" id="close-auth-modal">&times;</button>
        
        <div class="auth-modal-inner">
            <div class="auth-modal-tabs">
                <button class="auth-tab active" data-tab="login">Login</button>
                <button class="auth-tab" data-tab="register">Register</button>
            </div>

            <div class="auth-modal-body">
                <!-- Login Form -->
                <div id="login-tab" class="auth-form active">
                    <div class="auth-form-head">
                        <h4>Welcome back!</h4>
                        <p>Sign-in to your account to continue</p>
                    </div>
                    
                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="auth-form-group">
                            <label class="auth-label">Email Address</label>
                            <input type="email" name="email" class="auth-input" placeholder="Enter your email" required>
                        </div>
                        <div class="auth-form-group">
                            <div class="auth-label-row">
                                <label class="auth-label">Password</label>
                                <a href="{{ route('password.request') }}" class="auth-link-sm">Forgot Password?</a>
                            </div>
                            <input type="password" name="password" class="auth-input" placeholder="••••••••••••" required>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="remember">
                                <span>Remember Me</span>
                            </label>
                        </div>
                        <button type="submit" class="auth-submit-btn">Login</button>
                    </form>
                </div>

                <!-- Register Form -->
                <div id="register-tab" class="auth-form">
                    <div class="auth-form-head">
                        <h4>Adventure starts here</h4>
                        <p>Make your car buying & selling easy!</p>
                    </div>
                    
                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf
                        <div class="auth-form-group">
                            <label class="auth-label">Full Name</label>
                            <input type="text" name="name" class="auth-input" placeholder="Enter your full name" required>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-label">Email Address</label>
                            <input type="email" name="email" class="auth-input" placeholder="Enter your email" required>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-label">Password</label>
                            <input type="password" name="password" class="auth-input" placeholder="••••••••••••" required>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="auth-input" placeholder="••••••••••••" required>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="terms" required>
                                <span class="text-xs">I agree to the <a href="#">Terms & Privacy</a></span>
                            </label>
                        </div>
                        <button type="submit" class="auth-submit-btn">Create Account</button>
                    </form>
                </div>
            </div>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <div class="auth-social-login">
                <a href="#" class="auth-social-btn"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="auth-social-btn"><i class="fab fa-twitter"></i></a>
                <a href="#" class="auth-social-btn"><i class="fab fa-github"></i></a>
                <a href="#" class="auth-social-btn google"><i class="fab fa-google"></i></a>
            </div>
        </div>
    </div>
</div>

<style>
.auth-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
    font-family: 'DM Sans', 'Manrope', sans-serif;
}

.auth-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.auth-modal-content {
    position: relative;
    width: 100%;
    max-width: 440px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    z-index: 100;
    animation: authModalFade 0.3s ease-out;
}

@keyframes authModalFade {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.auth-modal-inner {
    padding: 32px;
}

.auth-modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 32px;
    height: 32px;
    background: #f3f4f6;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 0.2s;
}

.auth-modal-close:hover {
    background: #e5e7eb;
    color: #111827;
}

.auth-modal-tabs {
    display: flex;
    background: #f3f4f6;
    padding: 5px;
    border-radius: 12px;
    margin-bottom: 24px;
}

.auth-tab {
    flex: 1;
    padding: 10px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 700;
    color: #6b7280;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
}

.auth-tab.active {
    background: #ffffff;
    color: #6041E0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.auth-form {
    display: none;
}

.auth-form.active {
    display: block;
}

.auth-form-head {
    text-align: center;
    margin-bottom: 20px;
}

.auth-form-head h4 {
    font-size: 20px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 8px 0;
}

.auth-form-head p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.auth-form-group {
    margin-bottom: 20px;
}

.auth-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.auth-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
}

.auth-input {
    width: 100%;
    height: 48px;
    padding: 0 16px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    transition: all 0.2s;
}

.auth-input:focus {
    outline: none;
    border-color: #6041E0;
    box-shadow: 0 0 0 4px rgba(96, 65, 224, 0.1);
}

.auth-link-sm {
    font-size: 12px;
    font-weight: 700;
    color: #6041E0;
    text-decoration: none;
}

.auth-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.auth-checkbox input {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    cursor: pointer;
    accent-color: #6041E0;
}

.auth-checkbox span {
    font-size: 13px;
    color: #4b5563;
    font-weight: 500;
}

.auth-submit-btn {
    width: 100%;
    height: 48px;
    background: #6041E0;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
}

.auth-submit-btn:hover {
    background: #5235c7;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(96, 65, 224, 0.25);
}

.auth-divider {
    position: relative;
    text-align: center;
    margin: 24px 0;
}

.auth-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
    background: #e5e7eb;
}

.auth-divider span {
    position: relative;
    background: #ffffff;
    padding: 0 16px;
    font-size: 13px;
    color: #9ca3af;
    font-weight: 600;
}

.auth-social-login {
    display: flex;
    justify-content: center;
    gap: 16px;
}

.auth-social-btn {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    color: #4b5563;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 18px;
}

.auth-social-btn:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
}

.auth-social-btn.google {
    color: #ea4335;
}

.text-xs { font-size: 12px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('auth-modal');
    const backdrop = document.getElementById('auth-modal-backdrop');
    const closeBtn = document.getElementById('close-auth-modal');
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            forms.forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(targetTab + '-tab').classList.add('active');
        });
    });

    // Handle switching from links
    window.openAuthModal = function(tab = 'login') {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        const targetTab = document.querySelector(`.auth-tab[data-tab="${tab}"]`);
        if (targetTab) targetTab.click();
    };
});
</script>
