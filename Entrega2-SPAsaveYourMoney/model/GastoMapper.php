<?php
// file: model/GastoMapper.php

require_once(__DIR__ . "/../core/PDOConnection.php");

/**
 * Class GastoMapper
 *
 * Database interface for User entities
 */
class GastoMapper
{

    /**
     * Reference to the PDO connection
     * @var PDO
     */
    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save($gasto)
    {
        $stmt = $this->db->prepare("INSERT INTO gastos(usuario, tipo, cantidad, fecha, descripcion, fichero) values (?,?,?,?,?,?)");
        $stmt->execute(array($gasto->getUsuario(), $gasto->getTipo(), $gasto->getCantidad(), $gasto->getFecha(), $gasto->getDescription(), $gasto->getUuidFichero()));
    }
    public function delete($gasto)
    {
        $stmt = $this->db->prepare("DELETE FROM gastos WHERE id=?");
        $stmt->execute(array($gasto->getId()));
    }
    public function update($gasto)
    {
       
        $stmt = $this->db->prepare("UPDATE gastos SET tipo= ?,cantidad = ?,fecha = ?,descripcion = ?,fichero = ? WHERE id = ?");
        $stmt->execute(array($gasto->getTipo(), $gasto->getCantidad(), $gasto->getFecha(), $gasto->getDescription(), $gasto->getUuidFichero(), $gasto->getId()));
    }

    public function findGastoById($id)
    {
       
        $stmt = $this->db->prepare("SELECT * FROM gastos where id=?");
        $stmt->execute(array($id));

        $gasto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gasto != null) {
            
            return new Gasto(
                $gasto["id"],
                new User($gasto["usuario"]),
                $gasto["tipo"],
                $gasto["cantidad"],
                $gasto["fecha"],
                $gasto["descripcion"],
                $gasto["fichero"]
            );
        }

        return NULL;
    }

    public function findGastosByUsername($user)
    {

        $stmt = $this->db->prepare("SELECT * FROM gastos where usuario=?");
        $stmt->execute(array($user));

        $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $gastos;
    }
    public function findGastosByUsernameAndType($user, $type)
    {
        $stmt = $this->db->prepare("SELECT * FROM gastos where usuario=? and tipo = ?");
        $stmt->execute(array($user, $type));

        $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $gastos;
    }
    public function findGastosByUsernameByDateRange($user, $initialDate, $endDate)
    {
        $stmt = $this->db->prepare("SELECT * FROM gastos where usuario=? and fecha>=? and fecha<=?");
        $stmt->execute(array($user, $initialDate, $endDate));

        $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $gastos;
    }
}
