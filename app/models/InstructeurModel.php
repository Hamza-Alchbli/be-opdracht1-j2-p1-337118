<?php

class InstructeurModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getInstructeurs()
    {
        $sql = "SELECT Id
                      ,Voornaam
                      ,Tussenvoegsel
                      ,Achternaam
                      ,Mobiel
                      ,DatumInDienst
                      ,AantalSterren
                FROM  Instructeur
                ORDER BY AantalSterren DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getToegewezenVoertuigen($Id)
    {
        $sql = "SELECT      VOER.Id
                            ,VOER.Type
                            ,VOER.Kenteken
                            ,VOER.Bouwjaar
                            ,VOER.Brandstof
                            ,TYVO.TypeVoertuig
                            ,TYVO.RijbewijsCategorie

                FROM        Voertuig    AS  VOER
                
                INNER JOIN  TypeVoertuig AS TYVO

                ON          TYVO.Id = VOER.TypeVoertuigId
                
                INNER JOIN  VoertuigInstructeur AS VOIN
                
                ON          VOIN.VoertuigId = VOER.Id
                
                WHERE       VOIN.InstructeurId = $Id
                
                ORDER BY    TYVO.RijbewijsCategorie DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }
    function getToegewezenVoertuig($Id, $InstructeurId)
    {
        $sql = "SELECT      VOER.Id
        ,VOER.Type
        ,VOER.Kenteken
        ,VOER.Bouwjaar
        ,VOER.Brandstof
        ,TYVO.TypeVoertuig
        ,TYVO.RijbewijsCategorie
        FROM        Voertuig    AS  VOER

        INNER JOIN  TypeVoertuig AS TYVO

        ON          TYVO.Id = VOER.TypeVoertuigId

        INNER JOIN  VoertuigInstructeur AS VOIN

        ON          VOIN.VoertuigId = VOER.Id

        WHERE       VOIN.InstructeurId = $Id AND VOER.Id = $InstructeurId

        ORDER BY    TYVO.RijbewijsCategorie DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }
    public function getInstructeurById($Id)
    {
        $sql = "SELECT Voornaam
                      ,Tussenvoegsel
                      ,Achternaam
                      ,DatumInDienst
                      ,AantalSterren
                FROM  Instructeur
                WHERE Id = $Id";

        $this->db->query($sql);

        return $this->db->single();
    }

    function updateVoertuig($voertuigId)
    {
        $sql = "UPDATE Voertuig SET Type = :type, Brandstof = :brandstof, Kenteken = :kenteken WHERE 
                voertuigId = $voertuigId ";
        $this->db->query($sql);
        $this->db->bind(':type', $_POST['type']);
        $this->db->bind(':brandstof', $_POST['brandstof']);
        $this->db->bind(':kenteken', $_POST['kenteken']);
        

        $sql2 = "UPDATE VoertuigInstructeur SET InstructeurId = :instructeur WHERE VoertuigId = $voertuigId";
        $this->db->query($sql2);
        $this->db->bind(':instructeur', $_POST['instructeur']);

        return $this->db->resultSet();
    }
    
    function deleteVoertuig($Id)
    {
        $sql = "DELETE FROM Voertuig WHERE Id = $Id";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    function nietGebruiktVoertuig()
    {
        $sql = "SELECT * FROM Voertuig WHERE Id NOT IN (SELECT VoertuigId FROM VoertuigInstructeur);";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    function addNietGebruiktVoertuigen($voertuigId, $InstructeaurId) {
        $sql = "INSERT INTO VoertuigInstructeur (VoertuigId, InstructeurId) VALUES (:voertuigId, :instructeurId)";
        $this->db->query($sql);
        $this->db->bind(':voertuigId', $voertuigId);
        $this->db->bind(':instructeurId', $InstructeaurId);
        return $this->db->resultSet();
    }
}
