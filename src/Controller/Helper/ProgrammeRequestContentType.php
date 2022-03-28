<?php

declare(strict_types=1);

namespace App\Controller\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgrammeRequestContentType
{
    public function getRequestType(Request $request): string
    {
        $acceptHeader = $request->headers->get('accept');
        $mimeTypes = explode('/', $acceptHeader);

        return $mimeTypes[1];
    }

    public function getResponse(string $data, string $contentSubtype): Response
    {
        if ($contentSubtype === 'json') {
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        }

        return new Response($data, Response::HTTP_OK, []);
    }
}
