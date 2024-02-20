<?php

namespace App\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class CastAsInteger extends FunctionNode
{
    public $stringPrimary;

    public function getSql(SqlWalker $sqlWalker) : string
    {
        return 'CAST(' . $this->stringPrimary->dispatch($sqlWalker) . ' AS integer)';
    }

    public function parse(Parser $parser) : void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->stringPrimary = $parser->StringPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
