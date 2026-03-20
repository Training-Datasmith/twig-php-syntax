<?php

declare (strict_types=1);
namespace Squirrel\Twig_Php_Syntax;

use Squirrel\Twig_Php_Syntax\Expression_Parser\Binary_Operator_Expression_Parser;
use Squirrel\Twig_Php_Syntax\Test\Array_Test;
use Squirrel\Twig_Php_Syntax\Test\Boolean_Test;
use Squirrel\Twig_Php_Syntax\Test\Callable_Test;
use Squirrel\Twig_Php_Syntax\Test\False_Test;
use Squirrel\Twig_Php_Syntax\Test\Float_Test;
use Squirrel\Twig_Php_Syntax\Test\Integer_Test;
use Squirrel\Twig_Php_Syntax\Test\Object_Test;
use Squirrel\Twig_Php_Syntax\Test\Scalar_Test;
use Squirrel\Twig_Php_Syntax\Test\String_Test;
use Squirrel\Twig_Php_Syntax\Test\True_Test;
use Squirrel\Twig_Php_Syntax\Token_Parser\Break_Token_Parser;
use Squirrel\Twig_Php_Syntax\Token_Parser\Continue_Token_Parser;
use Squirrel\Twig_Php_Syntax\Token_Parser\Foreach_Token_Parser;
use Twig\Extension\Abstract_Extension;
use Twig\Node\Expression\Binary\And_Binary;
use Twig\Node\Expression\Binary\Or_Binary;
use Twig\Twig_Filter;
use Twig\Twig_Test;
final class Php_Syntax_Extension extends Abstract_Extension
{
    #[\Override]
    public function get_token_parsers(): array
    {
        return [new Foreach_Token_Parser(), new Break_Token_Parser(), new Continue_Token_Parser()];
    }
    #[\Override]
    public function get_filters(): array
    {
        return [new Twig_Filter('strtotime', function (string $time, ?int $now = null): int {
            $timestamp = \strtotime($time, $now ?? time());
            if ($timestamp === false) {
                throw new \InvalidArgumentException('Given time string for strtotime seems to be invalid: ' . $time);
            }
            return $timestamp;
        }), new Twig_Filter('intval', function (mixed $var): int {
            if (\is_int($var)) {
                return $var;
            }
            $var = $this->validate_type($var, 'intval');
            return \intval($var);
        }), new Twig_Filter('floatval', function (mixed $var): float {
            if (\is_float($var)) {
                return $var;
            }
            $var = $this->validate_type($var, 'floatval');
            return \floatval($var);
        }), new Twig_Filter('strval', function (mixed $var): string {
            if (\is_string($var)) {
                return $var;
            }
            $var = $this->validate_type($var, 'strval');
            return \strval($var);
        }), new Twig_Filter('boolval', function (mixed $var): bool {
            if (\is_bool($var)) {
                return $var;
            }
            $var = $this->validate_type($var, 'boolval');
            return \boolval($var);
        })];
    }
    private function validate_type(mixed $var, string $function_name): string|int|float|bool|null
    {
        if (\is_object($var) && \method_exists($var, '__toString')) {
            return $var->__toString();
        }
        if (!\is_scalar($var) && $var !== null) {
            throw new \InvalidArgumentException('Non-scalar value given to ' . $function_name . ' filter');
        }
        return $var;
    }
    #[\Override]
    public function get_tests(): array
    {
        return [
            // adds test: "var is true"
            new Twig_Test('true', null, ['node_class' => True_Test::class]),
            // adds test: "var is false"
            new Twig_Test('false', null, ['node_class' => False_Test::class]),
            // adds test: "var is array"
            new Twig_Test('array', null, ['node_class' => Array_Test::class]),
            // adds test: "var is bool" / "var is boolean"
            new Twig_Test('bool', null, ['node_class' => Boolean_Test::class]),
            new Twig_Test('boolean', null, ['node_class' => Boolean_Test::class]),
            // adds test: "var is callable"
            new Twig_Test('callable', null, ['node_class' => Callable_Test::class]),
            // adds test: "var is float"
            new Twig_Test('float', null, ['node_class' => Float_Test::class]),
            // adds test: "var is int" / "var is integer"
            new Twig_Test('int', null, ['node_class' => Integer_Test::class]),
            new Twig_Test('integer', null, ['node_class' => Integer_Test::class]),
            // adds test: "var is object"
            new Twig_Test('object', null, ['node_class' => Object_Test::class]),
            // adds test: "var is scalar"
            new Twig_Test('scalar', null, ['node_class' => Scalar_Test::class]),
            // adds test: "var is string"
            new Twig_Test('string', null, ['node_class' => String_Test::class]),
        ];
    }
    #[\Override]
    public function get_expression_parsers(): array
    {
        return [new Binary_Operator_Expression_Parser(Or_Binary::class, '||', 10), new Binary_Operator_Expression_Parser(And_Binary::class, '&&', 15)];
    }
}