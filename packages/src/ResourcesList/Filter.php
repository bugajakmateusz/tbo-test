<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList;

use Tab\Domain\SanitizedString;

final class Filter
{
    public const FORMATTER_LOWER = 'filter_lower';
    public const FORMATTER_TRIM = 'filter_trim';
    public const SANITIZER_STRING = 'sanitizer_string';

    /** @param null|array<mixed, mixed>|bool|int|string $value */
    public function __construct(
        private readonly string $name,
        private readonly bool|array|int|string|null $value,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return null|bool|int|mixed[]|string */
    public function value(): array|int|string|null|bool
    {
        return $this->value;
    }

    /** @return array<mixed,mixed> */
    public function arrayValue(): array
    {
        return (array) $this->value;
    }

    public function intValue(bool $absolute = true): int
    {
        $value = (int) \filter_var($this->value, \FILTER_SANITIZE_NUMBER_INT);

        if ($absolute) {
            $value = \abs($value);
        }

        return $value;
    }

    public function boolValue(): bool
    {
        return \filter_var($this->value, \FILTER_VALIDATE_BOOL);
    }

    /**
     * @param string[] $sanitizers
     * @param string[] $formatters
     */
    public function stringValue(array $sanitizers = [], array $formatters = []): string
    {
        if (\is_array($this->value)) {
            throw new \RuntimeException('Unable to get string from array value.');
        }

        $sanitizedValue = \array_reduce(
            $sanitizers,
            static fn (string $value, string $sanitizer): string => match ($sanitizer) {
                self::SANITIZER_STRING => SanitizedString::create($value)->toString(),
                default => throw new \RuntimeException("Sanitizer '{$sanitizer}' is not implemented."),
            },
            (string) $this->value,
        );

        return \array_reduce(
            $formatters,
            static fn (string $value, string $formatter): string => match ($formatter) {
                self::FORMATTER_LOWER => \mb_convert_case($value, \MB_CASE_LOWER),
                self::FORMATTER_TRIM => \trim($value),
                default => throw new \RuntimeException("Formatter '{$formatter}' is not implemented."),
            },
            $sanitizedValue,
        );
    }
}
