(function($) {
    $(document).ready(function() {
      // Hide parent menu if is hidden
      if(!$('.campaign-main-menu ul.main-menu li.parent-menu').is(':visible')) {
        $('.campaign-main-menu ul.main-menu > li.parent-menu').remove();
      };

      $('.touch .campaign-main-menu ul').slick({
        dots: true,
        arrows: false,
        variableWidth: true,
        infinite: false,
        slidesToShow: 4,
        responsive: [
            {
                breakpoint: 995,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
      });

      // Scroll to fixed main menu
      $('.campaign-main-menu').scrollToFixed();

      // Add cookie campaign
      var blugentoCampaign = $('.campaign');
      if ( $(blugentoCampaign).length) {
          var cookieCampaign = 'blugento-campaign',
              cookieLifetime = $('body').attr('data-cookie-lifetime') || 3600,
              now = new Date();

          now.setTime(now.getTime() + 1 * cookieLifetime * 1000);

          $.cookie(cookieCampaign, 'yes', {
              expires: now
          });
      }

      // Add smooth scrolling to all links
      $('.column-1-campaign .campaign-main-menu a[href*=#], .columns-2-campaign .campaign-main-menu a[href*=#]').on('click', function(e) {
          e.preventDefault(); // prevent hard jump, the default behavior

          var target = $(this).attr("href"); // Set the target as variable

          // perform animated scrolling by getting top-position of target-element and set it as scroll target
          $('html, body').stop().animate({
              scrollTop: $(target).offset().top
          }, 600, function() {
              location.hash = target; //attach the hash (#jumptarget) to the pageurl
          });

          var thisIndex = $(this).parent().attr('data-slick-index');
  
          if ($('.touch').length) {
            var slider = $( '.touch .campaign-main-menu ul' );
            slider[0].slick.slickGoTo(thisIndex);
          }

          return false;
      });

      $(window).scroll(function() {
        var scrollDistance = $(window).scrollTop();

        $('.page-section').each(function(i) {
            if ($(this).position().top <= scrollDistance) {
                $('.campaign-main-menu a.active').removeClass('active');
                $('.campaign-main-menu a').eq(i).addClass('active');

            if ( $('.touch').length) {
                setTimeout(function(){
                    var thisIndex = $('.campaign-main-menu a.active').parent().attr('data-slick-index');
                    var slider = $( '.touch .campaign-main-menu ul' );
                    slider[0].slick.slickGoTo(thisIndex);
                    }, 500);
                }
            }
        });
      }).scroll();

      // Get category ajax
      $('.columns-2-campaign-category-ajax .campaign-main-menu a').each(function(e) {
        $(this).on('click', function(e) {
            e.preventDefault();

            $('#ajax-overlay-campaign').show();
            $('.campaign-main-menu a.active').removeClass('active');
            $(this).addClass('active');
            $('.category-products .products-grid').remove();

            var categoryUrl = window.origin,
                categoryId = $(this).attr('id'),
                outOfStock = $(this).attr('data-stock'),
                showData = $('.category-products');

            $.ajax({
                type: 'GET',
                url: categoryUrl + "/campaign/campaign/category?category_id=" + categoryId + "&stock=" + outOfStock,
                success: function(data){
                    $(showData).append(data);
                    $('html, body').stop().animate({
                        scrollTop: $('.category-products-campaign').offset().top
                    }, 600);
                    $('#ajax-overlay-campaign').hide();
                },
                error: function(data){
                    $('#ajax-overlay-campaign').hide();
                }
            });
        });
      });
    });
})(jQuery);