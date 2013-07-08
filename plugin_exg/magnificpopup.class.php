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
defined('_JEXEC') or die('Restricted access');
/**
 * class MagnificPopup : fournit une class pour l'utilisation de MagnificPopup dans EXG
 **/
class magnificClass {
	protected $_gallerieNombre;
	public $_scriptNom;
	protected $_nombreImage;
	function __construct($nombreGallerie, $nombreImage) {
		$this->_scriptNom='Magnific Popup Script';
		$this->_gallerieNombre = $nombreGallerie;
		$this->_nombreImage = $nombreImage;
	}
	function css(){
		return $css ;
	}
	function javascript(){
		$html='<script type="text/javascript">'."	(document).ready(function() {
		$('.popup-gallery".$this->_gallerieNombre."').magnificPopup({delegate: 'a',type: 'image',tLoading: '".'Loading image'." #%curr%...',}	}
				mainClass: 'mfp-img-mobile',gallery: {enabled: true,navigateByImgClick: true,preload: [0,1] },image: {tError: '<a href=".'"%url%"'.">The image #%curr%</a> could not be loaded.',titleSrc: function(item) {return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';}}});});</script>";
		return $html;
	}
}