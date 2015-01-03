<?php

/**
 * Upload functionality of Uploader_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

session_start();

if (!isset($_SESSION['uploader_runtimes'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$type = isset($_GET['uploader_type'])
    && isset($_SESSION['uploader_folder'][$_GET['uploader_type']])
    ? $_GET['uploader_type']
    : 'images';
$subdir = !isset($_GET['uploader_subdir'])
    ? ''
    : preg_replace(
        '/\.\.[\/\\\\]?/', '',
        get_magic_quotes_gpc()
        ? stripslashes($_GET['uploader_subdir'])
        : $_GET['uploader_subdir']
    );
$subdir = is_dir($_SESSION['uploader_folder'][$type] . $subdir)
    ? $subdir
    : '';
$allowedSizes = array('small', 'medium', 'large', 'custom');
$resize = isset($_GET['uploader_resize'])
    && in_array($_GET['uploader_resize'], $allowedSizes)
    ? $_GET['uploader_resize']
    : '';
foreach (array('width', 'height', 'quality') as $name) {
    if ($resize == 'custom' && !empty($_GET['uploader_' . $name])
        && ctype_digit($_GET['uploader_' . $name])
    ) {
        $$name = $_GET['uploader_' . $name];
    }
}

// @codingStandardsIgnoreStart
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>title</title>
    <link rel="stylesheet" type="text/css" href="lib/jquery.plupload.queue/css/jquery.plupload.queue.css">
    <script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
    <script type="text/javascript" src="lib/plupload.full.js"></script>
<?php if (file_exists('lib/i18n/'.$_SESSION['uploader_lang'].'.js')) {?>
    <script type="text/javascript" src="lib/i18n/<?php echo $_SESSION['uploader_lang']?>.js"></script>
<?php }?>
    <script type="text/javascript" src="lib/jquery.plupload.queue/jquery.plupload.queue.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
    jQuery(function() {
	jQuery("#uploader").pluploadQueue({
	    runtimes : '<?php echo $_SESSION['uploader_runtimes']?>',
	    url : '../../?function=uploader_upload&type=<?php echo $type?>&subdir=<?php echo urlencode($subdir)?>',
	    max_file_size : '<?php echo $_SESSION['uploader_max_size']?>',
	    <?php echo $_SESSION['uploader_chunking']?>
<?php if (isset($width) && isset($height) && isset($quality)) {?>
	    resize : {
		width : <?php echo $width?>,
		height: <?php echo $height?>,
		quality: <?php echo $quality, "\n"?>
	    },
<?php } elseif ($resize != '') {?>
	    resize : {
		width : <?php echo $_SESSION['uploader_resize'][$resize]['width']?>,
		height : <?php echo $_SESSION['uploader_resize'][$resize]['height']?>,
		quality : <?php echo $_SESSION['uploader_resize'][$resize]['quality'], "\n"?>
	    },
<?php }?>
	    filters : [{
		title : '<?php echo $_SESSION['uploader_title'][$type]?>',
		extensions : '<?php echo $_SESSION['uploader_exts'][$type]?>'
	    }],
	    flash_swf_url : 'lib/plupload.flash.swf',
	    silverlight_xap_url : 'lib/plupload.silverlight.xap',
	    rename: true,
	    multiple_queues: true,
	    dragdrop: true
	});

	jQuery('form').submit(function(e) {
	    var uploader = jQuery('#uploader').pluploadQueue();
	    if (uploader.files.length > 0) {
		uploader.bind('StateChanged', function() {
		    if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
			jQuery('form')[0].submit();
		    }
		});
		uploader.start();
	    } else {
		alert('You must queue at least one file.');
	    }
	    return false;
	});
    });
    /* ]]> */
    </script>
</head>
<body>
    <form method="POST" action="#">
	<div id="uploader">
	    <img src="images/loading.gif" alt="loading &hellip;" style="display:none">
	    <script type="text/javascript">
		jQuery('#uploader img').show()
	    </script>
	    <noscript>
		<?php echo $_SESSION['uploader_message']['no_js']?>
	    </noscript>
	</div>
    </form>
</body>
</html>

<?
// @codingStandardsIgnoreEnd
?>
