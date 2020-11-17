<?php
namespace App\Controllers;
use App\Services\UsuarioService;

class UsuarioController
{
    public function GenerarToken($request, $response, array $args)
    {
        $jSend = UsuarioService::GenerarToken($request, $response, $args);
        $response->getBody()->write($jSend);
        return $response;
    }

    public function Insert($request, $response, array $args)
    {
        $jSend = UsuarioService::Insert($request, $response, $args);
        $response->getBody()->write($jSend);
        return $response;
    }
}
?>