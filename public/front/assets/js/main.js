(function ($) {
  "use strict";

  // Preloader
	$(window).on('load', function () {
		$("#shikor_preloader").delay(500).fadeOut(300);
		$("#shikor_preloader").delay(500).fadeOut(300);
	})

  // Offcanvas menu
  $(".header-btn").on("click", function () {
    $(".extra-info,.offcanvas-overlay").addClass("active");
    return false;
  });
  $(".menu-close,.offcanvas-overlay").on("click", function () {
    $(".extra-info,.offcanvas-overlay").removeClass("active");
  });

  //Header Search Form
  if ($(".search-trigger").length) {
    $(".search-trigger").on("click", function () {
      $("body").addClass("search-active");
    });
    $(".close-search, .search-back-drop").on("click", function () {
      $("body").removeClass("search-active");
    });
  }

  // data-backround

  $("[data-background").each(function () {
    $(this).css(
      "background-image",
      "url( " + $(this).attr("data-background") + "  )"
    );
  });

  // Metis Menu

  $("#mobile-menu").metisMenu();

  $("#hamburger").on("click", function () {
    $(".mobile-nav").addClass("show");
    $(".overlay").addClass("active");
  });

  $(".close-nav").on("click", function () {
    $(".mobile-nav").removeClass("show");
    $(".overlay").removeClass("active");
  });

  $(".overlay").on("click", function () {
    $(".mobile-nav").removeClass("show");
    $(".overlay").removeClass("active");
  });

  // Sticky Header Js

  var windowOn = $(window);

  windowOn.on("scroll", function () {
    var scroll = windowOn.scrollTop();
    if (scroll < 400) {
      $("#header-sticky").removeClass("header-sticky");
    } else {
      $("#header-sticky").addClass("header-sticky");
    }
  });

  
  // Portfolio Slider

  $(".project-wrapper").owlCarousel({
    items: 1,
    margin: 30,
    dots: true,
    nav: false,
    loop: true,
    autoplay: true,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false,
        dots: false,
      },

      575: {
        items: 1,
        nav: false,
        dots: false,
      },

      767: {
        items: 2,
        nav: false,
        dots: false,
      },

      990: {
        items: 3,
        loop: true,
      },
      1200: {
        items: 4,
        dots: true,
        loop: true,
      },
    },
  });

  // Testiomonial Slider

  $(".testimonial-wrap").owlCarousel({
    items: 1,
    margin: 30,
    dots: true,
    nav: false,
    loop: true,
    autoplay: false,
    responsiveClass: true,
  });

 
  $('.testimonial-slider').slick({
		slidesToShow: 1,
		arrows: false,
    infinite: true,
    autoplay: true,
	  dots: false,	
		
	});

    // Client Slider

    $(".client-wrapper").owlCarousel({
      loop: true,
      items: 5,
      dots: true,
      nav: false,
      smartSpeed: 500,
      autoHeight: false,
      touchDrag: true,
      mouseDrag: true,
      margin: 30,
      autoplay: true,
      responsive: {
        0: {
          items: 1,
          nav: false,
          dots: false,
        },
        600: {
          items: 2,
          nav: false,
          dots: false,
        },
        768: {
          items: 3,
          nav: false,
          dots: false,
        },
        1100: {
          items: 5,
          nav: false,
          dots: true,
        },
      },
    });

      // Portfolio Slider

  $(".project-slider").owlCarousel({
    items: 1,
    margin: 30,
    dots: false,
    nav: true,
    center: true,
    loop: true,
    autoplay: false,
    navText: [
      "<i class='las la-arrow-left'></i>",
      "<i class='las la-arrow-right'></i>",
    ],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false,
        dots: false,
      },
      575: {
        items: 1,
        nav: false,
        dots: false,
      },

      767: {
        items: 3,
        nav: false,
        dots: true,
      },

      990: {
        items: 3,
        loop: true,
      },
      1200: {
        items: 3,
        dots: true,
        loop: true,
      },
    },
  });

  // Feature Slider

  $(".feature_item").slick({
    speed: 8000,
    autoplay: true,
    autoplaySpeed: 1000,
    centerMode: true,
    cssEase: "linear",
    slidesToShow: 1,
    slidesToScroll: 1,
    variableWidth: true,
    infinite: true,
    initialSlide: 1,
    arrows: false,
    buttons: false,
  });

  // Hero Area Slider

  $(".homepage-slides").owlCarousel({
    items: 1,
    dots: false,
    nav: true,
    loop: true,
    autoplay: false,
    autoplayTimeout: 5000,
    smartSpeed: 2000,
    slideSpeed: 600,
    navText: [
      "<i class='las la-arrow-left'></i>",
      "<i class='las la-arrow-right'></i>",
    ],
    responsive: {
      0: {
        items: 1,
        nav: false,
        dots: false,
      },
      600: {
        items: 1,
        nav: false,
        dots: false,
      },
      768: {
        items: 1,
      },
      1100: {
        items: 1,
      },
    },
  });

  $(".homepage-slides").on("translate.owl.carousel", function () {
    $(".single-slide-item h3")
      .removeClass("animated fadeInUp")
      .css("opacity", "1");
    $(".single-slide-item h1")
      .removeClass("animated fadeInDown")
      .css("opacity", "1");
    $(".single-slide-item p")
      .removeClass("animated fadeInUp")
      .css("opacity", "1");
    $(".single-slide-item .main-btn")
      .removeClass("animated fadeInDown")
      .css("opacity", "1");
  });

  $(".homepage-slides").on("translated.owl.carousel", function () {
    $(".single-slide-item h3")
      .addClass("animated fadeInUp")
      .css("opacity", "1");
    $(".single-slide-item h1")
      .addClass("animated fadeInDown")
      .css("opacity", "1");
    $(".single-slide-item p").addClass("animated fadeInUp").css("opacity", "1");
    $(".single-slide-item .main-btn")
      .addClass("animated fadeInDown")
      .css("opacity", "1");
  });

  // Odometer js

  $(".odometer").appear(function (e) {
    var odo = $(".odometer");
    odo.each(function () {
      var countNumber = $(this).attr("data-count");
      $(this).html(countNumber);
    });
  });  

  //jQuery Animation
  new WOW().init();

  // Nice select
  $("select").niceSelect();

  //Counter Up

  $(".counter-number span").counterUp({
    delay: 10,
    time: 1000,
  });

  //Magnific Pop-up

  $(".video-play-btn").magnificPopup({
    type: "iframe",
  });

  
  // Active & Remove Class

  $(".sub-menu ul li").on("click", function () {
    $(".sub-menu").removeAttribute("style");
    $(this).addClass("active");
  });

  $("a.nav-link").on("mouseover", function () {
    $("a.nav-link").removeClass("active");
    $(this).addClass("active");
  });

  //Hide Loading Box (Preloader)
  function handlePreloader() {
    if ($(".preloader").length) {
      $(".preloader").delay(200).fadeOut(500);
    }
  }

  $(window).on("load", function () {
    handlePreloader();
  });

  // Mouse Custom Cursor
  function itCursor() {
    var myCursor = jQuery(".mouseCursor");
    if (myCursor.length) {
      if ($("body")) {
        const e = document.querySelector(".cursor-inner"),
          t = document.querySelector(".cursor-outer");
        let n,
          i = 0,
          o = !1;
        (window.onmousemove = function (s) {
          o ||
            (t.style.transform =
              "translate(" + s.clientX + "px, " + s.clientY + "px)"),
            (e.style.transform =
              "translate(" + s.clientX + "px, " + s.clientY + "px)"),
            (n = s.clientY),
            (i = s.clientX);
        }),
          $("body").on("mouseenter", "button, a, .cursor-pointer", function () {
            e.classList.add("cursor-hover"), t.classList.add("cursor-hover");
          }),
          $("body").on("mouseleave", "button, a, .cursor-pointer", function () {
            ($(this).is("a", "button") &&
              $(this).closest(".cursor-pointer").length) ||
              (e.classList.remove("cursor-hover"),
                t.classList.remove("cursor-hover"));
          }),
          (e.style.visibility = "visible"),
          (t.style.visibility = "visible");
      }
    }
  }
  itCursor();
})(window.jQuery);
