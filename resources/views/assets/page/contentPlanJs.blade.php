@push('js')
    @php
        $routeTrumbowyg = '';
        $routeUpload = '';
        $routeDuplicate = '';
        $routeDelete = '';

        if ($contentType == Globals::mContentPlan()::CONTENTTYPE_NOTICE) {
            $routeTrumbowyg = route("notice.uploadTrumbowygImage");
            $routeUpload = route('notice.upload');
            $routeDuplicate = route('notice.store', ['cp' => 1]);

            if ($data->isNotEmpty) {
                $routeDelete = route('notice.destroy', $data->id);
            }
        } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_RECIPE) {
            $routeTrumbowyg = route("recipe.uploadTrumbowygImage");
            $routeUpload = route('recipe.upload');
            $routeDuplicate = route('recipe.store', ['cp' => 1]);

            if ($data->isNotEmpty) {
                $routeDelete = route('recipe.destroy', $data->id);
            }
        } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_PRODUCTINFO) {
            $routeTrumbowyg = route("productInformation.uploadTrumbowygImage");
            $routeUpload = route('productInformation.upload');
            $routeDuplicate = route('productInformation.store', ['cp' => 1]);

            if ($data->isNotEmpty) {
                $routeDelete = route('productInformation.destroy', $data->id);
            }
        } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_COLUMN) {
            $routeTrumbowyg = route("column.uploadTrumbowygImage");
            $routeUpload = route('column.upload');
            $routeDuplicate = route('column.store', ['cp' => 1]);

            if ($data->isNotEmpty) {
                $routeDelete = route('column.destroy', $data->id);
            }
        }
    @endphp
    <script>
        'use strict';

        /*======================================================================
        * CONSTANTS
        *======================================================================*/

        const csvExtensionRegex = /({{ \Globals::implode(\Globals::mContentPlan()::CSV_ACCEPTEDEXTENSION, '|', '\\.') }})$/i;
        const imgExtensionRegex = /({{ \Globals::implode(\Globals::mContentPlan()::THUMBNAIL_ACCEPTEDEXTENSION, '|', '\\.') }})$/i;

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
                    $(msgHolder + '-error').text('{{ __('validation.mimes', ['attribute' => __('words.TopImg'), 'values' => \Globals::implode(\Globals::mContentPlan()::THUMBNAIL_ACCEPTEDEXTENSION, ', ') ]) }}');

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

                if (msgHolder == '#openingImg') {
                    $('body').addClass('thumbnail-uploading');
                    $('.file-thumbnail').addClass('uploading');
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

                        if (msgHolder == '#openingImg') {
                            $('input[name="openingImg"]').val(response.url);
                            $('.file-thumbnail span.label').html(_g.basename(response.url));
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
                    if (msgHolder == '#openingImg') {
                        $('body').removeClass('thumbnail-uploading');
                        $('.file-thumbnail').removeClass('uploading');
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
                let startDateTime = '';
                data = _g.form.toArray(data);

                if (typeof data.startDate != 'undefined') {
                    startDateTime = new Date(data.startDate);
                    startDateTime = startDateTime.getFullYear() + '年' + (startDateTime.getMonth() + 1) + '月' + startDateTime.getDate() + '日配信';
                }

                $('#contentPreview [role-name=opening-image]').each(function () {
                    $(this).attr('src', data.openingImg);
                });

                $('#contentPreview [role-name=opening-letter]').each(function () {
                    $(this).html(data.openingLetter);
                });

                $('#contentPreview [role-name=start-datetime]').each(function () {
                    $(this).html(startDateTime);
                });

                $('#contentPreview [role-name=contents]').each(function () {
                    $(this).html(data.contents);
                });
            };

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

        /*======================================================================
        * INITIALIZATION
        *======================================================================*/

        $(function () {
            $('#contents').trumbowyg({
                lang: 'ja',
                btnsDef: _g.trumbowyg.default.btnsDef,
                btns: _g.trumbowyg.default.btns,
                plugins: {
                    upload: {
                        serverPath: '{{ $routeTrumbowyg }}',
                        fileFieldName: 'image',
                        data: [_g.trumbowyg.uploadDataToken()],
                        urlPropertyName: 'url'
                    }
                }
            });
            
            $('#startDate, #endDate').datetimepicker({
                format: 'L'
            });

            $('#startTime, #endTime').datetimepicker({
                format: 'LT'
            });
        });
    
        /*======================================================================
        * DOM EVENTS
        *======================================================================*/

        $(function () {
            var displayTargetFlg = $('input[name="displayTargetFlg"]:checked').val();

            if (!displayTargetFlg || typeof displayTargetFlg == 'undefined') {
                $('input[name="csv"]').prop('disabled', true);
                $('select[name="utilizationBusiness"]').prop('disabled', true);
                $('select[name="affiliationOffice"]').prop('disabled', true);
                $('.ub-ao-box').addClass('is-ub');
            } else {
                $('input[name="csv"]').prop('disabled', displayTargetFlg != '{{ \Globals::mContentPlan()::DSPTARGET_UNIONMEMBER }}');
                $('select[name="utilizationBusiness"]').prop('disabled', displayTargetFlg != '{{ \Globals::mContentPlan()::DSPTARGET_UB }}');
                $('select[name="affiliationOffice"]').prop('disabled', displayTargetFlg != '{{ \Globals::mContentPlan()::DSPTARGET_AO }}');

                var dspTrgtFlgAddClass = displayTargetFlg == '{{ \Globals::mContentPlan()::DSPTARGET_AO }}' ? 'is-ao' : 'is-ub';
                var dspTrgtFlgRemoveClass = dspTrgtFlgAddClass == 'is-ao' ? 'is-ub' : 'is-ao';

                $('.ub-ao-box').addClass(dspTrgtFlgAddClass).removeClass(dspTrgtFlgRemoveClass);
            }

            if ($('[name=selectPublicationDateTime]:checked').val() == 0) {
                $('input[name="startDate"], input[name="startTime"]').prop('readonly', true);
                $('input[name="endDate"], input[name="endTime"]').prop('readonly', false);
            } else if ($('[name=selectPublicationDateTime]:checked').val() == 1) {
                $('input[name="startDate"], input[name="startTime"], input[name="endDate"], input[name="endTime"]').prop('readonly', false);
            } else {
                $('input[name="startDate"], input[name="startTime"], input[name="endDate"], input[name="endTime"]').prop('readonly', true);
            }

            $('input[name="displayTargetFlg"]').change(function () {
                $('input[name="csv"]').prop('disabled', this.value != '{{ \Globals::mContentPlan()::DSPTARGET_UNIONMEMBER }}');
                $('select[name="utilizationBusiness"]').prop('disabled', this.value != '{{ \Globals::mContentPlan()::DSPTARGET_UB }}');
                $('select[name="affiliationOffice"]').prop('disabled', this.value != '{{ \Globals::mContentPlan()::DSPTARGET_AO }}');

                var dspTrgtFlgAddClass = this.value == '{{ \Globals::mContentPlan()::DSPTARGET_AO }}' ? 'is-ao' : 'is-ub';
                var dspTrgtFlgRemoveClass = dspTrgtFlgAddClass == 'is-ao' ? 'is-ub' : 'is-ao';
    
                $('.ub-ao-box').addClass(dspTrgtFlgAddClass).removeClass(dspTrgtFlgRemoveClass);
            });

            $('input[name="csv"]').on('change',function () {
                $('#uploadType').val('csv');
                $.fn.isValidUpload(this, '#unionMemberCsvTrigger', csvExtensionRegex, 'csv');
            });

            $('input[name="thumbnail"]').on('change',function () {
                $('#uploadType').val('thumbnail');
                $.fn.isValidUpload(this, '#openingImg', imgExtensionRegex, 'img');
            });

            $(".alert").click(function () {
                $(this).remove();
            });

            $("#preview").click(function (e) {
                e.preventDefault();
                $.fn.reloadPreview();
                $('#contentPreview').modal('show');
            });

            $('input[name="selectPublicationDateTime"]').click(function () {
                if ($(this).filter(":checked").val() == 0) {
                    var dateTime = $.fn.currentDateTime();

                    $('input[name="startDate"]').val(dateTime.date);
                    $('input[name="startTime"]').val(dateTime.time);
                    $('input[id="startDateInput"], input[id="startTimeInput"]').prop('readonly', true);
                    $('input[name="endDate"], input[name="endTime"]').prop('readonly', false);
                } else {
                    $('input[name="startDate"], input[name="startTime"]').val('');
                    $('input[name="endDate"], input[name="endTime"]').val('');
                    $('input[name="startDate"], input[name="startTime"], input[name="endDate"], input[name="endTime"]').prop('readonly', false);
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
        });
    </script>
@endpush
