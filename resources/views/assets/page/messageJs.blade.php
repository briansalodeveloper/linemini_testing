
@push('js')
    @php
        $routeDelete = '';
        $routeTrumbowyg = route("message.uploadTrumbowygImage");
        $routeUpload = route('message.upload');
        $routeDuplicate = route('message.store', ['cp' => 1]);
        $routeSaveAndSend = '';

        if ($data->isNotEmpty) {
            $routeDelete = route('message.destroy', $data->id);

            if ($data->isStatusNotSend) {
                $routeSaveAndSend = route('message.update', [
                    'id' => $data->id,
                    'isSend' => 1
                ]);
            }
        } else {
            $routeSaveAndSend = route('message.store', [
                'isSend' => 1
            ]);
        }
    @endphp
    <script>
        'use strict';

        /*======================================================================
        * METHODS
        *======================================================================*/

        $(function () {
            $.fn.isValidUpload = function (filename, msgHolder, regex, type) {
                var input = $(filename);
                var files = input[0].files;

                if (files == null) {
                    return 0;
                }

                var fileSize = files[0].size;
                var filename = files[0].name;
                var extension = filename.substr(filename.lastIndexOf("."));
                var allowedExtensionsRegx = regex;
                var isAllowed = allowedExtensionsRegx.test(extension);

                if (!isAllowed) {
                    input[0].value = '';
                    input.css("color", "red");
                    $(msgHolder + '-error').text('{{ __('validation.mimes', ['attribute' => __('words.SendImage'), 'values' => \Globals::implode(\Globals::mMessage()::THUMBNAIL_ACCEPTEDEXTENSION, ', ') ]) }}');

                    return 0;
                }

                if (type == 'img') {
                    if (fileSize > 2 * Math.pow(1024, 2)) {
                        input[0].value = '';
                        input.css("color", "red");
                        $(msgHolder + '-error').text('{{ __('messages.custom.fileSize.mb', ['name' => __('words.TopImg'), 'mb' => 2]) }}');

                        return 0;
                    }
                }

                if (msgHolder == '#thumbnail') {
                    $('body').addClass('image-uploading');
                    $('.file-image').addClass('uploading');
                } else if (msgHolder == '#unionMemberCsvTrigger') {
                    $('.file-csv').addClass('uploading');
                }

                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: "{{ $routeUpload }}",
                    method: 'POST',
                    data: new FormData($('#form')[0]),
                    contentType: false,
                    processData: false,
                    dataType: 'JSON',
                    success: function (response) {
                        input.css("color", "green");
                        $(msgHolder + '-error').text('');

                        if (msgHolder == '#thumbnail') {
                            $('input[name="thumbnail"]').val(response.url);
                            $('.file-image span.label').html(_g.basename(response.url));
                        } else if (msgHolder == '#unionMemberCsvTrigger') {
                            $('input[name="unionMemberCsv"]').val(response.url);
                            $('.file-csv span.label').html(_g.basename(response.url));
                        }
                    },
                    error: function(response) {
                        input.css("color", "red");
                        $(msgHolder + '-error').text('Failed to upload Chosen File.');
                        console.log("failed to upload", response);
                    },

                }).always(function () {
                    if (msgHolder == '#thumbnail') {
                        $('body').removeClass('image-uploading');
                        $('.file-image').removeClass('uploading');
                        $.fn.reloadPreview();
                    } else if (msgHolder == '#unionMemberCsvTrigger') {
                        $('.file-csv').removeClass('uploading');
                    }
                });
            };

            $.fn.currentDateTime = function() {
                var now = new Date();
                var fullYear = now.getFullYear();
                var month = now.getMonth() + 1;
                var date = now.getDate();
                var hour = now.getHours();
                var min = now.getMinutes();
                var ampm = hour >= 12 ? 'PM' : 'AM';

                hour = hour % 12;
                hour = hour ? hour : 12;
                min = min < 10 ? '0' + min : min;
                hour = hour < 10 ? '0' + hour : hour;
                month = month < 10 ? '0' + month : month;
                date = date < 10 ? '0' + date : date;

                var fullDate = month + '/' + date + '/' + fullYear;
                var time = hour + ':' + min + ' ' + ampm;

                return {
                    date: fullDate,
                    time: time
                }
            };

            $.fn.reloadPreview = function () {
                let data = $('#form').serializeArray();
                data = _g.form.toArray(data);
                let contents = data.contents;

                if (typeof data.contents == 'undefined') {
                    contents = $('.contents').html();
                }

                $('#contentPreview [role-name=thumbnail]').each(function () {
                    if ($.trim(data.thumbnail) == '') {
                        $(this).parents('.message').hide();
                    } else {
                        $(this).parents('.message').show();
                        $(this).attr('src', data.thumbnail);
                    }
                });

                $('#contentPreview [role-name=time]').each(function () {
                    $(this).html($.trim(data.sendTime));
                });

                $('#contentPreview [role-name=contents]').each(function () {
                    if ($.trim(contents) == '') {
                        $(this).parents('.message').hide();
                    } else {
                        $(this).parents('.message').show();

                        var text = contents;
                        text = text.replace(/\<\/p\>/g, '[bbbrrr-the-break]</p>');
                        text = $(text).text();
                        text = text.replace(/\[bbbrrr\-the\-break\]/g, '<br/>');
                        $(this).html(text);
                    }
                });
            };
        });

        /*======================================================================
        * INITIALIZATION
        *======================================================================*/

        $(function () {
            $('#contents').trumbowyg({
                lang: 'ja',
                btnsDef: _g.trumbowyg.default.btnsDef,
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'],
                    ['emoji'],
                    ['removeformat'],
                    ['fullscreen']
                ],
                plugins: {
                    upload: {
                        serverPath: '{{ $routeTrumbowyg }}',
                        fileFieldName: 'image',
                        data: [_g.trumbowyg.uploadDataToken()],
                        urlPropertyName: 'url'
                    }
                }
            });
            
            $('#sendDate').datetimepicker({
                format: 'L'
            });

            $('#sendTime').datetimepicker({
                format: 'LT'
            });
        });

        /*======================================================================
        * DOM EVENTS
        *======================================================================*/

        $(function () {
            var sendTargetFlg = $('input[name="sendTargetFlg"]:checked').val();

            if (!sendTargetFlg || typeof sendTargetFlg == 'undefined' || {{ $data->getAttr('isStatusSend', false) ? 1 : 0 }}) {
                $('input[name="csv"]').prop('disabled', true);
                $('select[name="ubId"]').prop('disabled', true);
                $('select[name="aoId"]').prop('disabled', true);
                $('select[name="storeId"]').prop('disabled', true);
                $('.ub-ao-store-box').addClass('is-ub');
            } else {
                $('input[name="csv"]').prop('disabled', sendTargetFlg != '{{ \Globals::mMessage()::SENDTARGET_UNIONMEMBER }}');
                $('select[name="ubId"]').prop('disabled', sendTargetFlg != '{{ \Globals::mMessage()::SENDTARGET_UB }}');
                $('select[name="aoId"]').prop('disabled', sendTargetFlg != '{{ \Globals::mMessage()::SENDTARGET_AO }}');
                $('select[name="storeId"]').prop('disabled', sendTargetFlg != '{{ \Globals::mMessage()::SENDTARGET_STORE }}');

                var sendTargetFlgAddClass = sendTargetFlg == '{{ \Globals::mMessage()::SENDTARGET_STORE }}' ? 'is-store' : (sendTargetFlg == '{{ \Globals::mMessage()::SENDTARGET_AO }}' ? 'is-ao' : 'is-ub');
                var sendTargetFlgRemoveClass = sendTargetFlgAddClass == 'is-store' ? 'is-ao is-ub' : (sendTargetFlgAddClass == 'is-ao' ? 'is-ub is-store' : 'is-ao is-store');

                $('.ub-ao-store-box').addClass(sendTargetFlgAddClass).removeClass(sendTargetFlgRemoveClass);
            }

            if ($('[name=selectTransmissionTiming]:checked').val() == 0) {
                $('input[name="sendDate"], input[name="sendTime"]').prop('readonly', true);
            } else if ($('[name=selectTransmissionTiming]:checked').val() == 1) {
                $('input[name="sendDate"], input[name="sendTime"]').prop('readonly', false);
            } else {
                $('input[name="sendDate"], input[name="sendTime"]').prop('readonly', true);
            }

            $('input[name="sendTargetFlg"]').change(function () {
                @if($data->getAttr('isStatusNotSend', true))
                    $('input[name="csv"]').prop('disabled', this.value != '{{ \Globals::mMessage()::SENDTARGET_UNIONMEMBER }}');
                    $('select[name="ubId"]').prop('disabled', this.value != '{{ \Globals::mMessage()::SENDTARGET_UB }}');
                    $('select[name="aoId"]').prop('disabled', this.value != '{{ \Globals::mMessage()::SENDTARGET_AO }}');
                    $('select[name="storeId"]').prop('disabled', this.value != '{{ \Globals::mMessage()::SENDTARGET_STORE }}');
                @endif

                var sendTargetFlgAddClass = this.value == '{{ \Globals::mMessage()::SENDTARGET_STORE }}' ? 'is-store' : (this.value == '{{ \Globals::mMessage()::SENDTARGET_AO }}' ? 'is-ao' : 'is-ub');
                var sendTargetFlgRemoveClass = sendTargetFlgAddClass == 'is-store' ? 'is-ao is-ub' : (sendTargetFlgAddClass == 'is-ao' ? 'is-ub is-store' : 'is-ao is-store');

                $('.ub-ao-store-box').addClass(sendTargetFlgAddClass).removeClass(sendTargetFlgRemoveClass);
            });

            $('input[name="csv"]').on('change',function () {
                $('#uploadType').val('csv');
                $.fn.isValidUpload(this, '#unionMemberCsvTrigger', csvExtensionRegex, 'csv');
            });

            $('input[name="image"]').on('change',function () {
                $('#uploadType').val('image');
                $.fn.isValidUpload(this, '#thumbnail', imgExtensionRegex, 'img');
            });

            $(".alert").click(function () {
                $(this).remove();
            });

            $("#preview").click(function (e) {
                e.preventDefault();
                $.fn.reloadPreview();
                $('#contentPreview').modal('show');
            });

            $('input[name="selectTransmissionTiming"]').click(function () {
                if ($(this).filter(":checked").val() == 0) {
                    var dateTime = $.fn.currentDateTime();

                    $('input[name="sendDate"]').val(dateTime.date);
                    $('input[name="sendTime"]').val(dateTime.time);
                    $('input[id="sendDateInput"], input[id="sendTimeInput"]').prop('readonly', true);
                } else {
                    $('input[name="sendDate"], input[name="sendTime"]').val('');
                    $('input[name="sendDate"], input[name="sendTime"]').prop('readonly', false);
                }
            });

            $('#duplicateBtn').click(function(e) {
                e.preventDefault();
                $('#form').attr('action', '{{ $routeDuplicate }}');
                $("#submit").unbind('click').click();
            });

            $('#deleteBtn').click(function(e) {
                e.preventDefault()
                @if(!empty($routeDelete))
                    $('#form').attr('action', "{{ $routeDelete }}");
                    $("#submit").unbind('click').click();
                @endif
            });

            $('#clearBtn').click(function() {
                $.fn.undoEdit();
            });

            $('#undoEdit').click(function() {
                $.fn.undoEdit();
            });

            $('#saveAndSendMessage').click(function(e) {
                e.preventDefault()
                @if(!empty($routeSaveAndSend))
                    $('#form').attr('action', "{{ $routeSaveAndSend }}");
                    $("#submit").unbind('click').click();
                @endif
            });
        });
    </script>
@endpush