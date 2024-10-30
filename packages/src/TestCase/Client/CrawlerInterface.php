<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Client;

interface CrawlerInterface
{
    public static function fromData(
        string $uri,
        string $content,
        string $type,
    ): self;

    public function filter(string $selector): self;

    public function count(): int;

    public function text(): string;

    public function eq(int $position): self;

    public function children(string $selector = null): self;

    public function attribute(string $name): ?string;

    /** @return mixed[] */
    public function each(\Closure $closure): array;

    public function last(): self;

    public function nodeName(): string;
}
