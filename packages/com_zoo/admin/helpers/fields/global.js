/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function(a){a(".field .global").each(function(){var c=a(this).children("input:checkbox:first"),b=a(this).children("div.input:first");b.find("input, select").each(function(){a(this).data("name",a(this).attr("name"))});c.bind("change",function(){c.is(":checked")?b.hide():b.slideDown(200);b.find("input, select").each(function(){a(this).attr("name",c.is(":checked")?"":a(this).data("name"))})}).trigger("change")})});
