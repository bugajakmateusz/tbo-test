<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Tab\Application\ValidatorQuery\EmailExistsQueryInterface;
use Tab\Packages\DbConnection\DbConnectionInterface;

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
