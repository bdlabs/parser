<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser;

use Railt\Parser\Builder\Definition\Alternation;
use Railt\Parser\Builder\Definition\Concatenation;
use Railt\Parser\Builder\Definition\Lexeme;
use Railt\Parser\Builder\Definition\Repetition;
use Railt\Parser\Builder\DefinitionInterface;
use Railt\Parser\Builder\ProvidesGrammar;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface extends ProvidesGrammar
{
    /**
     * @param string|int $name
     * @return BuilderInterface|$this
     */
    public function startsAt($name): BuilderInterface;

    /**
     * @param iterable $definitions
     * @return BuilderInterface|$this
     */
    public function addMany(iterable $definitions): BuilderInterface;

    /**
     * @param DefinitionInterface $definition
     * @return BuilderInterface|$this
     */
    public function add(DefinitionInterface $definition): BuilderInterface;
}
