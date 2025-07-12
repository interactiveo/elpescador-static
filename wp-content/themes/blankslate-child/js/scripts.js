(function ($) {

	$(function () {


		// MODAL WINDOWS
		$(".modal-window").hide();
		$("#header-burger").click(function () {
			$(this).toggleClass("active");
			$("body").toggleClass("modal-open");
			$(".modal-window:not(#header-overlay)").hide();
			$("#header-overlay").fadeToggle('fast');
		});
		$("#header-search").click(function () {
			$(this).toggleClass("active");
			$(".modal-window:not(#search-overlay)").hide();
			$("#search-overlay").slideToggle('fast');
		});
		$(window).on("load resize scroll", function (e) {
			if ($(window).width() < 1280) { } else {
				$("#header-overlay").hide();
				$("#header-burger").removeClass("active");
				$('body').removeClass("modal-open");
			}
		});


		// ESC KEY CLOSE
		$(document).ready(function () {
			$(document).keydown(function (e) {
				if (e.keyCode == 27) {
					$(".modal-window").hide();
					$("#header-burger").removeClass("active");
					$('body').removeClass("modal-open");
				}
			});
		});


		// HEADER ADMINTOOL BAR FIX
		$(document).ready(function () {
			var adminToolbarHeight = $('body.logged-in #wpadminbar').outerHeight();
			$('body.logged-in #header').css({
				'top': '' + adminToolbarHeight + 'px'
			});
		});
		$(window).on("resize", function (e) {
			var adminToolbarHeight = $('body.logged-in #wpadminbar').outerHeight();
			$('body.logged-in #header').css({
				'top': '' + adminToolbarHeight + 'px'
			});
		});

		$(document).ready(function () {
			var headerHeight = $('body:not(.logged-in) #header').outerHeight();
			$('body:not(.logged-in) #header-overlay').css({
				'top': '' + headerHeight + 'px'
			});
		});
		$(window).on("resize", function (e) {
			var headerHeight = $('body:not(.logged-in) #header').outerHeight();
			$('body:not(.logged-in) #header-overlay').css({
				'top': '' + headerHeight + 'px'
			});
		});

		$(document).ready(function () {
			var headerHeight = $('body.logged-in #header').outerHeight();
			var headerHeight2 = $('body.logged-in #wpadminbar').outerHeight();
			var headerHeight3 = headerHeight + headerHeight2;
			$('body.logged-in #header-overlay').css({
				'top': '' + headerHeight3 + 'px'
			});
		});
		$(window).on("resize", function (e) {
			var headerHeight = $('body.logged-in #header').outerHeight();
			var headerHeight2 = $('body.logged-in #wpadminbar').outerHeight();
			var headerHeight3 = headerHeight + headerHeight2;
			$('body.logged-in #header-overlay').css({
				'top': '' + headerHeight3 + 'px'
			});
		});


		// INSERT ARROW
		$('body .insert-arrow').each(function () {
			var $this = $(this), text = $this.text().trim(), words = text.split(/\s+/);
			var lastWord = words.pop();
			words.push('<span class="nowrap">' + lastWord + '<i class="icon-"></i>');
			$this.html(words.join(' '));
		});


		// SELECT2
		$(document).ready(function () {
			$('select').select2({
				minimumResultsForSearch: -1
			});
		});

		// SWIPER
		$(document).ready(function () {
			var dragScroller = new Swiper('#dragScroll', {
				slidesPerView: 'auto',
				spaceBetween: 30,
				initialSlide: 0,
				freeMode: true,
				lazy:true,
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				  },				  
			});

			var slidingImages = new Swiper('#slidingImages', {
				initialSlide : 0,
				lazy: false,
				loop: true,
				freeMode: true,
				doubleClick: false,
				doubleTap: false,
				speed: 50000,
				autoplay: {
					delay: 0,
					disableOnInteraction: false
				},
			});

			var slidingImages = new Swiper('#slidingGallery', {
				slidesPerView: 2,
				spaceBetween: 30,
				freeMode: true,
				lazy:true,
				grid: {
					rows: 2,
					fill: 'row',
				},
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				  },
				breakpoints: {
					640: {
						slidesPerView: 2,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
					768: {
					  slidesPerView: 3,
					  grid: {
						rows: 2,
						fill: 'row',
					},
					},
					1024: {
					  slidesPerView: 4,
					  grid: {
						rows: 2,
						fill: 'row',
					},
					},
					1200: {
					  slidesPerView: 4,
					  grid: {
						rows: 3,
						fill: 'row',
					},
					},
				  },
			});

		});

		// Header Shadow
    // Function to calculate and set opacity
    function updateOpacity() {
        let scrollTop = $(window).scrollTop();
        let opacity = Math.min(scrollTop / 500, 1); // Every 5px = 1%, capped at 100%
        $('#header-shadow').css('opacity', opacity);
    }

    // Update opacity on page load/refresh
    updateOpacity();

    // Update opacity on scroll
    $(window).on('scroll', function() {
        updateOpacity();
    });

	// DATATABLES 
$(document).ready(function() {
  $('.flex-id-custom_html table').DataTable({
    scrollX: true,
	paging: false,
	searching: false,
	info: false,
	ordering: false
  });
});





	}); //end

}(jQuery));
