<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures;

final readonly class ChainEntitiesLoader implements EntitiesLoaderInterface
{
    public function __construct(
        private CustomEntitiesLoader $customEntitiesLoader,
    ) {}

    public function load(object ...$entities): void
    {
        $this->loadEntities(false, ...$entities);
    }

    public function append(object ...$entities): void
    {
        $this->loadEntities(true, ...$entities);
    }

    public function purge(): void
    {
        $this->customEntitiesLoader
            ->purgeTables()
        ;
    }

    private function loadEntities(bool $append = false, object ...$entities): void
    {
        $supportedClasses = self::CUSTOM_ENTITIES;

        foreach ($entities as $entity) {
            $entityClass = $entity::class;
            $isSupportedClass = \in_array(
                $entityClass,
                $supportedClasses,
                true,
            );

            if (!$isSupportedClass) {
                throw new \RuntimeException("Entity class '{$entityClass}' is not supported.");
            }
        }

        $customEntities = $this->filterEntities(self::CUSTOM_ENTITIES, ...$entities);
        $this->customEntitiesLoader
            ->load($append, ...$customEntities)
        ;
    }

    /**
     * @param string[] $allowedTypes
     *
     * @return object[]
     */
    private function filterEntities(array $allowedTypes, object ...$entities): array
    {
        return \array_filter(
            $entities,
            static fn (object $entity): bool => \in_array(
                $entity::class,
                $allowedTypes,
                true,
            ),
        );
    }
}
