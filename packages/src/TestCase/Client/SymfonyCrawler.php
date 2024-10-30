<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Client;

use Symfony\Component\DomCrawler\Crawler;

final class SymfonyCrawler implements CrawlerInterface
{
    public function __construct(private readonly Crawler $innerCrawler)
    {
    }

    public static function fromData(
        string $uri,
        string $content,
        string $type,
    ): CrawlerInterface {
        $crawler = new Crawler(uri: $uri);
        $crawler->addContent($content, $type);

        return new self($crawler);
    }

    public function filter(string $selector): CrawlerInterface
    {
        $crawler = $this->innerCrawler
            ->filter($selector)
        ;

        return self::fromSymfonyCrawler($crawler);
    }

    public function count(): int
    {
        return $this->innerCrawler
            ->count()
        ;
    }

    /** @return \Traversable<\DOMNode> */
    public function getIterator(): \Traversable
    {
        return $this->innerCrawler
            ->getIterator()
        ;
    }

    public function text(): string
    {
        return $this->innerCrawler
            ->text()
        ;
    }

    public function eq(int $position): CrawlerInterface
    {
        $crawler = $this->innerCrawler
            ->eq($position)
        ;

        return new self($crawler);
    }

    public function children(string $selector = null): CrawlerInterface
    {
        $children = $this->innerCrawler
            ->children($selector)
        ;

        return new self($children);
    }

    public function attribute(string $name): ?string
    {
        return $this->innerCrawler
            ->attr($name)
        ;
    }

    public function each(\Closure $closure): array
    {
        $data = [];
        foreach ($this->innerCrawler as $i => $node) {
            $crawler = self::fromSymfonyCrawler(new Crawler($node));
            $data[] = $closure($crawler, $i);
        }

        return $data;
    }

    public function last(): CrawlerInterface
    {
        $crawler = $this->innerCrawler
            ->last()
        ;

        return new self($crawler);
    }

    public function nodeName(): string
    {
        return $this->innerCrawler
            ->nodeName()
        ;
    }

    private static function fromSymfonyCrawler(Crawler $crawler): self
    {
        return new self($crawler);
    }
}
