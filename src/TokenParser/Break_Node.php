<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Token_Parser;

use Twig\Attribute\Yield_Ready;
use Twig\Compiler;
use Twig\Node\Node;
#[Yield_Ready]
final class Break_Node extends Node
{
    public function __construct(private readonly int $loop_number, int $lineno)
    {
        parent::__construct([], [], $lineno);
    }
    #[\Override]
    public function compile(Compiler $compiler): void
    {
        $compiler->add_debug_info($this)->write('break ' . $this->loop_number . ";\n");
    }
}