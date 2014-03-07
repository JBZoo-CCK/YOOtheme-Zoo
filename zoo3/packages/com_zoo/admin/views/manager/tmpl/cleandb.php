<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

    <div id="progressbar">
        <div class="progress-message">Cleaning database...</div>
    </div>

</div>

<style>
	.progress-message {
		float: left;
		margin-left: 50%;
		margin-top: 5px;
		font-weight: bold;
	}
	.ui-progressbar-value { background: lightblue; }
</style>

<script type="text/javascript">
	jQuery(function($) {

		var progressbar = $('#progressbar');
		progressbar.progressbar({ value: 1, max: <?php echo $this->steps + 1; ?> });

		step('index.php?option=com_zoo&controller=manager&format=raw&task=cleandbstep');

        function step(url) {

            $.getJSON(url)
                .success(function(data) {

                    if (data.error) {
                        progressbar.before($('<div class="alert alert-error">'+data.error+'</span>'));
                    }

                    if (data.message) {
                        $('.progress-message', progressbar).text(data.message);
                    }

                    if (data.step) {
                        progressbar.progressbar('value', data.step);
                    }

                    if (data.redirect) {
                        step(data.redirect);
                    }

                    if (data.forward) {
						progressbar.find('.ui-progressbar-value').css('background', 'lightgreen');
						window.setTimeout(function() {
							window.location.replace(data.forward);
						}, 5000);
                        ;
                    }

                })
                .error(function(result) {
					progressbar.find('.ui-progressbar-value').css('background', '#FF0000');
                    progressbar.before($('<div class="alert alert-error">'+result.responseText+'<br/ >Ooops. Something went wrong.</span>'));
                });
        }

    });
</script>

<?php echo ZOO_COPYRIGHT;