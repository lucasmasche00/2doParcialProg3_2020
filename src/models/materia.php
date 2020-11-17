<?php
namespace App\Models;
use Config\AccesoDatos;
use PDO;

class Materia
{
    public $materiaId;
    public $nombre;
    public $cuatrimestre;
    public $cupos;

    public static function Constructor($nombre, $cuatrimestre, $cupos)
    {
        $obj = new Materia();
        $obj->nombre = $nombre;
        $obj->cuatrimestre = $cuatrimestre;
        $obj->cupos = $cupos;
        return $obj;
    }

    //==================== CLASS ============================
    public static function GetInstance($obj)
    {
        return self::Constructor($obj->nombre, $obj->cuatrimestre, $obj->cupos);
    }
    public static function ListToMateria($lista)
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
            if($value->materiaId != null && $id != null && $value->materiaId == $id)
                return $value;
        }
        return false;
    }

    public static function IsInList($lista, $id)
    {
        return (!is_null($lista) && count($lista) > 0) ? ((self::FindById($lista, $id) === false) ? false : true) : false;
    }

    public static function Add($lista, $obj)
    {
        if(!self::IsInList($lista, $obj->materiaId))
        {
            array_push($lista, $obj);
            return $lista;
        }
        return false;
    }

    public static function Remove($lista, $obj)
    {
        $obj = self::GetInstance($obj);
        if(self::IsInList($lista, $obj->materiaId))
        {
            foreach ($lista as $key => $value)
            {
                if($value->materiaId != null && $obj->materiaId != null && $value->materiaId === $obj->materiaId)
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
        $query = $objetoAccesoDato->RetornarConsulta('SELECT * FROM materias');
        return $query->execute() ? $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Materia') : false;
    }

    public function Insert()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta("INSERT INTO materias (nombre, cuatrimestre, cupos) VALUES (:nombre,:cuatrimestre,:cupos)");
        $query->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $query->bindValue(':cuatrimestre', $this->cuatrimestre, PDO::PARAM_INT);
        $query->bindValue(':cupos', $this->cupos, PDO::PARAM_INT);
        $query->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function UpdateCupo()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta("UPDATE materias SET cupos = :cupos WHERE materiaId = :materiaId");
        $query->bindValue(':cupos', $this->cupos - 1, PDO::PARAM_INT);
        $query->bindValue(':materiaId', $this->materiaId, PDO::PARAM_INT);
        $query->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
}
?>