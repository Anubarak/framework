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


    public function init(){
        $path = anu()->config->get('paths');
        $templatePath = $path['customTemplateDirectory'];

        $this->loader = new \Twig_Loader_Filesystem($templatePath);
        $this->twig = new \Twig_Environment($this->loader, array(
            'debug' => true,
            'dev', true
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
        $this->twig->addExtension(new ClassTwigExtension());
        $this->twig->addTokenParser(new IncludeResource_TokenParser('includeJsFile'));
        $this->twig->addTokenParser(new IncludeResource_TokenParser('includeCssFile'));
        $this->twig->addGlobal('anu', anu());
        $this->twig->addGlobal('baseUrl', BASE_URL);
    }

    /**
     * @param $fileName
     */
    public function includeJsFile($fileName){
        $this->js_files[] = $fileName;
    }

    /**
     * @param $fileName
     */
    public function includeCssFile($fileName){
        $this->css_files[] = $fileName;
    }

    /**
     * @return array
     */
    public function getCssFile(){
        return $this->css_files;
    }

    /**
     * @param $code
     */
    public function addJsCode($code){
        $this->js_code[] = $code;
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
    public function render($template, $data = array()){
        $template = $this->twig->load($template);

        echo $template->render(array_merge($data, array(
            'assetPathCSS'   =>  BASE_URL . 'app/templates/assets/css/',
            'assetPathJS'   => BASE_URL . 'app/templates/assets/js/',
        )));
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
        return (new \ReflectionClass($object))->getShortName();
    }
}