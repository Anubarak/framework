<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 16:04
 */

namespace Craft;

require_once BASE . '/vendor/autoload.php';

/**
 * Class templateService
 * @package Craft
 */
class templateService
{
    private $loader = null;

    /** @var null|\Twig_Environment */
    private $twig = null;
    private $test = "hallo";
    private $defaultValues;

    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(Craft::getTemplatePath());
        $this->twig = new \Twig_Environment($this->loader, array(
            //'cache' => Craft::getTemplatePath() . '/cache',
            'debug' => true
        ));

        $this->twig->addTokenParser(new Project_Set_TokenParser());
    }


    public function render($template, $data = array()){
        $template = $this->twig->load($template);
        echo $template->render(array_merge($data, array(
            'assetPathCSS'   =>  BASE_URL . 'app/templates/assets/css/',
            'assetPathJS'   => BASE_URL . 'app/templates/assets/js/',
            'craft'         => craft(),
            'test'          => $this->test
        )));
    }
}

class Project_Set_TokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();
        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        Project_Set_Node::$values[] = $value;
        return new Project_Set_Node("jsRessources", $value, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'includeJsRessource';
    }
}

class Project_Set_Node extends \Twig_Node
{
    public static $values = array();

    public function __construct($name, \Twig_Node_Expression $value, $line, $tag = null)
    {
        parent::__construct(array('value' => $value), array('name' => $name), $line, $tag);
        self::$values[] = $this->getNode('value');
    }

    public function compile(\Twig_Compiler $compiler)
    {
        foreach (self::$values as $value){
            $compiler
                ->addDebugInfo($this)
                ->write('$context[\''.$this->getAttribute('name').'\'] = ')
                ->subcompile($value)
                ->raw(";\n")
            ;
        }

    }
}