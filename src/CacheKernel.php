<?php

declare(strict_types=1);

namespace Tab;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tab\Packages\Constants\HttpHeaders;
use Tab\Packages\Constants\HttpMethods;
use Tab\Packages\Constants\Ip;
use Toflar\Psr6HttpCacheStore\Psr6Store;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

final class CacheKernel extends HttpCache
{
    private const SUPPORTED_PURGE_METHODS = [
        HttpMethods::PURGE_TAGS => 0,
        HttpMethods::PURGE => 0,
    ];

    public static function create(KernelInterface $kernel): self
    {
        $cacheDirectory = $kernel->getCacheDir();

        return new self(
            $kernel,
            new Psr6Store(
                [
                    'cache' => new FilesystemTagAwareAdapter(
                        'http_cache',
                        directory: $cacheDirectory,
                    ),
                    'cache_directory' => $cacheDirectory,
                    'cache_tags_header' => HttpHeaders::X_CACHE_TAGS,
                ],
            ),
            options: ['terminate_on_cache_hit' => false],
        );
    }

    protected function invalidate(Request $request, $catch = false): Response
    {
        if (!isset(self::SUPPORTED_PURGE_METHODS[$request->getMethod()])) {
            return parent::invalidate($request, $catch);
        }

        $isIpValid = IpUtils::checkIp($request->getClientIp() ?? '', Ip::DOCKER_SUBNET);
        if (!$isIpValid) {
            return new Response(
                'Invalid HTTP method',
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (HttpMethods::PURGE_TAGS === $request->getMethod()) {
            $result = $this->handlePurgeTags($request);
        } else {
            $result = $this->handlePurgeUri($request);
        }

        $response = new Response();
        if ($result) {
            $response->setStatusCode(Response::HTTP_OK, 'Purged');
        } else {
            $response->setStatusCode(Response::HTTP_NOT_FOUND, 'Not found');
        }

        return $response;
    }

    private function handlePurgeTags(Request $request): bool
    {
        $headers = $request->headers;
        $store = $this->getStore();

        if (!$store instanceof Psr6StoreInterface) {
            $expectedInterface = Psr6StoreInterface::class;

            throw new \RuntimeException("To purge tags, store needs to implement '{$expectedInterface}'.");
        }

        $tagsRaw = $headers->get(HttpHeaders::X_CACHE_TAGS, '');
        $tags = \explode(',', $tagsRaw);
        $tags = \array_map('trim', $tags);

        return $store->invalidateTags($tags);
    }

    private function handlePurgeUri(Request $request): bool
    {
        $store = $this->getStore();

        return $store->purge($request->getUri());
    }
}
