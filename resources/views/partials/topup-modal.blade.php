<!-- Top Up Modal -->
<div id="topUpModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 12px; max-width: 400px; width: 90%; position: relative;">
        <button onclick="closeTopUpModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 20px; color: #1a1a1a;">Top Up Wallet</h3>

        @if($errors->has('amount'))
            <div style="padding: 10px; background: #fff1f2; color: #e11d48; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
                {{ $errors->first('amount') }}
            </div>
        @endif

        <form action="{{ route('wallet.topup') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Processing...';">
            @csrf
            <div style="margin-bottom: 20px;">
                <label for="amount" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a;">Amount (MAD)</label>
                <input type="number" id="amount" name="amount" min="10" max="10000" step="10" value="100" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            <button type="submit" style="width: 100%; background: #6041E0; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">Proceed to Payment</button>
        </form>
    </div>
</div>

<script>
function openTopUpModal() {
    const modal = document.getElementById('topUpModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeTopUpModal() {
    const modal = document.getElementById('topUpModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('topUpModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTopUpModal();
            }
        });
    }
});
</script>
