"use strict";define(["jquery"],function(e){return function(t,i){var n=e(".jl-count").children().html();e(".jl-submit-search").on("click",function(){var t=e(".jl-key-word").val().trim(),i=e("#js-submit");i.find('input[name="search"]').val(t);var n=parseInt(e(".jl-search-type").children(".jl-active").data("type"));1===n?i.find('input[name="search_type"]').prop("disabled",!1).val("hot_brand"):2===n?i.find('input[name="search_type"]').prop("disabled",!1).val("hot_pSign"):3===n?i.find('input[name="search_type"]').prop("disabled",!1).val("hot_function"):i.find('input[name="search_type"]').prop("disabled",!1).val("hot_pSign"),i.trigger("submit")}),e(".jl-key-word").focus(function(){e(document).keydown(function(t){13===t.keyCode&&e(".jl-submit-search").trigger("click")})}),i?"hot_pSign"==i.search_type||("hot_function"==i.search_type?e(".hot_function").show(100):e(".hot_brand").show(100)):e(".hot_pSign").show(100),e(".jl-search-type li").click(function(){e(this).addClass("jl-active").siblings().removeClass("jl-active");var t=e(this).attr("data-type");e(1==t?".hot_brand":2==t?".hot_pSign":".hot_function").show().siblings("ul").hide()}),e(window).scroll(function(t){200<=e(window).scrollTop()?e(".js-search-nav").fadeIn("fast"):e(".js-search-nav").fadeOut("fast")}),e(".jl-submit-float").on("click",function(){var t=e(".jl-key-float").val();e("#js-submit input[name=search]").val(t),e("#js-submit").trigger("submit")}),e(".jl-key-float").focus(function(){e(document).keydown(function(t){13===t.keyCode&&e(".jl-submit-float").trigger("click")})});var a=e("#js-submit input[name=brand_id]");e(".jl-hot-search a,.jl-brand a").on("click",function(){var t=e(this).parent().attr("data-id");a.val(t),e("#js-submit").trigger("submit")}),e(".jl-sidebar a").on("click",function(){var t=e(this).attr("data-id");e("input[name=cate_id]").val(t),e("#js-submit").trigger("submit")}),0==n&&(e(".jl-has-product").css("display","none"),e(".jl-no-product").css("display","block"))}});