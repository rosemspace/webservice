<?php

namespace Rosem\Component\Route;

use function mb_strlen;
use function mb_substr;

class RegexNode
{
    /**
     * @var string
     */
    protected string $prefix;

    /**
     * @var self[]
     */
    protected array $children = [];

    public function __construct(string $prefix = '', array $children = [])
    {
        $this->prefix = $prefix;
        $this->children = $children;
    }

    public function clear(): void
    {
        $this->__construct();
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function addRegex(string $regex): void
    {
        $patternLength = mb_strlen($regex);
        $matchLength = 0;

        foreach ($this->children as $index => &$node) {
            $nodePrefix = $node->getPrefix();
            $nodePrefixLength = mb_strlen($nodePrefix);
            $end = min($patternLength, $nodePrefixLength);
            $groups = 0;
            $ignoreMatchLength = 0;

            for ($matchLength = 0; $matchLength < $end; ++$matchLength) {
                if ($regex[$matchLength] !== $nodePrefix[$matchLength]) {
                    break;
                }

                if ('(' === $regex[$matchLength] || '(' === $nodePrefix[$matchLength]) {
                    ++$groups;
                } elseif (')' === $regex[$matchLength] || ')' === $nodePrefix[$matchLength]) {
                    --$groups;
                }

                if ($groups > 0) {
                    ++$ignoreMatchLength;
                } elseif ((isset($regex[$matchLength + 1]) && '?' === $regex[$matchLength + 1])
                    || (isset($nodePrefix[$matchLength + 1]) && '?' === $nodePrefix[$matchLength + 1])
//                    || '/' === $nodePrefix[$matchLength]
//                    || '/' === $regex[$matchLength]
                ) {
                    ++$ignoreMatchLength;
                } else {
                    $ignoreMatchLength = 0;
                }
            }

            $matchLength -= $ignoreMatchLength;

            if ($matchLength) {
                if ($matchLength !== $nodePrefixLength) {
                    $newPrefix = mb_substr($nodePrefix, 0, $matchLength);
                    $newChild = mb_substr($nodePrefix, $matchLength);
                    $node = new self($newPrefix, [new self($newChild, $node->getChildren())]);
                } elseif (!$node->hasChildren()) {
                    $node->addRegex('');
                }

                $node->addRegex(mb_substr($regex, $matchLength));

                break;
            }
        }

        // todo: add rollback

        unset($node);

        if (!$matchLength) {
            $this->children[] = new self($regex);
        }
    }

    public function getRegex(): string
    {
        $regex = $this->prefix;

        if ($this->hasChildren()) {
            $regex .= '(?';

            foreach ($this->children as $node) {
                $regex .= '|' . $node->getRegex();
            }

            $regex .= ')';
        }

        return $regex;
    }
}
