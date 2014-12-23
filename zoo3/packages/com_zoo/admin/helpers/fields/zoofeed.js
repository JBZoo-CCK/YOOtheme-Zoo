/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function(i){i("div.zoo-feed").each(function(){var n=i(this).find("div.input"),d=i(this).find("input:radio");d.first().is(":checked")&&n.hide(),d.bind("change",function(){n.slideToggle()})})});