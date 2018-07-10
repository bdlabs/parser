<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser\Trace;

/**
 * Class TraceItem
 */
abstract class TraceItem
{
    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @param int $offset
     * @return TraceItem
     */
    public function at(int $offset): TraceItem
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
