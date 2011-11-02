<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

/**
 * Change {{ passwords }} to passwords. 
 * set amount/length using url parameters like passwords?length=3&amount=2.
 */
class syntax_plugin_passwords extends DokuWiki_Syntax_Plugin 
{
    /**
     * @var string allowed characters
     */
    private $allowedCharacters = 'abcdefghjkmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    
    /**
     * @var int amount of passwords
     */
    private $amount = 4;
    
    /**
     * @var int length of password
     */
    private $length = 8;

    /**
     * @var int iterator
     */
    private $iterator = 0;

    /**
     * get plugin type
     *
     * @return string 
     */
    public function getType() {
        return 'substition';
    }
    
    /**
     * get sorting order
     *
     * @return int 
     */
    public function getSort() {
        return 32;
    }
    
    /**
     * Define matching strings
     *
     * @param string $mode 
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{ passwords }}', $mode, 'plugin_passwords');
        $this->Lexer->addSpecialPattern('\{\{ passwords\? }}', $mode, 'plugin_passwords');
        $this->Lexer->addSpecialPattern('\{\{ passwords\?amount=\d*&length=\d* }}', $mode, 'plugin_passwords');
        $this->Lexer->addSpecialPattern('\{\{ passwords\?length=\d*&amount=\d* }}', $mode, 'plugin_passwords');
        $this->Lexer->addSpecialPattern('\{\{ passwords\?amount=\d* }}', $mode, 'plugin_passwords');
        $this->Lexer->addSpecialPattern('\{\{ passwords\?length=\d* }}', $mode, 'plugin_passwords');
    }
    
    /**
     * set the parameters for $data for the render function
     *
     * @param string $match gematchte string
     * @param int $state
     * @param int $pos
     * @param DokuHandler $handler
     * 
     * @return array
     */
    public function handle($match, $state, $pos, &$handler) {
        return array($match, $state, $pos);
    }
    
    /**
     * Render the passwords
     *
     * @param string $format
     * @param Doku_Renderer_metadata $renderer
     * @param array $data 
     * 
     * @return type boolean is de operatie geslaagd?
     */
    public function render($format, &$renderer, $data) {
        if('xhtml' === $format) {
            $this->iterator = 0;
            $this->parseData($data[0]);
            for($i=0; $i<$this->getAmount(); $i++) {
                $renderer->doc .= $this->getPasswordHtml($this->getLength());
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Parse data to filter out variables
     *
     * @param string $data
     * 
     * @return boolean success 
     */
    private function parseData($data) {
        $regexAmount = '#amount=(\d*)#i';
        $regexLength = '#length=(\d*)#i';
        
        preg_match($regexAmount, $data, $resultAmount);
        $this->setAmount($resultAmount[1]);

        preg_match($regexLength, $data, $resultLength);
        $this->setLength($resultLength[1]);
        return true;
    }
    
    /**
     * Generate the password
     *
     * @param int $length lengte
     * 
     * @return string 
     */
    private function generatePassword($length) {
        $password = '';
        for($j=0; $j < $length; $j++) {
            $r = rand(0,strlen($this->getAllowedCharacters())-1);
            $c = substr($this->getAllowedCharacters(), $r, 1);
            $password .= $c;
        }
        return $password;
    }
    
    /**
     * Generate HTML
     *
     * @param int $passwordLength length of password
     * 
     * @return string 
     */
    private function getPasswordHtml($passwordLength) {
        $iterator = $this->getIterator();
        $iteratorHtml = '';
        if($iterator < 10) {
            $iteratorHtml .= '0';
        }
        $iteratorHtml .= $iterator;
        return $iteratorHtml
            . '<input class="inputfield" '
            . ' type="text" '
            . ' value="' . $this->generatePassword($passwordLength) . '"'
            . ' onmouseover="this.select();" '
            . ' onclick="this.select();" '
            . ' />'
            . '<br>';
    }
    
    /**
     * Haal de iterator op
     *
     * @return int 
     */
    private function getIterator() {
        return ++$this->iterator;
    }

    /**
     * Get the allowed characters
     *
     * @return string 
     */
    private function getAllowedCharacters() {
        return $this->allowedCharacters;
    }

    /**
     * Get the amount of passwords
     *
     * @return int 
     */
    private function getAmount() {
        return $this->amount;
    }

    /**
     * Set the amount of passwords
     *
     * @param int $amount 
     */
    private function setAmount($amount) {
        $amount = (int) $amount;
        if($amount > 0) {
            $this->amount = $amount;
        }
    }

    /**
     * Get the lenght of the password
     *
     * @return int
     */
    private function getLength() {
        return $this->length;
    }

    /**
     * Set the lenght of the password
     *
     * @param int $length 
     */
    private function setLength($length) {
        $length = (int) $length;
        if($length > 0) {
            $this->length = $length;
        }
    }

}