<?php

declare(strict_types=1);

namespace Polsl\Application\Service;

use Polsl\Application\Exception\ApplicationException;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Responder\Response\ResponseSpecification;
use Polsl\Packages\Validator\Violations;

final readonly class ApiProblemJsonResponseFactory
{
    public function __construct(private ResponseFactoryInterface $responseFactory) {}

    /** @param array<string,string> $pathMappings */
    public function unprocessableEntity(
        Violations $violations,
        array $pathMappings = [],
    ): ResponseInterface {
        if ($violations->isEmpty()) {
            throw new ApplicationException('Empty violations.');
        }

        $violationsArray = [];
        foreach ($violations->toArray() as $violation) {
            $propertyPath = $violation->propertyPath();
            $path = \array_key_exists($propertyPath, $pathMappings)
                ? $pathMappings[$propertyPath]
                : $propertyPath
            ;
            $violationsArray[$path][] = $violation->message();
        }

        $fieldsString = \implode("', '", \array_keys($violationsArray));
        $detail = "Przesłano błędne dane w polach: '{$fieldsString}'.";

        $data = [
            'type' => 'https://httpstatuses.com/422',
            'title' => 'Niepoprawne dane żądania',
            'status' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            'detail' => $detail,
            'errors' => $violationsArray,
        ];

        $responseSpecification = (new ResponseSpecification())
            ->withContentType('application/problem+json')
        ;

        return $this->responseFactory
            ->jsonResponse(
                $data,
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                $responseSpecification,
            )
        ;
    }
}
