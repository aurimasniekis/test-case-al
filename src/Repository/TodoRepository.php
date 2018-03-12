<?php

namespace App\Repository;

use App\Entity\Todo;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class TodoRepository
 *
 * @package App\Repository
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class TodoRepository extends BaseRepository
{
    public function findAllWithUserId(int $userId): array
    {
        return $this->findBy(
            [
                'user' => $this->getEntityManager()->getPartialReference(
                    User::class,
                    $userId
                )
            ]
        );
    }
    public function findWithUserId(int $id, int $userId): ?Todo
    {
        return $this->findOneBy(
            [
                'id' => $id,
                'user' => $this->getEntityManager()->getPartialReference(
                    User::class,
                    $userId
                )
            ]
        );
    }
}