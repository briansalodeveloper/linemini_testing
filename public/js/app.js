'use strict';

let _g = {
    token: '',
    /**
     * Global variable _g initialization
     */
    init: () => {
        _g.token = $('[name=csrf-token]').attr('content');
    },
    basename: (url) => {
        url = $.trim(url);
        if (url == '') {
            return url;
        }

        return url.split('/').reverse()[0];
    },
    form: {
        val: (key, serializedForm) => {
            var values = {};

            $.each(serializedForm, function (i, field) {
                values[field.name] = field.value;
            });

            var getValue = function (valueName) {
                return values[valueName];
            };

            return getValue(key);
        },
        toArray: (serializedForm) => {
            var values = {};

            $.each(serializedForm, function (i, field) {
                values[field.name] = field.value;
            });
            
            return values;
        }
    },
    trumbowyg: {
        /**
         * Trumbowyg upload token data
         */
        default: {
            btnsDef: {
                image: {
                    dropdown: ['insertImage', 'upload'],
                    ico: 'insertImage'
                }
            },
            btns: [
                ['viewHTML'],
                ['undo', 'redo'],
                ['formatting'],
                ['strong', 'em', 'del'],
                ['foreColor', 'backColor', 'fontsize', 'fontfamily', 'highlight'],
                ['emoji'],
                ['superscript', 'subscript'],
                ['link'],
                ['image'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ],
        },
        uploadDataToken: () => {
            return {name: '_token', value: _g.token};
        }
    }
};

_g.init();

$(function(){

    /*======================================================================
     * ENUMS
     *======================================================================*/

    /*======================================================================
     * CONSTANTS
     *======================================================================*/
    
    /*======================================================================
     * OTHER VARIABLES
     *======================================================================*/

    /*======================================================================
     * METHODS
     *======================================================================*/

    /*======================================================================
     * DOM EVENTS
     *======================================================================*/

});
