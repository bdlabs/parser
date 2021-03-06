<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Parser\Impl;

use Railt\Parser\Runtime\Grammar;

/**
 * Class JsonGrammar
 */
class JsonGrammar extends Grammar
{
    /**
     * @var array|int[]
     */
    public $transitional = [
        7, 9, 10,
        16, 18, 19
    ];

    /**
     * @var int
     */
    public $root = 3;

    /**
     * @var array
     */
    public $actions = [
        3, 3, 3, 0, 3,
        3, 3, 2, 3, 1,
        2, 3, 1, 3, 1,
        3, 2, 3, 1, 2,
        3, 1
    ];

    /**
     * @var array|string[]
     */
    public $names = [
        'true',
        'false',
        'null',
        null,
        'string',
        'number',
        'brace_',
        null,
        'comma',
        'object',
        null,
        '_brace',
        'object',
        'colon',
        'pair',
        'bracket_',
        null,
        'comma',
        'array',
        null,
        '_bracket',
        'array',
    ];

    /**
     * @var array
     */
    public $goto = [
        true,
        true,
        true,
        [0, 1, 2, 4, 12, 21, 5],
        true,
        true,
        false,
        [14, 0, 1],
        false,
        [8, 14],
        [9, 0, -1],
        false,
        [6, 7, 10, 11],
        false,
        [4, 13, 3],
        false,
        [3, 0, 1],
        false,
        [17, 3],
        [18, 0, -1],
        false,
        [15, 16, 19, 20],
    ];
}
