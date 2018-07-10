<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser\Rule;

/**
 * Class Rule
 */
abstract class Rule
{
    /**
     * Rule name.
     *
     * @var string
     */
    protected $name;

    /**
     * Rule's children. Can be an array of names or a single name.
     *
     * @var int|int[]|string|string[]
     */
    protected $children;

    /**
     * Node ID.
     *
     * @var string
     */
    protected $nodeId;

    /**
     * Default ID.
     *
     * @var string
     */
    protected $defaultId;

    /**
     * Whether the rule is transitional or not (i.e. not declared in the grammar
     * but created by the analyzer).
     *
     * @var bool
     */
    protected $transitional = true;

    /**
     * Rule constructor.
     * @param string|int $name Rule name.
     * @param int|int[]|string|string[] $children Children.
     * @param string $nodeId Node ID.
     */
    public function __construct($name, $children, string $nodeId = null)
    {
        $this->name     = $name;
        $this->children = $children;
        $this->nodeId   = $nodeId;
    }

    /**
     * Get rule name.
     *
     * @return string|int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get rule's children.
     *
     * @return int|int[]|string|string[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get node ID.
     *
     * @return string|null
     */
    public function getNodeId(): ?string
    {
        return $this->nodeId;
    }

    /**
     * Get default ID.
     *
     * @return string|null
     */
    public function getDefaultId(): ?string
    {
        return $this->defaultId;
    }

    /**
     * @param string|null $defaultId
     * @return Rule
     */
    public function setDefaultId(?string $defaultId): Rule
    {
        $this->defaultId = $defaultId;

        return $this;
    }
}