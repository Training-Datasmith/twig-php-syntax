<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax\Token_Parser;

use Twig\Lexer;
use Twig\Node\Expression\Variable\Assign_Context_Variable;
use Twig\Node\For_Else_Node;
use Twig\Node\For_Node;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\Token;
use Twig\Token_Parser\Abstract_Token_Parser;
final class Foreach_Token_Parser extends Abstract_Token_Parser
{
    /*
     * Taken from ForTokenParser, we just exchanged small parts of it to support the slightly different syntax
     */
    #[\Override]
    public function parse(Token $token): Node
    {
        $lineno = $token->get_line();
        $stream = $this->parser->get_stream();
        $seq = $this->parser->parse_expression();
        $stream->expect(Token::NAME_TYPE, 'as');
        $targets = $this->parse_assignment_expression();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse($this->decide_foreach_fork(...));
        if ($stream->next()->get_value() === 'else') {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = new For_Else_Node($this->parser->subparse($this->decide_foreach_end(...), true), $stream->get_current()->get_line());
        } else {
            $else = null;
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        if (\count($targets) > 1) {
            $key_target = $targets->get_node('0');
            $key_target = new Assign_Context_Variable($key_target->get_attribute('name'), $key_target->get_template_line());
            $value_target = $targets->get_node('1');
            $value_target = new Assign_Context_Variable($value_target->get_attribute('name'), $value_target->get_template_line());
        } else {
            $key_target = new Assign_Context_Variable('_key', $lineno);
            $value_target = $targets->get_node('0');
            $value_target = new Assign_Context_Variable($value_target->get_attribute('name'), $value_target->get_template_line());
        }
        return new For_Node($key_target, $value_target, $seq, null, $body, $else, $lineno);
    }
    public function decide_foreach_fork(Token $token): bool
    {
        return $token->test(['else', 'endforeach']);
    }
    public function decide_foreach_end(Token $token): bool
    {
        return $token->test('endforeach');
    }
    #[\Override]
    public function get_tag(): string
    {
        return 'foreach';
    }
    /*
     * Taken from ExpressionParser::parseAssignmentExpression, we just exchanged the operator usage from , to =>
     */
    #[\Override]
    protected function parse_assignment_expression(): Nodes
    {
        $stream = $this->parser->get_stream();
        $targets = [];
        while (true) {
            $token = $this->parser->get_current_token();
            if ($stream->test(Token::OPERATOR_TYPE) && preg_match(Lexer::REGEX_NAME, (string) $token->get_value())) {
                // in this context, string operators are variable names
                $this->parser->get_stream()->next();
            } else {
                $stream->expect(Token::NAME_TYPE, null, 'Only variables can be assigned to');
            }
            $targets[] = new Assign_Context_Variable($token->get_value(), $token->get_line());
            // The following line is the only change in the whole method: use => instead of ,
            if (!$stream->next_if(Token::OPERATOR_TYPE, '=>')) {
                break;
            }
        }
        return new Nodes($targets);
    }
}