/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function(a){a("div.zoo-feed").each(function(){var b=a(this).find("div.input"),c=a(this).find("input:radio");c.first().is(":checked")&&b.hide();c.bind("change",function(){b.slideToggle()})})});
