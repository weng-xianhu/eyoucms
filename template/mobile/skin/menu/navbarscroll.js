(function($) {
    $.fn.navbarscroll = function(options) {
        var _defaults = {
            className: 'cur',
            clickScrollTime: 300,
            duibiScreenWidth: 0.4,
            scrollerWidth: 3,
            defaultSelect: 0,
            fingerClick: 0,
            endClickScroll: function(thisObj) {}
        }
        var _opt = $.extend(_defaults, options);
        this.each(function() {
            var _wrapper = $(this);
            var _win = $(window);
            var _win_width = _win.width(),
                _wrapper_width = _wrapper.width(),
                _wrapper_off_left = _wrapper.offset().left;
            var _wrapper_off_right = _win_width - _wrapper_off_left - _wrapper_width;
            var _obj_scroller = _wrapper.children('.scroller');
            var _obj_ul = _obj_scroller.children('ul');
            var _obj_li = _obj_ul.children('li');
            var _scroller_w = 0;
            _obj_li.css({
                "margin-left": "0",
                "margin-right": "0"
            });
            for (var i = 0; i < _obj_li.length; i++) {
                _scroller_w += _obj_li[i].offsetWidth;
            }
            _obj_scroller.width(_scroller_w + _opt.scrollerWidth);
            var myScroll = new IScroll('#' + _wrapper.attr('id'), {
                eventPassthrough: true,
                scrollX: true,
                scrollY: false,
                preventDefault: false
            });
            _init(_obj_li.eq(_opt.defaultSelect));
            _obj_li.click(function() {
                _init($(this));
            });
            _wrapper[0].addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, false);

            function _init(thiObj) {
                var $this_obj = thiObj;
                var duibi = _opt.duibiScreenWidth * _win_width / 10,
                    this_index = $this_obj.index(),
                    this_off_left = $this_obj.offset().left,
                    this_pos_left = $this_obj.position().left,
                    this_width = $this_obj.width(),
                    this_prev_width = $this_obj.prev('li').width(),
                    this_next_width = $this_obj.next('li').width();
                var this_off_right = _win_width - this_off_left - this_width;
                if (_scroller_w + 2 > _wrapper_width) {
                    if (_opt.fingerClick == 1) {
                        if (this_index == 1) {
                            myScroll.scrollTo(-this_pos_left + this_prev_width, 0, _opt.clickScrollTime);
                        } else if (this_index == 0) {
                            myScroll.scrollTo(-this_pos_left, 0, _opt.clickScrollTime);
                        } else if (this_index == _obj_li.length - 2) {
                            myScroll.scrollBy(this_off_right - _wrapper_off_right - this_width, 0, _opt
                                .clickScrollTime);
                        } else if (this_index == _obj_li.length - 1) {
                            myScroll.scrollBy(this_off_right - _wrapper_off_right, 0, _opt.clickScrollTime);
                        } else {
                            if (this_off_left - _wrapper_off_left - (this_width * _opt.fingerClick) <
                                duibi) {
                                myScroll.scrollTo(-this_pos_left + this_prev_width + (this_width * _opt
                                    .fingerClick), 0, _opt.clickScrollTime);
                            } else if (this_off_right - _wrapper_off_right - (this_width * _opt.fingerClick) <
                                duibi) {
                                myScroll.scrollBy(this_off_right - this_next_width - _wrapper_off_right -
                                    (this_width * _opt.fingerClick), 0, _opt.clickScrollTime);
                            }
                        }
                    } else {
                        if (this_index == 1) {
                            myScroll.scrollTo(-this_pos_left + this_prev_width, 0, _opt.clickScrollTime);
                        } else if (this_index == _obj_li.length - 1) {
                            if (this_off_right - _wrapper_off_right > 1 || this_off_right -
                                _wrapper_off_right < -1) {
                                myScroll.scrollBy(this_off_right - _wrapper_off_right, 0, _opt.clickScrollTime);
                            }
                        } else {
                            if (this_off_left - _wrapper_off_left < duibi) {
                                myScroll.scrollTo(-this_pos_left + this_prev_width, 0, _opt.clickScrollTime);
                            } else if (this_off_right - _wrapper_off_right < duibi) {
                                myScroll.scrollBy(this_off_right - this_next_width - _wrapper_off_right,
                                    0, _opt.clickScrollTime);
                            }
                        }
                    }
                }
                $this_obj.addClass(_opt.className).siblings('li').removeClass(_opt.className);
                _opt.endClickScroll.call(this, $this_obj);
            }
        });
    };
})(jQuery);
