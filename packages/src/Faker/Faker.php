<?php

declare(strict_types=1);

namespace Tab\Packages\Faker;

use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Uid\Ulid;
use Tab\Packages\Constants\Date;

final class Faker
{
    private static ?Generator $generator = null;

    public static function int(int $min = \PHP_INT_MIN, int $max = \PHP_INT_MAX): int
    {
        return \random_int($min, $max);
    }

    public static function nullableInt(int $min = \PHP_INT_MIN, int $max = \PHP_INT_MAX): ?int
    {
        return self::boolean()
            ? self::int($min, $max)
            : null
        ;
    }

    public static function email(): string
    {
        return self::faker()->email();
    }

    public static function safeEmail(): string
    {
        return self::faker()->safeEmail();
    }

    public static function domainEmail(string $domain): string
    {
        if ('' === $domain) {
            throw new \RuntimeException('Domain parameter cannot be empty.');
        }

        $userName = self::faker()->userName();

        return "{$userName}@{$domain}";
    }

    public static function firstName(): string
    {
        return self::faker()->firstName();
    }

    public static function lastName(): string
    {
        return self::faker()->lastName();
    }

    public static function name(): string
    {
        return self::faker()->name();
    }

    /**
     * @template T
     *
     * @param array<int|string,T> $elements
     *
     * @return T
     */
    public static function randomElement(array $elements): mixed
    {
        if ([] === $elements) {
            throw new \RuntimeException('Unable to pick random element from empty array.');
        }

        $orderedElements = \array_values($elements);
        $randomIndex = self::int(1, \count($orderedElements));

        return $orderedElements[$randomIndex - 1];
    }

    /** @param array<int,mixed>|array<string,mixed> $array */
    public static function randomKey(array $array): int|string|null
    {
        return self::randomElement(\array_keys($array));
    }

    /**
     * @template T
     *
     * @param T[] $elements
     *
     * @return T[]
     */
    public static function randomElements(
        array $elements,
        int $count = 1,
        bool $allowDuplicates = false,
    ): array {
        return self::faker()->randomElements(
            $elements,
            $count,
            $allowDuplicates,
        );
    }

    public static function password(): string
    {
        return self::faker()->password();
    }

    public static function hexBytes(int $length = 16): string
    {
        $bytesLength = \ceil($length / 2);
        /** @var int<1,max> $bytesLengthInt */
        $bytesLengthInt = (int) $bytesLength;
        $bytes = \random_bytes($bytesLengthInt);
        $hexBytes = \bin2hex($bytes);

        return \substr(
            $hexBytes,
            0,
            $length,
        );
    }

    /** @phpstan-impure */
    public static function boolean(): bool
    {
        return self::faker()->boolean();
    }

    public static function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    /** @return string[] */
    public static function wordsArray(int $wordsCount = 3): array
    {
        /** @var string[] $words */
        $words = self::faker()->words($wordsCount);

        return $words;
    }

    public static function avatarFilePath(): string
    {
        $extension = self::randomElement(['png', 'jpg']);

        return self::hexBytes(2) . '/' . self::hexBytes() . ".{$extension}";
    }

    public static function words(int $wordsCount): string
    {
        /** @var string $words */
        $words = self::faker()->words($wordsCount, true);

        return $words;
    }

    public static function word(): string
    {
        return self::words(1);
    }

    public static function text(int $maxCharacters = 200): string
    {
        return self::faker()->text($maxCharacters);
    }

    public static function nullableText(int $maxCharacters = 200): ?string
    {
        return self::boolean()
            ? self::text($maxCharacters)
            : null
        ;
    }

    public static function city(): string
    {
        return self::faker()->city();
    }

    public static function street(): string
    {
        return 'ul. ' . self::faker()->streetName();
    }

    public static function dateTimeBetween(
        string $startDate = '-30 years',
        string $endDate = 'now',
    ): \DateTimeImmutable {
        $date = self::faker()->dateTimeBetween($startDate, $endDate);

        return \DateTimeImmutable::createFromMutable($date);
    }

    public static function facebookId(): string
    {
        return (string) self::int(1);
    }

    public static function intId(): int
    {
        return self::int(1, 999_999);
    }

    public static function nullableIntId(): ?int
    {
        return self::boolean()
            ? self::intId()
            : null
        ;
    }

    public static function stringNumberId(): string
    {
        return (string) self::int(1, 999_999);
    }

    public static function float(
        ?int $maxDecimals = null,
        float $min = 0.0,
        ?float $max = null,
    ): float {
        return self::faker()->randomFloat(
            $maxDecimals,
            $min,
            $max,
        );
    }

    public static function phoneNumber(): string
    {
        return (string) self::int(111_111_111, 999_999_999);
    }

    public static function url(): string
    {
        return self::faker()->url();
    }

    public static function nullableUrl(): ?string
    {
        return self::boolean()
            ? self::url()
            : null
        ;
    }

    public static function uri(): string
    {
        $uri = \parse_url(self::url(), \PHP_URL_PATH);

        if (false === $uri || null === $uri) {
            throw new \RuntimeException('Unable to generate uri.');
        }

        return $uri;
    }

    public static function birthday(): \DateTimeImmutable
    {
        return self::dateTimeBetween('-99 years', '-13 years');
    }

    public static function company(): string
    {
        return self::faker()->company();
    }

    public static function jobTitle(): string
    {
        return self::faker()->jobTitle();
    }

    public static function ulid(): string
    {
        return \strtolower(Ulid::generate());
    }

    public static function html(): string
    {
        return self::faker()->randomHtml();
    }

    public static function prizeCreationDate(): \DateTimeImmutable
    {
        $now = new \DateTimeImmutable();
        $grandFinal = new \DateTimeImmutable('2023-04-21 18:00:00');
        if ($now >= $grandFinal) {
            return self::dateTimeBetween(
                '2023-01-01 00:00:00',
                '2023-12-31 23:59:59',
            );
        }

        return self::dateTimeBetween(
            '2022-01-01 00:00:00',
            '2022-12-31 23:59:59',
        );
    }

    public static function ipv4(): string
    {
        return self::faker()->ipv4();
    }

    public static function postalCode(): string
    {
        return self::faker()->postcode();
    }

    public static function hexColor(): string
    {
        return self::faker()->hexColor();
    }

    public static function partnerId(): string
    {
        $edition = self::edition();
        $rawPartnerId = self::partnerIdWithoutEdition();

        return "{$rawPartnerId}{$edition}";
    }

    public static function partnerIdWithoutEdition(): string
    {
        return \substr(
            self::word() . self::word(),
            0,
            11,
        );
    }

    public static function edition(
        ?\DateTimeImmutable $year = null,
    ): string {
        $year ??= self::dateTimeBetween('-21 years', '+87 years');
        $nextYear = $year->modify('+1 year');
        $yearsString = $year->format(Date::YEAR_SHORT_FORMAT) . $nextYear->format(Date::YEAR_SHORT_FORMAT);

        return $yearsString;
    }

    public static function fileExtension(): string
    {
        return self::faker()->fileExtension();
    }

    public static function fileName(): string
    {
        $length = self::int(1, 20);
        $name = self::hexBytes($length);
        $extension = self::fileExtension();

        return "{$name}.{$extension}";
    }

    public static function mimeType(): string
    {
        return self::faker()->mimeType();
    }

    public static function nonDaylightTimeSavingDate(string $endDateModify): \DateTimeImmutable
    {
        $maxRetries = 100;
        $iterations = 0;
        do {
            $date = self::dateTimeBetween();
            $beginDate = $date->setTime(0, 0);
            $endDate = $date->modify($endDateModify);
            $timezone = $date->getTimezone();
            $transitions = $timezone->getTransitions(
                $beginDate->getTimestamp(),
                $endDate->getTimestamp(),
            );
            $hasTransition = \count($transitions) > 1;
            ++$iterations;
        } while ($hasTransition && $iterations < $maxRetries);

        if ($iterations > $maxRetries) {
            throw new \RuntimeException("Unable to generate satisfying date in {$iterations} iterations.");
        }

        return $date;
    }

    private static function faker(): Generator
    {
        return self::$generator ??= Factory::create('pl_PL');
    }
}
