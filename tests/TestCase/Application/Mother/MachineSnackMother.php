<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Packages\Faker\Faker;
use Tab\Tests\TestCase\Application\Mock\FakeClock;
use Tab\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

final class MachineSnackMother
{
    public static function random(): MachineSnack
    {
        return self::create();
    }

    public static function withPosition(string $position): MachineSnack
    {
        return self::create($position);
    }

    public static function withQuantity(
        int $quantity,
        ?string $position = null,
    ): MachineSnack {
        $propertyManipulator = PropertyManipulator::getInstance();
        $snackMachine = self::create($position);
        $propertyManipulator->propertySet(
            $snackMachine,
            'quantity',
            $quantity,
        );

        return $snackMachine;
    }

    private static function create(
        ?string $position = null,
    ): MachineSnack {
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $clock = FakeClock::getInstance();
        $positionVO = SnackPosition::fromString(
            $position ?? Faker::hexBytes(3),
        );

        return MachineSnack::create(
            $machine,
            $snack,
            Faker::int(1, 10),
            $positionVO,
            $clock,
        );
    }
}
