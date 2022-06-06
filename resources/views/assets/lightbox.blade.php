@push('css')
    <link href="{{ _vers('assets/ekko-lightbox/ekko-lightbox.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ _vers('assets/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script>
        $(document).on('click', '[data-toggle="lightbox"]', function(e) {
            e.preventDefault();
            $(this).ekkoLightbox();
        });
    </script>
@endpush
