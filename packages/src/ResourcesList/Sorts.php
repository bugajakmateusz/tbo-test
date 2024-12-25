<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

final class Sorts
{
    /** @var Sort[] */
    private readonly array $sorts;

    private function __construct(Sort ...$sorts)
    {
        $this->sorts = $sorts;
    }

    public static function fromJsonApiString(string $sortsString): self
    {
        $sorts = \explode(',', $sortsString);
        $filteredSorts = \array_filter(
            $sorts,
            static fn (string $include): bool => '' !== $include,
        );

        return new self(
            ...\array_map(
                static fn (string $sort): Sort => Sort::fromJsonApiString($sort),
                $filteredSorts,
            ),
        );
    }

    public function checkSupportedSorts(string ...$supportedSorts): void
    {
        $sortsNames = \array_map(
            static fn (Sort $sort): string => $sort->name(),
            $this->sorts,
        );

        $unsupportedSorts = \array_diff($sortsNames, $supportedSorts);

        if (\count($unsupportedSorts) > 0) {
            $unsupportedSortsString = \implode("', '", $unsupportedSorts);
            $supportedSortsString = \implode("', '", $supportedSorts);

            throw new \RuntimeException(
                "Unsupported sorts found: '{$unsupportedSortsString}', try one of these: '{$supportedSortsString}'.",
            );
        }
    }

    public function has(string $filterName): bool
    {
        $filters = $this->findSortsByName($filterName);

        return \count($filters) >= 1;
    }

    /** @return Sort[] */
    public function toArray(): array
    {
        return $this->sorts;
    }

    /** @return Sort[] */
    private function findSortsByName(string $name): array
    {
        return \array_filter(
            $this->sorts,
            static fn (Sort $sort): bool => $sort->name() === $name,
        );
    }
}
