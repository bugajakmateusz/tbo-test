<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Packages\Faker\Faker;
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

    public static function withZeroQuantity(string $position): MachineSnack
    {
        $propertyManipulator = PropertyManipulator::getInstance();
        $snackMachine = self::create($position);
        $propertyManipulator->propertySet(
            $snackMachine,
            'quantity',
            0,
        );

        return $snackMachine;
    }

    private static function create(
        ?string $position = null,
    ): MachineSnack {
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $positionVO = SnackPosition::fromString(
            $position ?? Faker::hexBytes(3),
        );

        return MachineSnack::create(
            $machine,
            $snack,
            Faker::intId(),
            $positionVO,
        );
    }
}
