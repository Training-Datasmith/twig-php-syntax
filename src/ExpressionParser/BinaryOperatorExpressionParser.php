<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Expression_Parser;

use Twig\Expression_Parser\Abstract_Expression_Parser;
use Twig\Expression_Parser\Infix_Associativity;
use Twig\Expression_Parser\Infix_Expression_Parser_Interface;
use Twig\Node\Expression\Abstract_Expression;
use Twig\Node\Expression\Binary\Abstract_Binary;
use Twig\Parser;
use Twig\Token;
final class Binary_Operator_Expression_Parser extends Abstract_Expression_Parser implements Infix_Expression_Parser_Interface
{
    public function __construct(
        /** @var class-string<AbstractBinary> $nodeClass */
        private readonly string $node_class,
        private readonly string $name,
        private readonly int $precedence,
        private readonly Infix_Associativity $associativity = Infix_Associativity::Left
    )
    {
    }
    #[\Override]
    public function parse(Parser $parser, Abstract_Expression $left, Token $token): Abstract_Binary
    {
        $right = $parser->parse_expression($this->get_associativity() === Infix_Associativity::Left ? $this->get_precedence() + 1 : $this->get_precedence());
        return new $this->node_class($left, $right, $token->get_line());
    }
    #[\Override]
    public function get_associativity(): Infix_Associativity
    {
        return $this->associativity;
    }
    #[\Override]
    public function get_name(): string
    {
        return $this->name;
    }
    #[\Override]
    public function get_precedence(): int
    {
        return $this->precedence;
    }
}