<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mock;

use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class FakeSnackRepository implements SnackRepositoryInterface
{
    public function __construct(private ?Snack $snack = null) {}

    public function add(Snack $snack): void {}

    public function get(int $snackId): Snack
    {
        $snack = $this->snack;
        if (null == $snack) {
            throw EntityNotFoundException::create(
                $snackId,
                Snack::class,
            );
        }

        return $snack;
    }

    public function findByName(string $name): ?Snack
    {
        return $this->snack;
    }
}
