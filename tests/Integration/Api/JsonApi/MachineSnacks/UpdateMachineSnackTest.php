<?php

declare(strict_types=1);

namespace Integration\Api\JsonApi\MachineSnacks;

use Tab\Application\Schema\MachineSnackSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\MachineSnackMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Packages\TestCase\Mother\Entity\WarehouseSnackMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UpdateMachineSnackTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_update_machine_snack(): void
    {
        // Arrange
        $loggedUser = UserMother::courier();
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $machineSnack = MachineSnackMother::fromEntities($machine, $snack);
        $newQuantity = $machineSnack->quantity + Faker::int(50, 99);
        $warehouseSnack = WarehouseSnackMother::fromSnack($snack, $newQuantity);
        $this->loadEntities(
            $loggedUser,
            $machine,
            $snack,
            $warehouseSnack,
            $machineSnack,
        );
        $machineSnackClient = $this->loggedJsonApiClient(
            MachineSnackSchema::class,
            $loggedUser,
        );
        $quantity = $newQuantity - Faker::int(10, 20);

        // Act
        $response = $machineSnackClient->updateResource(
            (string) $machineSnack->id,
            [
                MachineSnackSchema::ATTRIBUTE_QUANTITY => $quantity,
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_NO_CONTENT, $response->statusCode());
    }

    public function test_update_machine_snack_validation(): void
    {
        $loggedUser = UserMother::courier();
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $machineSnack = MachineSnackMother::fromEntities($machine, $snack);
        $warehouseSnack = WarehouseSnackMother::fromSnack($snack);
        $this->loadEntities(
            $machine,
            $snack,
            $machineSnack,
            $warehouseSnack,
            $loggedUser,
        );
        $client = $this->loggedJsonApiClient(
            MachineSnackSchema::class,
            $loggedUser,
        );

        // Act
        $response = $client->updateResource(
            (string) $machineSnack->id,
            [
                MachineSnackSchema::ATTRIBUTE_QUANTITY => $warehouseSnack->quantity + 1,
            ],
        );
        $responseData = $response->response;
        /**
         * @var array{
         *     errors?: array<string, string[]>,
         * } $responseDataContent
         */
        $responseDataContent = $this->jsonSerializer()
            ->decode(
                $responseData->content(),
                true,
            )
        ;
        $errors = $responseDataContent['errors'] ?? [];

        // Assert
        self::assertSame(
            HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            $response->statusCode(),
        );
        self::assertEquals(
            [
                MachineSnackSchema::ATTRIBUTE_QUANTITY => [
                    'Brak wystarczającej ilości produktu na magazynie.',
                ],
            ],
            $errors,
        );
    }
}
