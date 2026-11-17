/**
 * 优惠券插件 - 下单页选券（PC端/手机端通用）
 * 依赖：jquery
 * 页面需存在：#theForm、#couponWrap（入口容器）、#couponUseId（隐藏域）
 * 页面可选：#couponPrice（优惠金额显示）、#TotalAmount（应付总额）
 * 页面需定义：var EyouCouponOrderUrl = '插件 order_coupon 接口地址';
 * 页面可定义：var EyouCouponMobile = 1; // 手机端底部弹层
 * 页面可定义：var EyouCouponCenterUrl = '领券中心地址';
 */
var EyouCoupon = {
    use: [],
    unuse: [],
    // 当前选中的领券记录ID
    useId: '',
    // 当前选中的抵扣金额
    price: 0,
    // 弹层中临时选中
    tmpUseId: '',
    // 优惠券抵扣前的应付总额
    gross: 0,
    // 标记自身正在改写应付总额，避免与运费联动逻辑互相触发
    selfUpdate: false,
    isMobile: function () {
        return 'undefined' != typeof EyouCouponMobile && EyouCouponMobile;
    },

    init: function () {
        if ('undefined' == typeof EyouCouponOrderUrl || !$('#couponWrap').length) return;

        var $amount = $('#TotalAmount');
        if ($amount.length) {
            EyouCoupon.gross = parseFloat($amount.text()) || 0;
            EyouCoupon.watchAmount();
        }

        EyouCoupon.renderRow('loading');
        $.ajax({
            url: EyouCouponOrderUrl,
            data: $('#theForm').serialize() + '&_ajax=1',
            type: 'post',
            dataType: 'json',
            success: function (res) {
                if (1 != res.code) {
                    EyouCoupon.renderRow('error', res.msg);
                    return;
                }
                EyouCoupon.use = res.data.use || [];
                EyouCoupon.unuse = res.data.unuse || [];
                EyouCoupon.renderRow('ok');
            },
            error: function () {
                EyouCoupon.renderRow('error', '优惠券加载失败');
            }
        });
    },

    /**
     * 运费变动等外部逻辑会重写应付总额，这里同步基数并重新抵扣
     */
    watchAmount: function () {
        var el = document.getElementById('TotalAmount');
        if (!el || 'undefined' == typeof MutationObserver) return;

        var observer = new MutationObserver(function () {
            if (EyouCoupon.selfUpdate) return;
            EyouCoupon.gross = parseFloat($(el).text()) || 0;
            EyouCoupon.applyAmount();
        });
        observer.observe(el, {childList: true, characterData: true, subtree: true});
    },

    /**
     * 入口行
     */
    renderRow: function (state, msg) {
        var html = '';
        if ('loading' == state) {
            html = '<div class="ey-coupon-row disabled"><span class="ey-coupon-label">优惠券</span>'
                + '<span class="ey-coupon-summary">优惠券加载中...</span></div>';
            $('#couponWrap').html(html);
            return;
        }
        if ('error' == state) {
            html = '<div class="ey-coupon-row disabled"><span class="ey-coupon-label">优惠券</span>'
                + '<span class="ey-coupon-summary">' + (msg || '优惠券加载失败') + '</span>'
                + EyouCoupon.centerLink() + '</div>';
            $('#couponWrap').html(html);
            return;
        }

        var summary = '', cls = '';
        if (EyouCoupon.useId) {
            summary = '已选「' + EyouCoupon.selectedName() + '」，抵扣 ￥' + EyouCoupon.price.toFixed(2);
            cls = ' actived';
        } else if (EyouCoupon.use.length) {
            summary = EyouCoupon.use.length + ' 张可用，最高抵扣 ￥' + (parseFloat(EyouCoupon.use[0].coupon_price) || 0).toFixed(2);
            cls = ' actived';
        } else if (EyouCoupon.unuse.length) {
            summary = '暂无可用优惠券（' + EyouCoupon.unuse.length + ' 张不可用）';
        } else {
            summary = '暂无可用优惠券';
        }

        var clickable = EyouCoupon.use.length || EyouCoupon.unuse.length;
        html = '<div class="ey-coupon-row' + (clickable ? '' : ' disabled') + '"' + (clickable ? ' onclick="EyouCoupon.open();"' : '') + '>'
            + '<span class="ey-coupon-label">优惠券</span>'
            + '<span class="ey-coupon-summary' + cls + '">' + summary + '</span>'
            + (clickable ? '<span class="ey-coupon-arrow"></span>' : '')
            + EyouCoupon.centerLink()
            + '</div>';
        $('#couponWrap').html(html);
    },

    centerLink: function () {
        if ('undefined' == typeof EyouCouponCenterUrl || !EyouCouponCenterUrl) return '';
        return '<a class="ey-coupon-more" href="' + EyouCouponCenterUrl + '" target="_blank" onclick="event.stopPropagation();">去领券</a>';
    },

    selectedName: function () {
        for (var i = 0; i < EyouCoupon.use.length; i++) {
            if (EyouCoupon.use[i].use_id == EyouCoupon.useId) return EyouCoupon.use[i].coupon_name;
        }
        return '优惠券';
    },

    /**
     * 打开弹层
     */
    open: function () {
        if (!$('#eyCouponMask').length) {
            $('body').append('<div class="ey-coupon-mask' + (EyouCoupon.isMobile() ? ' mobile' : '') + '" id="eyCouponMask">'
                + '<div class="ey-coupon-layer">'
                + '<div class="ey-coupon-hd">选择优惠券<span class="ey-coupon-close" onclick="EyouCoupon.close();">&times;</span></div>'
                + '<div class="ey-coupon-tabs">'
                + '<span class="actived" data-tab="use" onclick="EyouCoupon.tab(\'use\');">可用（' + EyouCoupon.use.length + '）</span>'
                + '<span data-tab="unuse" onclick="EyouCoupon.tab(\'unuse\');">不可用（' + EyouCoupon.unuse.length + '）</span>'
                + '</div>'
                + '<div class="ey-coupon-bd" id="eyCouponList"></div>'
                + '<div class="ey-coupon-ft"><span class="ey-coupon-ft-tip" id="eyCouponFtTip"></span>'
                + '<span class="ey-coupon-btn" onclick="EyouCoupon.confirm();">确定</span></div>'
                + '</div></div>');
            $('#eyCouponMask').on('click', function (e) {
                if (e.target === this) EyouCoupon.close();
            });
        }
        EyouCoupon.tmpUseId = EyouCoupon.useId;
        EyouCoupon.tab(EyouCoupon.use.length ? 'use' : 'unuse');
        $('#eyCouponMask').addClass('show');
    },

    close: function () {
        $('#eyCouponMask').removeClass('show');
    },

    tab: function (type) {
        $('#eyCouponMask .ey-coupon-tabs span').removeClass('actived');
        $('#eyCouponMask .ey-coupon-tabs span[data-tab=' + type + ']').addClass('actived');

        var html = '';
        if ('use' == type) {
            if (!EyouCoupon.use.length) {
                html = '<div class="ey-coupon-empty">本单暂无可用优惠券</div>';
            } else {
                html += '<div class="ey-cp-none' + (EyouCoupon.tmpUseId ? '' : ' actived') + '" data-use-id="" onclick="EyouCoupon.pick(this, \'\');">'
                    + '<span class="ey-cp-none-name">不使用优惠券</span><span class="ey-cp-check"></span></div>';
                for (var i = 0; i < EyouCoupon.use.length; i++) {
                    html += EyouCoupon.card(EyouCoupon.use[i], true, 0 === i && EyouCoupon.use.length > 1);
                }
            }
        } else {
            if (!EyouCoupon.unuse.length) {
                html = '<div class="ey-coupon-empty">没有不可用的优惠券</div>';
            } else {
                for (var j = 0; j < EyouCoupon.unuse.length; j++) {
                    html += EyouCoupon.card(EyouCoupon.unuse[j], false, false);
                }
            }
        }
        $('#eyCouponList').html(html);
        EyouCoupon.ftTip();
    },

    /**
     * 券卡片
     */
    card: function (v, usable, best) {
        var price = parseFloat(v.coupon_price) || 0;
        var valueHtml = (2 == v.coupon_form)
            ? '<em>' + (parseFloat(v.coupon_discount) || 0) + '</em>折'
            : '￥<em>' + price + '</em>';
        if (2 == v.coupon_form && !usable) valueHtml = '<em>' + (parseFloat(v.coupon_discount) || 0) + '</em>折';

        var html = '<div class="ey-cp-card' + (usable ? '' : ' disabled')
            + (usable && EyouCoupon.tmpUseId == v.use_id ? ' actived' : '') + '"'
            + (usable ? ' onclick="EyouCoupon.pick(this, \'' + v.use_id + '\');"' : '')
            + ' data-use-id="' + v.use_id + '" data-price="' + price + '">'
            + '<div class="ey-cp-left">'
            + '<div class="ey-cp-amount">' + valueHtml + '</div>'
            + '<div class="ey-cp-cond">' + (v.conditions_use_text || '无门槛') + '</div>'
            + '</div>'
            + '<div class="ey-cp-body">'
            + '<div class="ey-cp-name">' + v.coupon_name
            + (best ? '<span class="ey-cp-tag">最优</span>' : '')
            + (usable && 2 == v.coupon_form ? '<span class="ey-cp-tag">抵扣￥' + price + '</span>' : '')
            + '</div>'
            + '<div class="ey-cp-meta">适用范围：' + (v.coupon_type_name || '全部商品') + '</div>'
            + '<div class="ey-cp-meta">有效期：' + (v.valid_text || '') + '</div>'
            + (usable ? '' : '<div class="ey-cp-reason">' + (v.unusable_reason || '本单不可用') + '</div>')
            + '</div>'
            + (usable ? '<span class="ey-cp-check"></span>' : '')
            + '</div>';
        return html;
    },

    /**
     * 弹层内选券
     */
    pick: function (obj, use_id) {
        EyouCoupon.tmpUseId = use_id ? String(use_id) : '';
        $('#eyCouponList .ey-cp-card, #eyCouponList .ey-cp-none').removeClass('actived');
        $(obj).addClass('actived');
        EyouCoupon.ftTip();
    },

    ftTip: function () {
        var tip = '不使用优惠券';
        if (EyouCoupon.tmpUseId) {
            var price = 0;
            for (var i = 0; i < EyouCoupon.use.length; i++) {
                if (EyouCoupon.use[i].use_id == EyouCoupon.tmpUseId) price = parseFloat(EyouCoupon.use[i].coupon_price) || 0;
            }
            tip = '已选 1 张，可抵扣 <em>￥' + price.toFixed(2) + '</em>';
        }
        $('#eyCouponFtTip').html(tip);
    },

    /**
     * 确定选券
     */
    confirm: function () {
        EyouCoupon.useId = EyouCoupon.tmpUseId;
        EyouCoupon.price = 0;
        for (var i = 0; i < EyouCoupon.use.length; i++) {
            if (EyouCoupon.use[i].use_id == EyouCoupon.useId) EyouCoupon.price = parseFloat(EyouCoupon.use[i].coupon_price) || 0;
        }

        $('#couponUseId').val(EyouCoupon.useId);
        $('#couponPrice').text(EyouCoupon.price > 0 ? ('-￥' + EyouCoupon.price.toFixed(2)) : '￥0.00');
        EyouCoupon.applyAmount();
        EyouCoupon.renderRow('ok');
        EyouCoupon.close();
    },

    /**
     * 抵扣后的应付总额
     */
    applyAmount: function () {
        var $amount = $('#TotalAmount');
        if (!$amount.length) return;

        var pay = EyouCoupon.gross - EyouCoupon.price;
        if (pay < 0) pay = 0;
        EyouCoupon.selfUpdate = true;
        $amount.text(pay.toFixed(2));
        setTimeout(function () {
            EyouCoupon.selfUpdate = false;
        }, 0);
    }
};

$(function () {
    EyouCoupon.init();
});
