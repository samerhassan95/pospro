<footer class="business-footer container-fluid d-flex align-items-center justify-content-center justify-content-sm-between flex-wrap py-3 ms-0 bg-white d-print-none">
    <p class="mb-0 me-3">{{ __('© :year codgoo, all rights reserved.', ['year' => date('Y')]) }}</p>
    <p class="mb-0">{{ __('Development By') }}: <a class="footer-acn" href="{{ get_option('general')['admin_footer_link'] ?? '' }}" target="_blank">{{ get_option('general')['admin_footer_link_text'] ?? '' }}</a></p>
</footer>
