<?php

declare(strict_types=1);

namespace Flow\ETL\Row;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row;
use Flow\ETL\Row\Reference\Expression;

/**
 * @implements Reference<array{entry: string, alias: ?string}>
 */
final class EntryReference implements Expression, Reference
{
    use Row\Reference\EntryExpression;

    private ?string $alias = null;

    private SortOrder $sort = SortOrder::ASC;

    public function __construct(private readonly string $entry)
    {
    }

    public static function init(string|self $ref) : self
    {
        if (\is_string($ref)) {
            return new self($ref);
        }

        return $ref;
    }

    public function __serialize() : array
    {
        return [
            'entry' => $this->entry,
            'alias' => $this->alias,
        ];
    }

    public function __toString() : string
    {
        return $this->name();
    }

    public function __unserialize(array $data) : void
    {
        $this->entry = $data['entry'];
        $this->alias = $data['alias'];
    }

    public function as(string $alias) : self
    {
        $this->alias = $alias;

        return $this;
    }

    public function asc() : self
    {
        $this->sort = SortOrder::ASC;

        return $this;
    }

    public function desc() : self
    {
        $this->sort = SortOrder::DESC;

        return $this;
    }

    public function eval(Row $row) : mixed
    {
        try {
            return $row->valueOf($this->entry);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    public function hasAlias() : bool
    {
        return $this->alias !== null;
    }

    public function is(Reference $ref) : bool
    {
        return $this->name() === $ref->name();
    }

    public function name() : string
    {
        return $this->alias ?? $this->entry;
    }

    public function sort() : SortOrder
    {
        return $this->sort;
    }

    public function to() : string
    {
        return $this->entry;
    }
}
