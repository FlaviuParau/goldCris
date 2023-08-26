function initializeListSearch(e){return null==e?void setDefaultValues():(toggleAnimationSpeed=void 0!==e.toggleAnimationSpeed?e.toggleAnimationSpeed:250,cssActiveClass=void 0!==e.cssActiveClass?e.cssActiveClass:"active",itemSelector=void 0!==e.itemSelector?e.itemSelector:".collection-item",openLinkWithEnterKey=void 0!==e.openLinkWithEnterKey&&e.openLinkWithEnterKey,searchTextBoxSelector=void 0!==e.searchTextBoxSelector?e.searchTextBoxSelector:"#search-box",void(noItemsFoundSelector=void 0!==e.noItemsFoundSelector?e.noItemsFoundSelector:".no-apps-found"))}function setDefaultValues(){toggleAnimationSpeed=250,itemSelector=".collection-item",cssActiveClass="active",openLinkWithEnterKey=!1,searchTextBoxSelector="#search-box",noItemsFoundSelector=".no-apps-found"}function searchListItems(e){return""===e?(resetSearch(),void jQuery(noItemsFoundSelector).hide()):void((foundItems=findItemsInList(e)).length>0&&openLinkWithEnterKey?(foundItems[0].addClass(cssActiveClass),jQuery(noItemsFoundSelector).hide()):jQuery(noItemsFoundSelector).show())}function resetSearch(){jQuery(itemSelector).slideDown(toggleAnimationSpeed),jQuery(itemSelector).removeClass(cssActiveClass),foundItems=jQuery(itemSelector)}function findItemsInList(e){for(var t=jQuery(itemSelector),s=[],o=0;o<t.length;o++){var i=t[o];jQuery(i).removeClass(cssActiveClass),jQuery(i).children("a").html().toLowerCase().indexOf(e.toLowerCase())<0?jQuery(i).slideUp(toggleAnimationSpeed):(s.push(jQuery(i)),jQuery(i).slideDown(toggleAnimationSpeed))}return s}function selectNextItem(e){if(openLinkWithEnterKey)if(0===jQuery(itemSelector+"."+cssActiveClass).length)"next"===e&&jQuery(foundItems[0]).addClass(cssActiveClass),"prev"===e&&jQuery(foundItems[foundItems.length-1]).addClass(cssActiveClass);else{var t=jQuery(itemSelector+"."+cssActiveClass);t.removeClass(cssActiveClass),"next"===e&&t.nextAll(itemSelector+":visible").first().addClass(cssActiveClass),"prev"===e&&t.prevAll(itemSelector+":visible").first().addClass(cssActiveClass)}}function openLink(){if(openLinkWithEnterKey){var e=jQuery(itemSelector+"."+cssActiveClass).first().children("a").attr("href");void 0===e&&null!==e||(document.location.href=e)}}var toggleAnimationSpeed,itemSelector,foundItems,cssActiveClass,openLinkWithEnterKey,searchTextBoxSelector,noItemsFoundSelector;jQuery(document).ready(function(){jQuery(searchTextBoxSelector).each(function(){resetSearch(),jQuery(this).on("input propertychange",function(){searchListItems(jQuery(this).val())}),jQuery(searchTextBoxSelector).keydown(function(e){40===e.keyCode&&selectNextItem("next"),38===e.keyCode&&selectNextItem("prev"),13===e.keyCode&&openLink()})})});

(function($) {
    $(document).ready(function() {
        // Scroll animation after selected filter
        var topFilters = $('.block-layered-nav-top .currently-wrapper');
        if ($(topFilters).length) {
            var target = $('.main-content');

            $('html, body').animate({
                scrollTop: target.offset().top
            }, 500);
        }

        // Show dropdown filters
        $('.filter-box').each(function(e){
            $(this).on('click', function(e){
                $('.collection-item').removeClass('collection-item');
                $(this).find('ol').children().addClass('collection-item');
                $(this).siblings().find('ol').hide();
                $(this).find('ol').show();
                e.stopPropagation();
            });
        });

        // Hide filters
        $(document).on('click', function(){
            $(".filter-box ol").hide();
        });
    });
})(jQuery);

// Initiliase filter search
initializeListSearch({
    toggleAnimationSpeed: 0,
    openLinkWithEnterKey: true,
    itemSelector: '.collection-item',
    searchTextBoxSelector: '.filter-input',
    noItemsFoundSelector: '.no-apps-found'
});