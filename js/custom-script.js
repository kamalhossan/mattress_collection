var $ = jQuery.noConflict();
jQuery(document).ready(function () {
  const $slider = $(".our-most-loved-sleep-systems-wrapper");
  $slider.slick({
    // 		slidesToShow: 5,
    variableWidth: true,
    arrows: true,
    cssEase: "ease-in-out",
    prevArrow: $(".prev"),
    nextArrow: $(".next"),
    infinite: true,
    variableWidth: true,
  });

  $(".mattress_collection_trust_slider").slick({
    slidesToShow: 1,
    variableWidth: false,
    arrows: true,
    prevArrow:
      '<button class="slick-prev slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png" alt="Previous"></button>',
    nextArrow:
      '<button class="slick-next slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png" alt="Next"></button>',
    cssEase: "ease-in-out",
    infinite: false,
  });

  $(".mattress_happy_customer_slider").slick({
    slidesToShow: 4,
    cssEase: "ease-in-out",
    infinite: false,
    dots: true,
    arrows: false,
    responsive: [
      {
        breakpoint: 980,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1.3,
        },
      },
    ],
  });

  $(".mattress_sleep_smart_slider").slick({
    slidesToShow: 3,
    arrows: true,
    dots: false,
    prevArrow:
      '<button class="slick-prev slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png" alt="Previous"></button>',
    nextArrow:
      '<button class="slick-next slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png" alt="Next"></button>',
    cssEase: "ease-in-out",
    infinite: false,
    responsive: [
      {
        breakpoint: 980,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1.3,
        },
      },
    ],
  });
  //
  $(".bed-gallery-main-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: ".bed-gallery-thumb-slider",
    variableWidth: false,
  });

  // Thumbnail Slider
  $(".bed-gallery-thumb-slider").slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    asNavFor: ".bed-gallery-main-slider",
    vertical: true,
    verticalSwiping: true,
    dots: false,
    arrows: false,
    focusOnSelect: true,
    responsive: [
      {
        breakpoint: 980,
        settings: {
          vertical: false,
          verticalSwiping: false,
          slidesToShow: 4,
        },
      },
    ],
  });
  //
  // control the request mattress size
  $(
    ".matt-base-info.mobile #mattress_size_submit, .matt-base-info.desktop #mattress_size_submit",
  ).on("click", function (e) {
    e.preventDefault();
    var mattress_size = $(this)
      .closest(".matt-base-info")
      .find("#mattress_size_select")
      .val();
    var form = $("#forminator-module-424");
    var hiddenfield = $("#hidden-2 input").val(mattress_size);
    var offset = form.offset().top;
    $("html, body").animate(
      {
        scrollTop: offset,
      },
      500,
    );
  });
  //
});

document.addEventListener("DOMContentLoaded", function () {
  const loadMoreBtn = document.getElementById("load-more-button");

  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", function () {
      const offset = parseInt(this.getAttribute("data-offset"));

      const data = new FormData();
      data.append("action", "bed_product_load_more");
      data.append("offset", offset);

      fetch(admin_ajaax.ajax_url, {
        method: "POST",
        body: data,
      })
        .then((response) => response.json())
        .then((result) => {
          if (result.success) {
            document
              .getElementById("bed-product-grid")
              .insertAdjacentHTML("beforeend", result.data);
            const newOffset = offset + 12;
            this.setAttribute("data-offset", newOffset);
          } else {
            alert("No more products!");
          }
        });
    });
  }

  if (typeof Swiper !== "undefined") {
    const iconSliderEl = document.querySelector(".icon_with_slider.swiper");
    if (iconSliderEl) {
      new Swiper(iconSliderEl, {
        slidesPerView: "auto",
        spaceBetween: 20,
        freeMode: true,
        grabCursor: true,
        scrollbar: {
          el: iconSliderEl.querySelector(".swiper-scrollbar"),
          draggable: true,
        },
        breakpoints: {
          768: {
            slidesPerView: 2,
            spaceBetween: 24,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 30,
          },
        },
      });
    }
  }
});

$("document").ready(function () {
  $(".filter-button").click(function () {
    $(".filter-form").addClass("active");
    $("body").addClass("overflow-hidden");
    $(".overlay_filter").addClass("active");
  });
  $(".filter-header img").click(function () {
    $(".filter-form").removeClass("active");
    $("body").removeClass("overflow-hidden");
    $(".overlay_filter").removeClass("active");
  });
  // 	$('.woocommerce-shop #header-outer').addClass('transparent')
  //
  var $ProductFeaturesslider = $(".matt-product-features-slider");
  var $progressBar = $(".slider-progress-bar");

  // Function to calculate and update progress bar width
  function updateProgressBar(slick, currentSlide) {
    // Total number of steps/pages in the slider
    var totalSlides = slick.slideCount;
    var slidesToShow = slick.options.slidesToShow;

    // Calculate the maximum index the slider can move to
    var maxScrollSteps = totalSlides - slidesToShow + 1;

    // Calculate percentage width for the active bar
    var percentage = ((currentSlide + 1) / maxScrollSteps) * 100;

    // Caps it at 100% just in case
    if (percentage > 100) percentage = 100;

    $progressBar.css("width", percentage + "%");
  }

  // 1. Event listener before slider initializes
  $ProductFeaturesslider.on("init", function (event, slick) {
    updateProgressBar(slick, 0);
  });

  // 2. Initialize Slick
  $ProductFeaturesslider.slick({
    slidesToShow: 1.2,
    slidesToScroll: 1,
    arrows: false, // Turn off arrows if you only want the scrollbar visual
    dots: false,
    infinite: false, // Progress bars look best when infinite scroll is disabled
    responsive: [
      {
        breakpoint: 980,
        settings: {
          slidesToShow: 2.3, // Drop to 1 column on mobile
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1.2, // Drop to 1 column on mobile
        },
      },
    ],
  });

  // 3. Event listener on slide change
  $ProductFeaturesslider.on(
    "beforeChange",
    function (event, slick, currentSlide, nextSlide) {
      updateProgressBar(slick, nextSlide);
    },
  );
  //
});

// image code starts//

document.addEventListener("click", function (e) {
  if (e.target.matches(".product_pillow_images img")) {
    const mainImage = document.getElementById("mainImage");
    if (mainImage) {
      mainImage.src = e.target.src;

      document
        .querySelectorAll(".product_pillow_images img")
        .forEach((i) => i.classList.remove("active"));

      e.target.classList.add("active");
    }
  }
});
