<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TodoController
 *
 * @package App\Controller
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 *
 * @Route("/api/todo")
 */
class TodoController extends BaseController
{
    /**
     * @var TodoRepository
     */
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/", methods={"GET"}, name="todo_list")
     */
    public function list(): JsonResponse
    {
        $records = $this->repository->findAllWithUserId($this->getUser()->getId());

        $result = [];
        foreach ($records as $record) {
            $result[] = $this->recordView($record);
        }

        return $this->json($result);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/", methods={"POST"}, name="todo_create")
     */
    public function create(Request $request): JsonResponse
    {
        $title = $request->request->get('title');

        if (null === $title) {
            return $this->error('Missing title');
        }

        $todo = new Todo();
        $todo
            ->setTitle($title)
            ->setUser($this->getUser());

        $this->repository->save($todo);

        return $this->json(
            $this->recordView($todo)
        );
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/{id}", methods={"PUT"}, name="todo_update", requirements={"id"="\d+"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        /** @var Todo $record */
        $record = $this->repository->findWithUserId($id, $this->getUser()->getId());

        if (null === $record) {
            return $this->errorNotFound();
        }

        $title = $request->request->get('title');

        if (null === $title) {
            return $this->error('Missing title');
        }

        $record->setTitle($title);

        $this->repository->save($record);

        return $this->json(
            $this->recordView($record)
        );
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     *
     * @Route("/{id}", methods={"GET"}, name="todo_show", requirements={"id"="\d+"})
     */
    public function show(int $id): JsonResponse
    {
        /** @var Todo $record */
        $record = $this->repository->findWithUserId($id, $this->getUser()->getId());

        if (null === $record) {
            return $this->errorNotFound();
        }

        return $this->json(
            $this->recordView($record)
        );
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     *
     * @Route("/{id}", methods={"DELETE"}, name="todo_delete", requirements={"id"="\d+"})
     */
    public function delete(int $id): JsonResponse
    {
        /** @var Todo $record */
        $record = $this->repository->findWithUserId($id, $this->getUser()->getId());

        if (null === $record) {
            return $this->errorNotFound();
        }

        $this->repository->delete($record);

        return $this->success();
    }

    protected function recordView(Todo $todo): array
    {
        return [
            'id'       => $todo->getId(),
            'username' => $todo->getUser()->getUsername(),
            'title'    => $todo->getTitle(),
        ];
    }
}