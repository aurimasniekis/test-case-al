<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserRepository
 *
 * @package App\Repository
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class UserRepository extends EntityRepository
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return UserRepository
     *
     * @required
     */
    public function setPasswordEncoder(UserPasswordEncoderInterface $passwordEncoder): UserRepository
    {
        $this->passwordEncoder = $passwordEncoder;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return User|null
     */
    public function loadByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function save(User $user)
    {
        if (null !== $user->getPassword()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );
        }

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();
    }
}