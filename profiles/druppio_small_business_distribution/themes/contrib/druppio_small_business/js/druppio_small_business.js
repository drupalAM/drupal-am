/* SAMPLE JS FILE CONTENTS */

(function ($) {
  appear=function(){"use strict";function e(){var e=window.scrollY||window.pageYOffset;null!=n&&(o.velocity=e-n,o.delta=o.velocity>=0?o.velocity:-1*o.velocity),n=e,i&&clearTimeout(i),i=setTimeout(function(){n=null},30)}function t(e,t){var n=e.getBoundingClientRect();return n.top+n.height>=0&&n.left+n.width>=0&&n.bottom-n.height<=(window.innerHeight||document.documentElement.clientHeight)+t&&n.right-n.width<=(window.innerWidth||document.documentElement.clientWidth)+t}var n=null,i=0,o={};return addEventListener("scroll",e,!1),function(e){return function(e){function n(e,t){return function(){var n=this,i=arguments;clearTimeout(l),l=setTimeout(function(){e.apply(n,i)},t)}}function i(){o.delta<y.delta.speed&&(s||(s=!0,d(),setTimeout(function(){s=!1},y.delta.timeout))),n(function(){d()},y.debounce)()}function r(){d(),addEventListener("scroll",i,!1),addEventListener("resize",i,!1)}function a(){v=[],l&&clearTimeout(l),u()}function u(){removeEventListener("scroll",i,!1),removeEventListener("resize",i,!1)}function d(){f||(v.forEach(function(e,n){e&&t(e,y.bounds)?h[n]&&(h[n]=!1,g++,y.appear&&y.appear(e),y.disappear||y.reappear||(v[n]=null)):(h[n]===!1&&(y.disappear&&y.disappear(e),w++,y.reappear||(v[n]=null)),h[n]=!0)}),y.reappear||y.appear&&(!y.appear||g!==p)||y.disappear&&(!y.disappear||w!==p)||(f=!0,u(),y.done&&y.done()))}function c(){if(!m){m=!0,y.init&&y.init();var e;if(e="function"==typeof y.elements?y.elements():y.elements){p=e.length;for(var t=0;p>t;t+=1)v.push(e[t]),h.push(!0);r()}}}var p,l,s,f,m=!1,v=[],h=[],g=0,w=0,y={};return function(e){e=e||{},y={init:e.init,elements:e.elements,appear:e.appear,disappear:e.disappear,done:e.done,reappear:e.reappear,bounds:e.bounds||0,debounce:e.debounce||50,delta:{speed:e.deltaSpeed||50,timeout:e.deltaTimeout||500}},addEventListener("DOMContentLoaded",c,!1);var t=!1;Function("/*@cc_on return document.documentMode===10@*/")()&&(t=!0);var n="complete"===document.readyState||"loaded"===document.readyState;return t?n&&c():(n||"interactive"===document.readyState)&&c(),{trigger:function(){d()},pause:function(){u()},resume:function(){r()},destroy:function(){a()}}}}()(e)}}();

  /* Mobile menu */
$(function () {
  $('#block-mobilemenutrigger,.sliding-panel-fade-screen,.sliding-panel-close').on('click touchstart', function (e) {
    $('#block-mobilemenutrigger, #navigation, .sliding-panel-fade-screen').toggleClass('is-visible');
    e.preventDefault();
  });
});


/* Scroll to top */
$(function () {
  $('html, body').animate({
    scrollTop: 0
  });
});

/* navigation and branding animation */
$(function () {
  var element = $('body, #navigation');
  $(window).bind('scroll', function () {
    if ($(window).scrollTop() > 1) {
      if (element.hasClass('fixed')) {} else {
        element.addClass('animation');
        setTimeout(function () {
          element.removeClass('animation');
        }, 350);
        setTimeout(function () {
          element.addClass('ready');
        }, 1450);

      }
      element.addClass('fixed');
    } else {
      element.addClass('animation');
      setTimeout(function () {
        element.removeClass('animation');
      }, 350);
      element.removeClass('fixed ready');
    }
  });
});

	
/* Parallax header image */
$(function () {
  setTimeout(function () {
    var rellax = new Rellax('.header.images .slide');
  }, 500);
});

$(function () {
	$('.header.images .field__items').cycle({
			fx: 'fade',
			timeout: 7000
	});
});



/* Replace all SVG images with inline SVG */
jQuery('img.svg').each(function () {
  var $img = jQuery(this);
  var imgID = $img.attr('id');
  var imgClass = $img.attr('class');
  var imgURL = $img.attr('src');

  jQuery.get(imgURL, function (data) {
    // Get the SVG tag, ignore the rest
    var $svg = jQuery(data).find('svg');

    // Add replaced image's ID to the new SVG
    if (typeof imgID !== 'undefined') {
      $svg = $svg.attr('id', imgID);
    }
    // Add replaced image's classes to the new SVG
    if (typeof imgClass !== 'undefined') {
      $svg = $svg.attr('class', imgClass + ' replaced-svg');
    }

    // Remove any invalid XML tags as per http://validator.w3.org
    $svg = $svg.removeAttr('xmlns:a');

    // Replace image with new SVG
    $img.replaceWith($svg);

  }, 'xml');

});

/* Navigation */

$(function () {
  $('a[href*="#"]:not([href="#"])').click(function () {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 800);
        return false;
      }
    }
  });
});

if ($(".path-frontpage").length > 0) {
  var preventHash = false;
  var gAppear = null;

  function initAppear() {
    gAppear = appear({
      reappear: true,
      bounds: -($(window).height() / 2),
      elements: function () {
        return document.querySelectorAll('.page-section');
      },
      appear: function (el) {
        var id = el.getAttribute('id');
        $("nav a").removeClass('is-active-menu');
        $('nav a[href*="' + id + '"]').addClass('is-active-menu');
        preventHash = true;
      }
    })

  }

  $(function () {
    $(document).ready(function () {
      if (history.pushState !== undefined) {
        initAppear();
        window.addEventListener('resize', function () {
          gAppear.destroy();
          initAppear();
        });
        window.addEventListener("hashchange", function (e) {
          if (preventHash) {
            //e.preventDefault();
            preventHash = false;
          }
        });
      }
    });
  });
}


})(jQuery);
