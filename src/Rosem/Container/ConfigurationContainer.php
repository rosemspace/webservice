<?php

namespace Rosem\Container;

use ArrayAccess;
use Countable;
use function count;

class ConfigurationContainer extends AbstractContainer implements ArrayAccess, Countable
{
    protected const REGEX_CONFIG_VAR = '/\${([a-zA-Z0-9_.-]+)}/';

    protected const REGEX_ENV_VAR = '/^([A-Z]+[A-Z0-9_]*)$/';

    /**
     * Resolve definitions.
     *
     * @var mixed[]
     */
    protected $resolvedDefinitions = [];

    /**
     * Last resolved id.
     *
     * @var string
     */
    protected $lastId;

    /**
     * Last resolved definition.
     *
     * @var mixed
     */
    protected $lastDefinition;

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
        parent::__construct($definitions);
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
        if (\is_string($value)) {
            $value = preg_replace_callback(self::REGEX_ENV_VAR, function ($matches) {
                return false !== ($envVar = getenv($matches[1])) ? $envVar : $matches[0];
            }, $value);
            $value = preg_replace_callback(self::REGEX_CONFIG_VAR, function ($matches) {
                if ($this->has($matches[1])) {
                    return $this->get($matches[1]);
                }

                return $matches[0];
            }, $value);
        } elseif (\is_array($value)) {
            array_walk($value, function (&$value) {
                $value = $this->replaceVars($value);
            });
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
    protected function getValue(array &$array, array &$path, int $offset, int $lastIndex)
    {
        $next = $array[$path[$offset]] ?? null;

        if (null !== $next && $offset < $lastIndex) {
            return $this->getValue($next, $path, ++$offset, $lastIndex);
        }

        return $next;
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
    protected function replaceVarsAndGetValue(array &$array, array &$path, int $offset, int $lastIndex)
    {
        $value = $this->getValue($array, $path, $offset, $lastIndex);

        return $value ? $this->replaceVars($value) : null;
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
    protected function &getReference(array &$array, array &$path, int $offset, int $lastIndex)
    {
        $next = &$array[$path[$offset]];
        $next = $next ?? [];

        if ($offset < $lastIndex) {
            return $this->getReference($next, $path, ++$offset, $lastIndex);
        }

        return $next;
    }

    /**
     * Set value.
     *
     * @param array $array
     * @param array $path
     * @param int   $offset
     * @param int   $lastIndex
     * @param mixed $value
     */
    protected function setValue(array &$array, array &$path, int $offset, int $lastIndex, $value): void
    {
        $placeholder = &$this->getReference($array, $path, $offset, $lastIndex);
        $placeholder = $value;
    }

    /**
     * @param string $id
     *
     * @return bool
     * @throws Exception\NotFoundException
     */
    public function has($id): bool
    {
        $path = explode($this->delimiter, $id);
        $lastIndex = count($path) - 1;

        $this->lastDefinition =
            $this->getValue($this->resolvedDefinitions, $path, 0, $lastIndex);

        if (null !== $this->lastDefinition) {
            return $this->lastDefinition;
        }

        $this->lastDefinition =
            $this->replaceVarsAndGetValue($this->definitions, $path, 0, $lastIndex);

        if (null !== $this->lastDefinition) {
            $this->setValue(
                $this->resolvedDefinitions,
                $path,
                0,
                $lastIndex,
                $this->lastDefinition
            );

            return true;
        }

        return false;
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
        if ($this->lastId === $id) {
            return $this->lastDefinition;
        }

        $this->lastId = $id;

        if ($this->has($id)) {
            return $this->lastDefinition;
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
        $this->setValue($this->definitions, $path, 0, count($path) - 1, $value);
    }

    /**
     * Extend definitions.
     *
     * @param array[] $definitions
     *
     * @return self
     */
    public function extend(array ...$definitions): self
    {
        $this->definitions = array_merge_recursive($this->definitions, ...$definitions);

        return $this;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean
     * @throws Exception\NotFoundException
     */
    public function offsetExists($offset): bool
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
    public function count(): int
    {
        return count($this->definitions);
    }
}
