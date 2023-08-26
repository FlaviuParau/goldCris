/*! blugento-theme v1.0.0 - 2020-05-07 20:29:01 */
;var Blugento=Blugento||{};Blugento.Review={},function(a){a.extend(Blugento.Review,{productList:function(){a(".jq-ratings").rating()}})}(jQuery),jQuery(document).ready(function(a){Blugento.Review.productList(),a(".button-review").on("click",function(b){b.preventDefault(),a("html, body").animate({scrollTop:a("#form-add-review").offset().top-a("#mini-search-wrapper-sticky").outerHeight()},700)}),a(".stars .star").on("click",function(){var b=a(this).attr("title");a(".stars-selected").addClass("active"),a(".stars-count").text(b)})});;

