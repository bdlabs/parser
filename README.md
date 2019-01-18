<p align="center">
    <img src="https://railt.org/images/logo-dark.svg" width="200" alt="Railt" />
</p>

<p align="center">
    <a href="https://travis-ci.org/railt/parser"><img src="https://travis-ci.org/railt/parser.svg?branch=1.3.x" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/parser/?branch=1.3.x"><img src="https://scrutinizer-ci.com/g/railt/parser/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://packagist.org/packages/railt/parser"><img src="https://poser.pugx.org/railt/parser/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/parser"><img src="https://poser.pugx.org/railt/parser/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/railt/parser/master/LICENSE.md"><img src="https://poser.pugx.org/railt/parser/license" alt="License MIT"></a>
</p>

# Parser

> Note: All questions and issues please send 
to [https://github.com/railt/railt/issues](https://github.com/railt/railt/issues)

- [Introduction](#introduction)
    - [Lexer](#lexer)
    - [Grammar](#grammar)
    - [Parser](#parser)
- [Rules](#rules)
    - [Alternation](#alternation)
    - [Concatenation](#concatenation)
    - [Repetition](#repetition)
    - [Terminal](#terminal)
- [Examples](#examples)
- [Abstract Syntax Tree](#abstract-syntax-tree)
- [Delegates](#delegates)
    - [Environment](#environment)
- [Finder](#finder)
- [Benchmarks](#benchmarks)

## Introduction

The parser provides a set of components for grammar analysis (Parsing) of the source code 
and converting them into an abstract syntax tree (AST).

For the beginning it is necessary to familiarize with parsing algorithms. This implementation,
although it allows you to switch between runtime, but provides out of the box two 
implementations: [LL(1) - Simple and LL(k) - Lookahead](https://en.wikipedia.org/wiki/LL_parser).

In order to create your own parser we need:
1) Create [lexer](#lexer)
2) Create [grammar](#grammar)
3) Create [parser](#parser)

### Lexer

Let's create a primitive lexer that can handle spaces, numbers and the addition character.

> More information about the lexer can be found in [this repository](https://github.com/railt/lexer).

```php
use Railt\Lexer\Driver\NativeRegex as Lexer;

$lexer = (new Lexer())
    ->add('T_WHITESPACE', '\\s+')
    ->add('T_NUMBER', '\\d+')
    ->add('T_PLUS', '\\+')
    ->skip('T_WHITESPACE'); 
```

### Grammar

Grammar will be a little more complicated. We need to determine in what order 
the tokens in the source text can be located, which we will parse.

```php
$grammar = new Grammar(array $rules[, string|int $rootRuleId = null [, array $delegates = []]])
```

First we start with the [(E)BNF format](https://en.wikipedia.org/wiki/Extended_Backus%E2%80%93Naur_form):

```ebnf
(* A simple example of adding two numbers will look like this: *)
expr = T_NUMBER T_PLUS T_NUMBER ;
```

To define this rule inside the Grammar, we simply use two classes that define the rules 
inside the product, this is the [concatenation](https://en.wikipedia.org/wiki/Concatenation) 
and definitions of the tokens.

```php
use Railt\Parser\Grammar;
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Terminal;

//
// This (e)BNF construction:
// expression = T_NUMBER T_PLUS T_NUMBER ;
// 
// Looks like:
// Concatenation1 = Token1 Token2 Token1
//
$grammar = new Grammar([
    new Concatenation(0, [1, 2, 1], 'expression'),
    new Terminal(1, 'T_NUMBER', true),
    new Terminal(2, 'T_PLUS', true),
]);
```

### Parser

In order to test the grammar, we can simply parse the source.

```php
use Railt\Io\File;
use Railt\Parser\Driver\Llk as Parser;

$parser = new Parser($lexer, $grammar);

echo $parser->parse(File::fromSources('2 + 2'));
```

Will outputs:
```xml
<Ast>
    <expression offset="0">
        <T_NUMBER offset="0">2</T_NUMBER>
        <T_PLUS offset="2">+</T_PLUS>
        <T_NUMBER offset="4">2</T_NUMBER>
    </expression>
</Ast>
```

But if the source is wrong, the parser will tell you exactly where the error occurred:

```php
echo $parser->parse(File::fromSources('2 + + 2'));
//                                         ^
//
// throws "Railt\Parser\Exception\UnexpectedTokenException" with message: 
// "Unexpected token '+' (T_PLUS) at line 1 and column 5"
```

## Rules

In addition, there are other grammar rules.

### Alternation 

Choosing between several rules.

```php
//
// EBNF: 
//  choice = some1 | any2 ;
//
new Alternation(<ID>, [<ID_1>, <ID_1>], 'choice');
```

### Concatenation 

Sequence of rules.

```php
//
// EBNF: 
//  concat = some1 any2 rule3;
//
new Concatenation(<ID>, [<ID_1>, <ID_2>, <ID_3>], 'concat');
```

### Repetition

Repeat one or more rules.

```php
//
// EBNF:
//  repeat_zero_or_more = some* ;
//
new Repetition(<ID>, 0, -1, <ID_1>, 'repeat_zero_or_more');

//
// EBNF: 
//  repeat_one_or_more = some+ ;
//
new Repetition(<ID>, 1, -1, <ID_2>, 'repeat one or more');
```

### Terminal

Refers to the token defined in the lexer.

```php
$kept = new Terminal(<ID>, 'T_NUMBER', true);

$skipped = new Terminal(<ID>, 'T_WHITESPACE', false);
```

## Examples

A more complex example of a math:

```ebnf
expression = T_NUMBER operation ( T_NUMBER | expression ) ;
operation = T_PLUS | T_MINUS ;
```

```php
$parser = new Grammar([
    new Concatenation(0, [8, 6, 7], 'expression'),  // expression = T_NUMBER operation ( ... ) ;
    new Alternation(7, [8, 0]),                     // ( T_NUMBER | expression ) ;
    new Alternation(6, [1, 2], 'operation'),        // operation = T_PLUS | T_MINUS ;
    new Token(8, 'T_NUMBER', true),
    new Token(1, 'T_PLUS', true),
    new Token(2, 'T_MINUS', true),
], 'expression');

echo $parser->parse(File::fromSources('2 + 2 - 10 + 1000'));
```

Result:

```xml
<Ast>
  <expression offset="0">
    <T_NUMBER offset="0">2</T_NUMBER>
    <operation offset="2">
      <T_PLUS offset="2">+</T_PLUS>
    </operation>
    <expression offset="4">
      <T_NUMBER offset="4">2</T_NUMBER>
      <operation offset="6">
        <T_MINUS offset="6">-</T_MINUS>
      </operation>
      <expression offset="8">
        <T_NUMBER offset="8">10</T_NUMBER>
        <operation offset="11">
          <T_PLUS offset="11">+</T_PLUS>
        </operation>
        <T_NUMBER offset="13">1000</T_NUMBER>
      </expression>
    </expression>
  </expression>
</Ast>
```

## Abstract Syntax Tree

An abstract syntax tree provides a set of classes 
that can be represented in one of two ways:

- `Railt\Parser\Ast\LeafInterface` - Terminal structures, which are represented inside the grammar as tokens.
- `Railt\Parser\Ast\RuleInterface` - Non-terminal structures that are part of the production of grammar.

The name and location offset (in bytes) are part of their 
common capabilities. However, terminals have the ability to retrieve 
values, and non-terminal contain descendants.

As you can see, each node has the `__toString` method, so the **XML** string
of these rules is just a representation of their internal structure.

## Delegates

Each **Rule** can be represented as its own structure, different from the 
standard. To do this, you only need to define in the 
parser when to delegate this authority.

To begin with, we should specify in the grammar which rule 
or token should delegate its authority to the external class:

```php
//
// BNF 
// operation = T_PLUS | T_MINUS ;
//
$delegates = ['operation' => Operation::class];

$grammar = new Grammar([...], 'operation', $delegates);
```

The definition of such a class might look like this. 
Please note that it must be an implementation `RuleInterface` or `LeafInterface`.

```php
class Operation extends Rule 
{
    public function isMinus(): bool 
    {
        return $this->getChild(0)->getName() === 'T_MINUS';
    }
    
    public function isPlus(): bool 
    {
        return $this->getChild(0)->getName() === 'T_PLUS';
    }
}
```

And now, as an **operation** rule, we get the instance of `Operation` class:

```php
$ast = (new Parser($lexer, $grammar))->parse(File::fromSources('2 + 2'));

$operation = $ast->first('operation'); // Operation::class

$operation->isPlus();   // true
$operation->isMinus();  // false
```

### Environment

You can also cast data and the external environment (or context) 
inside each rule using the environment class:

```php
$sources = File::fromSources('...');

$parser->env(File::class, $source); // Share environment variable

class DelegateExample extends Rule 
{
    public function getFile(): File
    {
        return $this->env(File::class);
    }
}
```

## Finder

For a convenient search by AST structure, we can take 
advantage of the capabilities of the `Finder`. This class provides a 
quick lazy API for querying and finding the right data 
inside an Abstract Syntax Tree.

Each rule already has the ability to execute the requested 
query using the `find` method:

```php
$ast = $parser->parse(File::fromSources('2 + (3 * 4 + (42 - 10))'));

echo $ast->find('T_DIGIT')->first(); 
// T_DIGIT "2"

foreach ($ast->find('group T_DIGIT') as $digit) {
    echo $digit;
    // T_DIGIT "3"
    // T_DIGIT "4"
    // T_DIGIT "42"
    // T_DIGIT "10"
}


echo $ast->find('group > group operation')->first(); 
// T_MINUS "-"


foreach ($ast->find('group > T_DIGIT') as $digit) {
    echo $digit;
    // T_DIGIT "3"
    // T_DIGIT "4"
}
```

Allowed expressions:

- `name` - Defines any node with the specified name.
- `:name` - Defines only tokens (`LeafInterface`) with the specified name.
- `#name` - Defines only rules (`RuleInterface`) with the specified name.
- `*` - Any rule
- ` ` - Whitespace indicates that the next rule can be at any nested depth.
- `>` - Indicates that the next rule can be strictly within the specified.
- `(N)` - Indicates that the following rule may be strictly within the rule with the N (digit) nesting.
