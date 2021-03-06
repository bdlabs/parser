<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser\Runtime\Trace;

/**
 * Class TraceItem
 */
abstract class TraceItem implements TraceInterface
{
    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * TraceItem constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param int $offset
     * @return TraceInterface
     */
    public function at(int $offset): TraceInterface
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
