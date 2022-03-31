<?php

declare(strict_types=1);

namespace App\Controller\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgrammeRequestContentType
{
    /**
     * @param Request $request
     * @return string|Response
     */
    public function getRequestType(Request $request, array $customSubtypes = [])
    {
        $acceptHeader = $request->headers->get('accept');
        $mimeTypes = [];

        if (in_array($acceptHeader, $customSubtypes)) {
            return $acceptHeader;
        }

        if ($acceptHeader) {
            $mimeTypes = explode('/', $acceptHeader);
        }

        if (count($mimeTypes) !== 2) {
            return new Response('Invalid headers', Response::HTTP_BAD_REQUEST);
        }

        if ($mimeTypes[1] === '*') {
            $mimeTypes[1] = 'json';
        }

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
