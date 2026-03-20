<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Token_Parser;

use Twig\Attribute\Yield_Ready;
use Twig\Compiler;
use Twig\Node\Node;
#[Yield_Ready]
final class Continue_Node extends Node
{
    public function __construct(private readonly int $loop_number, int $lineno)
    {
        parent::__construct([], [], $lineno);
    }
    #[\Override]
    public function compile(Compiler $compiler): void
    {
        $compiler->add_debug_info($this)->write("if (isset(\$context['loop'])) {\n")->indent()->write("++\$context['loop']['index0'];\n")->write("++\$context['loop']['index'];\n")->write("\$context['loop']['first'] = false;\n")->write("if (isset(\$context['loop']['length'])) {\n")->indent()->write("--\$context['loop']['revindex0'];\n")->write("--\$context['loop']['revindex'];\n")->write("\$context['loop']['last'] = 0 === \$context['loop']['revindex0'];\n")->outdent()->write("}\n")->outdent()->write("}\n")->write('continue ' . $this->loop_number . ";\n");
    }
}