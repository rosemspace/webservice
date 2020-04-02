<?php
declare(strict_types=1);

namespace Rosem\Component\Container;

use ArrayAccess;
use Countable;
use Rosem\Component\Container\Exception;

class ConfigurationContainer extends AbstractContainer implements ArrayAccess, Countable
{
    protected const REGEX_CONFIG_VAR = '/\${([a-zA-Z0-9_.-]+)}/';

    protected const REGEX_ENV_VAR = '/^([A-Z]+[A-Z0-9_]*)$/';

    /**
     * Resolve definitions.
     *
     * @var mixed[]
     */
    protected array $resolvedDefinitions = [];

    /**
     * Last resolved id.
     *
     * @var string
     */
    protected ?string $lastId = null;

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
    protected string $separator;

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
     * @throws Exception\ContainerException
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
            $value = preg_replace_callback(self::REGEX_ENV_VAR, static function ($matches) {
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
     * Recursively check if the value exists in the array by the path.
     *
     * @param array $array
     * @param int   $offset
     * @param array $path
     * @param int   $lastIndex
     *
     * @return bool
     */
    protected function hasByPath(array &$array, array &$path, int $offset, int $lastIndex): bool
    {
        if (isset($array[$path[$offset]])) {
            $next = $array[$path[$offset]];

            if (null !== $next && $offset < $lastIndex) {
                return $this->hasByPath($next, $path, ++$offset, $lastIndex);
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

        throw Exception\ContainerException::forUndefinedEntry($this->serializeId($path));
    }

    /**
     * Recursively getting an array item reference by the path.
     *
     * @param array   $array
     * @param integer $offset
     * @param         $path
     * @param         $lastIndex
     *
     * @return mixed
     */
    protected function &getRefByPath(array &$array, array &$path, int $offset, int $lastIndex)
    {
        $next = &$array[$path[$offset]];
        $next = $next ?? [];

        if ($offset < $lastIndex) {
            return $this->getRefByPath($next, $path, ++$offset, $lastIndex);
        }

        return $next;
    }

    /**
     * Set the value by the path.
     *
     * @param array $array
     * @param array $path
     * @param int   $offset
     * @param int   $lastIndex
     * @param mixed $value
     */
    protected function setByPath(array &$array, array &$path, int $offset, int $lastIndex, $value): void
    {
        $ref = &$this->getRefByPath($array, $path, $offset, $lastIndex);
        $ref = $value;
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

        if ($this->hasByPath($this->resolvedDefinitions, $path, 0, $lastIndex)) {
            $this->lastId = $id;

            return true;
        }

        if ($this->hasByPath($this->definitions, $path, 0, $lastIndex)) {
            $this->lastId = $id;
            $this->lastDefinition = $this->replaceVars($this->lastDefinition);
            $this->setByPath(
                $this->resolvedDefinitions,
                $path,
                0,
                $lastIndex,
                $this->lastDefinition
            );

            return true;
        }

        if ($this->delegate !== null) {
            if ($this->delegate->has($id)) {
//                $this->lastDefinition = $this->delegate->get($id);

                return true;
            }
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

        $this->lastDefinition = null;

        // Resolving
        if ($this->has($id)) {
            if (null !== $this->lastDefinition) {
                return $this->lastDefinition;
            }

            if ($this->delegate !== null) {
                return $this->delegate->get($id);
            }

            throw Exception\ContainerException::forUndefinedEntry($id);
        }

//        if ($this->delegate !== null) {
//            return $this->delegate->get($id);
//        }

        throw Exception\NotFoundException::dueToMissingEntry($id);
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
        $this->setByPath($this->definitions, $path, 0, count($path) - 1, $value);
    }

    /**
     * Extend definitions.
     *
     * @param mixed[] $definitions
     *
     * @return self
     */
    public function extend(array ...$definitions): self
    {
        $this->definitions = array_replace_recursive($this->definitions, ...$definitions);

        return $this;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean
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
