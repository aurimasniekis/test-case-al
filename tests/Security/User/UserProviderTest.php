<?php

namespace App\Tests\Security\User;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use App\Security\User\UserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProviderTest
 *
 * @package App\Tests\Security\User
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class UserProviderTest extends TestCase
{
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUserInvalidUser()
    {
        $repo = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userProvider = new UserProvider($repo);

        $user = new class implements UserInterface
        {
            public function getRoles() {}
            public function getPassword() {}
            public function getSalt() {}
            public function getUsername() {}
            public function eraseCredentials() {}
        };

        $userProvider->refreshUser($user);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRefreshUserNotFound()
    {
        $user = new User();
        $user->setUsername('user');

        $repo = $this->getUserRepositoryMock(null);

        $userProvider = new UserProvider($repo);
        $userProvider->refreshUser($user);
    }

    public function testRefreshUser()
    {
        $user = new User();
        $user->setUsername('user');

        $repo = $this->getUserRepositoryMock($user);

        $userProvider = new UserProvider($repo);
        $userProvider->refreshUser($user);
    }

    public function testSupportsClass()
    {
        $userProvider = new UserProvider(
            $this->getMockBuilder(UserRepository::class)
                ->disableOriginalConstructor()
                ->getMock()
        );

        $this->assertTrue($userProvider->supportsClass(User::class));
        $this->assertFalse($userProvider->supportsClass('demo-class'));
    }

    public function testLoadUserByUsername()
    {
        $user = new User();
        $user->setUsername('user');

        $repo = $this->getUserRepositoryMock($user);

        $userProvider = new UserProvider($repo);

        $this->assertEquals(
            $user,
            $userProvider->loadUserByUsername('user')
        );
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameNotFound()
    {
        $repo = $this->getUserRepositoryMock(null);

        $userProvider = new UserProvider($repo);
        $userProvider->loadUserByUsername('user');
    }

    /**
     * @param $return
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getUserRepositoryMock($return)
    {
        $repo = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadByUsername'])
            ->getMock();

        $repo->expects($this->once())
            ->method('loadByUsername')
            ->with($this->equalTo('user'))
            ->willReturn($return);

        return $repo;
    }
}
