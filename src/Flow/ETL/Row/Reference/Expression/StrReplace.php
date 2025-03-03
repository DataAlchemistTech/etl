<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Reference\Expression;

use Flow\ETL\Row;
use Flow\ETL\Row\Reference\Expression;

final class StrReplace implements Expression
{
    /**
     * @param string|string[] $search
     * @param string|string[] $replace
     */
    public function __construct(
        private readonly Expression $ref,
        private readonly string|array $search,
        private readonly string|array $replace
    ) {
    }

    public function eval(Row $row) : mixed
    {
        /** @var mixed $val */
        $val = $this->ref->eval($row);

        if (!\is_string($val)) {
            return null;
        }

        return \str_replace($this->search, $this->replace, $val);
    }
}
