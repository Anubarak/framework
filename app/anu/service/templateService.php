<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 16:04
 */

namespace Anu;

require_once BASE . '/vendor/autoload.php';

/**
 * Class templateService
 * @package Anu
 */
class templateService
{
    private $loader = null;

    /** @var null|\Twig_Environment */
    private $twig = null;
    private $test;
    private $defaultValues;

    private $js_files   = array();
    private $css_files   = array();
    private $js_code    =  array();
    private $notice = array();
    private $objectDefined = false;

    public function init(){
        $path = anu()->config->get('paths');
        $templatePath = $path['customTemplateDirectory'];
        $path['angularTemplatePath'] = BASE_URL . $path['customTemplateDirectory'];
        $this->addJsCode('
            var anu = {};
            anu.config = ' . json_encode($path) .  '
        ');
        $this->loader = new \Twig_Loader_Filesystem($templatePath);
        $this->twig = new \Twig_Environment($this->loader, array(
            'debug' => true,
            'dev', true
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
        $this->twig->addExtension(new ClassTwigExtension());
        $this->twig->addExtension(new AttributeTwigExtension());
        $this->twig->addTokenParser(new IncludeResource_TokenParser('includeJsFile'));
        $this->twig->addTokenParser(new IncludeResource_TokenParser('includeCssFile'));
        $this->twig->addGlobal('anu', anu());
        $this->twig->addGlobal('assetPathCSS', BASE_URL . $path['assetPath'] . '/css/');
        $this->twig->addGlobal('assetPathJS', BASE_URL . $path['assetPath'] . '/js/');
        $this->twig->addGlobal('baseUrl', BASE_URL);
        $this->twig->addGlobal('isCpRequest', true);
    }

    /**
     * @param $fileName
     */
    public function includeJsFile($fileName){
        if(!in_array($fileName, $this->js_files)){
            $this->js_files[] = $fileName;
        }
    }

    /**
     * @param $fileName
     */
    public function includeCssFile($fileName){
        if(!in_array($fileName, $this->css_files)){
            $this->css_files[] = $fileName;
        }
    }

    /**
     * @return array
     */
    public function getCssFile(){
        return $this->css_files;
    }

    /**
     * @return array
     */
    public function getJsFile(){
        return $this->js_files;
    }

    /**
     * @param $code
     */
    public function addJsCode($code){
        if(!in_array($code, $this->js_code)){
            $this->js_code[] = $code;
        }
    }

    /**
     * @param $js
     * @return string
     */
    private function _combineJs($js)
    {
        return implode("\n\n", $js);
    }


    /**
     * @return array
     */
    public function getNotice(){
        if(is_array($this->notice) && isset($this->notice['message'], $this->notice['level'])){
            $this->addJsCode('showNotification("' . $this->notice['message'] . '" , "' . $this->notice['level'] . '");');
        }
        return isset($this->notice['message'])? $this->notice['message'] : '';

    }

    /**
     * Set notice
     *
     * @param $message
     * @param string $level
     * @return bool
     */
    public function setNotice($message, $level = 'notice'){
        if($message){
            $this->notice = array(
                'message'   => $message,
                'level'     => $level
            );
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getJsCode(){
        $js = $this->_combineJs($this->js_code);
        return "<script type=\"text/javascript\">\n/*<![CDATA[*/\n" .$js."\n/*]]>*/\n</script>";
    }

    /**
     * @param $template
     * @param array $data
     */
    public function render($template, $data = array(), $returnTemplate = false){
        $template = $this->twig->load($template);
        $html = $template->render($data);

        if(!$returnTemplate){
            echo $html;
        }else{
            return $html;
        }
    }

    /**
     * Add an element to the anu js object
     *
     * @param $object
     * @param $index
     */
    public function addAnuJsObject($object, $index){
        if($this->objectDefined){
            $this->addJsCode('
                var anu = {};
            ');
            $this->objectDefined = true;
        }

        $this->addJsCode('
            anu["' . $index.  '"] = ' . json_encode($object) . ';
        ');
    }
}


class IncludeResource_Node extends \Twig_Node
{
    // Public Methods
    // =========================================================================

    /**
     * Compiles an IncludeResource_Node into PHP.
     *
     * @param \Twig_Compiler $compiler
     *
     * @return null
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $function = $this->getAttribute('function');
        $value = $this->getNode('value');

        $compiler
            ->addDebugInfo($this);

        if ($this->getAttribute('capture'))
        {
            $compiler
                ->write("ob_start();\n")
                ->subcompile($value)
                ->write("\$_js = ob_get_clean();\n")
            ;
        }
        else
        {
            $compiler
                ->write("\$_js = ")
                ->subcompile($value)
                ->raw(";\n")
            ;
        }

        $compiler
            ->write("\\Anu\\anu()->template->{$function}(\$_js")
        ;

        if ($this->getAttribute('first'))
        {
            $compiler->raw(', true');
        }

        $compiler->raw(");\n");
    }
}



class IncludeResource_TokenParser extends \Twig_TokenParser
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    private $_tag;

    /**
     * @var boolean
     */
    private $_allowTagPair;

    // Public Methods
    // =========================================================================

    /**
     * Constructor
     *
     * @param string $tag
     *
     * @return IncludeResource_TokenParser
     */
    public function __construct($tag, $allowTagPair = false)
    {
        $this->_tag = $tag;
        $this->_allowTagPair = $allowTagPair;
    }

    /**
     * Parses resource include tags.
     *
     * @param \Twig_Token $token
     *
     * @return IncludeResource_Node
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if ($this->_allowTagPair && ($stream->test(\Twig_Token::NAME_TYPE, 'first') || $stream->test(\Twig_Token::BLOCK_END_TYPE)))
        {
            $capture = true;

            $first = $this->_getFirstToken($stream);
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
            $value = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        }
        else
        {
            $capture = false;

            $value = $this->parser->getExpressionParser()->parseExpression();
            $first = $this->_getFirstToken($stream);
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        }

        $nodes = array(
            'value' => $value,
        );

        $attributes = array(
            'function' => $this->_tag,
            'capture'  => $capture,
            'first'    => $first,
        );

        return new IncludeResource_Node($nodes, $attributes, $lineno, $this->getTag());
    }

    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('end'.strtolower($this->_tag));
    }

    /**
     * Defines the tag name.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->_tag;
    }

    // Private Methods
    // =========================================================================

    private function _getFirstToken($stream)
    {
        $first = $stream->test(\Twig_Token::NAME_TYPE, 'first');

        if ($first)
        {
            $stream->next();
        }

        return $first;
    }
}

class ClassTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'class' => new \Twig_SimpleFunction('class', array($this, 'getClass'))
        );
    }

    public function getName()
    {
        return 'class_twig_extension';
    }

    public function getClass($object)
    {
        return (Anu::getClassName($object));
    }
}

class AttributeTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'title' => new \Twig_SimpleFunction('title', array($this, 'title'))
        );
    }

    public function getName()
    {
        return 'class_twig_extension';
    }

    public function title($attribute, $key)
    {
        if(array_key_exists('title', $attribute)){
            return $attribute['title'];
        }
        return $key;
    }
}