<?php
namespace App\Services;
use App\Models\JSend;
use App\Models\Token;
use App\Models\Materia;

class MateriaService
{
    public static function Insert($request, $response, array $args)
    {
        $jSend = new JSend('error');
        $params = $request->getParsedBody();
        $nombre = $params['materia'] ?? '';
        if($nombre !== '')
        {
            $cuatrimestre = $params['cuatrimestre'] ?? '';
            if($cuatrimestre === '1' || $cuatrimestre === '2' || $cuatrimestre === '3' || $cuatrimestre === '4')
            {
                $cupos = $params['cupos'] ?? '';
                if($cupos !== '' && is_numeric($cupos))
                {
                    $user = Materia::Constructor($nombre, $cuatrimestre, $cupos);
                    
                    $user->Insert();
                    
                    $jSend->status = 'success';
                    $jSend->data->mensajeExito = 'Guardado exitoso';
                }
                else
                {
                    $jSend->message = 'Cupo valido requerido';
                }
            }
            else
            {
                $jSend->message = 'Cuatrimestre valido requerido: 1, 2, 3 o 4';
            }
        }
        else
        {
            $jSend->message = 'Materia valida requerida';
        }
        return json_encode($jSend);
    }
}
?>