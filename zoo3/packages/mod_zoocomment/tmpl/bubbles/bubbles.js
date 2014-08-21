/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only */

jQuery(function($){$(".zoo-comments-bubbles").each(function(i){$.matchHeight(i+"-content",$(this).find(".match-height")).match();$.matchHeight(i+"-article",$(this).find("article")).match()})});