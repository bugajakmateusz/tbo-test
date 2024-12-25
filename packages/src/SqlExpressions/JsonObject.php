<?php

declare(strict_types=1);

namespace Polsl\Packages\SqlExpressions;

final class JsonObject
{
    /** @var array<string,string> */
    private array $fields = [];
    private string $alias = '';

    public function addField(string $name, string $value): self
    {
        $this->fields[$name] = $value;

        return $this;
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function toString(): string
    {
        if (empty($this->fields)) {
            throw new \RuntimeException('At least one field is required.');
        }

        $values = [];
        foreach ($this->fields as $name => $value) {
            $values[] = "'{$name}'";
            $values[] = $value;
        }

        $valuesString = \implode(', ', $values);
        $jsonObjectString = "JSON_BUILD_OBJECT({$valuesString})";

        if ('' !== $this->alias) {
            $jsonObjectString .= " AS {$this->alias}";
        }

        return $jsonObjectString;
    }
}
