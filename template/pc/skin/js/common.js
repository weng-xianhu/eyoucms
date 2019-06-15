
/*tether.min.js*/
!function(t,e){"function"==typeof define&&define.amd?define(e):"object"==typeof exports?module.exports=e(require,exports,module):t.Tether=e()}(this,function(t,e,o){"use strict";function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function i(t){var e=t.getBoundingClientRect(),o={};for(var n in e)o[n]=e[n];if(t.ownerDocument!==document){var r=t.ownerDocument.defaultView.frameElement;if(r){var s=i(r);o.top+=s.top,o.bottom+=s.top,o.left+=s.left,o.right+=s.left}}return o}function r(t){var e=getComputedStyle(t)||{},o=e.position,n=[];if("fixed"===o)return[t];for(var i=t;(i=i.parentNode)&&i&&1===i.nodeType;){var r=void 0;try{r=getComputedStyle(i)}catch(s){}if("undefined"==typeof r||null===r)return n.push(i),n;var a=r,f=a.overflow,l=a.overflowX,h=a.overflowY;/(auto|scroll)/.test(f+h+l)&&("absolute"!==o||["relative","absolute","fixed"].indexOf(r.position)>=0)&&n.push(i)}return n.push(t.ownerDocument.body),t.ownerDocument!==document&&n.push(t.ownerDocument.defaultView),n}function s(){A&&document.body.removeChild(A),A=null}function a(t){var e=void 0;t===document?(e=document,t=document.documentElement):e=t.ownerDocument;var o=e.documentElement,n=i(t),r=P();return n.top-=r.top,n.left-=r.left,"undefined"==typeof n.width&&(n.width=document.body.scrollWidth-n.left-n.right),"undefined"==typeof n.height&&(n.height=document.body.scrollHeight-n.top-n.bottom),n.top=n.top-o.clientTop,n.left=n.left-o.clientLeft,n.right=e.body.clientWidth-n.width-n.left,n.bottom=e.body.clientHeight-n.height-n.top,n}function f(t){return t.offsetParent||document.documentElement}function l(){if(M)return M;var t=document.createElement("div");t.style.width="100%",t.style.height="200px";var e=document.createElement("div");h(e.style,{position:"absolute",top:0,left:0,pointerEvents:"none",visibility:"hidden",width:"200px",height:"150px",overflow:"hidden"}),e.appendChild(t),document.body.appendChild(e);var o=t.offsetWidth;e.style.overflow="scroll";var n=t.offsetWidth;o===n&&(n=e.clientWidth),document.body.removeChild(e);var i=o-n;return M={width:i,height:i}}function h(){var t=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],e=[];return Array.prototype.push.apply(e,arguments),e.slice(1).forEach(function(e){if(e)for(var o in e)({}).hasOwnProperty.call(e,o)&&(t[o]=e[o])}),t}function d(t,e){if("undefined"!=typeof t.classList)e.split(" ").forEach(function(e){e.trim()&&t.classList.remove(e)});else{var o=new RegExp("(^| )"+e.split(" ").join("|")+"( |$)","gi"),n=c(t).replace(o," ");g(t,n)}}function u(t,e){if("undefined"!=typeof t.classList)e.split(" ").forEach(function(e){e.trim()&&t.classList.add(e)});else{d(t,e);var o=c(t)+(" "+e);g(t,o)}}function p(t,e){if("undefined"!=typeof t.classList)return t.classList.contains(e);var o=c(t);return new RegExp("(^| )"+e+"( |$)","gi").test(o)}function c(t){return t.className instanceof t.ownerDocument.defaultView.SVGAnimatedString?t.className.baseVal:t.className}function g(t,e){t.setAttribute("class",e)}function m(t,e,o){o.forEach(function(o){-1===e.indexOf(o)&&p(t,o)&&d(t,o)}),e.forEach(function(e){p(t,e)||u(t,e)})}function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function v(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function y(t,e){var o=arguments.length<=2||void 0===arguments[2]?1:arguments[2];return t+o>=e&&e>=t-o}function b(){return"undefined"!=typeof performance&&"undefined"!=typeof performance.now?performance.now():+new Date}function w(){for(var t={top:0,left:0},e=arguments.length,o=Array(e),n=0;e>n;n++)o[n]=arguments[n];return o.forEach(function(e){var o=e.top,n=e.left;"string"==typeof o&&(o=parseFloat(o,10)),"string"==typeof n&&(n=parseFloat(n,10)),t.top+=o,t.left+=n}),t}function C(t,e){return"string"==typeof t.left&&-1!==t.left.indexOf("%")&&(t.left=parseFloat(t.left,10)/100*e.width),"string"==typeof t.top&&-1!==t.top.indexOf("%")&&(t.top=parseFloat(t.top,10)/100*e.height),t}function O(t,e){return"scrollParent"===e?e=t.scrollParents[0]:"window"===e&&(e=[pageXOffset,pageYOffset,innerWidth+pageXOffset,innerHeight+pageYOffset]),e===document&&(e=e.documentElement),"undefined"!=typeof e.nodeType&&!function(){var t=e,o=a(e),n=o,i=getComputedStyle(e);if(e=[n.left,n.top,o.width+n.left,o.height+n.top],t.ownerDocument!==document){var r=t.ownerDocument.defaultView;e[0]+=r.pageXOffset,e[1]+=r.pageYOffset,e[2]+=r.pageXOffset,e[3]+=r.pageYOffset}G.forEach(function(t,o){t=t[0].toUpperCase()+t.substr(1),"Top"===t||"Left"===t?e[o]+=parseFloat(i["border"+t+"Width"]):e[o]-=parseFloat(i["border"+t+"Width"])})}(),e}var E=function(){function t(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}return function(e,o,n){return o&&t(e.prototype,o),n&&t(e,n),e}}(),x=void 0;"undefined"==typeof x&&(x={modules:[]});var A=null,T=function(){var t=0;return function(){return++t}}(),S={},P=function(){var t=A;t||(t=document.createElement("div"),t.setAttribute("data-tether-id",T()),h(t.style,{top:0,left:0,position:"absolute"}),document.body.appendChild(t),A=t);var e=t.getAttribute("data-tether-id");return"undefined"==typeof S[e]&&(S[e]=i(t),k(function(){delete S[e]})),S[e]},M=null,W=[],k=function(t){W.push(t)},_=function(){for(var t=void 0;t=W.pop();)t()},B=function(){function t(){n(this,t)}return E(t,[{key:"on",value:function(t,e,o){var n=arguments.length<=3||void 0===arguments[3]?!1:arguments[3];"undefined"==typeof this.bindings&&(this.bindings={}),"undefined"==typeof this.bindings[t]&&(this.bindings[t]=[]),this.bindings[t].push({handler:e,ctx:o,once:n})}},{key:"once",value:function(t,e,o){this.on(t,e,o,!0)}},{key:"off",value:function(t,e){if("undefined"!=typeof this.bindings&&"undefined"!=typeof this.bindings[t])if("undefined"==typeof e)delete this.bindings[t];else for(var o=0;o<this.bindings[t].length;)this.bindings[t][o].handler===e?this.bindings[t].splice(o,1):++o}},{key:"trigger",value:function(t){if("undefined"!=typeof this.bindings&&this.bindings[t]){for(var e=0,o=arguments.length,n=Array(o>1?o-1:0),i=1;o>i;i++)n[i-1]=arguments[i];for(;e<this.bindings[t].length;){var r=this.bindings[t][e],s=r.handler,a=r.ctx,f=r.once,l=a;"undefined"==typeof l&&(l=this),s.apply(l,n),f?this.bindings[t].splice(e,1):++e}}}}]),t}();x.Utils={getActualBoundingClientRect:i,getScrollParents:r,getBounds:a,getOffsetParent:f,extend:h,addClass:u,removeClass:d,hasClass:p,updateClasses:m,defer:k,flush:_,uniqueId:T,Evented:B,getScrollBarSize:l,removeUtilElements:s};var z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),E=function(){function t(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}return function(e,o,n){return o&&t(e.prototype,o),n&&t(e,n),e}}(),j=function(t,e,o){for(var n=!0;n;){var i=t,r=e,s=o;n=!1,null===i&&(i=Function.prototype);var a=Object.getOwnPropertyDescriptor(i,r);if(void 0!==a){if("value"in a)return a.value;var f=a.get;if(void 0===f)return;return f.call(s)}var l=Object.getPrototypeOf(i);if(null===l)return;t=l,e=r,o=s,n=!0,a=l=void 0}};if("undefined"==typeof x)throw new Error("You must include the utils.js file before tether.js");var Y=x.Utils,r=Y.getScrollParents,a=Y.getBounds,f=Y.getOffsetParent,h=Y.extend,u=Y.addClass,d=Y.removeClass,m=Y.updateClasses,k=Y.defer,_=Y.flush,l=Y.getScrollBarSize,s=Y.removeUtilElements,L=function(){if("undefined"==typeof document)return"";for(var t=document.createElement("div"),e=["transform","WebkitTransform","OTransform","MozTransform","msTransform"],o=0;o<e.length;++o){var n=e[o];if(void 0!==t.style[n])return n}}(),D=[],X=function(){D.forEach(function(t){t.position(!1)}),_()};!function(){var t=null,e=null,o=null,n=function i(){return"undefined"!=typeof e&&e>16?(e=Math.min(e-16,250),void(o=setTimeout(i,250))):void("undefined"!=typeof t&&b()-t<10||(null!=o&&(clearTimeout(o),o=null),t=b(),X(),e=b()-t))};"undefined"!=typeof window&&"undefined"!=typeof window.addEventListener&&["resize","scroll","touchmove"].forEach(function(t){window.addEventListener(t,n)})}();var F={center:"center",left:"right",right:"left"},H={middle:"middle",top:"bottom",bottom:"top"},N={top:0,left:0,middle:"50%",center:"50%",bottom:"100%",right:"100%"},U=function(t,e){var o=t.left,n=t.top;return"auto"===o&&(o=F[e.left]),"auto"===n&&(n=H[e.top]),{left:o,top:n}},V=function(t){var e=t.left,o=t.top;return"undefined"!=typeof N[t.left]&&(e=N[t.left]),"undefined"!=typeof N[t.top]&&(o=N[t.top]),{left:e,top:o}},R=function(t){var e=t.split(" "),o=z(e,2),n=o[0],i=o[1];return{top:n,left:i}},q=R,I=function(t){function e(t){var o=this;n(this,e),j(Object.getPrototypeOf(e.prototype),"constructor",this).call(this),this.position=this.position.bind(this),D.push(this),this.history=[],this.setOptions(t,!1),x.modules.forEach(function(t){"undefined"!=typeof t.initialize&&t.initialize.call(o)}),this.position()}return v(e,t),E(e,[{key:"getClass",value:function(){var t=arguments.length<=0||void 0===arguments[0]?"":arguments[0],e=this.options.classes;return"undefined"!=typeof e&&e[t]?this.options.classes[t]:this.options.classPrefix?this.options.classPrefix+"-"+t:t}},{key:"setOptions",value:function(t){var e=this,o=arguments.length<=1||void 0===arguments[1]?!0:arguments[1],n={offset:"0 0",targetOffset:"0 0",targetAttachment:"auto auto",classPrefix:"tether"};this.options=h(n,t);var i=this.options,s=i.element,a=i.target,f=i.targetModifier;if(this.element=s,this.target=a,this.targetModifier=f,"viewport"===this.target?(this.target=document.body,this.targetModifier="visible"):"scroll-handle"===this.target&&(this.target=document.body,this.targetModifier="scroll-handle"),["element","target"].forEach(function(t){if("undefined"==typeof e[t])throw new Error("Tether Error: Both element and target must be defined");"undefined"!=typeof e[t].jquery?e[t]=e[t][0]:"string"==typeof e[t]&&(e[t]=document.querySelector(e[t]))}),u(this.element,this.getClass("element")),this.options.addTargetClasses!==!1&&u(this.target,this.getClass("target")),!this.options.attachment)throw new Error("Tether Error: You must provide an attachment");this.targetAttachment=q(this.options.targetAttachment),this.attachment=q(this.options.attachment),this.offset=R(this.options.offset),this.targetOffset=R(this.options.targetOffset),"undefined"!=typeof this.scrollParents&&this.disable(),"scroll-handle"===this.targetModifier?this.scrollParents=[this.target]:this.scrollParents=r(this.target),this.options.enabled!==!1&&this.enable(o)}},{key:"getTargetBounds",value:function(){if("undefined"==typeof this.targetModifier)return a(this.target);if("visible"===this.targetModifier){if(this.target===document.body)return{top:pageYOffset,left:pageXOffset,height:innerHeight,width:innerWidth};var t=a(this.target),e={height:t.height,width:t.width,top:t.top,left:t.left};return e.height=Math.min(e.height,t.height-(pageYOffset-t.top)),e.height=Math.min(e.height,t.height-(t.top+t.height-(pageYOffset+innerHeight))),e.height=Math.min(innerHeight,e.height),e.height-=2,e.width=Math.min(e.width,t.width-(pageXOffset-t.left)),e.width=Math.min(e.width,t.width-(t.left+t.width-(pageXOffset+innerWidth))),e.width=Math.min(innerWidth,e.width),e.width-=2,e.top<pageYOffset&&(e.top=pageYOffset),e.left<pageXOffset&&(e.left=pageXOffset),e}if("scroll-handle"===this.targetModifier){var t=void 0,o=this.target;o===document.body?(o=document.documentElement,t={left:pageXOffset,top:pageYOffset,height:innerHeight,width:innerWidth}):t=a(o);var n=getComputedStyle(o),i=o.scrollWidth>o.clientWidth||[n.overflow,n.overflowX].indexOf("scroll")>=0||this.target!==document.body,r=0;i&&(r=15);var s=t.height-parseFloat(n.borderTopWidth)-parseFloat(n.borderBottomWidth)-r,e={width:15,height:.975*s*(s/o.scrollHeight),left:t.left+t.width-parseFloat(n.borderLeftWidth)-15},f=0;408>s&&this.target===document.body&&(f=-11e-5*Math.pow(s,2)-.00727*s+22.58),this.target!==document.body&&(e.height=Math.max(e.height,24));var l=this.target.scrollTop/(o.scrollHeight-s);return e.top=l*(s-e.height-f)+t.top+parseFloat(n.borderTopWidth),this.target===document.body&&(e.height=Math.max(e.height,24)),e}}},{key:"clearCache",value:function(){this._cache={}}},{key:"cache",value:function(t,e){return"undefined"==typeof this._cache&&(this._cache={}),"undefined"==typeof this._cache[t]&&(this._cache[t]=e.call(this)),this._cache[t]}},{key:"enable",value:function(){var t=this,e=arguments.length<=0||void 0===arguments[0]?!0:arguments[0];this.options.addTargetClasses!==!1&&u(this.target,this.getClass("enabled")),u(this.element,this.getClass("enabled")),this.enabled=!0,this.scrollParents.forEach(function(e){e!==t.target.ownerDocument&&e.addEventListener("scroll",t.position)}),e&&this.position()}},{key:"disable",value:function(){var t=this;d(this.target,this.getClass("enabled")),d(this.element,this.getClass("enabled")),this.enabled=!1,"undefined"!=typeof this.scrollParents&&this.scrollParents.forEach(function(e){e.removeEventListener("scroll",t.position)})}},{key:"destroy",value:function(){var t=this;this.disable(),D.forEach(function(e,o){e===t&&D.splice(o,1)}),0===D.length&&s()}},{key:"updateAttachClasses",value:function(t,e){var o=this;t=t||this.attachment,e=e||this.targetAttachment;var n=["left","top","bottom","right","middle","center"];"undefined"!=typeof this._addAttachClasses&&this._addAttachClasses.length&&this._addAttachClasses.splice(0,this._addAttachClasses.length),"undefined"==typeof this._addAttachClasses&&(this._addAttachClasses=[]);var i=this._addAttachClasses;t.top&&i.push(this.getClass("element-attached")+"-"+t.top),t.left&&i.push(this.getClass("element-attached")+"-"+t.left),e.top&&i.push(this.getClass("target-attached")+"-"+e.top),e.left&&i.push(this.getClass("target-attached")+"-"+e.left);var r=[];n.forEach(function(t){r.push(o.getClass("element-attached")+"-"+t),r.push(o.getClass("target-attached")+"-"+t)}),k(function(){"undefined"!=typeof o._addAttachClasses&&(m(o.element,o._addAttachClasses,r),o.options.addTargetClasses!==!1&&m(o.target,o._addAttachClasses,r),delete o._addAttachClasses)})}},{key:"position",value:function(){var t=this,e=arguments.length<=0||void 0===arguments[0]?!0:arguments[0];if(this.enabled){this.clearCache();var o=U(this.targetAttachment,this.attachment);this.updateAttachClasses(this.attachment,o);var n=this.cache("element-bounds",function(){return a(t.element)}),i=n.width,r=n.height;if(0===i&&0===r&&"undefined"!=typeof this.lastSize){var s=this.lastSize;i=s.width,r=s.height}else this.lastSize={width:i,height:r};var h=this.cache("target-bounds",function(){return t.getTargetBounds()}),d=h,u=C(V(this.attachment),{width:i,height:r}),p=C(V(o),d),c=C(this.offset,{width:i,height:r}),g=C(this.targetOffset,d);u=w(u,c),p=w(p,g);for(var m=h.left+p.left-u.left,v=h.top+p.top-u.top,y=0;y<x.modules.length;++y){var b=x.modules[y],O=b.position.call(this,{left:m,top:v,targetAttachment:o,targetPos:h,elementPos:n,offset:u,targetOffset:p,manualOffset:c,manualTargetOffset:g,scrollbarSize:S,attachment:this.attachment});if(O===!1)return!1;"undefined"!=typeof O&&"object"==typeof O&&(v=O.top,m=O.left)}var E={page:{top:v,left:m},viewport:{top:v-pageYOffset,bottom:pageYOffset-v-r+innerHeight,left:m-pageXOffset,right:pageXOffset-m-i+innerWidth}},A=this.target.ownerDocument,T=A.defaultView,S=void 0;return T.innerHeight>A.documentElement.clientHeight&&(S=this.cache("scrollbar-size",l),E.viewport.bottom-=S.height),T.innerWidth>A.documentElement.clientWidth&&(S=this.cache("scrollbar-size",l),E.viewport.right-=S.width),(-1===["","static"].indexOf(A.body.style.position)||-1===["","static"].indexOf(A.body.parentElement.style.position))&&(E.page.bottom=A.body.scrollHeight-v-r,E.page.right=A.body.scrollWidth-m-i),"undefined"!=typeof this.options.optimizations&&this.options.optimizations.moveElement!==!1&&"undefined"==typeof this.targetModifier&&!function(){var e=t.cache("target-offsetparent",function(){return f(t.target)}),o=t.cache("target-offsetparent-bounds",function(){return a(e)}),n=getComputedStyle(e),i=o,r={};if(["Top","Left","Bottom","Right"].forEach(function(t){r[t.toLowerCase()]=parseFloat(n["border"+t+"Width"])}),o.right=A.body.scrollWidth-o.left-i.width+r.right,o.bottom=A.body.scrollHeight-o.top-i.height+r.bottom,E.page.top>=o.top+r.top&&E.page.bottom>=o.bottom&&E.page.left>=o.left+r.left&&E.page.right>=o.right){var s=e.scrollTop,l=e.scrollLeft;E.offset={top:E.page.top-o.top+s-r.top,left:E.page.left-o.left+l-r.left}}}(),this.move(E),this.history.unshift(E),this.history.length>3&&this.history.pop(),e&&_(),!0}}},{key:"move",value:function(t){var e=this;if("undefined"!=typeof this.element.parentNode){var o={};for(var n in t){o[n]={};for(var i in t[n]){for(var r=!1,s=0;s<this.history.length;++s){var a=this.history[s];if("undefined"!=typeof a[n]&&!y(a[n][i],t[n][i])){r=!0;break}}r||(o[n][i]=!0)}}var l={top:"",left:"",right:"",bottom:""},d=function(t,o){var n="undefined"!=typeof e.options.optimizations,i=n?e.options.optimizations.gpu:null;if(i!==!1){var r=void 0,s=void 0;if(t.top?(l.top=0,r=o.top):(l.bottom=0,r=-o.bottom),t.left?(l.left=0,s=o.left):(l.right=0,s=-o.right),window.matchMedia){var a=window.matchMedia("only screen and (min-resolution: 1.3dppx)").matches||window.matchMedia("only screen and (-webkit-min-device-pixel-ratio: 1.3)").matches;a||(s=Math.round(s),r=Math.round(r))}l[L]="translateX("+s+"px) translateY("+r+"px)","msTransform"!==L&&(l[L]+=" translateZ(0)")}else t.top?l.top=o.top+"px":l.bottom=o.bottom+"px",t.left?l.left=o.left+"px":l.right=o.right+"px"},u=!1;if((o.page.top||o.page.bottom)&&(o.page.left||o.page.right)?(l.position="absolute",d(o.page,t.page)):(o.viewport.top||o.viewport.bottom)&&(o.viewport.left||o.viewport.right)?(l.position="fixed",d(o.viewport,t.viewport)):"undefined"!=typeof o.offset&&o.offset.top&&o.offset.left?!function(){l.position="absolute";var n=e.cache("target-offsetparent",function(){return f(e.target)});f(e.element)!==n&&k(function(){e.element.parentNode.removeChild(e.element),n.appendChild(e.element)}),d(o.offset,t.offset),u=!0}():(l.position="absolute",d({top:!0,left:!0},t.page)),!u){for(var p=!0,c=this.element.parentNode;c&&1===c.nodeType&&"BODY"!==c.tagName;){if("static"!==getComputedStyle(c).position){p=!1;break}c=c.parentNode}p||(this.element.parentNode.removeChild(this.element),this.element.ownerDocument.body.appendChild(this.element))}var g={},m=!1;for(var i in l){var v=l[i],b=this.element.style[i];b!==v&&(m=!0,g[i]=v)}m&&k(function(){h(e.element.style,g),e.trigger("repositioned")})}}}]),e}(B);I.modules=[],x.position=X;var $=h(I,x),z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),Y=x.Utils,a=Y.getBounds,h=Y.extend,m=Y.updateClasses,k=Y.defer,G=["left","top","right","bottom"];x.modules.push({position:function(t){var e=this,o=t.top,n=t.left,i=t.targetAttachment;if(!this.options.constraints)return!0;var r=this.cache("element-bounds",function(){return a(e.element)}),s=r.height,f=r.width;if(0===f&&0===s&&"undefined"!=typeof this.lastSize){var l=this.lastSize;f=l.width,s=l.height}var d=this.cache("target-bounds",function(){return e.getTargetBounds()}),u=d.height,p=d.width,c=[this.getClass("pinned"),this.getClass("out-of-bounds")];this.options.constraints.forEach(function(t){var e=t.outOfBoundsClass,o=t.pinnedClass;e&&c.push(e),o&&c.push(o)}),c.forEach(function(t){["left","top","right","bottom"].forEach(function(e){c.push(t+"-"+e)})});var g=[],v=h({},i),y=h({},this.attachment);return this.options.constraints.forEach(function(t){var r=t.to,a=t.attachment,l=t.pin;"undefined"==typeof a&&(a="");var h=void 0,d=void 0;if(a.indexOf(" ")>=0){var c=a.split(" "),m=z(c,2);d=m[0],h=m[1]}else h=d=a;var b=O(e,r);("target"===d||"both"===d)&&(o<b[1]&&"top"===v.top&&(o+=u,v.top="bottom"),o+s>b[3]&&"bottom"===v.top&&(o-=u,v.top="top")),"together"===d&&("top"===v.top&&("bottom"===y.top&&o<b[1]?(o+=u,v.top="bottom",o+=s,y.top="top"):"top"===y.top&&o+s>b[3]&&o-(s-u)>=b[1]&&(o-=s-u,v.top="bottom",y.top="bottom")),"bottom"===v.top&&("top"===y.top&&o+s>b[3]?(o-=u,v.top="top",o-=s,y.top="bottom"):"bottom"===y.top&&o<b[1]&&o+(2*s-u)<=b[3]&&(o+=s-u,v.top="top",y.top="top")),"middle"===v.top&&(o+s>b[3]&&"top"===y.top?(o-=s,y.top="bottom"):o<b[1]&&"bottom"===y.top&&(o+=s,y.top="top"))),("target"===h||"both"===h)&&(n<b[0]&&"left"===v.left&&(n+=p,v.left="right"),n+f>b[2]&&"right"===v.left&&(n-=p,v.left="left")),"together"===h&&(n<b[0]&&"left"===v.left?"right"===y.left?(n+=p,v.left="right",n+=f,y.left="left"):"left"===y.left&&(n+=p,v.left="right",n-=f,y.left="right"):n+f>b[2]&&"right"===v.left?"left"===y.left?(n-=p,v.left="left",n-=f,y.left="right"):"right"===y.left&&(n-=p,v.left="left",n+=f,y.left="left"):"center"===v.left&&(n+f>b[2]&&"left"===y.left?(n-=f,y.left="right"):n<b[0]&&"right"===y.left&&(n+=f,y.left="left"))),("element"===d||"both"===d)&&(o<b[1]&&"bottom"===y.top&&(o+=s,y.top="top"),o+s>b[3]&&"top"===y.top&&(o-=s,y.top="bottom")),("element"===h||"both"===h)&&(n<b[0]&&("right"===y.left?(n+=f,y.left="left"):"center"===y.left&&(n+=f/2,y.left="left")),n+f>b[2]&&("left"===y.left?(n-=f,y.left="right"):"center"===y.left&&(n-=f/2,y.left="right"))),"string"==typeof l?l=l.split(",").map(function(t){return t.trim()}):l===!0&&(l=["top","left","right","bottom"]),l=l||[];var w=[],C=[];o<b[1]&&(l.indexOf("top")>=0?(o=b[1],w.push("top")):C.push("top")),o+s>b[3]&&(l.indexOf("bottom")>=0?(o=b[3]-s,w.push("bottom")):C.push("bottom")),n<b[0]&&(l.indexOf("left")>=0?(n=b[0],w.push("left")):C.push("left")),n+f>b[2]&&(l.indexOf("right")>=0?(n=b[2]-f,w.push("right")):C.push("right")),w.length&&!function(){var t=void 0;t="undefined"!=typeof e.options.pinnedClass?e.options.pinnedClass:e.getClass("pinned"),g.push(t),w.forEach(function(e){g.push(t+"-"+e)})}(),C.length&&!function(){var t=void 0;t="undefined"!=typeof e.options.outOfBoundsClass?e.options.outOfBoundsClass:e.getClass("out-of-bounds"),g.push(t),C.forEach(function(e){g.push(t+"-"+e)})}(),(w.indexOf("left")>=0||w.indexOf("right")>=0)&&(y.left=v.left=!1),(w.indexOf("top")>=0||w.indexOf("bottom")>=0)&&(y.top=v.top=!1),(v.top!==i.top||v.left!==i.left||y.top!==e.attachment.top||y.left!==e.attachment.left)&&(e.updateAttachClasses(y,v),e.trigger("update",{attachment:y,targetAttachment:v}))}),k(function(){e.options.addTargetClasses!==!1&&m(e.target,g,c),m(e.element,g,c)}),{top:o,left:n}}});var Y=x.Utils,a=Y.getBounds,m=Y.updateClasses,k=Y.defer;x.modules.push({position:function(t){var e=this,o=t.top,n=t.left,i=this.cache("element-bounds",function(){return a(e.element)}),r=i.height,s=i.width,f=this.getTargetBounds(),l=o+r,h=n+s,d=[];o<=f.bottom&&l>=f.top&&["left","right"].forEach(function(t){var e=f[t];(e===n||e===h)&&d.push(t)}),n<=f.right&&h>=f.left&&["top","bottom"].forEach(function(t){var e=f[t];(e===o||e===l)&&d.push(t)});var u=[],p=[],c=["left","top","right","bottom"];return u.push(this.getClass("abutted")),c.forEach(function(t){u.push(e.getClass("abutted")+"-"+t)}),d.length&&p.push(this.getClass("abutted")),d.forEach(function(t){p.push(e.getClass("abutted")+"-"+t)}),k(function(){e.options.addTargetClasses!==!1&&m(e.target,p,u),m(e.element,p,u)}),!0}});var z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}();return x.modules.push({position:function(t){var e=t.top,o=t.left;if(this.options.shift){var n=this.options.shift;"function"==typeof this.options.shift&&(n=this.options.shift.call(this,{top:e,left:o}));var i=void 0,r=void 0;if("string"==typeof n){n=n.split(" "),n[1]=n[1]||n[0];var s=n,a=z(s,2);i=a[0],r=a[1],i=parseFloat(i,10),r=parseFloat(r,10)}else i=n.top,r=n.left;return e+=i,o+=r,{top:e,left:o}}}}),$});

/*bootstrap.min.js*/
/*!
 * Bootstrap v4.0.0-alpha.4 (http://getbootstrap.com)
 * Copyright 2011-2016 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1||b[0]>=4)throw new Error("Bootstrap's JavaScript requires at least jQuery v1.9.1 but less than v4.0.0")}(jQuery),+function(a){"use strict";function b(a,b){if("function"!=typeof b&&null!==b)throw new TypeError("Super expression must either be null or a function, not "+typeof b);a.prototype=Object.create(b&&b.prototype,{constructor:{value:a,enumerable:!1,writable:!0,configurable:!0}}),b&&(Object.setPrototypeOf?Object.setPrototypeOf(a,b):a.__proto__=b)}function c(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}var d=function(a,b,c){for(var d=!0;d;){var e=a,f=b,g=c;d=!1,null===e&&(e=Function.prototype);var h=Object.getOwnPropertyDescriptor(e,f);if(void 0!==h){if("value"in h)return h.value;var i=h.get;if(void 0===i)return;return i.call(g)}var j=Object.getPrototypeOf(e);if(null===j)return;a=j,b=f,c=g,d=!0,h=j=void 0}},e=function(){function a(a,b){for(var c=0;c<b.length;c++){var d=b[c];d.enumerable=d.enumerable||!1,d.configurable=!0,"value"in d&&(d.writable=!0),Object.defineProperty(a,d.key,d)}}return function(b,c,d){return c&&a(b.prototype,c),d&&a(b,d),b}}(),f=function(a){function b(a){return{}.toString.call(a).match(/\s([a-zA-Z]+)/)[1].toLowerCase()}function c(a){return(a[0]||a).nodeType}function d(){return{bindType:h.end,delegateType:h.end,handle:function(b){if(a(b.target).is(this))return b.handleObj.handler.apply(this,arguments)}}}function e(){if(window.QUnit)return!1;var a=document.createElement("bootstrap");for(var b in j)if(void 0!==a.style[b])return{end:j[b]};return!1}function f(b){var c=this,d=!1;return a(this).one(k.TRANSITION_END,function(){d=!0}),setTimeout(function(){d||k.triggerTransitionEnd(c)},b),this}function g(){h=e(),a.fn.emulateTransitionEnd=f,k.supportsTransitionEnd()&&(a.event.special[k.TRANSITION_END]=d())}var h=!1,i=1e6,j={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"},k={TRANSITION_END:"bsTransitionEnd",getUID:function(a){do a+=~~(Math.random()*i);while(document.getElementById(a));return a},getSelectorFromElement:function(a){var b=a.getAttribute("data-target");return b||(b=a.getAttribute("href")||"",b=/^#[a-z]/i.test(b)?b:null),b},reflow:function(a){new Function("bs","return bs")(a.offsetHeight)},triggerTransitionEnd:function(b){a(b).trigger(h.end)},supportsTransitionEnd:function(){return Boolean(h)},typeCheckConfig:function(a,d,e){for(var f in e)if(e.hasOwnProperty(f)){var g=e[f],h=d[f],i=void 0;if(i=h&&c(h)?"element":b(h),!new RegExp(g).test(i))throw new Error(a.toUpperCase()+": "+('Option "'+f+'" provided type "'+i+'" ')+('but expected type "'+g+'".'))}}};return g(),k}(jQuery),g=(function(a){var b="alert",d="4.0.0-alpha.4",g="bs.alert",h="."+g,i=".data-api",j=a.fn[b],k=150,l={DISMISS:'[data-dismiss="alert"]'},m={CLOSE:"close"+h,CLOSED:"closed"+h,CLICK_DATA_API:"click"+h+i},n={ALERT:"alert",FADE:"fade",IN:"in"},o=function(){function b(a){c(this,b),this._element=a}return e(b,[{key:"close",value:function(a){a=a||this._element;var b=this._getRootElement(a),c=this._triggerCloseEvent(b);c.isDefaultPrevented()||this._removeElement(b)}},{key:"dispose",value:function(){a.removeData(this._element,g),this._element=null}},{key:"_getRootElement",value:function(b){var c=f.getSelectorFromElement(b),d=!1;return c&&(d=a(c)[0]),d||(d=a(b).closest("."+n.ALERT)[0]),d}},{key:"_triggerCloseEvent",value:function(b){var c=a.Event(m.CLOSE);return a(b).trigger(c),c}},{key:"_removeElement",value:function(b){return a(b).removeClass(n.IN),f.supportsTransitionEnd()&&a(b).hasClass(n.FADE)?void a(b).one(f.TRANSITION_END,a.proxy(this._destroyElement,this,b)).emulateTransitionEnd(k):void this._destroyElement(b)}},{key:"_destroyElement",value:function(b){a(b).detach().trigger(m.CLOSED).remove()}}],[{key:"_jQueryInterface",value:function(c){return this.each(function(){var d=a(this),e=d.data(g);e||(e=new b(this),d.data(g,e)),"close"===c&&e[c](this)})}},{key:"_handleDismiss",value:function(a){return function(b){b&&b.preventDefault(),a.close(this)}}},{key:"VERSION",get:function(){return d}}]),b}();return a(document).on(m.CLICK_DATA_API,l.DISMISS,o._handleDismiss(new o)),a.fn[b]=o._jQueryInterface,a.fn[b].Constructor=o,a.fn[b].noConflict=function(){return a.fn[b]=j,o._jQueryInterface},o}(jQuery),function(a){var b="button",d="4.0.0-alpha.4",f="bs.button",g="."+f,h=".data-api",i=a.fn[b],j={ACTIVE:"active",BUTTON:"btn",FOCUS:"focus"},k={DATA_TOGGLE_CARROT:'[data-toggle^="button"]',DATA_TOGGLE:'[data-toggle="buttons"]',INPUT:"input",ACTIVE:".active",BUTTON:".btn"},l={CLICK_DATA_API:"click"+g+h,FOCUS_BLUR_DATA_API:"focus"+g+h+" "+("blur"+g+h)},m=function(){function b(a){c(this,b),this._element=a}return e(b,[{key:"toggle",value:function(){var b=!0,c=a(this._element).closest(k.DATA_TOGGLE)[0];if(c){var d=a(this._element).find(k.INPUT)[0];if(d){if("radio"===d.type)if(d.checked&&a(this._element).hasClass(j.ACTIVE))b=!1;else{var e=a(c).find(k.ACTIVE)[0];e&&a(e).removeClass(j.ACTIVE)}b&&(d.checked=!a(this._element).hasClass(j.ACTIVE),a(this._element).trigger("change")),d.focus()}}else this._element.setAttribute("aria-pressed",!a(this._element).hasClass(j.ACTIVE));b&&a(this._element).toggleClass(j.ACTIVE)}},{key:"dispose",value:function(){a.removeData(this._element,f),this._element=null}}],[{key:"_jQueryInterface",value:function(c){return this.each(function(){var d=a(this).data(f);d||(d=new b(this),a(this).data(f,d)),"toggle"===c&&d[c]()})}},{key:"VERSION",get:function(){return d}}]),b}();return a(document).on(l.CLICK_DATA_API,k.DATA_TOGGLE_CARROT,function(b){b.preventDefault();var c=b.target;a(c).hasClass(j.BUTTON)||(c=a(c).closest(k.BUTTON)),m._jQueryInterface.call(a(c),"toggle")}).on(l.FOCUS_BLUR_DATA_API,k.DATA_TOGGLE_CARROT,function(b){var c=a(b.target).closest(k.BUTTON)[0];a(c).toggleClass(j.FOCUS,/^focus(in)?$/.test(b.type))}),a.fn[b]=m._jQueryInterface,a.fn[b].Constructor=m,a.fn[b].noConflict=function(){return a.fn[b]=i,m._jQueryInterface},m}(jQuery),function(a){var b="carousel",d="4.0.0-alpha.4",g="bs.carousel",h="."+g,i=".data-api",j=a.fn[b],k=600,l=37,m=39,n={interval:5e3,keyboard:!0,slide:!1,pause:"hover",wrap:!0},o={interval:"(number|boolean)",keyboard:"boolean",slide:"(boolean|string)",pause:"(string|boolean)",wrap:"boolean"},p={NEXT:"next",PREVIOUS:"prev"},q={SLIDE:"slide"+h,SLID:"slid"+h,KEYDOWN:"keydown"+h,MOUSEENTER:"mouseenter"+h,MOUSELEAVE:"mouseleave"+h,LOAD_DATA_API:"load"+h+i,CLICK_DATA_API:"click"+h+i},r={CAROUSEL:"carousel",ACTIVE:"active",SLIDE:"slide",RIGHT:"right",LEFT:"left",ITEM:"carousel-item"},s={ACTIVE:".active",ACTIVE_ITEM:".active.carousel-item",ITEM:".carousel-item",NEXT_PREV:".next, .prev",INDICATORS:".carousel-indicators",DATA_SLIDE:"[data-slide], [data-slide-to]",DATA_RIDE:'[data-ride="carousel"]'},t=function(){function i(b,d){c(this,i),this._items=null,this._interval=null,this._activeElement=null,this._isPaused=!1,this._isSliding=!1,this._config=this._getConfig(d),this._element=a(b)[0],this._indicatorsElement=a(this._element).find(s.INDICATORS)[0],this._addEventListeners()}return e(i,[{key:"next",value:function(){this._isSliding||this._slide(p.NEXT)}},{key:"nextWhenVisible",value:function(){document.hidden||this.next()}},{key:"prev",value:function(){this._isSliding||this._slide(p.PREVIOUS)}},{key:"pause",value:function(b){b||(this._isPaused=!0),a(this._element).find(s.NEXT_PREV)[0]&&f.supportsTransitionEnd()&&(f.triggerTransitionEnd(this._element),this.cycle(!0)),clearInterval(this._interval),this._interval=null}},{key:"cycle",value:function(b){b||(this._isPaused=!1),this._interval&&(clearInterval(this._interval),this._interval=null),this._config.interval&&!this._isPaused&&(this._interval=setInterval(a.proxy(document.visibilityState?this.nextWhenVisible:this.next,this),this._config.interval))}},{key:"to",value:function(b){var c=this;this._activeElement=a(this._element).find(s.ACTIVE_ITEM)[0];var d=this._getItemIndex(this._activeElement);if(!(b>this._items.length-1||b<0)){if(this._isSliding)return void a(this._element).one(q.SLID,function(){return c.to(b)});if(d===b)return this.pause(),void this.cycle();var e=b>d?p.NEXT:p.PREVIOUS;this._slide(e,this._items[b])}}},{key:"dispose",value:function(){a(this._element).off(h),a.removeData(this._element,g),this._items=null,this._config=null,this._element=null,this._interval=null,this._isPaused=null,this._isSliding=null,this._activeElement=null,this._indicatorsElement=null}},{key:"_getConfig",value:function(c){return c=a.extend({},n,c),f.typeCheckConfig(b,c,o),c}},{key:"_addEventListeners",value:function(){this._config.keyboard&&a(this._element).on(q.KEYDOWN,a.proxy(this._keydown,this)),"hover"!==this._config.pause||"ontouchstart"in document.documentElement||a(this._element).on(q.MOUSEENTER,a.proxy(this.pause,this)).on(q.MOUSELEAVE,a.proxy(this.cycle,this))}},{key:"_keydown",value:function(a){if(a.preventDefault(),!/input|textarea/i.test(a.target.tagName))switch(a.which){case l:this.prev();break;case m:this.next();break;default:return}}},{key:"_getItemIndex",value:function(b){return this._items=a.makeArray(a(b).parent().find(s.ITEM)),this._items.indexOf(b)}},{key:"_getItemByDirection",value:function(a,b){var c=a===p.NEXT,d=a===p.PREVIOUS,e=this._getItemIndex(b),f=this._items.length-1,g=d&&0===e||c&&e===f;if(g&&!this._config.wrap)return b;var h=a===p.PREVIOUS?-1:1,i=(e+h)%this._items.length;return i===-1?this._items[this._items.length-1]:this._items[i]}},{key:"_triggerSlideEvent",value:function(b,c){var d=a.Event(q.SLIDE,{relatedTarget:b,direction:c});return a(this._element).trigger(d),d}},{key:"_setActiveIndicatorElement",value:function(b){if(this._indicatorsElement){a(this._indicatorsElement).find(s.ACTIVE).removeClass(r.ACTIVE);var c=this._indicatorsElement.children[this._getItemIndex(b)];c&&a(c).addClass(r.ACTIVE)}}},{key:"_slide",value:function(b,c){var d=this,e=a(this._element).find(s.ACTIVE_ITEM)[0],g=c||e&&this._getItemByDirection(b,e),h=Boolean(this._interval),i=b===p.NEXT?r.LEFT:r.RIGHT;if(g&&a(g).hasClass(r.ACTIVE))return void(this._isSliding=!1);var j=this._triggerSlideEvent(g,i);if(!j.isDefaultPrevented()&&e&&g){this._isSliding=!0,h&&this.pause(),this._setActiveIndicatorElement(g);var l=a.Event(q.SLID,{relatedTarget:g,direction:i});f.supportsTransitionEnd()&&a(this._element).hasClass(r.SLIDE)?(a(g).addClass(b),f.reflow(g),a(e).addClass(i),a(g).addClass(i),a(e).one(f.TRANSITION_END,function(){a(g).removeClass(i).removeClass(b),a(g).addClass(r.ACTIVE),a(e).removeClass(r.ACTIVE).removeClass(b).removeClass(i),d._isSliding=!1,setTimeout(function(){return a(d._element).trigger(l)},0)}).emulateTransitionEnd(k)):(a(e).removeClass(r.ACTIVE),a(g).addClass(r.ACTIVE),this._isSliding=!1,a(this._element).trigger(l)),h&&this.cycle()}}}],[{key:"_jQueryInterface",value:function(b){return this.each(function(){var c=a(this).data(g),d=a.extend({},n,a(this).data());"object"==typeof b&&a.extend(d,b);var e="string"==typeof b?b:d.slide;if(c||(c=new i(this,d),a(this).data(g,c)),"number"==typeof b)c.to(b);else if("string"==typeof e){if(void 0===c[e])throw new Error('No method named "'+e+'"');c[e]()}else d.interval&&(c.pause(),c.cycle())})}},{key:"_dataApiClickHandler",value:function(b){var c=f.getSelectorFromElement(this);if(c){var d=a(c)[0];if(d&&a(d).hasClass(r.CAROUSEL)){var e=a.extend({},a(d).data(),a(this).data()),h=this.getAttribute("data-slide-to");h&&(e.interval=!1),i._jQueryInterface.call(a(d),e),h&&a(d).data(g).to(h),b.preventDefault()}}}},{key:"VERSION",get:function(){return d}},{key:"Default",get:function(){return n}}]),i}();return a(document).on(q.CLICK_DATA_API,s.DATA_SLIDE,t._dataApiClickHandler),a(window).on(q.LOAD_DATA_API,function(){a(s.DATA_RIDE).each(function(){var b=a(this);t._jQueryInterface.call(b,b.data())})}),a.fn[b]=t._jQueryInterface,a.fn[b].Constructor=t,a.fn[b].noConflict=function(){return a.fn[b]=j,t._jQueryInterface},t}(jQuery),function(a){var b="collapse",d="4.0.0-alpha.4",g="bs.collapse",h="."+g,i=".data-api",j=a.fn[b],k=600,l={toggle:!0,parent:""},m={toggle:"boolean",parent:"string"},n={SHOW:"show"+h,SHOWN:"shown"+h,HIDE:"hide"+h,HIDDEN:"hidden"+h,CLICK_DATA_API:"click"+h+i},o={IN:"in",COLLAPSE:"collapse",COLLAPSING:"collapsing",COLLAPSED:"collapsed"},p={WIDTH:"width",HEIGHT:"height"},q={ACTIVES:".panel > .in, .panel > .collapsing",DATA_TOGGLE:'[data-toggle="collapse"]'},r=function(){function h(b,d){c(this,h),this._isTransitioning=!1,this._element=b,this._config=this._getConfig(d),this._triggerArray=a.makeArray(a('[data-toggle="collapse"][href="#'+b.id+'"],'+('[data-toggle="collapse"][data-target="#'+b.id+'"]'))),this._parent=this._config.parent?this._getParent():null,this._config.parent||this._addAriaAndCollapsedClass(this._element,this._triggerArray),this._config.toggle&&this.toggle()}return e(h,[{key:"toggle",value:function(){a(this._element).hasClass(o.IN)?this.hide():this.show()}},{key:"show",value:function(){var b=this;if(!this._isTransitioning&&!a(this._element).hasClass(o.IN)){var c=void 0,d=void 0;if(this._parent&&(c=a.makeArray(a(q.ACTIVES)),c.length||(c=null)),!(c&&(d=a(c).data(g),d&&d._isTransitioning))){var e=a.Event(n.SHOW);if(a(this._element).trigger(e),!e.isDefaultPrevented()){c&&(h._jQueryInterface.call(a(c),"hide"),d||a(c).data(g,null));var i=this._getDimension();a(this._element).removeClass(o.COLLAPSE).addClass(o.COLLAPSING),this._element.style[i]=0,this._element.setAttribute("aria-expanded",!0),this._triggerArray.length&&a(this._triggerArray).removeClass(o.COLLAPSED).attr("aria-expanded",!0),this.setTransitioning(!0);var j=function(){a(b._element).removeClass(o.COLLAPSING).addClass(o.COLLAPSE).addClass(o.IN),b._element.style[i]="",b.setTransitioning(!1),a(b._element).trigger(n.SHOWN)};if(!f.supportsTransitionEnd())return void j();var l=i[0].toUpperCase()+i.slice(1),m="scroll"+l;a(this._element).one(f.TRANSITION_END,j).emulateTransitionEnd(k),this._element.style[i]=this._element[m]+"px"}}}}},{key:"hide",value:function(){var b=this;if(!this._isTransitioning&&a(this._element).hasClass(o.IN)){var c=a.Event(n.HIDE);if(a(this._element).trigger(c),!c.isDefaultPrevented()){var d=this._getDimension(),e=d===p.WIDTH?"offsetWidth":"offsetHeight";this._element.style[d]=this._element[e]+"px",f.reflow(this._element),a(this._element).addClass(o.COLLAPSING).removeClass(o.COLLAPSE).removeClass(o.IN),this._element.setAttribute("aria-expanded",!1),this._triggerArray.length&&a(this._triggerArray).addClass(o.COLLAPSED).attr("aria-expanded",!1),this.setTransitioning(!0);var g=function(){b.setTransitioning(!1),a(b._element).removeClass(o.COLLAPSING).addClass(o.COLLAPSE).trigger(n.HIDDEN)};return this._element.style[d]=0,f.supportsTransitionEnd()?void a(this._element).one(f.TRANSITION_END,g).emulateTransitionEnd(k):void g()}}}},{key:"setTransitioning",value:function(a){this._isTransitioning=a}},{key:"dispose",value:function(){a.removeData(this._element,g),this._config=null,this._parent=null,this._element=null,this._triggerArray=null,this._isTransitioning=null}},{key:"_getConfig",value:function(c){return c=a.extend({},l,c),c.toggle=Boolean(c.toggle),f.typeCheckConfig(b,c,m),c}},{key:"_getDimension",value:function(){var b=a(this._element).hasClass(p.WIDTH);return b?p.WIDTH:p.HEIGHT}},{key:"_getParent",value:function(){var b=this,c=a(this._config.parent)[0],d='[data-toggle="collapse"][data-parent="'+this._config.parent+'"]';return a(c).find(d).each(function(a,c){b._addAriaAndCollapsedClass(h._getTargetFromElement(c),[c])}),c}},{key:"_addAriaAndCollapsedClass",value:function(b,c){if(b){var d=a(b).hasClass(o.IN);b.setAttribute("aria-expanded",d),c.length&&a(c).toggleClass(o.COLLAPSED,!d).attr("aria-expanded",d)}}}],[{key:"_getTargetFromElement",value:function(b){var c=f.getSelectorFromElement(b);return c?a(c)[0]:null}},{key:"_jQueryInterface",value:function(b){return this.each(function(){var c=a(this),d=c.data(g),e=a.extend({},l,c.data(),"object"==typeof b&&b);if(!d&&e.toggle&&/show|hide/.test(b)&&(e.toggle=!1),d||(d=new h(this,e),c.data(g,d)),"string"==typeof b){if(void 0===d[b])throw new Error('No method named "'+b+'"');d[b]()}})}},{key:"VERSION",get:function(){return d}},{key:"Default",get:function(){return l}}]),h}();return a(document).on(n.CLICK_DATA_API,q.DATA_TOGGLE,function(b){b.preventDefault();var c=r._getTargetFromElement(this),d=a(c).data(g),e=d?"toggle":a(this).data();r._jQueryInterface.call(a(c),e)}),a.fn[b]=r._jQueryInterface,a.fn[b].Constructor=r,a.fn[b].noConflict=function(){return a.fn[b]=j,r._jQueryInterface},r}(jQuery),function(a){var b="dropdown",d="4.0.0-alpha.4",g="bs.dropdown",h="."+g,i=".data-api",j=a.fn[b],k=27,l=38,m=40,n=3,o={HIDE:"hide"+h,HIDDEN:"hidden"+h,SHOW:"show"+h,SHOWN:"shown"+h,CLICK:"click"+h,CLICK_DATA_API:"click"+h+i,KEYDOWN_DATA_API:"keydown"+h+i},p={BACKDROP:"dropdown-backdrop",DISABLED:"disabled",OPEN:"open"},q={BACKDROP:".dropdown-backdrop",DATA_TOGGLE:'[data-toggle="dropdown"]',FORM_CHILD:".dropdown form",ROLE_MENU:'[role="menu"]',ROLE_LISTBOX:'[role="listbox"]',NAVBAR_NAV:".navbar-nav",VISIBLE_ITEMS:'[role="menu"] li:not(.disabled) a, [role="listbox"] li:not(.disabled) a'},r=function(){function b(a){c(this,b),this._element=a,this._addEventListeners()}return e(b,[{key:"toggle",value:function(){if(this.disabled||a(this).hasClass(p.DISABLED))return!1;var c=b._getParentFromElement(this),d=a(c).hasClass(p.OPEN);if(b._clearMenus(),d)return!1;if("ontouchstart"in document.documentElement&&!a(c).closest(q.NAVBAR_NAV).length){var e=document.createElement("div");e.className=p.BACKDROP,a(e).insertBefore(this),a(e).on("click",b._clearMenus)}var f={relatedTarget:this},g=a.Event(o.SHOW,f);return a(c).trigger(g),!g.isDefaultPrevented()&&(this.focus(),this.setAttribute("aria-expanded","true"),a(c).toggleClass(p.OPEN),a(c).trigger(a.Event(o.SHOWN,f)),!1)}},{key:"dispose",value:function(){a.removeData(this._element,g),a(this._element).off(h),this._element=null}},{key:"_addEventListeners",value:function(){a(this._element).on(o.CLICK,this.toggle)}}],[{key:"_jQueryInterface",value:function(c){return this.each(function(){var d=a(this).data(g);if(d||a(this).data(g,d=new b(this)),"string"==typeof c){if(void 0===d[c])throw new Error('No method named "'+c+'"');d[c].call(this)}})}},{key:"_clearMenus",value:function(c){if(!c||c.which!==n){var d=a(q.BACKDROP)[0];d&&d.parentNode.removeChild(d);for(var e=a.makeArray(a(q.DATA_TOGGLE)),f=0;f<e.length;f++){var g=b._getParentFromElement(e[f]),h={relatedTarget:e[f]};if(a(g).hasClass(p.OPEN)&&!(c&&"click"===c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(g,c.target))){var i=a.Event(o.HIDE,h);a(g).trigger(i),i.isDefaultPrevented()||(e[f].setAttribute("aria-expanded","false"),a(g).removeClass(p.OPEN).trigger(a.Event(o.HIDDEN,h)))}}}}},{key:"_getParentFromElement",value:function(b){var c=void 0,d=f.getSelectorFromElement(b);return d&&(c=a(d)[0]),c||b.parentNode}},{key:"_dataApiKeydownHandler",value:function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)&&(c.preventDefault(),c.stopPropagation(),!this.disabled&&!a(this).hasClass(p.DISABLED))){var d=b._getParentFromElement(this),e=a(d).hasClass(p.OPEN);if(!e&&c.which!==k||e&&c.which===k){if(c.which===k){var f=a(d).find(q.DATA_TOGGLE)[0];a(f).trigger("focus")}return void a(this).trigger("click")}var g=a.makeArray(a(q.VISIBLE_ITEMS));if(g=g.filter(function(a){return a.offsetWidth||a.offsetHeight}),g.length){var h=g.indexOf(c.target);c.which===l&&h>0&&h--,c.which===m&&h<g.length-1&&h++,h<0&&(h=0),g[h].focus()}}}},{key:"VERSION",get:function(){return d}}]),b}();return a(document).on(o.KEYDOWN_DATA_API,q.DATA_TOGGLE,r._dataApiKeydownHandler).on(o.KEYDOWN_DATA_API,q.ROLE_MENU,r._dataApiKeydownHandler).on(o.KEYDOWN_DATA_API,q.ROLE_LISTBOX,r._dataApiKeydownHandler).on(o.CLICK_DATA_API,r._clearMenus).on(o.CLICK_DATA_API,q.DATA_TOGGLE,r.prototype.toggle).on(o.CLICK_DATA_API,q.FORM_CHILD,function(a){a.stopPropagation()}),a.fn[b]=r._jQueryInterface,a.fn[b].Constructor=r,a.fn[b].noConflict=function(){return a.fn[b]=j,r._jQueryInterface},r}(jQuery),function(a){var b="modal",d="4.0.0-alpha.4",g="bs.modal",h="."+g,i=".data-api",j=a.fn[b],k=300,l=150,m=27,n={backdrop:!0,keyboard:!0,focus:!0,show:!0},o={backdrop:"(boolean|string)",keyboard:"boolean",focus:"boolean",show:"boolean"},p={HIDE:"hide"+h,HIDDEN:"hidden"+h,SHOW:"show"+h,SHOWN:"shown"+h,FOCUSIN:"focusin"+h,RESIZE:"resize"+h,CLICK_DISMISS:"click.dismiss"+h,KEYDOWN_DISMISS:"keydown.dismiss"+h,MOUSEUP_DISMISS:"mouseup.dismiss"+h,MOUSEDOWN_DISMISS:"mousedown.dismiss"+h,CLICK_DATA_API:"click"+h+i},q={SCROLLBAR_MEASURER:"modal-scrollbar-measure",BACKDROP:"modal-backdrop",OPEN:"modal-open",FADE:"fade",IN:"in"},r={DIALOG:".modal-dialog",DATA_TOGGLE:'[data-toggle="modal"]',DATA_DISMISS:'[data-dismiss="modal"]',FIXED_CONTENT:".navbar-fixed-top, .navbar-fixed-bottom, .is-fixed"},s=function(){function i(b,d){c(this,i),this._config=this._getConfig(d),this._element=b,this._dialog=a(b).find(r.DIALOG)[0],this._backdrop=null,this._isShown=!1,this._isBodyOverflowing=!1,this._ignoreBackdropClick=!1,this._originalBodyPadding=0,this._scrollbarWidth=0}return e(i,[{key:"toggle",value:function(a){return this._isShown?this.hide():this.show(a)}},{key:"show",value:function(b){var c=this,d=a.Event(p.SHOW,{relatedTarget:b});a(this._element).trigger(d),this._isShown||d.isDefaultPrevented()||(this._isShown=!0,this._checkScrollbar(),this._setScrollbar(),a(document.body).addClass(q.OPEN),this._setEscapeEvent(),this._setResizeEvent(),a(this._element).on(p.CLICK_DISMISS,r.DATA_DISMISS,a.proxy(this.hide,this)),a(this._dialog).on(p.MOUSEDOWN_DISMISS,function(){a(c._element).one(p.MOUSEUP_DISMISS,function(b){a(b.target).is(c._element)&&(c._ignoreBackdropClick=!0)})}),this._showBackdrop(a.proxy(this._showElement,this,b)))}},{key:"hide",value:function(b){b&&b.preventDefault();var c=a.Event(p.HIDE);a(this._element).trigger(c),this._isShown&&!c.isDefaultPrevented()&&(this._isShown=!1,this._setEscapeEvent(),this._setResizeEvent(),a(document).off(p.FOCUSIN),a(this._element).removeClass(q.IN),a(this._element).off(p.CLICK_DISMISS),a(this._dialog).off(p.MOUSEDOWN_DISMISS),f.supportsTransitionEnd()&&a(this._element).hasClass(q.FADE)?a(this._element).one(f.TRANSITION_END,a.proxy(this._hideModal,this)).emulateTransitionEnd(k):this._hideModal())}},{key:"dispose",value:function(){a.removeData(this._element,g),a(window).off(h),a(document).off(h),a(this._element).off(h),a(this._backdrop).off(h),this._config=null,this._element=null,this._dialog=null,this._backdrop=null,this._isShown=null,this._isBodyOverflowing=null,this._ignoreBackdropClick=null,this._originalBodyPadding=null,this._scrollbarWidth=null}},{key:"_getConfig",value:function(c){return c=a.extend({},n,c),f.typeCheckConfig(b,c,o),c}},{key:"_showElement",value:function(b){var c=this,d=f.supportsTransitionEnd()&&a(this._element).hasClass(q.FADE);this._element.parentNode&&this._element.parentNode.nodeType===Node.ELEMENT_NODE||document.body.appendChild(this._element),this._element.style.display="block",this._element.removeAttribute("aria-hidden"),this._element.scrollTop=0,d&&f.reflow(this._element),a(this._element).addClass(q.IN),this._config.focus&&this._enforceFocus();var e=a.Event(p.SHOWN,{relatedTarget:b}),g=function(){c._config.focus&&c._element.focus(),a(c._element).trigger(e)};d?a(this._dialog).one(f.TRANSITION_END,g).emulateTransitionEnd(k):g()}},{key:"_enforceFocus",value:function(){var b=this;a(document).off(p.FOCUSIN).on(p.FOCUSIN,function(c){document===c.target||b._element===c.target||a(b._element).has(c.target).length||b._element.focus()})}},{key:"_setEscapeEvent",value:function(){var b=this;this._isShown&&this._config.keyboard?a(this._element).on(p.KEYDOWN_DISMISS,function(a){a.which===m&&b.hide()}):this._isShown||a(this._element).off(p.KEYDOWN_DISMISS)}},{key:"_setResizeEvent",value:function(){this._isShown?a(window).on(p.RESIZE,a.proxy(this._handleUpdate,this)):a(window).off(p.RESIZE)}},{key:"_hideModal",value:function(){var b=this;this._element.style.display="none",this._element.setAttribute("aria-hidden","true"),this._showBackdrop(function(){a(document.body).removeClass(q.OPEN),b._resetAdjustments(),b._resetScrollbar(),a(b._element).trigger(p.HIDDEN)})}},{key:"_removeBackdrop",value:function(){this._backdrop&&(a(this._backdrop).remove(),this._backdrop=null)}},{key:"_showBackdrop",value:function(b){var c=this,d=a(this._element).hasClass(q.FADE)?q.FADE:"";if(this._isShown&&this._config.backdrop){var e=f.supportsTransitionEnd()&&d;if(this._backdrop=document.createElement("div"),this._backdrop.className=q.BACKDROP,d&&a(this._backdrop).addClass(d),a(this._backdrop).appendTo(document.body),a(this._element).on(p.CLICK_DISMISS,function(a){return c._ignoreBackdropClick?void(c._ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"===c._config.backdrop?c._element.focus():c.hide()))}),e&&f.reflow(this._backdrop),a(this._backdrop).addClass(q.IN),!b)return;if(!e)return void b();a(this._backdrop).one(f.TRANSITION_END,b).emulateTransitionEnd(l)}else if(!this._isShown&&this._backdrop){a(this._backdrop).removeClass(q.IN);var g=function(){c._removeBackdrop(),b&&b()};f.supportsTransitionEnd()&&a(this._element).hasClass(q.FADE)?a(this._backdrop).one(f.TRANSITION_END,g).emulateTransitionEnd(l):g()}else b&&b()}},{key:"_handleUpdate",value:function(){this._adjustDialog()}},{key:"_adjustDialog",value:function(){var a=this._element.scrollHeight>document.documentElement.clientHeight;!this._isBodyOverflowing&&a&&(this._element.style.paddingLeft=this._scrollbarWidth+"px"),this._isBodyOverflowing&&!a&&(this._element.style.paddingRight=this._scrollbarWidth+"px")}},{key:"_resetAdjustments",value:function(){this._element.style.paddingLeft="",this._element.style.paddingRight=""}},{key:"_checkScrollbar",value:function(){this._isBodyOverflowing=document.body.clientWidth<window.innerWidth,this._scrollbarWidth=this._getScrollbarWidth()}},{key:"_setScrollbar",value:function(){var b=parseInt(a(r.FIXED_CONTENT).css("padding-right")||0,10);this._originalBodyPadding=document.body.style.paddingRight||"",this._isBodyOverflowing&&(document.body.style.paddingRight=b+this._scrollbarWidth+"px")}},{key:"_resetScrollbar",value:function(){document.body.style.paddingRight=this._originalBodyPadding}},{key:"_getScrollbarWidth",value:function(){var a=document.createElement("div");a.className=q.SCROLLBAR_MEASURER,document.body.appendChild(a);var b=a.offsetWidth-a.clientWidth;return document.body.removeChild(a),b}}],[{key:"_jQueryInterface",value:function(b,c){return this.each(function(){var d=a(this).data(g),e=a.extend({},i.Default,a(this).data(),"object"==typeof b&&b);if(d||(d=new i(this,e),a(this).data(g,d)),"string"==typeof b){if(void 0===d[b])throw new Error('No method named "'+b+'"');d[b](c)}else e.show&&d.show(c)})}},{key:"VERSION",get:function(){return d}},{key:"Default",get:function(){return n}}]),i}();return a(document).on(p.CLICK_DATA_API,r.DATA_TOGGLE,function(b){var c=this,d=void 0,e=f.getSelectorFromElement(this);e&&(d=a(e)[0]);var h=a(d).data(g)?"toggle":a.extend({},a(d).data(),a(this).data());"A"===this.tagName&&b.preventDefault();var i=a(d).one(p.SHOW,function(b){b.isDefaultPrevented()||i.one(p.HIDDEN,function(){a(c).is(":visible")&&c.focus()})});s._jQueryInterface.call(a(d),h,this)}),a.fn[b]=s._jQueryInterface,a.fn[b].Constructor=s,a.fn[b].noConflict=function(){return a.fn[b]=j,s._jQueryInterface},s}(jQuery),function(a){var b="scrollspy",d="4.0.0-alpha.4",g="bs.scrollspy",h="."+g,i=".data-api",j=a.fn[b],k={offset:10,method:"auto",target:""},l={offset:"number",method:"string",target:"(string|element)"},m={ACTIVATE:"activate"+h,SCROLL:"scroll"+h,LOAD_DATA_API:"load"+h+i},n={DROPDOWN_ITEM:"dropdown-item",DROPDOWN_MENU:"dropdown-menu",NAV_LINK:"nav-link",NAV:"nav",ACTIVE:"active"},o={DATA_SPY:'[data-spy="scroll"]',ACTIVE:".active",LIST_ITEM:".list-item",LI:"li",LI_DROPDOWN:"li.dropdown",NAV_LINKS:".nav-link",DROPDOWN:".dropdown",DROPDOWN_ITEMS:".dropdown-item",DROPDOWN_TOGGLE:".dropdown-toggle"},p={OFFSET:"offset",POSITION:"position"},q=function(){function i(b,d){c(this,i),this._element=b,this._scrollElement="BODY"===b.tagName?window:b,this._config=this._getConfig(d),this._selector=this._config.target+" "+o.NAV_LINKS+","+(this._config.target+" "+o.DROPDOWN_ITEMS),this._offsets=[],this._targets=[],this._activeTarget=null,this._scrollHeight=0,a(this._scrollElement).on(m.SCROLL,a.proxy(this._process,this)),this.refresh(),this._process()}return e(i,[{key:"refresh",value:function(){var b=this,c=this._scrollElement!==this._scrollElement.window?p.POSITION:p.OFFSET,d="auto"===this._config.method?c:this._config.method,e=d===p.POSITION?this._getScrollTop():0;this._offsets=[],this._targets=[],this._scrollHeight=this._getScrollHeight();var g=a.makeArray(a(this._selector));g.map(function(b){var c=void 0,g=f.getSelectorFromElement(b);return g&&(c=a(g)[0]),c&&(c.offsetWidth||c.offsetHeight)?[a(c)[d]().top+e,g]:null}).filter(function(a){return a}).sort(function(a,b){return a[0]-b[0]}).forEach(function(a){b._offsets.push(a[0]),b._targets.push(a[1])})}},{key:"dispose",value:function(){a.removeData(this._element,g),a(this._scrollElement).off(h),this._element=null,this._scrollElement=null,this._config=null,this._selector=null,this._offsets=null,this._targets=null,this._activeTarget=null,this._scrollHeight=null}},{key:"_getConfig",value:function(c){if(c=a.extend({},k,c),"string"!=typeof c.target){var d=a(c.target).attr("id");d||(d=f.getUID(b),a(c.target).attr("id",d)),c.target="#"+d}return f.typeCheckConfig(b,c,l),c}},{key:"_getScrollTop",value:function(){return this._scrollElement===window?this._scrollElement.scrollY:this._scrollElement.scrollTop}},{key:"_getScrollHeight",value:function(){return this._scrollElement.scrollHeight||Math.max(document.body.scrollHeight,document.documentElement.scrollHeight)}},{key:"_process",value:function(){var a=this._getScrollTop()+this._config.offset,b=this._getScrollHeight(),c=this._config.offset+b-this._scrollElement.offsetHeight;if(this._scrollHeight!==b&&this.refresh(),a>=c){var d=this._targets[this._targets.length-1];this._activeTarget!==d&&this._activate(d)}if(this._activeTarget&&a<this._offsets[0])return this._activeTarget=null,void this._clear();for(var e=this._offsets.length;e--;){var f=this._activeTarget!==this._targets[e]&&a>=this._offsets[e]&&(void 0===this._offsets[e+1]||a<this._offsets[e+1]);f&&this._activate(this._targets[e])}}},{key:"_activate",value:function(b){this._activeTarget=b,this._clear();var c=this._selector.split(",");c=c.map(function(a){return a+'[data-target="'+b+'"],'+(a+'[href="'+b+'"]')});var d=a(c.join(","));d.hasClass(n.DROPDOWN_ITEM)?(d.closest(o.DROPDOWN).find(o.DROPDOWN_TOGGLE).addClass(n.ACTIVE),d.addClass(n.ACTIVE)):d.parents(o.LI).find(o.NAV_LINKS).addClass(n.ACTIVE),a(this._scrollElement).trigger(m.ACTIVATE,{relatedTarget:b})}},{key:"_clear",value:function(){a(this._selector).filter(o.ACTIVE).removeClass(n.ACTIVE)}}],[{key:"_jQueryInterface",value:function(b){return this.each(function(){var c=a(this).data(g),d="object"==typeof b&&b||null;if(c||(c=new i(this,d),a(this).data(g,c)),"string"==typeof b){if(void 0===c[b])throw new Error('No method named "'+b+'"');c[b]()}})}},{key:"VERSION",get:function(){return d}},{key:"Default",get:function(){return k}}]),i}();return a(window).on(m.LOAD_DATA_API,function(){for(var b=a.makeArray(a(o.DATA_SPY)),c=b.length;c--;){var d=a(b[c]);q._jQueryInterface.call(d,d.data())}}),a.fn[b]=q._jQueryInterface,a.fn[b].Constructor=q,a.fn[b].noConflict=function(){return a.fn[b]=j,q._jQueryInterface},q}(jQuery),function(a){var b="tab",d="4.0.0-alpha.4",g="bs.tab",h="."+g,i=".data-api",j=a.fn[b],k=150,l={HIDE:"hide"+h,HIDDEN:"hidden"+h,SHOW:"show"+h,SHOWN:"shown"+h,CLICK_DATA_API:"click"+h+i},m={DROPDOWN_MENU:"dropdown-menu",ACTIVE:"active",FADE:"fade",IN:"in"},n={A:"a",LI:"li",DROPDOWN:".dropdown",UL:"ul:not(.dropdown-menu)",FADE_CHILD:"> .nav-item .fade, > .fade",
ACTIVE:".active",ACTIVE_CHILD:"> .nav-item > .active, > .active",DATA_TOGGLE:'[data-toggle="tab"], [data-toggle="pill"]',DROPDOWN_TOGGLE:".dropdown-toggle",DROPDOWN_ACTIVE_CHILD:"> .dropdown-menu .active"},o=function(){function b(a){c(this,b),this._element=a}return e(b,[{key:"show",value:function(){var b=this;if(!this._element.parentNode||this._element.parentNode.nodeType!==Node.ELEMENT_NODE||!a(this._element).hasClass(m.ACTIVE)){var c=void 0,d=void 0,e=a(this._element).closest(n.UL)[0],g=f.getSelectorFromElement(this._element);e&&(d=a.makeArray(a(e).find(n.ACTIVE)),d=d[d.length-1]);var h=a.Event(l.HIDE,{relatedTarget:this._element}),i=a.Event(l.SHOW,{relatedTarget:d});if(d&&a(d).trigger(h),a(this._element).trigger(i),!i.isDefaultPrevented()&&!h.isDefaultPrevented()){g&&(c=a(g)[0]),this._activate(this._element,e);var j=function(){var c=a.Event(l.HIDDEN,{relatedTarget:b._element}),e=a.Event(l.SHOWN,{relatedTarget:d});a(d).trigger(c),a(b._element).trigger(e)};c?this._activate(c,c.parentNode,j):j()}}}},{key:"dispose",value:function(){a.removeClass(this._element,g),this._element=null}},{key:"_activate",value:function(b,c,d){var e=a(c).find(n.ACTIVE_CHILD)[0],g=d&&f.supportsTransitionEnd()&&(e&&a(e).hasClass(m.FADE)||Boolean(a(c).find(n.FADE_CHILD)[0])),h=a.proxy(this._transitionComplete,this,b,e,g,d);e&&g?a(e).one(f.TRANSITION_END,h).emulateTransitionEnd(k):h(),e&&a(e).removeClass(m.IN)}},{key:"_transitionComplete",value:function(b,c,d,e){if(c){a(c).removeClass(m.ACTIVE);var g=a(c).find(n.DROPDOWN_ACTIVE_CHILD)[0];g&&a(g).removeClass(m.ACTIVE),c.setAttribute("aria-expanded",!1)}if(a(b).addClass(m.ACTIVE),b.setAttribute("aria-expanded",!0),d?(f.reflow(b),a(b).addClass(m.IN)):a(b).removeClass(m.FADE),b.parentNode&&a(b.parentNode).hasClass(m.DROPDOWN_MENU)){var h=a(b).closest(n.DROPDOWN)[0];h&&a(h).find(n.DROPDOWN_TOGGLE).addClass(m.ACTIVE),b.setAttribute("aria-expanded",!0)}e&&e()}}],[{key:"_jQueryInterface",value:function(c){return this.each(function(){var d=a(this),e=d.data(g);if(e||(e=e=new b(this),d.data(g,e)),"string"==typeof c){if(void 0===e[c])throw new Error('No method named "'+c+'"');e[c]()}})}},{key:"VERSION",get:function(){return d}}]),b}();return a(document).on(l.CLICK_DATA_API,n.DATA_TOGGLE,function(b){b.preventDefault(),o._jQueryInterface.call(a(this),"show")}),a.fn[b]=o._jQueryInterface,a.fn[b].Constructor=o,a.fn[b].noConflict=function(){return a.fn[b]=j,o._jQueryInterface},o}(jQuery),function(a){if(void 0===window.Tether)throw new Error("Bootstrap tooltips require Tether (http://github.hubspot.com/tether/)");var b="tooltip",d="4.0.0-alpha.4",g="bs.tooltip",h="."+g,i=a.fn[b],j=150,k="bs-tether",l={animation:!0,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,selector:!1,placement:"top",offset:"0 0",constraints:[]},m={animation:"boolean",template:"string",title:"(string|element|function)",trigger:"string",delay:"(number|object)",html:"boolean",selector:"(string|boolean)",placement:"(string|function)",offset:"string",constraints:"array"},n={TOP:"bottom center",RIGHT:"middle left",BOTTOM:"top center",LEFT:"middle right"},o={IN:"in",OUT:"out"},p={HIDE:"hide"+h,HIDDEN:"hidden"+h,SHOW:"show"+h,SHOWN:"shown"+h,INSERTED:"inserted"+h,CLICK:"click"+h,FOCUSIN:"focusin"+h,FOCUSOUT:"focusout"+h,MOUSEENTER:"mouseenter"+h,MOUSELEAVE:"mouseleave"+h},q={FADE:"fade",IN:"in"},r={TOOLTIP:".tooltip",TOOLTIP_INNER:".tooltip-inner"},s={element:!1,enabled:!1},t={HOVER:"hover",FOCUS:"focus",CLICK:"click",MANUAL:"manual"},u=function(){function i(a,b){c(this,i),this._isEnabled=!0,this._timeout=0,this._hoverState="",this._activeTrigger={},this._tether=null,this.element=a,this.config=this._getConfig(b),this.tip=null,this._setListeners()}return e(i,[{key:"enable",value:function(){this._isEnabled=!0}},{key:"disable",value:function(){this._isEnabled=!1}},{key:"toggleEnabled",value:function(){this._isEnabled=!this._isEnabled}},{key:"toggle",value:function(b){if(b){var c=this.constructor.DATA_KEY,d=a(b.currentTarget).data(c);d||(d=new this.constructor(b.currentTarget,this._getDelegateConfig()),a(b.currentTarget).data(c,d)),d._activeTrigger.click=!d._activeTrigger.click,d._isWithActiveTrigger()?d._enter(null,d):d._leave(null,d)}else{if(a(this.getTipElement()).hasClass(q.IN))return void this._leave(null,this);this._enter(null,this)}}},{key:"dispose",value:function(){clearTimeout(this._timeout),this.cleanupTether(),a.removeData(this.element,this.constructor.DATA_KEY),a(this.element).off(this.constructor.EVENT_KEY),this.tip&&a(this.tip).remove(),this._isEnabled=null,this._timeout=null,this._hoverState=null,this._activeTrigger=null,this._tether=null,this.element=null,this.config=null,this.tip=null}},{key:"show",value:function(){var b=this,c=a.Event(this.constructor.Event.SHOW);if(this.isWithContent()&&this._isEnabled){a(this.element).trigger(c);var d=a.contains(this.element.ownerDocument.documentElement,this.element);if(c.isDefaultPrevented()||!d)return;var e=this.getTipElement(),g=f.getUID(this.constructor.NAME);e.setAttribute("id",g),this.element.setAttribute("aria-describedby",g),this.setContent(),this.config.animation&&a(e).addClass(q.FADE);var h="function"==typeof this.config.placement?this.config.placement.call(this,e,this.element):this.config.placement,j=this._getAttachment(h);a(e).data(this.constructor.DATA_KEY,this).appendTo(document.body),a(this.element).trigger(this.constructor.Event.INSERTED),this._tether=new Tether({attachment:j,element:e,target:this.element,classes:s,classPrefix:k,offset:this.config.offset,constraints:this.config.constraints,addTargetClasses:!1}),f.reflow(e),this._tether.position(),a(e).addClass(q.IN);var l=function(){var c=b._hoverState;b._hoverState=null,a(b.element).trigger(b.constructor.Event.SHOWN),c===o.OUT&&b._leave(null,b)};if(f.supportsTransitionEnd()&&a(this.tip).hasClass(q.FADE))return void a(this.tip).one(f.TRANSITION_END,l).emulateTransitionEnd(i._TRANSITION_DURATION);l()}}},{key:"hide",value:function(b){var c=this,d=this.getTipElement(),e=a.Event(this.constructor.Event.HIDE),g=function(){c._hoverState!==o.IN&&d.parentNode&&d.parentNode.removeChild(d),c.element.removeAttribute("aria-describedby"),a(c.element).trigger(c.constructor.Event.HIDDEN),c.cleanupTether(),b&&b()};a(this.element).trigger(e),e.isDefaultPrevented()||(a(d).removeClass(q.IN),f.supportsTransitionEnd()&&a(this.tip).hasClass(q.FADE)?a(d).one(f.TRANSITION_END,g).emulateTransitionEnd(j):g(),this._hoverState="")}},{key:"isWithContent",value:function(){return Boolean(this.getTitle())}},{key:"getTipElement",value:function(){return this.tip=this.tip||a(this.config.template)[0]}},{key:"setContent",value:function(){var b=a(this.getTipElement());this.setElementContent(b.find(r.TOOLTIP_INNER),this.getTitle()),b.removeClass(q.FADE).removeClass(q.IN),this.cleanupTether()}},{key:"setElementContent",value:function(b,c){var d=this.config.html;"object"==typeof c&&(c.nodeType||c.jquery)?d?a(c).parent().is(b)||b.empty().append(c):b.text(a(c).text()):b[d?"html":"text"](c)}},{key:"getTitle",value:function(){var a=this.element.getAttribute("data-original-title");return a||(a="function"==typeof this.config.title?this.config.title.call(this.element):this.config.title),a}},{key:"cleanupTether",value:function(){this._tether&&this._tether.destroy()}},{key:"_getAttachment",value:function(a){return n[a.toUpperCase()]}},{key:"_setListeners",value:function(){var b=this,c=this.config.trigger.split(" ");c.forEach(function(c){if("click"===c)a(b.element).on(b.constructor.Event.CLICK,b.config.selector,a.proxy(b.toggle,b));else if(c!==t.MANUAL){var d=c===t.HOVER?b.constructor.Event.MOUSEENTER:b.constructor.Event.FOCUSIN,e=c===t.HOVER?b.constructor.Event.MOUSELEAVE:b.constructor.Event.FOCUSOUT;a(b.element).on(d,b.config.selector,a.proxy(b._enter,b)).on(e,b.config.selector,a.proxy(b._leave,b))}}),this.config.selector?this.config=a.extend({},this.config,{trigger:"manual",selector:""}):this._fixTitle()}},{key:"_fixTitle",value:function(){var a=typeof this.element.getAttribute("data-original-title");(this.element.getAttribute("title")||"string"!==a)&&(this.element.setAttribute("data-original-title",this.element.getAttribute("title")||""),this.element.setAttribute("title",""))}},{key:"_enter",value:function(b,c){var d=this.constructor.DATA_KEY;return c=c||a(b.currentTarget).data(d),c||(c=new this.constructor(b.currentTarget,this._getDelegateConfig()),a(b.currentTarget).data(d,c)),b&&(c._activeTrigger["focusin"===b.type?t.FOCUS:t.HOVER]=!0),a(c.getTipElement()).hasClass(q.IN)||c._hoverState===o.IN?void(c._hoverState=o.IN):(clearTimeout(c._timeout),c._hoverState=o.IN,c.config.delay&&c.config.delay.show?void(c._timeout=setTimeout(function(){c._hoverState===o.IN&&c.show()},c.config.delay.show)):void c.show())}},{key:"_leave",value:function(b,c){var d=this.constructor.DATA_KEY;if(c=c||a(b.currentTarget).data(d),c||(c=new this.constructor(b.currentTarget,this._getDelegateConfig()),a(b.currentTarget).data(d,c)),b&&(c._activeTrigger["focusout"===b.type?t.FOCUS:t.HOVER]=!1),!c._isWithActiveTrigger())return clearTimeout(c._timeout),c._hoverState=o.OUT,c.config.delay&&c.config.delay.hide?void(c._timeout=setTimeout(function(){c._hoverState===o.OUT&&c.hide()},c.config.delay.hide)):void c.hide()}},{key:"_isWithActiveTrigger",value:function(){for(var a in this._activeTrigger)if(this._activeTrigger[a])return!0;return!1}},{key:"_getConfig",value:function(c){return c=a.extend({},this.constructor.Default,a(this.element).data(),c),c.delay&&"number"==typeof c.delay&&(c.delay={show:c.delay,hide:c.delay}),f.typeCheckConfig(b,c,this.constructor.DefaultType),c}},{key:"_getDelegateConfig",value:function(){var a={};if(this.config)for(var b in this.config)this.constructor.Default[b]!==this.config[b]&&(a[b]=this.config[b]);return a}}],[{key:"_jQueryInterface",value:function(b){return this.each(function(){var c=a(this).data(g),d="object"==typeof b?b:null;if((c||!/destroy|hide/.test(b))&&(c||(c=new i(this,d),a(this).data(g,c)),"string"==typeof b)){if(void 0===c[b])throw new Error('No method named "'+b+'"');c[b]()}})}},{key:"VERSION",get:function(){return d}},{key:"Default",get:function(){return l}},{key:"NAME",get:function(){return b}},{key:"DATA_KEY",get:function(){return g}},{key:"Event",get:function(){return p}},{key:"EVENT_KEY",get:function(){return h}},{key:"DefaultType",get:function(){return m}}]),i}();return a.fn[b]=u._jQueryInterface,a.fn[b].Constructor=u,a.fn[b].noConflict=function(){return a.fn[b]=i,u._jQueryInterface},u}(jQuery));(function(a){var f="popover",h="4.0.0-alpha.4",i="bs.popover",j="."+i,k=a.fn[f],l=a.extend({},g.Default,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),m=a.extend({},g.DefaultType,{content:"(string|element|function)"}),n={FADE:"fade",IN:"in"},o={TITLE:".popover-title",CONTENT:".popover-content",ARROW:".popover-arrow"},p={HIDE:"hide"+j,HIDDEN:"hidden"+j,SHOW:"show"+j,SHOWN:"shown"+j,INSERTED:"inserted"+j,CLICK:"click"+j,FOCUSIN:"focusin"+j,FOCUSOUT:"focusout"+j,MOUSEENTER:"mouseenter"+j,MOUSELEAVE:"mouseleave"+j},q=function(g){function k(){c(this,k),d(Object.getPrototypeOf(k.prototype),"constructor",this).apply(this,arguments)}return b(k,g),e(k,[{key:"isWithContent",value:function(){return this.getTitle()||this._getContent()}},{key:"getTipElement",value:function(){return this.tip=this.tip||a(this.config.template)[0]}},{key:"setContent",value:function(){var b=a(this.getTipElement());this.setElementContent(b.find(o.TITLE),this.getTitle()),this.setElementContent(b.find(o.CONTENT),this._getContent()),b.removeClass(n.FADE).removeClass(n.IN),this.cleanupTether()}},{key:"_getContent",value:function(){return this.element.getAttribute("data-content")||("function"==typeof this.config.content?this.config.content.call(this.element):this.config.content)}}],[{key:"_jQueryInterface",value:function(b){return this.each(function(){var c=a(this).data(i),d="object"==typeof b?b:null;if((c||!/destroy|hide/.test(b))&&(c||(c=new k(this,d),a(this).data(i,c)),"string"==typeof b)){if(void 0===c[b])throw new Error('No method named "'+b+'"');c[b]()}})}},{key:"VERSION",get:function(){return h}},{key:"Default",get:function(){return l}},{key:"NAME",get:function(){return f}},{key:"DATA_KEY",get:function(){return i}},{key:"Event",get:function(){return p}},{key:"EVENT_KEY",get:function(){return j}},{key:"DefaultType",get:function(){return m}}]),k}(g);return a.fn[f]=q._jQueryInterface,a.fn[f].Constructor=q,a.fn[f].noConflict=function(){return a.fn[f]=k,q._jQueryInterface},q})(jQuery)}(jQuery);

/*breakpoints.min.js*/
/**
* breakpoints-js v1.0.4
* https://github.com/amazingSurge/breakpoints-js
*
* Copyright (c) amazingSurge
* Released under the LGPL-3.0 license
*/
!function(t,n){if("function"==typeof define&&define.amd)define(["exports"],n);else if("undefined"!=typeof exports)n(exports);else{var e={exports:{}};n(e.exports),t.breakpoints=e.exports}}(this,function(t){"use strict";function n(t,n){if(!t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!n||"object"!=typeof n&&"function"!=typeof n?t:n}function e(t,n){if("function"!=typeof n&&null!==n)throw new TypeError("Super expression must either be null or a function, not "+typeof n);t.prototype=Object.create(n&&n.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),n&&(Object.setPrototypeOf?Object.setPrototypeOf(t,n):t.__proto__=n)}function i(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var r=function(){function t(t,n){for(var e=0;e<n.length;e++){var i=n[e];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}return function(n,e,i){return e&&t(n.prototype,e),i&&t(n,i),n}}(),o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},s={xs:{min:0,max:767},sm:{min:768,max:991},md:{min:992,max:1199},lg:{min:1200,max:1/0}},a={each:function(t,n){var e=void 0;for(var i in t)if(("object"!==("undefined"==typeof t?"undefined":o(t))||t.hasOwnProperty(i))&&(e=n(i,t[i]),e===!1))break},isFunction:function(t){return"function"==typeof t||!1},extend:function(t,n){for(var e in n)t[e]=n[e];return t}},u=function(){function t(){i(this,t),this.length=0,this.list=[]}return r(t,[{key:"add",value:function(t,n){var e=arguments.length>2&&void 0!==arguments[2]&&arguments[2];this.list.push({fn:t,data:n,one:e}),this.length++}},{key:"remove",value:function(t){for(var n=0;n<this.list.length;n++)this.list[n].fn===t&&(this.list.splice(n,1),this.length--,n--)}},{key:"empty",value:function(){this.list=[],this.length=0}},{key:"call",value:function(t,n){var e=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null;n||(n=this.length-1);var i=this.list[n];a.isFunction(e)?e.call(this,t,i,n):a.isFunction(i.fn)&&i.fn.call(t||window,i.data),i.one&&(delete this.list[n],this.length--)}},{key:"fire",value:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;for(var e in this.list)this.list.hasOwnProperty(e)&&this.call(t,e,n)}}]),t}(),f={current:null,callbacks:new u,trigger:function(t){var n=this.current;this.current=t,this.callbacks.fire(t,function(e,i){a.isFunction(i.fn)&&i.fn.call({current:t,previous:n},i.data)})},one:function(t,n){return this.on(t,n,!0)},on:function(t,n){var e=arguments.length>2&&void 0!==arguments[2]&&arguments[2];"undefined"==typeof n&&a.isFunction(t)&&(n=t,t=void 0),a.isFunction(n)&&this.callbacks.add(n,t,e)},off:function(t){"undefined"==typeof t&&this.callbacks.empty()}},c=function(){function t(n,e){i(this,t),this.name=n,this.media=e,this.initialize()}return r(t,[{key:"initialize",value:function(){this.callbacks={enter:new u,leave:new u},this.mql=window.matchMedia&&window.matchMedia(this.media)||{matches:!1,media:this.media,addListener:function(){},removeListener:function(){}};var t=this;this.mqlListener=function(n){var e=n.matches&&"enter"||"leave";t.callbacks[e].fire(t)},this.mql.addListener(this.mqlListener)}},{key:"on",value:function(t,n,e){var i=arguments.length>3&&void 0!==arguments[3]&&arguments[3];if("object"===("undefined"==typeof t?"undefined":o(t))){for(var r in t)t.hasOwnProperty(r)&&this.on(r,n,t[r],i);return this}return"undefined"==typeof e&&a.isFunction(n)&&(e=n,n=void 0),a.isFunction(e)?("undefined"!=typeof this.callbacks[t]&&(this.callbacks[t].add(e,n,i),"enter"===t&&this.isMatched()&&this.callbacks[t].call(this)),this):this}},{key:"one",value:function(t,n,e){return this.on(t,n,e,!0)}},{key:"off",value:function(t,n){var e=void 0;if("object"===("undefined"==typeof t?"undefined":o(t))){for(e in t)t.hasOwnProperty(e)&&this.off(e,t[e]);return this}return"undefined"==typeof t?(this.callbacks.enter.empty(),this.callbacks.leave.empty()):t in this.callbacks&&(n?this.callbacks[t].remove(n):this.callbacks[t].empty()),this}},{key:"isMatched",value:function(){return this.mql.matches}},{key:"destroy",value:function(){this.off()}}]),t}(),l={min:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"px";return"(min-width: "+t+n+")"},max:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"px";return"(max-width: "+t+n+")"},between:function(t,n){var e=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"px";return"(min-width: "+t+e+") and (max-width: "+n+e+")"},get:function(t,n){var e=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"px";return 0===t?this.max(n,e):n===1/0?this.min(t,e):this.between(t,n,e)}},h=function(t){function o(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:1/0,s=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"px";i(this,o);var a=l.get(e,r,s),u=n(this,(o.__proto__||Object.getPrototypeOf(o)).call(this,t,a));u.min=e,u.max=r,u.unit=s;var c=u;return u.changeListener=function(){c.isMatched()&&f.trigger(c)},u.isMatched()&&(f.current=u),u.mql.addListener(u.changeListener),u}return e(o,t),r(o,[{key:"destroy",value:function(){this.off(),this.mql.removeListener(this.changeHander)}}]),o}(c),d=function(t){function r(t){i(this,r);var e=[],o=[];return a.each(t.split(" "),function(t,n){var i=g.get(n);i&&(e.push(i),o.push(i.media))}),n(this,(r.__proto__||Object.getPrototypeOf(r)).call(this,t,o.join(",")))}return e(r,t),r}(c),v={version:"1.0.4"},p={},y={},m=window.Breakpoints=function(){for(var t=arguments.length,n=Array(t),e=0;e<t;e++)n[e]=arguments[e];m.define.apply(m,n)};m.defaults=s,m=a.extend(m,{version:v.version,defined:!1,define:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};this.defined&&this.destroy(),t||(t=m.defaults),this.options=a.extend(n,{unit:"px"});for(var e in t)t.hasOwnProperty(e)&&this.set(e,t[e].min,t[e].max,this.options.unit);this.defined=!0},destroy:function(){a.each(p,function(t,n){n.destroy()}),p={},f.current=null},is:function(t){var n=this.get(t);return n?n.isMatched():null},all:function(){var t=[];return a.each(p,function(n){t.push(n)}),t},set:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,e=arguments.length>2&&void 0!==arguments[2]?arguments[2]:1/0,i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"px",r=this.get(t);return r&&r.destroy(),p[t]=new h(t,n,e,i),p[t]},get:function(t){return p.hasOwnProperty(t)?p[t]:null},getUnion:function(t){return y.hasOwnProperty(t)?y[t]:(y[t]=new d(t),y[t])},getMin:function(t){var n=this.get(t);return n?n.min:null},getMax:function(t){var n=this.get(t);return n?n.max:null},current:function(){return f.current},getMedia:function(t){var n=this.get(t);return n?n.media:null},on:function(t,n,e,i){var r=arguments.length>4&&void 0!==arguments[4]&&arguments[4];if(t=t.trim(),"change"===t)return i=e,e=n,f.on(e,i,r);if(t.indexOf(' ')>=0){var o=this.getUnion(t);o&&o.on(n,e,i,r)}else{var s=this.get(t);s&&s.on(n,e,i,r)}return this},one:function(t,n,e,i){return this.on(t,n,e,i,!0)},off:function(t,n,e){if(t=t.trim(),"change"===t)return f.off(n);if(t.indexOf(' ')>=0){var i=this.getUnion(t);i&&i.off(n,e)}else{var r=this.get(t);r&&r.off(n,e)}return this}});var g=m;t.default=g});

/*
 *  webui popover plugin  - v1.2.17
 *  A lightWeight popover plugin with jquery ,enchance the  popover plugin of bootstrap with some awesome new features. It works well with bootstrap ,but bootstrap is not necessary!
 *  https://github.com/sandywalker/webui-popover
 *
 *  Made by Sandy Duan
 *  Under MIT License
 */
!function(a,b,c){"use strict";!function(b){"function"==typeof define&&define.amd?define(["jquery"],b):"object"==typeof exports?module.exports=b(require("jquery")):b(a.jQuery)}(function(d){function e(a,b){return this.$element=d(a),b&&("string"===d.type(b.delay)||"number"===d.type(b.delay))&&(b.delay={show:b.delay,hide:b.delay}),this.options=d.extend({},i,b),this._defaults=i,this._name=f,this._targetclick=!1,this.init(),k.push(this.$element),this}var f="webuiPopover",g="webui-popover",h="webui.popover",i={placement:"auto",container:null,width:"auto",height:"auto",trigger:"click",style:"",selector:!1,delay:{show:null,hide:300},async:{type:"GET",before:null,success:null,error:null},cache:!0,multi:!1,arrow:!0,title:"",content:"",closeable:!1,padding:!0,url:"",type:"html",direction:"",animation:null,template:'<div class="webui-popover"><div class="webui-arrow"></div><div class="webui-popover-inner"><a href="#" class="close"></a><h3 class="webui-popover-title"></h3><div class="webui-popover-content"><i class="icon-refresh"></i> <p>&nbsp;</p></div></div></div>',backdrop:!1,dismissible:!0,onShow:null,onHide:null,abortXHR:!0,autoHide:!1,offsetTop:0,offsetLeft:0,iframeOptions:{frameborder:"0",allowtransparency:"true",id:"",name:"",scrolling:"",onload:"",height:"",width:""},hideEmpty:!1},j=g+"-rtl",k=[],l=d('<div class="webui-popover-backdrop"></div>'),m=0,n=!1,o=-2e3,p=d(b),q=function(a,b){return isNaN(a)?b||0:Number(a)},r=function(a){return a.data("plugin_"+f)},s=function(){for(var a=null,b=0;b<k.length;b++)a=r(k[b]),a&&a.hide(!0);p.trigger("hiddenAll."+h)},t=function(a){for(var b=null,c=0;c<k.length;c++)b=r(k[c]),b&&b.id!==a.id&&b.hide(!0);p.trigger("hiddenAll."+h)},u="ontouchstart"in b.documentElement&&/Mobi/.test(navigator.userAgent),v=function(a){var b={x:0,y:0};if("touchstart"===a.type||"touchmove"===a.type||"touchend"===a.type||"touchcancel"===a.type){var c=a.originalEvent.touches[0]||a.originalEvent.changedTouches[0];b.x=c.pageX,b.y=c.pageY}else("mousedown"===a.type||"mouseup"===a.type||"click"===a.type)&&(b.x=a.pageX,b.y=a.pageY);return b};e.prototype={init:function(){if(this.$element[0]instanceof b.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");"manual"!==this.getTrigger()&&(u?this.$element.off("touchend",this.options.selector).on("touchend",this.options.selector,d.proxy(this.toggle,this)):"click"===this.getTrigger()?this.$element.off("click",this.options.selector).on("click",this.options.selector,d.proxy(this.toggle,this)):"hover"===this.getTrigger()&&this.$element.off("mouseenter mouseleave click",this.options.selector).on("mouseenter",this.options.selector,d.proxy(this.mouseenterHandler,this)).on("mouseleave",this.options.selector,d.proxy(this.mouseleaveHandler,this))),this._poped=!1,this._inited=!0,this._opened=!1,this._idSeed=m,this.id=f+this._idSeed,this.options.container=d(this.options.container||b.body).first(),this.options.backdrop&&l.appendTo(this.options.container).hide(),m++,"sticky"===this.getTrigger()&&this.show(),this.options.selector&&(this._options=d.extend({},this.options,{selector:""}))},destroy:function(){for(var a=-1,b=0;b<k.length;b++)if(k[b]===this.$element){a=b;break}k.splice(a,1),this.hide(),this.$element.data("plugin_"+f,null),"click"===this.getTrigger()?this.$element.off("click"):"hover"===this.getTrigger()&&this.$element.off("mouseenter mouseleave"),this.$target&&this.$target.remove()},getDelegateOptions:function(){var a={};return this._options&&d.each(this._options,function(b,c){i[b]!==c&&(a[b]=c)}),a},hide:function(a,b){if((a||"sticky"!==this.getTrigger())&&this._opened){b&&(b.preventDefault(),b.stopPropagation()),this.xhr&&this.options.abortXHR===!0&&(this.xhr.abort(),this.xhr=null);var c=d.Event("hide."+h);if(this.$element.trigger(c,[this.$target]),this.$target){this.$target.removeClass("in").addClass(this.getHideAnimation());var e=this;setTimeout(function(){e.$target.hide(),e.getCache()||e.$target.remove()},e.getHideDelay())}this.options.backdrop&&l.hide(),this._opened=!1,this.$element.trigger("hidden."+h,[this.$target]),this.options.onHide&&this.options.onHide(this.$target)}},resetAutoHide:function(){var a=this,b=a.getAutoHide();b&&(a.autoHideHandler&&clearTimeout(a.autoHideHandler),a.autoHideHandler=setTimeout(function(){a.hide()},b))},delegate:function(a){var b=d(a).data("plugin_"+f);return b||(b=new e(a,this.getDelegateOptions()),d(a).data("plugin_"+f,b)),b},toggle:function(a){var b=this;a&&(a.preventDefault(),a.stopPropagation(),this.options.selector&&(b=this.delegate(a.currentTarget))),b[b.getTarget().hasClass("in")?"hide":"show"]()},hideAll:function(){s()},hideOthers:function(){t(this)},show:function(){if(!this._opened){var a=this.getTarget().removeClass().addClass(g).addClass(this._customTargetClass);if(this.options.multi||this.hideOthers(),!this.getCache()||!this._poped||""===this.content){if(this.content="",this.setTitle(this.getTitle()),this.options.closeable||a.find(".close").off("click").remove(),this.isAsync()?this.setContentASync(this.options.content):this.setContent(this.getContent()),this.canEmptyHide()&&""===this.content)return;a.show()}this.displayContent(),this.options.onShow&&this.options.onShow(a),this.bindBodyEvents(),this.options.backdrop&&l.show(),this._opened=!0,this.resetAutoHide()}},displayContent:function(){var a=this.getElementPosition(),b=this.getTarget().removeClass().addClass(g).addClass(this._customTargetClass),c=this.getContentElement(),e=b[0].offsetWidth,f=b[0].offsetHeight,i="bottom",k=d.Event("show."+h);if(this.canEmptyHide()){var l=c.children().html();if(null!==l&&0===l.trim().length)return}this.$element.trigger(k,[b]);var m=this.$element.data("width")||this.options.width;""===m&&(m=this._defaults.width),"auto"!==m&&b.width(m);var n=this.$element.data("height")||this.options.height;""===n&&(n=this._defaults.height),"auto"!==n&&c.height(n),this.options.style&&this.$target.addClass(g+"-"+this.options.style),"rtl"!==this.options.direction||c.hasClass(j)||c.addClass(j),this.options.arrow||b.find(".webui-arrow").remove(),b.detach().css({top:o,left:o,display:"block"}),this.getAnimation()&&b.addClass(this.getAnimation()),b.appendTo(this.options.container),i=this.getPlacement(a),this.$element.trigger("added."+h),this.initTargetEvents(),this.options.padding||("auto"!==this.options.height&&c.css("height",c.outerHeight()),this.$target.addClass("webui-no-padding")),this.options.maxHeight&&c.css("maxHeight",this.options.maxHeight),this.options.maxWidth&&c.css("maxWidth",this.options.maxWidth),e=b[0].offsetWidth,f=b[0].offsetHeight;var p=this.getTargetPositin(a,i,e,f);if(this.$target.css(p.position).addClass(i).addClass("in"),"iframe"===this.options.type){var q=b.find("iframe"),r=b.width(),s=q.parent().height();""!==this.options.iframeOptions.width&&"auto"!==this.options.iframeOptions.width&&(r=this.options.iframeOptions.width),""!==this.options.iframeOptions.height&&"auto"!==this.options.iframeOptions.height&&(s=this.options.iframeOptions.height),q.width(r).height(s)}if(this.options.arrow||this.$target.css({margin:0}),this.options.arrow){var t=this.$target.find(".webui-arrow");t.removeAttr("style"),"left"===i||"right"===i?t.css({top:this.$target.height()/2}):("top"===i||"bottom"===i)&&t.css({left:this.$target.width()/2}),p.arrowOffset&&(-1===p.arrowOffset.left||-1===p.arrowOffset.top?t.hide():t.css(p.arrowOffset))}this._poped=!0,this.$element.trigger("shown."+h,[this.$target])},isTargetLoaded:function(){return 0===this.getTarget().find("i.glyphicon-refresh").length},getTriggerElement:function(){return this.$element},getTarget:function(){if(!this.$target){var a=f+this._idSeed;this.$target=d(this.options.template).attr("id",a),this._customTargetClass=this.$target.attr("class")!==g?this.$target.attr("class"):null,this.getTriggerElement().attr("data-target",a)}return this.$target.data("trigger-element")||this.$target.data("trigger-element",this.getTriggerElement()),this.$target},removeTarget:function(){this.$target.remove(),this.$target=null,this.$contentElement=null},getTitleElement:function(){return this.getTarget().find("."+g+"-title")},getContentElement:function(){return this.$contentElement||(this.$contentElement=this.getTarget().find("."+g+"-content")),this.$contentElement},getTitle:function(){return this.$element.attr("data-title")||this.options.title||this.$element.attr("title")},getUrl:function(){return this.$element.attr("data-url")||this.options.url},getAutoHide:function(){return this.$element.attr("data-auto-hide")||this.options.autoHide},getOffsetTop:function(){return q(this.$element.attr("data-offset-top"))||this.options.offsetTop},getOffsetLeft:function(){return q(this.$element.attr("data-offset-left"))||this.options.offsetLeft},getCache:function(){var a=this.$element.attr("data-cache");if("undefined"!=typeof a)switch(a.toLowerCase()){case"true":case"yes":case"1":return!0;case"false":case"no":case"0":return!1}return this.options.cache},getTrigger:function(){return this.$element.attr("data-trigger")||this.options.trigger},getDelayShow:function(){var a=this.$element.attr("data-delay-show");return"undefined"!=typeof a?a:0===this.options.delay.show?0:this.options.delay.show||100},getHideDelay:function(){var a=this.$element.attr("data-delay-hide");return"undefined"!=typeof a?a:0===this.options.delay.hide?0:this.options.delay.hide||100},getAnimation:function(){var a=this.$element.attr("data-animation");return a||this.options.animation},getHideAnimation:function(){var a=this.getAnimation();return a?a+"-out":"out"},setTitle:function(a){var b=this.getTitleElement();a?("rtl"!==this.options.direction||b.hasClass(j)||b.addClass(j),b.html(a)):b.remove()},hasContent:function(){return this.getContent()},canEmptyHide:function(){return this.options.hideEmpty&&"html"===this.options.type},getIframe:function(){var a=d("<iframe></iframe>").attr("src",this.getUrl()),b=this;return d.each(this._defaults.iframeOptions,function(c){"undefined"!=typeof b.options.iframeOptions[c]&&a.attr(c,b.options.iframeOptions[c])}),a},getContent:function(){if(this.getUrl())switch(this.options.type){case"iframe":this.content=this.getIframe();break;case"html":try{this.content=d(this.getUrl()),this.content.is(":visible")||this.content.show()}catch(a){throw new Error("Unable to get popover content. Invalid selector specified.")}}else if(!this.content){var b="";if(b=d.isFunction(this.options.content)?this.options.content.apply(this.$element[0],[this]):this.options.content,this.content=this.$element.attr("data-content")||b,!this.content){var c=this.$element.next();c&&c.hasClass(g+"-content")&&(this.content=c)}}return this.content},setContent:function(a){var b=this.getTarget(),c=this.getContentElement();"string"==typeof a?c.html(a):a instanceof d&&(c.html(""),this.options.cache?a.removeClass(g+"-content").appendTo(c):a.clone(!0,!0).removeClass(g+"-content").appendTo(c)),this.$target=b},isAsync:function(){return"async"===this.options.type},setContentASync:function(a){var b=this;this.xhr||(this.xhr=d.ajax({url:this.getUrl(),type:this.options.async.type,cache:this.getCache(),beforeSend:function(a,c){b.options.async.before&&b.options.async.before(b,a,c)},success:function(c){b.bindBodyEvents(),a&&d.isFunction(a)?b.content=a.apply(b.$element[0],[c]):b.content=c,b.setContent(b.content);var e=b.getContentElement();e.removeAttr("style"),b.displayContent(),b.options.async.success&&b.options.async.success(b,c)},complete:function(){b.xhr=null},error:function(a,c){b.options.async.error&&b.options.async.error(b,a,c)}}))},bindBodyEvents:function(){n||(this.options.dismissible&&"click"===this.getTrigger()?u?p.off("touchstart.webui-popover").on("touchstart.webui-popover",d.proxy(this.bodyTouchStartHandler,this)):(p.off("keyup.webui-popover").on("keyup.webui-popover",d.proxy(this.escapeHandler,this)),p.off("click.webui-popover").on("click.webui-popover",d.proxy(this.bodyClickHandler,this))):"hover"===this.getTrigger()&&p.off("touchend.webui-popover").on("touchend.webui-popover",d.proxy(this.bodyClickHandler,this)))},mouseenterHandler:function(a){var b=this;a&&this.options.selector&&(b=this.delegate(a.currentTarget)),b._timeout&&clearTimeout(b._timeout),b._enterTimeout=setTimeout(function(){b.getTarget().is(":visible")||b.show()},this.getDelayShow())},mouseleaveHandler:function(){var a=this;clearTimeout(a._enterTimeout),a._timeout=setTimeout(function(){a.hide()},this.getHideDelay())},escapeHandler:function(a){27===a.keyCode&&this.hideAll()},bodyTouchStartHandler:function(a){var b=this,c=d(a.currentTarget);c.on("touchend",function(a){b.bodyClickHandler(a),c.off("touchend")}),c.on("touchmove",function(){c.off("touchend")})},bodyClickHandler:function(a){n=!0;for(var b=!0,c=0;c<k.length;c++){var d=r(k[c]);if(d&&d._opened){var e=d.getTarget().offset(),f=e.left,g=e.top,h=e.left+d.getTarget().width(),i=e.top+d.getTarget().height(),j=v(a),l=j.x>=f&&j.x<=h&&j.y>=g&&j.y<=i;if(l){b=!1;break}}}b&&s()},initTargetEvents:function(){"hover"===this.getTrigger()&&this.$target.off("mouseenter mouseleave").on("mouseenter",d.proxy(this.mouseenterHandler,this)).on("mouseleave",d.proxy(this.mouseleaveHandler,this)),this.$target.find(".close").off("click").on("click",d.proxy(this.hide,this,!0))},getPlacement:function(a){var b,c=this.options.container,d=c.innerWidth(),e=c.innerHeight(),f=c.scrollTop(),g=c.scrollLeft(),h=Math.max(0,a.left-g),i=Math.max(0,a.top-f);b="function"==typeof this.options.placement?this.options.placement.call(this,this.getTarget()[0],this.$element[0]):this.$element.data("placement")||this.options.placement;var j="horizontal"===b,k="vertical"===b,l="auto"===b||j||k;return l?b=d/3>h?e/3>i?j?"right-bottom":"bottom-right":2*e/3>i?k?e/2>=i?"bottom-right":"top-right":"right":j?"right-top":"top-right":2*d/3>h?e/3>i?j?d/2>=h?"right-bottom":"left-bottom":"bottom":2*e/3>i?j?d/2>=h?"right":"left":e/2>=i?"bottom":"top":j?d/2>=h?"right-top":"left-top":"top":e/3>i?j?"left-bottom":"bottom-left":2*e/3>i?k?e/2>=i?"bottom-left":"top-left":"left":j?"left-top":"top-left":"auto-top"===b?b=d/3>h?"top-right":2*d/3>h?"top":"top-left":"auto-bottom"===b?b=d/3>h?"bottom-right":2*d/3>h?"bottom":"bottom-left":"auto-left"===b?b=e/3>i?"left-top":2*e/3>i?"left":"left-bottom":"auto-right"===b&&(b=e/3>i?"right-bottom":2*e/3>i?"right":"right-top"),b},getElementPosition:function(){var a=this.$element[0].getBoundingClientRect(),c=this.options.container,e=c.css("position");if(c.is(b.body)||"static"===e)return d.extend({},this.$element.offset(),{width:this.$element[0].offsetWidth||a.width,height:this.$element[0].offsetHeight||a.height});if("fixed"===e){var f=c[0].getBoundingClientRect();return{top:a.top-f.top+c.scrollTop(),left:a.left-f.left+c.scrollLeft(),width:a.width,height:a.height}}return"relative"===e?{top:this.$element.offset().top-c.offset().top,left:this.$element.offset().left-c.offset().left,width:this.$element[0].offsetWidth||a.width,height:this.$element[0].offsetHeight||a.height}:void 0},getTargetPositin:function(a,c,d,e){var f=a,g=this.options.container,h=this.$element.outerWidth(),i=this.$element.outerHeight(),j=b.documentElement.scrollTop+g.scrollTop(),k=b.documentElement.scrollLeft+g.scrollLeft(),l={},m=null,n=this.options.arrow?20:0,p=10,q=n+p>h?n:0,r=n+p>i?n:0,s=0,t=b.documentElement.clientHeight+j,u=b.documentElement.clientWidth+k,v=f.left+f.width/2-q>0,w=f.left+f.width/2+q<u,x=f.top+f.height/2-r>0,y=f.top+f.height/2+r<t;switch(c){case"bottom":l={top:f.top+f.height,left:f.left+f.width/2-d/2};break;case"top":l={top:f.top-e,left:f.left+f.width/2-d/2};break;case"left":l={top:f.top+f.height/2-e/2,left:f.left-d};break;case"right":l={top:f.top+f.height/2-e/2,left:f.left+f.width};break;case"top-right":l={top:f.top-e,left:v?f.left-q:p},m={left:v?Math.min(h,d)/2+q:o};break;case"top-left":s=w?q:-p,l={top:f.top-e,left:f.left-d+f.width+s},m={left:w?d-Math.min(h,d)/2-q:o};break;case"bottom-right":l={top:f.top+f.height,left:v?f.left-q:p},m={left:v?Math.min(h,d)/2+q:o};break;case"bottom-left":s=w?q:-p,l={top:f.top+f.height,left:f.left-d+f.width+s},m={left:w?d-Math.min(h,d)/2-q:o};break;case"right-top":s=y?r:-p,l={top:f.top-e+f.height+s,left:f.left+f.width},m={top:y?e-Math.min(i,e)/2-r:o};break;case"right-bottom":l={top:x?f.top-r:p,left:f.left+f.width},m={top:x?Math.min(i,e)/2+r:o};break;case"left-top":s=y?r:-p,l={top:f.top-e+f.height+s,left:f.left-d},m={top:y?e-Math.min(i,e)/2-r:o};break;case"left-bottom":l={top:x?f.top-r:p,left:f.left-d},m={top:x?Math.min(i,e)/2+r:o}}return l.top+=this.getOffsetTop(),l.left+=this.getOffsetLeft(),{position:l,arrowOffset:m}}},d.fn[f]=function(a,b){var c=[],g=this.each(function(){var g=d.data(this,"plugin_"+f);g?"destroy"===a?g.destroy():"string"==typeof a&&c.push(g[a]()):(a?"string"==typeof a?"destroy"!==a&&(b||(g=new e(this,null),c.push(g[a]()))):"object"==typeof a&&(g=new e(this,a)):g=new e(this,null),d.data(this,"plugin_"+f,g))});return c.length?c:g};var w=function(){var a=function(){s()},b=function(a,b){b=b||{},d(a).webuiPopover(b)},e=function(a){var b=!0;return d(a).each(function(a,e){b=b&&d(e).data("plugin_"+f)!==c}),b},g=function(a,b){b?d(a).webuiPopover(b).webuiPopover("show"):d(a).webuiPopover("show")},h=function(a){d(a).webuiPopover("hide")},j=function(a){i=d.extend({},i,a)},k=function(a,b){var c=d(a).data("plugin_"+f);if(c){var e=c.getCache();c.options.cache=!1,c.options.content=b,c._opened?(c._opened=!1,c.show()):c.isAsync()?c.setContentASync(b):c.setContent(b),c.options.cache=e}},l=function(a,b){var c=d(a).data("plugin_"+f);if(c){var e=c.getCache(),g=c.options.type;c.options.cache=!1,c.options.url=b,c._opened?(c._opened=!1,c.show()):(c.options.type="async",c.setContentASync(c.content)),c.options.cache=e,c.options.type=g}};return{show:g,hide:h,create:b,isCreated:e,hideAll:a,updateContent:k,updateContentAsync:l,setDefaultOptions:j}}();a.WebuiPopovers=w})}(window,document);

/*common.js*/
// 客户端判断
window.useragent=navigator.userAgent,
    useragent_tlc=useragent.toLowerCase(),
    device_type = /iPad/.test(useragent) ? 't' : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(useragent) ? 'm' : 'd',
    is_ucbro=/UC/.test(useragent),
    is_lteie9=false;
// lte IE9浏览器判断
if(new RegExp('msie').test(useragent_tlc)){
    var iebrowser_ver=(useragent_tlc.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'])[1];
    if(iebrowser_ver<10) is_lteie9=true;
}
// 延迟加载参数(模板前台用户设置)
window.met_lazyloadbg=$('input[name=met_lazyloadbg]').val()||'/template/pc/skin/images/loading.gif',
met_lazyloadbg_base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC';
if (!!window.ActiveXObject || 'ActiveXObject' in window || is_ucbro) met_lazyloadbg=met_lazyloadbg_base64;
if(typeof Breakpoints != 'undefined') Breakpoints();// 窗口宽度断点函数

$(function(){
    /*导航处理*/
   var aLink=$(".ey-nav").find('.dropdown a.nav-link');
    aLink.click(function(){
        if(!Breakpoints.is('xs')){
            if($(this).data("hover"))window.location.href = $(this).attr('href');
        }
    });
    var nav_li=$(".navlist .dropdown");
    (function($){
        $.fn.hoverDelay = function(options){
            var defaults = {
                // 鼠标经过的延时时间
                hoverDuring: 200,
                // 鼠标移出的延时时间
                outDuring: 0,
                // 鼠标经过执行的方法
                hoverEvent: function(){
                    // 设置为空函数，绑定的时候由使用者定义
                    $.noop();
                },
                // 鼠标移出执行的方法
                outEvent: function(){
                    $.noop();    
                }
            };
            var sets = $.extend(defaults,options || {});
            var hoverTimer, outTimer;
            return $(this).each(function(){
                // 保存当前上下文的this对象
                var $this = $(this)
                $this.hover(function(){
                    clearTimeout(outTimer);
                    hoverTimer = setTimeout(function () {
                        // 调用替换
                        sets.hoverEvent.apply($this);
                    }, sets.hoverDuring);
                }, function(){
                    clearTimeout(hoverTimer);
                    outTimer = setTimeout(function () {
                        sets.outEvent.apply($this);
                    }, sets.outDuring);
                });
            });
        }
    })(jQuery);
    // 具体使用，给id为“#test”的元素添加hoverEvent事件
    nav_li.hoverDelay({
        // 自定义，outEvent同
        hoverEvent: function(){
           $(this).addClass('open');
        },
        outEvent: function(){
           $(this).removeClass('open');
        }
    }).hover(function(){
        $(this).toggleClass('open');
    },function(){
        $(this).toggleClass('open', false);
    });

    // 导航下拉菜单三级栏目展开处理
    $met_navlist=$('.ey-nav .navlist');
    if(typeof(device_type) != 'undefined' && device_type=='d'){
        if($met_navlist.find('.dropdown-submenu').length){
            $met_navlist.find('.dropdown-submenu').hover(function(){
                $(this).parent('.dropdown-menu').addClass('overflow-visible');
            },function(){
                $(this).parent('.dropdown-menu').removeClass('overflow-visible');
            });
        }
    }else{
        if($met_navlist.find('.dropdown-submenu').length){
            setTimeout(function(){
                $met_navlist.find('.dropdown-submenu .dropdown-menu').addClass('block box-shadow-none').prev('.dropdown-item').addClass('dropdown-a');
            },0)
        }
    }

    /*banner只有一张的时候*/
    var bannerlen=$("#exampleCarouselDefault").find('.carousel-item').length;
    if(bannerlen<=1){
        $(".carousel-control").hide();
        $(".carousel-indicators").hide();
    }
    /*banner 自定义高度*/
    var img = $(".ey-banner").find('img').eq(0);
    function imgh(){
        if (typeof img.imageloadFun == 'function') {
            img.imageloadFun(function() {
                Breakpoints.on('md lg', {
                    enter: function() {
                         $(".carousel-item").each(function(){
                            var ph=$(this).find('img').attr('pch');//pc端
                            if(ph!=0){
                                $(this).find('img').height(ph);
                            }
                        });
                    }
                })
                Breakpoints.on('sm', {
                    enter: function() {
                         $(".carousel-item").each(function(){
                            var ah=$(this).find('img').attr('adh');//平板
                            if(ah!=0){
                                $(this).find('img').height(ah);
                            }
                        });
                    }
                })
                Breakpoints.on('xs', {
                    enter: function() {
                         $(".carousel-item").each(function(){
                            var ih=$(this).find('img').attr('iph');//手机端
                            if(ih!=0){
                                $(this).find('img').height(ih);
                            }
                        });
                    }
                })
            })
        }
    }
    imgh();
    $(window).resize(function() {
        imgh();
    });

    //简体繁体互换
    var isSimplified = true;
    $('#btn-convert').click(function() {
         if (isSimplified) {
            $('body').s2t();

            isSimplified = false;
            $(this).text('简体');
         } else {
            $('body').t2s();
            
            isSimplified = true;
            $(this).text('繁體');
         }
    });

    // 底部微信
    if($('#ey-weixin').length){
        var met_weixin=$('#ey-weixin');
        met_weixin.webuiPopover({content:'Content'});
        if(met_weixin.data('trigger')=='click'){
            met_weixin.mouseup(function(){
                $(this).click();
            });
        }
    }

    // 列表图片高度预设及删除
    var $imagesize=$('.imagesize[data-scale]');
    if($imagesize.length) $imagesize.imageSize();
    // 图片延迟加载
    if(typeof $.fn.lazyload == 'function'){
        var $original=$('[data-original]');
        if($original.length) $original.lazyload({placeholder:met_lazyloadbg});
    }
    // 内页子栏目导航水平滚动
    var $metcolumn_nav=$('.ey-column-nav-ul');
    if($metcolumn_nav.length){
        Breakpoints.on('xs',{
            enter:function(){
                $metcolumn_nav.navtabSwiper();
            }
        })
    }
    if($('[boxmh-mh]').length) $('[boxmh-mh]').boxMh('[boxmh-h]');//左右区块最小高度设置
    // 侧栏图片列表
    var $sidebar_piclist=$('.sidebar-piclist-ul');
    if($sidebar_piclist.find('.masonry-child').length>1){
        // 图片列表瀑布流
        Breakpoints.on('xs sm',{
            enter:function(){
                setTimeout(function(){
                    if (typeof $sidebar_piclist.masonry == 'function') {
                        $sidebar_piclist.masonry({itemSelector:".masonry-child"});
                    }
                },500)
            }
        });
    }
});

// 全局函数
$.fn.extend({
    // 选项卡列表水平滚动处理
    navtabSwiper:function(){
        var $self=$(this),
            $navObj_p=$(this).parents('.subcolumn-nav'),
            navtabSdefault=function(){
                if(typeof Swiper =='undefined') return false;
                var navObjW=$self.find('>li').parentWidth();
                if(navObjW>$self.parent().width()){
                    // 添加或初始化水平滚动处理
                    if($self.hasClass('swiper-wrapper')){
                        if(!$self.hasClass('flex-start')) $self.addClass('flex-start');
                    }else{
                        $self
                        .addClass("swiper-wrapper flex-start")
                        .wrap("<div class=\"swiper-container swiper-navtab\"></div>").after('<div class="swiper-scrollbar"></div>')
                        .find(">li").addClass("swiper-slide");
                        var swiperNavtab=new Swiper('.swiper-navtab',{
                            slidesPerView:'auto',
                            scrollbar:'.swiper-scrollbar',
                            scrollbarHide:false,
                            scrollbarDraggable:true
                        });
                    }
                    if($navObj_p.length && $('.product-search').length) $navObj_p.height('auto').css({'margin-bottom':10});
                    // 下拉菜单被隐藏特殊情况处理
                    if($self.find('.dropdown').length && $(".swiper-navtab").length){
                        if(!$(".swiper-navtab").hasClass('overflow-visible')) $(".swiper-navtab").addClass("overflow-visible");
                    }
                }else if($self.hasClass('flex-start')){
                    $self.removeClass('flex-start');
                    $navObj_p.css({'margin-bottom':0});
                }
        };
        navtabSdefault();
        $(window).resize(function(){
            navtabSdefault();
        })
        // 移动端下拉菜单浮动方向
        Breakpoints.on('xs sm',{
            enter:function(){
                $self.find('.dropdown-menu').each(function(){
                    if($(this).parent('li').offset().left > $(window).width()/2-$(this).parent('li').width()/2){
                        $(this).addClass('dropdown-menu-right');
                    }
                });
            }
        });
    },
    // 单张图片加载完成回调
    imageloadFunAlone:function(fun){
        var img=new Image();
        img.src=$(this).data('original') || $(this).data('lazy') || $(this).attr('src');
        if (img.complete){
            if (typeof fun==="function") fun();
            return;
        }
        img.onload=function(){
            if (typeof fun==="function") fun();
        };
    },
    // 图片加载完成回调
    imageloadFun:function(fun){
        $(this).each(function(){
            if($(this).data('lazy') || $(this).data('original')){// 图片延迟加载时
                var thisimg=$(this),
                    loadtime=setInterval(function(){
                        if(thisimg.attr('src')==thisimg.data('original') || thisimg.attr('src')==thisimg.data('lazy')){
                            clearInterval(loadtime);
                            thisimg.imageloadFunAlone(fun);
                        }
                    },100)
            }else if($(this).attr('src')){
                $(this).imageloadFunAlone(fun);
            }
        });
    },
    /**
     * imageSize 图片高度预设及删除
     * @param    {String} imgObj 目标图片类
     */
    imageSize:function(imgObj){
        var imgObj=imgObj||'img';
        $(this).each(function(){
            var scale=$(this).data('scale'),
                $self_scale=$(this),
                $img=$(imgObj,this),
                img_length=$img.length;
            if(!isNaN(scale)) scale=scale.toString();
            // 图片对象筛选
            for (var i = 0; i < img_length; i++) {
                for (var s = 0; s < $img.length; s++) {
                    if($($img[s]).parents('[data-scale]').eq(0).index('[data-scale]')!=$self_scale.index('[data-scale]')){
                        $img.splice(s,1);
                        break;
                    }
                }
                if(s==$img.length) break;
            }
            if($img.length && scale.indexOf('x')>=0){
                scale=scale.split('x');
                scale=scale[0]/scale[1];
                // 图片高度预设
                if($img.attr('src')){
                    $img.height(Math.round($img.width()*scale));
                }else{
                    var time=setInterval(function(){
                        if($img.attr('src')){
                            $img.height(Math.round($img.width()*scale));
                            clearInterval(time);
                        }
                    },30);
                }
                $(window).resize(function(){
                    $img.each(function(){
                        if($(this).is(':visible') && $(this).data('original') && $(this).attr('src')!=$(this).data('original')) $(this).height(Math.round($(this).width()*scale));
                    })
                });
                // 图片高度删除
                $img.each(function(){
                    var $self=$(this);
                    $(this).imageloadFun(function(){
                        $self.height('').removeAttr('height');
                    })
                });
            }
        });
    },
    // 父元素宽度计算
    parentWidth:function(sonNum){
        var sonTrueNum=$(this).length,
            parentObjW=0;
        if(sonNum>sonTrueNum||!sonNum) sonNum=sonTrueNum;
        $(this).each(function(index, el) {
            var sonObjW=$(this).outerWidth()+parseInt($(this).css('marginLeft'))+parseInt($(this).css('marginRight'));
            parentObjW+=sonObjW;
        });
        return parentObjW+sonNum;
    },
    /**
     * scrollFun 窗口距离触发
     * @param  {Number}  top            离窗口触发的距离
     * @param  {Boolean} loop           是否循环触发
     * @param  {Boolean} skip_invisible 是否跳过不可见元素的触发事件
     */
    scrollFun:function(fun,options){
        if (typeof fun==="function") {
            var defaults={
                    top:30,
                    loop:false,
                    skip_invisible:true,
                    is_scroll:false
                };
            $.extend(defaults,options);
            $(this).each(function(){
                var $self=$(this),
                    fun_open=true,
                    windowDistanceFun=function(){// 窗口距离触发回调
                        if(fun_open){
                            var this_t=$self.offset().top,
                                scroll_t=$(window).scrollTop(),
                                this_scroll_t=this_t-scroll_t-$(window).height(),
                                this_scroll_b=this_t+$self.outerHeight()-scroll_t,
                                visible=defaults.skip_invisible?$self.is(":visible"):true;
                            if(this_scroll_t<defaults.top && this_scroll_b>0 && visible){
                                if(!defaults.loop) fun_open=false;
                                fun($self);
                            }
                        }
                    };
                windowDistanceFun();
                // 滚动时窗口距离触发回调
                if(defaults.is_scroll){
                    $(window).scroll(function(){
                        if(fun_open) windowDistanceFun();
                    })
                }
            });
        }
    },
    /**
     * appearDiy 手动appear动画
     * @param  {Boolean} is_reset 是否重置动画
     */
    appearDiy:function(is_reset){
        $(this).each(function(){
            var $self=$(this),
                animation='animation-'+$(this).data('animate');
            if(is_reset){
                // 重置动画
                $(this).removeClass(animation).removeClass('appear-no-repeat').addClass('invisible');
            }else{
                // 执行动画
                $(this).addClass(animation).addClass('appear-no-repeat');
                setTimeout(function(){
                    $self.removeClass('invisible');
                },0)
            }
        });
    },
    /**
     * galleryLoad 画廊
     * @param  {Array} dynamic 自定义图片数组
     */
    galleryLoad:function(dynamic){
        if(typeof $.fn.lightGallery == 'undefined') return false;
        $("body").addClass("ey-lightgallery");//画廊皮肤
        if(dynamic){
            // 自定义图片数组
            $(this).lightGallery({
                loop:true,
                dynamic:true,
                dynamicEl:dynamic,
                thumbWidth:64,
                thumbContHeight:84
            });
        }else{
            // 默认加载画廊
            $(this).lightGallery({
                selector:'.lg-item-box:not(.slick-cloned)',
                exThumbImage:'data-exthumbimage',
                thumbWidth:64,
                thumbContHeight:84,
                nextHtml:'<i class="fa fa-angle-right"></i>',
                prevHtml:'<i class="fa fa-angle-left"></i>'
            });
        }
    },
    // 内页左右区块最小高度设置
    boxMh:function(boxmh_h){
        if($(this).length && $(boxmh_h).length){
            var $self=$(this),
                $boxmh_h=$(boxmh_h),
                box_mh=function(){
                    var boxmh_mh_t=$self.offset().top,
                        boxmh_h_t=$boxmh_h.offset().top,
                        mh=$boxmh_h.outerHeight();
                    if(boxmh_mh_t==boxmh_h_t){//两个区块并排时
                        if(mh!=$boxmh_h.attr('data-height')){
                            $boxmh_h.attr({'data-height':mh});
                            $self.css({'min-height':mh});
                        }
                    }else{
                        $boxmh_h.attr({'data-height':''});
                        $self.css({'min-height':''});
                    }
                };
            box_mh();
            setInterval(function(){
                box_mh();
            },50)
        }
    },
    // 表格响应式格式化
    tablexys:function(){
        var $self=$(this);
        $(this).addClass('tablesaw table-striped table-bordered table-hover tablesaw-sortable tablesaw-swipe').attr({"data-tablesaw-mode":"swipe",'data-tablesaw-sortable':''});
        Breakpoints.get('xs').on({
            enter:function(){
                $self.each(function(){
                    if(!$('thead',this).length){
                        var td=$("tbody tr:eq(0) td",this),th;
                        if(td.length==0) td=$("tbody tr:eq(0) th",this);
                        td.each(function(){
                            th+='<th data-tablesaw-sortable-col>'+$(this).html()+'</th>';
                        });
                        $(this).prepend("<thead><tr>"+th+"</tr></thead>");
                        $("tbody tr:eq(0)",this).remove();
                        $("tbody td",this).attr('width','auto');
                    }
                });
                $(document).trigger("enhance.tablesaw");
            }
        })
    }
});

/*!
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2015 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.9.7
 *
 */
(function($, window, document, undefined) {
    var $window = $(window),
        canvasPosition=function(from_dom,to_dom){ // canvas设置
            var top=from_dom.position().top,
                left=from_dom.position().left,
                width=from_dom.width(),
                height=from_dom.height();
            to_dom.css({top:top,left:left}).width(width).height(height);
        },
        canvasControl=function(dom,canvas_id){
            $.stackBlurImage(dom, canvas_id, 10, false);
            if(dom.is(':visible')) canvasPosition(dom,$('#'+canvas_id));
            $(window).resize(function() {
                if(dom.is(":visible")) canvasPosition(dom,$('#'+canvas_id));
            });
            $('#'+canvas_id).attr({'data-load':true});
        },
        thumbdir= '';//M['weburl']+'include/thumb.php?dir=';
    $.fn.lazyload = function(options) {
        var elements = this;
        var $container;
        var settings = {
            threshold       : 30,
            failure_limit   : 1000,
            event           : "scroll",
            effect          : "fadeIn",
            effect_speed    : null,
            container       : window,
            data_attribute  : "original",
            data_srcset     : 'srcset',
            skip_invisible  : true,
            appear          : null,
            load            : null,
            placeholder     : met_lazyloadbg,// 'base64',met_lazyloadbg,'blur'
        };

        function update() {
            var counter = 0;

            elements.each(function() {
                var $this = $(this),
                    $this_canvas=$this.next('canvas');
                if (settings.skip_invisible && !$this.is(":visible")) {
                    return;
                }
                if($this_canvas.length && !$this_canvas.attr('data-load') && $.stackBlurImage) canvasControl($this,$this_canvas.attr('id'));
                if ($.abovethetop(this, settings) ||
                    $.leftofbegin(this, settings)) {
                        /* Nothing. */
                } else if (!$.belowthefold(this, settings) &&
                    !$.rightoffold(this, settings)) {
                        $this.trigger("appear");
                        /* if we found an image we'll load, reset the counter */
                        counter = 0;
                } else {
                    if (++counter > settings.failure_limit) {
                        return false;
                    }
                }
            });

        }

        if(options) {
            /* Maintain BC for a couple of versions. */
            if (undefined !== options.failurelimit) {
                options.failure_limit = options.failurelimit;
                delete options.failurelimit;
            }
            if (undefined !== options.effectspeed) {
                options.effect_speed = options.effectspeed;
                delete options.effectspeed;
            }

            $.extend(settings, options);
        }

        /* Cache container as jQuery as object. */
        $container = (settings.container === undefined ||
                      settings.container === window) ? $window : $(settings.container);

        /* Fire one scroll event per scroll. Not one scroll event per image. */
        if (0 === settings.event.indexOf("scroll")) {
            $container.on(settings.event, function() {
                return update();
            });
        }
        if(settings.placeholder=='base64') settings.placeholder=met_lazyloadbg_base64;

        this.each(function(index) {
            var self = this,
                $self = $(self),
                original = $self.attr("data-" + settings.data_attribute),
                placeholder=settings.placeholder,
                placeholder_ok=placeholder!=met_lazyloadbg_base64?true:false;
            self.loaded = false;

            /* If no src attribute given use data:uri. */
            if ($self.is("img") && original && (!$self.attr("src") || $self.attr("src")!=original)) {
                if(placeholder=='blur' && $.stackBlurImage){
                    // 图片高斯模糊加载小图
                    if(!$self.attr('data-minimg')){
                        // 设置小图路径
                        var thumb=original.replace(M['weburl'],M['weburl']),
                            original_array=thumb.split('&');
                        if(thumb.indexOf('http')<0 || (thumb.indexOf('http')>=0 && thumb.indexOf(M['weburl'])>=0)){
                            if(original.indexOf('include/thumb.php?dir=')>-1){
                                var data_minimg=original_array[0]+'&x=50';
                            }else{
                                var data_minimg=thumbdir+thumb+'&x=50';
                            }
                            if(original_array && original_array.length==3){
                                scale_x=original_array[1].substring(2);
                                scale_y=original_array[2].substring(2);
                                scale=scale_y/scale_x;
                                minimg_h=Math.round(50*scale);
                                data_minimg+='&y='+minimg_h;
                            }
                            $(this).attr({src:data_minimg,'data-minimg':true});
                            // 高斯模糊小图
                            var img=new Image();
                            img.src=$self.attr("src");
                            img.onload=function(){
                                setTimeout(function(){
                                    var $self_canvas=$self.next('canvas');
                                    if($self.attr('src')!=original && !$self_canvas.length){
                                        var canvas_id="imgcanvas"+index;
                                        $self.wrapAll('<section style="position: relative;"></section>').after('<canvas id="'+canvas_id+'" data-load="false" width="'+$self.width()+'" height="'+$self.height()+'" style="position:absolute;z-index:10;"></canvas>');
                                        if(!settings.skip_invisible || $self.is(":visible")) canvasControl($self,canvas_id);
                                    }else if($self_canvas.length){
                                        canvasPosition($self,$self_canvas);
                                    }
                                },30)
                            }
                        }
                    }
                }else{
                    if(placeholder=='blur') placeholder=met_lazyloadbg;
                    $self.attr("src", placeholder);
                    if(placeholder_ok && !$self.hasClass('imgloading')) $self.addClass('imgloading');
                }
            }

            /* When appear is triggered load original image. */
            $self.one("appear", function() {
                if (!this.loaded) {
                    if (settings.appear) {
                        var elements_left = elements.length;
                        settings.appear.call(self, elements_left, settings);
                    }
                    var srcset = $self.attr("data-" + settings.data_srcset);
                    $("<img />")
                        .one("load", function() {
                        $self.hide();
                        if ($self.is("img")/* || $self.is("source") || $self.is("video") || $self.is("audio") || $self.is("iframe") || $self.is("script") || $self.is("link")*/) {
                            if(srcset) $self.attr("srcset", srcset);
                            $self.attr("src", original);
                        } else {
                            $self.css("background-image", "url('" + original + "')");
                            if(srcset) $self.css("background-image", "-webkit-image-set(" + srcset + ")");
                        }
                        $self[settings.effect](settings.effect_speed);
                        $self.one('load', function() {
                            $self.removeClass('imgloading');
                            $self.next('canvas').fadeOut("normal",function(){
                                $self.next('canvas').remove();
                            });
                        });

                        self.loaded = true;

                        /* Remove image from array so it is not looped next time. */
                        var temp = $.grep(elements, function(element) {
                            return !element.loaded;
                        });
                        elements = $(temp);

                        if (settings.load) {
                            var elements_left = elements.length;
                            settings.load.call(self, elements_left, settings);
                        }
                    }).attr({srcset:srcset,src:original}).removeClass('imgloading').next('canvas').fadeOut("normal",function(){
                        $("<img />").next('canvas').remove();
                    });
                }
            });

            /* When wanted event is triggered load original image */
            /* by triggering appear.                              */
            if (0 !== settings.event.indexOf("scroll")) {
                $self.on(settings.event, function() {
                    if (!self.loaded) {
                        $self.trigger("appear");
                    }
                });
            }
        });

        /* Check if something appears when window is resized. */
        $window.on("resize", function() {
            update();
        });

        /* With IOS5 force loading images when navigating with back button. */
        /* Non optimal workaround. */
        if ((/(?:iphone|ipod|ipad).*os 5/gi).test(navigator.appVersion)) {
            $window.on("pageshow", function(event) {
                if (event.originalEvent && event.originalEvent.persisted) {
                    elements.each(function() {
                        $(this).trigger("appear");
                    });
                }
            });
        }

        /* Force initial check if images should appear. */
        $(document).ready(function() {
            update();
        });

        return this;
    };

    /* Convenience methods in jQuery namespace.           */
    /* Use as  $.belowthefold(element, {threshold : 100, container : window}) */

    $.belowthefold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = (window.innerHeight ? window.innerHeight : $window.height()) + $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top + $(settings.container).height();
        }

        return fold <= $(element).offset().top - settings.threshold;
    };

    $.rightoffold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.width() + $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left + $(settings.container).width();
        }

        return fold <= $(element).offset().left - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top;
        }

        return fold >= $(element).offset().top + settings.threshold  + $(element).height();
    };

    $.leftofbegin = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left;
        }

        return fold >= $(element).offset().left + settings.threshold + $(element).width();
    };

    $.inviewport = function(element, settings) {
         return !$.rightoffold(element, settings) && !$.leftofbegin(element, settings) &&
                !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
     };

    /* Custom selectors for your convenience.   */
    /* Use as $("img:below-the-fold").something() or */
    /* $("img").filter(":below-the-fold").something() which is faster */

    $.extend($.expr[":"], {
        "below-the-fold" : function(a) { return $.belowthefold(a, {threshold : 0}); },
        "above-the-top"  : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-screen": function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-screen" : function(a) { return !$.rightoffold(a, {threshold : 0}); },
        "in-viewport"    : function(a) { return $.inviewport(a, {threshold : 0}); },
        /* Maintain BC for couple of versions. */
        "above-the-fold" : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-fold"  : function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-fold"   : function(a) { return !$.rightoffold(a, {threshold : 0}); }
    });

})(jQuery, window, document);

/**
 * jquery-s2t v0.1.0
 *
 * https://github.com/hustlzp/jquery-s2t
 * A jQuery plugin to convert between Simplified Chinese and Traditional Chinese.
 * Tested in IE6+, Chrome, Firefox.
 *
 * Copyright 2013-2014 hustlzp
 * Released under the MIT license
 */
(function($) {

    // 共收录2553条简繁对照
    // 尚未考证是否正确、重复、完整

    /**
     * 简体字
     * @const
     */
    var S = new String('万与丑专业丛东丝丢两严丧个丬丰临为丽举么义乌乐乔习乡书买乱争于亏云亘亚产亩亲亵亸亿仅从仑仓仪们价众优伙会伛伞伟传伤伥伦伧伪伫体余佣佥侠侣侥侦侧侨侩侪侬俣俦俨俩俪俭债倾偬偻偾偿傥傧储傩儿兑兖党兰关兴兹养兽冁内冈册写军农冢冯冲决况冻净凄凉凌减凑凛几凤凫凭凯击凼凿刍划刘则刚创删别刬刭刽刿剀剂剐剑剥剧劝办务劢动励劲劳势勋勐勚匀匦匮区医华协单卖卢卤卧卫却卺厂厅历厉压厌厍厕厢厣厦厨厩厮县参叆叇双发变叙叠叶号叹叽吁后吓吕吗吣吨听启吴呒呓呕呖呗员呙呛呜咏咔咙咛咝咤咴咸哌响哑哒哓哔哕哗哙哜哝哟唛唝唠唡唢唣唤唿啧啬啭啮啰啴啸喷喽喾嗫呵嗳嘘嘤嘱噜噼嚣嚯团园囱围囵国图圆圣圹场坂坏块坚坛坜坝坞坟坠垄垅垆垒垦垧垩垫垭垯垱垲垴埘埙埚埝埯堑堕塆墙壮声壳壶壸处备复够头夸夹夺奁奂奋奖奥妆妇妈妩妪妫姗姜娄娅娆娇娈娱娲娴婳婴婵婶媪嫒嫔嫱嬷孙学孪宁宝实宠审宪宫宽宾寝对寻导寿将尔尘尧尴尸尽层屃屉届属屡屦屿岁岂岖岗岘岙岚岛岭岳岽岿峃峄峡峣峤峥峦崂崃崄崭嵘嵚嵛嵝嵴巅巩巯币帅师帏帐帘帜带帧帮帱帻帼幂幞干并广庄庆庐庑库应庙庞废庼廪开异弃张弥弪弯弹强归当录彟彦彻径徕御忆忏忧忾怀态怂怃怄怅怆怜总怼怿恋恳恶恸恹恺恻恼恽悦悫悬悭悯惊惧惨惩惫惬惭惮惯愍愠愤愦愿慑慭憷懑懒懔戆戋戏戗战戬户扎扑扦执扩扪扫扬扰抚抛抟抠抡抢护报担拟拢拣拥拦拧拨择挂挚挛挜挝挞挟挠挡挢挣挤挥挦捞损捡换捣据捻掳掴掷掸掺掼揸揽揿搀搁搂搅携摄摅摆摇摈摊撄撑撵撷撸撺擞攒敌敛数斋斓斗斩断无旧时旷旸昙昼昽显晋晒晓晔晕晖暂暧札术朴机杀杂权条来杨杩杰极构枞枢枣枥枧枨枪枫枭柜柠柽栀栅标栈栉栊栋栌栎栏树栖样栾桊桠桡桢档桤桥桦桧桨桩梦梼梾检棂椁椟椠椤椭楼榄榇榈榉槚槛槟槠横樯樱橥橱橹橼檐檩欢欤欧歼殁殇残殒殓殚殡殴毁毂毕毙毡毵氇气氢氩氲汇汉污汤汹沓沟没沣沤沥沦沧沨沩沪沵泞泪泶泷泸泺泻泼泽泾洁洒洼浃浅浆浇浈浉浊测浍济浏浐浑浒浓浔浕涂涌涛涝涞涟涠涡涢涣涤润涧涨涩淀渊渌渍渎渐渑渔渖渗温游湾湿溃溅溆溇滗滚滞滟滠满滢滤滥滦滨滩滪漤潆潇潋潍潜潴澜濑濒灏灭灯灵灾灿炀炉炖炜炝点炼炽烁烂烃烛烟烦烧烨烩烫烬热焕焖焘煅煳熘爱爷牍牦牵牺犊犟状犷犸犹狈狍狝狞独狭狮狯狰狱狲猃猎猕猡猪猫猬献獭玑玙玚玛玮环现玱玺珉珏珐珑珰珲琎琏琐琼瑶瑷璇璎瓒瓮瓯电画畅畲畴疖疗疟疠疡疬疮疯疱疴痈痉痒痖痨痪痫痴瘅瘆瘗瘘瘪瘫瘾瘿癞癣癫癯皑皱皲盏盐监盖盗盘眍眦眬着睁睐睑瞒瞩矫矶矾矿砀码砖砗砚砜砺砻砾础硁硅硕硖硗硙硚确硷碍碛碜碱碹磙礼祎祢祯祷祸禀禄禅离秃秆种积称秽秾稆税稣稳穑穷窃窍窑窜窝窥窦窭竖竞笃笋笔笕笺笼笾筑筚筛筜筝筹签简箓箦箧箨箩箪箫篑篓篮篱簖籁籴类籼粜粝粤粪粮糁糇紧絷纟纠纡红纣纤纥约级纨纩纪纫纬纭纮纯纰纱纲纳纴纵纶纷纸纹纺纻纼纽纾线绀绁绂练组绅细织终绉绊绋绌绍绎经绐绑绒结绔绕绖绗绘给绚绛络绝绞统绠绡绢绣绤绥绦继绨绩绪绫绬续绮绯绰绱绲绳维绵绶绷绸绹绺绻综绽绾绿缀缁缂缃缄缅缆缇缈缉缊缋缌缍缎缏缐缑缒缓缔缕编缗缘缙缚缛缜缝缞缟缠缡缢缣缤缥缦缧缨缩缪缫缬缭缮缯缰缱缲缳缴缵罂网罗罚罢罴羁羟羡翘翙翚耢耧耸耻聂聋职聍联聩聪肃肠肤肷肾肿胀胁胆胜胧胨胪胫胶脉脍脏脐脑脓脔脚脱脶脸腊腌腘腭腻腼腽腾膑臜舆舣舰舱舻艰艳艹艺节芈芗芜芦苁苇苈苋苌苍苎苏苘苹茎茏茑茔茕茧荆荐荙荚荛荜荞荟荠荡荣荤荥荦荧荨荩荪荫荬荭荮药莅莜莱莲莳莴莶获莸莹莺莼萚萝萤营萦萧萨葱蒇蒉蒋蒌蓝蓟蓠蓣蓥蓦蔷蔹蔺蔼蕲蕴薮藁藓虏虑虚虫虬虮虽虾虿蚀蚁蚂蚕蚝蚬蛊蛎蛏蛮蛰蛱蛲蛳蛴蜕蜗蜡蝇蝈蝉蝎蝼蝾螀螨蟏衅衔补衬衮袄袅袆袜袭袯装裆裈裢裣裤裥褛褴襁襕见观觃规觅视觇览觉觊觋觌觍觎觏觐觑觞触觯詟誉誊讠计订讣认讥讦讧讨让讪讫训议讯记讱讲讳讴讵讶讷许讹论讻讼讽设访诀证诂诃评诅识诇诈诉诊诋诌词诎诏诐译诒诓诔试诖诗诘诙诚诛诜话诞诟诠诡询诣诤该详诧诨诩诪诫诬语诮误诰诱诲诳说诵诶请诸诹诺读诼诽课诿谀谁谂调谄谅谆谇谈谊谋谌谍谎谏谐谑谒谓谔谕谖谗谘谙谚谛谜谝谞谟谠谡谢谣谤谥谦谧谨谩谪谫谬谭谮谯谰谱谲谳谴谵谶谷豮贝贞负贠贡财责贤败账货质贩贪贫贬购贮贯贰贱贲贳贴贵贶贷贸费贺贻贼贽贾贿赀赁赂赃资赅赆赇赈赉赊赋赌赍赎赏赐赑赒赓赔赕赖赗赘赙赚赛赜赝赞赟赠赡赢赣赪赵赶趋趱趸跃跄跖跞践跶跷跸跹跻踊踌踪踬踯蹑蹒蹰蹿躏躜躯车轧轨轩轪轫转轭轮软轰轱轲轳轴轵轶轷轸轹轺轻轼载轾轿辀辁辂较辄辅辆辇辈辉辊辋辌辍辎辏辐辑辒输辔辕辖辗辘辙辚辞辩辫边辽达迁过迈运还这进远违连迟迩迳迹适选逊递逦逻遗遥邓邝邬邮邹邺邻郁郄郏郐郑郓郦郧郸酝酦酱酽酾酿释里鉅鉴銮錾钆钇针钉钊钋钌钍钎钏钐钑钒钓钔钕钖钗钘钙钚钛钝钞钟钠钡钢钣钤钥钦钧钨钩钪钫钬钭钮钯钰钱钲钳钴钵钶钷钸钹钺钻钼钽钾钿铀铁铂铃铄铅铆铈铉铊铋铍铎铏铐铑铒铕铗铘铙铚铛铜铝铞铟铠铡铢铣铤铥铦铧铨铪铫铬铭铮铯铰铱铲铳铴铵银铷铸铹铺铻铼铽链铿销锁锂锃锄锅锆锇锈锉锊锋锌锍锎锏锐锑锒锓锔锕锖锗错锚锜锞锟锠锡锢锣锤锥锦锨锩锫锬锭键锯锰锱锲锳锴锵锶锷锸锹锺锻锼锽锾锿镀镁镂镃镆镇镈镉镊镌镍镎镏镐镑镒镕镖镗镙镚镛镜镝镞镟镠镡镢镣镤镥镦镧镨镩镪镫镬镭镮镯镰镱镲镳镴镶长门闩闪闫闬闭问闯闰闱闲闳间闵闶闷闸闹闺闻闼闽闾闿阀阁阂阃阄阅阆阇阈阉阊阋阌阍阎阏阐阑阒阓阔阕阖阗阘阙阚阛队阳阴阵阶际陆陇陈陉陕陧陨险随隐隶隽难雏雠雳雾霁霉霭靓静靥鞑鞒鞯鞴韦韧韨韩韪韫韬韵页顶顷顸项顺须顼顽顾顿颀颁颂颃预颅领颇颈颉颊颋颌颍颎颏颐频颒颓颔颕颖颗题颙颚颛颜额颞颟颠颡颢颣颤颥颦颧风飏飐飑飒飓飔飕飖飗飘飙飚飞飨餍饤饥饦饧饨饩饪饫饬饭饮饯饰饱饲饳饴饵饶饷饸饹饺饻饼饽饾饿馀馁馂馃馄馅馆馇馈馉馊馋馌馍馎馏馐馑馒馓馔馕马驭驮驯驰驱驲驳驴驵驶驷驸驹驺驻驼驽驾驿骀骁骂骃骄骅骆骇骈骉骊骋验骍骎骏骐骑骒骓骔骕骖骗骘骙骚骛骜骝骞骟骠骡骢骣骤骥骦骧髅髋髌鬓魇魉鱼鱽鱾鱿鲀鲁鲂鲄鲅鲆鲇鲈鲉鲊鲋鲌鲍鲎鲏鲐鲑鲒鲓鲔鲕鲖鲗鲘鲙鲚鲛鲜鲝鲞鲟鲠鲡鲢鲣鲤鲥鲦鲧鲨鲩鲪鲫鲬鲭鲮鲯鲰鲱鲲鲳鲴鲵鲶鲷鲸鲹鲺鲻鲼鲽鲾鲿鳀鳁鳂鳃鳄鳅鳆鳇鳈鳉鳊鳋鳌鳍鳎鳏鳐鳑鳒鳓鳔鳕鳖鳗鳘鳙鳛鳜鳝鳞鳟鳠鳡鳢鳣鸟鸠鸡鸢鸣鸤鸥鸦鸧鸨鸩鸪鸫鸬鸭鸮鸯鸰鸱鸲鸳鸴鸵鸶鸷鸸鸹鸺鸻鸼鸽鸾鸿鹀鹁鹂鹃鹄鹅鹆鹇鹈鹉鹊鹋鹌鹍鹎鹏鹐鹑鹒鹓鹔鹕鹖鹗鹘鹚鹛鹜鹝鹞鹟鹠鹡鹢鹣鹤鹥鹦鹧鹨鹩鹪鹫鹬鹭鹯鹰鹱鹲鹳鹴鹾麦麸黄黉黡黩黪黾鼋鼌鼍鼗鼹齄齐齑齿龀龁龂龃龄龅龆龇龈龉龊龋龌龙龚龛龟志制咨只里系范松没尝尝闹面准钟别闲干尽脏拼');

    /**
     * 繁体字
     * @const
     */
    var T = new String('萬與醜專業叢東絲丟兩嚴喪個爿豐臨為麗舉麼義烏樂喬習鄉書買亂爭於虧雲亙亞產畝親褻嚲億僅從侖倉儀們價眾優夥會傴傘偉傳傷倀倫傖偽佇體餘傭僉俠侶僥偵側僑儈儕儂俁儔儼倆儷儉債傾傯僂僨償儻儐儲儺兒兌兗黨蘭關興茲養獸囅內岡冊寫軍農塚馮衝決況凍淨淒涼淩減湊凜幾鳳鳧憑凱擊氹鑿芻劃劉則剛創刪別剗剄劊劌剴劑剮劍剝劇勸辦務勱動勵勁勞勢勳猛勩勻匭匱區醫華協單賣盧鹵臥衛卻巹廠廳曆厲壓厭厙廁廂厴廈廚廄廝縣參靉靆雙發變敘疊葉號歎嘰籲後嚇呂嗎唚噸聽啟吳嘸囈嘔嚦唄員咼嗆嗚詠哢嚨嚀噝吒噅鹹呱響啞噠嘵嗶噦嘩噲嚌噥喲嘜嗊嘮啢嗩唕喚呼嘖嗇囀齧囉嘽嘯噴嘍嚳囁嗬噯噓嚶囑嚕劈囂謔團園囪圍圇國圖圓聖壙場阪壞塊堅壇壢壩塢墳墜壟壟壚壘墾坰堊墊埡墶壋塏堖塒塤堝墊垵塹墮壪牆壯聲殼壺壼處備複夠頭誇夾奪奩奐奮獎奧妝婦媽嫵嫗媯姍薑婁婭嬈嬌孌娛媧嫻嫿嬰嬋嬸媼嬡嬪嬙嬤孫學孿寧寶實寵審憲宮寬賓寢對尋導壽將爾塵堯尷屍盡層屭屜屆屬屢屨嶼歲豈嶇崗峴嶴嵐島嶺嶽崠巋嶨嶧峽嶢嶠崢巒嶗崍嶮嶄嶸嶔崳嶁脊巔鞏巰幣帥師幃帳簾幟帶幀幫幬幘幗冪襆幹並廣莊慶廬廡庫應廟龐廢廎廩開異棄張彌弳彎彈強歸當錄彠彥徹徑徠禦憶懺憂愾懷態慫憮慪悵愴憐總懟懌戀懇惡慟懨愷惻惱惲悅愨懸慳憫驚懼慘懲憊愜慚憚慣湣慍憤憒願懾憖怵懣懶懍戇戔戲戧戰戩戶紮撲扡執擴捫掃揚擾撫拋摶摳掄搶護報擔擬攏揀擁攔擰撥擇掛摯攣掗撾撻挾撓擋撟掙擠揮撏撈損撿換搗據撚擄摑擲撣摻摜摣攬撳攙擱摟攪攜攝攄擺搖擯攤攖撐攆擷擼攛擻攢敵斂數齋斕鬥斬斷無舊時曠暘曇晝曨顯晉曬曉曄暈暉暫曖劄術樸機殺雜權條來楊榪傑極構樅樞棗櫪梘棖槍楓梟櫃檸檉梔柵標棧櫛櫳棟櫨櫟欄樹棲樣欒棬椏橈楨檔榿橋樺檜槳樁夢檮棶檢欞槨櫝槧欏橢樓欖櫬櫚櫸檟檻檳櫧橫檣櫻櫫櫥櫓櫞簷檁歡歟歐殲歿殤殘殞殮殫殯毆毀轂畢斃氈毿氌氣氫氬氳彙漢汙湯洶遝溝沒灃漚瀝淪滄渢溈滬濔濘淚澩瀧瀘濼瀉潑澤涇潔灑窪浹淺漿澆湞溮濁測澮濟瀏滻渾滸濃潯濜塗湧濤澇淶漣潿渦溳渙滌潤澗漲澀澱淵淥漬瀆漸澠漁瀋滲溫遊灣濕潰濺漵漊潷滾滯灩灄滿瀅濾濫灤濱灘澦濫瀠瀟瀲濰潛瀦瀾瀨瀕灝滅燈靈災燦煬爐燉煒熗點煉熾爍爛烴燭煙煩燒燁燴燙燼熱煥燜燾煆糊溜愛爺牘犛牽犧犢強狀獷獁猶狽麅獮獰獨狹獅獪猙獄猻獫獵獼玀豬貓蝟獻獺璣璵瑒瑪瑋環現瑲璽瑉玨琺瓏璫琿璡璉瑣瓊瑤璦璿瓔瓚甕甌電畫暢佘疇癤療瘧癘瘍鬁瘡瘋皰屙癰痙癢瘂癆瘓癇癡癉瘮瘞瘺癟癱癮癭癩癬癲臒皚皺皸盞鹽監蓋盜盤瞘眥矓著睜睞瞼瞞矚矯磯礬礦碭碼磚硨硯碸礪礱礫礎硜矽碩硤磽磑礄確鹼礙磧磣堿镟滾禮禕禰禎禱禍稟祿禪離禿稈種積稱穢穠穭稅穌穩穡窮竊竅窯竄窩窺竇窶豎競篤筍筆筧箋籠籩築篳篩簹箏籌簽簡籙簀篋籜籮簞簫簣簍籃籬籪籟糴類秈糶糲粵糞糧糝餱緊縶糸糾紆紅紂纖紇約級紈纊紀紉緯紜紘純紕紗綱納紝縱綸紛紙紋紡紵紖紐紓線紺絏紱練組紳細織終縐絆紼絀紹繹經紿綁絨結絝繞絰絎繪給絢絳絡絕絞統綆綃絹繡綌綏絛繼綈績緒綾緓續綺緋綽緔緄繩維綿綬繃綢綯綹綣綜綻綰綠綴緇緙緗緘緬纜緹緲緝縕繢緦綞緞緶線緱縋緩締縷編緡緣縉縛縟縝縫縗縞纏縭縊縑繽縹縵縲纓縮繆繅纈繚繕繒韁繾繰繯繳纘罌網羅罰罷羆羈羥羨翹翽翬耮耬聳恥聶聾職聹聯聵聰肅腸膚膁腎腫脹脅膽勝朧腖臚脛膠脈膾髒臍腦膿臠腳脫腡臉臘醃膕齶膩靦膃騰臏臢輿艤艦艙艫艱豔艸藝節羋薌蕪蘆蓯葦藶莧萇蒼苧蘇檾蘋莖蘢蔦塋煢繭荊薦薘莢蕘蓽蕎薈薺蕩榮葷滎犖熒蕁藎蓀蔭蕒葒葤藥蒞蓧萊蓮蒔萵薟獲蕕瑩鶯蓴蘀蘿螢營縈蕭薩蔥蕆蕢蔣蔞藍薊蘺蕷鎣驀薔蘞藺藹蘄蘊藪槁蘚虜慮虛蟲虯蟣雖蝦蠆蝕蟻螞蠶蠔蜆蠱蠣蟶蠻蟄蛺蟯螄蠐蛻蝸蠟蠅蟈蟬蠍螻蠑螿蟎蠨釁銜補襯袞襖嫋褘襪襲襏裝襠褌褳襝褲襇褸襤繈襴見觀覎規覓視覘覽覺覬覡覿覥覦覯覲覷觴觸觶讋譽謄訁計訂訃認譏訐訌討讓訕訖訓議訊記訒講諱謳詎訝訥許訛論訩訟諷設訪訣證詁訶評詛識詗詐訴診詆謅詞詘詔詖譯詒誆誄試詿詩詰詼誠誅詵話誕詬詮詭詢詣諍該詳詫諢詡譸誡誣語誚誤誥誘誨誑說誦誒請諸諏諾讀諑誹課諉諛誰諗調諂諒諄誶談誼謀諶諜謊諫諧謔謁謂諤諭諼讒諮諳諺諦謎諞諝謨讜謖謝謠謗諡謙謐謹謾謫譾謬譚譖譙讕譜譎讞譴譫讖穀豶貝貞負貟貢財責賢敗賬貨質販貪貧貶購貯貫貳賤賁貰貼貴貺貸貿費賀貽賊贄賈賄貲賃賂贓資賅贐賕賑賚賒賦賭齎贖賞賜贔賙賡賠賧賴賵贅賻賺賽賾贗讚贇贈贍贏贛赬趙趕趨趲躉躍蹌蹠躒踐躂蹺蹕躚躋踴躊蹤躓躑躡蹣躕躥躪躦軀車軋軌軒軑軔轉軛輪軟轟軲軻轤軸軹軼軤軫轢軺輕軾載輊轎輈輇輅較輒輔輛輦輩輝輥輞輬輟輜輳輻輯轀輸轡轅轄輾轆轍轔辭辯辮邊遼達遷過邁運還這進遠違連遲邇逕跡適選遜遞邐邏遺遙鄧鄺鄔郵鄒鄴鄰鬱郤郟鄶鄭鄆酈鄖鄲醞醱醬釅釃釀釋裏钜鑒鑾鏨釓釔針釘釗釙釕釷釺釧釤鈒釩釣鍆釹鍚釵鈃鈣鈈鈦鈍鈔鍾鈉鋇鋼鈑鈐鑰欽鈞鎢鉤鈧鈁鈥鈄鈕鈀鈺錢鉦鉗鈷缽鈳鉕鈽鈸鉞鑽鉬鉭鉀鈿鈾鐵鉑鈴鑠鉛鉚鈰鉉鉈鉍鈹鐸鉶銬銠鉺銪鋏鋣鐃銍鐺銅鋁銱銦鎧鍘銖銑鋌銩銛鏵銓鉿銚鉻銘錚銫鉸銥鏟銃鐋銨銀銣鑄鐒鋪鋙錸鋱鏈鏗銷鎖鋰鋥鋤鍋鋯鋨鏽銼鋝鋒鋅鋶鐦鐧銳銻鋃鋟鋦錒錆鍺錯錨錡錁錕錩錫錮鑼錘錐錦鍁錈錇錟錠鍵鋸錳錙鍥鍈鍇鏘鍶鍔鍤鍬鍾鍛鎪鍠鍰鎄鍍鎂鏤鎡鏌鎮鎛鎘鑷鐫鎳鎿鎦鎬鎊鎰鎔鏢鏜鏍鏰鏞鏡鏑鏃鏇鏐鐔钁鐐鏷鑥鐓鑭鐠鑹鏹鐙鑊鐳鐶鐲鐮鐿鑔鑣鑞鑲長門閂閃閆閈閉問闖閏闈閑閎間閔閌悶閘鬧閨聞闥閩閭闓閥閣閡閫鬮閱閬闍閾閹閶鬩閿閽閻閼闡闌闃闠闊闋闔闐闒闕闞闤隊陽陰陣階際陸隴陳陘陝隉隕險隨隱隸雋難雛讎靂霧霽黴靄靚靜靨韃鞽韉韝韋韌韍韓韙韞韜韻頁頂頃頇項順須頊頑顧頓頎頒頌頏預顱領頗頸頡頰頲頜潁熲頦頤頻頮頹頷頴穎顆題顒顎顓顏額顳顢顛顙顥纇顫顬顰顴風颺颭颮颯颶颸颼颻飀飄飆飆飛饗饜飣饑飥餳飩餼飪飫飭飯飲餞飾飽飼飿飴餌饒餉餄餎餃餏餅餑餖餓餘餒餕餜餛餡館餷饋餶餿饞饁饃餺餾饈饉饅饊饌饢馬馭馱馴馳驅馹駁驢駔駛駟駙駒騶駐駝駑駕驛駘驍罵駰驕驊駱駭駢驫驪騁驗騂駸駿騏騎騍騅騌驌驂騙騭騤騷騖驁騮騫騸驃騾驄驏驟驥驦驤髏髖髕鬢魘魎魚魛魢魷魨魯魴魺鮁鮃鯰鱸鮋鮓鮒鮊鮑鱟鮍鮐鮭鮚鮳鮪鮞鮦鰂鮜鱠鱭鮫鮮鮺鯗鱘鯁鱺鰱鰹鯉鰣鰷鯀鯊鯇鮶鯽鯒鯖鯪鯕鯫鯡鯤鯧鯝鯢鯰鯛鯨鯵鯴鯔鱝鰈鰏鱨鯷鰮鰃鰓鱷鰍鰒鰉鰁鱂鯿鰠鼇鰭鰨鰥鰩鰟鰜鰳鰾鱈鱉鰻鰵鱅鰼鱖鱔鱗鱒鱯鱤鱧鱣鳥鳩雞鳶鳴鳲鷗鴉鶬鴇鴆鴣鶇鸕鴨鴞鴦鴒鴟鴝鴛鴬鴕鷥鷙鴯鴰鵂鴴鵃鴿鸞鴻鵐鵓鸝鵑鵠鵝鵒鷳鵜鵡鵲鶓鵪鶤鵯鵬鵮鶉鶊鵷鷫鶘鶡鶚鶻鶿鶥鶩鷊鷂鶲鶹鶺鷁鶼鶴鷖鸚鷓鷚鷯鷦鷲鷸鷺鸇鷹鸌鸏鸛鸘鹺麥麩黃黌黶黷黲黽黿鼂鼉鞀鼴齇齊齏齒齔齕齗齟齡齙齠齜齦齬齪齲齷龍龔龕龜誌製谘隻裡係範鬆冇嚐嘗鬨麵準鐘彆閒乾儘臟拚');

    /**
     * 转换文本
     * @param {String} str - 待转换的文本
     * @param {Boolean} toT - 是否转换成繁体
     * @returns {String} - 转换结果
     */
    function tranStr(str, toT) {
        var i;
        var letter;
        var code;
        var isChinese;
        var index;
        var src, des;
        var result = '';

        if (toT) {
            src = S;
            des = T;
        } else {
            src = T;
            des = S;
        }

        if (typeof str !== "string") {
            return str;
        }

        for (i = 0; i < str.length; i++) {
            letter = str.charAt(i);
            code = str.charCodeAt(i); 
            
            // 根据字符的Unicode判断是否为汉字，以提高性能
            // 参考:
            // [1] http://www.unicode.org
            // [2] http://zh.wikipedia.org/wiki/Unicode%E5%AD%97%E7%AC%A6%E5%88%97%E8%A1%A8
            // [3] http://xylonwang.iteye.com/blog/519552
            isChinese = (code > 0x3400 && code < 0x9FC3) || (code > 0xF900 && code < 0xFA6A);

            if (!isChinese) {
                result += letter;
                continue;
            }

            index = src.indexOf(letter);

            if (index !== -1) {
                result += des.charAt(index);
            } else {
                result += letter;
            }
        }

        return result;
    }

    /**
     * 转换HTML Element属性
     * @param {Element} element - 待转换的HTML Element节点
     * @param {String|Array} attr - 待转换的属性/属性列表
     * @param {Boolean} toT - 是否转换成繁体
     */
    function tranAttr(element, attr, toT) {
        var i, attrValue;

        if (attr instanceof Array) {
            for(i = 0; i < attr.length; i++) {
                tranAttr(element, attr[i], toT);
            }
        } else {
            attrValue = element.getAttribute(attr);

            if (attrValue !== "" && attrValue !== null) {
                element.setAttribute(attr, tranStr(attrValue, toT));
            }
        }
    }

    /**
     * 转换HTML Element节点
     * @param {Element} element - 待转换的HTML Element节点
     * @param {Boolean} toT - 是否转换成繁体
     */
    function tranElement(element, toT) {
        var i;
        var childNodes;

        if (element.nodeType !== 1) {
            return;
        }

        childNodes = element.childNodes;

        for (i = 0; i < childNodes.length; i++) {
            var childNode = childNodes.item(i);

            // 若为HTML Element节点
            if (childNode.nodeType === 1) {
                // 对以下标签不做处理
                if ("|BR|HR|TEXTAREA|SCRIPT|OBJECT|EMBED|".indexOf("|" + childNode.tagName + "|") !== -1) {
                    continue;
                }
                
                tranAttr(childNode, ['title', 'data-original-title', 'alt', 'placeholder'], toT);

                // input 标签
                // 对text类型的input输入框不做处理
                if (childNode.tagName === "INPUT"
                    && childNode.value !== ""
                    && childNode.type !== "text"
                    && childNode.type !== "hidden")
                {
                    childNode.value = tranStr(childNode.value, toT);
                }

                // 继续递归调用
                tranElement(childNode, toT);
            } else if (childNode.nodeType === 3) {  // 若为文本节点
                childNode.data = tranStr(childNode.data, toT);
            }
        }
    }

    // 扩展jQuery全局方法
    $.extend({
        /**
         * 文本简转繁
         * @param {String} str - 待转换的文本
         * @returns {String} 转换结果
         */
        s2t: function(str) {
            return tranStr(str, true);
        },

        /**
         * 文本繁转简
         * @param {String} str - 待转换的文本
         * @returns {String} 转换结果
         */
        t2s: function(str) {
            return tranStr(str, false);
        }
    });

    // 扩展jQuery对象方法
    $.fn.extend({
        /**
         * jQuery Objects简转繁
         * @this {jQuery Objects} 待转换的jQuery Objects
         */
        s2t: function() {
            return this.each(function() {
                tranElement(this, true);
            });
        },

        /**
         * jQuery Objects繁转简
         * @this {jQuery Objects} 待转换的jQuery Objects
         */
        t2s: function() {
            return this.each(function() {
                tranElement(this, false);
            });
        }
    });
}) (jQuery);

function tabspro(obj)
{
    var url = $(obj).attr('data-url');
    var typeid = $(obj).attr('data-typeid');
    $(obj).parent().parent().find('li').removeClass('active');
    $(obj).parent().addClass('active');
    $('ul.tabspro').hide();
    $('#pro_'+typeid).show();
    $('#tabspro_more').attr('href', url);
}