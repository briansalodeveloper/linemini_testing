@push('js')
    <script>
        'use strict';

        /*======================================================================
        * CONSTANTS
        *======================================================================*/

        const csvExtensionRegex = /({{ \Globals::implode(\Globals::mMessage()::CSV_ACCEPTEDEXTENSION, '|', '\\.') }})$/i;
        const imgExtensionRegex = /({{ \Globals::implode(\Globals::mMessage()::THUMBNAIL_ACCEPTEDEXTENSION, '|', '\\.') }})$/i;

        /*======================================================================
        * METHODS
        *======================================================================*/

        $(function () {
            $.fn.undoEdit = function () {
                $('form').trigger('reset');

                $('[data-original-value]').each(function () {
                    var _val = $(this).data('original-value');

                    if (this.tagName.toLowerCase() == 'input') {
                        if ($(this).attr('type').toLowerCase() == 'text') {
                            $(this).val(_val);
                        } else if ($(this).attr('type').toLowerCase() == 'radio') {
                            var target = $(this).attr('name');

                            if ($.trim(_val) == '') {
                                $('input[name=' + target + ']').prop('checked', false);
                            } else {
                                $('input[name=' + target + '][value=' + _val + ']').prop('checked', true);
                            }

                            $('input[name=' + target + ']').change();
                        } else if ($(this).attr('type').toLowerCase() == 'hidden') {
                            var extension = _val.substr(_val.lastIndexOf("."));
                            var fileName = _val.substring(_val.lastIndexOf("/") + 1);
                            var logo = '';
                            var label = '';

                            if (csvExtensionRegex.test(extension)) {
                                logo = '<i class="fa fa-file-csv"></i> ';
                                label = '<a href="' + _val + '" download>' + logo + fileName + '</a>';
                            } else if (imgExtensionRegex.test(extension)) {
                                logo = '<i class="fa fa-image"></i> ';
                                label = '<a href="' + _val + '" data-toggle="lightbox" title="{{ __("words.Preview") }}">' + logo + fileName + '</a>';
                            }

                            $(this).siblings( ".label" ).empty().append(label)
                            $(this).val(_val);
                        }
                    } else if (this.tagName.toLowerCase() == 'textarea') {
                        var target = _val;

                        if (target.length != 0) {
                            $('.trumbowyg-editor').empty().append($(target).val())
                        }
                    }
                });
            }
        });
    </script>
@endpush