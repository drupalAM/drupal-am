/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 * 
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})(jQuery);

/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 3.1.9
 *
 * Requires: jQuery 1.2.2+
 */
(function(factory){if(typeof define==="function"&&define.amd){define(["jquery"],factory)}else{if(typeof exports==="object"){module.exports=factory}else{factory(jQuery)}}}(function($){var toFix=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],toBind=("onwheel" in document||document.documentMode>=9)?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],slice=Array.prototype.slice,nullLowestDeltaTimeout,lowestDelta;if($.event.fixHooks){for(var i=toFix.length;i;){$.event.fixHooks[toFix[--i]]=$.event.mouseHooks}}var special=$.event.special.mousewheel={version:"3.1.9",setup:function(){if(this.addEventListener){for(var i=toBind.length;i;){this.addEventListener(toBind[--i],handler,false)}}else{this.onmousewheel=handler}$.data(this,"mousewheel-line-height",special.getLineHeight(this));$.data(this,"mousewheel-page-height",special.getPageHeight(this))},teardown:function(){if(this.removeEventListener){for(var i=toBind.length;i;){this.removeEventListener(toBind[--i],handler,false)}}else{this.onmousewheel=null}},getLineHeight:function(elem){return parseInt($(elem)["offsetParent" in $.fn?"offsetParent":"parent"]().css("fontSize"),10)},getPageHeight:function(elem){return $(elem).height()},settings:{adjustOldDeltas:true}};$.fn.extend({mousewheel:function(fn){return fn?this.bind("mousewheel",fn):this.trigger("mousewheel")},unmousewheel:function(fn){return this.unbind("mousewheel",fn)}});function handler(event){var orgEvent=event||window.event,args=slice.call(arguments,1),delta=0,deltaX=0,deltaY=0,absDelta=0;event=$.event.fix(orgEvent);event.type="mousewheel";if("detail" in orgEvent){deltaY=orgEvent.detail*-1}if("wheelDelta" in orgEvent){deltaY=orgEvent.wheelDelta}if("wheelDeltaY" in orgEvent){deltaY=orgEvent.wheelDeltaY}if("wheelDeltaX" in orgEvent){deltaX=orgEvent.wheelDeltaX*-1}if("axis" in orgEvent&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaX=deltaY*-1;deltaY=0}delta=deltaY===0?deltaX:deltaY;if("deltaY" in orgEvent){deltaY=orgEvent.deltaY*-1;delta=deltaY}if("deltaX" in orgEvent){deltaX=orgEvent.deltaX;if(deltaY===0){delta=deltaX*-1}}if(deltaY===0&&deltaX===0){return}if(orgEvent.deltaMode===1){var lineHeight=$.data(this,"mousewheel-line-height");delta*=lineHeight;deltaY*=lineHeight;deltaX*=lineHeight}else{if(orgEvent.deltaMode===2){var pageHeight=$.data(this,"mousewheel-page-height");delta*=pageHeight;deltaY*=pageHeight;deltaX*=pageHeight}}absDelta=Math.max(Math.abs(deltaY),Math.abs(deltaX));if(!lowestDelta||absDelta<lowestDelta){lowestDelta=absDelta;if(shouldAdjustOldDeltas(orgEvent,absDelta)){lowestDelta/=40}}if(shouldAdjustOldDeltas(orgEvent,absDelta)){delta/=40;deltaX/=40;deltaY/=40}delta=Math[delta>=1?"floor":"ceil"](delta/lowestDelta);deltaX=Math[deltaX>=1?"floor":"ceil"](deltaX/lowestDelta);deltaY=Math[deltaY>=1?"floor":"ceil"](deltaY/lowestDelta);event.deltaX=deltaX;event.deltaY=deltaY;event.deltaFactor=lowestDelta;event.deltaMode=0;args.unshift(event,delta,deltaX,deltaY);if(nullLowestDeltaTimeout){clearTimeout(nullLowestDeltaTimeout)}nullLowestDeltaTimeout=setTimeout(nullLowestDelta,200);return($.event.dispatch||$.event.handle).apply(this,args)}function nullLowestDelta(){lowestDelta=null}function shouldAdjustOldDeltas(orgEvent,absDelta){return special.settings.adjustOldDeltas&&orgEvent.type==="mousewheel"&&absDelta%120===0}}));
/*!
 * jQuery imagesLoaded plugin v1.0.4
 * http://github.com/desandro/imagesloaded
 *
 * MIT License. by Paul Irish et al.
 */

(function(a,b){a.fn.imagesLoaded=function(i){var g=this,e=g.find("img").add(g.filter("img")),c=e.length,h="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";function f(){i.call(g,e)}function d(j){if(--c<=0&&j.target.src!==h){setTimeout(f);e.unbind("load error",d)}}if(!c){f()}e.bind("load error",d).each(function(){if(this.complete||this.complete===b){var j=this.src;this.src=h;this.src=j}});return g}})(jQuery);


/*
 *  Sharrre.com - Make your sharing widget!
 *  Version: beta 1.3.3 
 *  Author: Julien Hany
 *  License: MIT http://en.wikipedia.org/wiki/MIT_License or GPLv2 http://en.wikipedia.org/wiki/GNU_General_Public_License
 */
(function(g,i,j,b){var h="sharrre",f={className:"sharrre",share:{googlePlus:false,facebook:false,twitter:false,digg:false,delicious:false,stumbleupon:false,linkedin:false,pinterest:false},shareTotal:0,template:"",title:"",url:j.location.href,text:j.title,urlCurl:"sharrre.php",count:{},total:0,shorterTotal:true,enableHover:true,enableCounter:true,enableTracking:false,hover:function(){},hide:function(){},click:function(){},render:function(){},buttons:{googlePlus:{url:"",urlCount:false,size:"medium",lang:"en-US",annotation:""},facebook:{url:"",urlCount:false,action:"like",layout:"button_count",width:"",send:"false",faces:"false",colorscheme:"",font:"",lang:"en_US"},twitter:{url:"",urlCount:false,count:"horizontal",hashtags:"",via:"",related:"",lang:"en"},digg:{url:"",urlCount:false,type:"DiggCompact"},delicious:{url:"",urlCount:false,size:"medium"},stumbleupon:{url:"",urlCount:false,layout:"1"},linkedin:{url:"",urlCount:false,counter:""},pinterest:{url:"",media:"",description:"",layout:"horizontal"}}},c={googlePlus:"",facebook:"https://graph.facebook.com/fql?q=SELECT%20url,%20normalized_url,%20share_count,%20like_count,%20comment_count,%20total_count,commentsbox_count,%20comments_fbid,%20click_count%20FROM%20link_stat%20WHERE%20url=%27{url}%27&callback=?",twitter:"http://cdn.api.twitter.com/1/urls/count.json?url={url}&callback=?",digg:"http://services.digg.com/2.0/story.getInfo?links={url}&type=javascript&callback=?",delicious:"http://feeds.delicious.com/v2/json/urlinfo/data?url={url}&callback=?",stumbleupon:"",linkedin:"http://www.linkedin.com/countserv/count/share?format=jsonp&url={url}&callback=?",pinterest:""},l={googlePlus:function(m){var n=m.options.buttons.googlePlus;g(m.element).find(".buttons").append('<div class="button googleplus"><div class="g-plusone" data-size="'+n.size+'" data-href="'+(n.url!==""?n.url:m.options.url)+'" data-annotation="'+n.annotation+'"></div></div>');i.___gcfg={lang:m.options.buttons.googlePlus.lang};var o=0;if(typeof gapi==="undefined"&&o==0){o=1;(function(){var p=j.createElement("script");p.type="text/javascript";p.async=true;p.src="//apis.google.com/js/plusone.js";var q=j.getElementsByTagName("script")[0];q.parentNode.insertBefore(p,q)})()}else{gapi.plusone.go()}},facebook:function(m){var n=m.options.buttons.facebook;g(m.element).find(".buttons").append('<div class="button facebook"><div id="fb-root"></div><div class="fb-like" data-href="'+(n.url!==""?n.url:m.options.url)+'" data-send="'+n.send+'" data-layout="'+n.layout+'" data-width="'+n.width+'" data-show-faces="'+n.faces+'" data-action="'+n.action+'" data-colorscheme="'+n.colorscheme+'" data-font="'+n.font+'" data-via="'+n.via+'"></div></div>');var o=0;if(typeof FB==="undefined"&&o==0){o=1;(function(t,p,u){var r,q=t.getElementsByTagName(p)[0];if(t.getElementById(u)){return}r=t.createElement(p);r.id=u;r.src="//connect.facebook.net/"+n.lang+"/all.js#xfbml=1";q.parentNode.insertBefore(r,q)}(j,"script","facebook-jssdk"))}else{FB.XFBML.parse()}},twitter:function(m){var n=m.options.buttons.twitter;g(m.element).find(".buttons").append('<div class="button twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-url="'+(n.url!==""?n.url:m.options.url)+'" data-count="'+n.count+'" data-text="'+m.options.text+'" data-via="'+n.via+'" data-hashtags="'+n.hashtags+'" data-related="'+n.related+'" data-lang="'+n.lang+'">Tweet</a></div>');var o=0;if(typeof twttr==="undefined"&&o==0){o=1;(function(){var q=j.createElement("script");q.type="text/javascript";q.async=true;q.src="//platform.twitter.com/widgets.js";var p=j.getElementsByTagName("script")[0];p.parentNode.insertBefore(q,p)})()}else{g.ajax({url:"//platform.twitter.com/widgets.js",dataType:"script",cache:true})}},digg:function(m){var n=m.options.buttons.digg;g(m.element).find(".buttons").append('<div class="button digg"><a class="DiggThisButton '+n.type+'" rel="nofollow external" href="http://digg.com/submit?url='+encodeURIComponent((n.url!==""?n.url:m.options.url))+'"></a></div>');var o=0;if(typeof __DBW==="undefined"&&o==0){o=1;(function(){var q=j.createElement("SCRIPT"),p=j.getElementsByTagName("SCRIPT")[0];q.type="text/javascript";q.async=true;q.src="//widgets.digg.com/buttons.js";p.parentNode.insertBefore(q,p)})()}},delicious:function(o){if(o.options.buttons.delicious.size=="tall"){var p="width:50px;",n="height:35px;width:50px;font-size:15px;line-height:35px;",m="height:18px;line-height:18px;margin-top:3px;"}else{var p="width:93px;",n="float:right;padding:0 3px;height:20px;width:26px;line-height:20px;",m="float:left;height:20px;line-height:20px;"}var q=o.shorterTotal(o.options.count.delicious);if(typeof q==="undefined"){q=0}g(o.element).find(".buttons").append('<div class="button delicious"><div style="'+p+'font:12px Arial,Helvetica,sans-serif;cursor:pointer;color:#666666;display:inline-block;float:none;height:20px;line-height:normal;margin:0;padding:0;text-indent:0;vertical-align:baseline;"><div style="'+n+'background-color:#fff;margin-bottom:5px;overflow:hidden;text-align:center;border:1px solid #ccc;border-radius:3px;">'+q+'</div><div style="'+m+'display:block;padding:0;text-align:center;text-decoration:none;width:50px;background-color:#7EACEE;border:1px solid #40679C;border-radius:3px;color:#fff;"><img src="http://www.delicious.com/static/img/delicious.small.gif" height="10" width="10" alt="Delicious" /> Add</div></div></div>');g(o.element).find(".delicious").on("click",function(){o.openPopup("delicious")})},stumbleupon:function(m){var n=m.options.buttons.stumbleupon;g(m.element).find(".buttons").append('<div class="button stumbleupon"><su:badge layout="'+n.layout+'" location="'+(n.url!==""?n.url:m.options.url)+'"></su:badge></div>');var o=0;if(typeof STMBLPN==="undefined"&&o==0){o=1;(function(){var p=j.createElement("script");p.type="text/javascript";p.async=true;p.src="//platform.stumbleupon.com/1/widgets.js";var q=j.getElementsByTagName("script")[0];q.parentNode.insertBefore(p,q)})();s=i.setTimeout(function(){if(typeof STMBLPN!=="undefined"){STMBLPN.processWidgets();clearInterval(s)}},500)}else{STMBLPN.processWidgets()}},linkedin:function(m){var n=m.options.buttons.linkedin;g(m.element).find(".buttons").append('<div class="button linkedin"><script type="in/share" data-url="'+(n.url!==""?n.url:m.options.url)+'" data-counter="'+n.counter+'"><\/script></div>');var o=0;if(typeof i.IN==="undefined"&&o==0){o=1;(function(){var p=j.createElement("script");p.type="text/javascript";p.async=true;p.src="//platform.linkedin.com/in.js";var q=j.getElementsByTagName("script")[0];q.parentNode.insertBefore(p,q)})()}else{i.IN.init()}},pinterest:function(m){var n=m.options.buttons.pinterest;g(m.element).find(".buttons").append('<div class="button pinterest"><a href="http://pinterest.com/pin/create/button/?url='+(n.url!==""?n.url:m.options.url)+"&media="+n.media+"&description="+n.description+'" class="pin-it-button" count-layout="'+n.layout+'">Pin It</a></div>');(function(){var o=j.createElement("script");o.type="text/javascript";o.async=true;o.src="//assets.pinterest.com/js/pinit.js";var p=j.getElementsByTagName("script")[0];p.parentNode.insertBefore(o,p)})()}},d={googlePlus:function(){},facebook:function(){fb=i.setInterval(function(){if(typeof FB!=="undefined"){FB.Event.subscribe("edge.create",function(m){_gaq.push(["_trackSocial","facebook","like",m])});FB.Event.subscribe("edge.remove",function(m){_gaq.push(["_trackSocial","facebook","unlike",m])});FB.Event.subscribe("message.send",function(m){_gaq.push(["_trackSocial","facebook","send",m])});clearInterval(fb)}},1000)},twitter:function(){tw=i.setInterval(function(){if(typeof twttr!=="undefined"){twttr.events.bind("tweet",function(m){if(m){_gaq.push(["_trackSocial","twitter","tweet"])}});clearInterval(tw)}},1000)},digg:function(){},delicious:function(){},stumbleupon:function(){},linkedin:function(){function m(){_gaq.push(["_trackSocial","linkedin","share"])}},pinterest:function(){}},a={googlePlus:function(m){i.open("https://plus.google.com/share?hl="+m.buttons.googlePlus.lang+"&url="+encodeURIComponent((m.buttons.googlePlus.url!==""?m.buttons.googlePlus.url:m.url)),"","toolbar=0, status=0, width=900, height=500")},facebook:function(m){i.open("http://www.facebook.com/sharer/sharer.php?u="+encodeURIComponent((m.buttons.facebook.url!==""?m.buttons.facebook.url:m.url))+"&t="+m.text+"","","toolbar=0, status=0, width=900, height=500")},twitter:function(m){i.open("https://twitter.com/intent/tweet?text="+encodeURIComponent(m.text)+"&url="+encodeURIComponent((m.buttons.twitter.url!==""?m.buttons.twitter.url:m.url))+(m.buttons.twitter.via!==""?"&via="+m.buttons.twitter.via:""),"","toolbar=0, status=0, width=650, height=360")},digg:function(m){i.open("http://digg.com/tools/diggthis/submit?url="+encodeURIComponent((m.buttons.digg.url!==""?m.buttons.digg.url:m.url))+"&title="+m.text+"&related=true&style=true","","toolbar=0, status=0, width=650, height=360")},delicious:function(m){i.open("http://www.delicious.com/save?v=5&noui&jump=close&url="+encodeURIComponent((m.buttons.delicious.url!==""?m.buttons.delicious.url:m.url))+"&title="+m.text,"delicious","toolbar=no,width=550,height=550")},stumbleupon:function(m){i.open("http://www.stumbleupon.com/badge/?url="+encodeURIComponent((m.buttons.delicious.url!==""?m.buttons.delicious.url:m.url)),"stumbleupon","toolbar=no,width=550,height=550")},linkedin:function(m){i.open("https://www.linkedin.com/cws/share?url="+encodeURIComponent((m.buttons.delicious.url!==""?m.buttons.delicious.url:m.url))+"&token=&isFramed=true","linkedin","toolbar=no,width=550,height=550")},pinterest:function(m){i.open("http://pinterest.com/pin/create/button/?url="+encodeURIComponent((m.buttons.pinterest.url!==""?m.buttons.pinterest.url:m.url))+"&media="+encodeURIComponent(m.buttons.pinterest.media)+"&description="+m.buttons.pinterest.description,"pinterest","toolbar=no,width=700,height=300")}};function k(n,m){this.element=n;this.options=g.extend(true,{},f,m);this.options.share=m.share;this._defaults=f;this._name=h;this.init()}k.prototype.init=function(){var m=this;if(this.options.urlCurl!==""){c.googlePlus=this.options.urlCurl+"?url={url}&type=googlePlus";c.stumbleupon=this.options.urlCurl+"?url={url}&type=stumbleupon";c.pinterest=this.options.urlCurl+"?url={url}&type=pinterest"}g(this.element).addClass(this.options.className);if(typeof g(this.element).data("title")!=="undefined"){this.options.title=g(this.element).attr("data-title")}if(typeof g(this.element).data("url")!=="undefined"){this.options.url=g(this.element).data("url")}if(typeof g(this.element).data("text")!=="undefined"){this.options.text=g(this.element).data("text")}g.each(this.options.share,function(n,o){if(o===true){m.options.shareTotal++}});if(m.options.enableCounter===true){g.each(this.options.share,function(n,p){if(p===true){try{m.getSocialJson(n)}catch(o){}}})}else{if(m.options.template!==""){this.options.render(this,this.options)}else{this.loadButtons()}}g(this.element).hover(function(){if(g(this).find(".buttons").length===0&&m.options.enableHover===true){m.loadButtons()}m.options.hover(m,m.options)},function(){m.options.hide(m,m.options)});g(this.element).click(function(){m.options.click(m,m.options);return false})};k.prototype.loadButtons=function(){var m=this;g(this.element).append('<div class="buttons"></div>');g.each(m.options.share,function(n,o){if(o==true){l[n](m);if(m.options.enableTracking===true){d[n]()}}})};k.prototype.getSocialJson=function(o){var m=this,p=0,n=c[o].replace("{url}",encodeURIComponent(this.options.url));if(this.options.buttons[o].urlCount===true&&this.options.buttons[o].url!==""){n=c[o].replace("{url}",this.options.buttons[o].url)}if(n!=""&&m.options.urlCurl!==""){g.getJSON(n,function(r){if(typeof r.count!=="undefined"){var q=r.count+"";q=q.replace("\u00c2\u00a0","");p+=parseInt(q,10)}else{if(r.data&&r.data.length>0&&typeof r.data[0].total_count!=="undefined"){p+=parseInt(r.data[0].total_count,10)}else{if(typeof r.shares!=="undefined"){p+=parseInt(r.shares,10)}else{if(typeof r[0]!=="undefined"){p+=parseInt(r[0].total_posts,10)}else{if(typeof r[0]!=="undefined"){}}}}}m.options.count[o]=p;m.options.total+=p;m.renderer();m.rendererPerso()}).error(function(){m.options.count[o]=0;m.rendererPerso()})}else{m.renderer();m.options.count[o]=0;m.rendererPerso()}};k.prototype.rendererPerso=function(){var m=0;for(e in this.options.count){m++}if(m===this.options.shareTotal){this.options.render(this,this.options)}};k.prototype.renderer=function(){var n=this.options.total,m=this.options.template;if(this.options.shorterTotal===true){n=this.shorterTotal(n)}if(m!==""){m=m.replace("{total}",n);g(this.element).html(m)}else{g(this.element).html('<div class="box"><a class="count" href="#">'+n+"</a>"+(this.options.title!==""?'<a class="share" href="#">'+this.options.title+"</a>":"")+"</div>")}};k.prototype.shorterTotal=function(m){if(m>=1000000){m=(m/1000000).toFixed(2)+"M"}else{if(m>=1000){m=(m/1000).toFixed(1)+"k"}}return m};k.prototype.openPopup=function(m){a[m](this.options);if(this.options.enableTracking===true){var n={googlePlus:{site:"Google",action:"+1"},facebook:{site:"facebook",action:"like"},twitter:{site:"twitter",action:"tweet"},digg:{site:"digg",action:"add"},delicious:{site:"delicious",action:"add"},stumbleupon:{site:"stumbleupon",action:"add"},linkedin:{site:"linkedin",action:"share"},pinterest:{site:"pinterest",action:"pin"}};_gaq.push(["_trackSocial",n[m].site,n[m].action])}};k.prototype.simulateClick=function(){var m=g(this.element).html();g(this.element).html(m.replace(this.options.total,this.options.total+1))};k.prototype.update=function(m,n){if(m!==""){this.options.url=m}if(n!==""){this.options.text=n}};g.fn[h]=function(n){var m=arguments;if(n===b||typeof n==="object"){return this.each(function(){if(!g.data(this,"plugin_"+h)){g.data(this,"plugin_"+h,new k(this,n))}})}else{if(typeof n==="string"&&n[0]!=="_"&&n!=="init"){return this.each(function(){var o=g.data(this,"plugin_"+h);if(o instanceof k&&typeof o[n]==="function"){o[n].apply(o,Array.prototype.slice.call(m,1))}})}}}})(jQuery,window,document);


/**
 * Copyright (c) 2007-2012 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * @author Ariel Flesler
 * @version 1.4.3.1
 */
(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,e,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);


/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];
jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d)},easeInQuad:function(x,t,b,c,d){return c*(t/=d)*t+b},easeOutQuad:function(x,t,b,c,d){return -c*(t/=d)*(t-2)+b},easeInOutQuad:function(x,t,b,c,d){if((t/=d/2)<1){return c/2*t*t+b}return -c/2*((--t)*(t-2)-1)+b},easeInCubic:function(x,t,b,c,d){return c*(t/=d)*t*t+b},easeOutCubic:function(x,t,b,c,d){return c*((t=t/d-1)*t*t+1)+b},easeInOutCubic:function(x,t,b,c,d){if((t/=d/2)<1){return c/2*t*t*t+b}return c/2*((t-=2)*t*t+2)+b},easeInQuart:function(x,t,b,c,d){return c*(t/=d)*t*t*t+b},easeOutQuart:function(x,t,b,c,d){return -c*((t=t/d-1)*t*t*t-1)+b},easeInOutQuart:function(x,t,b,c,d){if((t/=d/2)<1){return c/2*t*t*t*t+b}return -c/2*((t-=2)*t*t*t-2)+b},easeInQuint:function(x,t,b,c,d){return c*(t/=d)*t*t*t*t+b},easeOutQuint:function(x,t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b},easeInOutQuint:function(x,t,b,c,d){if((t/=d/2)<1){return c/2*t*t*t*t*t+b}return c/2*((t-=2)*t*t*t*t+2)+b},easeInSine:function(x,t,b,c,d){return -c*Math.cos(t/d*(Math.PI/2))+c+b},easeOutSine:function(x,t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b},easeInOutSine:function(x,t,b,c,d){return -c/2*(Math.cos(Math.PI*t/d)-1)+b},easeInExpo:function(x,t,b,c,d){return(t==0)?b:c*Math.pow(2,10*(t/d-1))+b},easeOutExpo:function(x,t,b,c,d){return(t==d)?b+c:c*(-Math.pow(2,-10*t/d)+1)+b},easeInOutExpo:function(x,t,b,c,d){if(t==0){return b}if(t==d){return b+c}if((t/=d/2)<1){return c/2*Math.pow(2,10*(t-1))+b}return c/2*(-Math.pow(2,-10*--t)+2)+b},easeInCirc:function(x,t,b,c,d){return -c*(Math.sqrt(1-(t/=d)*t)-1)+b},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b},easeInOutCirc:function(x,t,b,c,d){if((t/=d/2)<1){return -c/2*(Math.sqrt(1-t*t)-1)+b}return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b},easeInElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0){return b}if((t/=d)==1){return b+c}if(!p){p=d*0.3}if(a<Math.abs(c)){a=c;var s=p/4}else{var s=p/(2*Math.PI)*Math.asin(c/a)}return -(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b},easeOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0){return b}if((t/=d)==1){return b+c}if(!p){p=d*0.3}if(a<Math.abs(c)){a=c;var s=p/4}else{var s=p/(2*Math.PI)*Math.asin(c/a)}return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b},easeInOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0){return b}if((t/=d/2)==2){return b+c}if(!p){p=d*(0.3*1.5)}if(a<Math.abs(c)){a=c;var s=p/4}else{var s=p/(2*Math.PI)*Math.asin(c/a)}if(t<1){return -0.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b}return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*0.5+c+b},easeInBack:function(x,t,b,c,d,s){if(s==undefined){s=1.70158}return c*(t/=d)*t*((s+1)*t-s)+b},easeOutBack:function(x,t,b,c,d,s){if(s==undefined){s=1.70158}return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b},easeInOutBack:function(x,t,b,c,d,s){if(s==undefined){s=1.70158}if((t/=d/2)<1){return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b}return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b},easeInBounce:function(x,t,b,c,d){return c-jQuery.easing.easeOutBounce(x,d-t,0,c,d)+b},easeOutBounce:function(x,t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b}else{if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+0.75)+b}else{if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+0.9375)+b}else{return c*(7.5625*(t-=(2.625/2.75))*t+0.984375)+b}}}},easeInOutBounce:function(x,t,b,c,d){if(t<d/2){return jQuery.easing.easeInBounce(x,t*2,0,c,d)*0.5+b}return jQuery.easing.easeOutBounce(x,t*2-d,0,c,d)*0.5+c*0.5+b}});

/*
 *
 * TERMS OF USE - EASING EQUATIONS
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 */



/* ------------------------------------------------------------------------
	Class: prettyPhoto
	Use: Lightbox clone for jQuery
	Author: Stephane Caron (http://www.no-margin-for-errors.com)
	Version: 3.1.6
------------------------------------------------------------------------- */
!function(e){function t(){var e=location.href;return hashtag=-1!==e.indexOf("#prettyPhoto")?decodeURI(e.substring(e.indexOf("#prettyPhoto")+1,e.length)):!1,hashtag&&(hashtag=hashtag.replace(/<|>/g,"")),hashtag}function i(){"undefined"!=typeof theRel&&(location.hash=theRel+"/"+rel_index+"/")}function p(){-1!==location.href.indexOf("#prettyPhoto")&&(location.hash="prettyPhoto")}function o(e,t){e=e.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var i="[\\?&]"+e+"=([^&#]*)",p=new RegExp(i),o=p.exec(t);return null==o?"":o[1]}e.prettyPhoto={version:"3.1.6"},e.fn.prettyPhoto=function(a){function s(){e(".pp_loaderIcon").hide(),projectedTop=scroll_pos.scrollTop+(I/2-f.containerHeight/2),projectedTop<0&&(projectedTop=0),$ppt.fadeTo(settings.animation_speed,1),$pp_pic_holder.find(".pp_content").animate({height:f.contentHeight,width:f.contentWidth},settings.animation_speed),$pp_pic_holder.animate({top:projectedTop,left:j/2-f.containerWidth/2<0?0:j/2-f.containerWidth/2,width:f.containerWidth},settings.animation_speed,function(){$pp_pic_holder.find(".pp_hoverContainer,#fullResImage").height(f.height).width(f.width),$pp_pic_holder.find(".pp_fade").fadeIn(settings.animation_speed),isSet&&"image"==h(pp_images[set_position])?$pp_pic_holder.find(".pp_hoverContainer").show():$pp_pic_holder.find(".pp_hoverContainer").hide(),settings.allow_expand&&(f.resized?e("a.pp_expand,a.pp_contract").show():e("a.pp_expand").hide()),!settings.autoplay_slideshow||P||v||e.prettyPhoto.startSlideshow(),settings.changepicturecallback(),v=!0}),m(),a.ajaxcallback()}function n(t){$pp_pic_holder.find("#pp_full_res object,#pp_full_res embed").css("visibility","hidden"),$pp_pic_holder.find(".pp_fade").fadeOut(settings.animation_speed,function(){e(".pp_loaderIcon").show(),t()})}function r(t){t>1?e(".pp_nav").show():e(".pp_nav").hide()}function l(e,t){resized=!1;var i=PEXETO.utils.checkIfMobile(),p=i?40:200;if(d(e,t),imageWidth=e,imageHeight=t,(k>j||b>I)&&doresize&&settings.allow_resize&&!$){for(resized=!0,fitting=!1;!fitting;)k>j?(imageWidth=j-p,imageHeight=t/e*imageWidth):b>I?(imageHeight=I-p,imageWidth=e/t*imageHeight):fitting=!0,b=imageHeight,k=imageWidth;i||(k>j||b>I)&&l(k,b),d(imageWidth,imageHeight)}return{width:Math.floor(imageWidth),height:Math.floor(imageHeight),containerHeight:Math.floor(b),containerWidth:Math.floor(k)+2*settings.horizontal_padding,contentHeight:Math.floor(y),contentWidth:Math.floor(w),resized:resized}}function d(t,i){t=parseFloat(t),i=parseFloat(i),$pp_details=$pp_pic_holder.find(".pp_details"),$pp_details.width(t),detailsHeight=parseFloat($pp_details.css("marginTop"))+parseFloat($pp_details.css("marginBottom")),$pp_details=$pp_details.clone().addClass(settings.theme).width(t).appendTo(e("body")).css({position:"absolute",top:-1e4}),detailsHeight+=$pp_details.height(),detailsHeight=detailsHeight<=34?36:detailsHeight,$pp_details.remove(),$pp_title=$pp_pic_holder.find(".ppt"),$pp_title.width(t),titleHeight=parseFloat($pp_title.css("marginTop"))+parseFloat($pp_title.css("marginBottom")),$pp_title=$pp_title.clone().appendTo(e("body")).css({position:"absolute",top:-1e4}),titleHeight+=$pp_title.height(),$pp_title.remove(),y=i+detailsHeight,w=t,b=y+titleHeight+$pp_pic_holder.find(".pp_top").height()+$pp_pic_holder.find(".pp_bottom").height(),k=t}function h(e){return e.match(/youtube\.com\/watch/i)||e.match(/youtu\.be/i)?"youtube":e.match(/vimeo\.com/i)?"vimeo":e.match(/\b.mov\b/i)?"quicktime":e.match(/\b.swf\b/i)?"flash":e.match(/\biframe=true\b/i)?"iframe":e.match(/\bajax=true\b/i)?"ajax":e.match(/\bcustom=true\b/i)?"custom":"#"==e.substr(0,1)?"inline":"image"}function c(){if(doresize&&"undefined"!=typeof $pp_pic_holder){if(scroll_pos=_(),contentHeight=$pp_pic_holder.height(),contentwidth=$pp_pic_holder.width(),projectedTop=I/2+scroll_pos.scrollTop-contentHeight/2,projectedTop<0&&(projectedTop=0),contentHeight>I)return;$pp_pic_holder.css({top:projectedTop,left:j/2+scroll_pos.scrollLeft-contentwidth/2})}}function _(){return self.pageYOffset?{scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset}:document.documentElement&&document.documentElement.scrollTop?{scrollTop:document.documentElement.scrollTop,scrollLeft:document.documentElement.scrollLeft}:document.body?{scrollTop:document.body.scrollTop,scrollLeft:document.body.scrollLeft}:void 0}function g(){I=e(window).height(),j=e(window).width(),"undefined"!=typeof $pp_overlay&&$pp_overlay.height(e(document).height()).width(j)}function m(){isSet&&settings.overlay_gallery&&"image"==h(pp_images[set_position])?(itemWidth=57,navWidth="facebook"==settings.theme||"pp_default"==settings.theme?50:30,itemsPerPage=Math.floor((f.containerWidth-100-navWidth)/itemWidth),itemsPerPage=itemsPerPage<pp_images.length?itemsPerPage:pp_images.length,totalPage=Math.ceil(pp_images.length/itemsPerPage)-1,0==totalPage?(navWidth=0,$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").hide()):$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").show(),galleryWidth=itemsPerPage*itemWidth,fullGalleryWidth=pp_images.length*itemWidth,$pp_gallery.css("margin-left",-(galleryWidth/2+navWidth/2)).find("div:first").width(galleryWidth+5).find("ul").width(fullGalleryWidth).find("li.selected").removeClass("selected"),goToPage=Math.floor(set_position/itemsPerPage)<totalPage?Math.floor(set_position/itemsPerPage):totalPage,e.prettyPhoto.changeGalleryPage(goToPage),$pp_gallery_li.filter(":eq("+set_position+")").addClass("selected")):$pp_pic_holder.find(".pp_content").unbind("mouseenter mouseleave")}function u(t){if(settings.social_tools&&(facebook_like_link=settings.social_tools.replace("{location_href}",encodeURIComponent(location.href))),settings.markup=settings.markup.replace("{pp_social}",""),e("body").append(settings.markup),$pp_pic_holder=e(".pp_pic_holder"),$ppt=e(".ppt"),$pp_overlay=e("div.pp_overlay"),isSet&&settings.overlay_gallery){currentGalleryPage=0,toInject="";for(var i=0;i<pp_images.length;i++)pp_images[i].match(/\b(jpg|jpeg|png|gif)\b/gi)?(classname="",img_src=pp_images[i]):(classname="default",img_src=""),toInject+="<li class='"+classname+"'><a href='#'><img src='"+img_src+"' width='50' alt='' /></a></li>";toInject=settings.gallery_markup.replace(/{gallery}/g,toInject),$pp_pic_holder.find("#pp_full_res").after(toInject),$pp_gallery=e(".pp_pic_holder .pp_gallery"),$pp_gallery_li=$pp_gallery.find("li"),$pp_gallery.find(".pp_arrow_next").click(function(){return e.prettyPhoto.changeGalleryPage("next"),e.prettyPhoto.stopSlideshow(),!1}),$pp_gallery.find(".pp_arrow_previous").click(function(){return e.prettyPhoto.changeGalleryPage("previous"),e.prettyPhoto.stopSlideshow(),!1}),$pp_pic_holder.find(".pp_content").hover(function(){$pp_pic_holder.find(".pp_gallery:not(.disabled)").fadeIn()},function(){$pp_pic_holder.find(".pp_gallery:not(.disabled)").fadeOut()}),itemWidth=57,$pp_gallery_li.each(function(t){e(this).find("a").click(function(){return e.prettyPhoto.changePage(t),e.prettyPhoto.stopSlideshow(),!1})})}settings.slideshow&&($pp_pic_holder.find(".pp_nav").prepend('<a href="#" class="pp_play">Play</a>'),$pp_pic_holder.find(".pp_nav .pp_play").click(function(){return e.prettyPhoto.startSlideshow(),!1})),$pp_pic_holder.attr("class","pp_pic_holder "+settings.theme),$pp_overlay.css({opacity:0,height:e(document).height(),width:e(window).width()}).bind("click",function(){settings.modal||e.prettyPhoto.close()}),e("a.pp_close").bind("click",function(){return e.prettyPhoto.close(),!1}),settings.allow_expand&&e("a.pp_expand").bind("click",function(t){return e(this).hasClass("pp_expand")?(e(this).removeClass("pp_expand").addClass("pp_contract"),doresize=!1):(e(this).removeClass("pp_contract").addClass("pp_expand"),doresize=!0),n(function(){e.prettyPhoto.open()}),!1}),$pp_pic_holder.find(".pp_previous, .pp_nav .pp_arrow_previous").bind("click",function(){return e.prettyPhoto.changePage("previous"),e.prettyPhoto.stopSlideshow(),!1}),$pp_pic_holder.find(".pp_next, .pp_nav .pp_arrow_next").bind("click",function(){return e.prettyPhoto.changePage("next"),e.prettyPhoto.stopSlideshow(),!1}),c()}a=jQuery.extend({hook:"rel",animation_speed:"fast",ajaxcallback:function(){},slideshow:5e3,autoplay_slideshow:!1,opacity:.8,show_title:!0,allow_resize:!0,allow_expand:!0,default_width:500,default_height:344,counter_separator_label:"/",theme:"pp_default",horizontal_padding:20,hideflash:!1,wmode:"opaque",autoplay:!0,modal:!1,deeplinking:!0,overlay_gallery:!0,overlay_gallery_max:30,keyboard_shortcuts:!0,changepicturecallback:function(){},callback:function(){},ie6_fallback:!0,markup:'<div class="pp_pic_holder"> 						<div class="ppt">&nbsp;</div> 						<div class="pp_top"> 							<div class="pp_left"></div> 							<div class="pp_middle"></div> 							<div class="pp_right"></div> 						</div> 						<div class="pp_content_container"> 							<div class="pp_left"> 							<div class="pp_right"> 								<div class="pp_content"> 									<div class="pp_loaderIcon"></div> 									<div class="pp_fade"> 										<a href="#" class="pp_expand" title="Expand the image">Expand</a> 										<div class="pp_hoverContainer"> 											<a class="pp_next" href="#">next</a> 											<a class="pp_previous" href="#">previous</a> 										</div> 										<div id="pp_full_res"></div> 										<div class="pp_details"> 											<div class="pp_nav"> 												<a href="#" class="pp_arrow_previous">Previous</a> 												<p class="currentTextHolder">0/0</p> 												<a href="#" class="pp_arrow_next">Next</a> 											</div> 											<p class="pp_description"></p> 											<div class="pp_social">{pp_social}</div> 											<a class="pp_close" href="#">Close</a> 										</div> 									</div> 								</div> 							</div> 							</div> 						</div> 						<div class="pp_bottom"> 							<div class="pp_left"></div> 							<div class="pp_middle"></div> 							<div class="pp_right"></div> 						</div> 					</div> 					<div class="pp_overlay"></div>',gallery_markup:'<div class="pp_gallery"> 								<a href="#" class="pp_arrow_previous">Previous</a> 								<div> 									<ul> 										{gallery} 									</ul> 								</div> 								<a href="#" class="pp_arrow_next">Next</a> 							</div>',image_markup:'<img id="fullResImage" src="{path}" />',flash_markup:'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',quicktime_markup:'<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',iframe_markup:'<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',inline_markup:'<div class="pp_inline">{content}</div>',custom_markup:"",social_tools:'<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div><div class="facebook"><iframe src="//www.facebook.com/plugins/like.php?locale=en_US&href={location_href}&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe></div>'},a);var f,v,y,w,b,k,P,x=this,$=!1,I=e(window).height(),j=e(window).width();return doresize=!0,scroll_pos=_(),e(window).unbind("resize.prettyphoto").bind("resize.prettyphoto",function(){c(),g()}),a.keyboard_shortcuts&&e(document).unbind("keydown.prettyphoto").bind("keydown.prettyphoto",function(t){if("undefined"!=typeof $pp_pic_holder&&$pp_pic_holder.is(":visible"))switch(t.keyCode){case 37:e.prettyPhoto.changePage("previous"),t.preventDefault();break;case 39:e.prettyPhoto.changePage("next"),t.preventDefault();break;case 27:settings.modal||e.prettyPhoto.close(),t.preventDefault()}}),e.prettyPhoto.initialize=function(){return settings=a,"pp_default"==settings.theme&&(settings.horizontal_padding=16),theRel=e(this).attr(settings.hook),galleryRegExp=/\[(?:.*)\]/,isSet=galleryRegExp.exec(theRel)?!0:!1,pp_images=isSet?jQuery.map(x,function(t,i){return-1!=e(t).attr(settings.hook).indexOf(theRel)?e(t).attr("href"):void 0}):e.makeArray(e(this).attr("href")),pp_titles=isSet?jQuery.map(x,function(t,i){return-1!=e(t).attr(settings.hook).indexOf(theRel)?e(t).find("img").attr("alt")?e(t).find("img").attr("alt"):"":void 0}):e.makeArray(e(this).find("img").attr("alt")),pp_descriptions=isSet?jQuery.map(x,function(t,i){return-1!=e(t).attr(settings.hook).indexOf(theRel)?e(t).attr("title")?e(t).attr("title"):"":void 0}):e.makeArray(e(this).attr("title")),pp_images.length>settings.overlay_gallery_max&&(settings.overlay_gallery=!1),set_position=jQuery.inArray(e(this).attr("href"),pp_images),rel_index=isSet?set_position:e("a["+settings.hook+"^='"+theRel+"']").index(e(this)),u(this),settings.allow_resize&&e(window).bind("scroll.prettyphoto",function(){c()}),e.prettyPhoto.open(),!1},e.prettyPhoto.open=function(t){return"undefined"==typeof settings&&(settings=a,pp_images=e.makeArray(arguments[0]),pp_titles=e.makeArray(arguments[1]?arguments[1]:""),pp_descriptions=e.makeArray(arguments[2]?arguments[2]:""),isSet=pp_images.length>1?!0:!1,set_position=arguments[3]?arguments[3]:0,u(t.target)),settings.hideflash&&e("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css("visibility","hidden"),r(e(pp_images).size()),e(".pp_loaderIcon").show(),settings.deeplinking&&i(),settings.social_tools&&(facebook_like_link=settings.social_tools.replace("{location_href}",encodeURIComponent(location.href)),$pp_pic_holder.find(".pp_social").html(facebook_like_link)),$ppt.is(":hidden")&&$ppt.css("opacity",0).show(),$pp_overlay.show().fadeTo(settings.animation_speed,settings.opacity),$pp_pic_holder.find(".currentTextHolder").text(set_position+1+settings.counter_separator_label+e(pp_images).size()),"undefined"!=typeof pp_descriptions[set_position]&&""!=pp_descriptions[set_position]?$pp_pic_holder.find(".pp_description").show().html(unescape(pp_descriptions[set_position])):$pp_pic_holder.find(".pp_description").hide(),movie_width=parseFloat(o("width",pp_images[set_position]))?o("width",pp_images[set_position]):settings.default_width.toString(),movie_height=parseFloat(o("height",pp_images[set_position]))?o("height",pp_images[set_position]):settings.default_height.toString(),$=!1,-1!=movie_height.indexOf("%")&&(movie_height=parseFloat(e(window).height()*parseFloat(movie_height)/100-150),$=!0),-1!=movie_width.indexOf("%")&&(movie_width=parseFloat(e(window).width()*parseFloat(movie_width)/100-150),$=!0),$pp_pic_holder.fadeIn(function(){switch($ppt.html(settings.show_title&&""!=pp_titles[set_position]&&"undefined"!=typeof pp_titles[set_position]?unescape(pp_titles[set_position]):"&nbsp;"),imgPreloader="",skipInjection=!1,h(pp_images[set_position])){case"image":imgPreloader=new Image,nextImage=new Image,isSet&&set_position<e(pp_images).size()-1&&(nextImage.src=pp_images[set_position+1]),prevImage=new Image,isSet&&pp_images[set_position-1]&&(prevImage.src=pp_images[set_position-1]),$pp_pic_holder.find("#pp_full_res")[0].innerHTML=settings.image_markup.replace(/{path}/g,pp_images[set_position]),imgPreloader.onload=function(){f=l(imgPreloader.width,imgPreloader.height),s()},imgPreloader.onerror=function(){alert("Image cannot be loaded. Make sure the path is correct and image exist."),e.prettyPhoto.close()},imgPreloader.src=pp_images[set_position];break;case"youtube":f=l(movie_width,movie_height),movie_id=o("v",pp_images[set_position]),""==movie_id&&(movie_id=pp_images[set_position].split("youtu.be/"),movie_id=movie_id[1],movie_id.indexOf("?")>0&&(movie_id=movie_id.substr(0,movie_id.indexOf("?"))),movie_id.indexOf("&")>0&&(movie_id=movie_id.substr(0,movie_id.indexOf("&")))),movie="http://www.youtube.com/embed/"+movie_id,o("rel",pp_images[set_position])?movie+="?rel="+o("rel",pp_images[set_position]):movie+="?rel=1",settings.autoplay&&(movie+="&autoplay=1"),toInject=settings.iframe_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,movie);break;case"vimeo":f=l(movie_width,movie_height),movie_id=pp_images[set_position];var t=/http(s?):\/\/(www\.)?vimeo.com\/(\d+)/,i=movie_id.match(t);movie="http://player.vimeo.com/video/"+i[3]+"?title=0&amp;byline=0&amp;portrait=0",settings.autoplay&&(movie+="&autoplay=1;"),vimeo_width=f.width+"/embed/?moog_width="+f.width,toInject=settings.iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,f.height).replace(/{path}/g,movie);break;case"quicktime":f=l(movie_width,movie_height),f.height+=15,f.contentHeight+=15,f.containerHeight+=15,toInject=settings.quicktime_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,pp_images[set_position]).replace(/{autoplay}/g,settings.autoplay);break;case"flash":f=l(movie_width,movie_height),flash_vars=pp_images[set_position],flash_vars=flash_vars.substring(pp_images[set_position].indexOf("flashvars")+10,pp_images[set_position].length),filename=pp_images[set_position],filename=filename.substring(0,filename.indexOf("?")),toInject=settings.flash_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,filename+"?"+flash_vars);break;case"iframe":f=l(movie_width,movie_height),frame_url=pp_images[set_position],frame_url=frame_url.substr(0,frame_url.indexOf("iframe")-1),toInject=settings.iframe_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{path}/g,frame_url);break;case"ajax":doresize=!1,f=l(movie_width,movie_height),doresize=!0,skipInjection=!0,e.get(pp_images[set_position],function(e){toInject=settings.inline_markup.replace(/{content}/g,e),$pp_pic_holder.find("#pp_full_res")[0].innerHTML=toInject,s()});break;case"custom":f=l(movie_width,movie_height),toInject=settings.custom_markup;break;case"inline":myClone=e(pp_images[set_position]).clone().append('<br clear="all" />').css({width:settings.default_width}).wrapInner('<div id="pp_full_res"><div class="pp_inline"></div></div>').appendTo(e("body")).show(),doresize=!1,f=l(e(myClone).width(),e(myClone).height()),doresize=!0,e(myClone).remove(),toInject=settings.inline_markup.replace(/{content}/g,e(pp_images[set_position]).html())}imgPreloader||skipInjection||($pp_pic_holder.find("#pp_full_res")[0].innerHTML=toInject,s())}),!1},e.prettyPhoto.changePage=function(t){currentGalleryPage=0,"previous"==t?(set_position--,set_position<0&&(set_position=e(pp_images).size()-1)):"next"==t?(set_position++,set_position>e(pp_images).size()-1&&(set_position=0)):set_position=t,rel_index=set_position,doresize||(doresize=!0),settings.allow_expand&&e(".pp_contract").removeClass("pp_contract").addClass("pp_expand"),n(function(){e.prettyPhoto.open()})},e.prettyPhoto.changeGalleryPage=function(e){"next"==e?(currentGalleryPage++,currentGalleryPage>totalPage&&(currentGalleryPage=0)):"previous"==e?(currentGalleryPage--,currentGalleryPage<0&&(currentGalleryPage=totalPage)):currentGalleryPage=e,slide_speed="next"==e||"previous"==e?settings.animation_speed:0,slide_to=currentGalleryPage*itemsPerPage*itemWidth,$pp_gallery.find("ul").animate({left:-slide_to},slide_speed)},e.prettyPhoto.startSlideshow=function(){"undefined"==typeof P?($pp_pic_holder.find(".pp_play").unbind("click").removeClass("pp_play").addClass("pp_pause").click(function(){return e.prettyPhoto.stopSlideshow(),!1}),P=setInterval(e.prettyPhoto.startSlideshow,settings.slideshow)):e.prettyPhoto.changePage("next")},e.prettyPhoto.stopSlideshow=function(){$pp_pic_holder.find(".pp_pause").unbind("click").removeClass("pp_pause").addClass("pp_play").click(function(){return e.prettyPhoto.startSlideshow(),!1}),clearInterval(P),P=void 0},e.prettyPhoto.close=function(){$pp_overlay.is(":animated")||(e.prettyPhoto.stopSlideshow(),$pp_pic_holder.stop().find("object,embed").css("visibility","hidden"),e("div.pp_pic_holder,div.ppt,.pp_fade").fadeOut(settings.animation_speed,function(){e(this).remove()}),$pp_overlay.fadeOut(settings.animation_speed,function(){settings.hideflash&&e("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css("visibility","visible"),e(this).remove(),e(window).unbind("scroll.prettyphoto"),p(),settings.callback(),doresize=!0,v=!1,delete settings}))},!pp_alreadyInitialized&&t()&&(pp_alreadyInitialized=!0,hashIndex=t(),hashRel=hashIndex,hashIndex=hashIndex.substring(hashIndex.indexOf("/")+1,hashIndex.length-1),hashRel=hashRel.substring(0,hashRel.indexOf("/")),setTimeout(function(){e("a["+a.hook+"^='"+hashRel+"']:eq("+hashIndex+")").trigger("click")},50)),this.unbind("click.prettyphoto").bind("click.prettyphoto",e.prettyPhoto.initialize)}}(jQuery);var pp_alreadyInitialized=!1;


// Generated by CoffeeScript 1.4.0
/*
jQuery Waypoints - v2.0.2
Copyright (c) 2011-2013 Caleb Troughton
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
*/
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,a,c,h,d,p,y,v,w,g,m;i=n(r);c=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;a={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};t.data(u,this.id);a[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||c)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(c&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete a[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=t.data(w))!=null?o:[];i.push(this.id);t.data(w,i)}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=n(t).data(w);if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=a[i.data(u)];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke(this,"disable")},enable:function(){return d._invoke(this,"enable")},destroy:function(){return d._invoke(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t,e){t.each(function(){var t;t=l.getWaypointsByElement(this);return n.each(t,function(t,n){n[e]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(a,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=a[n(t).data(u)])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=a[n(t).data(u)];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.on('load', function(){return n[m]("refresh")})})}).call(this);



/**
 * Portfolio item slider (carousel) - displays a set of images, separated by pages.
 * The pages can be changed by clicking on arrows with an animation.
 * @author Pexeto
 * http://pexetothemes.com
 */
(function($) {
	"use strict";

	var carouselId = 0;

	$.fn.pexetoCarousel = function(options) {
		carouselId++;

			var defaults        = {
				//set the default options (can be overwritten from the calling function)
				minItemWidth        : 290,
				namespace           : 'carousel' + carouselId,
				itemMargin          : 12,
				shadowWidth         : 0,
				selfDisplay         : true, //if set to true, the carousel will get displayed 
				//as soon as it is loaded. Otherwise, the calling code would be
				//responsible to display the carousel (set its opacity to 1)
				
				//selectors and classes
				holderSelector      : '.pc-holder',
				pageWrapperSelector : '.pc-page-wrapper',
				wrapperSel          : '.pc-wrapper',
				itemSelector        : '.pc-item',
				titleSelector       : '.portfolio-project-title',
				hoverClass          : 'portfolio-hover',
				headerSelector      : '.pc-header'
			},
			o            = $.extend(defaults, options),
			//define some variables that will be used globally within the script
			$container   = this,
			$root        = $container.find(o.holderSelector).eq(0),
			$items       = $root.find(o.itemSelector),
			$wrapper     = $container.find(o.wrapperSel),
			$header      = $container.find(o.headerSelector),
			pageNumber   = 0,
			itemsNumber  = $items.length,
			currentPage  = 0,
			inAnimation  = false,
			pageWidth    = $root.find(o.pageWrapperSelector).eq(0).width(),
			itemsPerPage = 0,
			columns      = 0,
			$prevArrow   = null,
			$nextArrow   = null;


		/**
		 * Inits the main functionality.
		 */

		function init() {

			var defWidth = parseInt($items.eq(0).data('defwidth'), 10);
			if($container.hasClass('pc-no-spacing')){
				o.itemMargin = 0;
			}

			if(defWidth && defWidth>100){
				o.minItemWidth = defWidth - 70;
			}

			pageNumber = $root.find(o.pageWrapperSelector).length;

			if(pageNumber > 1) {
				//show the arrows and add the animation functionality if there are 
				//more than one pages
				buildNavigation();
			}

			setImageSize();

			bindEventHandlers();

			if(o.selfDisplay) {
				$container.animate({
					opacity: 1
				});
			}

			itemsPerPage = $root.find(o.pageWrapperSelector + ':first' + ' ' + o.itemSelector).length;

		}

		/**
		 * Sets the image size according to the current wrapper width.
		 */
		function setImageSize() {
			var itemWidth, rootWidth;

			columns = Math.floor(($container.width() - o.itemMargin) / (o.minItemWidth + o.itemMargin));

			if(columns <= 1) {
				columns = 2;
			}
			itemWidth = Math.floor(($container.width() + o.itemMargin - 2 * o.shadowWidth) / columns) - o.itemMargin;

			$items.width(itemWidth+1);

			pageWidth = $root.find(o.pageWrapperSelector).eq(0).width();

			rootWidth = pageNumber * pageWidth + 1000;
			$root.css({
				width: rootWidth
			});

			setNavigationVisibility();

		}

		/**
		 * Binds a change slide event handler to the root, so that it can be animated
		 * when any of the navigation buttons has been clicked.
		 */

		function bindEventHandlers() {
			if(pageNumber > 1) {

				//mobile device finger slide events
				$root.touchwipe({
					wipeLeft: doOnNextSlide,
					wipeRight: doOnPreviousSlide,
					preventDefaultEvents: false
				});

				$(window).on('resize.' + o.namespace, doOnWindowResize);
			}

			$root.on('destroy' + o.namespace, doOnDestroy);
		}

		/**
		 * Changes the current slide of items, to another one.
		 * @param  {int} index the index of the new slide to show
		 */
		function changeSlide(index) {
			if(!inAnimation) {
				inAnimation = true;
				var margin = getPageMarginPosition(index);
				$root.animate({
					marginLeft: [margin, 'easeOutExpo']
				}, 800, function() {
					inAnimation = false;
					currentPage = index;
				});
			}
		}

		/**
		 * Calculates the position offset (margin) of the current slide 
		 * according to the current wrapper width.
		 * @param  {int} index the inex of the current slide
		 * @return {int}       the calculated margin
		 */
		function getPageMarginPosition(index) {
			setSizes();
			return -index * pageWidth - o.itemMargin / 2 + o.shadowWidth;
		}

		function setSizes(){
			setImageSize();
			pageWidth = $root.find(o.pageWrapperSelector).eq(0).width();
		}

		/**
		 * On window resize event handler - resizes the wrapper and then the
		 * inner images according to the current window size.
		 */
		function doOnWindowResize() {
			setSizes();
			$root.css({
				marginLeft: getPageMarginPosition(currentPage)
			});
		}

		/**
		 * On next slide event handler - shows the next slide if there is one.
		 */
		function doOnNextSlide() {
			if(!inAnimation){
				if(!isLastPageVisible()) {
					var index = currentPage < pageNumber - 1 ? currentPage + 1 : 0;
					changeSlide(index);
				} else {
					animateLastPage(true);
				}
			}

		}

		/**
		 * On previous slide event handler - shows the previous slide if there
		 * is one.
		 */
		function doOnPreviousSlide() {
			if(!inAnimation){
				if(currentPage > 0) {
					changeSlide(currentPage - 1);
				} else {
					animateLastPage(false);
				}
			}

		}

		/**
		 * Animates the carousel when there are no more slides left and the
		 * user still tries to open the previous/next slide - animates it in a
		 * way to show that there are no more slides.
		 * @param  {boolean} last setting whether this is the last slide (when
		 * set to true) or the first slide (when set to false)
		 */
		function animateLastPage(last) {
			var i = last ? -1 : 1;
			$root.stop().animate({
				left: i * 10
			}, 100, function() {
				$(this).stop().animate({
					left: 0
				}, 300);
			});
		}

		/**
		 * Checks if the last slide/page is visible on the carousel.
		 * @return {boolean} true if it is visible and false if it is not
		 */
		function isLastPageVisible() {
			if((itemsNumber - currentPage * itemsPerPage) <= columns) {
				return true;
			} 
			
			return false;
		}

		/**
		 * Checks if all of the slides/pages are visible on the carousel.
		 * @return {boolean} true if they are visible and false if they are not
		 */
		function areAllPagesVisible() {
			return(itemsNumber <= columns && currentPage === 0) ? true : false;
		}

		/**
		 * Builds the navigation (arrows) to change the slides.
		 */
		function buildNavigation() {

			//next items arrow
			$prevArrow = $('<div />', {
				'class': 'pc-next hover'
			}).on('click.' + o.namespace, doOnNextSlide).appendTo($wrapper);

			//previous items arrow
			$nextArrow = $('<div />', {
				'class': 'pc-prev hover'
			}).on('click.' + o.namespace, doOnPreviousSlide).appendTo($wrapper);
		}

		/**
		 * Shows the navigation arrows when there are some slides that are not
		 * visible and hides them when all of the slides are visible.
		 */
		function setNavigationVisibility() {
			if(areAllPagesVisible()) {
				if($prevArrow){
					$prevArrow.hide();
				}
				if($nextArrow){
					$nextArrow.hide();
				}
			} else {
				if($prevArrow){
					$prevArrow.show();
				}
				if($nextArrow){
					$nextArrow.show();
				}
			}

		}

		/**
		 * On destroy event handler- removes all the registered event listeners.
		 */
		function doOnDestroy() {
			$(window).off('.' + o.namespace);
			$root.off('.' + o.namespace);
			$prevArrow.off('.' + o.namespace);
			$nextArrow.off('.' + o.namespace);
		}


		if($root.length) {
			init();
		}

	};
}(jQuery));

(function($) {
	"use strict";

	//CSS 3 transition support detection - code from: https://gist.github.com/jonraasch/373874
	var thisBody = document.body || document.documentElement,
	thisStyle = thisBody.style,
	supportTransition = thisStyle.transition !== undefined || thisStyle.WebkitTransition !== undefined || thisStyle.MozTransition !== undefined || thisStyle.MsTransition !== undefined || thisStyle.OTransition !== undefined;

	$.fn.pexetoTransit = function(){
		var properties={},
			callback = null,
			namespace = 'pexetoTransit',
			callbackCalled = false;

		if(!arguments.length || typeof arguments[0]!=='object'){
			return $(this);
		}

		if(supportTransition){
			properties = arguments[0];

			if(arguments[1]){
				callback = arguments[1];
				$(this).on('transitionend.'+namespace+' webkitTransitionEnd.'+namespace+' oTransitionEnd.'+namespace+' MSTransitionEnd.'+namespace, function(e){
					if(!callbackCalled){
						callback.call();
						$(this).off(namespace);
						callbackCalled = true;
					}
					
				});
			}
			$(this).css(properties);
		}else{
			$.fn.animate.apply($(this), arguments);
		}

		return $(this);
	};

}(jQuery));


/**
 * Pexeto Contact Form - contains all the contact form functionality.
 * @author Pexeto
 * http://pexetothemes.com
 */
(function($) {
	"use strict";

	$.fn.pexetoContactForm = function(options) {
		var defaults = {
			//set the default options (CAN BE OVERWRITTEN BY THE INITIALIZATION CODE)
			ajaxurl             : '',
			invalidClass        : 'invalid',
			afterValidClass     : 'after-validation',
			captcha             : false,
			
			//selectors
			submitSel           : '.send-button',
			errorSel            : '.error-message',
			statusSel           : '.contact-status',
			sentSel             : '.sent-message',
			loaderSel           : '.contact-loader',
			checkSel            : '.check',
			failSel             : '.fail-message',
			inputWrapperSel     : '.contact-input-wrapper',
			
			//texts
			wrongCaptchaText    : 'The text you have entered did not match the text on the image. Please try again.',
			failText            : 'An error occurred. Message not sent',
			validationErrorText : 'Please fill in all the fields correctly',
			messageSentText     : 'Message sent'
		};


		var o = $.extend(defaults, options);
		o.ajaxurl = $(this).attr('action');

		//define some variables that will be used globally within the script
		var $root           = $(this),
			$requiredFields = $root.find('input.required, textarea.required, #recaptcha_response_field'),
			$fields         = $root.find('input, textarea'),
			$errorBox       = $root.find(o.errorSel),
			$sentBox        = $root.find(o.sentSel),
			$loader         = $root.find(o.loaderSel),
			$check          = $root.find(o.checkSel);

		/**
		 * Inits the main functionality.
		 */

		function init() {
			$fields.on('focus', doOnFieldsFocus);
			$root.find(o.submitSel).eq(0).on('click', doOnSendClicked);
		}

		/**
		 * On send button click event handler. Sends an AJAX request to send the message if the
		 * entered input data is valid.
		 * @param  {object} e the event object
		 */

		function doOnSendClicked(e) {
			//set the send button click handler functionality
			e.preventDefault();
			var isValid = validateForm();

			if(isValid) {
				//the form is valid, send the email
				$loader.css({
					visibility: 'visible'
				});
				//hide all the message boxes
				$errorBox.slideUp();

				var data = $root.serialize() + '&action=pexeto_send_email';
				//send the AJAX request
				sendAjaxRequest(data);
			}
		}

		/**
		 * Sends the AJAX request to send the message.
		 * @param  {object} data the data needed for the request
		 */
		function sendAjaxRequest(data) {
			$.ajax({
				url: PEXETO.ajaxurl,
				data: data,
				dataType: 'json',
				type: 'post'
			}).done(function(res) {
				//reset the form
				$loader.css({
					visibility: 'hidden'
				});
				if(res.success) {
					//the message was sent successfully
					$root.get(0).reset();
					hideAfterValidationErrors();
					//show the confirmation check icon
					$check.css({
						visibility: 'visible'
					}, 200);


					$sentBox.html(o.messageSentText).slideDown();
					$.scrollTo($root, {
						duration:500,
						offset:{top:-80}
					});

					setTimeout(function() {
						//hide the confirmation boxes
						$sentBox.slideUp();
						$check.css({
							visibility: 'hidden'
						}, 200);
					}, 3000);
				} else {
					//the message was not sent successfully, show an error
					if(o.captcha && res.captcha_failed) {
						//captcha did not validate
						Recaptcha.reload();
						showErrorMessage(o.wrongCaptchaText);
					} else {
						//another error occurred, show general error message
						showErrorMessage(o.failText);
					}
				}
			}).fail(function() {
				//the message was not sent successfully, show an error
				$loader.css({
					visibility: 'hidden'
				});
				showErrorMessage(o.failText);
			});
		}


		/**
		 * Validates the form input.
		 * @return {boolean} true if the form is valid.
		 */

		function validateForm() {
			var isValid = true;

			hideValidationErrors();
			$requiredFields.each(function() {
				var $elem = $(this);
				if(!$.trim($elem.val()) || ($elem.hasClass('email') && !isValidEmail($elem.val()))) {
					//this field value is not valid display an error
					showError($elem);
					isValid = false;
				}
			});

			if(!isValid) {
				//show an error box
				showErrorMessage(o.validationErrorText);
			}
			return isValid;
		}

		/**
		 * Hides all the validation errors from the required fields.
		 */

		function hideValidationErrors() {
			$requiredFields.removeClass(o.invalidClass).removeClass(o.afterValidClass);
		}

		/**
		 * Hides the after validation errors and styles from the required fields. After validation
		 * means when there was a previous validation failure and the user after that clicks on a
		 * failed field, which gets a new after validation style.
		 */

		function hideAfterValidationErrors() {
			$requiredFields.each(function() {
				var $wrapper = $(this).parents(o.inputWrapperSel).eq(0),
					$errorElem = $wrapper.length ? $wrapper : $(this);
				$errorElem.removeClass(o.afterValidClass);
			});
		}

		/**
		 * Adds an error message to an element.
		 * @param  {object} $elem jQuery object element (input element) to which to add the message
		 */

		function showError($elem) {
			var $wrapper = $elem.parents(o.inputWrapperSel).eq(0),
				$errorElem = $wrapper.length ? $wrapper : $elem;
			$errorElem.addClass(o.invalidClass);
		}

		/**
		 * Displays a fail to send message error.
		 */

		function showErrorMessage(message) {
			$errorBox.html(message).slideDown();
			$.scrollTo($root, {
				duration:500,
				offset:{top:-80}
			});
		}

		/**
		 * On field focus in event handler. If the field is required and failed a validation,
		 * another after validation class is added to it when it gains focus.
		 */

		function doOnFieldsFocus() {
			var $wrapper = $(this).parents(o.inputWrapperSel).eq(0),
				$errorElem = $wrapper.length ? $wrapper : $(this);
			if($errorElem.hasClass(o.invalidClass)) {
				$errorElem.addClass(o.afterValidClass);
			}
			$errorElem.removeClass(o.invalidClass);
		}


		/**
		 * Checks if an email address is a valid one.
		 *
		 * @param {string} email the email address to validate
		 * @return {boolean} true if the address is a valid one
		 */

		function isValidEmail(email) {
			var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
			return pattern.test(email);
		}

		if($root.length) {
			init();
		}

	};
}(jQuery));

/**
 * Define a placeholder check to jQuery.support
 * code adapted from: http://diveintohtml5.org/detect.html#input-placeholder
 */
jQuery.support.placeholder = (function() {
	var i = document.createElement('input');
	return 'placeholder' in i;
})();



(function($) {
	"use strict";

	/**
	 * Calls a callback function when all the images from a collection have been loaded.
	 * A "callback" parameter should be added - the function to be called when all the
	 * images are loaded.
	 * @param  {object} options object literal containing the initialization options
	 *
	 * Dependencies: jQuery (http://jquery.com/)
	 *
	 * Example usage: $('.test img').pexetoOnImgLoaded({callback:showImages});
	 *
	 * @author Pexeto
	 * http://pexetothemes.com
	 */
	$.fn.pexetoOnImgLoaded = function(options) {
		var defaults     = {},
			o            = $.extend(defaults, options),
			$images      = $(this),
			ie           = PEXETO.getBrowser().msie;


		/**
		 * Contains the main plugin functionality - once all the images are loaded, calls the
		 * callback function.
		 */

		function init() {
			var imagesNum = $images.length,
				imgLoaded = 0;

			if(!imagesNum) {
				o.callback.call(this);
				return;
			}

			$images.each(function() {

				$(this).one('load', function(e) {
					e.stopPropagation();
					imgLoaded++;
					if(imgLoaded === imagesNum) {
						//all the images are loaded, call the callback function
						o.callback.call(this);
						$(this).off('load');
					}
				}).on('error', function(){
					$(this).trigger('load');
				});

				if(this.complete || (ie && this.width)) {
					$(this).trigger('load');
				}
			});
		}

		init();
	};
}(jQuery));


(function($) {
	"use strict";

	/**
	 * Pexeto Tabs Widget.
	 * Dependencies : jQuery
	 *
	 * @author Pexeto
 	 * http://pexetothemes.com
	 */
	$.fn.pexetoTabs = function(options) {
		var defaults = {
			//selectors and classes
			tabSel       : '.tabs li',
			paneSel      : '.panes>div',
			currentClass : 'current'
		},
		o       = $.extend(defaults, options),
		$root   = $(this),
		$tabs   = $root.find(o.tabSel),
		$panes  = $root.find(o.paneSel),
		current = 0;


		/**
		 * Inits the tabs - sets a click event handler to the tabs.
		 */
		function init() {
			showSelected(0);

			$root.on('click', o.tabSel, function(e) {
				e.preventDefault();

				var index = $tabs.index($(this));

				if(index !== current) {
					hideTab(current);
					showSelected(index);
				}

			});
		}

		/**
		 * Displays the selected tab.
		 * @param  {int} index the index of the selected tab
		 */
		function showSelected(index) {
			$panes.eq(index).fadeIn();
			$tabs.eq(index).addClass(o.currentClass);
			current = index;
		}

		/**
		 * Hides a tab when a new one has been selected.
		 * @param  {index} index the index of the tab to hide
		 */
		function hideTab(index) {
			$panes.eq(index).hide();
			$tabs.eq(index).removeClass(o.currentClass);
		}

		init();
	};
}(jQuery));



(function($) {
	"use strict";

	/**
	 * Pexeto Accordion Widget.
	 * Dependencies : jQuery
	 *
	 * @author Pexeto
 	 * http://pexetothemes.com
	 */
	$.fn.pexetoAccordion = function(options) {
		var defaults = {

			//selectors and classes
			tabSel       : '.accordion-title',
			paneSel      : '.pane',
			currentClass : 'current'
		},
		o       = $.extend(defaults, options),
		$root   = $(this),
		$tabs   = $root.find(o.tabSel),
		$panes  = $root.find(o.paneSel),
		allClosed = $root.hasClass('accordion-all-closed'),
		current = allClosed ? -1 : 0;


		/**
		 * Inits the main functionality - registers a click event handler
		 * to the accordion tabs.
		 */
		function init() {
			$root.data('acc_init', 'true');
			if(!allClosed){
				//display the first pane
				showSelected(0);
			}

			$root.on('click', o.tabSel, function(e) {
				e.preventDefault();

				var index = $tabs.index($(this));

				if(index !== current) {
					hideTab(current);
					showSelected(index);
				}else{
					hideTab(current);
					current = -1;
				}
			});
		}

		/**
		 * Displays the selected accordion tab.
		 * @param  {int} index the index of the selected tab
		 */
		function showSelected(index) {
			$panes.eq(index).stop().animate({
				height: 'show',
				opacity: 1
			});
			$tabs.eq(index).addClass(o.currentClass);
			current = index;
		}

		/**
		 * Hides a tab when a new one has been selected.
		 * @param  {index} index the index of the tab to hide
		 */
		function hideTab(index) {
			var def = new $.Deferred();
			$panes.eq(index).stop().animate({
				height: 'hide',
				opacity: 0
			}, function() {
				def.resolve();
			});
			$tabs.eq(index).removeClass(o.currentClass);
			return def.promise();
		}

		if(!$root.data('acc_init')){
			init();
		}
	};
}(jQuery));



/**
 * PEXETO contains the functionality for initializing all the main scripts in the
 * site and also some helper functions.
 *
 * @type {Object}
 * @author Pexeto
 */
var PEXETO = PEXETO || {};

(function($) {
	"use strict";
	
	$.extend(PEXETO, {
		ajaxurl       : '',
		lightboxStyle : 'light_rounded',
		masonryClass  : 'page-masonry'
	});

	/**
	 * Retrieves the current browser info.
	 * Code from jQuery Migrate: http://code.jquery.com/jquery-migrate-1.2.0.js
	 * @return an object containing the browser info, for example for IE version 7
	 * it would return:
	 * {msie:true, version:7}
	 */
	PEXETO.getBrowser = function(){
		var browser = {},
			ua,
			match,
			matched;

		if(PEXETO.browser){
			return PEXETO.browser;
		}

		ua = navigator.userAgent.toLowerCase();

		match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
			ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
			[];

		matched = {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};

		if ( matched.browser ) {
			browser[ matched.browser ] = true;
			browser.version = matched.version;
		}

		// Chrome is Webkit, but Webkit is also Safari.
		if ( browser.chrome ) {
			browser.webkit = true;
		} else if ( browser.webkit ) {
			browser.safari = true;
		}

		PEXETO.browser = browser;

		return browser;
	}


	PEXETO.supportsVideo = function() {
		if(typeof(PEXETO.videoSupport)=='undefined'){
			PEXETO.videoSupport = !!document.createElement('video').canPlayType;
		}
		return PEXETO.videoSupport;
	};

	PEXETO.$win = $(window);
	PEXETO.$body = $('body');


	/**
	 * Contains the init functionality.
	 * @type {Object}
	 */
	PEXETO.init = {

		/**
		 * Inits all the main functionality. Calls all the init functions.
		 */
		initSite: function() {

			var init = this,
				clickMsg = '';

			//initialize the lightbox
			init.lightbox(null, {});

			init.lightbox($("a[data-rel^='pglightbox']:not(#portfolio-slider a, #portfolio-gallery a)"), {});
			init.carouselLightbox();

			init.tabs();
			init.placeholder($('.placehoder'));
			init.loadableImg($('img.loadable, .blog-post-img img'));
			
			new PEXETO.menuNav($('#menu')).init();

			init.quickGallery();
			init.carousel();
			init.headerSearch();

			if(PEXETO.disableRightClick) {
				PEXETO.utils.disableRightClick();
			}

			PEXETO.utils.checkIfMobile();


			//wrap the sidebar categories widget number of posts text node within a span
			var catSpans = $('li.cat-item, .widget_archive li').contents().filter(function() {
				return this.nodeType == 3;
			});
			if(catSpans.length) {
				catSpans.wrap($('<span />', {
					'class': 'cat-number'
				}));
			}

			//init social sharing
			var $share = $('.social-share');
			$share.each(function() {
				PEXETO.init.share($(this));
			});

			init.parallax();

			init.bgCoverFallback();

			if(!PEXETO.utils.checkIfMobile()){
				init.setScrollToTop();
			}

			init.ieClass();

			if(PEXETO.stickyHeader){
				new PEXETO.utils.stickyHeader($('#header'), {}).init();
				init.ieIframeFix();
			}
                        
			init.resizeEvents();
			init.blogMobileFilter();

		},


		resizeEvents : function(){
			var resizeId,
				doOnResize = function(){
					PEXETO.$win.trigger('pexetoresize');
				};

			PEXETO.$win.on('resize', function(){
				clearTimeout(resizeId);
				resizeId = setTimeout(doOnResize, 500);
			});
		},

		/**
		 * Sets the search button functionality in the header. On click
		 * displays a search field.
		 */
		headerSearch : function(){
			var $searchBtn = $('.header-search-btn'),
				$searchWrapper,
				$searchInput,
				inAnimation = false,
				visible = false,
				visibleClass = 'search-visible';

			if($searchBtn.length){
				$searchWrapper = $('#header .search-wrapper:first');
				$searchInput = $searchWrapper.find('.search-input');

				$searchBtn.on('click', function(e){
					e.preventDefault();

					if(!inAnimation){
						inAnimation = true;

						if(visible){
							$searchBtn.removeClass(visibleClass);
							$searchWrapper.animate({width:'hide', opacity:0}, function(){
								$searchInput.blur();
								$searchBtn.blur();
								visible = false;
								inAnimation = false;
							});
						}else{
							$searchBtn.addClass(visibleClass);
							
							$searchWrapper.animate({width:'show', opacity:1}, function(){
								$searchInput.focus();
								visible = true;
								inAnimation = false;
							});
						}
					}
				});
			}
		},


		/**
		 * Fixes the IE ignoring of z-index on iframes.
		 */
		ieIframeFix : function(){
			if(PEXETO.getBrowser().msie){

				$('iframe').each(function() {
					var url = $(this).attr("src"),
						newUrl;

					if(url){
						newUrl = PEXETO.url.addUrlParameter(url, 'wmode=transparent');

						$(this).attr({
							"src" : newUrl,
							"wmode" : "Opaque"
						});
					}
					
				});
			}
		},

		/**
		 * Adds an ie10 class to Internet Explorer
		 * @return {[type]} [description]
		 */
		ieClass : function(){
			var browser = PEXETO.getBrowser(),
				version = 0;
			if(browser.msie){
				version = parseInt(browser.version,10);
				$('body').addClass('ie ie'+version);
			}
		},

		/**
		 * Inits the scroll to top button functionality.
		 */
		setScrollToTop : function(){
			var $scrollBtn = $('.scroll-to-top'),
				btnDisplayed = false;

			if($scrollBtn.length){
				/**
				 * Shows or hides the scroll to top button depending on the current
				 * document scroll position.
				 */
				var setButtonVisibility = function(){
					var scrollPos = $(document).scrollTop(),
				   		winHeight = $(window).height();

				   		if(!btnDisplayed && scrollPos > winHeight){
				   			//display scroll button
				   			$scrollBtn.pexetoTransit({opacity:1, marginBottom:0});
				   			btnDisplayed = true;
				   		}else if(btnDisplayed && scrollPos < winHeight){
				   			$scrollBtn.pexetoTransit({opacity:0, marginBottom:-30});
				   			btnDisplayed = false;
				   		}
				};
				$('body').on('mousewheel', setButtonVisibility);
				setButtonVisibility();

				$scrollBtn.on('click', function(){
					$.scrollTo($('#main-container'), {
						duration: 1000,
						easing: 'easeOutSine',
						offset: {
							top: 0
						},
						onAfter:function(){
							setButtonVisibility();
							$(window).trigger('pexetoscroll');
						}
					});
				});
			}
			
		},

		/**
		 * Inits the fallback functionality for the CSS background-size:cover
		 */
		bgCoverFallback : function(){
			if(PEXETO.getBrowser().msie && PEXETO.getBrowser().version<=8){
				$('.full-bg-image').each(function(){
					new PEXETO.utils.bgCoverFallback($(this)).init();
				});
			}
		},

		/**
		 * Inits the parallax animation effect for some elements,
		 */
		parallax : function(){
			//init the full-width backfround image parallax
			if(!PEXETO.utils.checkIfMobile()){
				$('.parallax-scroll .full-bg-image').each(function(){
					new PEXETO.parallax(
						$(this),
						'background',
						{}
						).init();
				});
			}

			//init the services boxes list parallax
			$('.services-default.pexeto-parallax,.services-icon.pexeto-parallax,.services-boxed-photo.pexeto-parallax').each(function(){
				new PEXETO.parallax(
					$(this),
					'list',
					{
						children:$(this).find('.services-box'),
						initProp : {opacity:0, top:50, position:'relative'},
						endProp: {opacity:1, top:0}
					}
					).init();
			});

			$('.services-thumbnail.pexeto-parallax').each(function(){
				new PEXETO.parallax(
					$(this),
					'list',
					{
						children:$(this).find('.services-box'),
						animation: 'scale'
					}
					).init();
			});


			var $parallaxHeader = $('.parallax-header');

			if($parallaxHeader.length){
				//init the parallax header effects
				
				$parallaxHeader.find('.page-title-wrapper').each(function(){
				new PEXETO.parallax(
					$(this),
					'hideOpacity',
					{
						disableMobile : true,
						$parent:$(this).parent('.header-wrapper')
					}
					).init();
				});

				$parallaxHeader.find('.page-title .content-boxed').each(function(){
					new PEXETO.parallax(
						$(this),
						'stickToViewport',
						{
							disableMobile : true,
							$parent:$(this).parents('.header-wrapper:first')
						}
						).init();
				});
			}

			

		},

		/**
		 * Inits the PrettyPhoto plugin lightbox.
		 * @param  {object} $el element or set of elements to which the lightbox will be loaded
		 */
		lightbox: function($el, add_options) {
			$el = $el || $("a[data-rel^='lightbox'],a[data-rel^='lightbox[group]']");
			var defaults = {
				animation_speed: 'normal',
				theme: PEXETO.lightboxStyle,
				overlay_gallery: false,
				slideshow: false,
				social_tools: '',
				hook:'data-rel'
			},
				options = $.extend(defaults, PEXETO.lightboxOptions);

			if(!$.isEmptyObject(add_options)){
				options = $.extend(options, add_options);
			}

			$el.prettyPhoto(options);
		},

		carouselLightbox : function(){
			$("a[data-rel^=\'pclightbox\']").on("click", function(e){
				e.preventDefault();
				var images = $(this).data("images");
				var captions = $(this).data("captions");
				$.prettyPhoto.open(images, [], captions);
			});
		},

		/**
		 * Inits a placeholder functionality for browsers that don't support placeholder.
		 * @param  {object} $el element or set of elements to which this functionality
		 * will be initialized.
		 */
		placeholder: function($el) {
			if(!$.support.placeholder) {
				$el.each(function() {
					$(this).attr('value', $(this).attr('placeholder'));
				}).on('focusin', function() {
					$(this).attr('value', $(this).attr('placeholder'));
				}).on('focusout', function() {
					$(this).attr('value', '');
				});
			}
		},

		/**
		 * Inits the tabs and accordion functionality.
		 */
		tabs: function() {
			//set the tabs functionality
			$('.tabs-container').each(function(){
				$(this).pexetoTabs();
			});

			//set the accordion functionality
			$('.accordion-container').each(function(){
				$(this).pexetoAccordion();
			});
		},

		/**
		 * Inits the portfolio items carousel
		 */
		carousel: function() {
			var isSinglePortfolio = $('body').hasClass('single-portfolio');

			if(!isSinglePortfolio
				|| (isSinglePortfolio && !$('#portfolio-slider').length)) {
				$('.portfolio-carousel').each(function() {
					$(this).pexetoCarousel();
				});
			}

		},

		/**
		 * Makes an image get displayed once it is loaded in a fade in animation.
		 * @param  {object} $el jQuery object or list of objects that contains 
		 * the loadable images. Each image must have a div parent from the 
		 * "img-loading" class which has a min-width and min-height set to it.
		 */
		loadableImg: function($el) {
			if($el.length) {
				$el.each(function() {
					$(this).pexetoOnImgLoaded({
						callback: function() {
							$(this).animate({
								opacity: 1
							}).parents('div.img-loading:first').css({
								minWidth: 0,
								minHeight: 0
							});
						}
					});
				});
			}
		},

		/**
		 * Inits the quick gallery functionality. Loads the masonry script if
		 * the masonry option has been enabled.
		 */
		quickGallery: function() {
			$('.quick-gallery').each(function() {
				var $gallery = $(this),
					masonry = $gallery.hasClass(PEXETO.masonryClass);

				if(!$gallery.hasClass('qg-full')){
					new PEXETO.utils.resizableImageGallery('.qg-img', 
						{
							masonry:masonry,
							parent:$gallery
						}).init();
				}
				
			});
		},

		/**
		 * Loads the Nivo slider.
		 * @param  {object} $el     jQuery element which will contain the 
		 * slider images
		 * @param  {object} options object literal containing the slider 
		 * initialization options
		 */
		nivoSlider: function($el, options) {
			var $caption;

			// loads the Nivo slider	
			var loadSlider = function() {
					$el.nivoSlider({
						effect: 'fade',
						animSpeed: options.speed,
						pauseTime: options.interval,
						startSlide: 0,
						// Set starting Slide (0 index)
						directionNav: options.arrows,
						// Next & Prev
						directionNavHide: false,
						// Only show on hover
						controlNav: options.buttons,
						// 1,2,3...
						controlNavThumbs: false,
						// Use thumbnails for
						// Control
						// Nav
						controlNavThumbsFromRel: false,
						// Use image rel for
						// thumbs
						keyboardNav: true,
						// Use left & right arrows
						pauseOnHover: options.pauseOnHover,
						// Stop animation while hovering
						manualAdvance: !options.autoplay,
						// Force manual transitions
						captionOpacity: 0.8,
						// Universal caption opacity
						beforeChange: function() {
							$caption.stop().css({opacity:0, bottom:-30});
						},
						afterChange: function() {
							$caption.animate({opacity:1, bottom:0});
						},
						slideshowEnd: function() {} // Triggers after all slides have been shown
					}).css({
						minHeight: 0
					});

					$caption=$el.find('.nivo-caption');

					// remove numbers from navigation
					$('.nivo-controlNav a').html('');
					$('.nivo-directionNav a').html('');
				};

			if(!PEXETO.getBrowser().msie) {
				//load the slider once the images get loaded
				$el.find('img').pexetoOnImgLoaded({
					callback: loadSlider
				});
			} else {
				loadSlider();
			}
		},

		/**
		 * Inits the sharing functionality. Uses the Sharrre script for the 
		 * sharing functionality.
		 * @param  {object} $wrapper a jQuery object wrapper that wraps the
		 * sharing buttons
		 */
		share: function($wrapper) {

			if(!$wrapper.length) {
				return;
			}

			$wrapper.find('.share-item').each(function() {
				var $el = $(this),
					type = $el.data('type'),
					title = $el.data('title'),
					url = $el.data('url'),
					args = {
						url: url,
						title: title,
						share: {},
						template: '<div></div>',
						enableHover: false,
						enableTracking: false,
						urlCurl: '',
						buttons: {},
						click: function(api, options) {
							api.simulateClick();
							api.openPopup(type);
						}
					};

				args.share[type] = true;

				if(type === 'googlePlus') {
					//set the language attribute for Google+
					args.buttons.googlePlus = {
						lang: $el.data('lang')
					};
				} else if(type === 'pinterest') {
					//set an image URL and a description to share on Pinterest
					args.buttons.pinterest = {
						media: $el.data('media'),
						description: title
					};

				}

				$el.sharrre(args);
			});
		},

		blogMasonry : function(cols){
			var spacing = 30,
				$parent = $('.'+PEXETO.masonryClass),
				setColumnWidth = function(){
					var curCols = cols,
						containerWidth = $parent.width();

					if(containerWidth <= 600){
						curCols = 1;
					}else if(containerWidth>600 && containerWidth<=800){
						curCols = 2;
					}

					var width = Math.floor((containerWidth - (curCols-1)*spacing) / curCols) -1;

					$parent.find('.post').width(width);

					return width;
				};

			setColumnWidth();

			$parent.masonry({
				itemSelector:'.post',
				gutter: spacing,
				transitionDuration : 0
			});

			$parent.find('img').each(function() {
				$(this).imagesLoaded(function() {
					$parent.masonry('layout');
				});
			});

			$(window).on('resize', function(){
				setColumnWidth();
				$parent.masonry('layout');
			});

		},
		
		blogMobileFilter : function(){
			var $filterBtn = $('.blog-filter-nav-btn:first');
			if($filterBtn.length){
				var $parent = $filterBtn.parent();
				//this is a blog page with a category filter
				$filterBtn.on('click', function(){
					$parent.toggleClass('blog-filter-visible');
				})
			}
		}

	};



	/***************************************************************************
	 * DROP-DOWN MENU
	 **************************************************************************/

	/**
	 * Main navigation functionality. Includes the following functionality:
	 * - drop-down on hover for submenus
	 * - keeps the drop-down always visible in the window area
	 * - responsive navigation
	 * - toggle drop-down menu on click on smaller screens
	 * @param  {object} $el     The menu element - jQuery object
	 * @param  {object} options An object literal containing all the options
	 */
	PEXETO.menuNav = function($el, options){
		this.$menu = $el;
		var defaults = {
			mobMenuClass      : 'mob-nav-menu',
			mobPrecedingElSel : '.section-header',
			mobBtnSel         : '.mobile-nav',
			mobArrowClass     : 'mob-nav-arrow',
			mobSubOpenedClass : 'mob-sub-opened',
			megaMenuClass     : 'mega-menu-item',
			megaMenuMaxWidth  : 1000,
			megaMenuColumnWidth : 232
		};
		this.o = $.extend(defaults, options);
	};

	var mn = PEXETO.menuNav.prototype;

	/**
	 * Inits the navigation functionality.
	 */
	mn.init = function(){
		var self = this,
			browser = PEXETO.getBrowser();

		self.$win = $(window);
		self.$body = $('body');
		self.$mainUl = self.$menu.find('ul:first');
		self.isIE9 = browser.msie && parseInt(browser.version, 10)==9;

		if(self.$menu.is(':visible')){
			//init the main navigation functionality
			self.initMain();
		}else{
			$(window).on('resize.pexetodropdown', function(){
				if(self.$menu.is(':visible')){
					self.initMain();
					$(window).off('.pexetodropdown');
				}
			});
		}

		//init the mobile navigation functionality
		self.initMobileMenu();
	};

	/**
	 * Inits the main navigation functionality with the drop-down menus on 
	 * hover.
	 */
	mn.initMain = function(){
		var self = this,
			menuPosition = 'right';

		if(this.$body.hasClass('header-layout-center')){
			menuPosition = 'center';
		}else if(this.$body.hasClass('header-layout-right')){
			menuPosition = 'left';
		}
		this.menuPosition = menuPosition;


		//bind the mouseover events
		self.$menu.find('ul li').has('ul').not('ul li.mega-menu-item li').each(function() {

			$(this).on('mouseenter', function(){
				self.doOnMenuMouseover($(this));
			}).on('mouseleave', function(){
				self.doOnMenuMouseout($(this));
			}).find('a:first').append('<span class="drop-arrow"></span>');
		});

		self.$menu.find('a[href="#"]').on('click', function(e){
			e.preventDefault();
		});

		this.initMegaMenu();
	};

	mn.initMegaMenu = function(){
		this.$megaUls = this.$menu.find('ul li.'+this.o.megaMenuClass).has('ul').children('ul');

		if(this.$megaUls.length){
			this.$parentWrapper = this.$menu.parents('.section-boxed:first');
			
			this.$win.on('pexetoresize', $.proxy(this.setMegaMenuWidth, this));
			
			this.setMegaMenuWidth();
		}
		
	};

	mn.setMegaMenuMaxWidth = function(){
		var maxWidth = 0;

		switch(this.menuPosition){
			case 'right' :
				if(!this.lastMenuLi){
					this.lastMenuLi = this.$menu.find('ul:first>li:last');
				}
				if(this.isIE9){
					this.lastMenuLi.offset();
				}
				maxWidth = this.lastMenuLi.offset().left + this.lastMenuLi.width() - this.$parentWrapper.offset().left;
			break;
			case 'left' :
				maxWidth = this.$parentWrapper.width();
			break;
			case 'center' :
				maxWidth = this.$parentWrapper.width();
			break;
		}

		this.megaMenuMaxWidth = Math.min(this.o.megaMenuMaxWidth, maxWidth);
	};

	mn.setMegaMenuWidth = function(){
		var self = this;

		this.setMegaMenuMaxWidth();
		this.mainUlWidth =  this.$mainUl.width();

		this.$megaUls.each(function(){
			var $ul = $(this),
				liNum =$ul.children('li').length,
				width,
				colsToFit;

			if(liNum>0){
				if(self.megaMenuMaxWidth<liNum*self.o.megaMenuColumnWidth){
					colsToFit = Math.floor(self.megaMenuMaxWidth/self.o.megaMenuColumnWidth) || 1;
					width = colsToFit*self.o.megaMenuColumnWidth;
				}else{
					width = liNum*self.o.megaMenuColumnWidth;
					colsToFit = liNum;
				}

				if(this.lastMegaClass){
					$ul.removeClass(this.lastMegaClass);
				}
				this.lastMegaClass = 'mega-columns-'+colsToFit;

				$ul.width(width)
					.addClass(this.lastMegaClass);

				self.setMegaMenuPosition($ul, width);

			}
		});
	}

	mn.setMegaMenuPosition = function($ul, ulWidth){
		var left,
			$li,
			centerPosition,
			shortestEndDistance;

		if(ulWidth >= this.mainUlWidth){
			//the mega drop-down is bigger than the parent menu
			switch(this.menuPosition){
				case 'right' :
					//align right
					$ul.css({left:'auto', right:0});
				break;
				case 'left' :
					//align left
					$ul.css({left:0});
				break;
				case 'center' :
					//center
					if(typeof this.iconsWidth === 'undefined'){
						var $icons = this.$parentWrapper.find('.header-buttons');
						this.iconsWidth = $icons.length ? $icons.width() : 0;
					}
					left = -(ulWidth - (this.mainUlWidth + this.iconsWidth) )/2;
					$ul.css({left:left});
				break;
			}
		}else{
			$li = $ul.parents('li:first');
			centerPosition = $li.position().left + $li.width()/2,
			shortestEndDistance = Math.min(centerPosition, this.mainUlWidth-centerPosition);

			if(ulWidth/2<=shortestEndDistance){
				//center
				left = centerPosition - ulWidth/2;
				$ul.css({left:left});
			}else{
				if(centerPosition<=this.mainUlWidth-centerPosition){
					//align left
					$ul.css({left:0});
				}else{
					//align right
					$ul.css({left:'auto', right:0});
				}
			}

		}

	};


	/**
	 * Displays the drop-down menu on mouse over.
	 * @param  {object} $li the hovered element - jQuery object
	 */
	mn.doOnMenuMouseover = function($li) {
		var self = this,
			$ul = $li.find('ul:first'),
			parentUlNum = $ul.parents('ul').length,
			elWidth = $li.width(),
			ulWidth = $ul.width(),
			winWidth = self.$win.width(),
			elOffset = $li.offset().left;


		$li.addClass('hovered');

		if(self.menuPosition=='right' && !$li.hasClass(self.o.megaMenuClass)){
			if(parentUlNum > 1 && (elWidth + ulWidth + elOffset > winWidth)) {
				//if the drop down ul goes beyound the screen, move it on the left side
				$ul.css({
					left: -elWidth
				});
			} else if(parentUlNum === 1) {
				if(ulWidth + elOffset > winWidth) {
					$ul.css({
						left: (winWidth - 3 - (ulWidth + elOffset))
					});
				} else {
					$ul.css({
						left: 0
					});
				}
			}
		}

		// display the drop-down
		$ul.stop().fadeIn(300);
	};

	/**
	 * Hides the drop-down on mouse out.
	 * @param  {object} $li the hovered li element - jQuery object
	 */
	mn.doOnMenuMouseout = function($li) {
		var $ul = $li.find('ul:first');
		$li.removeClass('hovered');

		$ul.stop().fadeOut( 300);
	};

	/**
	 * Inits the mobile navigation menu.
	 */
	mn.initMobileMenu = function(){
		var self = this,
			$menu = $('<div />', {
				'class': self.o.mobMenuClass,
				html: self.$menu.html()
			}).insertAfter($(self.o.mobPrecedingElSel));

		self.mobile = {
			opened : false,
			inAnimation : false,
			$menuBtn : $(self.o.mobBtnSel),
			$menu : $menu
		};

		//remove the already added element styles
		$menu.find('ul').css('width', '').css('left', '').css('right','');

		//append a toggle arrow to the elements that contain submenus
		$menu.find('ul li').has('ul').each(function(){
			$(this).append('<div class="'+self.o.mobArrowClass+'"><span></span></div>');
		});

		self.bindMobileEventHandlers();
	};


	/**
	 * Binds the event handlers to the menu navigation.
	 */
	mn.bindMobileEventHandlers = function(){
		var self = this,
			m = self.mobile;

		//menu button click handler
		m.$menuBtn.on('click', function(){
			self.toggleMobileMenu();
		});

		//hide the mobile menu 
		self.$win.on('resize', function(){
			if(!m.$menuBtn.is(':visible') && (m.$menu && m.opened)){
				m.$menu.hide();
				m.opened = false;
			}
		});

		m.$menu.find('li:has(ul) a[href="#"],'+'.'+self.o.mobArrowClass).on('click', function(e){
			var $submenu = $(this).siblings('ul:first'),
				$arrow = e.target.nodeName.toLowerCase()=='span' ?
					$(this) : $(this).siblings('.'+self.o.mobArrowClass);
			self.toggleMobileSubMenu($submenu, $(this));	
		});
	};

	/**
	 * Toggles the mobile menu.
	 */
	mn.toggleMobileMenu = function(){
		var self = this,
			m = self.mobile;

		if(!m.inAnimation) {
			if(!m.opened) {
				//show the menu
				m.inAnimation = true;
				m.$menu.animate({
					height: 'show'
				}, function() {
					m.opened = true;
					m.inAnimation = false;
				});
			} else {
				//hide the menu
				m.inAnimation = true;
				m.$menu.animate({
					height: 'hide'
				}, function() {
					m.opened = false;
					m.inAnimation = false;
				});
			}
		}
	};

	/**
	 * Toggles a mobile submenu.
	 * @param  {object} $ul    the ul element to display - a jQuery object
	 * @param  {object} $arrow the arrow object that has been clicked - a jQuery
	 * object
	 */
	mn.toggleMobileSubMenu = function($ul, $arrow){
		var self = this,
			m = self.mobile;

		if(!$ul.length || m.inAnimation){
			return;
		}

		m.inAnimation = true;
		$arrow.toggleClass(self.o.mobSubOpenedClass);
		if($ul.is(':visible')){
			//hide the menu
			$ul.animate({height:'hide'}, function(){
				m.inAnimation = false;
			});
		}else{
			//show the menu
			$ul.animate({height:'show'}, function(){
				m.inAnimation = false;
			});
		}
		
	};



	PEXETO.woocommerce = {

		init : function(enableLightbox){
			if(enableLightbox){
				PEXETO.init.lightbox($('a[data-rel^="prettyPhoto"],a.zoom,a[data-rel^="prettyPhoto[product-gallery]"]'), {hook: 'data-rel'});
			}
		}

	};




	/***************************************************************************
	 * PARALLAX EFFECTS
	 **************************************************************************/

	/**
	 * Parallax class - contains methods to apply various parallax animations
	 * to an element or set of elemements.
	 * @param  {object} $el     the element to apply the animation to
	 * @param  {string} type    the type of animation - available options:
	 * - background : animates a background image, changes its position on scroll
	 * - list : animates a list of items, one after another
	 * - single : animates a single item
	 * @param  {object} options options for the animation. Properties:
	 * - children : a list of elements to animate when the type of animation
	 * is set to list
	 * - initProp : object containing a set of CSS properties that are applied
	 * to each element before the animation starts
	 * - endProp : object containing a set of CSS properties that are applied
	 * to each element to be animated
	 */
	PEXETO.parallax = function($el, type, options){
		this.$el = $el;
		this.type = type;
		this.options = options;
	};

	/**
	 * Inits the parallax functionality. Calls the corresponding animation
	 * method depending on the type of effect selected.
	 */
	PEXETO.parallax.prototype.init = function(){
		var self = this,
			funcToExec = {
			'background' : 'setBackground',
			'list' : 'setList',
			'single' : 'setSingleElement',
			'hideOpacity' : 'setHideOpacity',
			'stickToViewport' : 'setStickOnViewportUntilParentBottom'
		};

		if(self.options.disableMobile && PEXETO.utils.checkIfMobile()){
			return;
		}

		if(funcToExec.hasOwnProperty(self.type)){
			PEXETO.parallax.prototype[funcToExec[self.type]].call(this);
		}
	};

	/**
	 * Sets a parallax background image functionality. Moves the image position
	 * on mouse scroll.
	 */
	PEXETO.parallax.prototype.setBackground = function(){
		var self = this,
			i,
			$el = self.$el,
			$parent = $el.parent(),
			waypoints = {},
			maxTop = 60,
			numSteps = 100,
			topStep = maxTop/numSteps,
			initWaypoint = 90,
			endWaypoint = 120,
			waypointStep = Math.floor((initWaypoint+endWaypoint) / numSteps);

			//generate an array containing waypoints and the corresponding data
			for(i=0; i<numSteps; i++){
				waypoints[initWaypoint-i*waypointStep] = '-'+((i+1)*topStep)+'%';
			}

			_.each(waypoints, function(top, waypoint){

				$parent.waypoint(function(direction){
					$el.stop().pexetoTransit({top:top});
				}, {offset:waypoint+'%'});
			});

	};

	/**
	 * Registers a single element parallax animation. The "initProp" and
	 * "endProp" properties should be set to the constructor's options object.
	 */
	PEXETO.parallax.prototype.setSingleElement = function(){
		var self = this,
			$el = self.$el;

		$el.css(self.options.initProp)
			.waypoint(function(){
				$el.addClass('animated-element')
					.pexetoTransit(self.options.endProp)
					.waypoint('destroy');
			}, {'offset':'90%'});
	};

	PEXETO.parallax.prototype.setHideOpacity = function(){
		var self = this,
			$el = self.$el,
			$parent = self.options.$parent,
			parentHeight = $parent.height(),
			$win = $(window),
			setOpacity = function(){
				var opacity = 1,
					scrollTop = $win.scrollTop();

				if(parentHeight<=0){
					return;
				}

				if (scrollTop < parentHeight && scrollTop >= 0) {
					opacity = 1 - scrollTop/parentHeight;
					$el.css({opacity:opacity});
				}
			};

			$win.scroll(setOpacity);
			setOpacity();
	};

	PEXETO.parallax.prototype.setStickOnViewportUntilParentBottom = function(){
		var self = this,
			$el = self.$el,
			$parent = self.options.$parent,
			parentHeight = $parent.height(),
			$win = $(window),
			gapDistance = 100,
			setPosition = function(){
				var scrollTop = $win.scrollTop();

				if(parentHeight<=0){
					return;
				}

				if (scrollTop < parentHeight && scrollTop >= 0) {
					$el.css({top:scrollTop*gapDistance/parentHeight});
				}
				
			},
			calculateGap = function(){
				gapDistance =  ($parent.offset().top + $parent.outerHeight() ) - 
					($el.offset().top + $el.height())
			};

			calculateGap();
			setPosition();

			$win.scroll(setPosition);
			
	};

	/**
	 * Registers a list of elements parallax animation. The "initProp" and
	 * "endProp" properties should be set to the constructor's options object
	 * to set the animation properties. Also a "children" property should be
	 * added to the options object containing the children elements to be loaded.
	 */
	PEXETO.parallax.prototype.setList = function(){
		var self = this,
			$el = self.$el,
			animation = self.options.animation && self.options.animation=='scale' ? 'scale' : 'custom',
			$children = self.options.children.addClass('parallax-element');
				
			if(animation==='custom'){
				$children.css(self.options.initProp);
			}

			$el.waypoint(function(direction){

				$children.each(function(i){
					var $element = $(this);
					setTimeout(function(){
						if(animation==='custom'){
							$element.pexetoTransit(self.options.endProp);
						}else{
							$element.addClass('parallax-scaled-original');
						}
					}, i * 400);
				});

				$el.waypoint('destroy');

			}, {'offset':'90%'});
	};




	/**
	 * Contains some general helper functions.
	 * @type {Object}
	 */
	PEXETO.utils = {

		/**
		 * Disables right click which opens the context menu.
		 * @param  {string} message a message that will be displayed on right click. Use empty
		 * string if you don't need to display a message
		 */
		disableRightClick: function() {
			$(document).bind('contextmenu', function(e) {
				return false;
			});
		},

		/**
		 * JavaScript templating function :
		 * http://mir.aculo.us/2011/03/09/little-helpers-a-tweet-sized-javascript-templating-engine/
		 * @param  {string} s the string template
		 * @param  {object} d object literal containing the values that will be replaced in the string
		 * @return {string}   the replaced string with the data set
		 */
		template: function(s, d) {
			var p;
			for(p in d)
			s = s.replace(new RegExp('{' + p + '}', 'g'), d[p]);
			return s;
		},

		/**
		 * Checks if the current device is a mobile device. If it is a mobile device, and it is within
		 * the recognized devices, adds its specific class to the body.
		 * @return {boolean} setting if the device is a mobile device or not
		 */
		checkIfMobile: function() {
			if(PEXETO.isMobile !== undefined) {
				return PEXETO.isMobile;
			}
			var userAgent = navigator.userAgent.toLowerCase(),
				devices = [{
					'class': 'iphone',
					regex: /iphone/
				}, {
					'class': 'ipad',
					regex: /ipad/
				}, {
					'class': 'ipod',
					regex: /ipod/
				}, {
					'class': 'android',
					regex: /android/
				}, {
					'class': 'bb',
					regex: /blackberry|bb10/
				}, {
					'class': 'iemobile|nokia',
					regex: /iemobile/
				}],
				i, len;
				
			PEXETO.isMobile = false;
			for(i = 0, len = devices.length; i < len; i += 1) {
				if(devices[i].regex.test(userAgent)) {
					$('body').addClass(devices[i]['class'] + ' mobile');
					PEXETO.isMobile = true;
					PEXETO.mobileType = devices[i]['class'];
					return true;
				}
			}

			return false;
		},

		/**
		 * Fades an element in.
		 * @param {object} $elem the element to be faded
		 */
		elemFadeIn: function($elem) {
			$elem.stop().animate({
				opacity: 1
			}, function() {
				$elem.animate({
					opacity: 1
				}, 0);
			});
		},

		/**
		 * Fades an elemen out to a selected opacity.
		 * @param {object} $elem the element to be faded
		 * @param {number} opacity the opacity to be faded to (number between 0 and 1)
		 */
		elemFadeOut: function($elem, opacity) {
			$elem.stop().animate({
				opacity: opacity
			}, function() {
				$elem.animate({
					opacity: opacity
				}, 0);
			});
		},

		getNaturalImgSize: function($img){
			var img = $img.get(0);
			if(img.naturalWidth && img.naturalHeight){
				return {width:img.naturalWidth, height:img.naturalHeight};
			}
			return {width:$img.width(), height:$img.height()};
		}
	};


	/**
	 * Contains some URL helper functions.
	 * @type {Object}
	 */
	PEXETO.url = {

		/**
		 * Retrieves the URL parameters/
		 * @return {object} containing the parameters and values in key-value pairs
		 */
		getUrlParameters: function() {
			var vars = {};
			var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
				vars[key] = value;
			});
			return vars;
		},

		getCustomUrlParameters : function(url){
			var vars = {};
			var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
				vars[key] = value;
			});
			return vars;
		},

		/**
		 * Adds a parameter to the URL.
		 * @param {string} url   the URL to which to add the parameters to
		 * @param {string} param the parameter in a string format including its value, example:
		 * "param=value"
		 * @return {string} the URL with the added parameter
		 */
		addUrlParameter: function(url, param) {
			url += (url.split('?')[1] ? '&' : '?') + param;
			return url;
		}

		

	};


	/***************************************************************************
	 * STICKY HEADER
	 **************************************************************************/


	/**
	 * Sticky header functionality - displays the hader always on the top of the
	 * screen.
	 * @param  {object} $element jQuery element - the header element
	 * @param  {object} options  the options settings
	 */
	PEXETO.utils.stickyHeader = function($element, options){
		this.$el = $element;
		this.$body = $('body');
		this.$win = $(window);

		var defaults = {
			scrollHeight : 64,
			scrollClass : 'fixed-header-scroll'
		};

		this.o = $.extend(defaults, options);
	};


	/**
	 * Inits the sticky header functionality.
	 */
	PEXETO.utils.stickyHeader.prototype.init = function(){
		var self = this,
			setDefaultHeight = function(){
				if(!self.scrolled){
					self.defaultHeight = self.$el.outerHeight();
					self.setPositions();
				}
			};

		self.setPadding = $('body').hasClass('slider-active') ? false : true;

		self.$parent = this.$el.parent();
		self.isMobile = PEXETO.utils.checkIfMobile();
		self.setPositions();

		$('#logo-container img').pexetoOnImgLoaded({callback:setDefaultHeight});

		$(window).on('mousewheel pexetoscroll scroll', function(){
			if(!self.isMobile){
				self.setPositions();
			}
		}).on('resize', function(){
			if(!self.scrolled){
				self.defaultHeight = self.$el.outerHeight();
				if(self.setPadding){
					self.$parent.css({paddingTop:self.defaultHeight}); 
				}
			}
		});
	};

	/**
	 * Checks whether the current window is scrolled.
	 * @return {boolean} true if it is scrolled and false if it is not scrolled
	 */
	PEXETO.utils.stickyHeader.prototype.isScrolled = function(){
		return $(document).scrollTop() > 5 ? true : false;
	};


	/**
	 * Positions the depending elements of the sticky header depending
	 * on the current header position.
	 */
	PEXETO.utils.stickyHeader.prototype.setPositions = function(){
		var self = this,
			currentScrolled = self.isScrolled();
			
		if(!self.defaultHeight){
			self.defaultHeight = self.$el.outerHeight();
		}

		if(currentScrolled && !self.scrolled){
			self.scrolled = true;
			self.$body.addClass(self.o.scrollClass);
			if(self.setPadding){
				self.$parent.css({paddingTop:self.o.scrollHeight}); 
			}
			self.$win.trigger('pexetostickychange');
		}else if(!currentScrolled && (self.scrolled || self.scrolled===undefined)){
			self.scrolled = false;
			self.$body.removeClass(self.o.scrollClass);
			if(self.setPadding){
				self.$parent.css({paddingTop:self.defaultHeight}); 
			}
			self.$win.trigger('pexetostickychange');
		}
	};


	/***************************************************************************
	 * RESIZABLE IMAGE GALLERY
	 **************************************************************************/

	/**
	 * Resizable gallery functionality. Resizes the images in a gallery so they
	 * so that they can always fill the full parent container without any gaps.
	 * Also provides a masonry functionality that uses the jQuery Masonry script.
	 * @param  {string} selector the items selector
	 * @param  {object} options  an options object, supported parameters:
	 * - parent : jQuery object, the parent container of the items
	 * - masonry : boolean setting whether to enable masonry or not
	 */
	PEXETO.utils.resizableImageGallery = function(selector, options){
		this.selector = selector;
		this.options = options;
		this.$parent = options.parent || $('.'+PEXETO.masonryClass);
		this.$items = this.$parent.find(selector);
		this.masonry = options.masonry;
	};

	/**
	 * Inits the resizable functionality.
	 * @return {object} the resizableImageGallery object
	 */
	PEXETO.utils.resizableImageGallery.prototype.init = function(){
		var self = this;

		self.setImageSize();

		if(self.masonry){
			self.initMasonry();
		}
		
		self.loadImages();

		$(window).on('resize', $.proxy(self.refresh, self));

		return self;
	};

	/**
	 * Inits the Masonry script.
	 */
	PEXETO.utils.resizableImageGallery.prototype.initMasonry = function(){
		var self = this;
		self.$parent.masonry({
			itemSelector : self.selector,
			transitionDuration: 0
		});
	};


	/**
	 * Adds an onload event handler to each of the images.
	 */
	PEXETO.utils.resizableImageGallery.prototype.loadImages = function(){
		var self = this;

		self.$parent.find('img').each(function() {
			$(this).pexetoOnImgLoaded({callback:function() {
				if(self.masonry){
					//refresh masonry
					self.$parent.masonry('layout');
				}
				$(this).css({
					 opacity: 1
				})
				.trigger('imgmasonryloaded');
			}});
		});
	};

	/**
	 * Calculates the image width depending on the default image width and
	 * the width of the parent container div.
	 * @return {int} the width of the image including the margins of the image
	 */
	PEXETO.utils.resizableImageGallery.prototype.setImageSize = function(){
		var self = this,
			$firstItem = self.$items.eq(0),
			space = parseInt($firstItem.css('marginRight'), 10) + parseInt($firstItem.css('marginLeft'), 10),
			defaultWidth = $firstItem.data('defwidth') || $firstItem.width(),
			numColumns = 0,
			spaceLeft = 0,
			containerWidth = self.$parent.width(),
			newImgW;

			containerWidth = Math.floor(containerWidth-1);

			numColumns = Math.floor(containerWidth / (defaultWidth + space));
			if(numColumns<=0){
				numColumns = 1;
			}

			spaceLeft = containerWidth - numColumns * (defaultWidth + space);

			if(spaceLeft > defaultWidth / 2) {
				numColumns += 1;
			}

			newImgW = numColumns === 1 ? containerWidth - space 
				: Math.floor(containerWidth / numColumns) - space;

			self.$items.css({
				width: newImgW,
				height: 'auto'
			});

			return newImgW + space;
	};

	/**
	 * Refreshes the gallery - recalculates the image dimensions and refreshes
	 * the masonry script if masonry is enabled.
	 */
	PEXETO.utils.resizableImageGallery.prototype.refresh = function(){
		var self = this;

		if(!self.paused){
			self.setImageSize();

			if(self.masonry){
				self.$parent.masonry('layout');
			}
		}
		
	};

	/**
	 * Destroys the masonry script if it is enabled.
	 */
	PEXETO.utils.resizableImageGallery.prototype.destroy = function(){
		var self = this;

		if(self.masonry){
			self.$parent.masonry('destroy');
		}
	};

	PEXETO.utils.resizableImageGallery.prototype.pause = function(){
		this.paused = true;
	};

	PEXETO.utils.resizableImageGallery.prototype.resume = function(){
		this.paused = false;
	};




	/***************************************************************************
	 * BACKGROUND IMAGE COVER FALLBACK
	 **************************************************************************/

	/**
	 * CSS background-size:cover fallback. Main constructior.
	 */
	PEXETO.utils.bgCoverFallback = function($el){
		this.$el = $el;
	};


	/**
	 * Inits the fallback functionality - sets the background image as an image
	 * element that is positioned main div element.
	 */
	PEXETO.utils.bgCoverFallback.prototype.init = function(){
		var self = this,
			src='',
			img,
			$img;

		src = self.$el.css('backgroundImage');
		self.$el.css({'backgroundImage':''});
		src = src.replace('url("','').replace('")','');

		img = new Image();
		img.src = src;

		$img = $(img).appendTo(self.$el);
		self.$img = $img;

		new PEXETO.utils.fullBgImage($img).init();
	};


	PEXETO.utils.fullBgImage = function($img){
		this.$img = $img;
		this.$parent = $img.parent();
		var naturalSize = PEXETO.utils.getNaturalImgSize($img);
		this.imgWidth = naturalSize.width;
		this.imgHeight = naturalSize.height;

	};

	PEXETO.utils.fullBgImage.prototype.init = function(){
		var self = this;
		self.positionImage();

		$(window).on('resize', function(){
			self.positionImage();
		});
	};

	PEXETO.utils.fullBgImage.prototype.positionImage = function(){
		var self = this,
			parentWidth = self.$parent.width(),
			parentHeight = self.$parent.height(),
			naturalSize = PEXETO.utils.getNaturalImgSize(self.$img),
			imgWidth = self.imgWidth,
			imgHeight = self.imgHeight,
			displayHeight = Math.round(parentWidth * imgHeight / imgWidth),
			args = {};

			if(parentWidth/parentHeight > imgWidth/imgHeight){
				args = {
					width:'100%',
					height:'auto',
					left:0
				};

				self.$img.css(args);

				var curImgHeight = self.$img.height(),
					top = curImgHeight > parentHeight ? - (curImgHeight - parentHeight) / 2 : 0;
				
				self.$img.css({top:top});

			}else{
			
				args = {
					width:'auto',
					height:'100%',
					top:0
				};

				self.$img.css(args);

				var curImgWidth = self.$img.width(),
					left = curImgWidth > parentWidth ? - (curImgWidth - parentWidth) / 2 : 0;

				self.$img.css({left:left});
			}

		
	};

	PEXETO.utils.supportsTransition = function(){
		if(PEXETO.supportsTransition !== undefined){
			return PEXETO.supportsTransition;
		}

		var b = document.body || document.documentElement,
        s = b.style,
        support = s.transition !== undefined || s.WebkitTransition !== undefined || s.MozTransition !== undefined || s.MsTransition !== undefined || s.OTransition !== undefined;
   		PEXETO.supportsTransition = support;
   		return support;
	};


	/***************************************************************************
	 * FADE EFFECT SLIDER
	 **************************************************************************/


	PEXETO.utils.fadeSlider = function($el, options){
		this.$el = $el;
		var defaults = {
			itemSel : '.slider-container',
			loadingClass : 'loading',
			leftArrowClass : 'fs-left-arrow',
			rightArrowClass : 'fs-right-arrow',
			autoplay : true,
			showNavigation : true,
			animationInterval : 5000,
			pauseOnHover : true
		};
		this.o = $.extend(defaults, options);
	};

	var fs = PEXETO.utils.fadeSlider.prototype;

	fs.init = function(){
		var self = this;

		self.$items = self.$el.find(self.o.itemSel);
		self.itemNum = self.$items.length;
		self.inAnimation = false;

		if(self.itemNum){
			self.$el.addClass(self.o.loadingClass);
			if(self.o.showNavigation && self.itemNum > 1){
				self.addNavigation();
			}
			self.$el.find('img').pexetoOnImgLoaded({
				callback: function(){
					self.loadSlider();
				}
			});
		}

		$(window).on('resize', function(){
			self.doOnWindowResize();
		});
		
	};

	fs.loadSlider = function(){
		var self = this;

		self.items = [];
		self.$items.each(function(){
			self.items.push({
				$el : $(this),
				height : $(this).height()
			});
		});

		self.$el.removeClass(self.o.loadingClass);
		self.showSlide(0);

		if(self.o.autoplay){
			self.setUpAutoplay();
		}
	};

	fs.addNavigation = function(){
		var self = this;

		self.$leftArrow = $('<div />', {'class':self.o.leftArrowClass})
			.appendTo(self.$el)
			.on('click', function(){
				self.doOnSlideChangeTrigger(false);
			});

		self.$rightArrow = $('<div />', {'class':self.o.rightArrowClass})
			.appendTo(self.$el)
			.on('click', function(){
				self.doOnSlideChangeTrigger(true);
			});
	};

	fs.doOnSlideChangeTrigger = function(next){
		var self = this,
			index = 0;

		if(next){
			index = self.currentIndex < self.itemNum - 1 ? self.currentIndex + 1 : 0;
		}else{
			index = self.currentIndex > 0 ? self.currentIndex - 1 : self.itemNum - 1;
		}

		self.showSlide(index);
	};

	fs.doOnWindowResize = function(){
		var self = this,
			curItem = self.items[self.currentIndex];

		//refresh the height value for all the items
		_.each(self.items, function(item){
			item.height = item.$el.height();
		});
		
		//resize the slider
		self.$el.css({height:curItem.height});

	};

	fs.showSlide = function(index){
		var self = this,
			showItem = self.items[index];

		if(!self.inAnimation){
			self.inAnimation = true;

			if(self.currentIndex !== undefined){
				//hide slide
				self.items[self.currentIndex].$el.css({zIndex:0}).animate({opacity:0});
			}

			self.$el.animate({height:showItem.height});

			showItem.$el.css({zIndex:10}).animate({opacity:1}, function(){
				self.currentIndex = index;
				self.inAnimation = false;
			});
		}
	};

	fs.setUpAutoplay = function(){
		var self = this;

		if(!self.o.autoplay){
			return;
		}

		//pause on hover events
		if(self.o.pauseOnHover){
			self.$el.on('mouseenter', function(){
				 self.pause();
			}).on('mouseleave', function(){
				self.startAnimation();
			});
		}

		self.startAnimation();
	};

	fs.startAnimation = function(){
		var self = this;

		self.timer = window.setInterval( function(){
			self.doOnSlideChangeTrigger(true);
		}, self.o.animationInterval);
	};

	fs.pause = function(){
		var self = this;

		window.clearInterval(self.timer);
		self.timer=-1;
	};


}(jQuery));

