/* ----------------------------------------

 * Ideal Forms 1.02
 * Copyright 2011, Cedric Ruiz
 * Free to use under the GPL license.
 * http://www.spacirdesigns.com

 -----------------------------------------*/

/* ---------------------------------------
 Set min-width
 ----------------------------------------*/
var setMinWidth = function (el) {
    var minWidth = 0;
    el
        .each(function () {
            var width = $(this).width();
            if (width > minWidth) {
                minWidth = width;
            }
        })
        .width(minWidth);
};

/* ---------------------------------------
 Start plugin
 ----------------------------------------*/
(function ($) {

    $.fn.idealforms = function () {
        this.each(function () {

            var $idealform,
                $labels,
                $selects,
                $radios,
                $checks;

            $idealform = $(this);
            $idealform.addClass('idealform');

            /* ---------------------------------------
             Label
             ----------------------------------------*/

            $labels = $idealform.find('div').children('label').addClass('main-label');
            $labels.filter('.required').prepend('<span>*</span>');
            setMinWidth($labels);

            /* ---------------------------------------
             Select
             ----------------------------------------*/
            //var Idealselect = function (select) {
            //
            //    var that = this;
            //
            //    // Build markup
            //    that.build = function () {
            //        var $options,
            //            $selected,
            //            _options = '',
            //            output;
            //        $options = select.find('option');
            //        $selected = $options.filter(':selected');
            //        $options.each(function () {
            //            _options += '<li><a href="#">' + $(this).text() + '</a></li>';
            //        });
            //        output =
            //            '<ul class="idealselect">' +
            //            '<li>' +
            //            '<a href="#" class="idealselect-title">' + $selected.text() + '<span><small></small></span></a>' +
            //            '<ul>' + _options + '</ul>' +
            //            '</li>' +
            //            '</ul>';
            //        return output;
            //    };
            //
            //    that.el = $(that.build()); // Wrap in jquery object
            //    that.title = that.el.find('.idealselect-title');
            //    that.menu = that.el.find('ul');
            //    that.items = that.menu.find('a');
            //
            //    // Events
            //    that.events = {
            //        open : function () {
            //            that.el.addClass('open');
            //            that.menu.show();
            //        },
            //        close : function () {
            //            that.el.removeClass('open');
            //            that.menu.scrollTop(0);
            //            that.menu.hide();
            //        },
            //        change : function () {
            //            var idx = $(this).parent().index();
            //            that.title.text($(this).text()).append('<span><small></small></span>');
            //            select.find('option').eq(idx).attr('selected', 'selected');
            //            select.trigger('change');
            //            that.events.close();
            //        }
            //    };
            //
            //    // Initializate
            //    that.init = function () {
            //
            //        // Calculate width & height and insert idealselect
            //        var $idealselect = that.el.insertAfter(select),
            //            $items = $idealselect.find('ul a'),
            //            $menu = $idealselect.find('ul'),
            //            setWidth = function () {
            //                $menu.width($idealselect.width());
            //                $idealselect.width($menu.outerWidth());
            //            };
            //        if ($items.length > 10) {
            //            setWidth();
            //            $menu.height($items.outerHeight() * 10);
            //        } else {
            //            setWidth();
            //            $menu.css('overflow', 'hidden');
            //        }
            //        that.menu.hide();
            //
            //        // Bind events
            //        that.el.find('a').click(function (e) {
            //            e.preventDefault();
            //        });
            //        that.el.on('mouseleave', that.events.close);
            //        that.title.on('click', that.events.open);
            //        that.menu.on('mouseleave', that.events.close);
            //        that.items.on('click', that.events.change);
            //
            //    };
            //};

            // Create & Insert all idealselects
            $selects = $idealform.find('.idealforms_select_obj');
            // Create & Insert all idealselects
            $selects.each(function () {
                var that = $(this);
                that.menu = that.siblings('ul.idealforms_select_menu');
                that.items = that.menu.find('li');

                that.find('input[type=text]').val( that.menu.find('li').eq(0).text());
                that.find('input[type=hidden]').val( that.menu.find('li').eq(0).data('value'));
                // Events
                that.events = {
                    open : function () {
                        that.siblings('ul.idealforms_select_menu').show();
                    },
                    close : function () {
                        that.siblings('ul.idealforms_select_menu').hide();
                    } ,
                    click: function () {
                        that.find('input[type=hidden]').val($(this).data('value'));
                        that.find('input[type=text]').val( $.trim($(this).text()));
                        that.siblings('ul.idealforms_select_menu').hide();
                    }
                };
                that.siblings('ul.idealforms_select_menu').delegate(that.siblings('ul.idealforms_select_menu'),'mouseleave',that.events.close);
                that.delegate(that.menu,'click', that.events.open);
                that.menu.delegate('li','click',that.events.click);
            });

            /* ---------------------------------------
             Radio & Check
             ----------------------------------------*/

            $radios = $idealform.find(':radio');
            $checks = $idealform.find(':checkbox');

            // Radio
            $radios.each(function () {
                $(this)
                    .after('<span class="radio"></span>')
                    .parents('ul').addClass('idealradio');
                if ($(this).is(':checked')) {
                    $(this).next('span').addClass('checked');
                }
            }).change(function () {
                $(this).parents('ul').find('span').removeClass('checked');
                $(this).next('span').addClass('checked');
            });

            // Check
            $checks.each(function () {
                $(this)
                    .after('<span class="check"></span>')
                    .parents('ul').addClass('idealcheck');
                if ($(this).is(':checked')) {
                    $(this).next('span').addClass('checked');
                }
            }).change(function () {
                $(this).next('span').toggleClass('checked');
            });

        });

    };
})(jQuery);