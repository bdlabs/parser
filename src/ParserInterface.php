<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser;

use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;

/**
 * Interface ParserInterface
 */
interface ParserInterface
{
    /**
     * @return Environment
     */
    public function env(): Environment;

    /**
     * @return GrammarInterface
     */
    public function grammar(): GrammarInterface;

    /**
     * @param Readable $input
     * @return iterable
     */
    public function trace(Readable $input): iterable;

    /**
     * @param Readable $input
     * @return RuleInterface
     */
    public function parse(Readable $input): RuleInterface;
}
