<?php

namespace App\Tests\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use PHPUnit\Framework\TestCase;
use App\Controller\TodoController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TodoControllerTest
 *
 * @package App\Tests\Controller
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class TodoControllerTest extends TestCase
{

    public function testList()
    {
        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findAllWithUserId'])
            ->getMock();

        $user = $this->getUserMock();
        $todo = $this->getTodoMock($user);

        $repo->expects($this->once())
            ->method('findAllWithUserId')
            ->with(1)
            ->willReturn([$todo]);


        $controller = $this->getController($repo, $user);
        $result     = $controller->list();

        $this->assertResponse(
            $result,
            [
                'code' => 200,
                'data' => [
                    [
                        'id'       => 1,
                        'username' => 'bar',
                        'title'    => 'foo',
                    ],
                ],
            ]
        );
    }

    public function testCreate()
    {
        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMock();

        $repo->expects($this->once())
            ->method('save')
            ->will(
                $this->returnCallback(
                    function ($object) {
                        $this->assertInstanceOf(Todo::class, $object);
                        $this->assertEquals('foo', $object->getTitle());
                        $this->assertEquals('bar', $object->getUser()->getUsername());

                        return $object;
                    }
                )
            );

        $controller = $this->getController($repo);

        $request = Request::create('');
        $request->request->set('title', 'foo');

        $result = $controller->create($request);

        $this->assertResponse(
            $result,
            [
                'code' => 200,
                'data' => [
                    'id'       => null,
                    'username' => 'bar',
                    'title'    => 'foo',
                ],
            ]
        );
    }

    public function testCreateNoTitle()
    {
        $controller = $this->getController();

        $request = Request::create('');

        $result = $controller->create($request);

        $this->assertResponse(
            $result,
            [
                'code' => 400,
                'message' => 'Missing title'
            ],
            400
        );
    }

    public function testUpdate()
    {
        $user = $this->getUserMock();
        $todo = $this->getTodoMock($user);

        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findWithUserId'])
            ->getMock();

        $repo->expects($this->once())
            ->method('save')
            ->will(
                $this->returnCallback(
                    function ($object) use ($todo) {
                        $this->assertEquals($todo, $object);

                        return $object;
                    }
                )
            );

        $repo->expects($this->once())
            ->method('findWithUserId')
            ->with(1, 1)
            ->willReturn($todo);

        $controller = $this->getController($repo, $user);

        $request = Request::create('');
        $request->request->set('title', 'foo');

        $result = $controller->update(1, $request);

        $this->assertResponse(
            $result,
            [
                'code' => 200,
                'data' => [
                    'id'       => 1,
                    'username' => 'bar',
                    'title'    => 'foo',
                ],
            ]
        );
    }

    public function testUpdateNoTitle()
    {
        $user = $this->getUserMock();
        $todo = $this->getTodoMock($user);

        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findWithUserId'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findWithUserId')
            ->with(1, 1)
            ->willReturn($todo);

        $controller = $this->getController($repo, $user);

        $request = Request::create('');

        $result = $controller->update(1, $request);

        $this->assertResponse(
            $result,
            [
                'code' => 400,
                'message' => 'Missing title'
            ],
            400
        );
    }

    public function testUpdateNoFound()
    {
        $user = $this->getUserMock();

        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findWithUserId'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findWithUserId')
            ->with(1, 1)
            ->willReturn(null);

        $controller = $this->getController($repo, $user);

        $request = Request::create('');

        $result = $controller->update(1, $request);

        $this->assertResponse(
            $result,
            [
                'code' => 404,
                'message' => 'Not Found'
            ],
            404
        );
    }

    public function testShow()
    {
        $user = $this->getUserMock();
        $todo = $this->getTodoMock($user);

        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findWithUserId'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findWithUserId')
            ->with(1, 1)
            ->willReturn($todo);

        $controller = $this->getController($repo, $user);

        $result = $controller->show(1);

        $this->assertResponse(
            $result,
            [
                'code' => 200,
                'data' => [
                    'id'       => 1,
                    'username' => 'bar',
                    'title'    => 'foo',
                ],
            ]
        );
    }

    public function testDelete()
    {
        $user = $this->getUserMock();
        $todo = $this->getTodoMock($user);

        $repo = $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['delete', 'findWithUserId'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findWithUserId')
            ->with(1, 1)
            ->willReturn($todo);

        $repo->expects($this->once())
            ->method('delete')
            ->with($todo);

        $controller = $this->getController($repo, $user);
        $result     = $controller->delete(1);

        $this->assertResponse(
            $result,
            [
                'code'    => 200,
                'message' => 'Success',
            ]
        );
    }

    private function getController($repo = null, $user = null)
    {
        if (null === $user) {
            $user = new User();
            $user->setUsername('bar');
        }

        $controller = $this->getMockBuilder(TodoController::class)
            ->setConstructorArgs([$repo])
            ->setMethods(['getUser']);

        if (null !== $repo) {
            $controller->setConstructorArgs([$repo]);
        } else {
            $controller->disableOriginalConstructor();
        }

        $controller = $controller->getMock();

            $controller->method('getUser')
                ->willReturn($user);

        return $controller;
    }

    private function getUserMock()
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['getId'])
            ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $user->setUsername('bar');

        return $user;
    }

    private function getTodoMock($user)
    {
        $todo = $this->getMockBuilder(Todo::class)
            ->setMethods(['getId'])
            ->getMock();

        $todo->method('getId')
            ->willReturn(1);

        $todo->setTitle('foo');
        $todo->setUser($user);

        return $todo;
    }

    private function assertResponse(JsonResponse $response, $expectedData, $expectedCode = 200)
    {
        $this->assertEquals($expectedData,
                            json_decode($response->getContent(), true)
        );

        $this->assertEquals($expectedCode, $response->getStatusCode());
    }
}
