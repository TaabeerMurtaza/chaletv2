$(".ts-card-slider").each(function () {
  $(this).slick({
    infinite: true,
    speed: 500,
    fade: true,
    cssEase: "linear",
    prevArrow:
      "<button type='button' class='slick-prev cs-arrow'><img src='"+theme_url+"/assets/images/icons/arrow-l.svg' alt='icons' /></button>",
    nextArrow:
      "<button type='button' class='slick-next cs-arrow'><img src='"+theme_url+"/assets/images/icons/arrow-r.svg' alt='icons' /></button>",
  });
});

$(".gall-slider").each(function () {
  $(this).slick({
    dots: true,
    infinite: false,
    speed: 300,
    arrows: false,
    slidesToShow: 5,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          dots: false,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          dots: false,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: false,
        },
      },
    ],
  });
});

$(".image-slider").each(function () {
  $(this).slick({
    dots: true,
    infinite: false,
    speed: 300,
    arrows: false,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          dots: false,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          dots: false,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: false,
        },
      },
    ],
  });
});

$(document).ready(function () {
  $(".extra-nav").hide();
  $(".menu-btn").click(function () {
    $(this).toggleClass("active");
    $(".header-button").toggleClass("active");
  });
});
