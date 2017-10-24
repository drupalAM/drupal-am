function onYouTubeIframeAPIReady(){
	if(PEXETO.pendingVideoFunctions && PEXETO.pendingVideoFunctions.length){
		for(var i in PEXETO.pendingVideoFunctions){
			PEXETO.pendingVideoFunctions[i].call();
		}
	}
}


/**
 * This is the script for the content slider - it slides between different slides with
 * different animation. Each slide is composed of two sections and each of this section
 * is animated in a random effect.
 *
 * Dependencies:
 * - jQuery
 * - jQuery Easing : http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * @author Pexeto
 * http://pexetothemes.com
 */
(function($) {
	"use strict";
	
	$.fn.pexetoContentSlider = function(options) {
		var defaults = {
			//set the default options (can be overwritten from the calling function)
			autoplay          : true,
			autoplayMobile    : false,
			animationInterval : 5000,
			//the interval between each animation when autoplay is true
			pauseOnHover      : true,
			//if true the animation will be paused when the mouse is
			// over the slider
			pauseInterval     : 5000,
			//the time that the animation will be paused after a click
			animationSpeed    : 400,
			easing            : 'easeOutSine',
			buttons           : true,
			//show the navigation buttons
			arrows            : true,
			thumbnailPreview  : true,
			//show the navigation arrows
			//selectors, classes and IDs
			ulSel             : '#cs-slider-ul',
			navigationId      : 'cs-navigation',
			selectedClass     : 'selected',
			firstBoxSel       : '.cs-content-left',
			secondBoxSel      : '.cs-content-right',
			centerBoxSel	  : '.cs-content-centered',
			prevArrowClass    : 'cs-arrows cs-prev-arrow',
			nextArrowClass    : 'cs-arrows cs-next-arrow',
			loadingClass      : 'cs-loading'
		};


		var o            = $.extend(defaults, options),
			$root        = null,
			$parent      = null,
			$ul          = null,
			$nav         = null,
			$navLi       = null,
			$prevArrow   = null,
			$nextArrow   = null,
			itemNum      = 0,
			currentIndex = 0,
			timer        = 0,
			stopped      = true,
			inAnimation  = false,
			sliderHeight = 0,
			showEffects = ['slideLeft', 'slideRight', 'slideUp', 'slideDown'],
			showEffectNum = showEffects.length,
			slides = [],
			$newBgImage = null,
			$oldBgImage = null,
			defaultBg = '#252525',
			isIe8 = PEXETO.getBrowser().msie && PEXETO.getBrowser().version<=8,
			loadProgress = 0,
			videoPlaying = false,
			videoLoading = false,
			pendingVideoFunctions = [],
			mouseIn = false,
			supportsTransition = PEXETO.utils.supportsTransition(),
			isMobile = PEXETO.utils.checkIfMobile(),
			elTiming = isMobile ? 300 : 150;

		$root = $(this);
		$parent = $root.parent();
		$ul = $root.find(o.ulSel);

		if(typeof PEXETO.videoId === 'undefined'){
			PEXETO.videoId = 0;
		}


		/**
		 * Inits the slider - calls the main functionality.
		 */

		function init() {

			$root.addClass(o.loadingClass);

			//get the items number
			itemNum = $ul.find('li').each(function(i) {
				var $li = $(this),
					layout = $li.data('layout');


				slides[i] = {
					firstBox: layout === 'centered' ? $li.find(o.centerBoxSel) : $li.find(o.firstBoxSel),
					secondBox: layout === 'centered' ? null : $li.find(o.secondBoxSel),
					li:$li,
					layout:layout,
					animation:$li.data('animation')
				};

				if(o.thumbnailPreview && $li.data('thumbnail')){
					slides[i].thumbnail = $li.data('thumbnail');
				}

				if($li.data('bg_image_url')){
					slides[i].bgImage = $li.data('bg_image_url');
					slides[i].bgOpacity = $li.data('bg_image_opacity');
				}

				if($li.data('bg_align')){
					slides[i].bgAlign = $li.data('bg_align');
				}

				if($li.data('bg_style')){
					slides[i].bgStyle = $li.data('bg_style');
				}

				if($li.data('bg_color')){
					slides[i].bgColor = $li.data('bg_color');
					$parent.css({backgroundColor:slides[i].bgColor});
				}

				if($li.data('video')){
					slides[i].video = $li.data('video');
				}

			}).length;

			if(!o.arrows && isMobile && itemNum>=1){
				//show the arrows on mobile
				o.arrows = true;
			}

			if(!o.arrows || itemNum<=1){
				o.thumbnailPreview = false;
			}



			$.when(loadSlideImages(0,0), setSliderPadding()).done(function(){
				doOnImgLoaded();
				$root.removeClass(o.loadingClass);
				showSlide(0);
			});

			if(itemNum<=1){
				o.buttons=false;
				o.arrows=false;
				o.autoplay=false;
			}

			if(isMobile){
				o.autoplay=o.autoplayMobile;
				if(o.autoplayMobile){
					o.pauseOnHover = false;
				}
			}

			loadSlideImages(1,itemNum-1).done(doOnImgLoaded);
		}

		function doOnImgLoaded(){
			loadProgress+=0.5;
			if(loadProgress===1){
				//both the first image and the rest of the images have been loaded
				//init the slider functionality
				
				//set the navigation buttons
				setNavigation();
				bindEventHandlers();

				if(o.thumbnailPreview){
					setThumbnailPreviews(0);
				}

				//the images are loaded, start the animation
				if(o.autoplay) {
					startAnimation();
				}

			}
		}

		function loadSlideImages(firstIndex, lastIndex){
			var deferred = new $.Deferred(),
				progress = 0,
				imgSrcToLoad = [],
				imagesToLoad = [],
				imgNum = 0,
				imgLoaded = 0,
				img = null,
				i,
				onImgLoaded = function(){
					imgLoaded++;

					if(imgLoaded===imgNum){
						deferred.resolve();
					}
				};

			for(i=firstIndex; i<=lastIndex; i++){
				slides[i].li.find('img').each(function(){
					imgSrcToLoad.push($(this).attr('src'));
				});

				if(slides[i].bgImage){
					imgSrcToLoad.push(slides[i].bgImage);
				}

				if(o.thumbnailPreview && slides[i].thumbnail){
					imgSrcToLoad.push(slides[i].thumbnail);
				}
			}

			imgNum = imgSrcToLoad.length;

			for(i=0; i<imgNum; i++){
				img = new Image();
				img.src = imgSrcToLoad[i];
				imagesToLoad.push($(img));
			}

			if(imgNum===0){
				deferred.resolve();
			}else{
				for(i=0; i<imgNum; i++){
					imagesToLoad[i].pexetoOnImgLoaded({callback:onImgLoaded});
				}
			}

			return deferred;
		}

		function setSliderPadding(){
			var deferred = new $.Deferred(),
				$logoImg;

			if(isMobile || $root.parents('.content').length){
				deferred.resolve();
				return deferred;
			}

			$logoImg = $('#logo-container').find('img');

			if($logoImg.length){
				$logoImg.pexetoOnImgLoaded({callback:function(){
					var logoHeight = $logoImg.height();
					$parent.css({paddingTop:logoHeight});

					deferred.resolve();
				}});
			}

			return deferred;
		}

		/**
		 * Binds event handlers for the main slider functionality.
		 */

		function bindEventHandlers() {

			if(o.buttons) {
				//add event handlers
				$nav.on({
					'click': doOnBtnClick,
					'slideChanged': doOnSlideChanged
				}, 'li');

			}

			if(o.arrows) {
				$prevArrow.on('click', function() {
					mouseIn = true;
					doOnArrowClick(false);
				});
				$nextArrow.on('click', function() {
					mouseIn = true;
					doOnArrowClick(true);
				});
			}



			//display/hide the navigation on $root hover
			$root.on({
				'mouseenter': doOnSliderMouseEnter,
				'mouseleave': doOnSliderMouseLeave
			});

			$(window).on('resize', function(){
				setSliderHeight(currentIndex);
				centerSlide(currentIndex);
			});
		}

		/**
		 * Calls the functionality to change the current slide with another one.
		 * @param  {int} index the index of the new slide
		 */

		function changeSlide(index) {
			if(!inAnimation) {
				inAnimation = true;
				hideSlide(currentIndex);
				showSlide(index);
				currentIndex = index;

				if(o.thumbnailPreview){
					setThumbnailPreviews(index);
				}

				if(o.buttons) {
					$navLi.trigger('slideChanged');
				}
			}
		}

		function setThumbnailPreviews(index){
			var nextSlideIndex = getNextSlideIndex(index),
				prevSlideIndex = getPrevSlideIndex(index);
			setThumbnailPreview($nextArrow, nextSlideIndex);
			setThumbnailPreview($prevArrow, prevSlideIndex);
		}

		function setThumbnailPreview($arrow, index){
			var thumbnail = slides[index].thumbnail;
			$arrow.find('.cs-thumbnail').remove();
			
			if(thumbnail){
				$arrow.prepend('<img src="'+thumbnail+'" class="cs-thumbnail" />');
			}
		}


		/**
		 * Adds navigation buttons to the slider and sets event handler functions to them.
		 */

		function setNavigation() {
			var i, html = '';

			//generate the buttons
			if(o.buttons) {
				$nav = $('<ul />', {
					id: o.navigationId
				});
				for(i = 0; i < itemNum; i++) {
					html += '<li><span></span></li>';
				}
				$nav.html(html).appendTo($root).fadeIn(700);

				$navLi = $nav.find('li');
				$navLi.eq(0).addClass(o.selectedClass);
			}

			//generate the arrows
			if(o.arrows) {
				$prevArrow = $('<div />', {
					'class': o.prevArrowClass
				}).appendTo($root);
				$nextArrow = $('<div />', {
					'class': o.nextArrowClass
				}).appendTo($root);
			}

		}

		/***********************************************************************
		 * EVENT HANDLER FUNCTIONS
		 **********************************************************************/

		/**
		 * On arrow click event handler. Calls a function to change the slide
		 * depending on which arrow (previous/next) was clicked.
		 * @param  {boolean} next whether the next arrow is clicked (set it to true)
		 * or the previous one was cicked (set it to false)
		 */

		function doOnArrowClick(next) {
			var index;
			if(next) {
				//next index will be the next item if there is one or the first item
				index = getNextSlideIndex(currentIndex);
			} else {
				//previous index will be the previous item if there is one or the last item
				index = getPrevSlideIndex(currentIndex);
			}
			if(!inAnimation) {
				pause();
			}
			changeSlide(index);
		}

		function getNextSlideIndex(index){
			return index + 1 < itemNum ? index + 1 : 0;
		}

		function getPrevSlideIndex(index){
			return index - 1 >= 0 ? index - 1 : itemNum - 1;
		}

		/**
		 * On slider mouse enter event handler.
		 */

		function doOnSliderMouseEnter() {
			if(o.buttons) {
				//show the buttons
				$nav.stop().fadeIn(function() {
					$nav.animate({
						opacity: 1
					}, 0);
				});
			}

			if(o.autoplay && o.pauseOnHover) {
				//pause the animation
				stopAnimation();
				mouseIn = true;
			}
		}

		/**
		 * On slider mouse leave event handler.
		 */

		function doOnSliderMouseLeave() {
			if(o.buttons) {
				//hide the buttons
				$nav.stop().animate({
					opacity: 0
				});
			}

			if(o.autoplay && o.pauseOnHover) {
				//resume the animation
				mouseIn = false;
				startAnimation();
			}
		}

		/**
		 * On navigation button click event handler. Calls the functionality
		 * to show the slide that corresponds to the button index.
		 * @param  {object} e the event object
		 */

		function doOnBtnClick(e) {
			e.stopPropagation();
			var index = $navLi.index($(this));
			if(!inAnimation) {
				pause();
			}
			if(currentIndex !== index) {
				changeSlide(index);
			}
		}

		/**
		 * On slider change event handler. Sets the current button in the navigation
		 * to be selected according to the current slide index.
		 */

		function doOnSlideChanged() {
			var index = $navLi.index($(this));
			if(currentIndex === index) {
				$(this).addClass(o.selectedClass);
			} else {
				$(this).removeClass(o.selectedClass);
			}
		}

		/***************************************************************************
		 * ANIMATION FUNCTIONS
		 **************************************************************************/

		/**
		 * Hides a slide with a random animation.
		 * @param {int} index the index of the slide to be displayed
		 */

		function hideSlide(index) {

			var slide = slides[index],
				$firstBox = slides[index].firstBox,
				$secondBox = slides[index].secondBox,
				finishHide = function($box){
					slide.li.hide();
					$box.find('.cs-element').removeClass('cs-animate');
				};

			if(slide.video){
				hideVideo(slide);
			}


			//hide the first box
			hideInnerElements($firstBox, function(){
				finishHide($firstBox);
			});

			//hide the second box
			if($secondBox){
				hideInnerElements($secondBox, function(){
					finishHide($secondBox);
				});
			}

			if($newBgImage){
				//fade out the background image
				$oldBgImage = $newBgImage;
				$oldBgImage.animate({opacity:0}, 1000, function(){
					$(this).remove();
					$oldBgImage = null;
				});
			}

		}

		function hideInnerElements($box, callback){
			var $elements = $box.find('.cs-element');
			$elements.css({opacity:0});
			if(callback){
				if(supportsTransition){
					$elements.last().on('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function(){
						callback.call();
						$(this).off('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd');
					});
				}else{
					callback.call();
				}
			}
		}

		function centerSlide(index){
			var slide = slides[index];

			if(slide.layout=='centered'){
				var liHeight = slide.li.height(),
					ulHeight = Math.max(sliderHeight, parseInt($ul.css('minHeight'), 10));
				if(ulHeight > liHeight){
					slide.li.css({paddingTop:((ulHeight-liHeight)/2)});
				}
			}
		}

		/**
		 * Displays a slide with a random animation.
		 * @param {int} index the index of the slide to be displayed
		 */

		function showSlide(index) {
			var slide = slides[index],
				animation = slide.animation === 'random' ? 
					showEffects[Math.floor(Math.random() * showEffectNum)] :
					slide.animation,
				//get a random effect
				$firstBox = slide.firstBox,
				$secondBox = slide.secondBox,
				lastBox = true;


			videoPlaying = false;
			slide.li.show();

			if(slide.video){
				initVideo(slide);
			}
			

			setSliderHeight(index);

			centerSlide(index);


			//show the background image
			if(slide.bgImage){
				var cssArgs = {backgroundImage:'url('+slide.bgImage+')'};
				if(slide.bgAlign){
					cssArgs.backgroundPosition = slide.bgAlign;
				}

				if(slide.bgStyle){
					//only the default style is parallax (background-attachment fixed), the other styles are scroll
					cssArgs.backgroundAttachment = 'scroll';
					if(slide.bgStyle==='contain'){
						cssArgs.backgroundSize = 'contain';
					}
				}

				$newBgImage = $('<div />', {'class':'full-bg-image'})
					.css(cssArgs)
					.insertBefore($root)
					.animate({opacity:slide.bgOpacity}, 1000);

				if(isIe8){
					new PEXETO.utils.bgCoverFallback($newBgImage).init();
				}

			}

			if(slide.bgColor){
				$parent.css({backgroundColor:'#'+slide.bgColor});
			}else if($parent.css('backgroundColor')!==defaultBg){
				$parent.css({backgroundColor:defaultBg});
			}
			
			$.when(
				(function(){
					//animate the first box elements with a delay
					var def = new $.Deferred();
					setTimeout(function() {
						showInnerElements($firstBox, animation, def);
					}, o.animationSpeed / 2 + 150);
					return def;
				}()),
				(function(){
					//animate the second box elements
					var def = new $.Deferred();
					if($secondBox){
						showInnerElements($secondBox, animation, def);
					}else{
						def.resolve();
					}
					return def;
				}())
			).done(function(){
				setEndAnimation();
			});
		}

		function initVideo (slide) {
			videoLoading = true;
			pause();

			var videoElId = 'video-'+(PEXETO.videoId++),
				initElVideo = function(){
					var player = new YT.Player(videoElId, {
			          height: '100%',
			          width: '100%',
			          videoId: slide.video,
			          events: {
			            'onReady': function(){
							slide.$videoWrap
								.animate({opacity:1}, 700)
								.parent().removeClass('loading');
							videoLoading = false;
							startAnimation();

							
			            },
			            'onStateChange': function(event){
							if (event.data == YT.PlayerState.PLAYING) {
								stopAnimation();
								videoPlaying = true;
							}
			            }
			          }
			        });

					PEXETO.init.ieIframeFix(); //fix the iframe z-index bug on IE

				};

			slide.$videoWrap = slide.li.find('.cs-video:first').css({opacity:0});
			$('<div />', {'id':videoElId}).appendTo(slide.$videoWrap);
			slide.$videoWrap.parent().addClass('loading');

			if(window.YT && window.YT.Player){
				initElVideo();
			}else{
				if(!PEXETO.pendingVideoFunctions){
					PEXETO.pendingVideoFunctions = [];
				}
				PEXETO.pendingVideoFunctions.push(initElVideo);
			}
		}

		function hideVideo(slide){
			slide.$videoWrap.animate({opacity:0}, function(){
				slide.$videoWrap.empty();
			});
		}

		function showInnerElements($box, animation, def){
			var i = 100,
				settings = getPositionPropertySettings(animation),
				animateProperty = settings.property,
				mult = settings.mult,
				pos = 150*mult,
				$elements = $box.find('.cs-element');

			if(!$elements.length){
				def.resolve();
				return;
			}

			$elements.each(function(index){
				var $el = $(this),
					args = {opacity:0};

				if(!isMobile){
					args[animateProperty] = pos;
				}

				$el.css(args);

				setTimeout(function(){
					var showArgs = {opacity:1},
						args;
					showArgs[animateProperty] = 0;
					args = [showArgs];

					if(index===$elements.length-1){
						//add a callback function to be executed after the
						//element animation
						args.push(function(){
							def.resolve();
						});
					}

					$el.addClass('cs-animate');
					$.fn.pexetoTransit.apply($el, args);
					
				}, i);
				i+=elTiming;
				pos = pos > 20 ? pos-20 : pos;
			});
		}

		function getPositionPropertySettings(animation){
			var settings = {
				'slideLeft' : {property: 'left', mult: 1},
				'slideRight' : {property: 'left', mult: -1},
				'slideUp' : {property: 'top', mult: 1},
				'slideDown' : {property: 'top', mult: -1}
			};

			return settings[animation];

		}

		function setSliderHeight(index){

			sliderHeight = slides[index].li.height();

			$ul.css({height: sliderHeight});
		}


		/**
		 * Starts the animation.
		 */

		function startAnimation() {
			if(o.autoplay && stopped && !videoPlaying) {
				if(o.pauseOnHover && mouseIn){
					//the mouse is still within the container
					return;
				}
				stopped = false;
				timer = window.setInterval(callNextSlide, o.animationInterval);
			}
		}

		/**
		 * Sets the inAnimation variable to false.
		 */

		function setEndAnimation() {
			inAnimation = false;
		}

		/**
		 * Triggers a changeSlide event to display the next slide.
		 */

		function callNextSlide() {
			if(!videoLoading){
				var nextIndex = (currentIndex < itemNum - 1) ? (currentIndex + 1) : 0;
				changeSlide(nextIndex);
			}
		}

		/**
		 * Stops the animation.
		 */

		function stopAnimation() {
			if(o.autoplay) {
				window.clearInterval(timer);
				timer = -1;
				stopped = true;
			}
		}

		/**
		 * Pauses the animation.
		 */

		function pause() {
			if(o.autoplay) {
				window.clearInterval(timer);
				timer = -1;
				if(!stopped) {
					window.setTimeout(startAnimation, o.pauseInterval);
				}
				stopped = true;
			}
		}


		init();

	};

}(jQuery));