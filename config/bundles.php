<?php

declare(strict_types=1);

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use League\Tactician\Bundle\TacticianBundle;
use Nelmio\ApiDocBundle\NelmioApiDocBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

return [
    FrameworkBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    DebugBundle::class => ['dev' => true],
    MonologBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    WebProfilerBundle::class => ['dev' => true, 'test' => true],
    WebpackEncoreBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    TwigExtraBundle::class => ['all' => true],
    MakerBundle::class => ['dev' => true],
    TacticianBundle::class => ['all' => true],
    NelmioApiDocBundle::class => ['all' => true],
];
