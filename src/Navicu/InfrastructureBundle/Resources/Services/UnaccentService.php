<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/***
 *
 * RandFunction ::= "unaccent" "(" ")"
 */
class UnaccentService extends FunctionNode
{
    public $string = null;

    public function parse(Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER); //nombre de la funciÃ³n
        $parser->match(Lexer::T_OPEN_PARENTHESIS); //abre parentesis
        $this->string = $parser->ArithmeticPrimary(); //parametro
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); //cierre parentesi
    }

    public function getSql(SqlWalker $sqlWalker) {
        return 'UNACCENT(' . $this->string->dispatch($sqlWalker) . ')';
    }

}
