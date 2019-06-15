/*! lightgallery - v1.3.9 - 2017-03-05
* http://sachinchoolur.github.io/lightGallery/
* Copyright (c) 2017 Sachin N; Licensed GPLv3 
*/
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(a.jQuery)}(this,function(a){!function(){"use strict";function b(b,d){if(this.el=b,this.$el=a(b),this.s=a.extend({},c,d),this.s.dynamic&&"undefined"!==this.s.dynamicEl&&this.s.dynamicEl.constructor===Array&&!this.s.dynamicEl.length)throw"When using dynamic mode, you must also define dynamicEl as an Array.";return this.modules={},this.lGalleryOn=!1,this.lgBusy=!1,this.hideBartimeout=!1,this.isTouch="ontouchstart"in document.documentElement,this.s.slideEndAnimatoin&&(this.s.hideControlOnEnd=!1),this.s.dynamic?this.$items=this.s.dynamicEl:"this"===this.s.selector?this.$items=this.$el:""!==this.s.selector?this.s.selectWithin?this.$items=a(this.s.selectWithin).find(this.s.selector):this.$items=this.$el.find(a(this.s.selector)):this.$items=this.$el.children(),this.$slide="",this.$outer="",this.init(),this}var c={mode:"lg-slide",cssEasing:"ease",easing:"linear",speed:600,height:"100%",width:"100%",addClass:"",startClass:"lg-start-zoom",backdropDuration:150,hideBarsDelay:6e3,useLeft:!1,closable:!0,loop:!0,escKey:!0,keyPress:!0,controls:!0,slideEndAnimatoin:!0,hideControlOnEnd:!1,mousewheel:!0,getCaptionFromTitleOrAlt:!0,appendSubHtmlTo:".lg-sub-html",subHtmlSelectorRelative:!1,preload:1,showAfterLoad:!0,selector:"",selectWithin:"",nextHtml:"",prevHtml:"",index:!1,iframeMaxWidth:"100%",download:!0,counter:!0,appendCounterTo:".lg-toolbar",swipeThreshold:50,enableSwipe:!0,enableDrag:!0,dynamic:!1,dynamicEl:[],galleryId:1};b.prototype.init=function(){var b=this;b.s.preload>b.$items.length&&(b.s.preload=b.$items.length);var c=window.location.hash;c.indexOf("lg="+this.s.galleryId)>0&&(b.index=parseInt(c.split("&slide=")[1],10),a("body").addClass("lg-from-hash"),a("body").hasClass("lg-on")||(setTimeout(function(){b.build(b.index)}),a("body").addClass("lg-on"))),b.s.dynamic?(b.$el.trigger("onBeforeOpen.lg"),b.index=b.s.index||0,a("body").hasClass("lg-on")||setTimeout(function(){b.build(b.index),a("body").addClass("lg-on")})):b.$items.on("click.lgcustom",function(c){try{c.preventDefault(),c.preventDefault()}catch(a){c.returnValue=!1}b.$el.trigger("onBeforeOpen.lg"),b.index=b.s.index||b.$items.index(this),a("body").hasClass("lg-on")||(b.build(b.index),a("body").addClass("lg-on"))})},b.prototype.build=function(b){var c=this;c.structure(),a.each(a.fn.lightGallery.modules,function(b){c.modules[b]=new a.fn.lightGallery.modules[b](c.el)}),c.slide(b,!1,!1,!1),c.s.keyPress&&c.keyPress(),c.$items.length>1&&(c.arrow(),setTimeout(function(){c.enableDrag(),c.enableSwipe()},50),c.s.mousewheel&&c.mousewheel()),c.counter(),c.closeGallery(),c.$el.trigger("onAfterOpen.lg"),c.$outer.on("mousemove.lg click.lg touchstart.lg",function(){c.$outer.removeClass("lg-hide-items"),clearTimeout(c.hideBartimeout),c.hideBartimeout=setTimeout(function(){c.$outer.addClass("lg-hide-items")},c.s.hideBarsDelay)}),c.$outer.trigger("mousemove.lg")},b.prototype.structure=function(){var b,c="",d="",e=0,f="",g=this;for(a("body").append('<div class="lg-backdrop"></div>'),a(".lg-backdrop").css("transition-duration",this.s.backdropDuration+"ms"),e=0;e<this.$items.length;e++)c+='<div class="lg-item"></div>';if(this.s.controls&&this.$items.length>1&&(d='<div class="lg-actions"><div class="lg-prev lg-icon">'+this.s.prevHtml+'</div><div class="lg-next lg-icon">'+this.s.nextHtml+"</div></div>"),".lg-sub-html"===this.s.appendSubHtmlTo&&(f='<div class="lg-sub-html"></div>'),b='<div class="lg-outer '+this.s.addClass+" "+this.s.startClass+'"><div class="lg" style="width:'+this.s.width+"; height:"+this.s.height+'"><div class="lg-inner">'+c+'</div><div class="lg-toolbar lg-group"><span class="lg-close lg-icon"></span></div>'+d+f+"</div></div>",a("body").append(b),this.$outer=a(".lg-outer"),this.$slide=this.$outer.find(".lg-item"),this.s.useLeft?(this.$outer.addClass("lg-use-left"),this.s.mode="lg-slide"):this.$outer.addClass("lg-use-css3"),g.setTop(),a(window).on("resize.lg orientationchange.lg",function(){setTimeout(function(){g.setTop()},100)}),this.$slide.eq(this.index).addClass("lg-current"),this.doCss()?this.$outer.addClass("lg-css3"):(this.$outer.addClass("lg-css"),this.s.speed=0),this.$outer.addClass(this.s.mode),this.s.enableDrag&&this.$items.length>1&&this.$outer.addClass("lg-grab"),this.s.showAfterLoad&&this.$outer.addClass("lg-show-after-load"),this.doCss()){var h=this.$outer.find(".lg-inner");h.css("transition-timing-function",this.s.cssEasing),h.css("transition-duration",this.s.speed+"ms")}setTimeout(function(){a(".lg-backdrop").addClass("in")}),setTimeout(function(){g.$outer.addClass("lg-visible")},this.s.backdropDuration),this.s.download&&this.$outer.find(".lg-toolbar").append('<a id="lg-download" target="_blank" download class="lg-download lg-icon"></a>'),this.prevScrollTop=a(window).scrollTop()},b.prototype.setTop=function(){if("100%"!==this.s.height){var b=a(window).height(),c=(b-parseInt(this.s.height,10))/2,d=this.$outer.find(".lg");b>=parseInt(this.s.height,10)?d.css("top",c+"px"):d.css("top","0px")}},b.prototype.doCss=function(){var a=function(){var a=["transition","MozTransition","WebkitTransition","OTransition","msTransition","KhtmlTransition"],b=document.documentElement,c=0;for(c=0;c<a.length;c++)if(a[c]in b.style)return!0};return!!a()},b.prototype.isVideo=function(a,b){var c;if(c=this.s.dynamic?this.s.dynamicEl[b].html:this.$items.eq(b).attr("data-html"),!a&&c)return{html5:!0};var d=a.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)/i),e=a.match(/\/\/(?:www\.)?vimeo.com\/([0-9a-z\-_]+)/i),f=a.match(/\/\/(?:www\.)?dai.ly\/([0-9a-z\-_]+)/i),g=a.match(/\/\/(?:www\.)?(?:vk\.com|vkontakte\.ru)\/(?:video_ext\.php\?)(.*)/i);return d?{youtube:d}:e?{vimeo:e}:f?{dailymotion:f}:g?{vk:g}:void 0},b.prototype.counter=function(){this.s.counter&&a(this.s.appendCounterTo).append('<div id="lg-counter"><span id="lg-counter-current">'+(parseInt(this.index,10)+1)+'</span> / <span id="lg-counter-all">'+this.$items.length+"</span></div>")},b.prototype.addHtml=function(b){var c,d,e=null;if(this.s.dynamic?this.s.dynamicEl[b].subHtmlUrl?c=this.s.dynamicEl[b].subHtmlUrl:e=this.s.dynamicEl[b].subHtml:(d=this.$items.eq(b),d.attr("data-sub-html-url")?c=d.attr("data-sub-html-url"):(e=d.attr("data-sub-html"),this.s.getCaptionFromTitleOrAlt&&!e&&(e=d.attr("title")||d.find("img").first().attr("alt")))),!c)if("undefined"!=typeof e&&null!==e){var f=e.substring(0,1);"."!==f&&"#"!==f||(e=this.s.subHtmlSelectorRelative&&!this.s.dynamic?d.find(e).html():a(e).html())}else e="";".lg-sub-html"===this.s.appendSubHtmlTo?c?this.$outer.find(this.s.appendSubHtmlTo).load(c):this.$outer.find(this.s.appendSubHtmlTo).html(e):c?this.$slide.eq(b).load(c):this.$slide.eq(b).append(e),"undefined"!=typeof e&&null!==e&&(""===e?this.$outer.find(this.s.appendSubHtmlTo).addClass("lg-empty-html"):this.$outer.find(this.s.appendSubHtmlTo).removeClass("lg-empty-html")),this.$el.trigger("onAfterAppendSubHtml.lg",[b])},b.prototype.preload=function(a){var b=1,c=1;for(b=1;b<=this.s.preload&&!(b>=this.$items.length-a);b++)this.loadContent(a+b,!1,0);for(c=1;c<=this.s.preload&&!(a-c<0);c++)this.loadContent(a-c,!1,0)},b.prototype.loadContent=function(b,c,d){var e,f,g,h,i,j,k=this,l=!1,m=function(b){for(var c=[],d=[],e=0;e<b.length;e++){var g=b[e].split(" ");""===g[0]&&g.splice(0,1),d.push(g[0]),c.push(g[1])}for(var h=a(window).width(),i=0;i<c.length;i++)if(parseInt(c[i],10)>h){f=d[i];break}};if(k.s.dynamic){if(k.s.dynamicEl[b].poster&&(l=!0,g=k.s.dynamicEl[b].poster),j=k.s.dynamicEl[b].html,f=k.s.dynamicEl[b].src,k.s.dynamicEl[b].responsive){var n=k.s.dynamicEl[b].responsive.split(",");m(n)}h=k.s.dynamicEl[b].srcset,i=k.s.dynamicEl[b].sizes}else{if(k.$items.eq(b).attr("data-poster")&&(l=!0,g=k.$items.eq(b).attr("data-poster")),j=k.$items.eq(b).attr("data-html"),f=k.$items.eq(b).attr("href")||k.$items.eq(b).attr("data-src"),k.$items.eq(b).attr("data-responsive")){var o=k.$items.eq(b).attr("data-responsive").split(",");m(o)}h=k.$items.eq(b).attr("data-srcset"),i=k.$items.eq(b).attr("data-sizes")}var p=!1;k.s.dynamic?k.s.dynamicEl[b].iframe&&(p=!0):"true"===k.$items.eq(b).attr("data-iframe")&&(p=!0);var q=k.isVideo(f,b);if(!k.$slide.eq(b).hasClass("lg-loaded")){if(p)k.$slide.eq(b).prepend('<div class="lg-video-cont" style="max-width:'+k.s.iframeMaxWidth+'"><div class="lg-video"><iframe class="lg-object" frameborder="0" src="'+f+'"  allowfullscreen="true"></iframe></div></div>');else if(l){var r="";r=q&&q.youtube?"lg-has-youtube":q&&q.vimeo?"lg-has-vimeo":"lg-has-html5",k.$slide.eq(b).prepend('<div class="lg-video-cont '+r+' "><div class="lg-video"><span class="lg-video-play"></span><img class="lg-object lg-has-poster" src="'+g+'" /></div></div>')}else q?(k.$slide.eq(b).prepend('<div class="lg-video-cont "><div class="lg-video"></div></div>'),k.$el.trigger("hasVideo.lg",[b,f,j])):k.$slide.eq(b).prepend('<div class="lg-img-wrap"><img class="lg-object lg-image" src="'+f+'" /></div>');if(k.$el.trigger("onAferAppendSlide.lg",[b]),e=k.$slide.eq(b).find(".lg-object"),i&&e.attr("sizes",i),h){e.attr("srcset",h);try{picturefill({elements:[e[0]]})}catch(a){console.warn("lightGallery :- If you want srcset to be supported for older browser please include picturefil version 2 javascript library in your document.")}}".lg-sub-html"!==this.s.appendSubHtmlTo&&k.addHtml(b),k.$slide.eq(b).addClass("lg-loaded")}k.$slide.eq(b).find(".lg-object").on("load.lg error.lg",function(){var c=0;d&&!a("body").hasClass("lg-from-hash")&&(c=d),setTimeout(function(){k.$slide.eq(b).addClass("lg-complete"),k.$el.trigger("onSlideItemLoad.lg",[b,d||0])},c)}),q&&q.html5&&!l&&k.$slide.eq(b).addClass("lg-complete"),c===!0&&(k.$slide.eq(b).hasClass("lg-complete")?k.preload(b):k.$slide.eq(b).find(".lg-object").on("load.lg error.lg",function(){k.preload(b)}))},b.prototype.slide=function(b,c,d,e){var f=this.$outer.find(".lg-current").index(),g=this;if(!g.lGalleryOn||f!==b){var h=this.$slide.length,i=g.lGalleryOn?this.s.speed:0;if(!g.lgBusy){if(this.s.download){var j;j=g.s.dynamic?g.s.dynamicEl[b].downloadUrl!==!1&&(g.s.dynamicEl[b].downloadUrl||g.s.dynamicEl[b].src):"false"!==g.$items.eq(b).attr("data-download-url")&&(g.$items.eq(b).attr("data-download-url")||g.$items.eq(b).attr("href")||g.$items.eq(b).attr("data-src")),j?(a("#lg-download").attr("href",j),g.$outer.removeClass("lg-hide-download")):g.$outer.addClass("lg-hide-download")}if(this.$el.trigger("onBeforeSlide.lg",[f,b,c,d]),g.lgBusy=!0,clearTimeout(g.hideBartimeout),".lg-sub-html"===this.s.appendSubHtmlTo&&setTimeout(function(){g.addHtml(b)},i),this.arrowDisable(b),e||(b<f?e="prev":b>f&&(e="next")),c){this.$slide.removeClass("lg-prev-slide lg-current lg-next-slide");var k,l;h>2?(k=b-1,l=b+1,0===b&&f===h-1?(l=0,k=h-1):b===h-1&&0===f&&(l=0,k=h-1)):(k=0,l=1),"prev"===e?g.$slide.eq(l).addClass("lg-next-slide"):g.$slide.eq(k).addClass("lg-prev-slide"),g.$slide.eq(b).addClass("lg-current")}else g.$outer.addClass("lg-no-trans"),this.$slide.removeClass("lg-prev-slide lg-next-slide"),"prev"===e?(this.$slide.eq(b).addClass("lg-prev-slide"),this.$slide.eq(f).addClass("lg-next-slide")):(this.$slide.eq(b).addClass("lg-next-slide"),this.$slide.eq(f).addClass("lg-prev-slide")),setTimeout(function(){g.$slide.removeClass("lg-current"),g.$slide.eq(b).addClass("lg-current"),g.$outer.removeClass("lg-no-trans")},50);g.lGalleryOn?(setTimeout(function(){g.loadContent(b,!0,0)},this.s.speed+50),setTimeout(function(){g.lgBusy=!1,g.$el.trigger("onAfterSlide.lg",[f,b,c,d])},this.s.speed)):(g.loadContent(b,!0,g.s.backdropDuration),g.lgBusy=!1,g.$el.trigger("onAfterSlide.lg",[f,b,c,d])),g.lGalleryOn=!0,this.s.counter&&a("#lg-counter-current").text(b+1)}}},b.prototype.goToNextSlide=function(a){var b=this,c=b.s.loop;a&&b.$slide.length<3&&(c=!1),b.lgBusy||(b.index+1<b.$slide.length?(b.index++,b.$el.trigger("onBeforeNextSlide.lg",[b.index]),b.slide(b.index,a,!1,"next")):c?(b.index=0,b.$el.trigger("onBeforeNextSlide.lg",[b.index]),b.slide(b.index,a,!1,"next")):b.s.slideEndAnimatoin&&!a&&(b.$outer.addClass("lg-right-end"),setTimeout(function(){b.$outer.removeClass("lg-right-end")},400)))},b.prototype.goToPrevSlide=function(a){var b=this,c=b.s.loop;a&&b.$slide.length<3&&(c=!1),b.lgBusy||(b.index>0?(b.index--,b.$el.trigger("onBeforePrevSlide.lg",[b.index,a]),b.slide(b.index,a,!1,"prev")):c?(b.index=b.$items.length-1,b.$el.trigger("onBeforePrevSlide.lg",[b.index,a]),b.slide(b.index,a,!1,"prev")):b.s.slideEndAnimatoin&&!a&&(b.$outer.addClass("lg-left-end"),setTimeout(function(){b.$outer.removeClass("lg-left-end")},400)))},b.prototype.keyPress=function(){var b=this;this.$items.length>1&&a(window).on("keyup.lg",function(a){b.$items.length>1&&(37===a.keyCode&&(a.preventDefault(),b.goToPrevSlide()),39===a.keyCode&&(a.preventDefault(),b.goToNextSlide()))}),a(window).on("keydown.lg",function(a){b.s.escKey===!0&&27===a.keyCode&&(a.preventDefault(),b.$outer.hasClass("lg-thumb-open")?b.$outer.removeClass("lg-thumb-open"):b.destroy())})},b.prototype.arrow=function(){var a=this;this.$outer.find(".lg-prev").on("click.lg",function(){a.goToPrevSlide()}),this.$outer.find(".lg-next").on("click.lg",function(){a.goToNextSlide()})},b.prototype.arrowDisable=function(a){!this.s.loop&&this.s.hideControlOnEnd&&(a+1<this.$slide.length?this.$outer.find(".lg-next").removeAttr("disabled").removeClass("disabled"):this.$outer.find(".lg-next").attr("disabled","disabled").addClass("disabled"),a>0?this.$outer.find(".lg-prev").removeAttr("disabled").removeClass("disabled"):this.$outer.find(".lg-prev").attr("disabled","disabled").addClass("disabled"))},b.prototype.setTranslate=function(a,b,c){this.s.useLeft?a.css("left",b):a.css({transform:"translate3d("+b+"px, "+c+"px, 0px)"})},b.prototype.touchMove=function(b,c){var d=c-b;Math.abs(d)>15&&(this.$outer.addClass("lg-dragging"),this.setTranslate(this.$slide.eq(this.index),d,0),this.setTranslate(a(".lg-prev-slide"),-this.$slide.eq(this.index).width()+d,0),this.setTranslate(a(".lg-next-slide"),this.$slide.eq(this.index).width()+d,0))},b.prototype.touchEnd=function(a){var b=this;"lg-slide"!==b.s.mode&&b.$outer.addClass("lg-slide"),this.$slide.not(".lg-current, .lg-prev-slide, .lg-next-slide").css("opacity","0"),setTimeout(function(){b.$outer.removeClass("lg-dragging"),a<0&&Math.abs(a)>b.s.swipeThreshold?b.goToNextSlide(!0):a>0&&Math.abs(a)>b.s.swipeThreshold?b.goToPrevSlide(!0):Math.abs(a)<5&&b.$el.trigger("onSlideClick.lg"),b.$slide.removeAttr("style")}),setTimeout(function(){b.$outer.hasClass("lg-dragging")||"lg-slide"===b.s.mode||b.$outer.removeClass("lg-slide")},b.s.speed+100)},b.prototype.enableSwipe=function(){var a=this,b=0,c=0,d=!1;a.s.enableSwipe&&a.isTouch&&a.doCss()&&(a.$slide.on("touchstart.lg",function(c){a.$outer.hasClass("lg-zoomed")||a.lgBusy||(c.preventDefault(),a.manageSwipeClass(),b=c.originalEvent.targetTouches[0].pageX)}),a.$slide.on("touchmove.lg",function(e){a.$outer.hasClass("lg-zoomed")||(e.preventDefault(),c=e.originalEvent.targetTouches[0].pageX,a.touchMove(b,c),d=!0)}),a.$slide.on("touchend.lg",function(){a.$outer.hasClass("lg-zoomed")||(d?(d=!1,a.touchEnd(c-b)):a.$el.trigger("onSlideClick.lg"))}))},b.prototype.enableDrag=function(){var b=this,c=0,d=0,e=!1,f=!1;b.s.enableDrag&&!b.isTouch&&b.doCss()&&(b.$slide.on("mousedown.lg",function(d){b.$outer.hasClass("lg-zoomed")||(a(d.target).hasClass("lg-object")||a(d.target).hasClass("lg-video-play"))&&(d.preventDefault(),b.lgBusy||(b.manageSwipeClass(),c=d.pageX,e=!0,b.$outer.scrollLeft+=1,b.$outer.scrollLeft-=1,b.$outer.removeClass("lg-grab").addClass("lg-grabbing"),b.$el.trigger("onDragstart.lg")))}),a(window).on("mousemove.lg",function(a){e&&(f=!0,d=a.pageX,b.touchMove(c,d),b.$el.trigger("onDragmove.lg"))}),a(window).on("mouseup.lg",function(g){f?(f=!1,b.touchEnd(d-c),b.$el.trigger("onDragend.lg")):(a(g.target).hasClass("lg-object")||a(g.target).hasClass("lg-video-play"))&&b.$el.trigger("onSlideClick.lg"),e&&(e=!1,b.$outer.removeClass("lg-grabbing").addClass("lg-grab"))}))},b.prototype.manageSwipeClass=function(){var a=this.index+1,b=this.index-1;this.s.loop&&this.$slide.length>2&&(0===this.index?b=this.$slide.length-1:this.index===this.$slide.length-1&&(a=0)),this.$slide.removeClass("lg-next-slide lg-prev-slide"),b>-1&&this.$slide.eq(b).addClass("lg-prev-slide"),this.$slide.eq(a).addClass("lg-next-slide")},b.prototype.mousewheel=function(){var a=this;a.$outer.on("mousewheel.lg",function(b){b.deltaY&&(b.deltaY>0?a.goToPrevSlide():a.goToNextSlide(),b.preventDefault())})},b.prototype.closeGallery=function(){var b=this,c=!1;this.$outer.find(".lg-close").on("click.lg",function(){b.destroy()}),b.s.closable&&(b.$outer.on("mousedown.lg",function(b){c=!!(a(b.target).is(".lg-outer")||a(b.target).is(".lg-item ")||a(b.target).is(".lg-img-wrap"))}),b.$outer.on("mouseup.lg",function(d){(a(d.target).is(".lg-outer")||a(d.target).is(".lg-item ")||a(d.target).is(".lg-img-wrap")&&c)&&(b.$outer.hasClass("lg-dragging")||b.destroy())}))},b.prototype.destroy=function(b){var c=this;b||(c.$el.trigger("onBeforeClose.lg"),a(window).scrollTop(c.prevScrollTop)),b&&(c.s.dynamic||this.$items.off("click.lg click.lgcustom"),a.removeData(c.el,"lightGallery")),this.$el.off(".lg.tm"),a.each(a.fn.lightGallery.modules,function(a){c.modules[a]&&c.modules[a].destroy()}),this.lGalleryOn=!1,clearTimeout(c.hideBartimeout),this.hideBartimeout=!1,a(window).off(".lg"),a("body").removeClass("lg-on lg-from-hash"),c.$outer&&c.$outer.removeClass("lg-visible"),a(".lg-backdrop").removeClass("in"),setTimeout(function(){c.$outer&&c.$outer.remove(),a(".lg-backdrop").remove(),b||c.$el.trigger("onCloseAfter.lg")},c.s.backdropDuration+50)},a.fn.lightGallery=function(c){return this.each(function(){if(a.data(this,"lightGallery"))try{a(this).data("lightGallery").init()}catch(a){console.error("lightGallery has not initiated properly")}else a.data(this,"lightGallery",new b(this,c))})},a.fn.lightGallery.modules={}}()});

/*! lg-fullscreen - v1.0.0 - 2016-09-20
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2016 Sachin N; Licensed GPLv3 
*/
!function(a,b){"function"==typeof define&&define.amd?define([],function(){return b()}):"object"==typeof exports?module.exports=b():b()}(this,function(){!function(a,b,c,d){"use strict";var e={fullScreen:!0},f=function(b){return this.core=a(b).data("lightGallery"),this.$el=a(b),this.core.s=a.extend({},e,this.core.s),this.init(),this};f.prototype.init=function(){var a="";if(this.core.s.fullScreen){if(!(c.fullscreenEnabled||c.webkitFullscreenEnabled||c.mozFullScreenEnabled||c.msFullscreenEnabled))return;a='<span class="lg-fullscreen lg-icon"></span>',this.core.$outer.find(".lg-toolbar").append(a),this.fullScreen()}},f.prototype.requestFullscreen=function(){var a=c.documentElement;a.requestFullscreen?a.requestFullscreen():a.msRequestFullscreen?a.msRequestFullscreen():a.mozRequestFullScreen?a.mozRequestFullScreen():a.webkitRequestFullscreen&&a.webkitRequestFullscreen()},f.prototype.exitFullscreen=function(){c.exitFullscreen?c.exitFullscreen():c.msExitFullscreen?c.msExitFullscreen():c.mozCancelFullScreen?c.mozCancelFullScreen():c.webkitExitFullscreen&&c.webkitExitFullscreen()},f.prototype.fullScreen=function(){var b=this;a(c).on("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg",function(){b.core.$outer.toggleClass("lg-fullscreen-on")}),this.core.$outer.find(".lg-fullscreen").on("click.lg",function(){c.fullscreenElement||c.mozFullScreenElement||c.webkitFullscreenElement||c.msFullscreenElement?b.exitFullscreen():b.requestFullscreen()})},f.prototype.destroy=function(){this.exitFullscreen(),a(c).off("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg")},a.fn.lightGallery.modules.fullscreen=f}(jQuery,window,document)});


/*! lg-thumbnail - v1.0.3 - 2017-02-05
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 
*/
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(){"use strict";var b={thumbnail:!0,animateThumb:!0,currentPagerPosition:"middle",thumbWidth:100,thumbContHeight:100,thumbMargin:5,exThumbImage:!1,showThumbByDefault:!0,toogleThumb:!0,pullCaptionUp:!0,enableThumbDrag:!0,enableThumbSwipe:!0,swipeThreshold:50,loadYoutubeThumbnail:!0,youtubeThumbSize:1,loadVimeoThumbnail:!0,vimeoThumbSize:"thumbnail_small",loadDailymotionThumbnail:!0},c=function(c){return this.core=a(c).data("lightGallery"),this.core.s=a.extend({},b,this.core.s),this.$el=a(c),this.$thumbOuter=null,this.thumbOuterWidth=0,this.thumbTotalWidth=this.core.$items.length*(this.core.s.thumbWidth+this.core.s.thumbMargin),this.thumbIndex=this.core.index,this.left=0,this.init(),this};c.prototype.init=function(){var a=this;this.core.s.thumbnail&&this.core.$items.length>1&&(this.core.s.showThumbByDefault&&setTimeout(function(){a.core.$outer.addClass("lg-thumb-open")},700),this.core.s.pullCaptionUp&&this.core.$outer.addClass("lg-pull-caption-up"),this.build(),this.core.s.animateThumb?(this.core.s.enableThumbDrag&&!this.core.isTouch&&this.core.doCss()&&this.enableThumbDrag(),this.core.s.enableThumbSwipe&&this.core.isTouch&&this.core.doCss()&&this.enableThumbSwipe(),this.thumbClickable=!1):this.thumbClickable=!0,this.toogle(),this.thumbkeyPress())},c.prototype.build=function(){function b(a,b,c){var g,h=d.core.isVideo(a,c)||{},i="";h.youtube||h.vimeo||h.dailymotion?h.youtube?g=d.core.s.loadYoutubeThumbnail?"//img.youtube.com/vi/"+h.youtube[1]+"/"+d.core.s.youtubeThumbSize+".jpg":b:h.vimeo?d.core.s.loadVimeoThumbnail?(g="//i.vimeocdn.com/video/error_"+f+".jpg",i=h.vimeo[1]):g=b:h.dailymotion&&(g=d.core.s.loadDailymotionThumbnail?"//www.dailymotion.com/thumbnail/video/"+h.dailymotion[1]:b):g=b,e+='<div data-vimeo-id="'+i+'" class="lg-thumb-item" style="width:'+d.core.s.thumbWidth+"px; margin-right: "+d.core.s.thumbMargin+'px"><img src="'+g+'" /></div>',i=""}var c,d=this,e="",f="",g='<div class="lg-thumb-outer"><div class="lg-thumb lg-group"></div></div>';switch(this.core.s.vimeoThumbSize){case"thumbnail_large":f="640";break;case"thumbnail_medium":f="200x150";break;case"thumbnail_small":f="100x75"}if(d.core.$outer.addClass("lg-has-thumb"),d.core.$outer.find(".lg").append(g),d.$thumbOuter=d.core.$outer.find(".lg-thumb-outer"),d.thumbOuterWidth=d.$thumbOuter.width(),d.core.s.animateThumb&&d.core.$outer.find(".lg-thumb").css({width:d.thumbTotalWidth+"px",position:"relative"}),this.core.s.animateThumb&&d.$thumbOuter.css("height",d.core.s.thumbContHeight+"px"),d.core.s.dynamic)for(var h=0;h<d.core.s.dynamicEl.length;h++)b(d.core.s.dynamicEl[h].src,d.core.s.dynamicEl[h].thumb,h);else d.core.$items.each(function(c){d.core.s.exThumbImage?b(a(this).attr("href")||a(this).attr("data-src"),a(this).attr(d.core.s.exThumbImage),c):b(a(this).attr("href")||a(this).attr("data-src"),a(this).find("img").attr("src"),c)});d.core.$outer.find(".lg-thumb").html(e),c=d.core.$outer.find(".lg-thumb-item"),c.each(function(){var b=a(this),c=b.attr("data-vimeo-id");c&&a.getJSON("//www.vimeo.com/api/v2/video/"+c+".json?callback=?",{format:"json"},function(a){b.find("img").attr("src",a[0][d.core.s.vimeoThumbSize])})}),c.eq(d.core.index).addClass("active"),d.core.$el.on("onBeforeSlide.lg.tm",function(){c.removeClass("active"),c.eq(d.core.index).addClass("active")}),c.on("click.lg touchend.lg",function(){var b=a(this);setTimeout(function(){(d.thumbClickable&&!d.core.lgBusy||!d.core.doCss())&&(d.core.index=b.index(),d.core.slide(d.core.index,!1,!0,!1))},50)}),d.core.$el.on("onBeforeSlide.lg.tm",function(){d.animateThumb(d.core.index)}),a(window).on("resize.lg.thumb orientationchange.lg.thumb",function(){setTimeout(function(){d.animateThumb(d.core.index),d.thumbOuterWidth=d.$thumbOuter.width()},200)})},c.prototype.setTranslate=function(a){this.core.$outer.find(".lg-thumb").css({transform:"translate3d(-"+a+"px, 0px, 0px)"})},c.prototype.animateThumb=function(a){var b=this.core.$outer.find(".lg-thumb");if(this.core.s.animateThumb){var c;switch(this.core.s.currentPagerPosition){case"left":c=0;break;case"middle":c=this.thumbOuterWidth/2-this.core.s.thumbWidth/2;break;case"right":c=this.thumbOuterWidth-this.core.s.thumbWidth}this.left=(this.core.s.thumbWidth+this.core.s.thumbMargin)*a-1-c,this.left>this.thumbTotalWidth-this.thumbOuterWidth&&(this.left=this.thumbTotalWidth-this.thumbOuterWidth),this.left<0&&(this.left=0),this.core.lGalleryOn?(b.hasClass("on")||this.core.$outer.find(".lg-thumb").css("transition-duration",this.core.s.speed+"ms"),this.core.doCss()||b.animate({left:-this.left+"px"},this.core.s.speed)):this.core.doCss()||b.css("left",-this.left+"px"),this.setTranslate(this.left)}},c.prototype.enableThumbDrag=function(){var b=this,c=0,d=0,e=!1,f=!1,g=0;b.$thumbOuter.addClass("lg-grab"),b.core.$outer.find(".lg-thumb").on("mousedown.lg.thumb",function(a){b.thumbTotalWidth>b.thumbOuterWidth&&(a.preventDefault(),c=a.pageX,e=!0,b.core.$outer.scrollLeft+=1,b.core.$outer.scrollLeft-=1,b.thumbClickable=!1,b.$thumbOuter.removeClass("lg-grab").addClass("lg-grabbing"))}),a(window).on("mousemove.lg.thumb",function(a){e&&(g=b.left,f=!0,d=a.pageX,b.$thumbOuter.addClass("lg-dragging"),g-=d-c,g>b.thumbTotalWidth-b.thumbOuterWidth&&(g=b.thumbTotalWidth-b.thumbOuterWidth),g<0&&(g=0),b.setTranslate(g))}),a(window).on("mouseup.lg.thumb",function(){f?(f=!1,b.$thumbOuter.removeClass("lg-dragging"),b.left=g,Math.abs(d-c)<b.core.s.swipeThreshold&&(b.thumbClickable=!0)):b.thumbClickable=!0,e&&(e=!1,b.$thumbOuter.removeClass("lg-grabbing").addClass("lg-grab"))})},c.prototype.enableThumbSwipe=function(){var a=this,b=0,c=0,d=!1,e=0;a.core.$outer.find(".lg-thumb").on("touchstart.lg",function(c){a.thumbTotalWidth>a.thumbOuterWidth&&(c.preventDefault(),b=c.originalEvent.targetTouches[0].pageX,a.thumbClickable=!1)}),a.core.$outer.find(".lg-thumb").on("touchmove.lg",function(f){a.thumbTotalWidth>a.thumbOuterWidth&&(f.preventDefault(),c=f.originalEvent.targetTouches[0].pageX,d=!0,a.$thumbOuter.addClass("lg-dragging"),e=a.left,e-=c-b,e>a.thumbTotalWidth-a.thumbOuterWidth&&(e=a.thumbTotalWidth-a.thumbOuterWidth),e<0&&(e=0),a.setTranslate(e))}),a.core.$outer.find(".lg-thumb").on("touchend.lg",function(){a.thumbTotalWidth>a.thumbOuterWidth&&d?(d=!1,a.$thumbOuter.removeClass("lg-dragging"),Math.abs(c-b)<a.core.s.swipeThreshold&&(a.thumbClickable=!0),a.left=e):a.thumbClickable=!0})},c.prototype.toogle=function(){var a=this;a.core.s.toogleThumb&&(a.core.$outer.addClass("lg-can-toggle"),a.$thumbOuter.append('<span class="lg-toogle-thumb lg-icon"></span>'),a.core.$outer.find(".lg-toogle-thumb").on("click.lg",function(){a.core.$outer.toggleClass("lg-thumb-open")}))},c.prototype.thumbkeyPress=function(){var b=this;a(window).on("keydown.lg.thumb",function(a){38===a.keyCode?(a.preventDefault(),b.core.$outer.addClass("lg-thumb-open")):40===a.keyCode&&(a.preventDefault(),b.core.$outer.removeClass("lg-thumb-open"))})},c.prototype.destroy=function(){this.core.s.thumbnail&&this.core.$items.length>1&&(a(window).off("resize.lg.thumb orientationchange.lg.thumb keydown.lg.thumb"),this.$thumbOuter.remove(),this.core.$outer.removeClass("lg-has-thumb"))},a.fn.lightGallery.modules.Thumbnail=c}()});


/*! lg-zoom - v1.0.4 - 2016-12-20
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2016 Sachin N; Licensed GPLv3 
*/
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(){"use strict";var b=function(){var a=!1,b=navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./);return b&&parseInt(b[2],10)<54&&(a=!0),a},c={scale:1,zoom:!0,actualSize:!0,enableZoomAfter:300,useLeftForZoom:b()},d=function(b){return this.core=a(b).data("lightGallery"),this.core.s=a.extend({},c,this.core.s),this.core.s.zoom&&this.core.doCss()&&(this.init(),this.zoomabletimeout=!1,this.pageX=a(window).width()/2,this.pageY=a(window).height()/2+a(window).scrollTop()),this};d.prototype.init=function(){var b=this,c='<span id="lg-zoom-in" class="lg-icon"></span><span id="lg-zoom-out" class="lg-icon"></span>';b.core.s.actualSize&&(c+='<span id="lg-actual-size" class="lg-icon"></span>'),b.core.s.useLeftForZoom?b.core.$outer.addClass("lg-use-left-for-zoom"):b.core.$outer.addClass("lg-use-transition-for-zoom"),this.core.$outer.find(".lg-toolbar").append(c),b.core.$el.on("onSlideItemLoad.lg.tm.zoom",function(c,d,e){var f=b.core.s.enableZoomAfter+e;a("body").hasClass("lg-from-hash")&&e?f=0:a("body").removeClass("lg-from-hash"),b.zoomabletimeout=setTimeout(function(){b.core.$slide.eq(d).addClass("lg-zoomable")},f+30)});var d=1,e=function(c){var d,e,f=b.core.$outer.find(".lg-current .lg-image"),g=(a(window).width()-f.prop("offsetWidth"))/2,h=(a(window).height()-f.prop("offsetHeight"))/2+a(window).scrollTop();d=b.pageX-g,e=b.pageY-h;var i=(c-1)*d,j=(c-1)*e;f.css("transform","scale3d("+c+", "+c+", 1)").attr("data-scale",c),b.core.s.useLeftForZoom?f.parent().css({left:-i+"px",top:-j+"px"}).attr("data-x",i).attr("data-y",j):f.parent().css("transform","translate3d(-"+i+"px, -"+j+"px, 0)").attr("data-x",i).attr("data-y",j)},f=function(){d>1?b.core.$outer.addClass("lg-zoomed"):b.resetZoom(),d<1&&(d=1),e(d)},g=function(c,e,g,h){var i,j=e.prop("offsetWidth");i=b.core.s.dynamic?b.core.s.dynamicEl[g].width||e[0].naturalWidth||j:b.core.$items.eq(g).attr("data-width")||e[0].naturalWidth||j;var k;b.core.$outer.hasClass("lg-zoomed")?d=1:i>j&&(k=i/j,d=k||2),h?(b.pageX=a(window).width()/2,b.pageY=a(window).height()/2+a(window).scrollTop()):(b.pageX=c.pageX||c.originalEvent.targetTouches[0].pageX,b.pageY=c.pageY||c.originalEvent.targetTouches[0].pageY),f(),setTimeout(function(){b.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")},10)},h=!1;b.core.$el.on("onAferAppendSlide.lg.tm.zoom",function(a,c){var d=b.core.$slide.eq(c).find(".lg-image");d.on("dblclick",function(a){g(a,d,c)}),d.on("touchstart",function(a){h?(clearTimeout(h),h=null,g(a,d,c)):h=setTimeout(function(){h=null},300),a.preventDefault()})}),a(window).on("resize.lg.zoom scroll.lg.zoom orientationchange.lg.zoom",function(){b.pageX=a(window).width()/2,b.pageY=a(window).height()/2+a(window).scrollTop(),e(d)}),a("#lg-zoom-out").on("click.lg",function(){b.core.$outer.find(".lg-current .lg-image").length&&(d-=b.core.s.scale,f())}),a("#lg-zoom-in").on("click.lg",function(){b.core.$outer.find(".lg-current .lg-image").length&&(d+=b.core.s.scale,f())}),a("#lg-actual-size").on("click.lg",function(a){g(a,b.core.$slide.eq(b.core.index).find(".lg-image"),b.core.index,!0)}),b.core.$el.on("onBeforeSlide.lg.tm",function(){d=1,b.resetZoom()}),b.core.isTouch||b.zoomDrag(),b.core.isTouch&&b.zoomSwipe()},d.prototype.resetZoom=function(){this.core.$outer.removeClass("lg-zoomed"),this.core.$slide.find(".lg-img-wrap").removeAttr("style data-x data-y"),this.core.$slide.find(".lg-image").removeAttr("style data-scale"),this.pageX=a(window).width()/2,this.pageY=a(window).height()/2+a(window).scrollTop()},d.prototype.zoomSwipe=function(){var a=this,b={},c={},d=!1,e=!1,f=!1;a.core.$slide.on("touchstart.lg",function(c){if(a.core.$outer.hasClass("lg-zoomed")){var d=a.core.$slide.eq(a.core.index).find(".lg-object");f=d.prop("offsetHeight")*d.attr("data-scale")>a.core.$outer.find(".lg").height(),e=d.prop("offsetWidth")*d.attr("data-scale")>a.core.$outer.find(".lg").width(),(e||f)&&(c.preventDefault(),b={x:c.originalEvent.targetTouches[0].pageX,y:c.originalEvent.targetTouches[0].pageY})}}),a.core.$slide.on("touchmove.lg",function(g){if(a.core.$outer.hasClass("lg-zoomed")){var h,i,j=a.core.$slide.eq(a.core.index).find(".lg-img-wrap");g.preventDefault(),d=!0,c={x:g.originalEvent.targetTouches[0].pageX,y:g.originalEvent.targetTouches[0].pageY},a.core.$outer.addClass("lg-zoom-dragging"),i=f?-Math.abs(j.attr("data-y"))+(c.y-b.y):-Math.abs(j.attr("data-y")),h=e?-Math.abs(j.attr("data-x"))+(c.x-b.x):-Math.abs(j.attr("data-x")),(Math.abs(c.x-b.x)>15||Math.abs(c.y-b.y)>15)&&(a.core.s.useLeftForZoom?j.css({left:h+"px",top:i+"px"}):j.css("transform","translate3d("+h+"px, "+i+"px, 0)"))}}),a.core.$slide.on("touchend.lg",function(){a.core.$outer.hasClass("lg-zoomed")&&d&&(d=!1,a.core.$outer.removeClass("lg-zoom-dragging"),a.touchendZoom(b,c,e,f))})},d.prototype.zoomDrag=function(){var b=this,c={},d={},e=!1,f=!1,g=!1,h=!1;b.core.$slide.on("mousedown.lg.zoom",function(d){var f=b.core.$slide.eq(b.core.index).find(".lg-object");h=f.prop("offsetHeight")*f.attr("data-scale")>b.core.$outer.find(".lg").height(),g=f.prop("offsetWidth")*f.attr("data-scale")>b.core.$outer.find(".lg").width(),b.core.$outer.hasClass("lg-zoomed")&&a(d.target).hasClass("lg-object")&&(g||h)&&(d.preventDefault(),c={x:d.pageX,y:d.pageY},e=!0,b.core.$outer.scrollLeft+=1,b.core.$outer.scrollLeft-=1,b.core.$outer.removeClass("lg-grab").addClass("lg-grabbing"))}),a(window).on("mousemove.lg.zoom",function(a){if(e){var i,j,k=b.core.$slide.eq(b.core.index).find(".lg-img-wrap");f=!0,d={x:a.pageX,y:a.pageY},b.core.$outer.addClass("lg-zoom-dragging"),j=h?-Math.abs(k.attr("data-y"))+(d.y-c.y):-Math.abs(k.attr("data-y")),i=g?-Math.abs(k.attr("data-x"))+(d.x-c.x):-Math.abs(k.attr("data-x")),b.core.s.useLeftForZoom?k.css({left:i+"px",top:j+"px"}):k.css("transform","translate3d("+i+"px, "+j+"px, 0)")}}),a(window).on("mouseup.lg.zoom",function(a){e&&(e=!1,b.core.$outer.removeClass("lg-zoom-dragging"),!f||c.x===d.x&&c.y===d.y||(d={x:a.pageX,y:a.pageY},b.touchendZoom(c,d,g,h)),f=!1),b.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")})},d.prototype.touchendZoom=function(a,b,c,d){var e=this,f=e.core.$slide.eq(e.core.index).find(".lg-img-wrap"),g=e.core.$slide.eq(e.core.index).find(".lg-object"),h=-Math.abs(f.attr("data-x"))+(b.x-a.x),i=-Math.abs(f.attr("data-y"))+(b.y-a.y),j=(e.core.$outer.find(".lg").height()-g.prop("offsetHeight"))/2,k=Math.abs(g.prop("offsetHeight")*Math.abs(g.attr("data-scale"))-e.core.$outer.find(".lg").height()+j),l=(e.core.$outer.find(".lg").width()-g.prop("offsetWidth"))/2,m=Math.abs(g.prop("offsetWidth")*Math.abs(g.attr("data-scale"))-e.core.$outer.find(".lg").width()+l);(Math.abs(b.x-a.x)>15||Math.abs(b.y-a.y)>15)&&(d&&(i<=-k?i=-k:i>=-j&&(i=-j)),c&&(h<=-m?h=-m:h>=-l&&(h=-l)),d?f.attr("data-y",Math.abs(i)):i=-Math.abs(f.attr("data-y")),c?f.attr("data-x",Math.abs(h)):h=-Math.abs(f.attr("data-x")),e.core.s.useLeftForZoom?f.css({left:h+"px",top:i+"px"}):f.css("transform","translate3d("+h+"px, "+i+"px, 0)"))},d.prototype.destroy=function(){var b=this;b.core.$el.off(".lg.zoom"),a(window).off(".lg.zoom"),b.core.$slide.off(".lg.zoom"),b.core.$el.off(".lg.tm.zoom"),b.resetZoom(),clearTimeout(b.zoomabletimeout),b.zoomabletimeout=!1},a.fn.lightGallery.modules.zoom=d}()});


/*! PhotoSwipe - v4.1.2 - 2017-04-05
* http://photoswipe.com
* Copyright (c) 2017 Dmitry Semenov; 
*/
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.PhotoSwipe = factory();
    }
})(this, function () {

    'use strict';
    var PhotoSwipe = function(template, UiClass, items, options){

        /*>>framework-bridge*/
        /**
         *
         * Set of generic functions used by gallery.
         *
         * You're free to modify anything here as long as functionality is kept.
         *
         */
        var framework = {
            features: null,
            bind: function(target, type, listener, unbind) {
                var methodName = (unbind ? 'remove' : 'add') + 'EventListener';
                type = type.split(' ');
                for(var i = 0; i < type.length; i++) {
                    if(type[i]) {
                        target[methodName]( type[i], listener, false);
                    }
                }
            },
            isArray: function(obj) {
                return (obj instanceof Array);
            },
            createEl: function(classes, tag) {
                var el = document.createElement(tag || 'div');
                if(classes) {
                    el.className = classes;
                }
                return el;
            },
            getScrollY: function() {
                var yOffset = window.pageYOffset;
                return yOffset !== undefined ? yOffset : document.documentElement.scrollTop;
            },
            unbind: function(target, type, listener) {
                framework.bind(target,type,listener,true);
            },
            removeClass: function(el, className) {
                var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
                el.className = el.className.replace(reg, ' ').replace(/^\s\s*/, '').replace(/\s\s*$/, '');
            },
            addClass: function(el, className) {
                if( !framework.hasClass(el,className) ) {
                    el.className += (el.className ? ' ' : '') + className;
                }
            },
            hasClass: function(el, className) {
                return el.className && new RegExp('(^|\\s)' + className + '(\\s|$)').test(el.className);
            },
            getChildByClass: function(parentEl, childClassName) {
                var node = parentEl.firstChild;
                while(node) {
                    if( framework.hasClass(node, childClassName) ) {
                        return node;
                    }
                    node = node.nextSibling;
                }
            },
            arraySearch: function(array, value, key) {
                var i = array.length;
                while(i--) {
                    if(array[i][key] === value) {
                        return i;
                    }
                }
                return -1;
            },
            extend: function(o1, o2, preventOverwrite) {
                for (var prop in o2) {
                    if (o2.hasOwnProperty(prop)) {
                        if(preventOverwrite && o1.hasOwnProperty(prop)) {
                            continue;
                        }
                        o1[prop] = o2[prop];
                    }
                }
            },
            easing: {
                sine: {
                    out: function(k) {
                        return Math.sin(k * (Math.PI / 2));
                    },
                    inOut: function(k) {
                        return - (Math.cos(Math.PI * k) - 1) / 2;
                    }
                },
                cubic: {
                    out: function(k) {
                        return --k * k * k + 1;
                    }
                }
                /*
                    elastic: {
                        out: function ( k ) {

                            var s, a = 0.1, p = 0.4;
                            if ( k === 0 ) return 0;
                            if ( k === 1 ) return 1;
                            if ( !a || a < 1 ) { a = 1; s = p / 4; }
                            else s = p * Math.asin( 1 / a ) / ( 2 * Math.PI );
                            return ( a * Math.pow( 2, - 10 * k) * Math.sin( ( k - s ) * ( 2 * Math.PI ) / p ) + 1 );

                        },
                    },
                    back: {
                        out: function ( k ) {
                            var s = 1.70158;
                            return --k * k * ( ( s + 1 ) * k + s ) + 1;
                        }
                    }
                */
            },

            /**
             *
             * @return {object}
             *
             * {
             *  raf : request animation frame function
             *  caf : cancel animation frame function
             *  transfrom : transform property key (with vendor), or null if not supported
             *  oldIE : IE8 or below
             * }
             *
             */
            detectFeatures: function() {
                if(framework.features) {
                    return framework.features;
                }
                var helperEl = framework.createEl(),
                    helperStyle = helperEl.style,
                    vendor = '',
                    features = {};

                // IE8 and below
                features.oldIE = document.all && !document.addEventListener;

                features.touch = 'ontouchstart' in window;

                if(window.requestAnimationFrame) {
                    features.raf = window.requestAnimationFrame;
                    features.caf = window.cancelAnimationFrame;
                }

                features.pointerEvent = navigator.pointerEnabled || navigator.msPointerEnabled;

                // fix false-positive detection of old Android in new IE
                // (IE11 ua string contains "Android 4.0")

                if(!features.pointerEvent) {

                    var ua = navigator.userAgent;

                    // Detect if device is iPhone or iPod and if it's older than iOS 8
                    // http://stackoverflow.com/a/14223920
                    //
                    // This detection is made because of buggy top/bottom toolbars
                    // that don't trigger window.resize event.
                    // For more info refer to _isFixedPosition variable in core.js

                    if (/iP(hone|od)/.test(navigator.platform)) {
                        var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
                        if(v && v.length > 0) {
                            v = parseInt(v[1], 10);
                            if(v >= 1 && v < 8 ) {
                                features.isOldIOSPhone = true;
                            }
                        }
                    }

                    // Detect old Android (before KitKat)
                    // due to bugs related to position:fixed
                    // http://stackoverflow.com/questions/7184573/pick-up-the-android-version-in-the-browser-by-javascript

                    var match = ua.match(/Android\s([0-9\.]*)/);
                    var androidversion =  match ? match[1] : 0;
                    androidversion = parseFloat(androidversion);
                    if(androidversion >= 1 ) {
                        if(androidversion < 4.4) {
                            features.isOldAndroid = true; // for fixed position bug & performance
                        }
                        features.androidVersion = androidversion; // for touchend bug
                    }
                    features.isMobileOpera = /opera mini|opera mobi/i.test(ua);

                    // p.s. yes, yes, UA sniffing is bad, propose your solution for above bugs.
                }

                var styleChecks = ['transform', 'perspective', 'animationName'],
                    vendors = ['', 'webkit','Moz','ms','O'],
                    styleCheckItem,
                    styleName;

                for(var i = 0; i < 4; i++) {
                    vendor = vendors[i];

                    for(var a = 0; a < 3; a++) {
                        styleCheckItem = styleChecks[a];

                        // uppercase first letter of property name, if vendor is present
                        styleName = vendor + (vendor ?
                                                styleCheckItem.charAt(0).toUpperCase() + styleCheckItem.slice(1) :
                                                styleCheckItem);

                        if(!features[styleCheckItem] && styleName in helperStyle ) {
                            features[styleCheckItem] = styleName;
                        }
                    }

                    if(vendor && !features.raf) {
                        vendor = vendor.toLowerCase();
                        features.raf = window[vendor+'RequestAnimationFrame'];
                        if(features.raf) {
                            features.caf = window[vendor+'CancelAnimationFrame'] ||
                                            window[vendor+'CancelRequestAnimationFrame'];
                        }
                    }
                }

                if(!features.raf) {
                    var lastTime = 0;
                    features.raf = function(fn) {
                        var currTime = new Date().getTime();
                        var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                        var id = window.setTimeout(function() { fn(currTime + timeToCall); }, timeToCall);
                        lastTime = currTime + timeToCall;
                        return id;
                    };
                    features.caf = function(id) { clearTimeout(id); };
                }

                // Detect SVG support
                features.svg = !!document.createElementNS &&
                                !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;

                framework.features = features;

                return features;
            }
        };

        framework.detectFeatures();

        // Override addEventListener for old versions of IE
        if(framework.features.oldIE) {

            framework.bind = function(target, type, listener, unbind) {

                type = type.split(' ');

                var methodName = (unbind ? 'detach' : 'attach') + 'Event',
                    evName,
                    _handleEv = function() {
                        listener.handleEvent.call(listener);
                    };

                for(var i = 0; i < type.length; i++) {
                    evName = type[i];
                    if(evName) {

                        if(typeof listener === 'object' && listener.handleEvent) {
                            if(!unbind) {
                                listener['oldIE' + evName] = _handleEv;
                            } else {
                                if(!listener['oldIE' + evName]) {
                                    return false;
                                }
                            }

                            target[methodName]( 'on' + evName, listener['oldIE' + evName]);
                        } else {
                            target[methodName]( 'on' + evName, listener);
                        }

                    }
                }
            };

        }

        /*>>framework-bridge*/

        /*>>core*/
        //function(template, UiClass, items, options)

        var self = this;

        /**
         * Static vars, don't change unless you know what you're doing.
         */
        var DOUBLE_TAP_RADIUS = 25,
            NUM_HOLDERS = 3;

        /**
         * Options
         */
        var _options = {
            allowPanToNext:true,
            spacing: 0.05,
            bgOpacity: 1,
            mouseUsed: false,
            loop: false,
            pinchToClose: true,
            closeOnScroll: true,
            closeOnVerticalDrag: true,
            verticalDragRange: 0.75,
            hideAnimationDuration: 333,
            showAnimationDuration: 333,
            showHideOpacity: false,
            focus: true,
            escKey: true,
            arrowKeys: true,
            mainScrollEndFriction: 0.35,
            panEndFriction: 0.35,
            isClickableElement: function(el) {
                return el.tagName === 'A';
            },
            getDoubleTapZoom: function(isMouseClick, item) {
                if(isMouseClick) {
                    return 1;
                } else {
                    return item.initialZoomLevel < 0.7 ? 1 : 1.33;
                }
            },
            maxSpreadZoom: 1.33,
            modal: true,

            // not fully implemented yet
            scaleMode: 'fit' // TODO
        };
        framework.extend(_options, options);


        /**
         * Private helper variables & functions
         */

        var _getEmptyPoint = function() {
                return {x:0,y:0};
            };

        var _isOpen,
            _isDestroying,
            _closedByScroll,
            _currentItemIndex,
            _containerStyle,
            _containerShiftIndex,
            _currPanDist = _getEmptyPoint(),
            _startPanOffset = _getEmptyPoint(),
            _panOffset = _getEmptyPoint(),
            _upMoveEvents, // drag move, drag end & drag cancel events array
            _downEvents, // drag start events array
            _globalEventHandlers,
            _viewportSize = {},
            _currZoomLevel,
            _startZoomLevel,
            _translatePrefix,
            _translateSufix,
            _updateSizeInterval,
            _itemsNeedUpdate,
            _currPositionIndex = 0,
            _offset = {},
            _slideSize = _getEmptyPoint(), // size of slide area, including spacing
            _itemHolders,
            _prevItemIndex,
            _indexDiff = 0, // difference of indexes since last content update
            _dragStartEvent,
            _dragMoveEvent,
            _dragEndEvent,
            _dragCancelEvent,
            _transformKey,
            _pointerEventEnabled,
            _isFixedPosition = true,
            _likelyTouchDevice,
            _modules = [],
            _requestAF,
            _cancelAF,
            _initalClassName,
            _initalWindowScrollY,
            _oldIE,
            _currentWindowScrollY,
            _features,
            _windowVisibleSize = {},
            _renderMaxResolution = false,
            _orientationChangeTimeout,


            // Registers PhotoSWipe module (History, Controller ...)
            _registerModule = function(name, module) {
                framework.extend(self, module.publicMethods);
                _modules.push(name);
            },

            _getLoopedId = function(index) {
                var numSlides = _getNumItems();
                if(index > numSlides - 1) {
                    return index - numSlides;
                } else  if(index < 0) {
                    return numSlides + index;
                }
                return index;
            },

            // Micro bind/trigger
            _listeners = {},
            _listen = function(name, fn) {
                if(!_listeners[name]) {
                    _listeners[name] = [];
                }
                return _listeners[name].push(fn);
            },
            _shout = function(name) {
                var listeners = _listeners[name];

                if(listeners) {
                    var args = Array.prototype.slice.call(arguments);
                    args.shift();

                    for(var i = 0; i < listeners.length; i++) {
                        listeners[i].apply(self, args);
                    }
                }
            },

            _getCurrentTime = function() {
                return new Date().getTime();
            },
            _applyBgOpacity = function(opacity) {
                _bgOpacity = opacity;
                // opacity=opacity * _options.bgOpacity;
                if(opacity){
                    $(self.bg).stop().animate({opacity:opacity}, 0);
                }else{
                    setTimeout(function() {
                        $(self.bg).stop().animate({opacity:opacity}, 100);
                    },300)
                }
                // self.bg.style.opacity = opacity;
            },

            _applyZoomTransform = function(styleObj,x,y,zoom,item) {
                if(!_renderMaxResolution || (item && item !== self.currItem) ) {
                    zoom = zoom / (item ? item.fitRatio : self.currItem.fitRatio);
                }

                styleObj[_transformKey] = _translatePrefix + x + 'px, ' + y + 'px' + _translateSufix + ' scale(' + zoom + ')';
            },
            _applyCurrentZoomPan = function( allowRenderResolution ) {
                if(_currZoomElementStyle) {

                    if(allowRenderResolution) {
                        if(_currZoomLevel > self.currItem.fitRatio) {
                            if(!_renderMaxResolution) {
                                _setImageSize(self.currItem, false, true);
                                _renderMaxResolution = true;
                            }
                        } else {
                            if(_renderMaxResolution) {
                                _setImageSize(self.currItem);
                                _renderMaxResolution = false;
                            }
                        }
                    }


                    _applyZoomTransform(_currZoomElementStyle, _panOffset.x, _panOffset.y, _currZoomLevel);
                }
            },
            _applyZoomPanToItem = function(item) {
                if(item.container) {

                    _applyZoomTransform(item.container.style,
                                        item.initialPosition.x,
                                        item.initialPosition.y,
                                        item.initialZoomLevel,
                                        item);
                }
            },
            _setTranslateX = function(x, elStyle) {
                elStyle[_transformKey] = _translatePrefix + x + 'px, 0px' + _translateSufix;
            },
            _moveMainScroll = function(x, dragging) {

                if(!_options.loop && dragging) {
                    var newSlideIndexOffset = _currentItemIndex + (_slideSize.x * _currPositionIndex - x) / _slideSize.x,
                        delta = Math.round(x - _mainScrollPos.x);

                    if( (newSlideIndexOffset < 0 && delta > 0) ||
                        (newSlideIndexOffset >= _getNumItems() - 1 && delta < 0) ) {
                        x = _mainScrollPos.x + delta * _options.mainScrollEndFriction;
                    }
                }

                _mainScrollPos.x = x;
                _setTranslateX(x, _containerStyle);
            },
            _calculatePanOffset = function(axis, zoomLevel) {
                var m = _midZoomPoint[axis] - _offset[axis];
                return _startPanOffset[axis] + _currPanDist[axis] + m - m * ( zoomLevel / _startZoomLevel );
            },

            _equalizePoints = function(p1, p2) {
                p1.x = p2.x;
                p1.y = p2.y;
                if(p2.id) {
                    p1.id = p2.id;
                }
            },
            _roundPoint = function(p) {
                p.x = Math.round(p.x);
                p.y = Math.round(p.y);
            },

            _mouseMoveTimeout = null,
            _onFirstMouseMove = function() {
                // Wait until mouse move event is fired at least twice during 100ms
                // We do this, because some mobile browsers trigger it on touchstart
                if(_mouseMoveTimeout ) {
                    framework.unbind(document, 'mousemove', _onFirstMouseMove);
                    framework.addClass(template, 'pswp--has_mouse');
                    _options.mouseUsed = true;
                    _shout('mouseUsed');
                }
                _mouseMoveTimeout = setTimeout(function() {
                    _mouseMoveTimeout = null;
                }, 100);
            },

            _bindEvents = function() {
                framework.bind(document, 'keydown', self);

                if(_features.transform) {
                    // don't bind click event in browsers that don't support transform (mostly IE8)
                    framework.bind(self.scrollWrap, 'click', self);
                }


                if(!_options.mouseUsed) {
                    framework.bind(document, 'mousemove', _onFirstMouseMove);
                }

                framework.bind(window, 'resize scroll orientationchange', self);

                _shout('bindEvents');
            },

            _unbindEvents = function() {
                framework.unbind(window, 'resize scroll orientationchange', self);
                framework.unbind(window, 'scroll', _globalEventHandlers.scroll);
                framework.unbind(document, 'keydown', self);
                framework.unbind(document, 'mousemove', _onFirstMouseMove);

                if(_features.transform) {
                    framework.unbind(self.scrollWrap, 'click', self);
                }

                if(_isDragging) {
                    framework.unbind(window, _upMoveEvents, self);
                }

                clearTimeout(_orientationChangeTimeout);

                _shout('unbindEvents');
            },

            _calculatePanBounds = function(zoomLevel, update) {
                var bounds = _calculateItemSize( self.currItem, _viewportSize, zoomLevel );
                if(update) {
                    _currPanBounds = bounds;
                }
                return bounds;
            },

            _getMinZoomLevel = function(item) {
                if(!item) {
                    item = self.currItem;
                }
                return item.initialZoomLevel;
            },
            _getMaxZoomLevel = function(item) {
                if(!item) {
                    item = self.currItem;
                }
                return item.w > 0 ? _options.maxSpreadZoom : 1;
            },

            // Return true if offset is out of the bounds
            _modifyDestPanOffset = function(axis, destPanBounds, destPanOffset, destZoomLevel) {
                if(destZoomLevel === self.currItem.initialZoomLevel) {
                    destPanOffset[axis] = self.currItem.initialPosition[axis];
                    return true;
                } else {
                    destPanOffset[axis] = _calculatePanOffset(axis, destZoomLevel);

                    if(destPanOffset[axis] > destPanBounds.min[axis]) {
                        destPanOffset[axis] = destPanBounds.min[axis];
                        return true;
                    } else if(destPanOffset[axis] < destPanBounds.max[axis] ) {
                        destPanOffset[axis] = destPanBounds.max[axis];
                        return true;
                    }
                }
                return false;
            },

            _setupTransforms = function() {

                if(_transformKey) {
                    // setup 3d transforms
                    var allow3dTransform = _features.perspective && !_likelyTouchDevice;
                    _translatePrefix = 'translate' + (allow3dTransform ? '3d(' : '(');
                    _translateSufix = _features.perspective ? ', 0px)' : ')';
                    return;
                }

                // Override zoom/pan/move functions in case old browser is used (most likely IE)
                // (so they use left/top/width/height, instead of CSS transform)

                _transformKey = 'left';
                framework.addClass(template, 'pswp--ie');

                _setTranslateX = function(x, elStyle) {
                    elStyle.left = x + 'px';
                };
                _applyZoomPanToItem = function(item) {

                    var zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
                        s = item.container.style,
                        w = zoomRatio * item.w,
                        h = zoomRatio * item.h;

                    s.width = w + 'px';
                    s.height = h + 'px';
                    s.left = item.initialPosition.x + 'px';
                    s.top = item.initialPosition.y + 'px';

                };
                _applyCurrentZoomPan = function() {
                    if(_currZoomElementStyle) {

                        var s = _currZoomElementStyle,
                            item = self.currItem,
                            zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
                            w = zoomRatio * item.w,
                            h = zoomRatio * item.h;

                        s.width = w + 'px';
                        s.height = h + 'px';


                        s.left = _panOffset.x + 'px';
                        s.top = _panOffset.y + 'px';
                    }

                };
            },

            _onKeyDown = function(e) {
                var keydownAction = '';
                if(_options.escKey && e.keyCode === 27) {
                    keydownAction = 'close';
                } else if(_options.arrowKeys) {
                    if(e.keyCode === 37) {
                        keydownAction = 'prev';
                    } else if(e.keyCode === 39) {
                        keydownAction = 'next';
                    }
                }

                if(keydownAction) {
                    // don't do anything if special key pressed to prevent from overriding default browser actions
                    // e.g. in Chrome on Mac cmd+arrow-left returns to previous page
                    if( !e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey ) {
                        if(e.preventDefault) {
                            e.preventDefault();
                        } else {
                            e.returnValue = false;
                        }
                        self[keydownAction]();
                    }
                }
            },

            _onGlobalClick = function(e) {
                if(!e) {
                    return;
                }

                // don't allow click event to pass through when triggering after drag or some other gesture
                if(_moved || _zoomStarted || _mainScrollAnimating || _verticalDragInitiated) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            },

            _updatePageScrollOffset = function() {
                self.setScrollOffset(0, framework.getScrollY());
            }
        // Micro animation engine
        var _animations = {},
            _numAnimations = 0,
            _stopAnimation = function(name) {
                if(_animations[name]) {
                    if(_animations[name].raf) {
                        _cancelAF( _animations[name].raf );
                    }
                    _numAnimations--;
                    delete _animations[name];
                }
            },
            _registerStartAnimation = function(name) {
                if(_animations[name]) {
                    _stopAnimation(name);
                }
                if(!_animations[name]) {
                    _numAnimations++;
                    _animations[name] = {};
                }
            },
            _stopAllAnimations = function() {
                for (var prop in _animations) {

                    if( _animations.hasOwnProperty( prop ) ) {
                        _stopAnimation(prop);
                    }

                }
            },
            _animateProp = function(name, b, endProp, d, easingFn, onUpdate, onComplete) {
                var startAnimTime = _getCurrentTime(), t;
                _registerStartAnimation(name);

                var animloop = function(){
                    if ( _animations[name] ) {

                        t = _getCurrentTime() - startAnimTime; // time diff
                        //b - beginning (start prop)
                        //d - anim duration

                        if ( t >= d ) {
                            _stopAnimation(name);
                            onUpdate(endProp);
                            if(onComplete) {
                                onComplete();
                            }
                            return;
                        }
                        onUpdate( (endProp - b) * easingFn(t/d) + b );

                        _animations[name].raf = _requestAF(animloop);
                    }
                };
                animloop();
            };



        var publicMethods = {

            // make a few local variables and functions public
            shout: _shout,
            listen: _listen,
            viewportSize: _viewportSize,
            options: _options,

            isMainScrollAnimating: function() {
                return _mainScrollAnimating;
            },
            getZoomLevel: function() {
                return _currZoomLevel;
            },
            getCurrentIndex: function() {
                return _currentItemIndex;
            },
            isDragging: function() {
                return _isDragging;
            },
            isZooming: function() {
                return _isZooming;
            },
            setScrollOffset: function(x,y) {
                _offset.x = x;
                _currentWindowScrollY = _offset.y = y;
                _shout('updateScrollOffset', _offset);
            },
            applyZoomPan: function(zoomLevel,panX,panY,allowRenderResolution) {
                _panOffset.x = panX;
                _panOffset.y = panY;
                _currZoomLevel = zoomLevel;
                _applyCurrentZoomPan( allowRenderResolution );
            },

            init: function() {

                if(_isOpen || _isDestroying) {
                    return;
                }

                var i;

                self.framework = framework; // basic functionality
                self.template = template; // root DOM element of PhotoSwipe
                self.bg = framework.getChildByClass(template, 'pswp__bg');

                _initalClassName = template.className;
                _isOpen = true;

                _features = framework.detectFeatures();
                _requestAF = _features.raf;
                _cancelAF = _features.caf;
                _transformKey = _features.transform;
                _oldIE = _features.oldIE;

                self.scrollWrap = framework.getChildByClass(template, 'pswp__scroll-wrap');
                self.container = framework.getChildByClass(self.scrollWrap, 'pswp__container');

                _containerStyle = self.container.style; // for fast access

                // Objects that hold slides (there are only 3 in DOM)
                self.itemHolders = _itemHolders = [
                    {el:self.container.children[0] , wrap:0, index: -1},
                    {el:self.container.children[1] , wrap:0, index: -1},
                    {el:self.container.children[2] , wrap:0, index: -1}
                ];

                // hide nearby item holders until initial zoom animation finishes (to avoid extra Paints)
                _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'none';

                _setupTransforms();

                // Setup global events
                _globalEventHandlers = {
                    resize: self.updateSize,

                    // Fixes: iOS 10.3 resize event
                    // does not update scrollWrap.clientWidth instantly after resize
                    // https://github.com/dimsemenov/PhotoSwipe/issues/1315
                    orientationchange: function() {
                        clearTimeout(_orientationChangeTimeout);
                        _orientationChangeTimeout = setTimeout(function() {
                            if(_viewportSize.x !== self.scrollWrap.clientWidth) {
                                self.updateSize();
                            }
                        }, 500);
                    },
                    scroll: _updatePageScrollOffset,
                    keydown: _onKeyDown,
                    click: _onGlobalClick
                };

                // disable show/hide effects on old browsers that don't support CSS animations or transforms,
                // old IOS, Android and Opera mobile. Blackberry seems to work fine, even older models.
                var oldPhone = _features.isOldIOSPhone || _features.isOldAndroid || _features.isMobileOpera;
                if(!_features.animationName || !_features.transform || oldPhone) {
                    _options.showAnimationDuration = _options.hideAnimationDuration = 0;
                }

                // init modules
                for(i = 0; i < _modules.length; i++) {
                    self['init' + _modules[i]]();
                }

                // init
                if(UiClass) {
                    var ui = self.ui = new UiClass(self, framework);
                    ui.init();
                }

                _shout('firstUpdate');
                _currentItemIndex = _currentItemIndex || _options.index || 0;
                // validate index
                if( isNaN(_currentItemIndex) || _currentItemIndex < 0 || _currentItemIndex >= _getNumItems() ) {
                    _currentItemIndex = 0;
                }
                self.currItem = _getItemAt( _currentItemIndex );


                if(_features.isOldIOSPhone || _features.isOldAndroid) {
                    _isFixedPosition = false;
                }

                template.setAttribute('aria-hidden', 'false');
                if(_options.modal) {
                    if(!_isFixedPosition) {
                        template.style.position = 'absolute';
                        template.style.top = framework.getScrollY() + 'px';
                    } else {
                        template.style.position = 'fixed';
                    }
                }

                if(_currentWindowScrollY === undefined) {
                    _shout('initialLayout');
                    _currentWindowScrollY = _initalWindowScrollY = framework.getScrollY();
                }

                // add classes to root element of PhotoSwipe
                var rootClasses = 'pswp--open ';
                if(_options.mainClass) {
                    rootClasses += _options.mainClass + ' ';
                }
                if(_options.showHideOpacity) {
                    rootClasses += 'pswp--animate_opacity ';
                }
                rootClasses += _likelyTouchDevice ? 'pswp--touch' : 'pswp--notouch';
                rootClasses += _features.animationName ? ' pswp--css_animation' : '';
                rootClasses += _features.svg ? ' pswp--svg' : '';
                framework.addClass(template, rootClasses);

                self.updateSize();

                // initial update
                _containerShiftIndex = -1;
                _indexDiff = null;
                for(i = 0; i < NUM_HOLDERS; i++) {
                    _setTranslateX( (i+_containerShiftIndex) * _slideSize.x, _itemHolders[i].el.style);
                }

                if(!_oldIE) {
                    framework.bind(self.scrollWrap, _downEvents, self); // no dragging for old IE
                }

                _listen('initialZoomInEnd', function() {
                    self.setContent(_itemHolders[0], _currentItemIndex-1);
                    self.setContent(_itemHolders[2], _currentItemIndex+1);

                    _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'block';

                    if(_options.focus) {
                        // focus causes layout,
                        // which causes lag during the animation,
                        // that's why we delay it untill the initial zoom transition ends
                        template.focus();
                    }


                    _bindEvents();
                });

                // set content for center slide (first time)
                self.setContent(_itemHolders[1], _currentItemIndex);

                self.updateCurrItem();

                _shout('afterInit');

                if(!_isFixedPosition) {

                    // On all versions of iOS lower than 8.0, we check size of viewport every second.
                    //
                    // This is done to detect when Safari top & bottom bars appear,
                    // as this action doesn't trigger any events (like resize).
                    //
                    // On iOS8 they fixed this.
                    //
                    // 10 Nov 2014: iOS 7 usage ~40%. iOS 8 usage 56%.

                    _updateSizeInterval = setInterval(function() {
                        if(!_numAnimations && !_isDragging && !_isZooming && (_currZoomLevel === self.currItem.initialZoomLevel)  ) {
                            self.updateSize();
                        }
                    }, 1000);
                }

                framework.addClass(template, 'pswp--visible');

                // 屏蔽滚动条滚动
                setTimeout(function(){
                    $('html').css({overflow:'hidden',height:'100%'});
                },300);
            },

            // Close the gallery, then destroy it
            close: function() {
                if(!_isOpen) {
                    return;
                }
                // 撤销滚动条屏蔽
                $('html').css({overflow:'',height:''});
                _isOpen = false;
                _isDestroying = true;
                _shout('close');
                _unbindEvents();

                _showOrHide(self.currItem, null, true, self.destroy);
            },

            // destroys the gallery (unbinds events, cleans up intervals and timeouts to avoid memory leaks)
            destroy: function() {
                _shout('destroy');

                if(_showOrHideTimeout) {
                    clearTimeout(_showOrHideTimeout);
                }

                template.setAttribute('aria-hidden', 'true');
                template.className = _initalClassName;

                if(_updateSizeInterval) {
                    clearInterval(_updateSizeInterval);
                }

                framework.unbind(self.scrollWrap, _downEvents, self);

                // we unbind scroll event at the end, as closing animation may depend on it
                framework.unbind(window, 'scroll', self);

                _stopDragUpdateLoop();

                _stopAllAnimations();

                _listeners = null;
            },

            /**
             * Pan image to position
             * @param {Number} x
             * @param {Number} y
             * @param {Boolean} force Will ignore bounds if set to true.
             */
            panTo: function(x,y,force) {
                if(!force) {
                    if(x > _currPanBounds.min.x) {
                        x = _currPanBounds.min.x;
                    } else if(x < _currPanBounds.max.x) {
                        x = _currPanBounds.max.x;
                    }

                    if(y > _currPanBounds.min.y) {
                        y = _currPanBounds.min.y;
                    } else if(y < _currPanBounds.max.y) {
                        y = _currPanBounds.max.y;
                    }
                }

                _panOffset.x = x;
                _panOffset.y = y;
                _applyCurrentZoomPan();
            },

            handleEvent: function (e) {
                e = e || window.event;
                if(_globalEventHandlers[e.type]) {
                    _globalEventHandlers[e.type](e);
                }
            },


            goTo: function(index) {
                var $container=$(self.container);
                $container.addClass('transition500');
                setTimeout(function(){
                    $container.removeClass('transition500');
                },500)
                index = _getLoopedId(index);

                var diff = index - _currentItemIndex;
                _indexDiff = diff;

                _currentItemIndex = index;
                self.currItem = _getItemAt( _currentItemIndex );
                _currPositionIndex -= diff;

                _moveMainScroll(_slideSize.x * _currPositionIndex);


                _stopAllAnimations();
                _mainScrollAnimating = false;

                self.updateCurrItem();
            },
            next: function() {
                self.goTo( _currentItemIndex + 1);
            },
            prev: function() {
                self.goTo( _currentItemIndex - 1);
            },

            // update current zoom/pan objects
            updateCurrZoomItem: function(emulateSetContent) {
                if(emulateSetContent) {
                    _shout('beforeChange', 0);
                }

                // itemHolder[1] is middle (current) item
                if(_itemHolders[1].el.children.length) {
                    var zoomElement = _itemHolders[1].el.children[0];
                    if( framework.hasClass(zoomElement, 'pswp__zoom-wrap') ) {
                        _currZoomElementStyle = zoomElement.style;
                    } else {
                        _currZoomElementStyle = null;
                    }
                } else {
                    _currZoomElementStyle = null;
                }

                _currPanBounds = self.currItem.bounds;
                _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;

                _panOffset.x = _currPanBounds.center.x;
                _panOffset.y = _currPanBounds.center.y;

                if(emulateSetContent) {
                    _shout('afterChange');
                }
            },


            invalidateCurrItems: function() {
                _itemsNeedUpdate = true;
                for(var i = 0; i < NUM_HOLDERS; i++) {
                    if( _itemHolders[i].item ) {
                        _itemHolders[i].item.needsUpdate = true;
                    }
                }
            },

            updateCurrItem: function(beforeAnimation) {

                if(_indexDiff === 0) {
                    return;
                }

                var diffAbs = Math.abs(_indexDiff),
                    tempHolder;

                if(beforeAnimation && diffAbs < 2) {
                    return;
                }


                self.currItem = _getItemAt( _currentItemIndex );
                _renderMaxResolution = false;

                _shout('beforeChange', _indexDiff);

                if(diffAbs >= NUM_HOLDERS) {
                    _containerShiftIndex += _indexDiff + (_indexDiff > 0 ? -NUM_HOLDERS : NUM_HOLDERS);
                    diffAbs = NUM_HOLDERS;
                }
                for(var i = 0; i < diffAbs; i++) {
                    if(_indexDiff > 0) {
                        tempHolder = _itemHolders.shift();
                        _itemHolders[NUM_HOLDERS-1] = tempHolder; // move first to last

                        _containerShiftIndex++;
                        _setTranslateX( (_containerShiftIndex+2) * _slideSize.x, tempHolder.el.style);
                        self.setContent(tempHolder, _currentItemIndex - diffAbs + i + 1 + 1);
                    } else {
                        tempHolder = _itemHolders.pop();
                        _itemHolders.unshift( tempHolder ); // move last to first

                        _containerShiftIndex--;
                        _setTranslateX( _containerShiftIndex * _slideSize.x, tempHolder.el.style);
                        self.setContent(tempHolder, _currentItemIndex + diffAbs - i - 1 - 1);
                    }

                }

                // reset zoom/pan on previous item
                if(_currZoomElementStyle && Math.abs(_indexDiff) === 1) {

                    var prevItem = _getItemAt(_prevItemIndex);
                    if(prevItem.initialZoomLevel !== _currZoomLevel) {
                        _calculateItemSize(prevItem , _viewportSize );
                        _setImageSize(prevItem);
                        _applyZoomPanToItem( prevItem );
                    }

                }

                // reset diff after update
                _indexDiff = 0;

                self.updateCurrZoomItem();

                _prevItemIndex = _currentItemIndex;

                _shout('afterChange');

            },



            updateSize: function(force) {

                if(!_isFixedPosition && _options.modal) {
                    var windowScrollY = framework.getScrollY();
                    if(_currentWindowScrollY !== windowScrollY) {
                        template.style.top = windowScrollY + 'px';
                        _currentWindowScrollY = windowScrollY;
                    }
                    if(!force && _windowVisibleSize.x === window.innerWidth && _windowVisibleSize.y === window.innerHeight) {
                        return;
                    }
                    _windowVisibleSize.x = window.innerWidth;
                    _windowVisibleSize.y = window.innerHeight;

                    //template.style.width = _windowVisibleSize.x + 'px';
                    template.style.height = _windowVisibleSize.y + 'px';
                }



                _viewportSize.x = self.scrollWrap.clientWidth;
                _viewportSize.y = self.scrollWrap.clientHeight;

                _updatePageScrollOffset();

                _slideSize.x = _viewportSize.x + Math.round(_viewportSize.x * _options.spacing);
                _slideSize.y = _viewportSize.y;

                _moveMainScroll(_slideSize.x * _currPositionIndex);

                _shout('beforeResize'); // even may be used for example to switch image sources


                // don't re-calculate size on inital size update
                if(_containerShiftIndex !== undefined) {

                    var holder,
                        item,
                        hIndex;

                    for(var i = 0; i < NUM_HOLDERS; i++) {
                        holder = _itemHolders[i];
                        _setTranslateX( (i+_containerShiftIndex) * _slideSize.x, holder.el.style);

                        hIndex = _currentItemIndex+i-1;

                        if(_options.loop && _getNumItems() > 2) {
                            hIndex = _getLoopedId(hIndex);
                        }

                        // update zoom level on items and refresh source (if needsUpdate)
                        item = _getItemAt( hIndex );

                        // re-render gallery item if `needsUpdate`,
                        // or doesn't have `bounds` (entirely new slide object)
                        if( item && (_itemsNeedUpdate || item.needsUpdate || !item.bounds) ) {

                            self.cleanSlide( item );

                            self.setContent( holder, hIndex );

                            // if "center" slide
                            if(i === 1) {
                                self.currItem = item;
                                self.updateCurrZoomItem(true);
                            }

                            item.needsUpdate = false;

                        } else if(holder.index === -1 && hIndex >= 0) {
                            // add content first time
                            self.setContent( holder, hIndex );
                        }
                        if(item && item.container) {
                            _calculateItemSize(item, _viewportSize);
                            _setImageSize(item);
                            _applyZoomPanToItem( item );
                        }

                    }
                    _itemsNeedUpdate = false;
                }

                _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;
                _currPanBounds = self.currItem.bounds;

                if(_currPanBounds) {
                    _panOffset.x = _currPanBounds.center.x;
                    _panOffset.y = _currPanBounds.center.y;
                    _applyCurrentZoomPan( true );
                }

                _shout('resize');
            },

            // Zoom current item to
            zoomTo: function(destZoomLevel, centerPoint, speed, easingFn, updateFn) {
                /*
                    if(destZoomLevel === 'fit') {
                        destZoomLevel = self.currItem.fitRatio;
                    } else if(destZoomLevel === 'fill') {
                        destZoomLevel = self.currItem.fillRatio;
                    }
                */

                if(centerPoint) {
                    _startZoomLevel = _currZoomLevel;
                    _midZoomPoint.x = Math.abs(centerPoint.x) - _panOffset.x ;
                    _midZoomPoint.y = Math.abs(centerPoint.y) - _panOffset.y ;
                    _equalizePoints(_startPanOffset, _panOffset);
                }

                var destPanBounds = _calculatePanBounds(destZoomLevel, false),
                    destPanOffset = {};

                _modifyDestPanOffset('x', destPanBounds, destPanOffset, destZoomLevel);
                _modifyDestPanOffset('y', destPanBounds, destPanOffset, destZoomLevel);

                var initialZoomLevel = _currZoomLevel;
                var initialPanOffset = {
                    x: _panOffset.x,
                    y: _panOffset.y
                };

                _roundPoint(destPanOffset);

                var onUpdate = function(now) {
                    if(now === 1) {
                        _currZoomLevel = destZoomLevel;
                        _panOffset.x = destPanOffset.x;
                        _panOffset.y = destPanOffset.y;
                    } else {
                        _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
                        _panOffset.x = (destPanOffset.x - initialPanOffset.x) * now + initialPanOffset.x;
                        _panOffset.y = (destPanOffset.y - initialPanOffset.y) * now + initialPanOffset.y;
                    }

                    if(updateFn) {
                        updateFn(now);
                    }

                    _applyCurrentZoomPan( now === 1 );
                };

                if(speed) {
                    _animateProp('customZoomTo', 0, 1, speed, easingFn || framework.easing.sine.inOut, onUpdate);
                } else {
                    onUpdate(1);
                }
            },
        };


        /*>>core*/

        /*>>gestures*/
        /**
         * Mouse/touch/pointer event handlers.
         *
         * separated from @core.js for readability
         */

        var MIN_SWIPE_DISTANCE = 30,
            DIRECTION_CHECK_OFFSET = 10; // amount of pixels to drag to determine direction of swipe

        var _gestureStartTime,
            _gestureCheckSpeedTime,

            // pool of objects that are used during dragging of zooming
            p = {}, // first point
            p2 = {}, // second point (for zoom gesture)
            delta = {},
            _currPoint = {},
            _startPoint = {},
            _currPointers = [],
            _startMainScrollPos = {},
            _releaseAnimData,
            _posPoints = [], // array of points during dragging, used to determine type of gesture
            _tempPoint = {},

            _isZoomingIn,
            _verticalDragInitiated,
            _oldAndroidTouchEndTimeout,
            _currZoomedItemIndex = 0,
            _centerPoint = _getEmptyPoint(),
            _lastReleaseTime = 0,
            _isDragging, // at least one pointer is down
            _isMultitouch, // at least two _pointers are down
            _zoomStarted, // zoom level changed during zoom gesture
            _moved,
            _dragAnimFrame,
            _mainScrollShifted,
            _currentPoints, // array of current touch points
            _isZooming,
            _currPointsDistance,
            _startPointsDistance,
            _currPanBounds,
            _mainScrollPos = _getEmptyPoint(),
            _currZoomElementStyle,
            _mainScrollAnimating, // true, if animation after swipe gesture is running
            _midZoomPoint = _getEmptyPoint(),
            _currCenterPoint = _getEmptyPoint(),
            _direction,
            _isFirstMove,
            _opacityChanged,
            _bgOpacity,
            _wasOverInitialZoom,

            _isEqualPoints = function(p1, p2) {
                return p1.x === p2.x && p1.y === p2.y;
            },
            _isNearbyPoints = function(touch0, touch1) {
                return Math.abs(touch0.x - touch1.x) < DOUBLE_TAP_RADIUS && Math.abs(touch0.y - touch1.y) < DOUBLE_TAP_RADIUS;
            },
            _calculatePointsDistance = function(p1, p2) {
                _tempPoint.x = Math.abs( p1.x - p2.x );
                _tempPoint.y = Math.abs( p1.y - p2.y );
                return Math.sqrt(_tempPoint.x * _tempPoint.x + _tempPoint.y * _tempPoint.y);
            },
            _stopDragUpdateLoop = function() {
                if(_dragAnimFrame) {
                    _cancelAF(_dragAnimFrame);
                    _dragAnimFrame = null;
                }
            },
            _dragUpdateLoop = function() {
                if(_isDragging) {
                    _dragAnimFrame = _requestAF(_dragUpdateLoop);
                    _renderMovement();
                }
            },
            _canPan = function() {
                return !(_options.scaleMode === 'fit' && _currZoomLevel ===  self.currItem.initialZoomLevel);
            },

            // find the closest parent DOM element
            _closestElement = function(el, fn) {
                if(!el || el === document) {
                    return false;
                }

                // don't search elements above pswp__scroll-wrap
                if(el.getAttribute('class') && el.getAttribute('class').indexOf('pswp__scroll-wrap') > -1 ) {
                    return false;
                }

                if( fn(el) ) {
                    return el;
                }

                return _closestElement(el.parentNode, fn);
            },

            _preventObj = {},
            _preventDefaultEventBehaviour = function(e, isDown) {
                _preventObj.prevent = !_closestElement(e.target, _options.isClickableElement);

                _shout('preventDragEvent', e, isDown, _preventObj);
                return _preventObj.prevent;

            },
            _convertTouchToPoint = function(touch, p) {
                p.x = touch.pageX;
                p.y = touch.pageY;
                p.id = touch.identifier;
                return p;
            },
            _findCenterOfPoints = function(p1, p2, pCenter) {
                pCenter.x = (p1.x + p2.x) * 0.5;
                pCenter.y = (p1.y + p2.y) * 0.5;
            },
            _pushPosPoint = function(time, x, y) {
                if(time - _gestureCheckSpeedTime > 50) {
                    var o = _posPoints.length > 2 ? _posPoints.shift() : {};
                    o.x = x;
                    o.y = y;
                    _posPoints.push(o);
                    _gestureCheckSpeedTime = time;
                }
            },

            _calculateVerticalDragOpacityRatio = function() {
                var yOffset = _panOffset.y - self.currItem.initialPosition.y; // difference between initial and current position
                return 1 -  Math.abs( yOffset / (_viewportSize.y / 2)  );
            },


            // points pool, reused during touch events
            _ePoint1 = {},
            _ePoint2 = {},
            _tempPointsArr = [],
            _tempCounter,
            _getTouchPoints = function(e) {
                // clean up previous points, without recreating array
                while(_tempPointsArr.length > 0) {
                    _tempPointsArr.pop();
                }

                if(!_pointerEventEnabled) {
                    if(e.type.indexOf('touch') > -1) {

                        if(e.touches && e.touches.length > 0) {
                            _tempPointsArr[0] = _convertTouchToPoint(e.touches[0], _ePoint1);
                            if(e.touches.length > 1) {
                                _tempPointsArr[1] = _convertTouchToPoint(e.touches[1], _ePoint2);
                            }
                        }

                    } else {
                        _ePoint1.x = e.pageX;
                        _ePoint1.y = e.pageY;
                        _ePoint1.id = '';
                        _tempPointsArr[0] = _ePoint1;//_ePoint1;
                    }
                } else {
                    _tempCounter = 0;
                    // we can use forEach, as pointer events are supported only in modern browsers
                    _currPointers.forEach(function(p) {
                        if(_tempCounter === 0) {
                            _tempPointsArr[0] = p;
                        } else if(_tempCounter === 1) {
                            _tempPointsArr[1] = p;
                        }
                        _tempCounter++;

                    });
                }
                return _tempPointsArr;
            },

            _panOrMoveMainScroll = function(axis, delta) {

                var panFriction,
                    overDiff = 0,
                    newOffset = _panOffset[axis] + delta[axis],
                    startOverDiff,
                    dir = delta[axis] > 0,
                    newMainScrollPosition = _mainScrollPos.x + delta.x,
                    mainScrollDiff = _mainScrollPos.x - _startMainScrollPos.x,
                    newPanPos,
                    newMainScrollPos;

                // calculate fdistance over the bounds and friction
                if(newOffset > _currPanBounds.min[axis] || newOffset < _currPanBounds.max[axis]) {
                    panFriction = _options.panEndFriction;
                    // Linear increasing of friction, so at 1/4 of viewport it's at max value.
                    // Looks not as nice as was expected. Left for history.
                    // panFriction = (1 - (_panOffset[axis] + delta[axis] + panBounds.min[axis]) / (_viewportSize[axis] / 4) );
                } else {
                    panFriction = 1;
                }

                newOffset = _panOffset[axis] + delta[axis] * panFriction;

                // move main scroll or start panning
                if(_options.allowPanToNext || _currZoomLevel === self.currItem.initialZoomLevel) {


                    if(!_currZoomElementStyle) {

                        newMainScrollPos = newMainScrollPosition;

                    } else if(_direction === 'h' && axis === 'x' && !_zoomStarted ) {

                        if(dir) {
                            if(newOffset > _currPanBounds.min[axis]) {
                                panFriction = _options.panEndFriction;
                                overDiff = _currPanBounds.min[axis] - newOffset;
                                startOverDiff = _currPanBounds.min[axis] - _startPanOffset[axis];
                            }

                            // drag right
                            if( (startOverDiff <= 0 || mainScrollDiff < 0) && _getNumItems() > 1 ) {
                                newMainScrollPos = newMainScrollPosition;
                                if(mainScrollDiff < 0 && newMainScrollPosition > _startMainScrollPos.x) {
                                    newMainScrollPos = _startMainScrollPos.x;
                                }
                            } else {
                                if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                                    newPanPos = newOffset;
                                }

                            }

                        } else {

                            if(newOffset < _currPanBounds.max[axis] ) {
                                panFriction =_options.panEndFriction;
                                overDiff = newOffset - _currPanBounds.max[axis];
                                startOverDiff = _startPanOffset[axis] - _currPanBounds.max[axis];
                            }

                            if( (startOverDiff <= 0 || mainScrollDiff > 0) && _getNumItems() > 1 ) {
                                newMainScrollPos = newMainScrollPosition;

                                if(mainScrollDiff > 0 && newMainScrollPosition < _startMainScrollPos.x) {
                                    newMainScrollPos = _startMainScrollPos.x;
                                }

                            } else {
                                if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                                    newPanPos = newOffset;
                                }
                            }

                        }


                        //
                    }

                    if(axis === 'x') {

                        if(newMainScrollPos !== undefined) {
                            _moveMainScroll(newMainScrollPos, true);
                            if(newMainScrollPos === _startMainScrollPos.x) {
                                _mainScrollShifted = false;
                            } else {
                                _mainScrollShifted = true;
                            }
                        }

                        if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                            if(newPanPos !== undefined) {
                                _panOffset.x = newPanPos;
                            } else if(!_mainScrollShifted) {
                                _panOffset.x += delta.x * panFriction;
                            }
                        }

                        return newMainScrollPos !== undefined;
                    }

                }

                if(!_mainScrollAnimating) {

                    if(!_mainScrollShifted) {
                        if(_currZoomLevel > self.currItem.fitRatio) {
                            _panOffset[axis] += delta[axis] * panFriction;

                        }
                    }


                }

            },

            // Pointerdown/touchstart/mousedown handler
            _onDragStart = function(e) {

                // Allow dragging only via left mouse button.
                // As this handler is not added in IE8 - we ignore e.which
                //
                // http://www.quirksmode.org/js/events_properties.html
                // https://developer.mozilla.org/en-US/docs/Web/API/event.button
                if(e.type === 'mousedown' && e.button > 0  ) {
                    return;
                }

                if(_initialZoomRunning) {
                    e.preventDefault();
                    return;
                }

                if(_oldAndroidTouchEndTimeout && e.type === 'mousedown') {
                    return;
                }

                if(_preventDefaultEventBehaviour(e, true)) {
                    // if(device_type!='d'){
                    //     var obj = e.srcElement ? e.srcElement : e.target;
                    //     if($(obj).parents('.pswp__button--rotate-left').length) {
                    //         _imgRotate('','','.pswp__item','.pswp__img:visible',1);
                    //     }
                    //     if($(obj).parents('.pswp__button--rotate-right').length) {
                    //         _imgRotate('','','.pswp__item','.pswp__img:visible',2);
                    //     }
                    // }
                    e.preventDefault();
                }



                _shout('pointerDown');

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
                    if(pointerIndex < 0) {
                        pointerIndex = _currPointers.length;
                    }
                    _currPointers[pointerIndex] = {x:e.pageX, y:e.pageY, id: e.pointerId};
                }



                var startPointsList = _getTouchPoints(e),
                    numPoints = startPointsList.length;

                _currentPoints = null;

                _stopAllAnimations();

                // init drag
                if(!_isDragging || numPoints === 1) {



                    _isDragging = _isFirstMove = true;
                    framework.bind(window, _upMoveEvents, self);

                    _isZoomingIn =
                        _wasOverInitialZoom =
                        _opacityChanged =
                        _verticalDragInitiated =
                        _mainScrollShifted =
                        _moved =
                        _isMultitouch =
                        _zoomStarted = false;

                    _direction = null;

                    _shout('firstTouchStart', startPointsList);

                    _equalizePoints(_startPanOffset, _panOffset);

                    _currPanDist.x = _currPanDist.y = 0;
                    _equalizePoints(_currPoint, startPointsList[0]);
                    _equalizePoints(_startPoint, _currPoint);

                    //_equalizePoints(_startMainScrollPos, _mainScrollPos);
                    _startMainScrollPos.x = _slideSize.x * _currPositionIndex;

                    _posPoints = [{
                        x: _currPoint.x,
                        y: _currPoint.y
                    }];

                    _gestureCheckSpeedTime = _gestureStartTime = _getCurrentTime();

                    //_mainScrollAnimationEnd(true);
                    _calculatePanBounds( _currZoomLevel, true );

                    // Start rendering
                    _stopDragUpdateLoop();
                    _dragUpdateLoop();

                }

                // init zoom
                if(!_isZooming && numPoints > 1 && !_mainScrollAnimating && !_mainScrollShifted) {
                    _startZoomLevel = _currZoomLevel;
                    _zoomStarted = false; // true if zoom changed at least once

                    _isZooming = _isMultitouch = true;
                    _currPanDist.y = _currPanDist.x = 0;

                    _equalizePoints(_startPanOffset, _panOffset);

                    _equalizePoints(p, startPointsList[0]);
                    _equalizePoints(p2, startPointsList[1]);

                    _findCenterOfPoints(p, p2, _currCenterPoint);

                    _midZoomPoint.x = Math.abs(_currCenterPoint.x) - _panOffset.x;
                    _midZoomPoint.y = Math.abs(_currCenterPoint.y) - _panOffset.y;
                    _currPointsDistance = _startPointsDistance = _calculatePointsDistance(p, p2);
                }


            },

            // Pointermove/touchmove/mousemove handler
            _onDragMove = function(e) {

                e.preventDefault();

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
                    if(pointerIndex > -1) {
                        var p = _currPointers[pointerIndex];
                        p.x = e.pageX;
                        p.y = e.pageY;
                    }
                }

                if(_isDragging) {
                    var touchesList = _getTouchPoints(e);
                    if(!_direction && !_moved && !_isZooming) {

                        if(_mainScrollPos.x !== _slideSize.x * _currPositionIndex) {
                            // if main scroll position is shifted – direction is always horizontal
                            _direction = 'h';
                        } else {
                            var diff = Math.abs(touchesList[0].x - _currPoint.x) - Math.abs(touchesList[0].y - _currPoint.y);
                            // check the direction of movement
                            if(Math.abs(diff) >= DIRECTION_CHECK_OFFSET) {
                                _direction = diff > 0 ? 'h' : 'v';
                                _currentPoints = touchesList;
                            }
                        }

                    } else {
                        _currentPoints = touchesList;
                    }
                }
            },
            //
            _renderMovement =  function() {

                if(!_currentPoints) {
                    return;
                }

                var numPoints = _currentPoints.length;

                if(numPoints === 0) {
                    return;
                }

                _equalizePoints(p, _currentPoints[0]);

                delta.x = p.x - _currPoint.x;
                delta.y = p.y - _currPoint.y;

                if(_isZooming && numPoints > 1) {
                    // Handle behaviour for more than 1 point

                    _currPoint.x = p.x;
                    _currPoint.y = p.y;

                    // check if one of two points changed
                    if( !delta.x && !delta.y && _isEqualPoints(_currentPoints[1], p2) ) {
                        return;
                    }

                    _equalizePoints(p2, _currentPoints[1]);


                    if(!_zoomStarted) {
                        _zoomStarted = true;
                        _shout('zoomGestureStarted');
                    }

                    // Distance between two points
                    var pointsDistance = _calculatePointsDistance(p,p2);

                    var zoomLevel = _calculateZoomLevel(pointsDistance);

                    // slightly over the of initial zoom level
                    if(zoomLevel > self.currItem.initialZoomLevel + self.currItem.initialZoomLevel / 15) {
                        _wasOverInitialZoom = true;
                    }

                    // Apply the friction if zoom level is out of the bounds
                    var zoomFriction = 1,
                        minZoomLevel = _getMinZoomLevel(),
                        maxZoomLevel = _getMaxZoomLevel();

                    if ( zoomLevel < minZoomLevel ) {

                        if(_options.pinchToClose && !_wasOverInitialZoom && _startZoomLevel <= self.currItem.initialZoomLevel) {
                            // fade out background if zooming out
                            var minusDiff = minZoomLevel - zoomLevel;
                            var percent = 1 - minusDiff / (minZoomLevel / 1.2);

                            _applyBgOpacity(percent);
                            _shout('onPinchClose', percent);
                            _opacityChanged = true;
                        } else {
                            zoomFriction = (minZoomLevel - zoomLevel) / minZoomLevel;
                            if(zoomFriction > 1) {
                                zoomFriction = 1;
                            }
                            zoomLevel = minZoomLevel - zoomFriction * (minZoomLevel / 3);
                        }

                    } else if ( zoomLevel > maxZoomLevel ) {
                        // 1.5 - extra zoom level above the max. E.g. if max is x6, real max 6 + 1.5 = 7.5
                        zoomFriction = (zoomLevel - maxZoomLevel) / ( minZoomLevel * 6 );
                        if(zoomFriction > 1) {
                            zoomFriction = 1;
                        }
                        zoomLevel = maxZoomLevel + zoomFriction * minZoomLevel;
                    }

                    if(zoomFriction < 0) {
                        zoomFriction = 0;
                    }

                    // distance between touch points after friction is applied
                    _currPointsDistance = pointsDistance;

                    // _centerPoint - The point in the middle of two pointers
                    _findCenterOfPoints(p, p2, _centerPoint);

                    // paning with two pointers pressed
                    _currPanDist.x += _centerPoint.x - _currCenterPoint.x;
                    _currPanDist.y += _centerPoint.y - _currCenterPoint.y;
                    _equalizePoints(_currCenterPoint, _centerPoint);

                    _panOffset.x = _calculatePanOffset('x', zoomLevel);
                    _panOffset.y = _calculatePanOffset('y', zoomLevel);

                    _isZoomingIn = zoomLevel > _currZoomLevel;
                    _currZoomLevel = zoomLevel;
                    _applyCurrentZoomPan();

                } else {

                    // handle behaviour for one point (dragging or panning)

                    if(!_direction) {
                        return;
                    }

                    if(_isFirstMove) {
                        _isFirstMove = false;

                        // subtract drag distance that was used during the detection direction

                        if( Math.abs(delta.x) >= DIRECTION_CHECK_OFFSET) {
                            delta.x -= _currentPoints[0].x - _startPoint.x;
                        }

                        if( Math.abs(delta.y) >= DIRECTION_CHECK_OFFSET) {
                            delta.y -= _currentPoints[0].y - _startPoint.y;
                        }
                    }

                    _currPoint.x = p.x;
                    _currPoint.y = p.y;

                    // do nothing if pointers position hasn't changed
                    if(delta.x === 0 && delta.y === 0) {
                        return;
                    }

                    if(_direction === 'v' && _options.closeOnVerticalDrag) {
                        if(!_canPan()) {
                            _currPanDist.y += delta.y;
                            _panOffset.y += delta.y;

                            var opacityRatio = _calculateVerticalDragOpacityRatio();

                            _verticalDragInitiated = true;
                            _shout('onVerticalDrag', opacityRatio);

                            _applyBgOpacity(opacityRatio);
                            _applyCurrentZoomPan();
                            return ;
                        }
                    }

                    _pushPosPoint(_getCurrentTime(), p.x, p.y);

                    _moved = true;
                    _currPanBounds = self.currItem.bounds;

                    var mainScrollChanged = _panOrMoveMainScroll('x', delta);
                    if(!mainScrollChanged) {
                        _panOrMoveMainScroll('y', delta);

                        _roundPoint(_panOffset);
                        _applyCurrentZoomPan();
                    }

                }

            },

            // Pointerup/pointercancel/touchend/touchcancel/mouseup event handler
            _onDragRelease = function(e) {

                if(_features.isOldAndroid ) {

                    if(_oldAndroidTouchEndTimeout && e.type === 'mouseup') {
                        return;
                    }

                    // on Android (v4.1, 4.2, 4.3 & possibly older)
                    // ghost mousedown/up event isn't preventable via e.preventDefault,
                    // which causes fake mousedown event
                    // so we block mousedown/up for 600ms
                    if( e.type.indexOf('touch') > -1 ) {
                        clearTimeout(_oldAndroidTouchEndTimeout);
                        _oldAndroidTouchEndTimeout = setTimeout(function() {
                            _oldAndroidTouchEndTimeout = 0;
                        }, 600);
                    }

                }

                _shout('pointerUp');

                if(_preventDefaultEventBehaviour(e, false)) {
                    e.preventDefault();
                }

                var releasePoint;

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');

                    if(pointerIndex > -1) {
                        releasePoint = _currPointers.splice(pointerIndex, 1)[0];

                        if(navigator.pointerEnabled) {
                            releasePoint.type = e.pointerType || 'mouse';
                        } else {
                            var MSPOINTER_TYPES = {
                                4: 'mouse', // event.MSPOINTER_TYPE_MOUSE
                                2: 'touch', // event.MSPOINTER_TYPE_TOUCH
                                3: 'pen' // event.MSPOINTER_TYPE_PEN
                            };
                            releasePoint.type = MSPOINTER_TYPES[e.pointerType];

                            if(!releasePoint.type) {
                                releasePoint.type = e.pointerType || 'mouse';
                            }
                        }

                    }
                }

                var touchList = _getTouchPoints(e),
                    gestureType,
                    numPoints = touchList.length;

                if(e.type === 'mouseup') {
                    numPoints = 0;
                }

                // Do nothing if there were 3 touch points or more
                if(numPoints === 2) {
                    _currentPoints = null;
                    return true;
                }

                // if second pointer released
                if(numPoints === 1) {
                    _equalizePoints(_startPoint, touchList[0]);
                }


                // pointer hasn't moved, send "tap release" point
                if(numPoints === 0 && !_direction && !_mainScrollAnimating) {
                    if(!releasePoint) {
                        if(e.type === 'mouseup') {
                            releasePoint = {x: e.pageX, y: e.pageY, type:'mouse'};
                        } else if(e.changedTouches && e.changedTouches[0]) {
                            releasePoint = {x: e.changedTouches[0].pageX, y: e.changedTouches[0].pageY, type:'touch'};
                        }
                    }

                    _shout('touchRelease', e, releasePoint);
                }

                // Difference in time between releasing of two last touch points (zoom gesture)
                var releaseTimeDiff = -1;

                // Gesture completed, no pointers left
                if(numPoints === 0) {
                    _isDragging = false;
                    framework.unbind(window, _upMoveEvents, self);

                    _stopDragUpdateLoop();

                    if(_isZooming) {
                        // Two points released at the same time
                        releaseTimeDiff = 0;
                    } else if(_lastReleaseTime !== -1) {
                        releaseTimeDiff = _getCurrentTime() - _lastReleaseTime;
                    }
                }
                _lastReleaseTime = numPoints === 1 ? _getCurrentTime() : -1;

                if(releaseTimeDiff !== -1 && releaseTimeDiff < 150) {
                    gestureType = 'zoom';
                } else {
                    gestureType = 'swipe';
                }

                if(_isZooming && numPoints < 2) {
                    _isZooming = false;

                    // Only second point released
                    if(numPoints === 1) {
                        gestureType = 'zoomPointerUp';
                    }
                    _shout('zoomGestureEnded');
                }

                _currentPoints = null;
                if(!_moved && !_zoomStarted && !_mainScrollAnimating && !_verticalDragInitiated) {
                    // nothing to animate
                    return;
                }

                _stopAllAnimations();


                if(!_releaseAnimData) {
                    _releaseAnimData = _initDragReleaseAnimationData();
                }

                _releaseAnimData.calculateSwipeSpeed('x');


                if(_verticalDragInitiated) {

                    var opacityRatio = _calculateVerticalDragOpacityRatio();

                    if(opacityRatio < _options.verticalDragRange) {
                        self.close();
                    } else {
                        var initalPanY = _panOffset.y,
                            initialBgOpacity = _bgOpacity;

                        _animateProp('verticalDrag', 0, 1, 300, framework.easing.cubic.out, function(now) {

                            _panOffset.y = (self.currItem.initialPosition.y - initalPanY) * now + initalPanY;

                            _applyBgOpacity(  (1 - initialBgOpacity) * now + initialBgOpacity );
                            _applyCurrentZoomPan();
                        });

                        _shout('onVerticalDrag', 1);
                    }

                    return;
                }


                // main scroll
                if(  (_mainScrollShifted || _mainScrollAnimating) && numPoints === 0) {
                    var itemChanged = _finishSwipeMainScrollGesture(gestureType, _releaseAnimData);
                    if(itemChanged) {
                        return;
                    }
                    gestureType = 'zoomPointerUp';
                }

                // prevent zoom/pan animation when main scroll animation runs
                if(_mainScrollAnimating) {
                    return;
                }

                // Complete simple zoom gesture (reset zoom level if it's out of the bounds)
                if(gestureType !== 'swipe') {
                    _completeZoomGesture();
                    return;
                }

                // Complete pan gesture if main scroll is not shifted, and it's possible to pan current image
                if(!_mainScrollShifted && _currZoomLevel > self.currItem.fitRatio) {
                    _completePanGesture(_releaseAnimData);
                }
            },


            // Returns object with data about gesture
            // It's created only once and then reused
            _initDragReleaseAnimationData  = function() {
                // temp local vars
                var lastFlickDuration,
                    tempReleasePos;

                // s = this
                var s = {
                    lastFlickOffset: {},
                    lastFlickDist: {},
                    lastFlickSpeed: {},
                    slowDownRatio:  {},
                    slowDownRatioReverse:  {},
                    speedDecelerationRatio:  {},
                    speedDecelerationRatioAbs:  {},
                    distanceOffset:  {},
                    backAnimDestination: {},
                    backAnimStarted: {},
                    calculateSwipeSpeed: function(axis) {


                        if( _posPoints.length > 1) {
                            lastFlickDuration = _getCurrentTime() - _gestureCheckSpeedTime + 50;
                            tempReleasePos = _posPoints[_posPoints.length-2][axis];
                        } else {
                            lastFlickDuration = _getCurrentTime() - _gestureStartTime; // total gesture duration
                            tempReleasePos = _startPoint[axis];
                        }
                        s.lastFlickOffset[axis] = _currPoint[axis] - tempReleasePos;
                        s.lastFlickDist[axis] = Math.abs(s.lastFlickOffset[axis]);
                        if(s.lastFlickDist[axis] > 20) {
                            s.lastFlickSpeed[axis] = s.lastFlickOffset[axis] / lastFlickDuration;
                        } else {
                            s.lastFlickSpeed[axis] = 0;
                        }
                        if( Math.abs(s.lastFlickSpeed[axis]) < 0.1 ) {
                            s.lastFlickSpeed[axis] = 0;
                        }

                        s.slowDownRatio[axis] = 0.95;
                        s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
                        s.speedDecelerationRatio[axis] = 1;
                    },

                    calculateOverBoundsAnimOffset: function(axis, speed) {
                        if(!s.backAnimStarted[axis]) {

                            if(_panOffset[axis] > _currPanBounds.min[axis]) {
                                s.backAnimDestination[axis] = _currPanBounds.min[axis];

                            } else if(_panOffset[axis] < _currPanBounds.max[axis]) {
                                s.backAnimDestination[axis] = _currPanBounds.max[axis];
                            }

                            if(s.backAnimDestination[axis] !== undefined) {
                                s.slowDownRatio[axis] = 0.7;
                                s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
                                if(s.speedDecelerationRatioAbs[axis] < 0.05) {

                                    s.lastFlickSpeed[axis] = 0;
                                    s.backAnimStarted[axis] = true;

                                    _animateProp('bounceZoomPan'+axis,_panOffset[axis],
                                        s.backAnimDestination[axis],
                                        speed || 300,
                                        framework.easing.sine.out,
                                        function(pos) {
                                            _panOffset[axis] = pos;
                                            _applyCurrentZoomPan();
                                        }
                                    );

                                }
                            }
                        }
                    },

                    // Reduces the speed by slowDownRatio (per 10ms)
                    calculateAnimOffset: function(axis) {
                        if(!s.backAnimStarted[axis]) {
                            s.speedDecelerationRatio[axis] = s.speedDecelerationRatio[axis] * (s.slowDownRatio[axis] +
                                                        s.slowDownRatioReverse[axis] -
                                                        s.slowDownRatioReverse[axis] * s.timeDiff / 10);

                            s.speedDecelerationRatioAbs[axis] = Math.abs(s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis]);
                            s.distanceOffset[axis] = s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis] * s.timeDiff;
                            _panOffset[axis] += s.distanceOffset[axis];

                        }
                    },

                    panAnimLoop: function() {
                        if ( _animations.zoomPan ) {
                            _animations.zoomPan.raf = _requestAF(s.panAnimLoop);

                            s.now = _getCurrentTime();
                            s.timeDiff = s.now - s.lastNow;
                            s.lastNow = s.now;

                            s.calculateAnimOffset('x');
                            s.calculateAnimOffset('y');

                            _applyCurrentZoomPan();

                            s.calculateOverBoundsAnimOffset('x');
                            s.calculateOverBoundsAnimOffset('y');


                            if (s.speedDecelerationRatioAbs.x < 0.05 && s.speedDecelerationRatioAbs.y < 0.05) {

                                // round pan position
                                _panOffset.x = Math.round(_panOffset.x);
                                _panOffset.y = Math.round(_panOffset.y);
                                _applyCurrentZoomPan();

                                _stopAnimation('zoomPan');
                                return;
                            }
                        }

                    }
                };
                return s;
            },

            _completePanGesture = function(animData) {
                // calculate swipe speed for Y axis (paanning)
                animData.calculateSwipeSpeed('y');

                _currPanBounds = self.currItem.bounds;

                animData.backAnimDestination = {};
                animData.backAnimStarted = {};

                // Avoid acceleration animation if speed is too low
                if(Math.abs(animData.lastFlickSpeed.x) <= 0.05 && Math.abs(animData.lastFlickSpeed.y) <= 0.05 ) {
                    animData.speedDecelerationRatioAbs.x = animData.speedDecelerationRatioAbs.y = 0;

                    // Run pan drag release animation. E.g. if you drag image and release finger without momentum.
                    animData.calculateOverBoundsAnimOffset('x');
                    animData.calculateOverBoundsAnimOffset('y');
                    return true;
                }

                // Animation loop that controls the acceleration after pan gesture ends
                _registerStartAnimation('zoomPan');
                animData.lastNow = _getCurrentTime();
                animData.panAnimLoop();
            },


            _finishSwipeMainScrollGesture = function(gestureType, _releaseAnimData) {
                var itemChanged;
                if(!_mainScrollAnimating) {
                    _currZoomedItemIndex = _currentItemIndex;
                }



                var itemsDiff;

                if(gestureType === 'swipe') {
                    var totalShiftDist = _currPoint.x - _startPoint.x,
                        isFastLastFlick = _releaseAnimData.lastFlickDist.x < 10;

                    // if container is shifted for more than MIN_SWIPE_DISTANCE,
                    // and last flick gesture was in right direction
                    if(totalShiftDist > MIN_SWIPE_DISTANCE &&
                        (isFastLastFlick || _releaseAnimData.lastFlickOffset.x > 20) ) {
                        // go to prev item
                        itemsDiff = -1;
                    } else if(totalShiftDist < -MIN_SWIPE_DISTANCE &&
                        (isFastLastFlick || _releaseAnimData.lastFlickOffset.x < -20) ) {
                        // go to next item
                        itemsDiff = 1;
                    }
                }

                var nextCircle;

                if(itemsDiff) {

                    _currentItemIndex += itemsDiff;

                    if(_currentItemIndex < 0) {
                        _currentItemIndex = _options.loop ? _getNumItems()-1 : 0;
                        nextCircle = true;
                    } else if(_currentItemIndex >= _getNumItems()) {
                        _currentItemIndex = _options.loop ? 0 : _getNumItems()-1;
                        nextCircle = true;
                    }

                    if(!nextCircle || _options.loop) {
                        _indexDiff += itemsDiff;
                        _currPositionIndex -= itemsDiff;
                        itemChanged = true;
                    }



                }

                var animateToX = _slideSize.x * _currPositionIndex;
                var animateToDist = Math.abs( animateToX - _mainScrollPos.x );
                var finishAnimDuration;


                if(!itemChanged && animateToX > _mainScrollPos.x !== _releaseAnimData.lastFlickSpeed.x > 0) {
                    // "return to current" duration, e.g. when dragging from slide 0 to -1
                    finishAnimDuration = 333;
                } else {
                    finishAnimDuration = Math.abs(_releaseAnimData.lastFlickSpeed.x) > 0 ?
                                            animateToDist / Math.abs(_releaseAnimData.lastFlickSpeed.x) :
                                            333;

                    finishAnimDuration = Math.min(finishAnimDuration, 400);
                    finishAnimDuration = Math.max(finishAnimDuration, 250);
                }

                if(_currZoomedItemIndex === _currentItemIndex) {
                    itemChanged = false;
                }

                _mainScrollAnimating = true;

                _shout('mainScrollAnimStart');

                _animateProp('mainScroll', _mainScrollPos.x, animateToX, finishAnimDuration, framework.easing.cubic.out,
                    _moveMainScroll,
                    function() {
                        _stopAllAnimations();
                        _mainScrollAnimating = false;
                        _currZoomedItemIndex = -1;

                        if(itemChanged || _currZoomedItemIndex !== _currentItemIndex) {
                            self.updateCurrItem();
                        }

                        _shout('mainScrollAnimComplete');
                    }
                );

                if(itemChanged) {
                    self.updateCurrItem(true);
                }

                return itemChanged;
            },

            _calculateZoomLevel = function(touchesDistance) {
                return  1 / _startPointsDistance * touchesDistance * _startZoomLevel;
            },

            // Resets zoom if it's out of bounds
            _completeZoomGesture = function() {
                var destZoomLevel = _currZoomLevel,
                    minZoomLevel = _getMinZoomLevel(),
                    maxZoomLevel = _getMaxZoomLevel();

                if ( _currZoomLevel < minZoomLevel ) {
                    destZoomLevel = minZoomLevel;
                } else if ( _currZoomLevel > maxZoomLevel ) {
                    destZoomLevel = maxZoomLevel;
                }

                var destOpacity = 1,
                    onUpdate,
                    initialOpacity = _bgOpacity;

                if(_opacityChanged && !_isZoomingIn && !_wasOverInitialZoom && _currZoomLevel < minZoomLevel) {
                    //_closedByScroll = true;
                    self.close();
                    return true;
                }

                if(_opacityChanged) {
                    onUpdate = function(now) {
                        _applyBgOpacity(  (destOpacity - initialOpacity) * now + initialOpacity );
                    };
                }

                self.zoomTo(destZoomLevel, 0, 200,  framework.easing.cubic.out, onUpdate);
                return true;
            };


        _registerModule('Gestures', {
            publicMethods: {

                initGestures: function() {

                    // helper function that builds touch/pointer/mouse events
                    var addEventNames = function(pref, down, move, up, cancel) {
                        _dragStartEvent = pref + down;
                        _dragMoveEvent = pref + move;
                        _dragEndEvent = pref + up;
                        if(cancel) {
                            _dragCancelEvent = pref + cancel;
                        } else {
                            _dragCancelEvent = '';
                        }
                    };

                    _pointerEventEnabled = _features.pointerEvent;
                    if(_pointerEventEnabled && _features.touch) {
                        // we don't need touch events, if browser supports pointer events
                        _features.touch = false;
                    }

                    if(_pointerEventEnabled) {
                        if(navigator.pointerEnabled) {
                            addEventNames('pointer', 'down', 'move', 'up', 'cancel');
                        } else {
                            // IE10 pointer events are case-sensitive
                            addEventNames('MSPointer', 'Down', 'Move', 'Up', 'Cancel');
                        }
                    } else if(_features.touch) {
                        addEventNames('touch', 'start', 'move', 'end', 'cancel');
                        _likelyTouchDevice = true;
                    } else {
                        addEventNames('mouse', 'down', 'move', 'up');
                    }

                    _upMoveEvents = _dragMoveEvent + ' ' + _dragEndEvent  + ' ' +  _dragCancelEvent;
                    _downEvents = _dragStartEvent;

                    if(_pointerEventEnabled && !_likelyTouchDevice) {
                        _likelyTouchDevice = (navigator.maxTouchPoints > 1) || (navigator.msMaxTouchPoints > 1);
                    }
                    // make variable public
                    self.likelyTouchDevice = _likelyTouchDevice;

                    _globalEventHandlers[_dragStartEvent] = _onDragStart;
                    _globalEventHandlers[_dragMoveEvent] = _onDragMove;
                    _globalEventHandlers[_dragEndEvent] = _onDragRelease; // the Kraken

                    if(_dragCancelEvent) {
                        _globalEventHandlers[_dragCancelEvent] = _globalEventHandlers[_dragEndEvent];
                    }

                    // Bind mouse events on device with detected hardware touch support, in case it supports multiple types of input.
                    if(_features.touch) {
                        _downEvents += ' mousedown';
                        _upMoveEvents += ' mousemove mouseup';
                        _globalEventHandlers.mousedown = _globalEventHandlers[_dragStartEvent];
                        _globalEventHandlers.mousemove = _globalEventHandlers[_dragMoveEvent];
                        _globalEventHandlers.mouseup = _globalEventHandlers[_dragEndEvent];
                    }

                    if(!_likelyTouchDevice) {
                        // don't allow pan to next slide from zoomed state on Desktop
                        _options.allowPanToNext = false;
                    }
                }

            }
        });


        /*>>gestures*/

        /*>>show-hide-transition*/
        /**
         * show-hide-transition.js:
         *
         * Manages initial opening or closing transition.
         *
         * If you're not planning to use transition for gallery at all,
         * you may set options hideAnimationDuration and showAnimationDuration to 0,
         * and just delete startAnimation function.
         *
         */


        var _showOrHideTimeout,
            _showOrHide = function(item, img, out, completeFn) {

                if(_showOrHideTimeout) {
                    clearTimeout(_showOrHideTimeout);
                }

                _initialZoomRunning = true;
                _initialContentSet = true;

                // dimensions of small thumbnail {x:,y:,w:}.
                // Height is optional, as calculated based on large image.
                var thumbBounds;
                if(item.initialLayout) {
                    thumbBounds = item.initialLayout;
                    item.initialLayout = null;
                } else {
                    thumbBounds = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
                }

                var duration = out ? _options.hideAnimationDuration : _options.showAnimationDuration;

                var onComplete = function() {
                    _stopAnimation('initialZoom');
                    if(!out) {
                        _applyBgOpacity(1);
                        if(img) {
                            img.style.display = 'block';
                        }
                        framework.addClass(template, 'pswp--animated-in');
                        _shout('initialZoom' + (out ? 'OutEnd' : 'InEnd'));
                    } else {
                        self.template.removeAttribute('style');
                        self.bg.removeAttribute('style');
                    }

                    if(completeFn) {
                        completeFn();
                    }
                    _initialZoomRunning = false;
                };

                // if bounds aren't provided, just open gallery without animation
                if(!duration || !thumbBounds || thumbBounds.x === undefined) {

                    _shout('initialZoom' + (out ? 'Out' : 'In') );

                    _currZoomLevel = item.initialZoomLevel;
                    _equalizePoints(_panOffset,  item.initialPosition );
                    _applyCurrentZoomPan();

                    template.style.opacity = out ? 0 : 1;
                    _applyBgOpacity(1);

                    if(duration) {
                        setTimeout(function() {
                            onComplete();
                        }, duration);
                    } else {
                        onComplete();
                    }

                    return;
                }

                var startAnimation = function() {
                    var closeWithRaf = _closedByScroll,
                        fadeEverything = !self.currItem.src || self.currItem.loadError || _options.showHideOpacity;

                    // apply hw-acceleration to image
                    if(item.miniImg) {
                        item.miniImg.style.webkitBackfaceVisibility = 'hidden';
                    }

                    if(!out) {
                        _currZoomLevel = thumbBounds.w / item.w;
                        _panOffset.x = thumbBounds.x;
                        _panOffset.y = thumbBounds.y - _initalWindowScrollY;

                        self[fadeEverything ? 'template' : 'bg'].style.opacity = 0.001;
                        _applyCurrentZoomPan();
                    }

                    _registerStartAnimation('initialZoom');

                    if(out && !closeWithRaf) {
                        framework.removeClass(template, 'pswp--animated-in');
                    }

                    if(fadeEverything) {
                        if(out) {
                            framework[ (closeWithRaf ? 'remove' : 'add') + 'Class' ](template, 'pswp--animate_opacity');
                        } else {
                            setTimeout(function() {
                                framework.addClass(template, 'pswp--animate_opacity');
                            }, 30);
                        }
                    }

                    _showOrHideTimeout = setTimeout(function() {

                        _shout('initialZoom' + (out ? 'Out' : 'In') );


                        if(!out) {

                            // "in" animation always uses CSS transitions (instead of rAF).
                            // CSS transition work faster here,
                            // as developer may also want to animate other things,
                            // like ui on top of sliding area, which can be animated just via CSS

                            _currZoomLevel = item.initialZoomLevel;
                            _equalizePoints(_panOffset,  item.initialPosition );
                            _applyCurrentZoomPan();
                            _applyBgOpacity(1);

                            if(fadeEverything) {
                                template.style.opacity = 1;
                            } else {
                                _applyBgOpacity(1);
                            }

                            _showOrHideTimeout = setTimeout(onComplete, duration + 20);
                        } else {

                            // "out" animation uses rAF only when PhotoSwipe is closed by browser scroll, to recalculate position
                            var destZoomLevel = thumbBounds.w / item.w,
                                initialPanOffset = {
                                    x: _panOffset.x,
                                    y: _panOffset.y
                                },
                                initialZoomLevel = _currZoomLevel,
                                initalBgOpacity = _bgOpacity,
                                onUpdate = function(now) {

                                    if(now === 1) {
                                        _currZoomLevel = destZoomLevel;
                                        _panOffset.x = thumbBounds.x;
                                        _panOffset.y = thumbBounds.y  - _currentWindowScrollY;
                                    } else {
                                        _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
                                        _panOffset.x = (thumbBounds.x - initialPanOffset.x) * now + initialPanOffset.x;
                                        _panOffset.y = (thumbBounds.y - _currentWindowScrollY - initialPanOffset.y) * now + initialPanOffset.y;
                                    }

                                    _applyCurrentZoomPan();
                                    if(fadeEverything) {
                                        template.style.opacity = 1 - now;
                                    } else {
                                        _applyBgOpacity( initalBgOpacity - now * initalBgOpacity );
                                    }
                                };

                            if(closeWithRaf) {
                                _animateProp('initialZoom', 0, 1, duration, framework.easing.cubic.out, onUpdate, onComplete);
                            } else {
                                onUpdate(1);
                                _showOrHideTimeout = setTimeout(onComplete, duration + 20);
                            }
                        }

                    }, out ? 25 : 90); // Main purpose of this delay is to give browser time to paint and
                            // create composite layers of PhotoSwipe UI parts (background, controls, caption, arrows).
                            // Which avoids lag at the beginning of scale transition.
                };
                startAnimation();


            };

        /*>>show-hide-transition*/

        /*>>items-controller*/
        /**
        *
        * Controller manages gallery items, their dimensions, and their content.
        *
        */

        var _items,
            _tempPanAreaSize = {},
            _imagesToAppendPool = [],
            _initialContentSet,
            _initialZoomRunning,
            _controllerDefaultOptions = {
                index: 0,
                errorMsg: '<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',
                forceProgressiveLoading: false, // TODO
                preload: [1,1],
                getNumItemsFn: function() {
                    return _items.length;
                }
            };


        var _getItemAt,
            _getNumItems,
            _initialIsLoop,
            _getZeroBounds = function() {
                return {
                    center:{x:0,y:0},
                    max:{x:0,y:0},
                    min:{x:0,y:0}
                };
            },
            _calculateSingleItemPanBounds = function(item, realPanElementW, realPanElementH ) {
                var bounds = item.bounds;

                // position of element when it's centered
                bounds.center.x = Math.round((_tempPanAreaSize.x - realPanElementW) / 2);
                bounds.center.y = Math.round((_tempPanAreaSize.y - realPanElementH) / 2) + item.vGap.top;

                // maximum pan position
                bounds.max.x = (realPanElementW > _tempPanAreaSize.x) ?
                                    Math.round(_tempPanAreaSize.x - realPanElementW) :
                                    bounds.center.x;

                bounds.max.y = (realPanElementH > _tempPanAreaSize.y) ?
                                    Math.round(_tempPanAreaSize.y - realPanElementH) + item.vGap.top :
                                    bounds.center.y;

                // minimum pan position
                bounds.min.x = (realPanElementW > _tempPanAreaSize.x) ? 0 : bounds.center.x;
                bounds.min.y = (realPanElementH > _tempPanAreaSize.y) ? item.vGap.top : bounds.center.y;
            },
            _calculateItemSize = function(item, viewportSize, zoomLevel) {

                if (item.src && !item.loadError) {
                    var isInitial = !zoomLevel;

                    if(isInitial) {
                        if(!item.vGap) {
                            item.vGap = {top:0,bottom:0};
                        }
                        // allows overriding vertical margin for individual items
                        _shout('parseVerticalMargin', item);
                    }


                    _tempPanAreaSize.x = viewportSize.x;
                    _tempPanAreaSize.y = viewportSize.y - item.vGap.top - item.vGap.bottom;

                    if (isInitial) {
                        var hRatio = _tempPanAreaSize.x / item.w;
                        var vRatio = _tempPanAreaSize.y / item.h;

                        item.fitRatio = hRatio < vRatio ? hRatio : vRatio;
                        //item.fillRatio = hRatio > vRatio ? hRatio : vRatio;

                        var scaleMode = _options.scaleMode;

                        if (scaleMode === 'orig') {
                            zoomLevel = 1;
                        } else if (scaleMode === 'fit') {
                            zoomLevel = item.fitRatio;
                        }

                        if (zoomLevel > 1) {
                            zoomLevel = 1;
                        }

                        item.initialZoomLevel = zoomLevel;

                        if(!item.bounds) {
                            // reuse bounds object
                            item.bounds = _getZeroBounds();
                        }
                    }

                    if(!zoomLevel) {
                        return;
                    }

                    _calculateSingleItemPanBounds(item, item.w * zoomLevel, item.h * zoomLevel);

                    if (isInitial && zoomLevel === item.initialZoomLevel) {
                        item.initialPosition = item.bounds.center;
                    }

                    return item.bounds;
                } else {
                    item.w = item.h = 0;
                    item.initialZoomLevel = item.fitRatio = 1;
                    item.bounds = _getZeroBounds();
                    item.initialPosition = item.bounds.center;

                    // if it's not image, we return zero bounds (content is not zoomable)
                    return item.bounds;
                }

            },




            _appendImage = function(index, item, baseDiv, img, preventAnimation, keepPlaceholder) {


                if(item.loadError) {
                    return;
                }

                if(img) {

                    item.imageAppended = true;
                    _setImageSize(item, img, (item === self.currItem && _renderMaxResolution) );

                    baseDiv.appendChild(img);

                    if(keepPlaceholder) {
                        setTimeout(function() {
                            if(item && item.loaded && item.placeholder) {
                                item.placeholder.style.display = 'none';
                                item.placeholder = null;
                            }
                        }, 500);
                    }
                }
            },



            _preloadImage = function(item) {
                item.loading = true;
                item.loaded = false;
                var img = item.img = framework.createEl('pswp__img', 'img');
                var onComplete = function() {
                    item.loading = false;
                    item.loaded = true;

                    if(item.loadComplete) {
                        item.loadComplete(item);
                    } else {
                        item.img = null; // no need to store image object
                    }
                    img.onload = img.onerror = null;
                    img = null;
                };
                img.onload = onComplete;
                img.onerror = function() {
                    item.loadError = true;
                    onComplete();
                };

                img.src = item.src;// + '?a=' + Math.random();

                return img;
            },
            _checkForError = function(item, cleanUp) {
                if(item.src && item.loadError && item.container) {

                    if(cleanUp) {
                        item.container.innerHTML = '';
                    }

                    item.container.innerHTML = _options.errorMsg.replace('%url%',  item.src );
                    return true;

                }
            },
            _setImageSize = function(item, img, maxRes) {
                if(!item.src) {
                    return;
                }

                if(!img) {
                    img = item.container.lastChild;
                }

                var w = maxRes ? item.w : Math.round(item.w * item.fitRatio),
                    h = maxRes ? item.h : Math.round(item.h * item.fitRatio);

                if(item.placeholder && !item.loaded) {
                    item.placeholder.style.width = w + 'px';
                    item.placeholder.style.height = h + 'px';
                }

                img.style.width = w + 'px';
                img.style.height = h + 'px';
            },
            _appendImagesPool = function() {

                if(_imagesToAppendPool.length) {
                    var poolItem;

                    for(var i = 0; i < _imagesToAppendPool.length; i++) {
                        poolItem = _imagesToAppendPool[i];
                        if( poolItem.holder.index === poolItem.index ) {
                            _appendImage(poolItem.index, poolItem.item, poolItem.baseDiv, poolItem.img, false, poolItem.clearPlaceholder);
                        }
                    }
                    _imagesToAppendPool = [];
                }
            };



        _registerModule('Controller', {

            publicMethods: {

                lazyLoadItem: function(index) {
                    index = _getLoopedId(index);
                    var item = _getItemAt(index);

                    if(!item || ((item.loaded || item.loading) && !_itemsNeedUpdate)) {
                        return;
                    }

                    _shout('gettingData', index, item);

                    if (!item.src) {
                        return;
                    }

                    _preloadImage(item);
                },
                initController: function() {
                    framework.extend(_options, _controllerDefaultOptions, true);
                    self.items = _items = items;
                    _getItemAt = self.getItemAt;
                    _getNumItems = _options.getNumItemsFn; //self.getNumItems;



                    _initialIsLoop = _options.loop;
                    if(_getNumItems() < 3) {
                        _options.loop = false; // disable loop if less then 3 items
                    }

                    _listen('beforeChange', function(diff) {

                        var p = _options.preload,
                            isNext = diff === null ? true : (diff >= 0),
                            preloadBefore = Math.min(p[0], _getNumItems() ),
                            preloadAfter = Math.min(p[1], _getNumItems() ),
                            i;


                        for(i = 1; i <= (isNext ? preloadAfter : preloadBefore); i++) {
                            self.lazyLoadItem(_currentItemIndex+i);
                        }
                        for(i = 1; i <= (isNext ? preloadBefore : preloadAfter); i++) {
                            self.lazyLoadItem(_currentItemIndex-i);
                        }
                    });

                    _listen('initialLayout', function() {
                        self.currItem.initialLayout = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
                    });

                    _listen('mainScrollAnimComplete', _appendImagesPool);
                    _listen('initialZoomInEnd', _appendImagesPool);



                    _listen('destroy', function() {
                        var item;
                        for(var i = 0; i < _items.length; i++) {
                            item = _items[i];
                            // remove reference to DOM elements, for GC
                            if(item.container) {
                                item.container = null;
                            }
                            if(item.placeholder) {
                                item.placeholder = null;
                            }
                            if(item.img) {
                                item.img = null;
                            }
                            if(item.preloader) {
                                item.preloader = null;
                            }
                            if(item.loadError) {
                                item.loaded = item.loadError = false;
                            }
                        }
                        _imagesToAppendPool = null;
                    });
                },


                getItemAt: function(index) {
                    if (index >= 0) {
                        return _items[index] !== undefined ? _items[index] : false;
                    }
                    return false;
                },

                allowProgressiveImg: function() {
                    // 1. Progressive image loading isn't working on webkit/blink
                    //    when hw-acceleration (e.g. translateZ) is applied to IMG element.
                    //    That's why in PhotoSwipe parent element gets zoom transform, not image itself.
                    //
                    // 2. Progressive image loading sometimes blinks in webkit/blink when applying animation to parent element.
                    //    That's why it's disabled on touch devices (mainly because of swipe transition)
                    //
                    // 3. Progressive image loading sometimes doesn't work in IE (up to 11).

                    // Don't allow progressive loading on non-large touch devices
                    return _options.forceProgressiveLoading || !_likelyTouchDevice || _options.mouseUsed || screen.width > 1200;
                    // 1200 - to eliminate touch devices with large screen (like Chromebook Pixel)
                },

                setContent: function(holder, index) {

                    if(_options.loop) {
                        index = _getLoopedId(index);
                    }

                    var prevItem = self.getItemAt(holder.index);
                    if(prevItem) {
                        prevItem.container = null;
                    }

                    var item = self.getItemAt(index),
                        img;

                    if(!item) {
                        holder.el.innerHTML = '';
                        return;
                    }

                    // allow to override data
                    _shout('gettingData', index, item);

                    holder.index = index;
                    holder.item = item;

                    // base container DIV is created only once for each of 3 holders
                    var baseDiv = item.container = framework.createEl('pswp__zoom-wrap');



                    if(!item.src && item.html) {
                        if(item.html.tagName) {
                            baseDiv.appendChild(item.html);
                        } else {
                            baseDiv.innerHTML = item.html;
                        }
                    }

                    _checkForError(item);

                    _calculateItemSize(item, _viewportSize);

                    if(item.src && !item.loadError && !item.loaded) {

                        item.loadComplete = function(item) {

                            // gallery closed before image finished loading
                            if(!_isOpen) {
                                return;
                            }

                            // check if holder hasn't changed while image was loading
                            if(holder && holder.index === index ) {
                                if( _checkForError(item, true) ) {
                                    item.loadComplete = item.img = null;
                                    _calculateItemSize(item, _viewportSize);
                                    _applyZoomPanToItem(item);

                                    if(holder.index === _currentItemIndex) {
                                        // recalculate dimensions
                                        self.updateCurrZoomItem();
                                    }
                                    return;
                                }
                                if( !item.imageAppended ) {
                                    if(_features.transform && (_mainScrollAnimating || _initialZoomRunning) ) {
                                        _imagesToAppendPool.push({
                                            item:item,
                                            baseDiv:baseDiv,
                                            img:item.img,
                                            index:index,
                                            holder:holder,
                                            clearPlaceholder:true
                                        });
                                    } else {
                                        _appendImage(index, item, baseDiv, item.img, _mainScrollAnimating || _initialZoomRunning, true);
                                    }
                                } else {
                                    // remove preloader & mini-img
                                    if(!_initialZoomRunning && item.placeholder) {
                                        item.placeholder.style.display = 'none';
                                        item.placeholder = null;
                                    }
                                }
                            }

                            item.loadComplete = null;
                            item.img = null; // no need to store image element after it's added

                            _shout('imageLoadComplete', index, item);
                        };

                        if(framework.features.transform) {

                            var placeholderClassName = 'pswp__img pswp__img--placeholder';
                            placeholderClassName += (item.msrc ? '' : ' pswp__img--placeholder--blank');

                            var placeholder = framework.createEl(placeholderClassName, item.msrc ? 'img' : '');
                            if(item.src) {
                                placeholder.src = item.src;
                                placeholder.style.display='none';
                            }

                            _setImageSize(item, placeholder);
                            baseDiv.appendChild(placeholder);
                            setTimeout(function(){
                                $('.pswp__img--placeholder').fadeIn();
                            },100)
                            item.placeholder = placeholder;
                        }


                        if(!item.loading) {
                            _preloadImage(item);
                        }


                        if( self.allowProgressiveImg() ) {
                            // just append image
                            if(!_initialContentSet && _features.transform) {
                                _imagesToAppendPool.push({
                                    item:item,
                                    baseDiv:baseDiv,
                                    img:item.img,
                                    index:index,
                                    holder:holder
                                });
                            } else {
                                _appendImage(index, item, baseDiv, item.img, true, true);
                            }
                        }

                    } else if(item.src && !item.loadError) {
                        // image object is created every time, due to bugs of image loading & delay when switching images
                        img = framework.createEl('pswp__img', 'img');
                        img.style.opacity = 1;
                        img.src = item.src;
                        _setImageSize(item, img);
                        _appendImage(index, item, baseDiv, img, true);
                    }


                    if(!_initialContentSet && index === _currentItemIndex) {
                        _currZoomElementStyle = baseDiv.style;
                        _showOrHide(item, (img ||item.img) );
                    } else {
                        _applyZoomPanToItem(item);
                    }

                    holder.el.innerHTML = '';
                    holder.el.appendChild(baseDiv);
                },

                cleanSlide: function( item ) {
                    if(item.img ) {
                        item.img.onload = item.img.onerror = null;
                    }
                    item.loaded = item.loading = item.img = item.imageAppended = false;
                }

            }
        });

        /*>>items-controller*/

        /*>>tap*/
        /**
         * tap.js:
         *
         * Displatches tap and double-tap events.
         *
         */

        var tapTimer,
            tapReleasePoint = {},
            _dispatchTapEvent = function(origEvent, releasePoint, pointerType) {
                var e = document.createEvent( 'CustomEvent' ),
                    eDetail = {
                        origEvent:origEvent,
                        target:origEvent.target,
                        releasePoint: releasePoint,
                        pointerType:pointerType || 'touch'
                    };

                e.initCustomEvent( 'pswpTap', true, true, eDetail );
                origEvent.target.dispatchEvent(e);
            };

        _registerModule('Tap', {
            publicMethods: {
                initTap: function() {
                    _listen('firstTouchStart', self.onTapStart);
                    _listen('touchRelease', self.onTapRelease);
                    _listen('destroy', function() {
                        tapReleasePoint = {};
                        tapTimer = null;
                    });
                },
                onTapStart: function(touchList) {
                    if(touchList.length > 1) {
                        clearTimeout(tapTimer);
                        tapTimer = null;
                    }
                },
                onTapRelease: function(e, releasePoint) {
                    if(!releasePoint) {
                        return;
                    }

                    if(!_moved && !_isMultitouch && !_numAnimations) {
                        var p0 = releasePoint;
                        if(tapTimer) {
                            clearTimeout(tapTimer);
                            tapTimer = null;

                            // Check if taped on the same place
                            if ( _isNearbyPoints(p0, tapReleasePoint) ) {
                                _shout('doubleTap', p0);
                                return;
                            }
                        }

                        if(releasePoint.type === 'mouse') {
                            _dispatchTapEvent(e, releasePoint, 'mouse');
                            return;
                        }

                        var clickedTagName = e.target.tagName.toUpperCase();
                        // avoid double tap delay on buttons and elements that have class pswp__single-tap
                        if(clickedTagName === 'BUTTON' || framework.hasClass(e.target, 'pswp__single-tap') ) {
                            _dispatchTapEvent(e, releasePoint);
                            return;
                        }

                        _equalizePoints(tapReleasePoint, p0);

                        tapTimer = setTimeout(function() {
                            _dispatchTapEvent(e, releasePoint);
                            tapTimer = null;
                        }, 300);
                    }
                }
            }
        });

        /*>>tap*/

        /*>>desktop-zoom*/
        /**
         *
         * desktop-zoom.js:
         *
         * - Binds mousewheel event for paning zoomed image.
         * - Manages "dragging", "zoomed-in", "zoom-out" classes.
         *   (which are used for cursors and zoom icon)
         * - Adds toggleDesktopZoom function.
         *
         */

        var _wheelDelta;

        _registerModule('DesktopZoom', {

            publicMethods: {

                initDesktopZoom: function() {

                    if(_oldIE) {
                        // no zoom for old IE (<=8)
                        return;
                    }

                    if(_likelyTouchDevice) {
                        // if detected hardware touch support, we wait until mouse is used,
                        // and only then apply desktop-zoom features
                        _listen('mouseUsed', function() {
                            self.setupDesktopZoom();
                        });
                    } else {
                        self.setupDesktopZoom(true);
                    }

                },

                setupDesktopZoom: function(onInit) {

                    _wheelDelta = {};

                    var events = 'wheel mousewheel DOMMouseScroll';

                    _listen('bindEvents', function() {
                        framework.bind(template, events,  self.handleMouseWheel);
                    });

                    _listen('unbindEvents', function() {
                        if(_wheelDelta) {
                            framework.unbind(template, events, self.handleMouseWheel);
                        }
                    });

                    self.mouseZoomedIn = false;

                    var hasDraggingClass,
                        updateZoomable = function() {
                            if(self.mouseZoomedIn) {
                                framework.removeClass(template, 'pswp--zoomed-in');
                                self.mouseZoomedIn = false;
                            }
                            // if(_currZoomLevel < 1) {
                                framework.addClass(template, 'pswp--zoom-allowed');
                            // } else {
                            //     framework.removeClass(template, 'pswp--zoom-allowed');
                            // }
                            removeDraggingClass();
                        },
                        removeDraggingClass = function() {
                            if(hasDraggingClass) {
                                framework.removeClass(template, 'pswp--dragging');
                                hasDraggingClass = false;
                            }
                        };

                    _listen('resize' , updateZoomable);
                    _listen('afterChange' , updateZoomable);
                    _listen('pointerDown', function() {
                        if(self.mouseZoomedIn) {
                            hasDraggingClass = true;
                            framework.addClass(template, 'pswp--dragging');
                        }
                    });
                    _listen('pointerUp', removeDraggingClass);

                    if(!onInit) {
                        updateZoomable();
                    }

                },

                handleMouseWheel: function(e) {

                    if(_currZoomLevel <= self.currItem.fitRatio) {
                        if( _options.modal ) {

                            if (!_options.closeOnScroll || _numAnimations || _isDragging) {
                                e.preventDefault();
                            } else if(_transformKey && Math.abs(e.deltaY) > 2) {
                                // close PhotoSwipe
                                // if browser supports transforms & scroll changed enough
                                _closedByScroll = true;
                                self.close();
                            }

                        }
                        return true;
                    }

                    // allow just one event to fire
                    e.stopPropagation();

                    // https://developer.mozilla.org/en-US/docs/Web/Events/wheel
                    _wheelDelta.x = 0;

                    if('deltaX' in e) {
                        if(e.deltaMode === 1 /* DOM_DELTA_LINE */) {
                            // 18 - average line height
                            _wheelDelta.x = e.deltaX * 18;
                            _wheelDelta.y = e.deltaY * 18;
                        } else {
                            _wheelDelta.x = e.deltaX;
                            _wheelDelta.y = e.deltaY;
                        }
                    } else if('wheelDelta' in e) {
                        if(e.wheelDeltaX) {
                            _wheelDelta.x = -0.16 * e.wheelDeltaX;
                        }
                        if(e.wheelDeltaY) {
                            _wheelDelta.y = -0.16 * e.wheelDeltaY;
                        } else {
                            _wheelDelta.y = -0.16 * e.wheelDelta;
                        }
                    } else if('detail' in e) {
                        _wheelDelta.y = e.detail;
                    } else {
                        return;
                    }

                    _calculatePanBounds(_currZoomLevel, true);

                    var newPanX = _panOffset.x - _wheelDelta.x,
                        newPanY = _panOffset.y - _wheelDelta.y;

                    // only prevent scrolling in nonmodal mode when not at edges
                    if (_options.modal ||
                        (
                        newPanX <= _currPanBounds.min.x && newPanX >= _currPanBounds.max.x &&
                        newPanY <= _currPanBounds.min.y && newPanY >= _currPanBounds.max.y
                        ) ) {
                        e.preventDefault();
                    }

                    // TODO: use rAF instead of mousewheel?
                    self.panTo(newPanX, newPanY);
                },

                toggleDesktopZoom: function(centerPoint) {
                    centerPoint = centerPoint || {x:_viewportSize.x/2 + _offset.x, y:_viewportSize.y/2 + _offset.y };

                    var doubleTapZoomLevel = _options.getDoubleTapZoom(true, self.currItem);
                    var zoomOut = _currZoomLevel === doubleTapZoomLevel;

                    self.mouseZoomedIn = !zoomOut;

                    self.zoomTo(zoomOut ? self.currItem.initialZoomLevel : doubleTapZoomLevel, centerPoint, 333);
                    framework[ (!zoomOut ? 'add' : 'remove') + 'Class'](template, 'pswp--zoomed-in');
                }

            }
        });


        /*>>desktop-zoom*/

        /*>>history*/
        /**
         *
         * history.js:
         *
         * - Back button to close gallery.
         *
         * - Unique URL for each slide: example.com/&pid=1&gid=3
         *   (where PID is picture index, and GID and gallery index)
         *
         * - Switch URL when slides change.
         *
         */


        var _historyDefaultOptions = {
            history: true,
            galleryUID: 1
        };

        var _historyUpdateTimeout,
            _hashChangeTimeout,
            _hashAnimCheckTimeout,
            _hashChangedByScript,
            _hashChangedByHistory,
            _hashReseted,
            _initialHash,
            _historyChanged,
            _closedFromURL,
            _urlChangedOnce,
            _windowLoc,

            _supportsPushState,

            _getHash = function() {
                return _windowLoc.hash.substring(1);
            },
            _cleanHistoryTimeouts = function() {

                if(_historyUpdateTimeout) {
                    clearTimeout(_historyUpdateTimeout);
                }

                if(_hashAnimCheckTimeout) {
                    clearTimeout(_hashAnimCheckTimeout);
                }
            },

            // pid - Picture index
            // gid - Gallery index
            _parseItemIndexFromURL = function() {
                var hash = _getHash(),
                    params = {};

                if(hash.length < 5) { // pid=1
                    return params;
                }

                var i, vars = hash.split('&');
                for (i = 0; i < vars.length; i++) {
                    if(!vars[i]) {
                        continue;
                    }
                    var pair = vars[i].split('=');
                    if(pair.length < 2) {
                        continue;
                    }
                    params[pair[0]] = pair[1];
                }
                if(_options.galleryPIDs) {
                    // detect custom pid in hash and search for it among the items collection
                    var searchfor = params.pid;
                    params.pid = 0; // if custom pid cannot be found, fallback to the first item
                    for(i = 0; i < _items.length; i++) {
                        if(_items[i].pid === searchfor) {
                            params.pid = i;
                            break;
                        }
                    }
                } else {
                    params.pid = parseInt(params.pid,10)-1;
                }
                if( params.pid < 0 ) {
                    params.pid = 0;
                }
                return params;
            },
            _updateHash = function() {

                if(_hashAnimCheckTimeout) {
                    clearTimeout(_hashAnimCheckTimeout);
                }


                if(_numAnimations || _isDragging) {
                    // changing browser URL forces layout/paint in some browsers, which causes noticable lag during animation
                    // that's why we update hash only when no animations running
                    _hashAnimCheckTimeout = setTimeout(_updateHash, 500);
                    return;
                }

                if(_hashChangedByScript) {
                    clearTimeout(_hashChangeTimeout);
                } else {
                    _hashChangedByScript = true;
                }


                var pid = (_currentItemIndex + 1);
                var item = _getItemAt( _currentItemIndex );
                if(item.hasOwnProperty('pid')) {
                    // carry forward any custom pid assigned to the item
                    pid = item.pid;
                }
                var newHash = _initialHash + '&'  +  'gid=' + _options.galleryUID + '&' + 'pid=' + pid;

                if(!_historyChanged) {
                    if(_windowLoc.hash.indexOf(newHash) === -1) {
                        _urlChangedOnce = true;
                    }
                    // first time - add new hisory record, then just replace
                }

                var newURL = _windowLoc.href.split('#')[0] + '#' +  newHash;

                if( _supportsPushState ) {

                    if('#' + newHash !== window.location.hash) {
                        history[_historyChanged ? 'replaceState' : 'pushState']('', document.title, newURL);
                    }

                } else {
                    if(_historyChanged) {
                        _windowLoc.replace( newURL );
                    } else {
                        _windowLoc.hash = newHash;
                    }
                }



                _historyChanged = true;
                _hashChangeTimeout = setTimeout(function() {
                    _hashChangedByScript = false;
                }, 60);
            };





        _registerModule('History', {



            publicMethods: {
                initHistory: function() {

                    framework.extend(_options, _historyDefaultOptions, true);

                    if( !_options.history ) {
                        return;
                    }


                    _windowLoc = window.location;
                    _urlChangedOnce = false;
                    _closedFromURL = false;
                    _historyChanged = false;
                    _initialHash = _getHash();
                    _supportsPushState = ('pushState' in history);


                    if(_initialHash.indexOf('gid=') > -1) {
                        _initialHash = _initialHash.split('&gid=')[0];
                        _initialHash = _initialHash.split('?gid=')[0];
                    }


                    _listen('afterChange', self.updateURL);
                    _listen('unbindEvents', function() {
                        framework.unbind(window, 'hashchange', self.onHashChange);
                    });


                    var returnToOriginal = function() {
                        _hashReseted = true;
                        if(!_closedFromURL) {

                            if(_urlChangedOnce) {
                                history.back();
                            } else {

                                if(_initialHash) {
                                    _windowLoc.hash = _initialHash;
                                } else {
                                    if (_supportsPushState) {

                                        // remove hash from url without refreshing it or scrolling to top
                                        history.pushState('', document.title,  _windowLoc.pathname + _windowLoc.search );
                                    } else {
                                        _windowLoc.hash = '';
                                    }
                                }
                            }

                        }

                        _cleanHistoryTimeouts();
                    };


                    _listen('unbindEvents', function() {
                        if(_closedByScroll) {
                            // if PhotoSwipe is closed by scroll, we go "back" before the closing animation starts
                            // this is done to keep the scroll position
                            returnToOriginal();
                        }
                    });
                    _listen('destroy', function() {
                        if(!_hashReseted) {
                            returnToOriginal();
                        }
                    });
                    _listen('firstUpdate', function() {
                        _currentItemIndex = _parseItemIndexFromURL().pid;
                    });




                    var index = _initialHash.indexOf('pid=');
                    if(index > -1) {
                        _initialHash = _initialHash.substring(0, index);
                        if(_initialHash.slice(-1) === '&') {
                            _initialHash = _initialHash.slice(0, -1);
                        }
                    }


                    setTimeout(function() {
                        if(_isOpen) { // hasn't destroyed yet
                            framework.bind(window, 'hashchange', self.onHashChange);
                        }
                    }, 40);

                },
                onHashChange: function() {

                    if(_getHash() === _initialHash) {

                        _closedFromURL = true;
                        self.close();
                        return;
                    }
                    if(!_hashChangedByScript) {

                        _hashChangedByHistory = true;
                        self.goTo( _parseItemIndexFromURL().pid );
                        _hashChangedByHistory = false;
                    }

                },
                updateURL: function() {

                    // Delay the update of URL, to avoid lag during transition,
                    // and to not to trigger actions like "refresh page sound" or "blinking favicon" to often

                    _cleanHistoryTimeouts();


                    if(_hashChangedByHistory) {
                        return;
                    }

                    if(!_historyChanged) {
                        _updateHash(); // first time
                    } else {
                        _historyUpdateTimeout = setTimeout(_updateHash, 50);
                    }
                }

            }
        });


    /*>>history*/
        framework.extend(self, publicMethods); 
    };

    return PhotoSwipe;
});


/*! PhotoSwipe Default UI - 4.1.0 - 2015-09-04
* http://photoswipe.com
* Copyright (c) 2015 Dmitry Semenov; 
*/
!function(a,b){"function"==typeof define&&define.amd?define(b):"object"==typeof exports?module.exports=b():a.PhotoSwipeUI_Default=b()}(this,function(){"use strict";var a=function(a,b){var c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v=this,w=!1,x=!0,y=!0,z={barsSize:{top:44,bottom:"auto"},closeElClasses:["item","caption","zoom-wrap","ui","top-bar"],timeToIdle:4e3,timeToIdleOutside:1e3,loadingIndicatorDelay:1e3,addCaptionHTMLFn:function(a,b){return a.title?(b.children[0].innerHTML=a.title,!0):(b.children[0].innerHTML="",!1)},closeEl:!0,captionEl:!0,fullscreenEl:!0,zoomEl:!0,shareEl:!0,counterEl:!0,arrowEl:!0,preloaderEl:!0,tapToClose:!1,tapToToggleControls:!0,clickToCloseNonZoomable:!0,shareButtons:[{id:"facebook",label:"Share on Facebook",url:"https://www.facebook.com/sharer/sharer.php?u={{url}}"},{id:"twitter",label:"Tweet",url:"https://twitter.com/intent/tweet?text={{text}}&url={{url}}"},{id:"pinterest",label:"Pin it",url:"http://www.pinterest.com/pin/create/button/?url={{url}}&media={{image_url}}&description={{text}}"},{id:"download",label:"Download image",url:"{{raw_image_url}}",download:!0}],getImageURLForShare:function(){return a.currItem.src||""},getPageURLForShare:function(){return window.location.href},getTextForShare:function(){return a.currItem.title||""},indexIndicatorSep:" / "},A=function(a){if(r)return!0;a=a||window.event,q.timeToIdle&&q.mouseUsed&&!k&&K();for(var c,d,e=a.target||a.srcElement,f=e.className,g=0;g<S.length;g++)c=S[g],c.onTap&&f.indexOf("pswp__"+c.name)>-1&&(c.onTap(),d=!0);if(d){a.stopPropagation&&a.stopPropagation(),r=!0;var h=b.features.isOldAndroid?600:30;s=setTimeout(function(){r=!1},h)}},B=function(){return!a.likelyTouchDevice||q.mouseUsed||screen.width>1200},C=function(a,c,d){b[(d?"add":"remove")+"Class"](a,"pswp__"+c)},D=function(){var a=1===q.getNumItemsFn();a!==p&&(C(d,"ui--one-slide",a),p=a)},E=function(){C(i,"share-modal--hidden",y)},F=function(){return y=!y,y?(b.removeClass(i,"pswp__share-modal--fade-in"),setTimeout(function(){y&&E()},300)):(E(),setTimeout(function(){y||b.addClass(i,"pswp__share-modal--fade-in")},30)),y||H(),!1},G=function(b){b=b||window.event;var c=b.target||b.srcElement;return a.shout("shareLinkClick",b,c),c.href?c.hasAttribute("download")?!0:(window.open(c.href,"pswp_share","scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=550,height=420,top=100,left="+(window.screen?Math.round(screen.width/2-275):100)),y||F(),!1):!1},H=function(){for(var a,b,c,d,e,f="",g=0;g<q.shareButtons.length;g++)a=q.shareButtons[g],c=q.getImageURLForShare(a),d=q.getPageURLForShare(a),e=q.getTextForShare(a),b=a.url.replace("{{url}}",encodeURIComponent(d)).replace("{{image_url}}",encodeURIComponent(c)).replace("{{raw_image_url}}",c).replace("{{text}}",encodeURIComponent(e)),f+='<a href="'+b+'" target="_blank" class="pswp__share--'+a.id+'"'+(a.download?"download":"")+">"+a.label+"</a>",q.parseShareButtonOut&&(f=q.parseShareButtonOut(a,f));i.children[0].innerHTML=f,i.children[0].onclick=G},I=function(a){for(var c=0;c<q.closeElClasses.length;c++)if(b.hasClass(a,"pswp__"+q.closeElClasses[c]))return!0},J=0,K=function(){clearTimeout(u),J=0,k&&v.setIdle(!1)},L=function(a){a=a?a:window.event;var b=a.relatedTarget||a.toElement;b&&"HTML"!==b.nodeName||(clearTimeout(u),u=setTimeout(function(){v.setIdle(!0)},q.timeToIdleOutside))},M=function(){q.fullscreenEl&&!b.features.isOldAndroid&&(c||(c=v.getFullscreenAPI()),c?(b.bind(document,c.eventK,v.updateFullscreen),v.updateFullscreen(),b.addClass(a.template,"pswp--supports-fs")):b.removeClass(a.template,"pswp--supports-fs"))},N=function(){q.preloaderEl&&(O(!0),l("beforeChange",function(){clearTimeout(o),o=setTimeout(function(){a.currItem&&a.currItem.loading?(!a.allowProgressiveImg()||a.currItem.img&&!a.currItem.img.naturalWidth)&&O(!1):O(!0)},q.loadingIndicatorDelay)}),l("imageLoadComplete",function(b,c){a.currItem===c&&O(!0)}))},O=function(a){n!==a&&(C(m,"preloader--active",!a),n=a)},P=function(a){var c=a.vGap;if(B()){var g=q.barsSize;if(q.captionEl&&"auto"===g.bottom)if(f||(f=b.createEl("pswp__caption pswp__caption--fake"),f.appendChild(b.createEl("pswp__caption__center")),d.insertBefore(f,e),b.addClass(d,"pswp__ui--fit")),q.addCaptionHTMLFn(a,f,!0)){var h=f.clientHeight;c.bottom=parseInt(h,10)||44}else c.bottom=g.top;else c.bottom="auto"===g.bottom?0:g.bottom;c.top=g.top}else c.top=c.bottom=0},Q=function(){q.timeToIdle&&l("mouseUsed",function(){b.bind(document,"mousemove",K),b.bind(document,"mouseout",L),t=setInterval(function(){J++,2===J&&v.setIdle(!0)},q.timeToIdle/2)})},R=function(){l("onVerticalDrag",function(a){x&&.95>a?v.hideControls():!x&&a>=.95&&v.showControls()});var a;l("onPinchClose",function(b){x&&.9>b?(v.hideControls(),a=!0):a&&!x&&b>.9&&v.showControls()}),l("zoomGestureEnded",function(){a=!1,a&&!x&&v.showControls()})},S=[{name:"caption",option:"captionEl",onInit:function(a){e=a}},{name:"share-modal",option:"shareEl",onInit:function(a){i=a},onTap:function(){F()}},{name:"button--share",option:"shareEl",onInit:function(a){h=a},onTap:function(){F()}},{name:"button--zoom",option:"zoomEl",onTap:a.toggleDesktopZoom},{name:"counter",option:"counterEl",onInit:function(a){g=a}},{name:"button--close",option:"closeEl",onTap:a.close},{name:"button--arrow--left",option:"arrowEl",onTap:a.prev},{name:"button--arrow--right",option:"arrowEl",onTap:a.next},{name:"button--fs",option:"fullscreenEl",onTap:function(){c.isFullscreen()?c.exit():c.enter()}},{name:"preloader",option:"preloaderEl",onInit:function(a){m=a}}],T=function(){var a,c,e,f=function(d){if(d)for(var f=d.length,g=0;f>g;g++){a=d[g],c=a.className;for(var h=0;h<S.length;h++)e=S[h],c.indexOf("pswp__"+e.name)>-1&&(q[e.option]?(b.removeClass(a,"pswp__element--disabled"),e.onInit&&e.onInit(a)):b.addClass(a,"pswp__element--disabled"))}};f(d.children);var g=b.getChildByClass(d,"pswp__top-bar");g&&f(g.children)};v.init=function(){b.extend(a.options,z,!0),q=a.options,d=b.getChildByClass(a.scrollWrap,"pswp__ui"),l=a.listen,R(),l("beforeChange",v.update),l("doubleTap",function(b){var c=a.currItem.initialZoomLevel;a.getZoomLevel()!==c?a.zoomTo(c,b,333):a.zoomTo(q.getDoubleTapZoom(!1,a.currItem),b,333)}),l("preventDragEvent",function(a,b,c){var d=a.target||a.srcElement;d&&d.className&&a.type.indexOf("mouse")>-1&&(d.className.indexOf("__caption")>0||/(SMALL|STRONG|EM)/i.test(d.tagName))&&(c.prevent=!1)}),l("bindEvents",function(){b.bind(d,"pswpTap click",A),b.bind(a.scrollWrap,"pswpTap",v.onGlobalTap),a.likelyTouchDevice||b.bind(a.scrollWrap,"mouseover",v.onMouseOver)}),l("unbindEvents",function(){y||F(),t&&clearInterval(t),b.unbind(document,"mouseout",L),b.unbind(document,"mousemove",K),b.unbind(d,"pswpTap click",A),b.unbind(a.scrollWrap,"pswpTap",v.onGlobalTap),b.unbind(a.scrollWrap,"mouseover",v.onMouseOver),c&&(b.unbind(document,c.eventK,v.updateFullscreen),c.isFullscreen()&&(q.hideAnimationDuration=0,c.exit()),c=null)}),l("destroy",function(){q.captionEl&&(f&&d.removeChild(f),b.removeClass(e,"pswp__caption--empty")),i&&(i.children[0].onclick=null),b.removeClass(d,"pswp__ui--over-close"),b.addClass(d,"pswp__ui--hidden"),v.setIdle(!1)}),q.showAnimationDuration||b.removeClass(d,"pswp__ui--hidden"),l("initialZoomIn",function(){q.showAnimationDuration&&b.removeClass(d,"pswp__ui--hidden")}),l("initialZoomOut",function(){b.addClass(d,"pswp__ui--hidden")}),l("parseVerticalMargin",P),T(),q.shareEl&&h&&i&&(y=!0),D(),Q(),M(),N()},v.setIdle=function(a){k=a,C(d,"ui--idle",a)},v.update=function(){x&&a.currItem?(v.updateIndexIndicator(),q.captionEl&&(q.addCaptionHTMLFn(a.currItem,e),C(e,"caption--empty",!a.currItem.title)),w=!0):w=!1,y||F(),D()},v.updateFullscreen=function(d){d&&setTimeout(function(){a.setScrollOffset(0,b.getScrollY())},50),b[(c.isFullscreen()?"add":"remove")+"Class"](a.template,"pswp--fs")},v.updateIndexIndicator=function(){q.counterEl&&(g.innerHTML=a.getCurrentIndex()+1+q.indexIndicatorSep+q.getNumItemsFn())},v.onGlobalTap=function(c){c=c||window.event;var d=c.target||c.srcElement;if(!r)if(c.detail&&"mouse"===c.detail.pointerType){if(I(d))return void a.close();b.hasClass(d,"pswp__img")&&(1===a.getZoomLevel()&&a.getZoomLevel()<=a.currItem.fitRatio?q.clickToCloseNonZoomable&&a.close():a.toggleDesktopZoom(c.detail.releasePoint))}else if(q.tapToToggleControls&&(x?v.hideControls():v.showControls()),q.tapToClose&&(b.hasClass(d,"pswp__img")||I(d)))return void a.close()},v.onMouseOver=function(a){a=a||window.event;var b=a.target||a.srcElement;C(d,"ui--over-close",I(b))},v.hideControls=function(){b.addClass(d,"pswp__ui--hidden"),x=!1},v.showControls=function(){x=!0,w||v.update(),b.removeClass(d,"pswp__ui--hidden")},v.supportsFullscreen=function(){var a=document;return!!(a.exitFullscreen||a.mozCancelFullScreen||a.webkitExitFullscreen||a.msExitFullscreen)},v.getFullscreenAPI=function(){var b,c=document.documentElement,d="fullscreenchange";return c.requestFullscreen?b={enterK:"requestFullscreen",exitK:"exitFullscreen",elementK:"fullscreenElement",eventK:d}:c.mozRequestFullScreen?b={enterK:"mozRequestFullScreen",exitK:"mozCancelFullScreen",elementK:"mozFullScreenElement",eventK:"moz"+d}:c.webkitRequestFullscreen?b={enterK:"webkitRequestFullscreen",exitK:"webkitExitFullscreen",elementK:"webkitFullscreenElement",eventK:"webkit"+d}:c.msRequestFullscreen&&(b={enterK:"msRequestFullscreen",exitK:"msExitFullscreen",elementK:"msFullscreenElement",eventK:"MSFullscreenChange"}),b&&(b.enter=function(){return j=q.closeOnScroll,q.closeOnScroll=!1,"webkitRequestFullscreen"!==this.enterK?a.template[this.enterK]():void a.template[this.enterK](Element.ALLOW_KEYBOARD_INPUT)},b.exit=function(){return q.closeOnScroll=j,document[this.exitK]()},b.isFullscreen=function(){return document[this.elementK]}),b}};return a});$.initPhotoSwipeFromDOM = function(gallerySelector,medDom) {
    var parseThumbnailElements = function(el) {
        var thumbElements = $(medDom,el),
            numNodes = thumbElements.length,
            items = [],
            el,
            childElements,
            thumbnailEl,
            size,
            item;
        for (var i = 0; i < numNodes; i++) {
            el = thumbElements[i];
            // include only element nodes
            if (el.nodeType !== 1) {
                continue;
            }
            childElements = el.children;
            size = el.getAttribute('data-size').split('x');
            // create slide object
            item = {
                src: el.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10),
                author: el.getAttribute('data-author')
            };
            item.el = el; // save link to element for getThumbBoundsFn
            if (childElements.length > 0) {
                item.msrc = childElements[0].getAttribute('src'); // thumbnail url
                if (childElements.length > 1) {
                    item.title = childElements[1].innerHTML/*childElements[0].getAttribute('alt')*/; // caption (contents of figure)
                }
            }
            var mediumSrc = el.getAttribute('data-med');
            if (mediumSrc) {
                size = el.getAttribute('data-med-size').split('x');
                // "medium-sized" image
                item.m = {
                    src: mediumSrc,
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10)
                };
            }
            // original image
            item.o = {
                src: item.src,
                w: item.w,
                h: item.h
            };
            items.push(item);
        }
        return items;
    };
    // find nearest parent element
    var closest = function closest(el, fn) {
        return el && (fn(el) ? el : closest(el.parentNode, fn));
    };
    var onThumbnailsClick = function(e,parents) {
        e = e || window.event;
        e.preventDefault ? e.preventDefault() : e.returnValue = false;
        var eTarget = e.target || e.srcElement;
        var clickedListItem = closest(eTarget, function(el) {
            return el.tagName === 'A';
        });
        if (!clickedListItem) {
            return;
        }
        var clickedGallery = parents,
            clickedListItemMed=$(clickedListItem).data('med'),
            index;
        $(medDom,parents).each(function(i, el) {
            if($(this).data('med')==clickedListItemMed){
                index=i;
                return false;
            }
        });
        if (index >= 0) {
            openPhotoSwipe(index, clickedGallery);
        }
        return false;
    };
    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
            params = {};
        if (hash.length < 5) { // pid=1
            return params;
        }
        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if (!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');
            if (pair.length < 2) {
                continue;
            }
            params[pair[0]] = pair[1];
        }
        if (params.gid) {
            params.gid = parseInt(params.gid, 10);
        }
        return params;
    };
    var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
        if(!$('.pswp').length){
            var pswp_html='<div id="photoswipe-gallery" class="pswp" tabindex="-1" role="dialog">'
                    +'<div class="pswp__bg"></div>'
                    +'<div class="pswp__scroll-wrap">'
                        +'<div class="pswp__container">'
                            +'<div class="pswp__item"></div>'
                            +'<div class="pswp__item"></div>'
                            +'<div class="pswp__item"></div>'
                        +'</div>'
                        +'<div class="pswp__ui pswp__ui--hidden">'
                            +'<div class="pswp__top-bar">'
                                +'<div class="pswp__counter"></div>'
                                +'<button class="pswp__button pswp__button--close" title="退出画廊"></button>'
                                +'<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>'
                                +'<button class="pswp__button pswp__button--zoom" title="放大 / 缩小"></button>'
                                // +'<button class="pswp__button pswp__button--rotate-left" title="逆时针旋转"><i class="icon md-rotate-ccw"></i></button>'
                                // +'<button class="pswp__button pswp__button--rotate-right" title="顺时针旋转"><i class="icon md-rotate-cw"></i></button>'
                                +'<div class="pswp__preloader">'
                                    +'<div class="pswp__preloader__icn">'
                                        +'<div class="pswp__preloader__cut">'
                                            +'<div class="pswp__preloader__donut"></div>'
                                        +'</div>'
                                    +'</div>'
                                +'</div>'
                            +'</div>'
                             +'<div class="pswp__loading-indicator">'
                                +'<div class="pswp__loading-indicator__line"></div>'
                            +'</div>'
                            +'<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>'
                            +'<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>'
                            +'<div class="pswp__caption">'
                                +'<div class="pswp__caption__center"></div>'
                            +'</div>'
                        +'</div>'
                    +'</div>'
                +'</div>';
            $('body').append(pswp_html);
        }
        var pswpElement = document.querySelectorAll('.pswp')[0],
            gallery,
            options,
            items;
        items = parseThumbnailElements(galleryElement);
        // define options (if needed)
        options = {
            galleryUID: galleryElement.getAttribute('data-pswp-uid'),
            getThumbBoundsFn: function(index) {
                // See Options->getThumbBoundsFn section of docs for more info
                var thumbnail = items[index].el.children[0],
                    pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                    rect = thumbnail.getBoundingClientRect();
                return {
                    x: rect.left,
                    y: rect.top + pageYScroll,
                    w: rect.width
                };
            },
            addCaptionHTMLFn: function(item, captionEl, isFake) {
                if (!item.title) {
                    captionEl.children[0].innerText = '';
                    return false;
                }
                captionEl.children[0].innerHTML = item.title/* + '<br/><small>Photo: ' + item.author + '</small>'*/;
                return true;
            },
            closeOnScroll:false,
            tapToClose:true,
            tapToToggleControls:false,
            fullscreenEl:false,
            // captionEl:false,
            shareEl:false,
            errorMsg:'<div class="pswp__error-msg"><a href="%url%" target="_blank">图片</a> 加载失败</div>'
        };
        if (fromURL) {
            if (options.galleryPIDs) {
                // parse real index when custom PIDs are used
                // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                for (var j = 0; j < items.length; j++) {
                    if (items[j].pid == index) {
                        options.index = j;
                        break;
                    }
                }
            } else {
                options.index = parseInt(index, 10) - 1;
            }
        } else {
            options.index = parseInt(index, 10);
        }
        // exit if index not found
        if (isNaN(options.index)) {
            return;
        }
        if (disableAnimation) {
            options.showAnimationDuration = 0;
        }
        // Pass data to PhotoSwipe and initialize it
        gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
        // see: http://photoswipe.com/documentation/responsive-images.html
        var realViewportWidth,
            useLargeImages = false,
            firstResize = true,
            imageSrcWillChange;
        gallery.listen('beforeResize', function() {
            var dpiRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
            dpiRatio = Math.min(dpiRatio, 2.5);
            realViewportWidth = gallery.viewportSize.x * dpiRatio;
            if (realViewportWidth >= 1200 || (!gallery.likelyTouchDevice && realViewportWidth > 800) || screen.width > 1200) {
                if (!useLargeImages) {
                    useLargeImages = true;
                    imageSrcWillChange = true;
                }

            } else {
                if (useLargeImages) {
                    useLargeImages = false;
                    imageSrcWillChange = true;
                }
            }
            if (imageSrcWillChange && !firstResize) {
                gallery.invalidateCurrItems();
            }
            if (firstResize) {
                firstResize = false;
            }
            imageSrcWillChange = false;
        });
        gallery.listen('gettingData', function(index, item) {
            if (useLargeImages) {
                item.src = item.o.src;
                item.w = item.o.w;
                item.h = item.o.h;
            } else {
                item.src = item.m.src;
                item.w = item.m.w;
                item.h = item.m.h;
            }
        });
        gallery.init();
    };
    // select all gallery elements
    // var galleryElements = document.querySelectorAll(gallerySelector),
    var galleryElements = $(gallerySelector),
        medDom=medDom||'[data-med]';
    $(gallerySelector).each(function(index, el) {
        $(this).attr({'data-pswp-uid':index + 1}).click(function(e) {
            onThumbnailsClick(e,this);
        });
    });
    // Parse URL and open gallery if it contains #&pid=3&gid=1
    var hashData = photoswipeParseHash();
    if (hashData.pid && hashData.gid) {
        // openPhotoSwipe(hashData.pid, galleryElements[hashData.gid - 1], true, true);
        openPhotoSwipe(hashData.pid, galleryElements.eq(hashData.gid - 1), true, true);
    }
};
if(location.hash.indexOf('#&gid=')>=0 && location.hash.indexOf('&pid=')>=0) window.history.back();


/**
 * Swiper 3.3.1
 * Most modern mobile touch slider and framework with hardware accelerated transitions
 *
 * http://www.idangero.us/swiper/
 *
 * Copyright 2016, Vladimir Kharlampidi
 * The iDangero.us
 * http://www.idangero.us/
 *
 * Licensed under MIT
 *
 * Released on: February 7, 2016
 */
!function(){"use strict";function e(e){e.fn.swiper=function(a){var s;return e(this).each(function(){var e=new t(this,a);s||(s=e)}),s}}var a,t=function(e,s){function r(e){return Math.floor(e)}function i(){y.autoplayTimeoutId=setTimeout(function(){y.params.loop?(y.fixLoop(),y._slideNext(),y.emit("onAutoplay",y)):y.isEnd?s.autoplayStopOnLast?y.stopAutoplay():(y._slideTo(0),y.emit("onAutoplay",y)):(y._slideNext(),y.emit("onAutoplay",y))},y.params.autoplay)}function n(e,t){var s=a(e.target);if(!s.is(t))if("string"==typeof t)s=s.parents(t);else if(t.nodeType){var r;return s.parents().each(function(e,a){a===t&&(r=t)}),r?t:void 0}if(0!==s.length)return s[0]}function o(e,a){a=a||{};var t=window.MutationObserver||window.WebkitMutationObserver,s=new t(function(e){e.forEach(function(e){y.onResize(!0),y.emit("onObserverUpdate",y,e)})});s.observe(e,{attributes:"undefined"==typeof a.attributes?!0:a.attributes,childList:"undefined"==typeof a.childList?!0:a.childList,characterData:"undefined"==typeof a.characterData?!0:a.characterData}),y.observers.push(s)}function l(e){e.originalEvent&&(e=e.originalEvent);var a=e.keyCode||e.charCode;if(!y.params.allowSwipeToNext&&(y.isHorizontal()&&39===a||!y.isHorizontal()&&40===a))return!1;if(!y.params.allowSwipeToPrev&&(y.isHorizontal()&&37===a||!y.isHorizontal()&&38===a))return!1;if(!(e.shiftKey||e.altKey||e.ctrlKey||e.metaKey||document.activeElement&&document.activeElement.nodeName&&("input"===document.activeElement.nodeName.toLowerCase()||"textarea"===document.activeElement.nodeName.toLowerCase()))){if(37===a||39===a||38===a||40===a){var t=!1;if(y.container.parents(".swiper-slide").length>0&&0===y.container.parents(".swiper-slide-active").length)return;var s={left:window.pageXOffset,top:window.pageYOffset},r=window.innerWidth,i=window.innerHeight,n=y.container.offset();y.rtl&&(n.left=n.left-y.container[0].scrollLeft);for(var o=[[n.left,n.top],[n.left+y.width,n.top],[n.left,n.top+y.height],[n.left+y.width,n.top+y.height]],l=0;l<o.length;l++){var p=o[l];p[0]>=s.left&&p[0]<=s.left+r&&p[1]>=s.top&&p[1]<=s.top+i&&(t=!0)}if(!t)return}y.isHorizontal()?((37===a||39===a)&&(e.preventDefault?e.preventDefault():e.returnValue=!1),(39===a&&!y.rtl||37===a&&y.rtl)&&y.slideNext(),(37===a&&!y.rtl||39===a&&y.rtl)&&y.slidePrev()):((38===a||40===a)&&(e.preventDefault?e.preventDefault():e.returnValue=!1),40===a&&y.slideNext(),38===a&&y.slidePrev())}}function p(e){e.originalEvent&&(e=e.originalEvent);var a=y.mousewheel.event,t=0,s=y.rtl?-1:1;if("mousewheel"===a)if(y.params.mousewheelForceToAxis)if(y.isHorizontal()){if(!(Math.abs(e.wheelDeltaX)>Math.abs(e.wheelDeltaY)))return;t=e.wheelDeltaX*s}else{if(!(Math.abs(e.wheelDeltaY)>Math.abs(e.wheelDeltaX)))return;t=e.wheelDeltaY}else t=Math.abs(e.wheelDeltaX)>Math.abs(e.wheelDeltaY)?-e.wheelDeltaX*s:-e.wheelDeltaY;else if("DOMMouseScroll"===a)t=-e.detail;else if("wheel"===a)if(y.params.mousewheelForceToAxis)if(y.isHorizontal()){if(!(Math.abs(e.deltaX)>Math.abs(e.deltaY)))return;t=-e.deltaX*s}else{if(!(Math.abs(e.deltaY)>Math.abs(e.deltaX)))return;t=-e.deltaY}else t=Math.abs(e.deltaX)>Math.abs(e.deltaY)?-e.deltaX*s:-e.deltaY;if(0!==t){if(y.params.mousewheelInvert&&(t=-t),y.params.freeMode){var r=y.getWrapperTranslate()+t*y.params.mousewheelSensitivity,i=y.isBeginning,n=y.isEnd;if(r>=y.minTranslate()&&(r=y.minTranslate()),r<=y.maxTranslate()&&(r=y.maxTranslate()),y.setWrapperTransition(0),y.setWrapperTranslate(r),y.updateProgress(),y.updateActiveIndex(),(!i&&y.isBeginning||!n&&y.isEnd)&&y.updateClasses(),y.params.freeModeSticky?(clearTimeout(y.mousewheel.timeout),y.mousewheel.timeout=setTimeout(function(){y.slideReset()},300)):y.params.lazyLoading&&y.lazy&&y.lazy.load(),0===r||r===y.maxTranslate())return}else{if((new window.Date).getTime()-y.mousewheel.lastScrollTime>60)if(0>t)if(y.isEnd&&!y.params.loop||y.animating){if(y.params.mousewheelReleaseOnEdges)return!0}else y.slideNext();else if(y.isBeginning&&!y.params.loop||y.animating){if(y.params.mousewheelReleaseOnEdges)return!0}else y.slidePrev();y.mousewheel.lastScrollTime=(new window.Date).getTime()}return y.params.autoplay&&y.stopAutoplay(),e.preventDefault?e.preventDefault():e.returnValue=!1,!1}}function d(e,t){e=a(e);var s,r,i,n=y.rtl?-1:1;s=e.attr("data-swiper-parallax")||"0",r=e.attr("data-swiper-parallax-x"),i=e.attr("data-swiper-parallax-y"),r||i?(r=r||"0",i=i||"0"):y.isHorizontal()?(r=s,i="0"):(i=s,r="0"),r=r.indexOf("%")>=0?parseInt(r,10)*t*n+"%":r*t*n+"px",i=i.indexOf("%")>=0?parseInt(i,10)*t+"%":i*t+"px",e.transform("translate3d("+r+", "+i+",0px)")}function u(e){return 0!==e.indexOf("on")&&(e=e[0]!==e[0].toUpperCase()?"on"+e[0].toUpperCase()+e.substring(1):"on"+e),e}if(!(this instanceof t))return new t(e,s);var c={direction:"horizontal",touchEventsTarget:"container",initialSlide:0,speed:300,autoplay:!1,autoplayDisableOnInteraction:!0,autoplayStopOnLast:!1,iOSEdgeSwipeDetection:!1,iOSEdgeSwipeThreshold:20,freeMode:!1,freeModeMomentum:!0,freeModeMomentumRatio:1,freeModeMomentumBounce:!0,freeModeMomentumBounceRatio:1,freeModeSticky:!1,freeModeMinimumVelocity:.02,autoHeight:!1,setWrapperSize:!1,virtualTranslate:!1,effect:"slide",coverflow:{rotate:50,stretch:0,depth:100,modifier:1,slideShadows:!0},flip:{slideShadows:!0,limitRotation:!0},cube:{slideShadows:!0,shadow:!0,shadowOffset:20,shadowScale:.94},fade:{crossFade:!1},parallax:!1,scrollbar:null,scrollbarHide:!0,scrollbarDraggable:!1,scrollbarSnapOnRelease:!1,keyboardControl:!1,mousewheelControl:!1,mousewheelReleaseOnEdges:!1,mousewheelInvert:!1,mousewheelForceToAxis:!1,mousewheelSensitivity:1,hashnav:!1,breakpoints:void 0,spaceBetween:0,slidesPerView:1,slidesPerColumn:1,slidesPerColumnFill:"column",slidesPerGroup:1,centeredSlides:!1,slidesOffsetBefore:0,slidesOffsetAfter:0,roundLengths:!1,touchRatio:1,touchAngle:45,simulateTouch:!0,shortSwipes:!0,longSwipes:!0,longSwipesRatio:.5,longSwipesMs:300,followFinger:!0,onlyExternal:!1,threshold:0,touchMoveStopPropagation:!0,uniqueNavElements:!0,pagination:null,paginationElement:"span",paginationClickable:!1,paginationHide:!1,paginationBulletRender:null,paginationProgressRender:null,paginationFractionRender:null,paginationCustomRender:null,paginationType:"bullets",resistance:!0,resistanceRatio:.85,nextButton:null,prevButton:null,watchSlidesProgress:!1,watchSlidesVisibility:!1,grabCursor:!1,preventClicks:!0,preventClicksPropagation:!0,slideToClickedSlide:!1,lazyLoading:!1,lazyLoadingInPrevNext:!1,lazyLoadingInPrevNextAmount:1,lazyLoadingOnTransitionStart:!1,preloadImages:!0,updateOnImagesReady:!0,loop:!1,loopAdditionalSlides:0,loopedSlides:null,control:void 0,controlInverse:!1,controlBy:"slide",allowSwipeToPrev:!0,allowSwipeToNext:!0,swipeHandler:null,noSwiping:!0,noSwipingClass:"swiper-no-swiping",slideClass:"swiper-slide",slideActiveClass:"swiper-slide-active",slideVisibleClass:"swiper-slide-visible",slideDuplicateClass:"swiper-slide-duplicate",slideNextClass:"swiper-slide-next",slidePrevClass:"swiper-slide-prev",wrapperClass:"swiper-wrapper",bulletClass:"swiper-pagination-bullet",bulletActiveClass:"swiper-pagination-bullet-active",buttonDisabledClass:"swiper-button-disabled",paginationCurrentClass:"swiper-pagination-current",paginationTotalClass:"swiper-pagination-total",paginationHiddenClass:"swiper-pagination-hidden",paginationProgressbarClass:"swiper-pagination-progressbar",observer:!1,observeParents:!1,a11y:!1,prevSlideMessage:"Previous slide",nextSlideMessage:"Next slide",firstSlideMessage:"This is the first slide",lastSlideMessage:"This is the last slide",paginationBulletMessage:"Go to slide {{index}}",runCallbacksOnInit:!0},m=s&&s.virtualTranslate;s=s||{};var f={};for(var g in s)if("object"!=typeof s[g]||null===s[g]||(s[g].nodeType||s[g]===window||s[g]===document||"undefined"!=typeof Dom7&&s[g]instanceof Dom7||"undefined"!=typeof jQuery&&s[g]instanceof jQuery))f[g]=s[g];else{f[g]={};for(var h in s[g])f[g][h]=s[g][h]}for(var v in c)if("undefined"==typeof s[v])s[v]=c[v];else if("object"==typeof s[v])for(var w in c[v])"undefined"==typeof s[v][w]&&(s[v][w]=c[v][w]);var y=this;if(y.params=s,y.originalParams=f,y.classNames=[],"undefined"!=typeof a&&"undefined"!=typeof Dom7&&(a=Dom7),("undefined"!=typeof a||(a="undefined"==typeof Dom7?window.Dom7||window.Zepto||window.jQuery:Dom7))&&(y.$=a,y.currentBreakpoint=void 0,y.getActiveBreakpoint=function(){if(!y.params.breakpoints)return!1;var e,a=!1,t=[];for(e in y.params.breakpoints)y.params.breakpoints.hasOwnProperty(e)&&t.push(e);t.sort(function(e,a){return parseInt(e,10)>parseInt(a,10)});for(var s=0;s<t.length;s++)e=t[s],e>=window.innerWidth&&!a&&(a=e);return a||"max"},y.setBreakpoint=function(){var e=y.getActiveBreakpoint();if(e&&y.currentBreakpoint!==e){var a=e in y.params.breakpoints?y.params.breakpoints[e]:y.originalParams,t=y.params.loop&&a.slidesPerView!==y.params.slidesPerView;for(var s in a)y.params[s]=a[s];y.currentBreakpoint=e,t&&y.destroyLoop&&y.reLoop(!0)}},y.params.breakpoints&&y.setBreakpoint(),y.container=a(e),0!==y.container.length)){if(y.container.length>1){var b=[];return y.container.each(function(){b.push(new t(this,s))}),b}y.container[0].swiper=y,y.container.data("swiper",y),y.classNames.push("swiper-container-"+y.params.direction),y.params.freeMode&&y.classNames.push("swiper-container-free-mode"),y.support.flexbox||(y.classNames.push("swiper-container-no-flexbox"),y.params.slidesPerColumn=1),y.params.autoHeight&&y.classNames.push("swiper-container-autoheight"),(y.params.parallax||y.params.watchSlidesVisibility)&&(y.params.watchSlidesProgress=!0),["cube","coverflow","flip"].indexOf(y.params.effect)>=0&&(y.support.transforms3d?(y.params.watchSlidesProgress=!0,y.classNames.push("swiper-container-3d")):y.params.effect="slide"),"slide"!==y.params.effect&&y.classNames.push("swiper-container-"+y.params.effect),"cube"===y.params.effect&&(y.params.resistanceRatio=0,y.params.slidesPerView=1,y.params.slidesPerColumn=1,y.params.slidesPerGroup=1,y.params.centeredSlides=!1,y.params.spaceBetween=0,y.params.virtualTranslate=!0,y.params.setWrapperSize=!1),("fade"===y.params.effect||"flip"===y.params.effect)&&(y.params.slidesPerView=1,y.params.slidesPerColumn=1,y.params.slidesPerGroup=1,y.params.watchSlidesProgress=!0,y.params.spaceBetween=0,y.params.setWrapperSize=!1,"undefined"==typeof m&&(y.params.virtualTranslate=!0)),y.params.grabCursor&&y.support.touch&&(y.params.grabCursor=!1),y.wrapper=y.container.children("."+y.params.wrapperClass),y.params.pagination&&(y.paginationContainer=a(y.params.pagination),y.params.uniqueNavElements&&"string"==typeof y.params.pagination&&y.paginationContainer.length>1&&1===y.container.find(y.params.pagination).length&&(y.paginationContainer=y.container.find(y.params.pagination)),"bullets"===y.params.paginationType&&y.params.paginationClickable?y.paginationContainer.addClass("swiper-pagination-clickable"):y.params.paginationClickable=!1,y.paginationContainer.addClass("swiper-pagination-"+y.params.paginationType)),(y.params.nextButton||y.params.prevButton)&&(y.params.nextButton&&(y.nextButton=a(y.params.nextButton),y.params.uniqueNavElements&&"string"==typeof y.params.nextButton&&y.nextButton.length>1&&1===y.container.find(y.params.nextButton).length&&(y.nextButton=y.container.find(y.params.nextButton))),y.params.prevButton&&(y.prevButton=a(y.params.prevButton),y.params.uniqueNavElements&&"string"==typeof y.params.prevButton&&y.prevButton.length>1&&1===y.container.find(y.params.prevButton).length&&(y.prevButton=y.container.find(y.params.prevButton)))),y.isHorizontal=function(){return"horizontal"===y.params.direction},y.rtl=y.isHorizontal()&&("rtl"===y.container[0].dir.toLowerCase()||"rtl"===y.container.css("direction")),y.rtl&&y.classNames.push("swiper-container-rtl"),y.rtl&&(y.wrongRTL="-webkit-box"===y.wrapper.css("display")),y.params.slidesPerColumn>1&&y.classNames.push("swiper-container-multirow"),y.device.android&&y.classNames.push("swiper-container-android"),y.container.addClass(y.classNames.join(" ")),y.translate=0,y.progress=0,y.velocity=0,y.lockSwipeToNext=function(){y.params.allowSwipeToNext=!1},y.lockSwipeToPrev=function(){y.params.allowSwipeToPrev=!1},y.lockSwipes=function(){y.params.allowSwipeToNext=y.params.allowSwipeToPrev=!1},y.unlockSwipeToNext=function(){y.params.allowSwipeToNext=!0},y.unlockSwipeToPrev=function(){y.params.allowSwipeToPrev=!0},y.unlockSwipes=function(){y.params.allowSwipeToNext=y.params.allowSwipeToPrev=!0},y.params.grabCursor&&(y.container[0].style.cursor="move",y.container[0].style.cursor="-webkit-grab",y.container[0].style.cursor="-moz-grab",y.container[0].style.cursor="grab"),y.imagesToLoad=[],y.imagesLoaded=0,y.loadImage=function(e,a,t,s,r){function i(){r&&r()}var n;e.complete&&s?i():a?(n=new window.Image,n.onload=i,n.onerror=i,t&&(n.srcset=t),a&&(n.src=a)):i()},y.preloadImages=function(){function e(){"undefined"!=typeof y&&null!==y&&(void 0!==y.imagesLoaded&&y.imagesLoaded++,y.imagesLoaded===y.imagesToLoad.length&&(y.params.updateOnImagesReady&&y.update(),y.emit("onImagesReady",y)))}y.imagesToLoad=y.container.find("img");for(var a=0;a<y.imagesToLoad.length;a++)y.loadImage(y.imagesToLoad[a],y.imagesToLoad[a].currentSrc||y.imagesToLoad[a].getAttribute("src"),y.imagesToLoad[a].srcset||y.imagesToLoad[a].getAttribute("srcset"),!0,e)},y.autoplayTimeoutId=void 0,y.autoplaying=!1,y.autoplayPaused=!1,y.startAutoplay=function(){return"undefined"!=typeof y.autoplayTimeoutId?!1:y.params.autoplay?y.autoplaying?!1:(y.autoplaying=!0,y.emit("onAutoplayStart",y),void i()):!1},y.stopAutoplay=function(e){y.autoplayTimeoutId&&(y.autoplayTimeoutId&&clearTimeout(y.autoplayTimeoutId),y.autoplaying=!1,y.autoplayTimeoutId=void 0,y.emit("onAutoplayStop",y))},y.pauseAutoplay=function(e){y.autoplayPaused||(y.autoplayTimeoutId&&clearTimeout(y.autoplayTimeoutId),y.autoplayPaused=!0,0===e?(y.autoplayPaused=!1,i()):y.wrapper.transitionEnd(function(){y&&(y.autoplayPaused=!1,y.autoplaying?i():y.stopAutoplay())}))},y.minTranslate=function(){return-y.snapGrid[0]},y.maxTranslate=function(){return-y.snapGrid[y.snapGrid.length-1]},y.updateAutoHeight=function(){var e=y.slides.eq(y.activeIndex)[0];if("undefined"!=typeof e){var a=e.offsetHeight;a&&y.wrapper.css("height",a+"px")}},y.updateContainerSize=function(){var e,a;e="undefined"!=typeof y.params.width?y.params.width:y.container[0].clientWidth,a="undefined"!=typeof y.params.height?y.params.height:y.container[0].clientHeight,0===e&&y.isHorizontal()||0===a&&!y.isHorizontal()||(e=e-parseInt(y.container.css("padding-left"),10)-parseInt(y.container.css("padding-right"),10),a=a-parseInt(y.container.css("padding-top"),10)-parseInt(y.container.css("padding-bottom"),10),y.width=e,y.height=a,y.size=y.isHorizontal()?y.width:y.height)},y.updateSlidesSize=function(){y.slides=y.wrapper.children("."+y.params.slideClass),y.snapGrid=[],y.slidesGrid=[],y.slidesSizesGrid=[];var e,a=y.params.spaceBetween,t=-y.params.slidesOffsetBefore,s=0,i=0;if("undefined"!=typeof y.size){"string"==typeof a&&a.indexOf("%")>=0&&(a=parseFloat(a.replace("%",""))/100*y.size),y.virtualSize=-a,y.rtl?y.slides.css({marginLeft:"",marginTop:""}):y.slides.css({marginRight:"",marginBottom:""});var n;y.params.slidesPerColumn>1&&(n=Math.floor(y.slides.length/y.params.slidesPerColumn)===y.slides.length/y.params.slidesPerColumn?y.slides.length:Math.ceil(y.slides.length/y.params.slidesPerColumn)*y.params.slidesPerColumn,"auto"!==y.params.slidesPerView&&"row"===y.params.slidesPerColumnFill&&(n=Math.max(n,y.params.slidesPerView*y.params.slidesPerColumn)));var o,l=y.params.slidesPerColumn,p=n/l,d=p-(y.params.slidesPerColumn*p-y.slides.length);for(e=0;e<y.slides.length;e++){o=0;var u=y.slides.eq(e);if(y.params.slidesPerColumn>1){var c,m,f;"column"===y.params.slidesPerColumnFill?(m=Math.floor(e/l),f=e-m*l,(m>d||m===d&&f===l-1)&&++f>=l&&(f=0,m++),c=m+f*n/l,u.css({"-webkit-box-ordinal-group":c,"-moz-box-ordinal-group":c,"-ms-flex-order":c,"-webkit-order":c,order:c})):(f=Math.floor(e/p),m=e-f*p),u.css({"margin-top":0!==f&&y.params.spaceBetween&&y.params.spaceBetween+"px"}).attr("data-swiper-column",m).attr("data-swiper-row",f)}"none"!==u.css("display")&&("auto"===y.params.slidesPerView?(o=y.isHorizontal()?u.outerWidth(!0):u.outerHeight(!0),y.params.roundLengths&&(o=r(o))):(o=(y.size-(y.params.slidesPerView-1)*a)/y.params.slidesPerView,y.params.roundLengths&&(o=r(o)),y.isHorizontal()?y.slides[e].style.width=o+"px":y.slides[e].style.height=o+"px"),y.slides[e].swiperSlideSize=o,y.slidesSizesGrid.push(o),y.params.centeredSlides?(t=t+o/2+s/2+a,0===e&&(t=t-y.size/2-a),Math.abs(t)<.001&&(t=0),i%y.params.slidesPerGroup===0&&y.snapGrid.push(t),y.slidesGrid.push(t)):(i%y.params.slidesPerGroup===0&&y.snapGrid.push(t),y.slidesGrid.push(t),t=t+o+a),y.virtualSize+=o+a,s=o,i++)}y.virtualSize=Math.max(y.virtualSize,y.size)+y.params.slidesOffsetAfter;var g;if(y.rtl&&y.wrongRTL&&("slide"===y.params.effect||"coverflow"===y.params.effect)&&y.wrapper.css({width:y.virtualSize+y.params.spaceBetween+"px"}),(!y.support.flexbox||y.params.setWrapperSize)&&(y.isHorizontal()?y.wrapper.css({width:y.virtualSize+y.params.spaceBetween+"px"}):y.wrapper.css({height:y.virtualSize+y.params.spaceBetween+"px"})),y.params.slidesPerColumn>1&&(y.virtualSize=(o+y.params.spaceBetween)*n,y.virtualSize=Math.ceil(y.virtualSize/y.params.slidesPerColumn)-y.params.spaceBetween,y.wrapper.css({width:y.virtualSize+y.params.spaceBetween+"px"}),y.params.centeredSlides)){for(g=[],e=0;e<y.snapGrid.length;e++)y.snapGrid[e]<y.virtualSize+y.snapGrid[0]&&g.push(y.snapGrid[e]);y.snapGrid=g}if(!y.params.centeredSlides){for(g=[],e=0;e<y.snapGrid.length;e++)y.snapGrid[e]<=y.virtualSize-y.size&&g.push(y.snapGrid[e]);y.snapGrid=g,Math.floor(y.virtualSize-y.size)-Math.floor(y.snapGrid[y.snapGrid.length-1])>1&&y.snapGrid.push(y.virtualSize-y.size)}0===y.snapGrid.length&&(y.snapGrid=[0]),0!==y.params.spaceBetween&&(y.isHorizontal()?y.rtl?y.slides.css({marginLeft:a+"px"}):y.slides.css({marginRight:a+"px"}):y.slides.css({marginBottom:a+"px"})),y.params.watchSlidesProgress&&y.updateSlidesOffset()}},y.updateSlidesOffset=function(){for(var e=0;e<y.slides.length;e++)y.slides[e].swiperSlideOffset=y.isHorizontal()?y.slides[e].offsetLeft:y.slides[e].offsetTop},y.updateSlidesProgress=function(e){if("undefined"==typeof e&&(e=y.translate||0),0!==y.slides.length){"undefined"==typeof y.slides[0].swiperSlideOffset&&y.updateSlidesOffset();var a=-e;y.rtl&&(a=e),y.slides.removeClass(y.params.slideVisibleClass);for(var t=0;t<y.slides.length;t++){var s=y.slides[t],r=(a-s.swiperSlideOffset)/(s.swiperSlideSize+y.params.spaceBetween);if(y.params.watchSlidesVisibility){var i=-(a-s.swiperSlideOffset),n=i+y.slidesSizesGrid[t],o=i>=0&&i<y.size||n>0&&n<=y.size||0>=i&&n>=y.size;o&&y.slides.eq(t).addClass(y.params.slideVisibleClass)}s.progress=y.rtl?-r:r}}},y.updateProgress=function(e){"undefined"==typeof e&&(e=y.translate||0);var a=y.maxTranslate()-y.minTranslate(),t=y.isBeginning,s=y.isEnd;0===a?(y.progress=0,y.isBeginning=y.isEnd=!0):(y.progress=(e-y.minTranslate())/a,y.isBeginning=y.progress<=0,y.isEnd=y.progress>=1),y.isBeginning&&!t&&y.emit("onReachBeginning",y),y.isEnd&&!s&&y.emit("onReachEnd",y),y.params.watchSlidesProgress&&y.updateSlidesProgress(e),y.emit("onProgress",y,y.progress)},y.updateActiveIndex=function(){var e,a,t,s=y.rtl?y.translate:-y.translate;for(a=0;a<y.slidesGrid.length;a++)"undefined"!=typeof y.slidesGrid[a+1]?s>=y.slidesGrid[a]&&s<y.slidesGrid[a+1]-(y.slidesGrid[a+1]-y.slidesGrid[a])/2?e=a:s>=y.slidesGrid[a]&&s<y.slidesGrid[a+1]&&(e=a+1):s>=y.slidesGrid[a]&&(e=a);(0>e||"undefined"==typeof e)&&(e=0),t=Math.floor(e/y.params.slidesPerGroup),t>=y.snapGrid.length&&(t=y.snapGrid.length-1),e!==y.activeIndex&&(y.snapIndex=t,y.previousIndex=y.activeIndex,y.activeIndex=e,y.updateClasses())},y.updateClasses=function(){y.slides.removeClass(y.params.slideActiveClass+" "+y.params.slideNextClass+" "+y.params.slidePrevClass);var e=y.slides.eq(y.activeIndex);e.addClass(y.params.slideActiveClass);var t=e.next("."+y.params.slideClass).addClass(y.params.slideNextClass);y.params.loop&&0===t.length&&y.slides.eq(0).addClass(y.params.slideNextClass);var s=e.prev("."+y.params.slideClass).addClass(y.params.slidePrevClass);if(y.params.loop&&0===s.length&&y.slides.eq(-1).addClass(y.params.slidePrevClass),y.paginationContainer&&y.paginationContainer.length>0){var r,i=y.params.loop?Math.ceil((y.slides.length-2*y.loopedSlides)/y.params.slidesPerGroup):y.snapGrid.length;if(y.params.loop?(r=Math.ceil((y.activeIndex-y.loopedSlides)/y.params.slidesPerGroup),r>y.slides.length-1-2*y.loopedSlides&&(r-=y.slides.length-2*y.loopedSlides),r>i-1&&(r-=i),0>r&&"bullets"!==y.params.paginationType&&(r=i+r)):r="undefined"!=typeof y.snapIndex?y.snapIndex:y.activeIndex||0,"bullets"===y.params.paginationType&&y.bullets&&y.bullets.length>0&&(y.bullets.removeClass(y.params.bulletActiveClass),y.paginationContainer.length>1?y.bullets.each(function(){a(this).index()===r&&a(this).addClass(y.params.bulletActiveClass)}):y.bullets.eq(r).addClass(y.params.bulletActiveClass)),"fraction"===y.params.paginationType&&(y.paginationContainer.find("."+y.params.paginationCurrentClass).text(r+1),y.paginationContainer.find("."+y.params.paginationTotalClass).text(i)),"progress"===y.params.paginationType){var n=(r+1)/i,o=n,l=1;y.isHorizontal()||(l=n,o=1),y.paginationContainer.find("."+y.params.paginationProgressbarClass).transform("translate3d(0,0,0) scaleX("+o+") scaleY("+l+")").transition(y.params.speed)}"custom"===y.params.paginationType&&y.params.paginationCustomRender&&(y.paginationContainer.html(y.params.paginationCustomRender(y,r+1,i)),y.emit("onPaginationRendered",y,y.paginationContainer[0]))}y.params.loop||(y.params.prevButton&&y.prevButton&&y.prevButton.length>0&&(y.isBeginning?(y.prevButton.addClass(y.params.buttonDisabledClass),y.params.a11y&&y.a11y&&y.a11y.disable(y.prevButton)):(y.prevButton.removeClass(y.params.buttonDisabledClass),y.params.a11y&&y.a11y&&y.a11y.enable(y.prevButton))),y.params.nextButton&&y.nextButton&&y.nextButton.length>0&&(y.isEnd?(y.nextButton.addClass(y.params.buttonDisabledClass),y.params.a11y&&y.a11y&&y.a11y.disable(y.nextButton)):(y.nextButton.removeClass(y.params.buttonDisabledClass),y.params.a11y&&y.a11y&&y.a11y.enable(y.nextButton))))},y.updatePagination=function(){if(y.params.pagination&&y.paginationContainer&&y.paginationContainer.length>0){var e="";if("bullets"===y.params.paginationType){for(var a=y.params.loop?Math.ceil((y.slides.length-2*y.loopedSlides)/y.params.slidesPerGroup):y.snapGrid.length,t=0;a>t;t++)e+=y.params.paginationBulletRender?y.params.paginationBulletRender(t,y.params.bulletClass):"<"+y.params.paginationElement+' class="'+y.params.bulletClass+'"></'+y.params.paginationElement+">";y.paginationContainer.html(e),y.bullets=y.paginationContainer.find("."+y.params.bulletClass),y.params.paginationClickable&&y.params.a11y&&y.a11y&&y.a11y.initPagination()}"fraction"===y.params.paginationType&&(e=y.params.paginationFractionRender?y.params.paginationFractionRender(y,y.params.paginationCurrentClass,y.params.paginationTotalClass):'<span class="'+y.params.paginationCurrentClass+'"></span> / <span class="'+y.params.paginationTotalClass+'"></span>',y.paginationContainer.html(e)),"progress"===y.params.paginationType&&(e=y.params.paginationProgressRender?y.params.paginationProgressRender(y,y.params.paginationProgressbarClass):'<span class="'+y.params.paginationProgressbarClass+'"></span>',y.paginationContainer.html(e)),"custom"!==y.params.paginationType&&y.emit("onPaginationRendered",y,y.paginationContainer[0])}},y.update=function(e){function a(){s=Math.min(Math.max(y.translate,y.maxTranslate()),y.minTranslate()),y.setWrapperTranslate(s),y.updateActiveIndex(),y.updateClasses()}if(y.updateContainerSize(),y.updateSlidesSize(),y.updateProgress(),y.updatePagination(),y.updateClasses(),y.params.scrollbar&&y.scrollbar&&y.scrollbar.set(),e){var t,s;y.controller&&y.controller.spline&&(y.controller.spline=void 0),y.params.freeMode?(a(),y.params.autoHeight&&y.updateAutoHeight()):(t=("auto"===y.params.slidesPerView||y.params.slidesPerView>1)&&y.isEnd&&!y.params.centeredSlides?y.slideTo(y.slides.length-1,0,!1,!0):y.slideTo(y.activeIndex,0,!1,!0),t||a())}else y.params.autoHeight&&y.updateAutoHeight()},y.onResize=function(e){y.params.breakpoints&&y.setBreakpoint();var a=y.params.allowSwipeToPrev,t=y.params.allowSwipeToNext;y.params.allowSwipeToPrev=y.params.allowSwipeToNext=!0,y.updateContainerSize(),y.updateSlidesSize(),("auto"===y.params.slidesPerView||y.params.freeMode||e)&&y.updatePagination(),y.params.scrollbar&&y.scrollbar&&y.scrollbar.set(),y.controller&&y.controller.spline&&(y.controller.spline=void 0);var s=!1;if(y.params.freeMode){var r=Math.min(Math.max(y.translate,y.maxTranslate()),y.minTranslate());y.setWrapperTranslate(r),y.updateActiveIndex(),y.updateClasses(),y.params.autoHeight&&y.updateAutoHeight()}else y.updateClasses(),s=("auto"===y.params.slidesPerView||y.params.slidesPerView>1)&&y.isEnd&&!y.params.centeredSlides?y.slideTo(y.slides.length-1,0,!1,!0):y.slideTo(y.activeIndex,0,!1,!0);y.params.lazyLoading&&!s&&y.lazy&&y.lazy.load(),y.params.allowSwipeToPrev=a,y.params.allowSwipeToNext=t};var x=["mousedown","mousemove","mouseup"];window.navigator.pointerEnabled?x=["pointerdown","pointermove","pointerup"]:window.navigator.msPointerEnabled&&(x=["MSPointerDown","MSPointerMove","MSPointerUp"]),y.touchEvents={start:y.support.touch||!y.params.simulateTouch?"touchstart":x[0],move:y.support.touch||!y.params.simulateTouch?"touchmove":x[1],end:y.support.touch||!y.params.simulateTouch?"touchend":x[2]},(window.navigator.pointerEnabled||window.navigator.msPointerEnabled)&&("container"===y.params.touchEventsTarget?y.container:y.wrapper).addClass("swiper-wp8-"+y.params.direction),y.initEvents=function(e){var a=e?"off":"on",t=e?"removeEventListener":"addEventListener",r="container"===y.params.touchEventsTarget?y.container[0]:y.wrapper[0],i=y.support.touch?r:document,n=y.params.nested?!0:!1;y.browser.ie?(r[t](y.touchEvents.start,y.onTouchStart,!1),i[t](y.touchEvents.move,y.onTouchMove,n),i[t](y.touchEvents.end,y.onTouchEnd,!1)):(y.support.touch&&(r[t](y.touchEvents.start,y.onTouchStart,!1),r[t](y.touchEvents.move,y.onTouchMove,n),r[t](y.touchEvents.end,y.onTouchEnd,!1)),!s.simulateTouch||y.device.ios||y.device.android||(r[t]("mousedown",y.onTouchStart,!1),document[t]("mousemove",y.onTouchMove,n),document[t]("mouseup",y.onTouchEnd,!1))),window[t]("resize",y.onResize),y.params.nextButton&&y.nextButton&&y.nextButton.length>0&&(y.nextButton[a]("click",y.onClickNext),y.params.a11y&&y.a11y&&y.nextButton[a]("keydown",y.a11y.onEnterKey)),y.params.prevButton&&y.prevButton&&y.prevButton.length>0&&(y.prevButton[a]("click",y.onClickPrev),y.params.a11y&&y.a11y&&y.prevButton[a]("keydown",y.a11y.onEnterKey)),y.params.pagination&&y.params.paginationClickable&&(y.paginationContainer[a]("click","."+y.params.bulletClass,y.onClickIndex),y.params.a11y&&y.a11y&&y.paginationContainer[a]("keydown","."+y.params.bulletClass,y.a11y.onEnterKey)),(y.params.preventClicks||y.params.preventClicksPropagation)&&r[t]("click",y.preventClicks,!0)},y.attachEvents=function(){y.initEvents()},y.detachEvents=function(){y.initEvents(!0)},y.allowClick=!0,y.preventClicks=function(e){y.allowClick||(y.params.preventClicks&&e.preventDefault(),y.params.preventClicksPropagation&&y.animating&&(e.stopPropagation(),e.stopImmediatePropagation()))},y.onClickNext=function(e){e.preventDefault(),(!y.isEnd||y.params.loop)&&y.slideNext()},y.onClickPrev=function(e){e.preventDefault(),(!y.isBeginning||y.params.loop)&&y.slidePrev()},y.onClickIndex=function(e){e.preventDefault();var t=a(this).index()*y.params.slidesPerGroup;y.params.loop&&(t+=y.loopedSlides),y.slideTo(t)},y.updateClickedSlide=function(e){var t=n(e,"."+y.params.slideClass),s=!1;if(t)for(var r=0;r<y.slides.length;r++)y.slides[r]===t&&(s=!0);if(!t||!s)return y.clickedSlide=void 0,void(y.clickedIndex=void 0);if(y.clickedSlide=t,y.clickedIndex=a(t).index(),y.params.slideToClickedSlide&&void 0!==y.clickedIndex&&y.clickedIndex!==y.activeIndex){var i,o=y.clickedIndex;if(y.params.loop){if(y.animating)return;i=a(y.clickedSlide).attr("data-swiper-slide-index"),y.params.centeredSlides?o<y.loopedSlides-y.params.slidesPerView/2||o>y.slides.length-y.loopedSlides+y.params.slidesPerView/2?(y.fixLoop(),o=y.wrapper.children("."+y.params.slideClass+'[data-swiper-slide-index="'+i+'"]:not(.swiper-slide-duplicate)').eq(0).index(),setTimeout(function(){y.slideTo(o)},0)):y.slideTo(o):o>y.slides.length-y.params.slidesPerView?(y.fixLoop(),o=y.wrapper.children("."+y.params.slideClass+'[data-swiper-slide-index="'+i+'"]:not(.swiper-slide-duplicate)').eq(0).index(),setTimeout(function(){y.slideTo(o)},0)):y.slideTo(o)}else y.slideTo(o)}};var T,S,C,z,M,P,I,k,E,B,D="input, select, textarea, button",L=Date.now(),H=[];y.animating=!1,y.touches={startX:0,startY:0,currentX:0,currentY:0,diff:0};var G,A;if(y.onTouchStart=function(e){if(e.originalEvent&&(e=e.originalEvent),G="touchstart"===e.type,G||!("which"in e)||3!==e.which){if(y.params.noSwiping&&n(e,"."+y.params.noSwipingClass))return void(y.allowClick=!0);if(!y.params.swipeHandler||n(e,y.params.swipeHandler)){var t=y.touches.currentX="touchstart"===e.type?e.targetTouches[0].pageX:e.pageX,s=y.touches.currentY="touchstart"===e.type?e.targetTouches[0].pageY:e.pageY;if(!(y.device.ios&&y.params.iOSEdgeSwipeDetection&&t<=y.params.iOSEdgeSwipeThreshold)){if(T=!0,S=!1,C=!0,M=void 0,A=void 0,y.touches.startX=t,y.touches.startY=s,z=Date.now(),y.allowClick=!0,y.updateContainerSize(),y.swipeDirection=void 0,y.params.threshold>0&&(k=!1),"touchstart"!==e.type){var r=!0;a(e.target).is(D)&&(r=!1),document.activeElement&&a(document.activeElement).is(D)&&document.activeElement.blur(),r&&e.preventDefault()}y.emit("onTouchStart",y,e)}}}},y.onTouchMove=function(e){if(e.originalEvent&&(e=e.originalEvent),!G||"mousemove"!==e.type){if(e.preventedByNestedSwiper)return y.touches.startX="touchmove"===e.type?e.targetTouches[0].pageX:e.pageX,void(y.touches.startY="touchmove"===e.type?e.targetTouches[0].pageY:e.pageY);if(y.params.onlyExternal)return y.allowClick=!1,void(T&&(y.touches.startX=y.touches.currentX="touchmove"===e.type?e.targetTouches[0].pageX:e.pageX,y.touches.startY=y.touches.currentY="touchmove"===e.type?e.targetTouches[0].pageY:e.pageY,z=Date.now()));if(G&&document.activeElement&&e.target===document.activeElement&&a(e.target).is(D))return S=!0,void(y.allowClick=!1);if(C&&y.emit("onTouchMove",y,e),!(e.targetTouches&&e.targetTouches.length>1)){if(y.touches.currentX="touchmove"===e.type?e.targetTouches[0].pageX:e.pageX,y.touches.currentY="touchmove"===e.type?e.targetTouches[0].pageY:e.pageY,"undefined"==typeof M){var t=180*Math.atan2(Math.abs(y.touches.currentY-y.touches.startY),Math.abs(y.touches.currentX-y.touches.startX))/Math.PI;M=y.isHorizontal()?t>y.params.touchAngle:90-t>y.params.touchAngle}if(M&&y.emit("onTouchMoveOpposite",y,e),"undefined"==typeof A&&y.browser.ieTouch&&(y.touches.currentX!==y.touches.startX||y.touches.currentY!==y.touches.startY)&&(A=!0),T){if(M)return void(T=!1);if(A||!y.browser.ieTouch){y.allowClick=!1,y.emit("onSliderMove",y,e),e.preventDefault(),y.params.touchMoveStopPropagation&&!y.params.nested&&e.stopPropagation(),S||(s.loop&&y.fixLoop(),I=y.getWrapperTranslate(),y.setWrapperTransition(0),y.animating&&y.wrapper.trigger("webkitTransitionEnd transitionend oTransitionEnd MSTransitionEnd msTransitionEnd"),y.params.autoplay&&y.autoplaying&&(y.params.autoplayDisableOnInteraction?y.stopAutoplay():y.pauseAutoplay()),B=!1,y.params.grabCursor&&(y.container[0].style.cursor="move",y.container[0].style.cursor="-webkit-grabbing",y.container[0].style.cursor="-moz-grabbin",y.container[0].style.cursor="grabbing")),S=!0;var r=y.touches.diff=y.isHorizontal()?y.touches.currentX-y.touches.startX:y.touches.currentY-y.touches.startY;r*=y.params.touchRatio,y.rtl&&(r=-r),y.swipeDirection=r>0?"prev":"next",P=r+I;var i=!0;if(r>0&&P>y.minTranslate()?(i=!1,y.params.resistance&&(P=y.minTranslate()-1+Math.pow(-y.minTranslate()+I+r,y.params.resistanceRatio))):0>r&&P<y.maxTranslate()&&(i=!1,y.params.resistance&&(P=y.maxTranslate()+1-Math.pow(y.maxTranslate()-I-r,y.params.resistanceRatio))),i&&(e.preventedByNestedSwiper=!0),!y.params.allowSwipeToNext&&"next"===y.swipeDirection&&I>P&&(P=I),!y.params.allowSwipeToPrev&&"prev"===y.swipeDirection&&P>I&&(P=I),y.params.followFinger){if(y.params.threshold>0){if(!(Math.abs(r)>y.params.threshold||k))return void(P=I);if(!k)return k=!0,y.touches.startX=y.touches.currentX,y.touches.startY=y.touches.currentY,P=I,void(y.touches.diff=y.isHorizontal()?y.touches.currentX-y.touches.startX:y.touches.currentY-y.touches.startY)}(y.params.freeMode||y.params.watchSlidesProgress)&&y.updateActiveIndex(),y.params.freeMode&&(0===H.length&&H.push({position:y.touches[y.isHorizontal()?"startX":"startY"],time:z}),H.push({position:y.touches[y.isHorizontal()?"currentX":"currentY"],time:(new window.Date).getTime()})),y.updateProgress(P),y.setWrapperTranslate(P)}}}}}},y.onTouchEnd=function(e){if(e.originalEvent&&(e=e.originalEvent),C&&y.emit("onTouchEnd",y,e),C=!1,T){y.params.grabCursor&&S&&T&&(y.container[0].style.cursor="move",y.container[0].style.cursor="-webkit-grab",y.container[0].style.cursor="-moz-grab",y.container[0].style.cursor="grab");var t=Date.now(),s=t-z;if(y.allowClick&&(y.updateClickedSlide(e),y.emit("onTap",y,e),300>s&&t-L>300&&(E&&clearTimeout(E),E=setTimeout(function(){y&&(y.params.paginationHide&&y.paginationContainer.length>0&&!a(e.target).hasClass(y.params.bulletClass)&&y.paginationContainer.toggleClass(y.params.paginationHiddenClass),y.emit("onClick",y,e))},300)),300>s&&300>t-L&&(E&&clearTimeout(E),y.emit("onDoubleTap",y,e))),L=Date.now(),setTimeout(function(){y&&(y.allowClick=!0)},0),!T||!S||!y.swipeDirection||0===y.touches.diff||P===I)return void(T=S=!1);T=S=!1;var r;if(r=y.params.followFinger?y.rtl?y.translate:-y.translate:-P,y.params.freeMode){if(r<-y.minTranslate())return void y.slideTo(y.activeIndex);if(r>-y.maxTranslate())return void(y.slides.length<y.snapGrid.length?y.slideTo(y.snapGrid.length-1):y.slideTo(y.slides.length-1));if(y.params.freeModeMomentum){if(H.length>1){var i=H.pop(),n=H.pop(),o=i.position-n.position,l=i.time-n.time;y.velocity=o/l,y.velocity=y.velocity/2,Math.abs(y.velocity)<y.params.freeModeMinimumVelocity&&(y.velocity=0),(l>150||(new window.Date).getTime()-i.time>300)&&(y.velocity=0)}else y.velocity=0;H.length=0;var p=1e3*y.params.freeModeMomentumRatio,d=y.velocity*p,u=y.translate+d;y.rtl&&(u=-u);var c,m=!1,f=20*Math.abs(y.velocity)*y.params.freeModeMomentumBounceRatio;if(u<y.maxTranslate())y.params.freeModeMomentumBounce?(u+y.maxTranslate()<-f&&(u=y.maxTranslate()-f),c=y.maxTranslate(),m=!0,B=!0):u=y.maxTranslate();else if(u>y.minTranslate())y.params.freeModeMomentumBounce?(u-y.minTranslate()>f&&(u=y.minTranslate()+f),c=y.minTranslate(),m=!0,B=!0):u=y.minTranslate();else if(y.params.freeModeSticky){var g,h=0;for(h=0;h<y.snapGrid.length;h+=1)if(y.snapGrid[h]>-u){g=h;break}u=Math.abs(y.snapGrid[g]-u)<Math.abs(y.snapGrid[g-1]-u)||"next"===y.swipeDirection?y.snapGrid[g]:y.snapGrid[g-1],y.rtl||(u=-u)}if(0!==y.velocity)p=y.rtl?Math.abs((-u-y.translate)/y.velocity):Math.abs((u-y.translate)/y.velocity);else if(y.params.freeModeSticky)return void y.slideReset();y.params.freeModeMomentumBounce&&m?(y.updateProgress(c),y.setWrapperTransition(p),y.setWrapperTranslate(u),y.onTransitionStart(),y.animating=!0,y.wrapper.transitionEnd(function(){y&&B&&(y.emit("onMomentumBounce",y),y.setWrapperTransition(y.params.speed),y.setWrapperTranslate(c),y.wrapper.transitionEnd(function(){y&&y.onTransitionEnd()}))})):y.velocity?(y.updateProgress(u),y.setWrapperTransition(p),y.setWrapperTranslate(u),y.onTransitionStart(),y.animating||(y.animating=!0,y.wrapper.transitionEnd(function(){y&&y.onTransitionEnd()}))):y.updateProgress(u),y.updateActiveIndex()}return void((!y.params.freeModeMomentum||s>=y.params.longSwipesMs)&&(y.updateProgress(),y.updateActiveIndex()))}var v,w=0,b=y.slidesSizesGrid[0];for(v=0;v<y.slidesGrid.length;v+=y.params.slidesPerGroup)"undefined"!=typeof y.slidesGrid[v+y.params.slidesPerGroup]?r>=y.slidesGrid[v]&&r<y.slidesGrid[v+y.params.slidesPerGroup]&&(w=v,b=y.slidesGrid[v+y.params.slidesPerGroup]-y.slidesGrid[v]):r>=y.slidesGrid[v]&&(w=v,b=y.slidesGrid[y.slidesGrid.length-1]-y.slidesGrid[y.slidesGrid.length-2]);var x=(r-y.slidesGrid[w])/b;if(s>y.params.longSwipesMs){if(!y.params.longSwipes)return void y.slideTo(y.activeIndex);"next"===y.swipeDirection&&(x>=y.params.longSwipesRatio?y.slideTo(w+y.params.slidesPerGroup):y.slideTo(w)),"prev"===y.swipeDirection&&(x>1-y.params.longSwipesRatio?y.slideTo(w+y.params.slidesPerGroup):y.slideTo(w))}else{if(!y.params.shortSwipes)return void y.slideTo(y.activeIndex);"next"===y.swipeDirection&&y.slideTo(w+y.params.slidesPerGroup),"prev"===y.swipeDirection&&y.slideTo(w)}}},y._slideTo=function(e,a){return y.slideTo(e,a,!0,!0)},y.slideTo=function(e,a,t,s){"undefined"==typeof t&&(t=!0),"undefined"==typeof e&&(e=0),0>e&&(e=0),y.snapIndex=Math.floor(e/y.params.slidesPerGroup),y.snapIndex>=y.snapGrid.length&&(y.snapIndex=y.snapGrid.length-1);var r=-y.snapGrid[y.snapIndex];y.params.autoplay&&y.autoplaying&&(s||!y.params.autoplayDisableOnInteraction?y.pauseAutoplay(a):y.stopAutoplay()),y.updateProgress(r);for(var i=0;i<y.slidesGrid.length;i++)-Math.floor(100*r)>=Math.floor(100*y.slidesGrid[i])&&(e=i);return!y.params.allowSwipeToNext&&r<y.translate&&r<y.minTranslate()?!1:!y.params.allowSwipeToPrev&&r>y.translate&&r>y.maxTranslate()&&(y.activeIndex||0)!==e?!1:("undefined"==typeof a&&(a=y.params.speed),y.previousIndex=y.activeIndex||0,y.activeIndex=e,y.rtl&&-r===y.translate||!y.rtl&&r===y.translate?(y.params.autoHeight&&y.updateAutoHeight(),y.updateClasses(),"slide"!==y.params.effect&&y.setWrapperTranslate(r),!1):(y.updateClasses(),y.onTransitionStart(t),0===a?(y.setWrapperTranslate(r),y.setWrapperTransition(0),y.onTransitionEnd(t)):(y.setWrapperTranslate(r),y.setWrapperTransition(a),y.animating||(y.animating=!0,y.wrapper.transitionEnd(function(){y&&y.onTransitionEnd(t)}))),!0))},y.onTransitionStart=function(e){"undefined"==typeof e&&(e=!0),y.params.autoHeight&&y.updateAutoHeight(),y.lazy&&y.lazy.onTransitionStart(),e&&(y.emit("onTransitionStart",y),y.activeIndex!==y.previousIndex&&(y.emit("onSlideChangeStart",y),y.activeIndex>y.previousIndex?y.emit("onSlideNextStart",y):y.emit("onSlidePrevStart",y)))},y.onTransitionEnd=function(e){y.animating=!1,y.setWrapperTransition(0),"undefined"==typeof e&&(e=!0),y.lazy&&y.lazy.onTransitionEnd(),e&&(y.emit("onTransitionEnd",y),y.activeIndex!==y.previousIndex&&(y.emit("onSlideChangeEnd",y),y.activeIndex>y.previousIndex?y.emit("onSlideNextEnd",y):y.emit("onSlidePrevEnd",y))),y.params.hashnav&&y.hashnav&&y.hashnav.setHash()},y.slideNext=function(e,a,t){if(y.params.loop){if(y.animating)return!1;y.fixLoop();y.container[0].clientLeft;return y.slideTo(y.activeIndex+y.params.slidesPerGroup,a,e,t)}return y.slideTo(y.activeIndex+y.params.slidesPerGroup,a,e,t)},y._slideNext=function(e){return y.slideNext(!0,e,!0)},y.slidePrev=function(e,a,t){if(y.params.loop){if(y.animating)return!1;y.fixLoop();y.container[0].clientLeft;return y.slideTo(y.activeIndex-1,a,e,t)}return y.slideTo(y.activeIndex-1,a,e,t)},y._slidePrev=function(e){return y.slidePrev(!0,e,!0)},y.slideReset=function(e,a,t){return y.slideTo(y.activeIndex,a,e)},y.setWrapperTransition=function(e,a){y.wrapper.transition(e),"slide"!==y.params.effect&&y.effects[y.params.effect]&&y.effects[y.params.effect].setTransition(e),y.params.parallax&&y.parallax&&y.parallax.setTransition(e),y.params.scrollbar&&y.scrollbar&&y.scrollbar.setTransition(e),y.params.control&&y.controller&&y.controller.setTransition(e,a),y.emit("onSetTransition",y,e)},y.setWrapperTranslate=function(e,a,t){var s=0,i=0,n=0;y.isHorizontal()?s=y.rtl?-e:e:i=e,y.params.roundLengths&&(s=r(s),i=r(i)),y.params.virtualTranslate||(y.support.transforms3d?y.wrapper.transform("translate3d("+s+"px, "+i+"px, "+n+"px)"):y.wrapper.transform("translate("+s+"px, "+i+"px)")),y.translate=y.isHorizontal()?s:i;var o,l=y.maxTranslate()-y.minTranslate();o=0===l?0:(e-y.minTranslate())/l,o!==y.progress&&y.updateProgress(e),a&&y.updateActiveIndex(),"slide"!==y.params.effect&&y.effects[y.params.effect]&&y.effects[y.params.effect].setTranslate(y.translate),y.params.parallax&&y.parallax&&y.parallax.setTranslate(y.translate),y.params.scrollbar&&y.scrollbar&&y.scrollbar.setTranslate(y.translate),y.params.control&&y.controller&&y.controller.setTranslate(y.translate,t),y.emit("onSetTranslate",y,y.translate)},y.getTranslate=function(e,a){var t,s,r,i;return"undefined"==typeof a&&(a="x"),y.params.virtualTranslate?y.rtl?-y.translate:y.translate:(r=window.getComputedStyle(e,null),window.WebKitCSSMatrix?(s=r.transform||r.webkitTransform,s.split(",").length>6&&(s=s.split(", ").map(function(e){return e.replace(",",".")}).join(", ")),i=new window.WebKitCSSMatrix("none"===s?"":s)):(i=r.MozTransform||r.OTransform||r.MsTransform||r.msTransform||r.transform||r.getPropertyValue("transform").replace("translate(","matrix(1, 0, 0, 1,"),t=i.toString().split(",")),"x"===a&&(s=window.WebKitCSSMatrix?i.m41:16===t.length?parseFloat(t[12]):parseFloat(t[4])),"y"===a&&(s=window.WebKitCSSMatrix?i.m42:16===t.length?parseFloat(t[13]):parseFloat(t[5])),y.rtl&&s&&(s=-s),s||0)},y.getWrapperTranslate=function(e){return"undefined"==typeof e&&(e=y.isHorizontal()?"x":"y"),y.getTranslate(y.wrapper[0],e)},y.observers=[],y.initObservers=function(){if(y.params.observeParents)for(var e=y.container.parents(),a=0;a<e.length;a++)o(e[a]);o(y.container[0],{childList:!1}),o(y.wrapper[0],{attributes:!1})},y.disconnectObservers=function(){for(var e=0;e<y.observers.length;e++)y.observers[e].disconnect();y.observers=[]},y.createLoop=function(){y.wrapper.children("."+y.params.slideClass+"."+y.params.slideDuplicateClass).remove();var e=y.wrapper.children("."+y.params.slideClass);"auto"!==y.params.slidesPerView||y.params.loopedSlides||(y.params.loopedSlides=e.length),y.loopedSlides=parseInt(y.params.loopedSlides||y.params.slidesPerView,10),y.loopedSlides=y.loopedSlides+y.params.loopAdditionalSlides,y.loopedSlides>e.length&&(y.loopedSlides=e.length);var t,s=[],r=[];for(e.each(function(t,i){var n=a(this);t<y.loopedSlides&&r.push(i),t<e.length&&t>=e.length-y.loopedSlides&&s.push(i),n.attr("data-swiper-slide-index",t)}),t=0;t<r.length;t++)y.wrapper.append(a(r[t].cloneNode(!0)).addClass(y.params.slideDuplicateClass));for(t=s.length-1;t>=0;t--)y.wrapper.prepend(a(s[t].cloneNode(!0)).addClass(y.params.slideDuplicateClass))},y.destroyLoop=function(){y.wrapper.children("."+y.params.slideClass+"."+y.params.slideDuplicateClass).remove(),y.slides.removeAttr("data-swiper-slide-index")},y.reLoop=function(e){var a=y.activeIndex-y.loopedSlides;y.destroyLoop(),y.createLoop(),y.updateSlidesSize(),e&&y.slideTo(a+y.loopedSlides,0,!1)},y.fixLoop=function(){var e;y.activeIndex<y.loopedSlides?(e=y.slides.length-3*y.loopedSlides+y.activeIndex,e+=y.loopedSlides,y.slideTo(e,0,!1,!0)):("auto"===y.params.slidesPerView&&y.activeIndex>=2*y.loopedSlides||y.activeIndex>y.slides.length-2*y.params.slidesPerView)&&(e=-y.slides.length+y.activeIndex+y.loopedSlides,e+=y.loopedSlides,y.slideTo(e,0,!1,!0))},y.appendSlide=function(e){if(y.params.loop&&y.destroyLoop(),"object"==typeof e&&e.length)for(var a=0;a<e.length;a++)e[a]&&y.wrapper.append(e[a]);else y.wrapper.append(e);y.params.loop&&y.createLoop(),y.params.observer&&y.support.observer||y.update(!0)},y.prependSlide=function(e){y.params.loop&&y.destroyLoop();var a=y.activeIndex+1;if("object"==typeof e&&e.length){for(var t=0;t<e.length;t++)e[t]&&y.wrapper.prepend(e[t]);a=y.activeIndex+e.length}else y.wrapper.prepend(e);y.params.loop&&y.createLoop(),y.params.observer&&y.support.observer||y.update(!0),y.slideTo(a,0,!1)},y.removeSlide=function(e){y.params.loop&&(y.destroyLoop(),y.slides=y.wrapper.children("."+y.params.slideClass));var a,t=y.activeIndex;if("object"==typeof e&&e.length){for(var s=0;s<e.length;s++)a=e[s],y.slides[a]&&y.slides.eq(a).remove(),t>a&&t--;t=Math.max(t,0)}else a=e,y.slides[a]&&y.slides.eq(a).remove(),t>a&&t--,t=Math.max(t,0);y.params.loop&&y.createLoop(),y.params.observer&&y.support.observer||y.update(!0),y.params.loop?y.slideTo(t+y.loopedSlides,0,!1):y.slideTo(t,0,!1)},y.removeAllSlides=function(){for(var e=[],a=0;a<y.slides.length;a++)e.push(a);y.removeSlide(e)},y.effects={fade:{setTranslate:function(){for(var e=0;e<y.slides.length;e++){var a=y.slides.eq(e),t=a[0].swiperSlideOffset,s=-t;y.params.virtualTranslate||(s-=y.translate);var r=0;y.isHorizontal()||(r=s,s=0);var i=y.params.fade.crossFade?Math.max(1-Math.abs(a[0].progress),0):1+Math.min(Math.max(a[0].progress,-1),0);a.css({opacity:i}).transform("translate3d("+s+"px, "+r+"px, 0px)")}},setTransition:function(e){if(y.slides.transition(e),y.params.virtualTranslate&&0!==e){var a=!1;y.slides.transitionEnd(function(){if(!a&&y){a=!0,y.animating=!1;for(var e=["webkitTransitionEnd","transitionend","oTransitionEnd","MSTransitionEnd","msTransitionEnd"],t=0;t<e.length;t++)y.wrapper.trigger(e[t])}})}}},flip:{setTranslate:function(){for(var e=0;e<y.slides.length;e++){var t=y.slides.eq(e),s=t[0].progress;y.params.flip.limitRotation&&(s=Math.max(Math.min(t[0].progress,1),-1));var r=t[0].swiperSlideOffset,i=-180*s,n=i,o=0,l=-r,p=0;if(y.isHorizontal()?y.rtl&&(n=-n):(p=l,l=0,o=-n,n=0),t[0].style.zIndex=-Math.abs(Math.round(s))+y.slides.length,y.params.flip.slideShadows){var d=y.isHorizontal()?t.find(".swiper-slide-shadow-left"):t.find(".swiper-slide-shadow-top"),u=y.isHorizontal()?t.find(".swiper-slide-shadow-right"):t.find(".swiper-slide-shadow-bottom");0===d.length&&(d=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"left":"top")+'"></div>'),t.append(d)),0===u.length&&(u=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"right":"bottom")+'"></div>'),t.append(u)),d.length&&(d[0].style.opacity=Math.max(-s,0)),u.length&&(u[0].style.opacity=Math.max(s,0))}t.transform("translate3d("+l+"px, "+p+"px, 0px) rotateX("+o+"deg) rotateY("+n+"deg)")}},setTransition:function(e){if(y.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e),y.params.virtualTranslate&&0!==e){var t=!1;y.slides.eq(y.activeIndex).transitionEnd(function(){if(!t&&y&&a(this).hasClass(y.params.slideActiveClass)){t=!0,y.animating=!1;for(var e=["webkitTransitionEnd","transitionend","oTransitionEnd","MSTransitionEnd","msTransitionEnd"],s=0;s<e.length;s++)y.wrapper.trigger(e[s])}})}}},cube:{setTranslate:function(){var e,t=0;y.params.cube.shadow&&(y.isHorizontal()?(e=y.wrapper.find(".swiper-cube-shadow"),0===e.length&&(e=a('<div class="swiper-cube-shadow"></div>'),y.wrapper.append(e)),e.css({height:y.width+"px"})):(e=y.container.find(".swiper-cube-shadow"),0===e.length&&(e=a('<div class="swiper-cube-shadow"></div>'),y.container.append(e))));for(var s=0;s<y.slides.length;s++){var r=y.slides.eq(s),i=90*s,n=Math.floor(i/360);y.rtl&&(i=-i,n=Math.floor(-i/360));var o=Math.max(Math.min(r[0].progress,1),-1),l=0,p=0,d=0;s%4===0?(l=4*-n*y.size,d=0):(s-1)%4===0?(l=0,d=4*-n*y.size):(s-2)%4===0?(l=y.size+4*n*y.size,d=y.size):(s-3)%4===0&&(l=-y.size,d=3*y.size+4*y.size*n),y.rtl&&(l=-l),y.isHorizontal()||(p=l,l=0);var u="rotateX("+(y.isHorizontal()?0:-i)+"deg) rotateY("+(y.isHorizontal()?i:0)+"deg) translate3d("+l+"px, "+p+"px, "+d+"px)";if(1>=o&&o>-1&&(t=90*s+90*o,y.rtl&&(t=90*-s-90*o)),r.transform(u),y.params.cube.slideShadows){var c=y.isHorizontal()?r.find(".swiper-slide-shadow-left"):r.find(".swiper-slide-shadow-top"),m=y.isHorizontal()?r.find(".swiper-slide-shadow-right"):r.find(".swiper-slide-shadow-bottom");0===c.length&&(c=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"left":"top")+'"></div>'),r.append(c)),0===m.length&&(m=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"right":"bottom")+'"></div>'),r.append(m)),c.length&&(c[0].style.opacity=Math.max(-o,0)),m.length&&(m[0].style.opacity=Math.max(o,0))}}if(y.wrapper.css({"-webkit-transform-origin":"50% 50% -"+y.size/2+"px","-moz-transform-origin":"50% 50% -"+y.size/2+"px","-ms-transform-origin":"50% 50% -"+y.size/2+"px","transform-origin":"50% 50% -"+y.size/2+"px"}),y.params.cube.shadow)if(y.isHorizontal())e.transform("translate3d(0px, "+(y.width/2+y.params.cube.shadowOffset)+"px, "+-y.width/2+"px) rotateX(90deg) rotateZ(0deg) scale("+y.params.cube.shadowScale+")");else{var f=Math.abs(t)-90*Math.floor(Math.abs(t)/90),g=1.5-(Math.sin(2*f*Math.PI/360)/2+Math.cos(2*f*Math.PI/360)/2),h=y.params.cube.shadowScale,v=y.params.cube.shadowScale/g,w=y.params.cube.shadowOffset;e.transform("scale3d("+h+", 1, "+v+") translate3d(0px, "+(y.height/2+w)+"px, "+-y.height/2/v+"px) rotateX(-90deg)")}var b=y.isSafari||y.isUiWebView?-y.size/2:0;y.wrapper.transform("translate3d(0px,0,"+b+"px) rotateX("+(y.isHorizontal()?0:t)+"deg) rotateY("+(y.isHorizontal()?-t:0)+"deg)")},setTransition:function(e){y.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e),y.params.cube.shadow&&!y.isHorizontal()&&y.container.find(".swiper-cube-shadow").transition(e)}},coverflow:{setTranslate:function(){for(var e=y.translate,t=y.isHorizontal()?-e+y.width/2:-e+y.height/2,s=y.isHorizontal()?y.params.coverflow.rotate:-y.params.coverflow.rotate,r=y.params.coverflow.depth,i=0,n=y.slides.length;n>i;i++){var o=y.slides.eq(i),l=y.slidesSizesGrid[i],p=o[0].swiperSlideOffset,d=(t-p-l/2)/l*y.params.coverflow.modifier,u=y.isHorizontal()?s*d:0,c=y.isHorizontal()?0:s*d,m=-r*Math.abs(d),f=y.isHorizontal()?0:y.params.coverflow.stretch*d,g=y.isHorizontal()?y.params.coverflow.stretch*d:0;Math.abs(g)<.001&&(g=0),Math.abs(f)<.001&&(f=0),Math.abs(m)<.001&&(m=0),Math.abs(u)<.001&&(u=0),Math.abs(c)<.001&&(c=0);var h="translate3d("+g+"px,"+f+"px,"+m+"px)  rotateX("+c+"deg) rotateY("+u+"deg)";if(o.transform(h),o[0].style.zIndex=-Math.abs(Math.round(d))+1,y.params.coverflow.slideShadows){var v=y.isHorizontal()?o.find(".swiper-slide-shadow-left"):o.find(".swiper-slide-shadow-top"),w=y.isHorizontal()?o.find(".swiper-slide-shadow-right"):o.find(".swiper-slide-shadow-bottom");0===v.length&&(v=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"left":"top")+'"></div>'),o.append(v)),0===w.length&&(w=a('<div class="swiper-slide-shadow-'+(y.isHorizontal()?"right":"bottom")+'"></div>'),o.append(w)),v.length&&(v[0].style.opacity=d>0?d:0),w.length&&(w[0].style.opacity=-d>0?-d:0)}}if(y.browser.ie){var b=y.wrapper[0].style;b.perspectiveOrigin=t+"px 50%"}},setTransition:function(e){y.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e)}}},y.lazy={initialImageLoaded:!1,loadImageInSlide:function(e,t){if("undefined"!=typeof e&&("undefined"==typeof t&&(t=!0),0!==y.slides.length)){var s=y.slides.eq(e),r=s.find(".swiper-lazy:not(.swiper-lazy-loaded):not(.swiper-lazy-loading)");!s.hasClass("swiper-lazy")||s.hasClass("swiper-lazy-loaded")||s.hasClass("swiper-lazy-loading")||(r=r.add(s[0])),0!==r.length&&r.each(function(){var e=a(this);e.addClass("swiper-lazy-loading");var r=e.attr("data-background"),i=e.attr("data-src"),n=e.attr("data-srcset");y.loadImage(e[0],i||r,n,!1,function(){if(r?(e.css("background-image",'url("'+r+'")'),e.removeAttr("data-background")):(n&&(e.attr("srcset",n),e.removeAttr("data-srcset")),i&&(e.attr("src",i),e.removeAttr("data-src"))),e.addClass("swiper-lazy-loaded").removeClass("swiper-lazy-loading"),s.find(".swiper-lazy-preloader, .preloader").remove(),y.params.loop&&t){var a=s.attr("data-swiper-slide-index");if(s.hasClass(y.params.slideDuplicateClass)){var o=y.wrapper.children('[data-swiper-slide-index="'+a+'"]:not(.'+y.params.slideDuplicateClass+")");y.lazy.loadImageInSlide(o.index(),!1)}else{var l=y.wrapper.children("."+y.params.slideDuplicateClass+'[data-swiper-slide-index="'+a+'"]');y.lazy.loadImageInSlide(l.index(),!1)}}y.emit("onLazyImageReady",y,s[0],e[0])}),y.emit("onLazyImageLoad",y,s[0],e[0])})}},load:function(){var e;if(y.params.watchSlidesVisibility)y.wrapper.children("."+y.params.slideVisibleClass).each(function(){y.lazy.loadImageInSlide(a(this).index())});else if(y.params.slidesPerView>1)for(e=y.activeIndex;e<y.activeIndex+y.params.slidesPerView;e++)y.slides[e]&&y.lazy.loadImageInSlide(e);else y.lazy.loadImageInSlide(y.activeIndex);if(y.params.lazyLoadingInPrevNext)if(y.params.slidesPerView>1||y.params.lazyLoadingInPrevNextAmount&&y.params.lazyLoadingInPrevNextAmount>1){var t=y.params.lazyLoadingInPrevNextAmount,s=y.params.slidesPerView,r=Math.min(y.activeIndex+s+Math.max(t,s),y.slides.length),i=Math.max(y.activeIndex-Math.max(s,t),0);for(e=y.activeIndex+y.params.slidesPerView;r>e;e++)y.slides[e]&&y.lazy.loadImageInSlide(e);for(e=i;e<y.activeIndex;e++)y.slides[e]&&y.lazy.loadImageInSlide(e)}else{var n=y.wrapper.children("."+y.params.slideNextClass);n.length>0&&y.lazy.loadImageInSlide(n.index());var o=y.wrapper.children("."+y.params.slidePrevClass);o.length>0&&y.lazy.loadImageInSlide(o.index())}},onTransitionStart:function(){y.params.lazyLoading&&(y.params.lazyLoadingOnTransitionStart||!y.params.lazyLoadingOnTransitionStart&&!y.lazy.initialImageLoaded)&&y.lazy.load()},onTransitionEnd:function(){y.params.lazyLoading&&!y.params.lazyLoadingOnTransitionStart&&y.lazy.load()}},y.scrollbar={isTouched:!1,setDragPosition:function(e){var a=y.scrollbar,t=y.isHorizontal()?"touchstart"===e.type||"touchmove"===e.type?e.targetTouches[0].pageX:e.pageX||e.clientX:"touchstart"===e.type||"touchmove"===e.type?e.targetTouches[0].pageY:e.pageY||e.clientY,s=t-a.track.offset()[y.isHorizontal()?"left":"top"]-a.dragSize/2,r=-y.minTranslate()*a.moveDivider,i=-y.maxTranslate()*a.moveDivider;r>s?s=r:s>i&&(s=i),s=-s/a.moveDivider,y.updateProgress(s),y.setWrapperTranslate(s,!0)},dragStart:function(e){var a=y.scrollbar;a.isTouched=!0,e.preventDefault(),e.stopPropagation(),a.setDragPosition(e),clearTimeout(a.dragTimeout),a.track.transition(0),y.params.scrollbarHide&&a.track.css("opacity",1),y.wrapper.transition(100),a.drag.transition(100),y.emit("onScrollbarDragStart",y)},dragMove:function(e){var a=y.scrollbar;a.isTouched&&(e.preventDefault?e.preventDefault():e.returnValue=!1,a.setDragPosition(e),y.wrapper.transition(0),a.track.transition(0),a.drag.transition(0),y.emit("onScrollbarDragMove",y))},dragEnd:function(e){var a=y.scrollbar;a.isTouched&&(a.isTouched=!1,y.params.scrollbarHide&&(clearTimeout(a.dragTimeout),a.dragTimeout=setTimeout(function(){a.track.css("opacity",0),a.track.transition(400)},1e3)),y.emit("onScrollbarDragEnd",y),y.params.scrollbarSnapOnRelease&&y.slideReset())},enableDraggable:function(){var e=y.scrollbar,t=y.support.touch?e.track:document;a(e.track).on(y.touchEvents.start,e.dragStart),a(t).on(y.touchEvents.move,e.dragMove),a(t).on(y.touchEvents.end,e.dragEnd)},disableDraggable:function(){var e=y.scrollbar,t=y.support.touch?e.track:document;a(e.track).off(y.touchEvents.start,e.dragStart),a(t).off(y.touchEvents.move,e.dragMove),a(t).off(y.touchEvents.end,e.dragEnd)},set:function(){if(y.params.scrollbar){var e=y.scrollbar;e.track=a(y.params.scrollbar),y.params.uniqueNavElements&&"string"==typeof y.params.scrollbar&&e.track.length>1&&1===y.container.find(y.params.scrollbar).length&&(e.track=y.container.find(y.params.scrollbar)),e.drag=e.track.find(".swiper-scrollbar-drag"),0===e.drag.length&&(e.drag=a('<div class="swiper-scrollbar-drag"></div>'),e.track.append(e.drag)),e.drag[0].style.width="",e.drag[0].style.height="",e.trackSize=y.isHorizontal()?e.track[0].offsetWidth:e.track[0].offsetHeight,e.divider=y.size/y.virtualSize,e.moveDivider=e.divider*(e.trackSize/y.size),e.dragSize=e.trackSize*e.divider,y.isHorizontal()?e.drag[0].style.width=e.dragSize+"px":e.drag[0].style.height=e.dragSize+"px",e.divider>=1?e.track[0].style.display="none":e.track[0].style.display="",y.params.scrollbarHide&&(e.track[0].style.opacity=0)}},setTranslate:function(){if(y.params.scrollbar){var e,a=y.scrollbar,t=(y.translate||0,a.dragSize);e=(a.trackSize-a.dragSize)*y.progress,y.rtl&&y.isHorizontal()?(e=-e,e>0?(t=a.dragSize-e,e=0):-e+a.dragSize>a.trackSize&&(t=a.trackSize+e)):0>e?(t=a.dragSize+e,e=0):e+a.dragSize>a.trackSize&&(t=a.trackSize-e),y.isHorizontal()?(y.support.transforms3d?a.drag.transform("translate3d("+e+"px, 0, 0)"):a.drag.transform("translateX("+e+"px)"),a.drag[0].style.width=t+"px"):(y.support.transforms3d?a.drag.transform("translate3d(0px, "+e+"px, 0)"):a.drag.transform("translateY("+e+"px)"),a.drag[0].style.height=t+"px"),y.params.scrollbarHide&&(clearTimeout(a.timeout),a.track[0].style.opacity=1,a.timeout=setTimeout(function(){a.track[0].style.opacity=0,a.track.transition(400)},1e3))}},setTransition:function(e){y.params.scrollbar&&y.scrollbar.drag.transition(e)}},y.controller={LinearSpline:function(e,a){this.x=e,this.y=a,this.lastIndex=e.length-1;var t,s;this.x.length;this.interpolate=function(e){return e?(s=r(this.x,e),t=s-1,(e-this.x[t])*(this.y[s]-this.y[t])/(this.x[s]-this.x[t])+this.y[t]):0};var r=function(){var e,a,t;return function(s,r){for(a=-1,e=s.length;e-a>1;)s[t=e+a>>1]<=r?a=t:e=t;return e}}()},getInterpolateFunction:function(e){y.controller.spline||(y.controller.spline=y.params.loop?new y.controller.LinearSpline(y.slidesGrid,e.slidesGrid):new y.controller.LinearSpline(y.snapGrid,e.snapGrid))},setTranslate:function(e,a){function s(a){e=a.rtl&&"horizontal"===a.params.direction?-y.translate:y.translate,"slide"===y.params.controlBy&&(y.controller.getInterpolateFunction(a),i=-y.controller.spline.interpolate(-e)),i&&"container"!==y.params.controlBy||(r=(a.maxTranslate()-a.minTranslate())/(y.maxTranslate()-y.minTranslate()),i=(e-y.minTranslate())*r+a.minTranslate()),y.params.controlInverse&&(i=a.maxTranslate()-i),a.updateProgress(i),a.setWrapperTranslate(i,!1,y),a.updateActiveIndex()}var r,i,n=y.params.control;if(y.isArray(n))for(var o=0;o<n.length;o++)n[o]!==a&&n[o]instanceof t&&s(n[o]);else n instanceof t&&a!==n&&s(n)},setTransition:function(e,a){function s(a){a.setWrapperTransition(e,y),0!==e&&(a.onTransitionStart(),a.wrapper.transitionEnd(function(){i&&(a.params.loop&&"slide"===y.params.controlBy&&a.fixLoop(),a.onTransitionEnd())}))}var r,i=y.params.control;if(y.isArray(i))for(r=0;r<i.length;r++)i[r]!==a&&i[r]instanceof t&&s(i[r]);else i instanceof t&&a!==i&&s(i)}},y.hashnav={init:function(){if(y.params.hashnav){y.hashnav.initialized=!0;var e=document.location.hash.replace("#","");if(e)for(var a=0,t=0,s=y.slides.length;s>t;t++){var r=y.slides.eq(t),i=r.attr("data-hash");if(i===e&&!r.hasClass(y.params.slideDuplicateClass)){var n=r.index();y.slideTo(n,a,y.params.runCallbacksOnInit,!0)}}}},setHash:function(){y.hashnav.initialized&&y.params.hashnav&&(document.location.hash=y.slides.eq(y.activeIndex).attr("data-hash")||"")}},y.disableKeyboardControl=function(){y.params.keyboardControl=!1,a(document).off("keydown",l)},y.enableKeyboardControl=function(){y.params.keyboardControl=!0,a(document).on("keydown",l)},y.mousewheel={event:!1,lastScrollTime:(new window.Date).getTime()},y.params.mousewheelControl){try{new window.WheelEvent("wheel"),y.mousewheel.event="wheel"}catch(O){(window.WheelEvent||y.container[0]&&"wheel"in y.container[0])&&(y.mousewheel.event="wheel")}!y.mousewheel.event&&window.WheelEvent,y.mousewheel.event||void 0===document.onmousewheel||(y.mousewheel.event="mousewheel"),y.mousewheel.event||(y.mousewheel.event="DOMMouseScroll")}y.disableMousewheelControl=function(){return y.mousewheel.event?(y.container.off(y.mousewheel.event,p),!0):!1},y.enableMousewheelControl=function(){return y.mousewheel.event?(y.container.on(y.mousewheel.event,p),!0):!1},y.parallax={setTranslate:function(){y.container.children("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){d(this,y.progress)}),y.slides.each(function(){var e=a(this);e.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){var a=Math.min(Math.max(e[0].progress,-1),1);d(this,a)})})},setTransition:function(e){"undefined"==typeof e&&(e=y.params.speed),y.container.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){var t=a(this),s=parseInt(t.attr("data-swiper-parallax-duration"),10)||e;0===e&&(s=0),t.transition(s)})}},y._plugins=[];for(var N in y.plugins){var R=y.plugins[N](y,y.params[N]);R&&y._plugins.push(R)}return y.callPlugins=function(e){for(var a=0;a<y._plugins.length;a++)e in y._plugins[a]&&y._plugins[a][e](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5])},y.emitterEventListeners={},y.emit=function(e){y.params[e]&&y.params[e](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5]);var a;if(y.emitterEventListeners[e])for(a=0;a<y.emitterEventListeners[e].length;a++)y.emitterEventListeners[e][a](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5]);y.callPlugins&&y.callPlugins(e,arguments[1],arguments[2],arguments[3],arguments[4],arguments[5])},y.on=function(e,a){return e=u(e),y.emitterEventListeners[e]||(y.emitterEventListeners[e]=[]),y.emitterEventListeners[e].push(a),y},y.off=function(e,a){var t;if(e=u(e),"undefined"==typeof a)return y.emitterEventListeners[e]=[],y;if(y.emitterEventListeners[e]&&0!==y.emitterEventListeners[e].length){for(t=0;t<y.emitterEventListeners[e].length;t++)y.emitterEventListeners[e][t]===a&&y.emitterEventListeners[e].splice(t,1);return y}},y.once=function(e,a){e=u(e);var t=function(){a(arguments[0],arguments[1],arguments[2],arguments[3],arguments[4]),y.off(e,t)};return y.on(e,t),y},y.a11y={makeFocusable:function(e){return e.attr("tabIndex","0"),e},addRole:function(e,a){return e.attr("role",a),e},addLabel:function(e,a){return e.attr("aria-label",a),e},disable:function(e){return e.attr("aria-disabled",!0),e},enable:function(e){return e.attr("aria-disabled",!1),e},onEnterKey:function(e){13===e.keyCode&&(a(e.target).is(y.params.nextButton)?(y.onClickNext(e),y.isEnd?y.a11y.notify(y.params.lastSlideMessage):y.a11y.notify(y.params.nextSlideMessage)):a(e.target).is(y.params.prevButton)&&(y.onClickPrev(e),y.isBeginning?y.a11y.notify(y.params.firstSlideMessage):y.a11y.notify(y.params.prevSlideMessage)),a(e.target).is("."+y.params.bulletClass)&&a(e.target)[0].click())},liveRegion:a('<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>'),notify:function(e){var a=y.a11y.liveRegion;0!==a.length&&(a.html(""),a.html(e))},init:function(){y.params.nextButton&&y.nextButton&&y.nextButton.length>0&&(y.a11y.makeFocusable(y.nextButton),y.a11y.addRole(y.nextButton,"button"),y.a11y.addLabel(y.nextButton,y.params.nextSlideMessage)),y.params.prevButton&&y.prevButton&&y.prevButton.length>0&&(y.a11y.makeFocusable(y.prevButton),y.a11y.addRole(y.prevButton,"button"),y.a11y.addLabel(y.prevButton,y.params.prevSlideMessage)),a(y.container).append(y.a11y.liveRegion)},initPagination:function(){y.params.pagination&&y.params.paginationClickable&&y.bullets&&y.bullets.length&&y.bullets.each(function(){var e=a(this);y.a11y.makeFocusable(e),y.a11y.addRole(e,"button"),y.a11y.addLabel(e,y.params.paginationBulletMessage.replace(/{{index}}/,e.index()+1))})},destroy:function(){y.a11y.liveRegion&&y.a11y.liveRegion.length>0&&y.a11y.liveRegion.remove()}},y.init=function(){y.params.loop&&y.createLoop(),y.updateContainerSize(),y.updateSlidesSize(),y.updatePagination(),y.params.scrollbar&&y.scrollbar&&(y.scrollbar.set(),y.params.scrollbarDraggable&&y.scrollbar.enableDraggable()),"slide"!==y.params.effect&&y.effects[y.params.effect]&&(y.params.loop||y.updateProgress(),y.effects[y.params.effect].setTranslate()),y.params.loop?y.slideTo(y.params.initialSlide+y.loopedSlides,0,y.params.runCallbacksOnInit):(y.slideTo(y.params.initialSlide,0,y.params.runCallbacksOnInit),0===y.params.initialSlide&&(y.parallax&&y.params.parallax&&y.parallax.setTranslate(),y.lazy&&y.params.lazyLoading&&(y.lazy.load(),y.lazy.initialImageLoaded=!0))),y.attachEvents(),y.params.observer&&y.support.observer&&y.initObservers(),y.params.preloadImages&&!y.params.lazyLoading&&y.preloadImages(),y.params.autoplay&&y.startAutoplay(),y.params.keyboardControl&&y.enableKeyboardControl&&y.enableKeyboardControl(),y.params.mousewheelControl&&y.enableMousewheelControl&&y.enableMousewheelControl(),y.params.hashnav&&y.hashnav&&y.hashnav.init(),y.params.a11y&&y.a11y&&y.a11y.init(),y.emit("onInit",y)},y.cleanupStyles=function(){y.container.removeClass(y.classNames.join(" ")).removeAttr("style"),y.wrapper.removeAttr("style"),y.slides&&y.slides.length&&y.slides.removeClass([y.params.slideVisibleClass,y.params.slideActiveClass,y.params.slideNextClass,y.params.slidePrevClass].join(" ")).removeAttr("style").removeAttr("data-swiper-column").removeAttr("data-swiper-row"),y.paginationContainer&&y.paginationContainer.length&&y.paginationContainer.removeClass(y.params.paginationHiddenClass),y.bullets&&y.bullets.length&&y.bullets.removeClass(y.params.bulletActiveClass),y.params.prevButton&&a(y.params.prevButton).removeClass(y.params.buttonDisabledClass),y.params.nextButton&&a(y.params.nextButton).removeClass(y.params.buttonDisabledClass),y.params.scrollbar&&y.scrollbar&&(y.scrollbar.track&&y.scrollbar.track.length&&y.scrollbar.track.removeAttr("style"),y.scrollbar.drag&&y.scrollbar.drag.length&&y.scrollbar.drag.removeAttr("style"))},y.destroy=function(e,a){y.detachEvents(),y.stopAutoplay(),y.params.scrollbar&&y.scrollbar&&y.params.scrollbarDraggable&&y.scrollbar.disableDraggable(),y.params.loop&&y.destroyLoop(),a&&y.cleanupStyles(),y.disconnectObservers(),y.params.keyboardControl&&y.disableKeyboardControl&&y.disableKeyboardControl(),y.params.mousewheelControl&&y.disableMousewheelControl&&y.disableMousewheelControl(),y.params.a11y&&y.a11y&&y.a11y.destroy(),y.emit("onDestroy"),e!==!1&&(y=null)},y.init(),y}};t.prototype={isSafari:function(){var e=navigator.userAgent.toLowerCase();return e.indexOf("safari")>=0&&e.indexOf("chrome")<0&&e.indexOf("android")<0}(),isUiWebView:/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent),isArray:function(e){return"[object Array]"===Object.prototype.toString.apply(e)},browser:{ie:window.navigator.pointerEnabled||window.navigator.msPointerEnabled,ieTouch:window.navigator.msPointerEnabled&&window.navigator.msMaxTouchPoints>1||window.navigator.pointerEnabled&&window.navigator.maxTouchPoints>1},device:function(){var e=navigator.userAgent,a=e.match(/(Android);?[\s\/]+([\d.]+)?/),t=e.match(/(iPad).*OS\s([\d_]+)/),s=e.match(/(iPod)(.*OS\s([\d_]+))?/),r=!t&&e.match(/(iPhone\sOS)\s([\d_]+)/);return{ios:t||r||s,android:a}}(),support:{touch:window.Modernizr&&Modernizr.touch===!0||function(){return!!("ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch)}(),transforms3d:window.Modernizr&&Modernizr.csstransforms3d===!0||function(){var e=document.createElement("div").style;return"webkitPerspective"in e||"MozPerspective"in e||"OPerspective"in e||"MsPerspective"in e||"perspective"in e}(),flexbox:function(){for(var e=document.createElement("div").style,a="alignItems webkitAlignItems webkitBoxAlign msFlexAlign mozBoxAlign webkitFlexDirection msFlexDirection mozBoxDirection mozBoxOrient webkitBoxDirection webkitBoxOrient".split(" "),t=0;t<a.length;t++)if(a[t]in e)return!0}(),observer:function(){return"MutationObserver"in window||"WebkitMutationObserver"in window}()},plugins:{}};for(var s=["jQuery","Zepto","Dom7"],r=0;r<s.length;r++)window[s[r]]&&e(window[s[r]]);var i;i="undefined"==typeof Dom7?window.Dom7||window.Zepto||window.jQuery:Dom7,i&&("transitionEnd"in i.fn||(i.fn.transitionEnd=function(e){function a(i){if(i.target===this)for(e.call(this,i),t=0;t<s.length;t++)r.off(s[t],a)}var t,s=["webkitTransitionEnd","transitionend","oTransitionEnd","MSTransitionEnd","msTransitionEnd"],r=this;if(e)for(t=0;t<s.length;t++)r.on(s[t],a);return this}),"transform"in i.fn||(i.fn.transform=function(e){for(var a=0;a<this.length;a++){var t=this[a].style;t.webkitTransform=t.MsTransform=t.msTransform=t.MozTransform=t.OTransform=t.transform=e}return this}),"transition"in i.fn||(i.fn.transition=function(e){"string"!=typeof e&&(e+="ms");for(var a=0;a<this.length;a++){var t=this[a].style;t.webkitTransitionDuration=t.MsTransitionDuration=t.msTransitionDuration=t.MozTransitionDuration=t.OTransitionDuration=t.transitionDuration=e}return this})),window.Swiper=t}(),"undefined"!=typeof module?module.exports=window.Swiper:"function"==typeof define&&define.amd&&define([],function(){"use strict";return window.Swiper});! function(a) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], a) : "undefined" != typeof exports ? module.exports = a(require("jquery")) : a(jQuery)
}(function(a) {
    "use strict";
    var b = window.Slick || {};
    b = function() {
        function c(c, d) {
            var f, e = this;
            e.defaults = {
                accessibility: !0,
                adaptiveHeight: !1,
                appendArrows: a(c),
                appendDots: a(c),
                arrows: !0,
                asNavFor: null,
                prevArrow: '<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button">Previous</button>',
                nextArrow: '<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button">Next</button>',
                autoplay: !1,
                autoplaySpeed: 3e3,
                centerMode: !1,
                centerPadding: "50px",
                cssEase: "ease",
                customPaging: function(b, c) {
                    return a('<button type="button" data-role="none" role="button" tabindex="0" />').text(c + 1)
                },
                dots: !0,
                dotsClass: "slick-dots",
                draggable: !0,
                easing: "linear",
                edgeFriction: .35,
                fade: !1,
                focusOnSelect: !1,
                infinite: !0,
                initialSlide: 0,
                lazyLoad: "ondemand",
                placeHolder: met_lazyloadbg,
                lazyloadPrevNext:!1,
                mobileFirst: !1,
                pauseOnHover: !0,
                pauseOnFocus: !0,
                pauseOnDotsHover: !1,
                respondTo: "window",
                responsive: null,
                rows: 1,
                rtl: !1,
                slide: "",
                slidesPerRow: 1,
                slidesToShow: 1,
                slidesToScroll: 1,
                speed: 500,
                swipe: !0,
                swipeToSlide: !1,
                touchMove: !0,
                touchThreshold: 5,
                useCSS: !0,
                useTransform: !0,
                variableWidth: !1,
                vertical: !1,
                verticalSwiping: !1,
                waitForAnimate: !0,
                zIndex: 1e3
            }, e.initials = {
                animating: !1,
                dragging: !1,
                autoPlayTimer: null,
                currentDirection: 0,
                currentLeft: null,
                currentSlide: 0,
                direction: 1,
                $dots: null,
                listWidth: null,
                listHeight: null,
                loadIndex: 0,
                $nextArrow: null,
                $prevArrow: null,
                slideCount: null,
                slideWidth: null,
                $slideTrack: null,
                $slides: null,
                sliding: !1,
                slideOffset: 0,
                swipeLeft: null,
                $list: null,
                touchObject: {},
                transformsEnabled: !1,
                unslicked: !1
            }, a.extend(e, e.initials), e.activeBreakpoint = null, e.animType = null, e.animProp = null, e.breakpoints = [], e.breakpointSettings = [], e.cssTransitions = !1, e.focussed = !1, e.interrupted = !1, e.hidden = "hidden", e.paused = !0, e.positionProp = null, e.respondTo = null, e.rowCount = 1, e.shouldClick = !0, e.$slider = a(c), e.$slidesCache = null, e.transformType = null, e.transitionType = null, e.visibilityChange = "visibilitychange", e.windowWidth = 0, e.windowTimer = null, f = a(c).data("slick") || {}, e.options = a.extend({}, e.defaults, d, f), e.currentSlide = e.options.initialSlide, e.originalSettings = e.options, "undefined" != typeof document.mozHidden ? (e.hidden = "mozHidden", e.visibilityChange = "mozvisibilitychange") : "undefined" != typeof document.webkitHidden && (e.hidden = "webkitHidden", e.visibilityChange = "webkitvisibilitychange"), e.autoPlay = a.proxy(e.autoPlay, e), e.autoPlayClear = a.proxy(e.autoPlayClear, e), e.autoPlayIterator = a.proxy(e.autoPlayIterator, e), e.changeSlide = a.proxy(e.changeSlide, e), e.clickHandler = a.proxy(e.clickHandler, e), e.selectHandler = a.proxy(e.selectHandler, e), e.setPosition = a.proxy(e.setPosition, e), e.swipeHandler = a.proxy(e.swipeHandler, e), e.dragHandler = a.proxy(e.dragHandler, e), e.keyHandler = a.proxy(e.keyHandler, e), e.instanceUid = b++, e.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/, e.registerBreakpoints(), e.init(!0)
        }
        var b = 0;
        return c
    }(), b.prototype.activateADA = function() {
        var a = this;
        a.$slideTrack.find(".slick-active").attr({
            "aria-hidden": "false"
        }).find("a, input, button, select").attr({
            tabindex: "0"
        })
    }, b.prototype.addSlide = b.prototype.slickAdd = function(b, c, d) {
        var e = this;
        if ("boolean" == typeof c) d = c, c = null;
        else if (0 > c || c >= e.slideCount) return !1;
        e.unload(), "number" == typeof c ? 0 === c && 0 === e.$slides.length ? a(b).appendTo(e.$slideTrack) : d ? a(b).insertBefore(e.$slides.eq(c)) : a(b).insertAfter(e.$slides.eq(c)) : d === !0 ? a(b).prependTo(e.$slideTrack) : a(b).appendTo(e.$slideTrack), e.$slides = e.$slideTrack.children(this.options.slide), e.$slideTrack.children(this.options.slide).detach(), e.$slideTrack.append(e.$slides), e.$slides.each(function(b, c) {
            a(c).attr("data-slick-index", b)
        }), e.$slidesCache = e.$slides, e.reinit()
    }, b.prototype.animateHeight = function() {
        var a = this;
        if (1 === a.options.slidesToShow && a.options.adaptiveHeight === !0 && a.options.vertical === !1) {
            var b = a.$slides.eq(a.currentSlide).outerHeight(!0);
            a.$list.animate({
                height: b
            }, a.options.speed)
        }
    }, b.prototype.animateSlide = function(b, c) {
        var d = {},
            e = this;
        e.animateHeight(), e.options.rtl === !0 && e.options.vertical === !1 && (b = -b), e.transformsEnabled === !1 ? e.options.vertical === !1 ? e.$slideTrack.animate({
            left: b
        }, e.options.speed, e.options.easing, c) : e.$slideTrack.animate({
            top: b
        }, e.options.speed, e.options.easing, c) : e.cssTransitions === !1 ? (e.options.rtl === !0 && (e.currentLeft = -e.currentLeft), a({
            animStart: e.currentLeft
        }).animate({
            animStart: b
        }, {
            duration: e.options.speed,
            easing: e.options.easing,
            step: function(a) {
                a = Math.ceil(a), e.options.vertical === !1 ? (d[e.animType] = "translate(" + a + "px, 0px)", e.$slideTrack.css(d)) : (d[e.animType] = "translate(0px," + a + "px)", e.$slideTrack.css(d))
            },
            complete: function() {
                c && c.call()
            }
        })) : (e.applyTransition(), b = Math.ceil(b), e.options.vertical === !1 ? d[e.animType] = "translate3d(" + b + "px, 0px, 0px)" : d[e.animType] = "translate3d(0px," + b + "px, 0px)", e.$slideTrack.css(d), c && setTimeout(function() {
            e.disableTransition(), c.call()
        }, e.options.speed))
    }, b.prototype.getNavTarget = function() {
        var b = this,
            c = b.options.asNavFor;
        return c && null !== c && (c = a(c).not(b.$slider)), c
    }, b.prototype.asNavFor = function(b) {
        var c = this,
            d = c.getNavTarget();
        null !== d && "object" == typeof d && d.each(function() {
            var c = a(this).slick("getSlick");
            c.unslicked || c.slideHandler(b, !0)
        })
    }, b.prototype.applyTransition = function(a) {
        var b = this,
            c = {};
        b.options.fade === !1 ? c[b.transitionType] = b.transformType + " " + b.options.speed + "ms " + b.options.cssEase : c[b.transitionType] = "opacity " + b.options.speed + "ms " + b.options.cssEase, b.options.fade === !1 ? b.$slideTrack.css(c) : b.$slides.eq(a).css(c)
    }, b.prototype.autoPlay = function() {
        var a = this;
        a.autoPlayClear(), a.slideCount > a.options.slidesToShow && (a.autoPlayTimer = setInterval(a.autoPlayIterator, a.options.autoplaySpeed))
    }, b.prototype.autoPlayClear = function() {
        var a = this;
        a.autoPlayTimer && clearInterval(a.autoPlayTimer)
    }, b.prototype.autoPlayIterator = function() {
        var a = this,
            b = a.currentSlide + a.options.slidesToScroll;
        a.paused || a.interrupted || a.focussed || (a.options.infinite === !1 && (1 === a.direction && a.currentSlide + 1 === a.slideCount - 1 ? a.direction = 0 : 0 === a.direction && (b = a.currentSlide - a.options.slidesToScroll, a.currentSlide - 1 === 0 && (a.direction = 1))), a.slideHandler(b))
    }, b.prototype.buildArrows = function() {
        var b = this;
        b.options.arrows === !0 && (b.$prevArrow = a(b.options.prevArrow).addClass("slick-arrow"), b.$nextArrow = a(b.options.nextArrow).addClass("slick-arrow"), b.slideCount > b.options.slidesToShow ? (b.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), b.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), b.htmlExpr.test(b.options.prevArrow) && b.$prevArrow.prependTo(b.options.appendArrows), b.htmlExpr.test(b.options.nextArrow) && b.$nextArrow.appendTo(b.options.appendArrows), b.options.infinite !== !0 && b.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true")) : b.$prevArrow.add(b.$nextArrow).addClass("slick-hidden").attr({
            "aria-disabled": "true",
            tabindex: "-1"
        }))
    }, b.prototype.buildDots = function() {
        var c, d, b = this;
        if (b.options.dots === !0 && b.slideCount > b.options.slidesToShow) {
            for (b.$slider.addClass("slick-dotted"), d = a("<ul />").addClass(b.options.dotsClass), c = 0; c <= b.getDotCount(); c += 1) d.append(a("<li />").append(b.options.customPaging.call(this, b, c)));
            b.$dots = d.appendTo(b.options.appendDots), b.$dots.find("li").first().addClass("slick-active").attr("aria-hidden", "false")
        }
    }, b.prototype.buildOut = function() {
        var b = this;
        b.$slides = b.$slider.children(b.options.slide + ":not(.slick-cloned)").addClass("slick-slide"), b.slideCount = b.$slides.length, b.$slides.each(function(b, c) {
            a(c).attr("data-slick-index", b).data("originalStyling", a(c).attr("style") || "")
        }), b.$slider.addClass("slick-slider"), b.$slideTrack = 0 === b.slideCount ? a('<div class="slick-track"/>').appendTo(b.$slider) : b.$slides.wrapAll('<div class="slick-track"/>').parent(), b.$list = b.$slideTrack.wrap('<div aria-live="polite" class="slick-list"/>').parent(), b.$slideTrack.css("opacity", 0), (b.options.centerMode === !0 || b.options.swipeToSlide === !0) && (b.options.slidesToScroll = 1), a("img[data-lazy]", b.$slider).not("[src]").addClass("slick-loading"), b.setupInfinite(), b.buildArrows(), b.buildDots(), b.updateDots(), b.setSlideClasses("number" == typeof b.currentSlide ? b.currentSlide : 0), b.options.draggable === !0 && b.$list.addClass("draggable")
    }, b.prototype.buildRows = function() {
        var b, c, d, e, f, g, h, a = this;
        if (e = document.createDocumentFragment(), g = a.$slider.children(), a.options.rows > 1) {
            for (h = a.options.slidesPerRow * a.options.rows, f = Math.ceil(g.length / h), b = 0; f > b; b++) {
                var i = document.createElement("div");
                for (c = 0; c < a.options.rows; c++) {
                    var j = document.createElement("div");
                    for (d = 0; d < a.options.slidesPerRow; d++) {
                        var k = b * h + (c * a.options.slidesPerRow + d);
                        g.get(k) && j.appendChild(g.get(k))
                    }
                    i.appendChild(j)
                }
                e.appendChild(i)
            }
            a.$slider.empty().append(e), a.$slider.children().children().children().css({
                width: 100 / a.options.slidesPerRow + "%",
                display: "inline-block"
            })
        }
    }, b.prototype.checkResponsive = function(b, c) {
        var e, f, g, d = this,
            h = !1,
            i = d.$slider.width(),
            j = window.innerWidth || a(window).width();
        if ("window" === d.respondTo ? g = j : "slider" === d.respondTo ? g = i : "min" === d.respondTo && (g = Math.min(j, i)), d.options.responsive && d.options.responsive.length && null !== d.options.responsive) {
            f = null;
            for (e in d.breakpoints) d.breakpoints.hasOwnProperty(e) && (d.originalSettings.mobileFirst === !1 ? g < d.breakpoints[e] && (f = d.breakpoints[e]) : g > d.breakpoints[e] && (f = d.breakpoints[e]));
            null !== f ? null !== d.activeBreakpoint ? (f !== d.activeBreakpoint || c) && (d.activeBreakpoint = f, "unslick" === d.breakpointSettings[f] ? d.unslick(f) : (d.options = a.extend({}, d.originalSettings, d.breakpointSettings[f]), b === !0 && (d.currentSlide = d.options.initialSlide), d.refresh(b)), h = f) : (d.activeBreakpoint = f, "unslick" === d.breakpointSettings[f] ? d.unslick(f) : (d.options = a.extend({}, d.originalSettings, d.breakpointSettings[f]), b === !0 && (d.currentSlide = d.options.initialSlide), d.refresh(b)), h = f) : null !== d.activeBreakpoint && (d.activeBreakpoint = null, d.options = d.originalSettings, b === !0 && (d.currentSlide = d.options.initialSlide), d.refresh(b), h = f), b || h === !1 || d.$slider.trigger("breakpoint", [d, h])
        }
    }, b.prototype.changeSlide = function(b, c) {
        var f, g, h, d = this,
            e = a(b.currentTarget);
        switch (e.is("a") && b.preventDefault(), e.is("li") || (e = e.closest("li")), h = d.slideCount % d.options.slidesToScroll !== 0, f = h ? 0 : (d.slideCount - d.currentSlide) % d.options.slidesToScroll, b.data.message) {
            case "previous":
                g = 0 === f ? d.options.slidesToScroll : d.options.slidesToShow - f, d.slideCount > d.options.slidesToShow && d.slideHandler(d.currentSlide - g, !1, c);
                break;
            case "next":
                g = 0 === f ? d.options.slidesToScroll : f, d.slideCount > d.options.slidesToShow && d.slideHandler(d.currentSlide + g, !1, c);
                break;
            case "index":
                var i = 0 === b.data.index ? 0 : b.data.index || e.index() * d.options.slidesToScroll;
                d.slideHandler(d.checkNavigable(i), !1, c), e.children().trigger("focus");
                break;
            default:
                return
        }
    }, b.prototype.checkNavigable = function(a) {
        var c, d, b = this;
        if (c = b.getNavigableIndexes(), d = 0, a > c[c.length - 1]) a = c[c.length - 1];
        else
            for (var e in c) {
                if (a < c[e]) {
                    a = d;
                    break
                }
                d = c[e]
            }
        return a
    }, b.prototype.cleanUpEvents = function() {
        var b = this;
        b.options.dots && null !== b.$dots && a("li", b.$dots).off("click.slick", b.changeSlide).off("mouseenter.slick", a.proxy(b.interrupt, b, !0)).off("mouseleave.slick", a.proxy(b.interrupt, b, !1)), b.$slider.off("focus.slick blur.slick"), b.options.arrows === !0 && b.slideCount > b.options.slidesToShow && (b.$prevArrow && b.$prevArrow.off("click.slick", b.changeSlide), b.$nextArrow && b.$nextArrow.off("click.slick", b.changeSlide)), b.$list.off("touchstart.slick mousedown.slick", b.swipeHandler), b.$list.off("touchmove.slick mousemove.slick", b.swipeHandler), b.$list.off("touchend.slick mouseup.slick", b.swipeHandler), b.$list.off("touchcancel.slick mouseleave.slick", b.swipeHandler), b.$list.off("click.slick", b.clickHandler), a(document).off(b.visibilityChange, b.visibility), b.cleanUpSlideEvents(), b.options.accessibility === !0 && b.$list.off("keydown.slick", b.keyHandler), b.options.focusOnSelect === !0 && a(b.$slideTrack).children().off("click.slick", b.selectHandler), a(window).off("orientationchange.slick.slick-" + b.instanceUid, b.orientationChange), a(window).off("resize.slick.slick-" + b.instanceUid, b.resize), a("[draggable!=true]", b.$slideTrack).off("dragstart", b.preventDefault), a(window).off("load.slick.slick-" + b.instanceUid, b.setPosition), a(document).off("ready.slick.slick-" + b.instanceUid, b.setPosition)
    }, b.prototype.cleanUpSlideEvents = function() {
        var b = this;
        b.$list.off("mouseenter.slick", a.proxy(b.interrupt, b, !0)), b.$list.off("mouseleave.slick", a.proxy(b.interrupt, b, !1))
    }, b.prototype.cleanUpRows = function() {
        var b, a = this;
        a.options.rows > 1 && (b = a.$slides.children().children(), b.removeAttr("style"), a.$slider.empty().append(b))
    }, b.prototype.clickHandler = function(a) {
        var b = this;
        b.shouldClick === !1 && (a.stopImmediatePropagation(), a.stopPropagation(), a.preventDefault())
    }, b.prototype.destroy = function(b) {
        var c = this;
        c.autoPlayClear(), c.touchObject = {}, c.cleanUpEvents(), a(".slick-cloned", c.$slider).detach(), c.$dots && c.$dots.remove(), c.$prevArrow && c.$prevArrow.length && (c.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), c.htmlExpr.test(c.options.prevArrow) && c.$prevArrow.remove()), c.$nextArrow && c.$nextArrow.length && (c.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), c.htmlExpr.test(c.options.nextArrow) && c.$nextArrow.remove()), c.$slides && (c.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function() {
            a(this).attr("style", a(this).data("originalStyling"))
        }), c.$slideTrack.children(this.options.slide).detach(), c.$slideTrack.detach(), c.$list.detach(), c.$slider.append(c.$slides)), c.cleanUpRows(), c.$slider.removeClass("slick-slider"), c.$slider.removeClass("slick-initialized"), c.$slider.removeClass("slick-dotted"), c.unslicked = !0, b || c.$slider.trigger("destroy", [c])
    }, b.prototype.disableTransition = function(a) {
        var b = this,
            c = {};
        c[b.transitionType] = "", b.options.fade === !1 ? b.$slideTrack.css(c) : b.$slides.eq(a).css(c)
    }, b.prototype.fadeSlide = function(a, b) {
        var c = this;
        c.cssTransitions === !1 ? (c.$slides.eq(a).css({
            zIndex: c.options.zIndex
        }), c.$slides.eq(a).animate({
            opacity: 1
        }, c.options.speed, c.options.easing, b)) : (c.applyTransition(a), c.$slides.eq(a).css({
            opacity: 1,
            zIndex: c.options.zIndex
        }), b && setTimeout(function() {
            c.disableTransition(a), b.call()
        }, c.options.speed))
    }, b.prototype.fadeSlideOut = function(a) {
        var b = this;
        b.cssTransitions === !1 ? b.$slides.eq(a).animate({
            opacity: 0,
            zIndex: b.options.zIndex - 2
        }, b.options.speed, b.options.easing) : (b.applyTransition(a), b.$slides.eq(a).css({
            opacity: 0,
            zIndex: b.options.zIndex - 2
        }))
    }, b.prototype.filterSlides = b.prototype.slickFilter = function(a) {
        var b = this;
        null !== a && (b.$slidesCache = b.$slides, b.unload(), b.$slideTrack.children(this.options.slide).detach(), b.$slidesCache.filter(a).appendTo(b.$slideTrack), b.reinit())
    }, b.prototype.focusHandler = function() {
        var b = this;
        b.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick", "*:not(.slick-arrow)", function(c) {
            c.stopImmediatePropagation();
            var d = a(this);
            setTimeout(function() {
                b.options.pauseOnFocus && (b.focussed = d.is(":focus"), b.autoPlay())
            }, 0)
        })
    }, b.prototype.getCurrent = b.prototype.slickCurrentSlide = function() {
        var a = this;
        return a.currentSlide
    }, b.prototype.getDotCount = function() {
        var a = this,
            b = 0,
            c = 0,
            d = 0;
        if (a.options.infinite === !0)
            for (; b < a.slideCount;) ++d, b = c + a.options.slidesToScroll, c += a.options.slidesToScroll <= a.options.slidesToShow ? a.options.slidesToScroll : a.options.slidesToShow;
        else if (a.options.centerMode === !0) d = a.slideCount;
        else if (a.options.asNavFor)
            for (; b < a.slideCount;) ++d, b = c + a.options.slidesToScroll, c += a.options.slidesToScroll <= a.options.slidesToShow ? a.options.slidesToScroll : a.options.slidesToShow;
        else d = 1 + Math.ceil((a.slideCount - a.options.slidesToShow) / a.options.slidesToScroll);
        return d - 1
    }, b.prototype.getLeft = function(a) {
        var c, d, f, b = this,
            e = 0;
        return b.slideOffset = 0, d = b.$slides.first().outerHeight(!0), b.options.infinite === !0 ? (b.slideCount > b.options.slidesToShow && (b.slideOffset = b.slideWidth * b.options.slidesToShow * -1, e = d * b.options.slidesToShow * -1), b.slideCount % b.options.slidesToScroll !== 0 && a + b.options.slidesToScroll > b.slideCount && b.slideCount > b.options.slidesToShow && (a > b.slideCount ? (b.slideOffset = (b.options.slidesToShow - (a - b.slideCount)) * b.slideWidth * -1, e = (b.options.slidesToShow - (a - b.slideCount)) * d * -1) : (b.slideOffset = b.slideCount % b.options.slidesToScroll * b.slideWidth * -1, e = b.slideCount % b.options.slidesToScroll * d * -1))) : a + b.options.slidesToShow > b.slideCount && (b.slideOffset = (a + b.options.slidesToShow - b.slideCount) * b.slideWidth, e = (a + b.options.slidesToShow - b.slideCount) * d), b.slideCount <= b.options.slidesToShow && (b.slideOffset = 0, e = 0), b.options.centerMode === !0 && b.options.infinite === !0 ? b.slideOffset += b.slideWidth * Math.floor(b.options.slidesToShow / 2) - b.slideWidth : b.options.centerMode === !0 && (b.slideOffset = 0, b.slideOffset += b.slideWidth * Math.floor(b.options.slidesToShow / 2)), c = b.options.vertical === !1 ? a * b.slideWidth * -1 + b.slideOffset : a * d * -1 + e, b.options.variableWidth === !0 && (f = b.slideCount <= b.options.slidesToShow || b.options.infinite === !1 ? b.$slideTrack.children(".slick-slide").eq(a) : b.$slideTrack.children(".slick-slide").eq(a + b.options.slidesToShow), c = b.options.rtl === !0 ? f[0] ? -1 * (b.$slideTrack.width() - f[0].offsetLeft - f.width()) : 0 : f[0] ? -1 * f[0].offsetLeft : 0, b.options.centerMode === !0 && (f = b.slideCount <= b.options.slidesToShow || b.options.infinite === !1 ? b.$slideTrack.children(".slick-slide").eq(a) : b.$slideTrack.children(".slick-slide").eq(a + b.options.slidesToShow + 1), c = b.options.rtl === !0 ? f[0] ? -1 * (b.$slideTrack.width() - f[0].offsetLeft - f.width()) : 0 : f[0] ? -1 * f[0].offsetLeft : 0, c += (b.$list.width() - f.outerWidth()) / 2)), c
    }, b.prototype.getOption = b.prototype.slickGetOption = function(a) {
        var b = this;
        return b.options[a]
    }, b.prototype.getNavigableIndexes = function() {
        var e, a = this,
            b = 0,
            c = 0,
            d = [];
        for (a.options.infinite === !1 ? e = a.slideCount : (b = -1 * a.options.slidesToScroll, c = -1 * a.options.slidesToScroll, e = 2 * a.slideCount); e > b;) d.push(b), b = c + a.options.slidesToScroll, c += a.options.slidesToScroll <= a.options.slidesToShow ? a.options.slidesToScroll : a.options.slidesToShow;
        return d
    }, b.prototype.getSlick = function() {
        return this
    }, b.prototype.getSlideCount = function() {
        var c, d, e, b = this;
        return e = b.options.centerMode === !0 ? b.slideWidth * Math.floor(b.options.slidesToShow / 2) : 0, b.options.swipeToSlide === !0 ? (b.$slideTrack.find(".slick-slide").each(function(c, f) {
            return f.offsetLeft - e + a(f).outerWidth() / 2 > -1 * b.swipeLeft ? (d = f, !1) : void 0
        }), c = Math.abs(a(d).attr("data-slick-index") - b.currentSlide) || 1) : b.options.slidesToScroll
    }, b.prototype.goTo = b.prototype.slickGoTo = function(a, b) {
        var c = this;
        c.changeSlide({
            data: {
                message: "index",
                index: parseInt(a)
            }
        }, b)
    }, b.prototype.init = function(b) {
        var c = this;
        a(c.$slider).hasClass("slick-initialized") || (a(c.$slider).addClass("slick-initialized"), c.buildRows(), c.buildOut(), c.setProps(), c.startLoad(), c.loadSlider(), c.initializeEvents(), c.updateArrows(), c.updateDots(), c.checkResponsive(!0), c.focusHandler()), b && c.$slider.trigger("init", [c]), c.options.accessibility === !0 && c.initADA(), c.options.autoplay && (c.paused = !1, c.autoPlay())
    }, b.prototype.initADA = function() {
        var b = this;
        b.$slides.add(b.$slideTrack.find(".slick-cloned")).attr({
            "aria-hidden": "true",
            tabindex: "-1"
        }).find("a, input, button, select").attr({
            tabindex: "-1"
        }), b.$slideTrack.attr("role", "listbox"), b.$slides.not(b.$slideTrack.find(".slick-cloned")).each(function(c) {
            a(this).attr({
                role: "option",
                "aria-describedby": "slick-slide" + b.instanceUid + c
            })
        }), null !== b.$dots && b.$dots.attr("role", "tablist").find("li").each(function(c) {
            a(this).attr({
                role: "presentation",
                "aria-selected": "false",
                "aria-controls": "navigation" + b.instanceUid + c,
                id: "slick-slide" + b.instanceUid + c
            })
        }).first().attr("aria-selected", "true").end().find("button").attr("role", "button").end().closest("div").attr("role", "toolbar"), b.activateADA()
    }, b.prototype.initArrowEvents = function() {
        var a = this;
        a.options.arrows === !0 && a.slideCount > a.options.slidesToShow && (a.$prevArrow.off("click.slick").on("click.slick", {
            message: "previous"
        }, a.changeSlide), a.$nextArrow.off("click.slick").on("click.slick", {
            message: "next"
        }, a.changeSlide))
    }, b.prototype.initDotEvents = function() {
        var b = this;
        b.options.dots === !0 && b.slideCount > b.options.slidesToShow && a("li", b.$dots).on("click.slick", {
            message: "index"
        }, b.changeSlide), b.options.dots === !0 && b.options.pauseOnDotsHover === !0 && a("li", b.$dots).on("mouseenter.slick", a.proxy(b.interrupt, b, !0)).on("mouseleave.slick", a.proxy(b.interrupt, b, !1))
    }, b.prototype.initSlideEvents = function() {
        var b = this;
        b.options.pauseOnHover && (b.$list.on("mouseenter.slick", a.proxy(b.interrupt, b, !0)), b.$list.on("mouseleave.slick", a.proxy(b.interrupt, b, !1)))
    }, b.prototype.initializeEvents = function() {
        var b = this;
        b.initArrowEvents(), b.initDotEvents(), b.initSlideEvents(), b.$list.on("touchstart.slick mousedown.slick", {
            action: "start"
        }, b.swipeHandler), b.$list.on("touchmove.slick mousemove.slick", {
            action: "move"
        }, b.swipeHandler), b.$list.on("touchend.slick mouseup.slick", {
            action: "end"
        }, b.swipeHandler), b.$list.on("touchcancel.slick mouseleave.slick", {
            action: "end"
        }, b.swipeHandler), b.$list.on("click.slick", b.clickHandler), a(document).on(b.visibilityChange, a.proxy(b.visibility, b)), b.options.accessibility === !0 && b.$list.on("keydown.slick", b.keyHandler), b.options.focusOnSelect === !0 && a(b.$slideTrack).children().on("click.slick", b.selectHandler), a(window).on("orientationchange.slick.slick-" + b.instanceUid, a.proxy(b.orientationChange, b)), a(window).on("resize.slick.slick-" + b.instanceUid, a.proxy(b.resize, b)), a("[draggable!=true]", b.$slideTrack).on("dragstart", b.preventDefault), a(window).on("load.slick.slick-" + b.instanceUid, b.setPosition), a(document).on("ready.slick.slick-" + b.instanceUid, b.setPosition)
    }, b.prototype.initUI = function() {
        var a = this;
        a.options.arrows === !0 && a.slideCount > a.options.slidesToShow && (a.$prevArrow.show(), a.$nextArrow.show()), a.options.dots === !0 && a.slideCount > a.options.slidesToShow && a.$dots.show()
    }, b.prototype.keyHandler = function(a) {
        var b = this;
        a.target.tagName.match("TEXTAREA|INPUT|SELECT") || (37 === a.keyCode && b.options.accessibility === !0 ? b.changeSlide({
            data: {
                message: b.options.rtl === !0 ? "next" : "previous"
            }
        }) : 39 === a.keyCode && b.options.accessibility === !0 && b.changeSlide({
            data: {
                message: b.options.rtl === !0 ? "previous" : "next"
            }
        }))
    }, b.prototype.lazyLoad = function() {
        function g(c) {
            a("img[data-lazy]", c).each(function() {
                var c = a(this),
                    d = a(this).attr("data-lazy"),
                    ds = a(this).attr("data-srcset"),
                    e = document.createElement("img");
                c.animate({
                    opacity: 0
                }, 100, function() {
                    c.attr({
                        src: d,
                        srcset: ds
                    }).removeAttr("data-lazy").removeAttr("data-srcset").removeClass("slick-loading").animate({
                        opacity: 1
                    }, 200), b.$slider.trigger("lazyLoaded", [b, c, d])
                });
                e.onerror = function() {
                    c.removeAttr("data-lazy").removeAttr("data-srcset").removeClass("slick-loading").addClass("slick-lazyload-error"), b.$slider.trigger("lazyLoadError", [b, c, d])
                }
            })
        }
        var c, d, e, f, b = this;
        b.options.centerMode === !0 ? b.options.infinite === !0 ? (e = b.currentSlide + (b.options.slidesToShow / 2 + 1), f = e + b.options.slidesToShow + 2) : (e = Math.max(0, b.currentSlide - (b.options.slidesToShow / 2 + 1)), f = 2 + (b.options.slidesToShow / 2 + 1) + b.currentSlide) : (e = b.options.infinite ? b.options.slidesToShow + b.currentSlide : b.currentSlide, f = Math.ceil(e + b.options.slidesToShow), b.options.fade === !0 && (e > 0 && e--, f <= b.slideCount && f++)), c = b.$slider.find(".slick-slide").slice(e, f), g(c), b.slideCount <= b.options.slidesToShow ? (d = b.$slider.find(".slick-slide"), g(d)) : b.currentSlide >= b.slideCount - b.options.slidesToShow ? (d = b.$slider.find(".slick-cloned").slice(0, b.options.slidesToShow), g(d)) : 0 === b.currentSlide && (d = b.$slider.find(".slick-cloned").slice(-1 * b.options.slidesToShow), g(d))
        if(b.options.lazyloadPrevNext && b.$slideTrack.find('.slick-slide').length>2){
            var slide_index=b.$slideTrack.find('.slick-active').index(),
                slide_prevnext_order=[slide_index-1,slide_index+1];
            for (var i = 0; i < 2; i++) {
                b.$slideTrack.find('.slick-slide:eq('+slide_prevnext_order[i]+') img').each(function(){
                    if($(this).attr('data-lazy')) $(this).attr({src:$(this).data('lazy')}).removeAttr('data-lazy').removeClass('slick-loading');
                    if($(this).attr('data-srcset')) $(this).attr({srcset:$(this).data('srcset')}).removeAttr('data-srcset');
                })
            }
        }
    }, b.prototype.loadSlider = function() {
        var a = this;
        if (a.options.placeHolder){
            a.$slideTrack.find('img[data-lazy]').each(function(index, el) {
                $(this).attr({src:a.options.placeHolder});
            });
        };
        a.setPosition(), a.$slideTrack.css({
            opacity: 1
        }), a.$slider.removeClass("slick-loading"), a.initUI(), "progressive" === a.options.lazyLoad && a.progressiveLazyLoad()
    }, b.prototype.next = b.prototype.slickNext = function() {
        var a = this;
        a.changeSlide({
            data: {
                message: "next"
            }
        })
    }, b.prototype.orientationChange = function() {
        var a = this;
        a.checkResponsive(), a.setPosition()
    }, b.prototype.pause = b.prototype.slickPause = function() {
        var a = this;
        a.autoPlayClear(), a.paused = !0
    }, b.prototype.play = b.prototype.slickPlay = function() {
        var a = this;
        a.autoPlay(), a.options.autoplay = !0, a.paused = !1, a.focussed = !1, a.interrupted = !1
    }, b.prototype.postSlide = function(a) {
        var b = this;
        b.unslicked || (b.$slider.trigger("afterChange", [b, a]), b.animating = !1, b.setPosition(), b.swipeLeft = null, b.options.autoplay && b.autoPlay(), b.options.accessibility === !0 && b.initADA())
    }, b.prototype.prev = b.prototype.slickPrev = function() {
        var a = this;
        a.changeSlide({
            data: {
                message: "previous"
            }
        })
    }, b.prototype.preventDefault = function(a) {
        a.preventDefault()
    }, b.prototype.progressiveLazyLoad = function(b) {
        b = b || 1;
        var e, f, fs, g, c = this,
            d = a("img[data-lazy]", c.$slider);
        d.length ? (e = d.first(), f = e.attr("data-lazy"), fs = e.attr("data-srcset"), g = document.createElement("img"), g.onload = function() {
            e.attr({
                src: f,
                srcset: fs
            }).removeAttr("data-lazy").removeAttr("data-srcset").removeClass("slick-loading"), c.options.adaptiveHeight === !0 && c.setPosition(), c.$slider.trigger("lazyLoaded", [c, e, f]), c.progressiveLazyLoad()
        }, g.onerror = function() {
            3 > b ? setTimeout(function() {
                c.progressiveLazyLoad(b + 1)
            }, 500) : (e.removeAttr("data-lazy").removeAttr("data-srcset").removeClass("slick-loading").addClass("slick-lazyload-error"), c.$slider.trigger("lazyLoadError", [c, e, f]), c.progressiveLazyLoad())
        }, g.src = f) : c.$slider.trigger("allImagesLoaded", [c])
    }, b.prototype.refresh = function(b) {
        var d, e, c = this;
        e = c.slideCount - c.options.slidesToShow, !c.options.infinite && c.currentSlide > e && (c.currentSlide = e), c.slideCount <= c.options.slidesToShow && (c.currentSlide = 0), d = c.currentSlide, c.destroy(!0), a.extend(c, c.initials, {
            currentSlide: d
        }), c.init(), b || c.changeSlide({
            data: {
                message: "index",
                index: d
            }
        }, !1)
    }, b.prototype.registerBreakpoints = function() {
        var c, d, e, b = this,
            f = b.options.responsive || null;
        if ("array" === a.type(f) && f.length) {
            b.respondTo = b.options.respondTo || "window";
            for (c in f)
                if (e = b.breakpoints.length - 1, d = f[c].breakpoint, f.hasOwnProperty(c)) {
                    for (; e >= 0;) b.breakpoints[e] && b.breakpoints[e] === d && b.breakpoints.splice(e, 1), e--;
                    b.breakpoints.push(d), b.breakpointSettings[d] = f[c].settings
                }
            b.breakpoints.sort(function(a, c) {
                return b.options.mobileFirst ? a - c : c - a
            })
        }
    }, b.prototype.reinit = function() {
        var b = this;
        b.$slides = b.$slideTrack.children(b.options.slide).addClass("slick-slide"), b.slideCount = b.$slides.length, b.currentSlide >= b.slideCount && 0 !== b.currentSlide && (b.currentSlide = b.currentSlide - b.options.slidesToScroll), b.slideCount <= b.options.slidesToShow && (b.currentSlide = 0), b.registerBreakpoints(), b.setProps(), b.setupInfinite(), b.buildArrows(), b.updateArrows(), b.initArrowEvents(), b.buildDots(), b.updateDots(), b.initDotEvents(), b.cleanUpSlideEvents(), b.initSlideEvents(), b.checkResponsive(!1, !0), b.options.focusOnSelect === !0 && a(b.$slideTrack).children().on("click.slick", b.selectHandler), b.setSlideClasses("number" == typeof b.currentSlide ? b.currentSlide : 0), b.setPosition(), b.focusHandler(), b.paused = !b.options.autoplay, b.autoPlay(), b.$slider.trigger("reInit", [b])
    }, b.prototype.resize = function() {
        var b = this;
        a(window).width() !== b.windowWidth && (clearTimeout(b.windowDelay), b.windowDelay = window.setTimeout(function() {
            b.windowWidth = a(window).width(), b.checkResponsive(), b.unslicked || b.setPosition()
        }, 50))
    }, b.prototype.removeSlide = b.prototype.slickRemove = function(a, b, c) {
        var d = this;
        return "boolean" == typeof a ? (b = a, a = b === !0 ? 0 : d.slideCount - 1) : a = b === !0 ? --a : a, d.slideCount < 1 || 0 > a || a > d.slideCount - 1 ? !1 : (d.unload(), c === !0 ? d.$slideTrack.children().remove() : d.$slideTrack.children(this.options.slide).eq(a).remove(), d.$slides = d.$slideTrack.children(this.options.slide), d.$slideTrack.children(this.options.slide).detach(), d.$slideTrack.append(d.$slides), d.$slidesCache = d.$slides, void d.reinit())
    }, b.prototype.setCSS = function(a) {
        var d, e, b = this,
            c = {};
        b.options.rtl === !0 && (a = -a), d = "left" == b.positionProp ? Math.ceil(a) + "px" : "0px", e = "top" == b.positionProp ? Math.ceil(a) + "px" : "0px", c[b.positionProp] = a, b.transformsEnabled === !1 ? b.$slideTrack.css(c) : (c = {}, b.cssTransitions === !1 ? (c[b.animType] = "translate(" + d + ", " + e + ")", b.$slideTrack.css(c)) : (c[b.animType] = "translate3d(" + d + ", " + e + ", 0px)", b.$slideTrack.css(c)))
    }, b.prototype.setDimensions = function() {
        var a = this;
        a.options.vertical === !1 ? a.options.centerMode === !0 && a.$list.css({
            padding: "0px " + a.options.centerPadding
        }) : (a.$list.height(a.$slides.first().outerHeight(!0) * a.options.slidesToShow), a.options.centerMode === !0 && a.$list.css({
            padding: a.options.centerPadding + " 0px"
        })), a.listWidth = a.$list.width(), a.listHeight = a.$list.height(), a.options.vertical === !1 && a.options.variableWidth === !1 ? (a.slideWidth = Math.ceil(a.listWidth / a.options.slidesToShow), a.$slideTrack.width(Math.ceil(a.slideWidth * a.$slideTrack.children(".slick-slide").length))) : a.options.variableWidth === !0 ? a.$slideTrack.width(5e3 * a.slideCount) : (a.slideWidth = Math.ceil(a.listWidth), a.$slideTrack.height(Math.ceil(a.$slides.first().outerHeight(!0) * a.$slideTrack.children(".slick-slide").length)));
        var b = a.$slides.first().outerWidth(!0) - a.$slides.first().width();
        a.options.variableWidth === !1 && a.$slideTrack.children(".slick-slide").width(a.slideWidth - b)
    }, b.prototype.setFade = function() {
        var c, b = this;
        b.$slides.each(function(d, e) {
            c = b.slideWidth * d * -1, b.options.rtl === !0 ? a(e).css({
                position: "relative",
                right: c,
                top: 0,
                zIndex: b.options.zIndex - 2,
                opacity: 0
            }) : a(e).css({
                position: "relative",
                left: c,
                top: 0,
                zIndex: b.options.zIndex - 2,
                opacity: 0
            })
        }), b.$slides.eq(b.currentSlide).css({
            zIndex: b.options.zIndex - 1,
            opacity: 1
        })
    }, b.prototype.setHeight = function() {
        var a = this;
        if (1 === a.options.slidesToShow && a.options.adaptiveHeight === !0 && a.options.vertical === !1) {
            var b = a.$slides.eq(a.currentSlide).outerHeight(!0);
            a.$list.css("height", b)
        }
    }, b.prototype.setOption = b.prototype.slickSetOption = function() {
        var c, d, e, f, h, b = this,
            g = !1;
        if ("object" === a.type(arguments[0]) ? (e = arguments[0], g = arguments[1], h = "multiple") : "string" === a.type(arguments[0]) && (e = arguments[0], f = arguments[1], g = arguments[2], "responsive" === arguments[0] && "array" === a.type(arguments[1]) ? h = "responsive" : "undefined" != typeof arguments[1] && (h = "single")), "single" === h) b.options[e] = f;
        else if ("multiple" === h) a.each(e, function(a, c) {
            b.options[a] = c
        });
        else if ("responsive" === h)
            for (d in f)
                if ("array" !== a.type(b.options.responsive)) b.options.responsive = [f[d]];
                else {
                    for (c = b.options.responsive.length - 1; c >= 0;) b.options.responsive[c].breakpoint === f[d].breakpoint && b.options.responsive.splice(c, 1), c--;
                    b.options.responsive.push(f[d])
                }
        g && (b.unload(), b.reinit())
    }, b.prototype.setPosition = function() {
        var a = this;
        a.setDimensions(), a.setHeight(), a.options.fade === !1 ? a.setCSS(a.getLeft(a.currentSlide)) : a.setFade(), a.$slider.trigger("setPosition", [a])
    }, b.prototype.setProps = function() {
        var a = this,
            b = document.body.style;
        a.positionProp = a.options.vertical === !0 ? "top" : "left", "top" === a.positionProp ? a.$slider.addClass("slick-vertical") : a.$slider.removeClass("slick-vertical"), (void 0 !== b.WebkitTransition || void 0 !== b.MozTransition || void 0 !== b.msTransition) && a.options.useCSS === !0 && (a.cssTransitions = !0), a.options.fade && ("number" == typeof a.options.zIndex ? a.options.zIndex < 3 && (a.options.zIndex = 3) : a.options.zIndex = a.defaults.zIndex), void 0 !== b.OTransform && (a.animType = "OTransform", a.transformType = "-o-transform", a.transitionType = "OTransition", void 0 === b.perspectiveProperty && void 0 === b.webkitPerspective && (a.animType = !1)), void 0 !== b.MozTransform && (a.animType = "MozTransform", a.transformType = "-moz-transform", a.transitionType = "MozTransition", void 0 === b.perspectiveProperty && void 0 === b.MozPerspective && (a.animType = !1)), void 0 !== b.webkitTransform && (a.animType = "webkitTransform", a.transformType = "-webkit-transform", a.transitionType = "webkitTransition", void 0 === b.perspectiveProperty && void 0 === b.webkitPerspective && (a.animType = !1)), void 0 !== b.msTransform && (a.animType = "msTransform", a.transformType = "-ms-transform", a.transitionType = "msTransition", void 0 === b.msTransform && (a.animType = !1)), void 0 !== b.transform && a.animType !== !1 && (a.animType = "transform", a.transformType = "transform", a.transitionType = "transition"), a.transformsEnabled = a.options.useTransform && null !== a.animType && a.animType !== !1
    }, b.prototype.setSlideClasses = function(a) {
        var c, d, e, f, b = this;
        d = b.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden", "true"), b.$slides.eq(a).addClass("slick-current"), b.options.centerMode === !0 ? (c = Math.floor(b.options.slidesToShow / 2), b.options.infinite === !0 && (a >= c && a <= b.slideCount - 1 - c ? b.$slides.slice(a - c, a + c + 1).addClass("slick-active").attr("aria-hidden", "false") : (e = b.options.slidesToShow + a, d.slice(e - c + 1, e + c + 2).addClass("slick-active").attr("aria-hidden", "false")), 0 === a ? d.eq(d.length - 1 - b.options.slidesToShow).addClass("slick-center") : a === b.slideCount - 1 && d.eq(b.options.slidesToShow).addClass("slick-center")), b.$slides.eq(a).addClass("slick-center")) : a >= 0 && a <= b.slideCount - b.options.slidesToShow ? b.$slides.slice(a, a + b.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false") : d.length <= b.options.slidesToShow ? d.addClass("slick-active").attr("aria-hidden", "false") : (f = b.slideCount % b.options.slidesToShow, e = b.options.infinite === !0 ? b.options.slidesToShow + a : a, b.options.slidesToShow == b.options.slidesToScroll && b.slideCount - a < b.options.slidesToShow ? d.slice(e - (b.options.slidesToShow - f), e + f).addClass("slick-active").attr("aria-hidden", "false") : d.slice(e, e + b.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false")), "ondemand" === b.options.lazyLoad && b.lazyLoad()
    }, b.prototype.setupInfinite = function() {
        var c, d, e, b = this;
        if (b.options.fade === !0 && (b.options.centerMode = !1), b.options.infinite === !0 && b.options.fade === !1 && (d = null, b.slideCount > b.options.slidesToShow)) {
            for (e = b.options.centerMode === !0 ? b.options.slidesToShow + 1 : b.options.slidesToShow, c = b.slideCount; c > b.slideCount - e; c -= 1) d = c - 1, a(b.$slides[d]).clone(!0).attr("id", "").attr("data-slick-index", d - b.slideCount).prependTo(b.$slideTrack).addClass("slick-cloned").find('img').height('').removeAttr('height');
            for (c = 0; e > c; c += 1) d = c, a(b.$slides[d]).clone(!0).attr("id", "").attr("data-slick-index", d + b.slideCount).appendTo(b.$slideTrack).addClass("slick-cloned").find('img').height('').removeAttr('height');
            b.$slideTrack.find(".slick-cloned").find("[id]").each(function() {
                a(this).attr("id", "")
            })
        }
    }, b.prototype.interrupt = function(a) {
        var b = this;
        a || b.autoPlay(), b.interrupted = a
    }, b.prototype.selectHandler = function(b) {
        var c = this,
            d = a(b.target).is(".slick-slide") ? a(b.target) : a(b.target).parents(".slick-slide"),
            e = parseInt(d.attr("data-slick-index"));
        return e || (e = 0), c.slideCount <= c.options.slidesToShow ? (c.setSlideClasses(e), void c.asNavFor(e)) : void c.slideHandler(e)
    }, b.prototype.slideHandler = function(a, b, c) {
        var d, e, f, g, j, h = null,
            i = this;
        return b = b || !1, i.animating === !0 && i.options.waitForAnimate === !0 || i.options.fade === !0 && i.currentSlide === a || i.slideCount <= i.options.slidesToShow ? void 0 : (b === !1 && i.asNavFor(a), d = a, h = i.getLeft(d), g = i.getLeft(i.currentSlide), i.currentLeft = null === i.swipeLeft ? g : i.swipeLeft, i.options.infinite === !1 && i.options.centerMode === !1 && (0 > a || a > i.getDotCount() * i.options.slidesToScroll) ? void(i.options.fade === !1 && (d = i.currentSlide, c !== !0 ? i.animateSlide(g, function() {
            i.postSlide(d)
        }) : i.postSlide(d))) : i.options.infinite === !1 && i.options.centerMode === !0 && (0 > a || a > i.slideCount - i.options.slidesToScroll) ? void(i.options.fade === !1 && (d = i.currentSlide, c !== !0 ? i.animateSlide(g, function() {
            i.postSlide(d)
        }) : i.postSlide(d))) : (i.options.autoplay && clearInterval(i.autoPlayTimer), e = 0 > d ? i.slideCount % i.options.slidesToScroll !== 0 ? i.slideCount - i.slideCount % i.options.slidesToScroll : i.slideCount + d : d >= i.slideCount ? i.slideCount % i.options.slidesToScroll !== 0 ? 0 : d - i.slideCount : d, i.animating = !0, i.$slider.trigger("beforeChange", [i, i.currentSlide, e]), f = i.currentSlide, i.currentSlide = e, i.setSlideClasses(i.currentSlide), i.options.asNavFor && (j = i.getNavTarget(), j = j.slick("getSlick"), j.slideCount <= j.options.slidesToShow && j.setSlideClasses(i.currentSlide)), i.updateDots(), i.updateArrows(), i.options.fade === !0 ? (c !== !0 ? (i.fadeSlideOut(f), i.fadeSlide(e, function() {
            i.postSlide(e)
        })) : i.postSlide(e), void i.animateHeight()) : void(c !== !0 ? i.animateSlide(h, function() {
            i.postSlide(e)
        }) : i.postSlide(e))))
    }, b.prototype.startLoad = function() {
        var a = this;
        a.options.arrows === !0 && a.slideCount > a.options.slidesToShow && (a.$prevArrow.hide(), a.$nextArrow.hide()), a.options.dots === !0 && a.slideCount > a.options.slidesToShow && a.$dots.hide(), a.$slider.addClass("slick-loading")
    }, b.prototype.swipeDirection = function() {
        var a, b, c, d, e = this;
        return a = e.touchObject.startX - e.touchObject.curX, b = e.touchObject.startY - e.touchObject.curY, c = Math.atan2(b, a), d = Math.round(180 * c / Math.PI), 0 > d && (d = 360 - Math.abs(d)), 45 >= d && d >= 0 ? e.options.rtl === !1 ? "left" : "right" : 360 >= d && d >= 315 ? e.options.rtl === !1 ? "left" : "right" : d >= 135 && 225 >= d ? e.options.rtl === !1 ? "right" : "left" : e.options.verticalSwiping === !0 ? d >= 35 && 135 >= d ? "down" : "up" : "vertical"
    }, b.prototype.swipeEnd = function(a) {
        var c, d, b = this;
        if (b.dragging = !1, b.interrupted = !1, b.shouldClick = b.touchObject.swipeLength > 10 ? !1 : !0, void 0 === b.touchObject.curX) return !1;
        if (b.touchObject.edgeHit === !0 && b.$slider.trigger("edge", [b, b.swipeDirection()]), b.touchObject.swipeLength >= b.touchObject.minSwipe) {
            switch (d = b.swipeDirection()) {
                case "left":
                case "down":
                    c = b.options.swipeToSlide ? b.checkNavigable(b.currentSlide + b.getSlideCount()) : b.currentSlide + b.getSlideCount(), b.currentDirection = 0;
                    break;
                case "right":
                case "up":
                    c = b.options.swipeToSlide ? b.checkNavigable(b.currentSlide - b.getSlideCount()) : b.currentSlide - b.getSlideCount(), b.currentDirection = 1
            }
            "vertical" != d && (b.slideHandler(c), b.touchObject = {}, b.$slider.trigger("swipe", [b, d]))
        } else b.touchObject.startX !== b.touchObject.curX && (b.slideHandler(b.currentSlide), b.touchObject = {})
    }, b.prototype.swipeHandler = function(a) {
        var b = this;
        if (!(b.options.swipe === !1 || "ontouchend" in document && b.options.swipe === !1 || b.options.draggable === !1 && -1 !== a.type.indexOf("mouse"))) switch (b.touchObject.fingerCount = a.originalEvent && void 0 !== a.originalEvent.touches ? a.originalEvent.touches.length : 1, b.touchObject.minSwipe = b.listWidth / b.options.touchThreshold, b.options.verticalSwiping === !0 && (b.touchObject.minSwipe = b.listHeight / b.options.touchThreshold), a.data.action) {
            case "start":
                b.swipeStart(a);
                break;
            case "move":
                b.swipeMove(a);
                break;
            case "end":
                b.swipeEnd(a)
        }
    }, b.prototype.swipeMove = function(a) {
        var d, e, f, g, h, b = this;
        return h = void 0 !== a.originalEvent ? a.originalEvent.touches : null, !b.dragging || h && 1 !== h.length ? !1 : (d = b.getLeft(b.currentSlide), b.touchObject.curX = void 0 !== h ? h[0].pageX : a.clientX, b.touchObject.curY = void 0 !== h ? h[0].pageY : a.clientY, b.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(b.touchObject.curX - b.touchObject.startX, 2))), b.options.verticalSwiping === !0 && (b.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(b.touchObject.curY - b.touchObject.startY, 2)))), e = b.swipeDirection(), "vertical" !== e ? (void 0 !== a.originalEvent && b.touchObject.swipeLength > 4 && a.preventDefault(), g = (b.options.rtl === !1 ? 1 : -1) * (b.touchObject.curX > b.touchObject.startX ? 1 : -1), b.options.verticalSwiping === !0 && (g = b.touchObject.curY > b.touchObject.startY ? 1 : -1), f = b.touchObject.swipeLength, b.touchObject.edgeHit = !1, b.options.infinite === !1 && (0 === b.currentSlide && "right" === e || b.currentSlide >= b.getDotCount() && "left" === e) && (f = b.touchObject.swipeLength * b.options.edgeFriction, b.touchObject.edgeHit = !0), b.options.vertical === !1 ? b.swipeLeft = d + f * g : b.swipeLeft = d + f * (b.$list.height() / b.listWidth) * g, b.options.verticalSwiping === !0 && (b.swipeLeft = d + f * g), b.options.fade === !0 || b.options.touchMove === !1 ? !1 : b.animating === !0 ? (b.swipeLeft = null, !1) : void b.setCSS(b.swipeLeft)) : void 0)
    }, b.prototype.swipeStart = function(a) {
        var c, b = this;
        return b.interrupted = !0, 1 !== b.touchObject.fingerCount || b.slideCount <= b.options.slidesToShow ? (b.touchObject = {}, !1) : (void 0 !== a.originalEvent && void 0 !== a.originalEvent.touches && (c = a.originalEvent.touches[0]), b.touchObject.startX = b.touchObject.curX = void 0 !== c ? c.pageX : a.clientX, b.touchObject.startY = b.touchObject.curY = void 0 !== c ? c.pageY : a.clientY, void(b.dragging = !0))
    }, b.prototype.unfilterSlides = b.prototype.slickUnfilter = function() {
        var a = this;
        null !== a.$slidesCache && (a.unload(), a.$slideTrack.children(this.options.slide).detach(), a.$slidesCache.appendTo(a.$slideTrack), a.reinit())
    }, b.prototype.unload = function() {
        var b = this;
        a(".slick-cloned", b.$slider).remove(), b.$dots && b.$dots.remove(), b.$prevArrow && b.htmlExpr.test(b.options.prevArrow) && b.$prevArrow.remove(), b.$nextArrow && b.htmlExpr.test(b.options.nextArrow) && b.$nextArrow.remove(), b.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden", "true").css("width", "")
    }, b.prototype.unslick = function(a) {
        var b = this;
        b.$slider.trigger("unslick", [b, a]), b.destroy()
    }, b.prototype.updateArrows = function() {
        var b, a = this;
        b = Math.floor(a.options.slidesToShow / 2), a.options.arrows === !0 && a.slideCount > a.options.slidesToShow && !a.options.infinite && (a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), a.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), 0 === a.currentSlide ? (a.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"), a.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : a.currentSlide >= a.slideCount - a.options.slidesToShow && a.options.centerMode === !1 ? (a.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : a.currentSlide >= a.slideCount - 1 && a.options.centerMode === !0 && (a.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")))
    }, b.prototype.updateDots = function() {
        var a = this;
        null !== a.$dots && (a.$dots.find("li").removeClass("slick-active").attr("aria-hidden", "true"), a.$dots.find("li").eq(Math.floor(a.currentSlide / a.options.slidesToScroll)).addClass("slick-active").attr("aria-hidden", "false"))
    }, b.prototype.visibility = function() {
        var a = this;
        a.options.autoplay && (document[a.hidden] ? a.interrupted = !0 : a.interrupted = !1)
    }, a.fn.slick = function() {
        var f, g, a = this,
            c = arguments[0],
            d = Array.prototype.slice.call(arguments, 1),
            e = a.length;
        for (f = 0; e > f; f++)
            if ("object" == typeof c || "undefined" == typeof c ? a[f].slick = new b(a[f], c) : g = a[f].slick[c].apply(a[f].slick, d), "undefined" != typeof g) return g;
        return a
    }
});
window.met_prevarrow='<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
    met_nextarrow='<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>';$(function(){
});$(function(){
    // 编辑器响应式表格
    var $meteditor_table=$(".ey-editor table");
    if($meteditor_table.length) $meteditor_table.tablexys();
    // 编辑器图片处理
    var $metEditorImg=$(".ey-editor img");
    if($metEditorImg.length){
        // 响应式图片
        Breakpoints.get('xs').on({
            enter:function(){
                var editorimg_gallery_open=true;
                // 编辑器画廊
                $(".ey-editor").each(function(){
                    if($("img",this).length && !$(this).hasClass('no-gallery')){
                        // 图片画廊参数设置
                        var $self=$(this),
                            imgsizeset=true;
                        $("img",this).one('click',function(){
                            if(imgsizeset){
                                $self.find('img').each(function(){
                                    var original=$(this).data('original'),
                                        size='500x500';
                                    if($(this).data('width')){
                                        size=$(this).data('width')+'x'+$(this).data('height');
                                    }else if($(this).attr('width') && $(this).attr('height')){
                                        size=$(this).attr('width')+'x'+$(this).attr('height');
                                    }
                                    if(!($(this).parents('a').length && $(this).parents('a').find('img').length==1)) $(this).wrapAll('<a></a>');
                                    $(this).parents('a').attr({href:original,'data-size':size,'data-med':original,'data-med-size':size});
                                });
                                imgsizeset=false;
                            }
                            if(editorimg_gallery_open){
                                $.initPhotoSwipeFromDOM('.ey-editor');
                                editorimg_gallery_open=false;
                            }
                        });
                    }
                });
            }
        })
    }
});

$(function(){
    // 详情页轮播图
    // 产品详情页、图片模块详情页共用
    var $met_img_slick=$('#ey-imgs-slick'),
        $met_img_slick_slide=$met_img_slick.find('.slick-slide');
    if($met_img_slick_slide.length>1){
        // 缩略图水平滑动
        $met_img_slick.on('init',function(event,slick){
            $met_img_slick.find('ul.slick-dots').navtabSwiper();
        })
        // 开始轮播
        var slick_lazyloadPrevNext=slick_swipe=true,
            slick_fade=slick_arrows=false;
        if(device_type=='d'){
            if($met_img_slick.hasClass('fngallery')){
                slick_lazyloadPrevNext=slick_swipe=false;
                slick_fade=true;
            }
        }
        if(!slick_swipe) $met_img_slick.addClass('slick-fade');// 如果切换效果为淡入淡出，则加上标记class
        if(device_type!='m') slick_arrows=true;
        $met_img_slick.slick({
            arrows:slick_arrows,
            dots:true,
            speed:300,
            fade:slick_fade,
            swipe:slick_swipe,
            customPaging:function(a,b) {// 缩略图html
                var $selfimg=$met_img_slick_slide.eq(b),
                    src=$selfimg.find('.lg-item-box').data('exthumbimage'),
                    alt=$selfimg.find('img').attr('alt'),
                    img_html='<img src="'+src+'" alt="'+alt+'" />';
                return img_html;
            },
            lazyloadPrevNext:slick_lazyloadPrevNext,
            prevArrow:met_prevarrow,
            nextArrow:met_nextarrow,
            adaptiveHeight: true
        })
        // 切换图片之前，判断所有图片是否被替换，如被替换，则还原
        $met_img_slick.on('beforeChange', function(event, slick, currentSlide, nextSlide){
            $met_img_slick_slide.each(function(index, el) {
                var thisimg=$('img',this),
                    thisimg_datasrc=thisimg.attr('data-src');
                if(!thisimg.attr('data-lazy') && thisimg.attr('src')!=thisimg_datasrc) thisimg.attr({src:thisimg_datasrc});
            });
        });
    }
    // 画廊加载
    var $fngallery=$('.fngallery');
    if($fngallery.length){
        var $fngalleryimg=$fngallery.find('.slick-slide img');
        if($fngalleryimg.length){
            var fngallery_open=true;
            $fngalleryimg.each(function() {
                $(this).one('click',function(){
                    if(fngallery_open){
                        if(device_type=='m'){
                            $.initPhotoSwipeFromDOM('.fngallery','.slick-slide:not(.slick-cloned) [data-med]');
                        }else{
                            $fngallery.galleryLoad();
                        }
                        fngallery_open=false;
                    }
                });
            })
        }
    }
});