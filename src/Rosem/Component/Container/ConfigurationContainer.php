<?php

namespace Rosem\Component\Container;

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
     * Query string separator
     *
     * @var string $separator
     */
    protected $separator;

    /**
     * ConfigurationContainer constructor.
     *
     * @param array  $definitions
     * @param string $separator
     */
    public function __construct(array $definitions = [], string $separator = '.')
    {
        parent::__construct($definitions);

        $this->separator = $separator;
    }

    /**
     * Create container instance from array configuration.
     *
     * @param array  $definitions
     * @param string $separator
     *
     * @return self
     */
    public static function fromArray(array $definitions, string $separator = '.'): self
    {
        return new static($definitions, $separator);
    }

    /**
     * Create container instance from file configuration.
     *
     * @param string $filename
     * @param string $separator
     *
     * @return self
     * @throws \Exception
     */
    public static function fromFile(string $filename, string $separator = '.'): self
    {
        return self::fromArray(self::getConfigurationFromFile($filename), $separator);
    }

    public function serializeId(array $id): string
    {
        return implode($this->separator, $id);
    }

    public function deserializeId(string $id): array
    {
        return explode($this->separator, $id);
    }

    protected function replaceVars($value)
    {
        if (is_string($value)) {
            $value = preg_replace_callback(self::REGEX_ENV_VAR, function ($matches) {
                return false !== ($envVar = getenv($matches[1])) ? $envVar : $matches[0];
            }, $value);
            $value = preg_replace_callback(self::REGEX_CONFIG_VAR, function ($matches) {
                if ($this->has($matches[1])) {
                    return $this->get($matches[1]);
                }

                return $matches[0];
            }, $value);
        } elseif (is_array($value)) {
            array_walk($value, function (&$value) {
                $value = $this->replaceVars($value);
            });
        }

        return $value;
    }

    /**
     * Recursively check if value exists in array by query
     *
     * @param array $array
     * @param int   $offset
     * @param array $path
     * @param int   $lastIndex
     *
     * @return bool
     */
    protected function internalHas(array &$array, array &$path, int $offset, int $lastIndex): bool
    {
        if (isset($array[$path[$offset]])) {
            $next = $array[$path[$offset]];

            if (null !== $next && $offset < $lastIndex) {
                return $this->internalHas($next, $path, ++$offset, $lastIndex);
            }

            $this->lastDefinition = $next;

            return true;
        }

        return false;
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
     * @throws Exception\ContainerException
     */
    protected function internalGet(array &$array, array &$path, int $offset, int $lastIndex)
    {
        if (isset($array[$path[$offset]])) {
            $next = $array[$path[$offset]];

            if (null !== $next && $offset < $lastIndex) {
                return $this->internalGet($next, $path, ++$offset, $lastIndex);
            }

            return $this->lastDefinition = $next;
        }

        return Exception\ContainerException::notDefined($this->serializeId($path));
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
    protected function internalSet(array &$array, array &$path, int $offset, int $lastIndex, $value): void
    {
        $placeholder = &$this->getReference($array, $path, $offset, $lastIndex);
        $placeholder = $value;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        $path = $this->deserializeId($id);
        $lastIndex = count($path) - 1;

        if ($this->internalHas($this->resolvedDefinitions, $path, 0, $lastIndex)) {
            $this->lastId = $id;

            return true;
        }

        if ($this->internalHas($this->definitions, $path, 0, $lastIndex)) {
            $this->lastId = $id;
            $this->lastDefinition = $this->replaceVars($this->lastDefinition);
            $this->internalSet(
                $this->resolvedDefinitions,
                $path,
                0,
                $lastIndex,
                $this->lastDefinition
            );

            return true;
        }

        if ($this->delegate) {
            return $this->delegate->has($id);
        }

        return false;
    }

    /**
     * Select value by query
     *
     * @param string $id
     *
     * @return mixed
     * @throws Exception\ContainerException
     * @throws Exception\NotFoundException
     */
    public function get($id)
    {
        if ($id === $this->lastId) {
            return $this->lastDefinition;
        }

        if ($this->has($id)) {
            if (null !== $this->lastDefinition) {
                return $this->lastDefinition;
            }

            if ($this->delegate) {
                return $this->delegate->get($id);
            }

            return Exception\ContainerException::notDefined($id);
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
        $path = $this->deserializeId($id);
        $this->internalSet($this->definitions, $path, 0, count($path) - 1, $value);
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
