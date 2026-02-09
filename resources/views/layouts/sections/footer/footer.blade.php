@php
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                &#169;
                <script>
                document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by <a href="https://wa.me/212634348184" target="_blank" class="footer-link">Media Ma</a>
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="{{ config('variables.documentation') ? config('variables.documentation') . '/laravel-introduction.html' : '#' }}" target="_blank" class="footer-link me-4">Documentation</a>
                <a href="{{ config('variables.support') ? config('variables.support') : '#' }}" target="_blank" class="footer-link">Support</a>
            </div>
        </div>
    </div>
</footer>
<!-- / Footer -->
