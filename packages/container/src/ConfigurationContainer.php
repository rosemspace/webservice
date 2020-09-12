<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Rosem\Component\Container\Exception;

class ConfigurationContainer extends AbstractContainer
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
     * @var string|null
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
    protected function __construct(array $definitions = [], string $separator = '.')
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

    protected function deserializeId(string $id): array
    {
        return explode($this->separator, $id);
    }

    protected function replaceVars($value)
    {
        if (is_string($value)) {
            $value = preg_replace_callback(
                self::REGEX_ENV_VAR,
                static function ($matches) {
                    return false !== ($envVar = getenv($matches[1])) ? $envVar : $matches[0];
                },
                $value
            );
            $value = preg_replace_callback(
                self::REGEX_CONFIG_VAR,
                function ($matches) {
                    if ($this->has($matches[1])) {
                        return $this->get($matches[1]);
                    }

                    return $matches[0];
                },
                $value
            );
        } elseif (is_array($value)) {
            array_walk(
                $value,
                function (&$value) {
                    $value = $this->replaceVars($value);
                }
            );
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
     * @inheritDoc
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

        if ($this->child !== null && $this->child->has($id)) {
            $this->lastId = $id;
            $this->lastDefinition = $this->child->get($id);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if ($id === $this->lastId) {
            return $this->lastDefinition;
        }

        // Resolving
        if ($this->has($id)) {
            if (null !== $this->lastDefinition) {
                return $this->lastDefinition;
            }

            throw Exception\ContainerException::forUndefinedEntry($id);
        }

        throw Exception\NotFoundException::dueToMissingEntry($id);
    }

    /**
     * @inheritDoc
     */
    protected function set(string $id, $value): void
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
}
