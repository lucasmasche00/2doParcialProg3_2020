<?php
namespace App\Services;
use App\Models\JSend;
use App\Models\Usuario;
use App\Models\Materia;
use App\Models\Inscripcion;
use App\Models\Token;

class InscripcionService
{
    public static function Insert($request, $response, array $args)
    {
        $token = $_SERVER['HTTP_TOKEN'] ?? '';
        $usuarioLogeado = Token::DecodificarToken($token);

        $jSend = new JSend('error');
        $materiaId = $args['idMateria'] ?? '';

        if($materiaId !== '')
        {
            $materias = Materia::GetAll();

            if(Materia::IsInList($materias, $materiaId))
            {
                $Usuarios = Usuario::GetAll();

                if(Usuario::IsInList($Usuarios, $usuarioLogeado->email))
                {
                    $inscripciones = Inscripcion::GetAll();

                    $materia = Materia::FindById($materias, $materiaId);
                    Usuario::FindById($Usuarios, $usuarioLogeado->email);

                    $inscripcion = Inscripcion::Constructor($usuarioLogeado->email, $materiaId, date('Y-m-d H:i:s'));
                    if(!Inscripcion::IsInList($inscripciones, $inscripcion))
                    {
                        if($materia->cupos > 0)
                        {
                            $materia->UpdateCupo();
                            $inscripcion->Insert();
                            
                            $jSend->status = 'success';
                            $jSend->data->mensajeExito = 'Guardado exitoso';
                        }
                        else
                        {
                            $jSend->message = 'No hay mas cupos';
                        }
                    }
                    else
                    {
                        $jSend->message = 'Inscripcion repetida';
                    }
                }
                else
                {
                    $jSend->message = 'Usuario no encontrado';
                }
            }
            else
            {
                $jSend->message = 'Materia no encontrada';
            }
        }
        else
        {
            $jSend->message = 'Id de la materia valida requerida';
        }
        return json_encode($jSend);
    }
}
?>