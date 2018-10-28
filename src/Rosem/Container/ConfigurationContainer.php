<?php

namespace Rosem\Container;

use ArrayAccess;
use Countable;

class ConfigurationContainer extends AbstractContainer implements ArrayAccess, Countable
{
    protected const REGEX_VAR = '/\${([a-zA-Z0-9_.-]+)}/';

    /**
     * Last resolved id.
     *
     * @var string
     */
    protected $currentId;

    /**
     * Last resolved definition.
     *
     * @var mixed
     */
    protected $currentDefinition;

    /**
     * Query string delimiter
     *
     * @var string $delimiter
     */
    protected $delimiter;

    /**
     * ArraySeparatorQuery constructor.
     *
     * @param array  $definitions
     * @param string $delimiter
     */
    public function __construct(array $definitions = [], string $delimiter = '.')
    {
        $this->definitions = $definitions;
        $this->delimiter = $delimiter;
    }

    /**
     * Create container instance from array configuration.
     *
     * @param array  $definitions
     * @param string $delimiter
     *
     * @return self
     */
    public static function fromArray(array $definitions, string $delimiter = '.'): self
    {
        return new static($definitions, $delimiter);
    }

    /**
     * Create container instance from file configuration.
     *
     * @param string $filename
     * @param string $delimiter
     *
     * @return self
     * @throws \Exception
     */
    public static function fromFile(string $filename, string $delimiter = '.'): self
    {
        return self::fromArray(self::getConfigurationFromFile($filename), $delimiter);
    }

    protected function replaceVars($value)
    {
        if (\is_string($value) && strpos($value, '$') !== false) {
            $value = preg_replace_callback(self::REGEX_VAR, function ($matches) {
                if ($this->has($matches[1])) {
                    return $this->get($matches[1]);
                }

                return $matches[0];
            }, $value);
        }

        return $value;
    }

    /**
     * Recursively getting value from array by query
     *
     * @param array $array
     * @param int   $offset
     * @param array $path
     * @param int   $lastIndex
     *
     * @return mixed
     * @throws Exception\NotFoundException
     */
    protected function getValue(array &$array, array &$path, int $offset, int &$lastIndex)
    {
        $next = &$array[$path[$offset]];

        if ($next && $offset < $lastIndex) {
            return $this->getValue($next, $path, ++$offset, $lastIndex);
        }

        return $next ? $this->replaceVars($next) : null;
    }

    /**
     * Recursively getting array item reference by query
     *
     * @param array   $array
     * @param integer $offset
     * @param         $path
     * @param         $lastIndex
     *
     * @return mixed
     */
    protected function &getReference(array &$array, array &$path, int $offset, int &$lastIndex)
    {
        $next = &$array[$path[$offset]] ?? [];

        if ($offset < $lastIndex) {
            return $this->getReference($next, $path, ++$offset, $lastIndex);
        }

        return $next;
    }

    /**
     * @param string $id
     *
     * @return bool
     * @throws Exception\NotFoundException
     */
    public function has($id)
    {
        $path = explode($this->delimiter, $id);
        $lastIndex = \count($path) - 1;
        $this->currentDefinition = $this->getValue($this->definitions, $path, 0, $lastIndex);

        return (bool)$this->currentDefinition;
    }

    /**
     * Select value by query
     *
     * @param string $id
     *
     * @return mixed
     * @throws Exception\NotFoundException
     */
    public function get($id)
    {
        if ($this->currentId === $id) {
            return $this->currentDefinition;
        }

        $this->currentId = $id;

        if ($this->has($id)) {
            return $this->currentDefinition;
        }

        if ($this->delegate) {
            return $this->delegate->get($id);
        }

        return Exception\NotFoundException::notFound($id);
    }

    /**
     * Set value by query
     *
     * @param string $id
     * @param mixed  $value
     */
    public function set(string $id, $value): void
    {
        $path = explode($this->delimiter, $id);
        $lastIndex = \count($path) - 1;
        $placeholder = &$this->getReference($this->definitions, $path, 0, $lastIndex);
        $placeholder = $value;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean
     * @throws Exception\NotFoundException
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->definitions[$offset] ?? null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        null === $offset ? $this->definitions[] = $value : $this->definitions[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->definitions[$offset]);
    }

    /**
     * Count elements of an object.
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return \count($this->definitions);
    }
}
