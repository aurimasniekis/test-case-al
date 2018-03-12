<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BaseRepository
 *
 * @package App\Repository
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class BaseRepository extends EntityRepository
{
    public function save($object)
    {
        $em = $this->getEntityManager();

        $em->persist($object);
        $em->flush();

        return $object;
    }

    public function delete($object)
    {
        $em = $this->getEntityManager();

        $em->remove($object);
        $em->flush();

        return $object;
    }
}