<?php
namespace App\Controllers;
use App\Services\MateriaService;

class MateriaController
{
    public function Insert($request, $response, array $args)
    {
        $jSend = MateriaService::Insert($request, $response, $args);
        $response->getBody()->write($jSend);
        return $response;
    }
}
?>