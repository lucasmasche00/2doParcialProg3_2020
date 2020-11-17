<?php
namespace App\Controllers;
use App\Services\InscripcionService;

class InscripcionController
{
    public function Insert($request, $response, array $args)
    {
        $jSend = InscripcionService::Insert($request, $response, $args);
        $response->getBody()->write($jSend);
        return $response;
    }
}
?>