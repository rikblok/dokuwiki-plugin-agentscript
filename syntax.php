<?php
/**
 * DokuWiki Plugin agentscript (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Rik Blok <rik.blok@ubc.ca>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_agentscript extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
		/* Should be less than 320 as defined in
		 * /inc/parser/parser.php:class Doku_Parser_Mode_media
		 * http://xref.dokuwiki.org/reference/dokuwiki/_classes/doku_parser_mode_media.html
		 * After plugin:applet (316), before media (320).  See https://www.dokuwiki.org/devel:parser:getsort_list
		*/
        return 317;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
//        $this->Lexer->addSpecialPattern('<FIXME>',$mode,'plugin_agentscript');
        $this->Lexer->addEntryPattern('<agentscript>',$mode,'plugin_agentscript');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</agentscript>','plugin_agentscript');
    }

    /**
     * Handle matches of the agentscript syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler){
	// Use https://www.dokuwiki.org/devel:syntax_plugins#sample_plugin_2_-_color as a guide when needed [Rik, 2014-07-18]
		switch ($state) {
			case DOKU_LEXER_ENTER :			return array($state, ''); 
			case DOKU_LEXER_UNMATCHED :	return array($state, $match);
			case DOKU_LEXER_EXIT :			return array($state, '');
		}
	}

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml'){
            /** @var Doku_Renderer_xhtml $renderer */
            list($state,$match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    $renderer->doc .= '<script src="http://agentscript.org/lib/agentscript.js"></script>' .
						'<script src="http://agentscript.org/tools/dat.gui.min.js"></script>' .
						'<script src="http://agentscript.org/lib/as.dat.gui.js"></script>' .
						'<script src="http://agentscript.org/tools/coffee-script.js"></script>' .
						'<script type="text/coffeescript">'; 
                    break;
                case DOKU_LEXER_UNMATCHED :  
				// don't clean the text. It breaks Javascript. [Rik, 2014-07-27]
                    $renderer->doc .= $match;
                    break;
                case DOKU_LEXER_EXIT :       
                    $renderer->doc .= '</script>' .
						'<div id="agentscriptwrapper">' . // interface ideas from https://github.com/concord-consortium/agentscript-models/blob/master/solar-panel/solar-panel-2.0.html [2014-07-27]
						'<canvas id="canvas" >Your browser does not support HTML5 Canvas.</canvas>' .
						'<div id="layers"></div>' .
						'<div id="playback-controls">' .
						//'<ul><li><button id="reset-button">Reset</button></li>' .
						'<li><button id="play-button">Play</button></li>' .
						//'<li><button id="step-button">Step</button></li>' .
						'<li><button id="stop-button">Stop</button></li>' .
						//'<li>Model Ticks: <span id="tick-counter"></span></li></ul>' .
						'</div>' .
						'<script>' .
						'playButton = document.getElementById("play-button"),' .
						'stopButton = document.getElementById("stop-button");' .
						'playButton.onclick = function() {ABM.model.start();}' .
						'stopButton.onclick = function() {ABM.model.stop();}' .
						'</script>';
                    break;
            }
            return true;
        }
        return false;
	}
}
// vim:ts=4:sw=4:et:
