<?php
namespace App\Models;
use Config\AccesoDatos;
use PDO;

class Usuario
{
    public $email;
    public $clave;
    public $tipo;
    public $nombre;

    public static function Constructor($email, $clave, $tipo, $nombre)
    {
        $obj = new Usuario();
        $obj->email = $email;
        $obj->clave = sha1($clave);
        $obj->tipo = $tipo;
        $obj->nombre = $nombre;
        return $obj;
    }

    //==================== CLASS ============================
    public static function GetInstance($obj)
    {
        return self::Constructor($obj->email, $obj->clave, $obj->tipo, $obj->nombre);
    }
    public static function ListStdToUsuario($lista)
    {
        $listaObj = array();
        foreach ($lista as $value)
        {
            array_push($listaObj, self::GetInstance($value));
        }
        return $listaObj;
    }
    
    public static function FindById($lista, $id)
    {
        foreach ($lista as $value)
        {
            if($value->email != null && $id != null && $value->email === $id)
                return $value;
        }
        return false;
    }

    public static function IsInList($lista, $id)
    {
        return (!is_null($lista) && count($lista) > 0) ? ((self::FindById($lista, $id) === false) ? false : true) : false;
    }

    
    public static function NameIsInList($lista, $name)
    {
        return (!is_null($lista) && count($lista) > 0) ? ((self::FindByName($lista, $name) === false) ? false : true) : false;
    }
    
    public static function FindByName($lista, $name)
    {
        foreach ($lista as $value)
        {
            if($value->nombre != null && $name != null && $value->nombre === $name)
                return $value;
        }
        return false;
    }

    public static function Add($lista, $obj)
    {
        if(!self::IsInList($lista, $obj->email))
        {
            array_push($lista, $obj);
            return $lista;
        }
        return false;
    }

    public static function Remove($lista, $obj)
    {
        $obj = self::GetInstance($obj);
        if(self::IsInList($lista, $obj->email))
        {
            foreach ($lista as $key => $value)
            {
                if($value->email != null && $obj->email != null && $value->email === $obj->email)
                {
                    unset($lista[$key]);
                    return $lista;
                }
            }
        }
        return false;
    }
    
    //==================== DAO ============================
    public static function GetAll()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios');
        return $query->execute() ? $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Usuario') : false;
    }

    public function Insert()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta("INSERT INTO usuarios (email, clave, tipo, nombre) VALUES (:email,:clave,:tipo,:nombre)");
        $query->bindValue(':email',$this->email, PDO::PARAM_STR);
        $query->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $query->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $query->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $query->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
}
?>