<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Neomerx;

use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaInterface;
use Neomerx\JsonApi\Factories\Factory;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\Schema\MachineSnackSchema;
use Polsl\Application\Schema\SnackPriceSchema;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Application\Schema\UserSchema;
use Polsl\Application\View\MachineSnackView;
use Polsl\Application\View\MachineView;
use Polsl\Application\View\PriceView;
use Polsl\Application\View\SnackView;
use Polsl\Application\View\UserView;
use Polsl\Packages\JsonApi\Infrastructure\NeomerxSchemaAdapter;
use Psr\Container\ContainerInterface;

final class NeomerxLazySchemaContainer implements SchemaContainerInterface
{
    /** @var array<class-string,class-string> */
    public const TYPE_SCHEMAS_MAP = [
        MachineView::class => MachineSchema::class,
        SnackView::class => SnackSchema::class,
        UserView::class => UserSchema::class,
        MachineSnackView::class => MachineSnackSchema::class,
        PriceView::class => SnackPriceSchema::class,
    ];

    /** @var array<class-string,SchemaInterface> */
    private array $schemas = [];

    public function __construct(
        private readonly ContainerInterface $schemaLocator,
        private readonly Factory $factory,
    ) {}

    public function getSchema($resourceObject): SchemaInterface
    {
        $type = $resourceObject::class;
        if (!\array_key_exists($type, self::TYPE_SCHEMAS_MAP)) {
            throw new \RuntimeException("Unable to find schema for class '{$type}'.");
        }

        /** @var class-string $schemaClass */
        $schemaClass = self::TYPE_SCHEMAS_MAP[$type];

        return $this->fetchSchema($schemaClass);
    }

    public function hasSchema($resourceObject): bool
    {
        return \is_object($resourceObject)
            && \array_key_exists($resourceObject::class, self::TYPE_SCHEMAS_MAP)
        ;
    }

    /** @param class-string $schemaClass */
    private function fetchSchema(string $schemaClass): SchemaInterface
    {
        if (!\array_key_exists($schemaClass, $this->schemas)) {
            $hasSchema = $this->schemaLocator
                ->has($schemaClass)
            ;

            if (!$hasSchema) {
                throw new \RuntimeException("Schema '{$schemaClass}' is not registered in schemas locator.");
            }

            /** @var \Polsl\Packages\JsonApi\Contracts\SchemaInterface $schema */
            $schema = $this->schemaLocator
                ->get($schemaClass)
            ;

            $this->schemas[$schemaClass] = new NeomerxSchemaAdapter(
                $this->factory,
                $schema,
            );
        }

        return $this->schemas[$schemaClass];
    }
}
