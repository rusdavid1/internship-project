<?php

declare(strict_types=1);

namespace App\Programme;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgrammeRequestContentType
{
    public function getRequestType(Request $request)
    {
        $acceptHeader = $request->headers->get('accept');
        $acceptedCustomTypes = ['gigel'];

        if (in_array($acceptHeader, $acceptedCustomTypes)) {
            return $acceptHeader;
        }

        if ($acceptHeader) {
            $mimeTypes = explode('/', $acceptHeader);

            return $mimeTypes;
        }

        return null;
    }

    public function getResponse(string $data, string $contentSubtype): Response
    {
        if ($contentSubtype === 'json') {
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        }

        return new Response($data, Response::HTTP_OK, []);
    }
}
