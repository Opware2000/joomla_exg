<?php
/**
 * @package		EXG - Easy eXtended Gallery - plugin for Joomla 3.x
 * @copyright 	Nicolas Ogier
 * @author 		Nicolas Ogier {@link http://www.nicolas-ogier.fr}
 * @version 	3-1.0	2013-05-01
 * @link 		http://www.nicolas-ogier.fr/exg/
 * 
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
// Import library dependencies
//jimport('joomla.plugin.plugin');
class PlgContentEXG extends JPlugin
{
	protected $_tag_gallery = 'gallery';
	protected $_live_site;
	protected $_absolute_path;
	
	function __construct(&$subject, $params) {
		$app = JFactory::getApplication();
		if($app->isAdmin())
		{
			return;
		}
		parent::__construct($subject, $params);
		// activation de la langue
		$this->loadLanguage('plg_content_exg', JPATH_ADMINISTRATOR);
		// on récupere le chemin absolu et l'URL du site
		$this->_absolute_path = JPATH_SITE;
		$this->_live_site = JURI::base();
		if(substr($this->_live_site, -1) == '/')
		{
			$this->_live_site = substr($this->_live_site, 0, -1);
		}
		// on récupère quelques paramètres
		$tag = $this->params->get('exg_tag', $this->_tag_gallery);
		//vérification que le tag est correctement formaté
		if(is_string($tag) && ctype_alnum($tag)) {
			$this->_tag_gallery = $tag;
		}
	}
	public function onContentPrepare($context, &$article, &$params, $limitstart=0) 
	{
		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}
		// simple performance check to determine whether bot should process further
		if (strpos($article->text, 'gallery') === false && strpos($article->text, '/gallery') === false)
		{
			return true;
		}
		
		
	}
}