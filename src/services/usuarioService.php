<?php
namespace App\Services;
use App\Models\Usuario;
use App\Models\Archivo;
use App\Models\JSend;
use App\Models\Token;

class UsuarioService
{
    public static function GenerarToken($request, $response, array $args)
    {
        $jSend = new JSend('error');
        $params = $request->getParsedBody();
        $email = $params['email'] ?? '';
        $clave = $params['clave'] ?? '';
        $lista = Usuario::GetAll();
        if($email !== '')
        {
            if(strpos($email, '@'))
            {
                foreach ($lista as $value)
                {
                    if($value->email === $email && $value->clave === sha1($clave))
                    {
                        $jwt = Token::CrearToken($value->email, $value->nombre, $value->tipo);
                        $jSend->status = 'success';
                        $jSend->data->token = $jwt;
                        return json_encode($jSend);
                    }
                }
                $jSend->message = 'Email y/o clave incorrecto/s';
            }
            else
            {
                foreach ($lista as $value)
                {
                    if($value->nombre === $email && $value->clave === sha1($clave))
                    {
                        $jwt = Token::CrearToken($value->email, $value->nombre, $value->tipo);
                        $jSend->status = 'success';
                        $jSend->data->token = $jwt;
                        return json_encode($jSend);
                    }
                }
                $jSend->message = 'Nombre y/o clave incorrecto/s';
            }
        }
        else
        {
            $jSend->message = 'Datos incorrectos';
        }
        return json_encode($jSend);
    }

    public static function Insert($request, $response, $args)
    {
        $jSend = new JSend('error');
        $params = $request->getParsedBody();
        $email = $params['email'] ?? '';
        if($email !== '')
        {
            $nombre = $params['nombre'] ?? '';
            if($nombre !== '' && !strpos($nombre, ' '))
            {
                if(!strpos($nombre, '@'))
                {
                    $clave = $params['clave'] ?? '';
                    if($clave !== '' && strlen($clave) >= 4)
                    {
                        $tipo = $params['tipo'] ?? '';
                        if($tipo === 'admin' || $tipo === 'profesor' || $tipo === 'alumno')
                        {
                            
                            $lista = Usuario::GetAll();
    
                            if(!Usuario::NameIsInList($lista, $nombre))
                            {
                                if(!Usuario::IsInList($lista, $email))
                                {
                                    $user = Usuario::Constructor($email, $clave, $tipo, $nombre);
                                    
                                    $user->Insert();
                                    
                                    $jSend->status = 'success';
                                    $jSend->data->mensajeExito = 'Guardado exitoso';
                                }
                                else
                                {
                                    $jSend->message = 'Email repetido';
                                }
                            }
                            else
                            {
                                $jSend->message = 'Nombre repetido';
                            }
                        }
                        else
                        {
                            $jSend->message = 'Tipo valida requerido: admin, profesor o alumno';
                        }
                    }
                    else
                    {
                        $jSend->message = 'Clave valida requerida (minimo 4 caracteres)';
                    }
                }
                else
                {
                    $jSend->message = 'Nombre valido requerido (no puede contener @)';
                }
            }
            else
            {
                $jSend->message = 'Nombre valido requerido (sin espacios)';
            }
        }
        else
        {
            $jSend->message = 'Email valido requerido';
        }
        return json_encode($jSend);
    }
}
?>