<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Token_Parser;

use Twig\Node\Node;
final class Break_Token_Parser extends Break_Or_Continue_Token_Parser
{
    #[\Override]
    public function get_tag(): string
    {
        return 'break';
    }
    #[\Override]
    protected function get_node_object(int $loop_number, int $lineno): Node
    {
        return new Break_Node($loop_number, $lineno);
    }
}