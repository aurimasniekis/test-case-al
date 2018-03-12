<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BaseController
 *
 * @package App\Controller
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract  class BaseController extends Controller
{
    /**
     * {@inheritDoc}
     */
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $status,
                'data' => $data,
            ],
            $status,
            $headers
        );
    }

    protected function errorNotFound(string $message = 'Not Found', int $code = 404): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $code,
                'message' => $message
            ],
            $code
        );
    }

    protected function error(string $message, int $code = 400): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $code,
                'message' => $message
            ],
            $code
        );
    }
}