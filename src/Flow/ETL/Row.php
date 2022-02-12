<?php

declare(strict_types=1);

namespace Flow\ETL;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row\Entries;
use Flow\ETL\Row\Entry;
use Flow\Serializer\Serializable;

/**
 * @psalm-immutable
 */
final class Row implements Serializable
{
    private Entries $entries;

    public function __construct(Entries $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @psalm-pure
     */
    public static function create(Entry ...$entries) : self
    {
        return new self(new Entries(...$entries));
    }

    /**
     * @return array{entries: Entries}
     */
    public function __serialize() : array
    {
        return ['entries' => $this->entries];
    }

    /**
     * @param array{entries: Entries} $data
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function __unserialize(array $data) : void
    {
        $this->entries = $data['entries'];
    }

    public function entries() : Entries
    {
        return $this->entries;
    }

    /**
     * @throws RuntimeException
     */
    public function get(string $name) : Entry
    {
        return $this->entries->get($name);
    }

    /**
     * @psalm-suppress MissingReturnType
     * @phpstan-ignore-next-line
     */
    public function valueOf(string $name)
    {
        return $this->get($name)->value();
    }

    public function set(Entry ...$entries) : self
    {
        return new self($this->entries->set(...$entries));
    }

    public function remove(string ...$names) : self
    {
        $namesToRemove = [];

        foreach ($names as $name) {
            if ($this->entries->has($name)) {
                $namesToRemove[] = $name;
            }
        }

        return new self($this->entries->remove(...$namesToRemove));
    }

    /**
     * @param callable(Entry) : Entry $mapper
     */
    public function map(callable $mapper) : self
    {
        return new self(new Entries(...$this->entries->map($mapper)));
    }

    public function rename(string $currentName, string $newName) : self
    {
        return new self($this->entries->rename($currentName, $newName));
    }

    /**
     * @param callable(Entry) : bool $callable
     */
    public function filter(callable $callable) : self
    {
        return new self($this->entries->filter($callable));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray() : array
    {
        return $this->entries->toArray();
    }

    public function isEqual(self $row) : bool
    {
        return $this->entries->isEqual($row->entries());
    }

    public function add(Entry ...$entries) : self
    {
        return new self($this->entries->add(...$entries));
    }

    public function sortEntries() : self
    {
        return new self($this->entries->sort());
    }
}
