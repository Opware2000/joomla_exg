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
 * class Popup : fournit une class pour l'utilisation de MagnificPopup dans EXG
 **/
class swipeboxClass {
	protected $_gallerieNombre;
	public $_scriptNom;
	protected $_nombreImage;
	public $_live_site;
	function __construct( $livesite) {
		$this->_scriptNom='swipebox';
		$this->_live_site = $livesite;
	}
	function css(){
		$css =$this->_live_site.'/plugins/content/exg/plugin_exg/source/swipebox.css';
		return $css ;
	}
	function javascript(){
	$texte ='<script src="'.$this->_live_site.'/plugins/content/exg/plugin_exg/lib/jquery-2.0.3.min.js"></script>
	<script src="'.$this->_live_site.'/plugins/content/exg/plugin_exg/source/jquery.swipebox.js"></script>
	<script type="text/javascript">
		jQuery(function($) {
			/* Basic Gallery */
			$(".swipebox").swipebox();
		});
	</script>';
		return $texte;
	}
}