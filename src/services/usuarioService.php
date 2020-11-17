<?php
namespace App\Services;
use App\Models\Usuario;
use App\Models\Archivo;
use App\Models\JSend;
use App\Models\Token;

class UsuarioService
{
    //USADO
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

    public static function GetOne($request, $response, array $args)
    {
        $jSend = new JSend('error');
        $email = $args['email'] ?? '';
        if($email !== '')
        {
            $lista = Usuario::GetAll();
            
            if(Usuario::IsInList($lista, $email))
            {
                $user = Usuario::FindById($lista, $email);
                
                $jSend->status = 'success';
                $jSend->data->mensajeExito = $user;
            }
            else
            {
                $jSend->message = 'No hay usuario con ese email';
            }
        }
        else
        {
            $jSend->message = 'Email valido requerido';
        }
        return json_encode($jSend);
    }

    public static function GetAll($request, $response, array $args)
    {
        $jSend = new JSend('success');
        $jSend->data->usuarios = Usuario::GetAll();
            
        return json_encode($jSend);
    }

    //USADO
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
    
    public static function Update($request, $response, array $args)
    {
        $jSend = new JSend('error');
        $params = $request->getParsedBody();
        $email = $args['email'] ?? '';
        if($email !== '')
        {
            $clave = $params['clave'] ?? '';
            if($clave !== '')
            {
                $tipo = $params['tipo'] ?? '';
                if($tipo === 'admin' || $tipo === 'user')
                {
                    $lista = Usuario::GetAll();
                    
                    if(Usuario::IsInList($lista, $email))
                    {
                        $file = $_FILES['foto'] ?? null;
                    
                        if(!is_null($file))
                        {
                            $oldUser = Usuario::FindById($lista, $email);

                            $foto = Archivo::ModificarArchivo($file, $oldUser->foto);
                            if($foto !== false)
                            {
                                $user = Usuario::Constructor($email, $clave, $tipo, $foto);

                                $user->Update();
                                                
                                $jSend->status = 'success';
                                $jSend->data->mensajeExito = 'Modificacion exitosa';
                            }
                            else
                            {
                                $jSend->message = 'Error al guardar la foto';
                            }
                        }
                        else
                        {
                            $jSend->message = 'Foto valida requerida';
                        }
                    }
                    else
                    {
                        $jSend->message = 'Email no encontrado';
                    }
                }
                else
                {
                    $jSend->message = 'Tipo valida requerido: admin o user';
                }
            }
            else
            {
                $jSend->message = 'Clave valida requerida';
            }
        }
        else
        {
            $jSend->message = 'Email valido requerido';
        }
        return json_encode($jSend);
    }
    
    public static function Delete($request, $response, array $args)
    {
        $jSend = new JSend('error');
        $params = $request->getParsedBody();
        $email = $args['email'] ?? '';
        if($email !== '')
        {
            $lista = Usuario::GetAll();
            
            if(Usuario::IsInList($lista, $email))
            {
                $oldUser = Usuario::FindById($lista, $email);

                $foto = Archivo::BorrarArchivo($oldUser->foto);
                if($foto !== false)
                {
                    $user = Usuario::Constructor($email, $oldUser->clave, $oldUser->tipo, $oldUser->foto);

                    $user->Delete();
                                    
                    $jSend->status = 'success';
                    $jSend->data->mensajeExito = 'Borrado exitoso';
                }
                else
                {
                    $jSend->message = 'Error al borrar la foto';
                }
            }
            else
            {
                $jSend->message = 'Email no encontrado';
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