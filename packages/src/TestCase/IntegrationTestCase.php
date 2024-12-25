<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase;

use http\Exception\RuntimeException;
use Polsl\Infrastructure\Symfony\Security\SymfonyUser;
use Polsl\Packages\DbConnection\DbConnectionInterface;
use Polsl\Packages\HttpResponse\CookieInterface;
use Polsl\Packages\HttpResponse\SymfonyCookie;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\TestCase\Client\CrawlerFactoryInterface;
use Polsl\Packages\TestCase\Client\KernelBrowserInterface;
use Polsl\Packages\TestCase\Fixtures\EntitiesLoaderInterface;
use Polsl\Packages\TestCase\Fixtures\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    public const FIREWALL_MAIN = 'main';

    private static ?JsonSerializerInterface $jsonSerializer = null;

    protected function client(): KernelBrowserInterface
    {
        $container = $this->container();

        /** @var KernelBrowserInterface $kernelBrowser */
        $kernelBrowser = $container->get(KernelBrowserInterface::class);

        return $kernelBrowser;
    }

    protected function loggedClient(User $user, string $firewall): KernelBrowserInterface
    {
        $loginCookie = $this->loginCookie($user, $firewall);
        $client = $this->client();
        $client->addCookie($loginCookie);

        return $client;
    }

    protected function crawlerFactory(): CrawlerFactoryInterface
    {
        $container = $this->container();

        /** @var CrawlerFactoryInterface $crawlerFactory */
        $crawlerFactory = $container->get(CrawlerFactoryInterface::class);

        return $crawlerFactory;
    }

    protected function loadEntities(object ...$objects): void
    {
        $this->entitiesLoader()
            ->load(...$objects)
        ;
    }

    protected function appendEntities(object ...$objects): void
    {
        $this->entitiesLoader()
            ->append(...$objects)
        ;
    }

    protected function purgeEntities(): void
    {
        $this->entitiesLoader()
            ->purge()
        ;
    }

    protected function loginCookie(User $user, string $firewall): CookieInterface
    {
        $sessionFactory = $this->container()
            ->get('session.factory')
        ;
        $session = $sessionFactory->createSession();
        $firewallContext = $firewall;
        $token = $this->createSecurityToken($user, $firewall);

        $session->set('_security_' . $firewallContext, \serialize($token));
        $session->save();

        return new SymfonyCookie(
            Cookie::create(
                $session->getName(),
                $session->getId(),
            ),
        );
    }

    /** @return TestContainer */
    protected function container(): ContainerInterface
    {
        return self::getContainer();
    }

    protected function jsonSerializer(): JsonSerializerInterface
    {
        return self::$jsonSerializer ??= $this->container()
            ->get(JsonSerializerInterface::class)
        ;
    }

    protected function dbConnection(): DbConnectionInterface
    {
        return $this->classService(DbConnectionInterface::class);
    }

    /** @param class-string $id */
    protected function overwriteService(string $id, object $instance): void
    {
        $container = $this->container();
        $container->set($id, $instance);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $serviceName
     *
     * @return T
     */
    protected function classService(string $serviceName): object
    {
        $container = $this->container();
        /** @var null|T $service */
        $service = $container->get($serviceName);
        $service ?? throw new \RuntimeException("Unable to fetch service '{$serviceName}' from container.");

        return $service;
    }

    protected function service(string $serviceName): object
    {
        $container = $this->container();

        $service = $container->get($serviceName);
        if (null === $service) {
            throw new RuntimeException("Cannot get service {$serviceName}.");
        }

        return $service;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $clientClass
     *
     * @return T
     */
    protected function createLoggedClient(
        string $clientClass,
        User $loggedUser,
        string $firewall = self::FIREWALL_MAIN,
    ): object {
        $loggedHttpClient = $this->loggedClient($loggedUser, $firewall);
        $client = $this->service($clientClass);

        return $client->withHttpClient($loggedHttpClient);
    }

    protected function jsonDecode(string $json, bool $array = true): mixed
    {
        $jsonSerializer = $this->jsonSerializer();

        return $jsonSerializer->decode($json, $array);
    }

    protected function overwriteLoggedUser(User $user): void
    {
        $token = $this->createSecurityToken($user, self::FIREWALL_MAIN);
        $tokenStorage = $this->classService(TokenStorageInterface::class);
        $tokenStorage->setToken($token);
    }

    private function entitiesLoader(): EntitiesLoaderInterface
    {
        return $this->classService(EntitiesLoaderInterface::class);
    }

    private function createSecurityToken(User $user, string $firewall): TokenInterface
    {
        $roleHierarchy = $this->classService(RoleHierarchyInterface::class);
        $roles = $roleHierarchy->getReachableRoleNames($user->roles);

        return new UsernamePasswordToken(
            new SymfonyUser(
                $user->id,
                $user->email,
                $user->passwordHash,
                $roles,
            ),
            $firewall,
            $roles,
        );
    }
}
