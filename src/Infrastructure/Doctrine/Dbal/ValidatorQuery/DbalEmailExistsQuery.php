<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Polsl\Application\ValidatorQuery\EmailExistsQueryInterface;
use Polsl\Packages\DbConnection\DbConnectionInterface;

final readonly class DbalEmailExistsQuery implements EmailExistsQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function query(string $email): bool
    {
        if ('' === $email) {
            return false;
        }

        $statement = $this->connection
            ->fetchAssociative(
                <<<'SQL'
                    SELECT user_id
                    FROM users
                    WHERE email = :email
                    LIMIT 1

                    SQL,
                ['email' => $email],
            )
        ;

        return false !== $statement;
    }
}
