<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Filesystem Helper. Deals with files and directories
 *
 * @package Framework.Helpers
 */
class FilesystemHelper extends AppHelper {

	/**
	 * Get a file size with a suffix (B, KB, MB, etc)
	 *
	 * @param int $bytes The number of bytes
	 *
	 * @return string The file size with a suffix
	 *
	 * @since 1.0.0
	 */
	public function formatFilesize($bytes) {
		$exp    = 0;
		$value  = 0;
		$symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if ($bytes > 0) {
			$exp   = floor( log($bytes)/log(1024));
			$value = ($bytes/pow(1024,floor($exp)));
		}

		return sprintf('%.2f '.$symbol[$exp], $value);
	}

	/**
	 * Output a file to the browser
	 *
	 * @param string $file The file to output
	 *
	 * @since 1.0.0
	 */
	public function output($file) {
		@error_reporting(E_ERROR);

		$name = basename($file);
		$type = $this->getContentType($name);
		$size = @filesize($file);
		$mod  = date('r', filemtime($file));

		while (@ob_end_clean());

		// required for IE, otherwise Content-disposition is ignored
		if (ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		// set header
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
		header('Content-Type: '.$type);
		header('Content-Disposition: attachment;'
			  .' filename="'.$name.'";'
			  .' modification-date="'.$mod.'";'
			  .' size='.$size.';');
        header("Content-Length: ".$size);

		// set_time_limit doesn't work in safe mode
        if (!ini_get('safe_mode')) {
		    @set_time_limit(0);
        }

		// output file
		$handle = fopen($file, 'rb');
		fpassthru($handle);
		fclose($handle);
	}

	/**
	 * Get a list of directories from the given directory
	 *
	 * @param string $path The path of the directory
	 * @param string $prefix A prefix to prepend
	 * @param string $filter A regex used to filter directories
	 * @param boolean $recursive If the search should be recursive (default: true)
	 *
	 * @return array The list of subdirectories
	 *
	 * @since 1.0.0
	 */
	public function readDirectory($path, $prefix = '', $filter = false, $recursive = true) {

		$dirs   = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		if (is_readable($path) && is_dir($path) && $handle = @opendir($path)) {
			while (false !== ($file = readdir($handle))) {

				// continue if ignore match
				if (in_array($file, $ignore)) {
					continue;
				}

	            if (is_dir($path.'/'.$file)) {

					// continue if not recursive
					if (!$recursive) {
						continue;
					}

					// continue if no regex filter match
					if ($filter && !preg_match($filter, $file)) {
						continue;
					}

					// read subdirectory
					$dirs[] = $prefix.$file;
	            	$dirs   = array_merge($dirs, $this->readDirectory($path.'/'.$file, $prefix.$file.'/', $filter, $recursive));

				}
		    }
		    closedir($handle);
		}

		return $dirs;
	}

	/**
	 * Get a list of files in the given directory
	 *
	 * @param string $path The path to search in
	 * @param string $prefix A prefix to prepend
	 * @param string $filter A regex to filter the files
	 * @param boolean $recursive If the search should be recursive (default: true)
	 *
	 * @return array The list of files
	 *
	 * @since 1.0.0
	 */
	public function readDirectoryFiles($path, $prefix = '', $filter = false, $recursive = true) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		if (is_readable($path) && is_dir($path) && $handle = @opendir($path)) {
			while (false !== ($file = readdir($handle))) {

				// continue if ignore match
				if (in_array($file, $ignore)) {
					continue;
				}

	            if (is_dir($path.'/'.$file)) {

					// continue if not recursive
					if (!$recursive) {
						continue;
					}

					// read subdirectory
	            	$files = array_merge($files, $this->readDirectoryFiles($path.'/'.$file, $prefix.$file.'/', $filter, $recursive));

				} else {

					// continue if no regex filter match
					if ($filter && !preg_match($filter, $file)) {
						continue;
					}

					$files[] = $prefix.$file;
	            }
		    }
		    closedir($handle);
		}

		return $files;
	}

	/**
	 * Get the file extension
	 *
	 * @param string $filename The file name
	 *
	 * @return string The file extension
	 *
	 * @since 1.0.0
	 */
	public function getExtension($filename) {
		$mimes = $this->getMimeMapping();
		$file  = pathinfo($filename);
		$ext   = isset($file['extension']) ? $file['extension'] : null;

		if ($ext) {

			// check extensions content type (with dot, like tar.gz)
			if (($pos = strrpos($file['filename'], '.')) !== false) {
				$ext2 = strtolower(substr($file['filename'], $pos + 1).'.'.$ext);
				if (array_key_exists($ext2, $mimes)) {
					return $ext2;
				}
			}

			// check extensions content type
			$ext = strtolower($ext);
			if (array_key_exists(strtolower($ext), $mimes)) {
				return $ext;
			}
		}

		return null;
	}

	/**
	 * Get the content type of a file
	 *
	 * @param string $filename The file name
	 *
	 * @return string The content type
	 *
	 * @since 1.0.0
	 */
	public function getContentType($filename) {
		$mimes = $this->getMimeMapping();
		$file  = pathinfo($filename);
		$ext   = $file['extension'];

		if ($ext) {

			// check extensions content type (with dot, like tar.gz)
			if (($pos = strrpos($file['filename'], '.')) !== false) {
				$ext2 = strtolower(substr($file['filename'], $pos + 1).'.'.$ext);
				if (array_key_exists($ext2, $mimes)) {
					return array_shift($mimes[$ext2]);
				}
			}

			// check extensions content type
			$ext = strtolower($ext);
			if (array_key_exists(strtolower($ext), $mimes)) {
				return array_shift($mimes[$ext]);
			}
		}

		return 'application/octet-stream';
	}

	/**
	 * Get the mapping from extension to mime type
	 *
	 * @return array The associative array of extension => mime type mapping
	 *
	 * @since 1.0.0
	 */
	public static function getMimeMapping() {

		$mimes = array();
		$mimes['3ds'][] = 'image/x-3ds';
		$mimes['BLEND'][] = 'application/x-blender';
		$mimes['C'][] = 'text/x-c++src';
		$mimes['CSSL'][] = 'text/css';
		$mimes['NSV'][] = 'video/x-nsv';
		$mimes['XM'][] = 'audio/x-mod';
		$mimes['Z'][] = 'application/x-compress';
		$mimes['a'][] = 'application/x-archive';
		$mimes['abw'][] = 'application/x-abiword';
		$mimes['abw.gz'][] = 'application/x-abiword';
		$mimes['ac3'][] = 'audio/ac3';
		$mimes['adb'][] = 'text/x-adasrc';
		$mimes['ads'][] = 'text/x-adasrc';
		$mimes['afm'][] = 'application/x-font-afm';
		$mimes['ag'][] = 'image/x-applix-graphics';
		$mimes['ai'][] = 'application/illustrator';
		$mimes['aif'][] = 'audio/x-aiff';
		$mimes['aifc'][] = 'audio/x-aiff';
		$mimes['aiff'][] = 'audio/x-aiff';
		$mimes['al'][] = 'application/x-perl';
		$mimes['arj'][] = 'application/x-arj';
		$mimes['as'][] = 'application/x-applix-spreadsheet';
		$mimes['asc'][] = 'text/plain';
		$mimes['asf'][] = 'video/x-ms-asf';
		$mimes['asp'][] = 'application/x-asp';
		$mimes['asx'][] = 'video/x-ms-asf';
		$mimes['au'][] = 'audio/basic';
		$mimes['avi'][] = 'video/x-msvideo';
		$mimes['avi'][] = 'video/avi';  // IE
		$mimes['aw'][] = 'application/x-applix-word';
		$mimes['bak'][] = 'application/x-trash';
		$mimes['bcpio'][] = 'application/x-bcpio';
		$mimes['bdf'][] = 'application/x-font-bdf';
		$mimes['bib'][] = 'text/x-bibtex';
		$mimes['bin'][] = 'application/octet-stream';
		$mimes['blend'][] = 'application/x-blender';
		$mimes['blender'][] = 'application/x-blender';
		$mimes['bmp'][] = 'image/bmp';
		$mimes['bz'][] = 'application/x-bzip';
		$mimes['bz2'][] = 'application/x-bzip';
		$mimes['c'][] = 'text/x-csrc';
		$mimes['c++'][] = 'text/x-c++src';
		$mimes['cc'][] = 'text/x-c++src';
		$mimes['cdf'][] = 'application/x-netcdf';
		$mimes['cdr'][] = 'application/vnd.corel-draw';
		$mimes['cer'][] = 'application/x-x509-ca-cert';
		$mimes['cert'][] = 'application/x-x509-ca-cert';
		$mimes['cgi'][] = 'application/x-cgi';
		$mimes['cgm'][] = 'image/cgm';
		$mimes['chrt'][] = 'application/x-kchart';
		$mimes['class'][] = 'application/x-java';
		$mimes['cls'][] = 'text/x-tex';
		$mimes['cpio'][] = 'application/x-cpio';
		$mimes['cpio.gz'][] = 'application/x-cpio-compressed';
		$mimes['cpp'][] = 'text/x-c++src';
		$mimes['cpt'][] = 'application/mac-compactpro';
		$mimes['crt'][] = 'application/x-x509-ca-cert';
		$mimes['cs'][] = 'text/x-csharp';
		$mimes['csh'][] = 'application/x-shellscript';
		$mimes['css'][] = 'text/css';
		$mimes['csv'][] = 'text/x-comma-separated-values';
		$mimes['cur'][] = 'image/x-win-bitmap';
		$mimes['cxx'][] = 'text/x-c++src';
		$mimes['dat'][] = 'video/mpeg';
		$mimes['dbf'][] = 'application/x-dbase';
		$mimes['dc'][] = 'application/x-dc-rom';
		$mimes['dcl'][] = 'text/x-dcl';
		$mimes['dcm'][] = 'image/x-dcm';
		$mimes['dcr'][] = 'application/x-director';
		$mimes['dd2'][] = 'application/vnd.oma.dd2+xml';
		$mimes['deb'][] = 'application/x-deb';
		$mimes['der'][] = 'application/x-x509-ca-cert';
		$mimes['desktop'][] = 'application/x-desktop';
		$mimes['dia'][] = 'application/x-dia-diagram';
		$mimes['diff'][] = 'text/x-patch';
		$mimes['dir'][] = 'application/x-director';
		$mimes['djv'][] = 'image/vnd.djvu';
		$mimes['djvu'][] = 'image/vnd.djvu';
		$mimes['dll'][] = 'application/octet-stream';
		$mimes['dms'][] = 'application/octet-stream';
		$mimes['doc'][] = 'application/msword';
		$mimes['dsl'][] = 'text/x-dsl';
		$mimes['dtd'][] = 'text/x-dtd';
		$mimes['dvi'][] = 'application/x-dvi';
		$mimes['dwg'][] = 'image/vnd.dwg';
		$mimes['dxf'][] = 'image/vnd.dxf';
		$mimes['dxr'][] = 'application/x-director';
		$mimes['egon'][] = 'application/x-egon';
		$mimes['el'][] = 'text/x-emacs-lisp';
		$mimes['eps'][] = 'image/x-eps';
		$mimes['epsf'][] = 'image/x-eps';
		$mimes['epsi'][] = 'image/x-eps';
		$mimes['etheme'][] = 'application/x-e-theme';
		$mimes['etx'][] = 'text/x-setext';
		$mimes['exe'][] = 'application/x-executable';
		$mimes['exe'][] = 'application/x-msdownload';  // IE
		$mimes['ez'][] = 'application/andrew-inset';
		$mimes['f'][] = 'text/x-fortran';
		$mimes['fig'][] = 'image/x-xfig';
		$mimes['fits'][] = 'image/x-fits';
		$mimes['flac'][] = 'audio/x-flac';
		$mimes['flc'][] = 'video/x-flic';
		$mimes['fli'][] = 'video/x-flic';
		$mimes['flv'][] = 'video/x-flv';
		$mimes['flw'][] = 'application/x-kivio';
		$mimes['fo'][] = 'text/x-xslfo';
		$mimes['g3'][] = 'image/fax-g3';
		$mimes['gb'][] = 'application/x-gameboy-rom';
		$mimes['gcrd'][] = 'text/x-vcard';
		$mimes['gen'][] = 'application/x-genesis-rom';
		$mimes['gg'][] = 'application/x-sms-rom';
		$mimes['gif'][] = 'image/gif';
		$mimes['glade'][] = 'application/x-glade';
		$mimes['gmo'][] = 'application/x-gettext-translation';
		$mimes['gnc'][] = 'application/x-gnucash';
		$mimes['gnucash'][] = 'application/x-gnucash';
		$mimes['gnumeric'][] = 'application/x-gnumeric';
		$mimes['gra'][] = 'application/x-graphite';
		$mimes['gsf'][] = 'application/x-font-type1';
		$mimes['gtar'][] = 'application/x-gtar';
		$mimes['gz'][] = 'application/gzip';
		$mimes['gz'][] = 'application/x-gzip';
		$mimes['gz'][] = 'application/x-gzip-compressed'; // IE
		$mimes['h'][] = 'text/x-chdr';
		$mimes['h++'][] = 'text/x-chdr';
		$mimes['hdf'][] = 'application/x-hdf';
		$mimes['hh'][] = 'text/x-c++hdr';
		$mimes['hp'][] = 'text/x-chdr';
		$mimes['hpgl'][] = 'application/vnd.hp-hpgl';
		$mimes['hqx'][] = 'application/mac-binhex40';
		$mimes['hs'][] = 'text/x-haskell';
		$mimes['htm'][] = 'text/html';
		$mimes['html'][] = 'text/html';
		$mimes['icb'][] = 'image/x-icb';
		$mimes['ice'][] = 'x-conference/x-cooltalk';
		$mimes['ico'][] = 'image/x-ico';
		$mimes['ics'][] = 'text/calendar';
		$mimes['idl'][] = 'text/x-idl';
		$mimes['ief'][] = 'image/ief';
		$mimes['ifb'][] = 'text/calendar';
		$mimes['iff'][] = 'image/x-iff';
		$mimes['iges'][] = 'model/iges';
		$mimes['igs'][] = 'model/iges';
		$mimes['igs'][] = 'application/iges';
		$mimes['ilbm'][] = 'image/x-ilbm';
		$mimes['iso'][] = 'application/x-cd-image';
		$mimes['it'][] = 'audio/x-it';
		$mimes['jar'][] = 'application/x-jar';
		$mimes['java'][] = 'text/x-java';
		$mimes['jng'][] = 'image/x-jng';
		$mimes['jp2'][] = 'image/jpeg2000';
		$mimes['jpg'][] = 'image/jpeg';
		$mimes['jpg'][] = 'image/pjpeg';  // IE
		$mimes['jpe'][] = 'image/jpeg';
		$mimes['jpeg'][] = 'image/jpeg';
		$mimes['jpeg'][] = 'image/pjpeg';  // IE
		$mimes['jpr'][] = 'application/x-jbuilder-project';
		$mimes['jpx'][] = 'application/x-jbuilder-project';
		$mimes['js'][] = 'application/x-javascript';
		$mimes['kar'][] = 'audio/midi';
		$mimes['karbon'][] = 'application/x-karbon';
		$mimes['kdelnk'][] = 'application/x-desktop';
		$mimes['kfo'][] = 'application/x-kformula';
		$mimes['kil'][] = 'application/x-killustrator';
		$mimes['kon'][] = 'application/x-kontour';
		$mimes['kpm'][] = 'application/x-kpovmodeler';
		$mimes['kpr'][] = 'application/x-kpresenter';
		$mimes['kpt'][] = 'application/x-kpresenter';
		$mimes['kra'][] = 'application/x-krita';
		$mimes['ksp'][] = 'application/x-kspread';
		$mimes['kud'][] = 'application/x-kugar';
		$mimes['kwd'][] = 'application/x-kword';
		$mimes['kwt'][] = 'application/x-kword';
		$mimes['la'][] = 'application/x-shared-library-la';
		$mimes['latex'][] = 'application/x-latex';
		$mimes['lha'][] = 'application/x-lha';
		$mimes['lhs'][] = 'text/x-literate-haskell';
		$mimes['lhz'][] = 'application/x-lhz';
		$mimes['log'][] = 'text/x-log';
		$mimes['ltx'][] = 'text/x-tex';
		$mimes['lwo'][] = 'image/x-lwo';
		$mimes['lwob'][] = 'image/x-lwo';
		$mimes['lws'][] = 'image/x-lws';
		$mimes['lyx'][] = 'application/x-lyx';
		$mimes['lzh'][] = 'application/x-lha';
		$mimes['lzo'][] = 'application/x-lzop';
		$mimes['m'][] = 'text/x-objcsrc';
		$mimes['m15'][] = 'audio/x-mod';
		$mimes['m3u'][] = 'audio/x-mpegurl';
        $mimes['m4v'][] = 'video/x-m4v';
		$mimes['man'][] = 'application/x-troff-man';
		$mimes['md'][] = 'application/x-genesis-rom';
		$mimes['me'][] = 'text/x-troff-me';
		$mimes['mesh'][] = 'model/mesh';
		$mimes['mgp'][] = 'application/x-magicpoint';
		$mimes['mid'][] = 'audio/midi';
		$mimes['mid'][] = 'audio/mid'; // IE
		$mimes['midi'][] = 'audio/midi';
		$mimes['mif'][] = 'application/x-mif';
		$mimes['mkv'][] = 'application/x-matroska';
		$mimes['mm'][] = 'text/x-troff-mm';
		$mimes['mml'][] = 'text/mathml';
		$mimes['mng'][] = 'video/x-mng';
		$mimes['moc'][] = 'text/x-moc';
		$mimes['mod'][] = 'audio/x-mod';
		$mimes['moov'][] = 'video/quicktime';
		$mimes['mov'][] = 'video/quicktime';
		$mimes['movie'][] = 'video/x-sgi-movie';
		$mimes['mp2'][] = 'video/mpeg';
		$mimes['mp3'][] = 'audio/mpeg';
		$mimes['mp4'][] = 'video/mp4';
		$mimes['mpe'][] = 'video/mpeg';
		$mimes['mpeg'][] = 'video/mpeg';
		$mimes['mpg'][] = 'video/mpeg';
		$mimes['mpga'][] = 'audio/mpeg';
		$mimes['ms'][] = 'text/x-troff-ms';
		$mimes['msh'][] = 'model/mesh';
		$mimes['msod'][] = 'image/x-msod';
		$mimes['msx'][] = 'application/x-msx-rom';
		$mimes['mtm'][] = 'audio/x-mod';
		$mimes['mxu'][] = 'video/vnd.mpegurl';
		$mimes['n64'][] = 'application/x-n64-rom';
		$mimes['nc'][] = 'application/x-netcdf';
		$mimes['nes'][] = 'application/x-nes-rom';
		$mimes['nsv'][] = 'video/x-nsv';
		$mimes['o'][] = 'application/x-object';
		$mimes['obj'][] = 'application/x-tgif';
		$mimes['oda'][] = 'application/oda';
		$mimes['odb'][] = 'application/vnd.oasis.opendocument.database';
		$mimes['odc'][] = 'application/vnd.oasis.opendocument.chart';
		$mimes['odf'][] = 'application/vnd.oasis.opendocument.formula';
		$mimes['odg'][] = 'application/vnd.oasis.opendocument.graphics';
		$mimes['odi'][] = 'application/vnd.oasis.opendocument.image';
		$mimes['odm'][] = 'application/vnd.oasis.opendocument.text-master';
		$mimes['odp'][] = 'application/vnd.oasis.opendocument.presentation';
		$mimes['ods'][] = 'application/vnd.oasis.opendocument.spreadsheet';
		$mimes['odt'][] = 'application/vnd.oasis.opendocument.text';
		$mimes['ogg'][] = 'application/ogg';
		$mimes['ogx'][] = 'application/ogg';
		$mimes['oga'][] = 'audio/ogg';
		$mimes['ogv'][] = 'video/ogg';
		$mimes['old'][] = 'application/x-trash';
		$mimes['oleo'][] = 'application/x-oleo';
		$mimes['otg'][] = 'application/vnd.oasis.opendocument.graphics-template';
		$mimes['oth'][] = 'application/vnd.oasis.opendocument.text-web';
		$mimes['otp'][] = 'application/vnd.oasis.opendocument.presentation-template';
		$mimes['ots'][] = 'application/vnd.oasis.opendocument.spreadsheet-template';
		$mimes['ott'][] = 'application/vnd.oasis.opendocument.text-template';
		$mimes['p'][] = 'text/x-pascal';
		$mimes['p12'][] = 'application/x-pkcs12';
		$mimes['p7s'][] = 'application/pkcs7-signature';
		$mimes['pas'][] = 'text/x-pascal';
		$mimes['patch'][] = 'text/x-patch';
		$mimes['pbm'][] = 'image/x-portable-bitmap';
		$mimes['pcd'][] = 'image/x-photo-cd';
		$mimes['pcf'][] = 'application/x-font-pcf';
		$mimes['pcf.Z'][] = 'application/x-font-type1';
		$mimes['pcl'][] = 'application/vnd.hp-pcl';
		$mimes['pdb'][] = 'application/vnd.palm';
		$mimes['pdf'][] = 'application/pdf';
		$mimes['pdf'][] = 'application/x-pdf';
		$mimes['pem'][] = 'application/x-x509-ca-cert';
		$mimes['perl'][] = 'application/x-perl';
		$mimes['pfa'][] = 'application/x-font-type1';
		$mimes['pfb'][] = 'application/x-font-type1';
		$mimes['pfx'][] = 'application/x-pkcs12';
		$mimes['pgm'][] = 'image/x-portable-graymap';
		$mimes['pgn'][] = 'application/x-chess-pgn';
		$mimes['pgp'][] = 'application/pgp';
		$mimes['php'][] = 'application/x-php';
		$mimes['php3'][] = 'application/x-php';
		$mimes['php4'][] = 'application/x-php';
		$mimes['pict'][] = 'image/x-pict';
		$mimes['pict1'][] = 'image/x-pict';
		$mimes['pict2'][] = 'image/x-pict';
		$mimes['pl'][] = 'application/x-perl';
		$mimes['pls'][] = 'audio/x-scpls';
		$mimes['pm'][] = 'application/x-perl';
		$mimes['png'][] = 'image/png';
		$mimes['png'][] = 'image/x-png';  // IE
		$mimes['pnm'][] = 'image/x-portable-anymap';
		$mimes['po'][] = 'text/x-gettext-translation';
		$mimes['pot'][] = 'application/vnd.ms-powerpoint';
		$mimes['ppm'][] = 'image/x-portable-pixmap';
		$mimes['pps'][] = 'application/vnd.ms-powerpoint';
		$mimes['ppt'][] = 'application/vnd.ms-powerpoint';
		$mimes['ppz'][] = 'application/vnd.ms-powerpoint';
		$mimes['ps'][] = 'application/postscript';
		$mimes['ps.gz'][] = 'application/x-gzpostscript';
		$mimes['psd'][] = 'image/x-psd';
		$mimes['psf'][] = 'application/x-font-linux-psf';
		$mimes['psid'][] = 'audio/prs.sid';
		$mimes['pw'][] = 'application/x-pw';
		$mimes['py'][] = 'application/x-python';
		$mimes['pyc'][] = 'application/x-python-bytecode';
		$mimes['pyo'][] = 'application/x-python-bytecode';
		$mimes['qif'][] = 'application/x-qw';
		$mimes['qt'][] = 'video/quicktime';
		$mimes['qtvr'][] = 'video/quicktime';
		$mimes['ra'][] = 'audio/x-pn-realaudio';
		$mimes['ram'][] = 'audio/x-pn-realaudio';
		$mimes['rar'][] = 'application/x-rar';
		$mimes['ras'][] = 'image/x-cmu-raster';
		$mimes['rdf'][] = 'text/rdf';
		$mimes['rej'][] = 'application/x-reject';
		$mimes['rgb'][] = 'image/x-rgb';
		$mimes['rle'][] = 'image/rle';
		$mimes['rm'][] = 'audio/x-pn-realaudio';
		$mimes['roff'][] = 'application/x-troff';
		$mimes['rpm'][] = 'application/x-rpm';
		$mimes['rss'][] = 'text/rss';
		$mimes['rtf'][] = 'application/rtf';
		$mimes['rtx'][] = 'text/richtext';
		$mimes['s3m'][] = 'audio/x-s3m';
		$mimes['sam'][] = 'application/x-amipro';
		$mimes['scm'][] = 'text/x-scheme';
		$mimes['sda'][] = 'application/vnd.stardivision.draw';
		$mimes['sdc'][] = 'application/vnd.stardivision.calc';
		$mimes['sdd'][] = 'application/vnd.stardivision.impress';
		$mimes['sdp'][] = 'application/vnd.stardivision.impress';
		$mimes['sds'][] = 'application/vnd.stardivision.chart';
		$mimes['sdw'][] = 'application/vnd.stardivision.writer';
		$mimes['sgi'][] = 'image/x-sgi';
		$mimes['sgl'][] = 'application/vnd.stardivision.writer';
		$mimes['sgm'][] = 'text/sgml';
		$mimes['sgml'][] = 'text/sgml';
		$mimes['sh'][] = 'application/x-shellscript';
		$mimes['shar'][] = 'application/x-shar';
		$mimes['shtml'][] = 'text/html';
		$mimes['siag'][] = 'application/x-siag';
		$mimes['sid'][] = 'audio/prs.sid';
		$mimes['sik'][] = 'application/x-trash';
		$mimes['silo'][] = 'model/mesh';
		$mimes['sit'][] = 'application/x-stuffit';
		$mimes['skd'][] = 'application/x-koan';
		$mimes['skm'][] = 'application/x-koan';
		$mimes['skp'][] = 'application/x-koan';
		$mimes['skt'][] = 'application/x-koan';
		$mimes['slk'][] = 'text/spreadsheet';
		$mimes['smd'][] = 'application/vnd.stardivision.mail';
		$mimes['smf'][] = 'application/vnd.stardivision.math';
		$mimes['smi'][] = 'application/smil';
		$mimes['smil'][] = 'application/smil';
		$mimes['sml'][] = 'application/smil';
		$mimes['sms'][] = 'application/x-sms-rom';
		$mimes['snd'][] = 'audio/basic';
		$mimes['so'][] = 'application/x-sharedlib';
		$mimes['spd'][] = 'application/x-font-speedo';
		$mimes['spl'][] = 'application/x-futuresplash';
		$mimes['sql'][] = 'text/x-sql';
		$mimes['src'][] = 'application/x-wais-source';
		$mimes['stc'][] = 'application/vnd.sun.xml.calc.template';
		$mimes['std'][] = 'application/vnd.sun.xml.draw.template';
		$mimes['sti'][] = 'application/vnd.sun.xml.impress.template';
		$mimes['stm'][] = 'audio/x-stm';
		$mimes['stw'][] = 'application/vnd.sun.xml.writer.template';
		$mimes['sty'][] = 'text/x-tex';
		$mimes['sun'][] = 'image/x-sun-raster';
		$mimes['sv4cpio'][] = 'application/x-sv4cpio';
		$mimes['sv4crc'][] = 'application/x-sv4crc';
		$mimes['svg'][] = 'image/svg+xml';
		$mimes['swf'][] = 'application/x-shockwave-flash';
		$mimes['sxc'][] = 'application/vnd.sun.xml.calc';
		$mimes['sxd'][] = 'application/vnd.sun.xml.draw';
		$mimes['sxg'][] = 'application/vnd.sun.xml.writer.global';
		$mimes['sxi'][] = 'application/vnd.sun.xml.impress';
		$mimes['sxm'][] = 'application/vnd.sun.xml.math';
		$mimes['sxw'][] = 'application/vnd.sun.xml.writer';
		$mimes['sylk'][] = 'text/spreadsheet';
		$mimes['t'][] = 'application/x-troff';
		$mimes['tar'][] = 'application/x-tar';
		$mimes['tar.Z'][] = 'application/x-tarz';
		$mimes['tar.bz'][] = 'application/x-bzip-compressed-tar';
		$mimes['tar.bz2'][] = 'application/x-bzip-compressed-tar';
		$mimes['tar.gz'][] = 'application/x-compressed-tar';
		$mimes['tar.gz'][] = 'application/x-compressed'; // IE
		$mimes['tar.lzo'][] = 'application/x-tzo';
		$mimes['tcl'][] = 'text/x-tcl';
		$mimes['tex'][] = 'text/x-tex';
		$mimes['texi'][] = 'text/x-texinfo';
		$mimes['texinfo'][] = 'text/x-texinfo';
		$mimes['tga'][] = 'image/x-tga';
		$mimes['tgz'][] = 'application/x-compressed-tar';
		$mimes['theme'][] = 'application/x-theme';
		$mimes['tif'][] = 'image/tiff';
		$mimes['tiff'][] = 'image/tiff';
		$mimes['tk'][] = 'text/x-tcl';
		$mimes['torrent'][] = 'application/x-bittorrent';
		$mimes['tr'][] = 'application/x-troff';
		$mimes['ts'][] = 'application/x-linguist';
		$mimes['tsv'][] = 'text/tab-separated-values';
		$mimes['ttf'][] = 'application/x-font-ttf';
		$mimes['txt'][] = 'text/plain';
		$mimes['tzo'][] = 'application/x-tzo';
		$mimes['ui'][] = 'application/x-designer';
		$mimes['uil'][] = 'text/x-uil';
		$mimes['ult'][] = 'audio/x-mod';
		$mimes['uni'][] = 'audio/x-mod';
		$mimes['unity3d'][] = 'application/octet-stream';
		$mimes['uri'][] = 'text/x-uri';
		$mimes['url'][] = 'text/x-uri';
		$mimes['ustar'][] = 'application/x-ustar';
		$mimes['vcd'][] = 'application/x-cdlink';
		$mimes['vcf'][] = 'text/x-vcalendar';
		$mimes['vcs'][] = 'text/x-vcalendar';
		$mimes['vct'][] = 'text/x-vcard';
		$mimes['vfb'][] = 'text/calendar';
		$mimes['vob'][] = 'video/mpeg';
		$mimes['voc'][] = 'audio/x-voc';
		$mimes['vor'][] = 'application/vnd.stardivision.writer';
		$mimes['vrml'][] = 'model/vrml';
		$mimes['vsd'][] = 'application/vnd.visio';
		$mimes['wav'][] = 'audio/x-wav';
		$mimes['wav'][] = 'audio/wav'; // IE
		$mimes['wax'][] = 'audio/x-ms-wax';
		$mimes['wb1'][] = 'application/x-quattropro';
		$mimes['wb2'][] = 'application/x-quattropro';
		$mimes['wb3'][] = 'application/x-quattropro';
		$mimes['wbmp'][] = 'image/vnd.wap.wbmp';
		$mimes['wbxml'][] = 'application/vnd.wap.wbxml';
		$mimes['wk1'][] = 'application/vnd.lotus-1-2-3';
		$mimes['wk3'][] = 'application/vnd.lotus-1-2-3';
		$mimes['wk4'][] = 'application/vnd.lotus-1-2-3';
		$mimes['wks'][] = 'application/vnd.lotus-1-2-3';
		$mimes['wm'][] = 'video/x-ms-wm';
		$mimes['wma'][] = 'audio/x-ms-wma';
		$mimes['wmd'][] = 'application/x-ms-wmd';
		$mimes['wmf'][] = 'image/x-wmf';
		$mimes['wml'][] = 'text/vnd.wap.wml';
		$mimes['wmlc'][] = 'application/vnd.wap.wmlc';
		$mimes['wmls'][] = 'text/vnd.wap.wmlscript';
		$mimes['wmlsc'][] = 'application/vnd.wap.wmlscriptc';
		$mimes['wmv'][] = 'video/x-ms-wmv';
		$mimes['wmx'][] = 'video/x-ms-wmx';
		$mimes['wmz'][] = 'application/x-ms-wmz';
		$mimes['wpd'][] = 'application/wordperfect';
		$mimes['wpg'][] = 'application/x-wpg';
		$mimes['wri'][] = 'application/x-mswrite';
		$mimes['wrl'][] = 'model/vrml';
		$mimes['wvx'][] = 'video/x-ms-wvx';
		$mimes['xac'][] = 'application/x-gnucash';
		$mimes['xbel'][] = 'application/x-xbel';
		$mimes['xbm'][] = 'image/x-xbitmap';
		$mimes['xcf'][] = 'image/x-xcf';
		$mimes['xcf.bz2'][] = 'image/x-compressed-xcf';
		$mimes['xcf.gz'][] = 'image/x-compressed-xcf';
		$mimes['xht'][] = 'application/xhtml+xml';
		$mimes['xhtml'][] = 'application/xhtml+xml';
		$mimes['xi'][] = 'audio/x-xi';
		$mimes['xls'][] = 'application/vnd.ms-excel';
		$mimes['xla'][] = 'application/vnd.ms-excel';
		$mimes['xlc'][] = 'application/vnd.ms-excel';
		$mimes['xld'][] = 'application/vnd.ms-excel';
		$mimes['xll'][] = 'application/vnd.ms-excel';
		$mimes['xlm'][] = 'application/vnd.ms-excel';
		$mimes['xlt'][] = 'application/vnd.ms-excel';
		$mimes['xlw'][] = 'application/vnd.ms-excel';
		$mimes['xm'][] = 'audio/x-xm';
		$mimes['xml'][] = 'text/xml';
		$mimes['xml'][] = 'application/xml'; // IE
		$mimes['xpm'][] = 'image/x-xpixmap';
		$mimes['xsl'][] = 'text/x-xslt';
		$mimes['xslfo'][] = 'text/x-xslfo';
		$mimes['xslt'][] = 'text/x-xslt';
		$mimes['xwd'][] = 'image/x-xwindowdump';
		$mimes['xyz'][] = 'chemical/x-xyz';
		$mimes['zabw'][] = 'application/x-abiword';
		$mimes['zip'][] = 'application/zip';
		$mimes['zip'][] = 'application/x-zip';
		$mimes['zip'][] = 'application/x-zip-compressed'; // IE
		$mimes['zoo'][] = 'application/x-zoo';
		$mimes['123'][] = 'application/vnd.lotus-1-2-3';
		$mimes['669'][] = 'audio/x-mod';

		// office 2007
		$mimes['docm'][] = 'application/vnd.ms-word.document.macroEnabled.12';
		$mimes['docx'][] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		$mimes['dotm'][] = 'application/vnd.ms-word.template.macroEnabled.12';
		$mimes['dotx'][] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
		$mimes['potm'][] = 'application/vnd.ms-powerpoint.template.macroEnabled.12';
		$mimes['potx'][] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
		$mimes['ppam'][] = 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
		$mimes['ppsm'][] = 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
		$mimes['ppsx'][] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
		$mimes['pptm'][] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
		$mimes['pptx'][] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
		$mimes['xlam'][] = 'application/vnd.ms-excel.addin.macroEnabled.12';
		$mimes['xlsb'][] = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
		$mimes['xlsm'][] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
		$mimes['xlsx'][] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		$mimes['xltm'][] = 'application/vnd.ms-excel.template.macroEnabled.12';
		$mimes['xltx'][] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';

		return $mimes;
	}

}