layui.use('laydate', function() {
    var laydate = layui.laydate;
    var myDate = new Date;
    var year = myDate.getFullYear(); //获取当前年
    var mon = myDate.getMonth() + 1; //获取当前月
    var day = myDate.getDate(); //获取当前日
    var mark_time = year + "-" + mon + "-" + day;//当前日期
    var mark = [];
    mark[mark_time] = '';
    var max_date = '9999-12-12';
    if ($('#eYLaydateTimeLinkage').data('max_date')) {
        max_date = $('#eYLaydateTimeLinkage').data('max_date');
    }
    laydate.render({
        elem : '#eYLaydateTimeLinkage', //指定元素
        range: '~',
        value: '',
        btns : ['clear', 'confirm', 'weeks', 'month'],
        mark: mark,
        max: max_date,
        ready: function(date) {
            $('.laydate-day-mark').removeClass('laydate-day-mark').parent().addClass('today_laydate_mark');
            // 更改 weeks 名字
            $(".laydate-btns-weeks").html('近7天');
            // 更改 month 名字
            $(".laydate-btns-month").html('近30天');

            // 日期初始化
            var _thisid = this.elem;
            var d = new Date();
            date.year = d.getFullYear();
            date.month = (d.getMonth() + 1);
            date.date = d.getDate();

            // 近七天时间选择
            $(".laydate-btns-weeks").on('click', function() {
                // 当前日期处理
                if (date.month < 10) {
                    if (date.date < 10) {
                        var end = date.year + '-0' + date.month + '-0' + date.date;
                    } else {
                        var end = date.year + '-0' + date.month + '-' + date.date;
                    }
                } else {
                    if (date.date < 10) {
                        var end = date.year + '-' + date.month + '-0' + date.date;
                    } else {
                        var end = date.year + '-' + date.month + '-' + date.date;
                    }
                }

                // 组装时间返回
                var start = new Date(d.getTime() - (7 * 24 * 60 * 60 * 1000)).toLocaleDateString()
                var da = start.replace(/\//g, '-') + ' ~ ' + end;
                _thisid.val(da);
                $("#layui-laydate1").remove();
            });

            // 近一个月时间选择
            $(".laydate-btns-month").on('click', function() {
                // 当前日期处理
                if (date.month < 10) {
                    if (date.date < 10) {
                        var end = date.year + '-0' + date.month + '-0' + date.date;
                    } else {
                        var end = date.year + '-0' + date.month + '-' + date.date;
                    }
                } else {
                    if (date.date < 10) {
                        var end = date.year + '-' + date.month + '-0' + date.date;
                    } else {
                        var end = date.year + '-' + date.month + '-' + date.date;
                    }
                }

                // 组装时间返回
                var state = getDay(date, 1);
                var da = state + ' ~ ' + end;
                _thisid.val(da);
                $("#layui-laydate1").remove();
            });

            // data为传递的结束时间,number表示往前推几个月，3个月3，1年12
            function getDay(dats, number) {
                var data = new Array(); //定义数组
                var data = dats;
                var year = data.year, //获取年份
                    month = data.month, //获取月份
                    date = data.date; //获取日期
                if (number == 12) {
                    // 推一年
                    if(month < 10) {
                        if (date < 10) {
                            s = year - 1 + '-0' + month + '-0' + date;
                        } else {
                            s = year - 1 + '-0' + month + '-' + date;
                        }
                    } else {
                        if (date < 10) {
                            s = year - 1 + '-' + month + '-0' + date;
                        } else {
                            s = year - 1 + '-' + month + '-' + date;
                        }
                    }
                } else {
                    var month = month - number;
                    // 假如是2月份，推3个月，会出现跨年情况
                    if (month <= 0) {
                        month = 12 + month;
                        if (month < 10) {
                            if (date < 10) {
                                s = year - 1 + '-0' + month + '-0' + date;
                            } else {
                                s = year - 1 + '-0' + month + '-' + date;
                            }
                        } else {
                            if (date < 10) {
                                s = year - 1 + '-' + month + '-0' + date;
                            } else {
                                s = year - 1 + '-' + month + '-' + date;
                            }
                        }
                    } else if (month < 10) {
                        if (date < 10) {
                            s = year + '-0' + month + '-0' + date;
                        } else {
                            s = year + '-0' + month + '-' + date;
                        }
                    } else {
                        if (date < 10) {
                            s = year + '-' + month + '-0' + date;
                        } else {
                            s = year + '-' + month + '-' + date;
                        }
                    }
                }
                return s;
            }
            $(".laydate-prev-m").eq(0).trigger('click');
        }
        ,done: function(value, date, endDate){
            var callback = $('#eYLaydateTimeLinkage').data('callback');
            eval(callback);
        }
    });
});