@push('css')
    <link href="{{ _vers('assets/prismjs/themes/prism.min.css') }}" rel="stylesheet">
    <link href="{{ _vers('assets/prismjs/plugins/line-highlight/prism-line-highlight.min.css') }}" rel="stylesheet">
    <link href="{{ _vers('assets/trumbowyg/dist/ui/trumbowyg.min.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ _vers('assets/prismjs/prism.js') }}"></script>
    <script src="{{ _vers('assets/prismjs/plugins/line-highlight/prism-line-highlight.min.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/trumbowyg.min.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/langs/ja.min.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/upload/trumbowyg.upload.min.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/colors/trumbowyg.colors.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/fontsize/trumbowyg.fontsize.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/fontfamily/trumbowyg.fontfamily.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/highlight/trumbowyg.highlight.js') }}"></script>
    <script src="{{ _vers('assets/trumbowyg/dist/plugins/emoji/trumbowyg.emoji.js') }}"></script>
    <script>
        $.trumbowyg.svgPath = '{{ url('svg/trumbowyg/icons.svg') }}';
    </script>
@endpush
