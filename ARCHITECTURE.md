# Architecture: twig-php-syntax

## Purpose

A Twig extension that adds PHP-style syntax to Twig templates: `foreach` loops, `break`/`continue` control flow, PHP type tests (`is integer`, `is string`, `is float`, `is callable`, etc.), and additional binary operators. Useful for projects migrating templates from PHP to Twig.

## Directory Structure

```
src/
  Php_Syntax_Extension.php              - Registers all token parsers, tests, and operators
  TokenParser/
    Foreach_Token_Parser.php            - Parses {% foreach $items as $item %} syntax
    Break_Token_Parser.php              - Parses {% break %} statements
    Continue_Token_Parser.php           - Parses {% continue %} statements
    Break_Or_Continue_Token_Parser.php  - Shared base for break/continue parsers
    Break_Node.php / Continue_Node.php  - Twig compiler nodes for break/continue
  Test/
    Array_Test.php    - Twig test: value `is array`
    Boolean_Test.php  - Twig test: value `is boolean`
    Callable_Test.php - Twig test: value `is callable`
    False_Test.php    - Twig test: value `is false`
    Float_Test.php    - Twig test: value `is float`
    Integer_Test.php  - Twig test: value `is integer`
    Object_Test.php   - Twig test: value `is object`
    Scalar_Test.php   - Twig test: value `is scalar`
    String_Test.php   - Twig test: value `is string`
    True_Test.php     - Twig test: value `is true`
  ExpressionParser/
    Binary_Operator_Expression_Parser.php - Parses PHP-style binary operators in expressions
tests/
  Integration_Test.php  - End-to-end tests for each syntax feature
```

## Key Design Decisions

- **Token parsers over tag extensions**: Uses Twig `TokenParserInterface` to intercept `{% foreach %}`, `{% break %}`, and `{% continue %}` at the lexer level, integrating naturally with Twig's template compilation pipeline.
- **Type-test classes**: Each PHP type check is a separate `SimpleTest` subclass, keeping test logic isolated and independently testable.
- **No runtime overhead for unused features**: Twig compiles templates to PHP; `break`/`continue` nodes compile to native PHP `break`/`continue` statements, adding zero runtime cost.

## Extension Points

- Add new PHP-style tests by creating a class in `Test/` extending Twig's `Test` and registering it in `Php_Syntax_Extension::getTests()`.
- Add new token parsers for other PHP control structures.

## Dependency Flow

```
Twig environment
  └─> Php_Syntax_Extension registered
        └─> TokenParser: {% foreach %} / {% break %} / {% continue %}
        └─> Tests: is array / is string / is integer / ...
        └─> Operators: PHP-style binary operators
```
