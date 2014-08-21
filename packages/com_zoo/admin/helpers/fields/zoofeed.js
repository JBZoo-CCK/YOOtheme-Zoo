/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function($){$("div.zoo-feed").each(function(){var input=$(this).find("div.input");var radios=$(this).find("input:radio");if(radios.first().is(":checked"))input.hide();radios.bind("change",function(){input.slideToggle()})})});