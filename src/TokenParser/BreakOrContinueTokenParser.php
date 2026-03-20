<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Token_Parser;

use Twig\Error\Syntax_Error;
use Twig\Node\Node;
use Twig\Token;
use Twig\Token_Parser\Abstract_Token_Parser;
abstract class Break_Or_Continue_Token_Parser extends Abstract_Token_Parser
{
    abstract protected function get_node_object(int $loop_number, int $lineno): Node;
    #[\Override]
    public function parse(Token $token): Node
    {
        $lineno = $token->get_line();
        $stream = $this->parser->get_stream();
        // How many loops to break out of
        $loop_number = 1;
        $number_token = $stream->next_if(Token::NUMBER_TYPE);
        if ($number_token !== null) {
            $loop_number = (int) $number_token->get_value();
        }
        if ($loop_number > 1 && $this->get_tag() === 'continue') {
            throw new Syntax_Error(\ucfirst($this->get_tag()) . ' tag cannot be used with a number higher than 1.', $stream->get_current()->get_line(), $stream->get_source_context());
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        // Count how many loops are starting minus the loops ending
        $loop_count = 0;
        for ($i = 1; true; $i++) {
            try {
                // Look ahead to find for and endfor tokens to make sure
                // there are more loops ending than starting
                $token = $stream->look($i);
            } catch (Syntax_Error) {
                // End of template, leading to SyntaxError
                break;
            }
            // Count both "for" loops and "foreach" loops
            if ($token->test(Token::NAME_TYPE, 'for') || $token->test(Token::NAME_TYPE, 'foreach')) {
                $loop_count++;
            } elseif ($token->test(Token::NAME_TYPE, 'endfor') || $token->test(Token::NAME_TYPE, 'endforeach')) {
                $loop_count--;
            }
        }
        // There should be more loops ending than starting, making loopCount negative
        if ($loop_count >= 0) {
            throw new Syntax_Error(\ucfirst($this->get_tag()) . ' tag is only allowed in \'for\' or \'foreach\' loops.', $stream->get_current()->get_line(), $stream->get_source_context());
        }
        // There should be more loops ending than starting, making loopCount negative
        if (\abs($loop_count) < $loop_number) {
            throw new Syntax_Error(\ucfirst($this->get_tag()) . ' tag uses a loop number higher than the actual loops in this context - you are using the number ' . $loop_number . ' but in the given context the maximum number is ' . \abs($loop_count) . '.', $stream->get_current()->get_line(), $stream->get_source_context());
        }
        return $this->get_node_object($loop_number, $lineno);
    }
}