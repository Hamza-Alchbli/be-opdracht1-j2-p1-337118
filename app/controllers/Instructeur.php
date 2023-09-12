<?php

class Instructeur extends BaseController
{
    private $instructeurModel;

    public function __construct()
    {
        $this->instructeurModel = $this->model('InstructeurModel');
    }

    public function overzichtInstructeur()
    {
        $result = $this->instructeurModel->getInstructeurs();

        //  var_dump($result);
        $rows = "";
        foreach ($result as $instructeur) {
            /**
             * Datum in het juiste formaat gezet
             */
            $date = date_create($instructeur->DatumInDienst);
            $formatted_date = date_format($date, 'd-m-Y');

            $rows .= "<tr>
                        <td>$instructeur->Voornaam</td>
                        <td>$instructeur->Tussenvoegsel</td>
                        <td>$instructeur->Achternaam</td>
                        <td>$instructeur->Mobiel</td>
                        <td>$formatted_date</td>            
                        <td>$instructeur->AantalSterren</td>            
                        <td>
                            <a href='" . URLROOT . "/instructeur/overzichtvoertuigen/$instructeur->Id'>
                                <i class='bi bi-car-front'></i>
                            </a>
                        </td>            
                      </tr>";
        }

        $data = [
            'title' => 'Instructeurs in dienst',
            'rows' => $rows
        ];

        $this->view('Instructeur/overzichtinstructeur', $data);
    }

    public function overzichtVoertuigen($InstructeaurId)
    {

        $instructeurInfo = $this->instructeurModel->getInstructeurById($InstructeaurId);

        // var_dump($instructeurInfo);
        $naam = $instructeurInfo->Voornaam . " " . $instructeurInfo->Tussenvoegsel . " " . $instructeurInfo->Achternaam;
        $datumInDienst = $instructeurInfo->DatumInDienst;
        $aantalSterren = $instructeurInfo->AantalSterren;

        /**
         * We laten de model alle gegevens ophalen uit de database
         */
        $result = $this->instructeurModel->getToegewezenVoertuigen($InstructeaurId);


        $tableRows = "";
        if (empty($result)) {
            /**
             * Als er geen toegewezen voertuigen zijn komt de onderstaande tekst in de tabel
             */
            $tableRows = "<tr>
                            <td colspan='6'>
                                Er zijn op dit moment nog geen voertuigen toegewezen aan deze instructeur
                            </td>
                          </tr>";
        } else {
            /**
             * Bouw de rows op in een foreach-loop en stop deze in de variabele
             * $tabelRows
             */
            foreach ($result as $voertuig) {

                /**
                 * Zet de datum in het juiste format
                 */
                $date_formatted = date_format(date_create($voertuig->Bouwjaar), 'd-m-Y');

                $tableRows .= "<tr>
                                    <td>$voertuig->Id</td>
                                    <td>$voertuig->TypeVoertuig</td>
                                    <td>$voertuig->Type</td>
                                    <td>$voertuig->Kenteken</td>
                                    <td>$date_formatted</td>
                                    <td>$voertuig->Brandstof</td>
                                    <td>$voertuig->RijbewijsCategorie</td>  
                                    <td class='d-flex justify-content-between gap-8'>
                                        <a href='" . URLROOT . "/instructeur/voertuigDelete/$voertuig->Id/$InstructeaurId' class='m-4'>
                                            <i class='bi bi-trash'></i>
                                        </a>
                                        <a href='" . URLROOT . "/instructeur/overzichtvoertuigen_wijzig/$voertuig->Id/$InstructeaurId' class='m-4'>
                                            <i class='bi bi-pencil-square'></i>
                                        </a>

                                    </td>            
                            </tr>";
            }
        }


        $data = [
            'title'     => 'Door instructeur gebruikte voertuigen',
            'tableRows' => $tableRows,
            'naam'      => $naam,
            'datumInDienst' => $datumInDienst,
            'aantalSterren' => $aantalSterren,
            'instructeaurId' => $InstructeaurId
        ];

        $this->view('Instructeur/overzichtVoertuigen', $data);
    }
    function overzichtvoertuigen_wijzig($Id, $InstructeaurId)
    {
        $VoertuigInfo = $this->instructeurModel->getToegewezenVoertuig($InstructeaurId, $Id);
        $result = $this->instructeurModel->getInstructeurs();
        $data = [
            'title' => 'Wijzig voertuig',
            'voertuigId' => $Id,
            'instructeaurId' => $InstructeaurId,
            'voertuigInfo' => $VoertuigInfo,
            'instructeurs' => $result,
        ];
        $this->view('Instructeur/overzichtvoertuigen_wijzig', $data);
    }
    function overzichtvoertuigen_wijzig_save($Id, $InstructeaurId)
    {
        $this->instructeurModel->updateVoertuig($Id);
        $this->overzichtVoertuigen($InstructeaurId);
    }
    // function to delete voertuig
    function voertuigDelete($Id, $InstructeaurId)
    {
        $this->instructeurModel->deleteVoertuig($Id);
        $this->overzichtVoertuigen($InstructeaurId);
    }

    function nietGebruiktVoertuigen($InstructeaurId) {
        $nietGebruiktVoeruigen = $this->instructeurModel->nietGebruiktVoertuig();

        $data = [
            'title' => 'Niet gebruikte Voertuigen',
            'result' => $nietGebruiktVoeruigen,
            'instructeaurId' => $InstructeaurId
        ];

        $this->view('Instructeur/overzichtNietGebruiktVoertuigen', $data);
    }
    function addNietGebruiktVoertuigen($Id, $InstructeaurId) {
        $this->instructeurModel->addNietGebruiktVoertuigen($Id, $InstructeaurId);
        $this->overzichtVoertuigen($InstructeaurId);
    }
}
