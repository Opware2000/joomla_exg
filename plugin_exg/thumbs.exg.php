<?php
/**
 * @package		EXG - Easy eXtended Gallery - plugin for Joomla 3.x
 * @copyright 	Nicolas Ogier
 * @author 		Nicolas Ogier {@link http://www.nicolas-ogier.fr}
 * @version 	3-1.0	2013-05-01
 * @link 		http://www.nicolas-ogier.fr/exg/
 * @access 		public
 * @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
//defined('_JEXEC') or die('Restricted access');
function doThumb($string_img, $int_largeur, $int_hauteur) {
	/**
	 * Génération de la vignette
	 */
	$string_imagesource_path = DOSSIER_VIGNETTE.'/'.$string_img;
	$string_thumb_path = DOSSIER_VIGNETTE.'/images/thumbs/'.$int_largeur.'x'.$int_hauteur.'/';
	//echo($string_imagesource_path.' - '.$string_thumb_path);
	if(!is_dir($string_thumb_path))
		mkdir($string_thumb_path,0777, true);
	$string_thumb_path .=md5($string_imagesource_path).'.jpg';
	if(!file_exists($string_thumb_path)) {
		$phpI = imagecreatefromjpeg($string_imagesource_path);
		if(!$phpI) return false;
		$srcSize = getimagesize($string_imagesource_path);
		if($srcSize == '') return false;
		$destSize = array();
		list($srcSize[0],$srcSize[1],$destSize[0],$destSize[1]) = algoThumb($int_largeur, $int_hauteur, $srcSize);
		$tmp = imagecreatetruecolor($destSize[0], $destSize[1]);
		imageantialias($tmp, true);
		imagecopyresampled($tmp, $phpI, 0, 0, 0, 0, $destSize[0], $destSize[1], $srcSize[0], $srcSize[1]);
		imagejpeg($tmp, $string_thumb_path, 85);
		chmod($string_thumb_path,0777);
		imagedestroy($tmp);
	}
	return $string_thumb_path;
}

function algoThumb($int_largeur,$int_hauteur, $srcSize) {
	$bool_carre = ($int_largeur == $int_hauteur);
	if(!$bool_carre) {
		$srcRatio = $srcSize[0]/$srcSize[1];
		if($int_hauteur != 0) {$destRatio = $int_largeur / $int_hauteur; }
		else {$destRatio = 0;}
		if($destRatio > $srcRatio) {
			$destSize[1] = $int_hauteur;
			$destSize[0] = $int_hauteur * $srcRatio;
		} else {
			$destSize[0] = $int_largeur;
			$destSize[1] = $int_largeur / $srcRatio;
		}
	}else {
		$destSize[0] = $int_largeur;
		$destSize[1] = $int_hauteur;
		if($srcSize[0] >= $srcSize[1]) {
			$srcSize[0] = $srcSize[1] * ($destSize[0]/$destSize[1]);
			$destSize[1] = $destSize[0];
		} else {
			$srcSize[1] = $srcSize[0] * ($destSize[0]/$destSize[1]);
			$destSize[0] = $destSize[1];
		}
	}
	return array($srcSize[0],$srcSize[1], $destSize[0],$destSize[1]) ;
}

function sendToClient($string_thumb_path) {
//	ob_start();
	$string_extension = strtolower(end(explode('.', $string_thumb_path)));
	if(file_exists($string_thumb_path)) {
		$intSize = filesize($string_thumb_path);
//		ob_end_clean();
		
		$bolDownloadFromBegin = true;
		if (isset($_SERVER['HTTP_RANGE'])) {
			$offset = substr($_SERVER['HTTP_RANGE'],strlen("bytes="),-1);
		} else {$offset=0;}
		$intSize = filesize($string_thumb_path)-$offset;
		if($offset > 0) {
			$bolDownloadFromBegin = false;
		}
		header("Pragma: public");
		header("Cache-Control: must-revalidate");
		$expires_time = ' . $expires_time . ';
		$offset = 60 * $expires_time;
		$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s", time() + $offset). " GMT";
		header("Cache-Control: private", false);
		header("Content-Type: image/jpeg");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $intSize");
	//	ob_clean();
		$fp = fopen($string_thumb_path, 'rb');
		fseek($fp, $offset);
		while(!feof($fp))
			echo fread($fp, 8192);
	flush();
	//	ob_flush();
		fclose($fp);
	}
//	flush();
//	ob_flush();
//	ob_end_clean();
	die;
}
//error_reporting(0);
//define('DOSSIER_IMAGE', JPATH_SITE);
define('DOSSIER_VIGNETTE', dirname(__FILE__).'/../../../../');
//require('lib.php');
if(!isset($_GET['largeur'])) $_GET['largeur']=640;
if(!isset($_GET['hauteur'])) $_GET['hauteur']=480;
$int_largeur = (int) $_GET['largeur'];
$int_hauteur = (int) $_GET['hauteur'];
/**
 * Le paramètre chemin contiendra le chemin de l'image source à redimensionner
 */
if(!isset($_GET['chemin'])) die;
/**
 * récupération et nettoyage du chemin
 */
$string_img = $_GET['chemin'];
$string_img = str_replace('..','',$string_img);
$string_img = str_replace('\\', '/',$string_img);
$string_img = str_replace('//', '/',$string_img);
//echo($string_img);
/**
 * Vérification de l'extension
 */
$arr_extension = array('jpg','jpeg','JPG','JPEG');
$string_extension = @end(explode('.',$string_img));
if(!in_array($string_extension,$arr_extension)) die;
sendToClient(doThumb($string_img, $int_largeur, $int_hauteur));

