<?php
/*
 *      Partilhator for DOCman
 *      @package Partilhator Plug-in for DOCman
 *      @subpackage Content
 *      @author José António Cidre Bardelás
 *      @copyright Copyright (C) 2011 José António Cidre Bardelás and Joomla Empresa. All rights reserved
 *      @license GNU/GPL v3 or later
 *      
 *      Contact us at info@joomlaempresa.com (http://www.joomlaempresa.es)
 *      
 *      This file is part of Partilhator Plug-in for DOCman.
 *      
 *          Partilhator Plug-in for DOCman is free software: you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation, either version 3 of the License, or
 *          (at your option) any later version.
 *      
 *          Partilhator Plug-in for DOCman is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *      
 *          You should have received a copy of the GNU General Public License
 *          along with Partilhator Plug-in for DOCman.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Acesso restrito');
$versJ = new JVersion;
$versomJ = substr($versJ->getShortVersion(), 0, 3);
if($versomJ == '1.5') {
	class JElementSubtitulo extends JElement {
		var $_name = 'Subtitulo';
		function fetchElement($name, $value, &$node, $control_name) {
			return '<div style="color:#000; font-size:10px; font-weight:bold; font-style:italic; padding:3px; margin:0; text-align:center; background:#BCD9E5;">'.JText::_($value).'</div>';
		}
	}
}
else {
	class JFormFieldSubtitulo extends JFormField {
		protected $type = 'subtitulo';
		public function getInput() {
			return '<div style="clear: both; color:#000; font-size:10px; font-weight:bold; font-style:italic; padding:3px; margin:0; text-align:center; background:#BCD9E5;">'.JText::_($this->value).'</div>';
		}
	}
}
