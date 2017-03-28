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
jimport('joomla.event.plugin');
jimport('joomla.version');
jimport('joomla.application.component.helper');

class plgContentpartilhatordocman extends JPlugin {
	protected $_plugin = null;
	protected $_params = null;
	protected $_titulo = null;
	protected $_tarefa = null;
	protected $_gid = null;
	protected $_Itemid = null;
	protected $_versomJ = null;
	protected $_cabecalhoPagina = null;

	function plgContentpartilhatordocman(&$subject, $config) {
		parent::__construct($subject, $config);
		$versom = new JVersion;
		$this->_versomJ = substr($versom->getShortVersion(),0,3);

		if ($this->_versomJ == "1.5") {
			$this->_plugin = &JPluginHelper::getPlugin('content', 'partilhatordocman');
			$this->_params = new JParameter($this->_plugin->params);
		}
		else {
			$this->loadLanguage();
		}
	}

	// DOCman for Joomla 1.5 && 2.5
	function onPrepareContent($article, $params, $limitstart) {
		if(JPluginHelper::isEnabled('content', 'partilhatordocman') == false) 
			return false;
		if(!JComponentHelper::isEnabled('com_docman', true))
			return false;
		$this->_tarefa = JRequest::getVar('task');
		$this->_gid = JRequest::getVar('gid');
		$this->_Itemid = JRequest::getVar('Itemid');
		if(JRequest::getVar('option') != 'com_docman' || $this->_tarefa != "doc_details")
			return;
		$mainframe = &JFactory::getApplication();
		if($mainframe->isAdmin()) {
			JPlugin::loadLanguage('plg_content_partilhatordocman');
		}
		else {
			JPlugin::loadLanguage('plg_content_partilhatordocman', 'administrator');
		}
		if($this->getParam('excluir_ligacoes')) {
			$idLigacoes = explode(',', str_replace(' ', '', $this->getParam('excluir_ligacoes')));
			if(in_array($this->_gid, $idLigacoes)) 
				return;
		}
		// $servicos[número] = array(tipo, nome, parâmetro, ligaçom, imagem);
		$servicos[] = array('shakeit', 'Tuenti', 'ver_tuenti', 'http://tuenti.com/share?url=', 'jtuenti_');
		$servicos[] = array('shakeit', 'Facebook', 'ver_facebook', 'http://www.facebook.com/share.php?u=', 'jfacebook_');
		$servicos[] = array('tweettit', 'Twitter', 'ver_twitter', 'http://twitter.com/share?url=', 'jtwitter_');
		$servicos[] = array('inshare', 'In Share', 'ver_inshare', '', '');
		$servicos[] = array('googleplus', 'Google +1', 'ver_googleplus', '', '');
		$servicos[] = array('twitter_conta', 'Twitter', 'ver_twitter_conta', '', '');
		$servicos[] = array('facebook_like', 'Facebook', 'ver_facebook_like', '', '');
		$ladrairo = '';
		$totalServicos = count($servicos);
		$serieServicos = join(',', range(1, $totalServicos, 1));
		$doc = &JFactory::getDocument();
		$this->_cabecalhoPagina = $doc->getHeadData();
		// CSS -->
		$espaco_ico = $this->getParam('espaco_icones_produto', 5);
		$tamanho_ico = $this->getParam('tamanho_icones_produto', 24);
		$tamanho_ico_qua = $tamanho_ico.'x'.$tamanho_ico;
		$alinhamento_ico = $this->getParam('alinhamento_icones_produto', 'left');
		$altoBloco = $this->getParam('alto_bloco_produto', 90).'px';
		$amploBloco = ($this->getParam('amplo_bloco_produto') ? 'width: '.$this->getParam('amplo_bloco_produto').'px;' : 'width: 100%;');
		$margemTopo = $this->getParam('margem_topo_produto', 3).'px';
		$margemPe = $this->getParam('margem_pe_produto', 3).'px';
		$margemEsquerda = $this->getParam('margem_esquerda_produto', 3).'px';
		$margemDireita = $this->getParam('margem_direita_produto', 3).'px';
		$posicom = $this->getParam('posicom_produto', 'topo');
		$margemSuperiorFacebook = $this->getParam('top_margin_facebook', 5).'px';
		$margemInferiorFacebook = $this->getParam('bottom_margin_facebook', 5).'px';
		$margemSuperiorTwitter = $this->getParam('top_margin_twitter', 2).'px';
		$margemInferiorTwitter = $this->getParam('bottom_margin_twitter', 2).'px';
		if(preg_match('/#partilhatordocman/i',$this->_cabecalhoPagina['style']['text/css']) == 0) {
		$estilo = <<<REMATE
#partilhatordocman { $amploBloco height: $altoBloco; margin: $margemTopo $margemDireita $margemPe $margemEsquerda; }
#partilhatordocman.left { text-align: left; }
#partilhatordocman.right { text-align: right; }
#partilhatordocman.center { text-align: center; }
#partilhatordocman img { border: none; margin: 0; padding: 0; }
#partilhatordocman a, #partilhatordocman a:hover, #partilhatordocman a:visited, #partilhatordocman a:link { text-decoration: none; margin: 0; padding: 0; background-color: transparent; }
#partilhatordocman .partilhatordocman_icone { margin-right:${espaco_ico}px; background-color: transparent; }
#partilhatordocman .fb-like { margin: $margemSuperiorFacebook 0 $margemInferiorFacebook 0; }
#partilhatordocman .twitter { margin: $margemSuperiorTwitter 0 $margemInferiorTwitter 0; text-align: left; }
REMATE;
			if($this->getParam('ver_googleplus', 0)) 
				$estilo .= "\n#partilhatordocman .partilhatordocman_googleplus {display: inline-block;".($this->getParam('amplo_googleplus') ? " width: ".$this->getParam('amplo_googleplus')."px;" : "").($tamanho_ico == "24" ? "}" : " position: relative; bottom: 1px;}");
			if($this->getParam('ver_inshare', 0)) 
				$estilo .= "\n#partilhatordocman .partilhatordocman_inshare {display: inline-block; margin-right: ".$espaco_ico."px; ".($tamanho_ico == "24" ? "position: relative; top: 2px;}" : "position: relative; top: 5px;}");
		// <-- CSS
		}
		else $estilo = '';
		$doc->addStyleDeclaration($estilo);
		$db = JFactory::getDBO();
		if(isset($article->dmname))
			$this->_titulo = $article->dmname;
		elseif(isset($article->title))
			$this->_titulo = $article->title;
		else
			$this->_titulo = JFactory::getConfig()->getValue('config.sitename');
		$ladrairo .= '<div id="partilhatordocman" class="'.$alinhamento_ico.'">';
		if($this->getParam('ordenar_icones', 0)) 
			$ordem = explode(",", $this->getParam('ordem_icones', $serieServicos));
		else 
			$ordem = explode(",", $serieServicos);
		foreach($ordem as $servico) {
			$ladrairo .= $this->geraCodigoBotoes($servicos, $servico, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
		}
		if(count($ordem) < $totalServicos) {
			for($i = 1;$i <= $totalServicos;$i++) {
				if(!in_array($i, $ordem)) {
					$ladrairo .= $this->geraCodigoBotoes($servicos, $i, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				}
			}
		}
		$ladrairo .= '</div>';
			if(($posicom == 'pe')) 
				$article->text = $article->text.'<!-- Partilhator Plug-in for DOCman Begin -->'.$ladrairo.'<!-- Partilhator Plug-in for DOCman End -->';
			else 
				$article->text = '<!-- Partilhator Plug-in for DOCman Begin -->'.$ladrairo.'<!-- Partilhator Plug-in for DOCman End -->'.$article->text;
			return $article->text;
	}

	function plgGetPageUrl(&$obj) {
		if(!is_null($obj)) {
				$uri = &JURI::getInstance();
				$curPageURL = 'index.php?option=com_docman';
				if(!empty($this->_gid)) {
					$curPageURL .= '&task=doc_details';
					$curPageURL .= '&gid='.$this->_gid;
				}
				if(!empty($this->_Itemid)) {
					$curPageURL .= '&Itemid='.$this->_Itemid;
				}
			$curPageURL = $uri->toString(array('scheme', 'host', 'port')).JRoute::_($curPageURL);
			return $curPageURL;
		}
	}

/*
 * 
 * name: geraCodigoBotoes
 * @param
 * @return
 */
	function geraCodigoBotoes($servicos, $indice, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		$treito = '';
		$indice--;
		switch($servicos[$indice][0]) {
			case 'shakeit':
			$treito .= $this->shakeit($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
			case 'tweettit':
			$treito .= $this->tweettit($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
			case 'googleplus':
			$treito .= $this->googleplus($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
			case 'inshare':
			$treito .= $this->inshare($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
			case 'twitter_conta':
			$treito .= $this->twitter_conta($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
			case 'facebook_like':
			$treito .= $this->facebook_like($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			break;
		}
		return $treito;
	}

	function getParam($parametro, $padrom = null) {
		$retorno = false;
		if ($this->_versomJ == "1.5") $retorno = $this->_params->get($parametro, $padrom);
		else $retorno = $this->params->def($parametro, $padrom);
		return $retorno;
	}

	function shakeit($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 1)) 
			return('<span class="partilhatordocman_icone"><a href="'.$ligacom.rawurlencode(utf8_encode($this->plgGetPageUrl($article))).'" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilhatordocman/'.($this->_versomJ == '1.5' ? '' : 'partilhatordocman/').$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
		else 
			return('');
	}

	function tweettit($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 0) == 1) {
			// Novo sistema: o twitter reduce o tamanho das ligações e trata bem os diacríticos
			$url = rawurlencode(utf8_encode($this->plgGetPageUrl($article)));
			if(!$this->getParam('mensagem_twitter')) 
				$mensagemTwitter = $this->_titulo;
			else 
				$mensagemTwitter = $this->getParam('mensagem_twitter');
			$title = urlencode($mensagemTwitter);
			$separator = $this->getParam('separator');
			$space = urlencode(' ');
			$tweet = $url;
			return('<span class="partilhatordocman_icone"><a rel="nofollow" href="'.$ligacom.$tweet.'&text='.$this->getParam('sitio_curto_twitter', 'Web').': '.$title.'&via='.$this->getParam('sitio_longo_twitter', 'Web').'" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilhatordocman/'.($this->_versomJ == '1.5' ? '' : 'partilhatordocman/').$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
		}
		else 
			return('');
	}

	function googleplus($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 0) == 1) {
			$tamanho = ($tamanho_ico == '24') ? 'standard' : 'small';
			$localFinal = '';
			if($this->getParam('local_googleplus')) {
				$localFinal = $this->getParam('local_googleplus');
			}
			else {
				$local = &JFactory::getLanguage();
				$localCompleto = $local->getLocale();
				$localSimples = explode('.', $localCompleto[0]);
				$localFinal = $localSimples[0];
			}
			return('<script src="http://apis.google.com/js/plusone.js" type="text/javascript">'.($localFinal ? '{lang:"'.str_replace('_', '-', $localFinal).'"}' : '').'</script><span class="partilhatordocman_googleplus"><g:plusone size="'.$tamanho.'" href="'.utf8_encode($this->plgGetPageUrl($article)).'"></g:plusone></span>');
		}
		else 
			return('');
	}

	function inshare($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 0) == 1) {
			$doc = &JFactory::getDocument();
			$doc->addScript("http://platform.linkedin.com/in.js");
			$tamanho = ($tamanho_ico == '24') ? 'standard' : 'small';
			return('<span class="partilhatordocman_inshare"><script type="IN/Share" data-url="'.utf8_encode($this->plgGetPageUrl($article)).'" data-counter="right"></script></span>');
		}
		else 
			return('');
	}

	function twitter_conta($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 0) == 1) {
			return('<div class="twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-url="'.utf8_encode($this->plgGetPageUrl($article)).'" data-counturl="'.utf8_encode($this->plgGetPageUrl($article)).'" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>');
		}
		else 
			return('');
	}

	function facebook_like($nome, $parametro, $ligacom, $imagem, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if($this->getParam($parametro, 0) == 1) {
			if($this->getParam('local_facebook')) {
				$localFinal = $this->getParam('local_facebook');
			}
			else {
				$local = &JFactory::getLanguage();
				$localCompleto = $local->getLocale();
				$localSimples = explode('.', $localCompleto[0]);
				$localFinal = $localSimples[0];
			}
			$db = JFactory::getDBO();
				$query = 'SELECT dmthumbnail'
					.' FROM #__docman'
					.' WHERE id = '.$this->_gid ;
			$db->setQuery($query, 0, 1);
			$imagem = $db->loadResult();
			$doc	= & JFactory::getDocument();
			$config =& JFactory::getConfig();
			$localFB = str_replace('-', '_', $localFinal);
			if(preg_match('/<meta property="og:type"/i', $this->_cabecalhoPagina['custom'][0]) == 0) {
				$metaOG = "<meta property=\"og:type\" content=\"article\"/>".PHP_EOL;
				$metaOG .= "<meta property=\"og:title\" content=\"".$this->_titulo."\"/>".PHP_EOL;
				$corpoAnuncio = str_replace(array("\r", "\r\n", "\n"), " ", strip_tags($article->text));
				$metaOG .= "<meta property=\"og:description\" content=\"".($corpoAnuncio != '' ? $corpoAnuncio : $this->_titulo)."\"/>".PHP_EOL;
				$metaOG .= "<meta property=\"og:site_name\" content=\"".$config->getValue('sitename')."\"/>".PHP_EOL;
				$metaOG .= "<meta property=\"og:url\" content=\"".$this->plgGetPageUrl($article)."\"/>".PHP_EOL;
				if($imagem !='') $metaOG .= "<meta property=\"og:image\" content=\"".JURI::root().'images/stories/'.$imagem."\"/>".PHP_EOL;
				$metaOG .= "<meta property=\"og:locale\" content=\"".$localFB."\"/>".PHP_EOL;
				$doc->addCustomTag($metaOG);
			}
		
			return('<div id="fb-root"></div>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/'.$localFinal.'/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));</script>

			<div class="fb-like" data-href="'.utf8_encode($this->plgGetPageUrl($article)).'" '.($this->getParam('show_send_facebook', 1) ? 'data-send="true" ' : '') .'data-layout="'.$this->getParam('layout_facebook', 'standard').'" data-width="'.$this->getParam('width_facebook', 450).'" data-show-faces="'.($this->getParam('show_faces_facebook', 1) ? 'true' : 'false').'" data-action="'.$this->getParam('verb_facebook', 'like').'"></div>');
		}
		else 
			return('');
	}
}
