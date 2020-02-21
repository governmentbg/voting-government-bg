<?php

namespace App\Libraries;

use App\TradeRegister;
use App\Libraries\CourtNomenclatures;

/**
 * XML parser specific for trade register xml files format
 */
class XMLParser implements IXMLParser
{
    private $data;

    private $parseBranchesOnly;

    const LEGAL_FORMS = ['ASSOC', 'FOUND', 'CC', 'BFLE'];
    //    ASSOC = 24,// Сдружение - юридическо лице с нестопанска цел
    //    FOUND = 25,// Фондация - юридическо лице с нестопанска цел
    //    BFLE  = 26,// Клон на чуждестранно юридическо лице с нестопанска цел
    //    CC    = 27 // Читалище - юридическо лице с нестопанска цел

    const STATUSES = ['E', 'C', 'L', 'N'];
    // N - Нова
    // Е - Пререгистрирана фирма по Булстат
    // L - Пререгистрирана фирма по Булстат затворена
    // C - Нова партида затворена

    public function __construct()
    {
        $this->parseBranchesOnly = false;
    }

    /**
     * Tries to parse XML file from a given path.
     * @param string $path
     * @return boolean
     */
    public function loadFile($path)
    {
        $this->data = simplexml_load_file($path);

        return $this->data === false ? false : true;
    }

    /**
     * Return parsed data as array.
     * @param  string $path
     * @return array
     */
    public function getParsedData()
    {
        if (!isset($this->data->Body->Deeds[0])) {
            return [];
        }

        $result = [];
        foreach ($this->data->Body->Deeds[0] as $org) {
            if (isset($org->attributes()['UIC']) && $this->isOrgRelevant($org)) {
                $parsedOrg = $this->getRelevantFields($org);
                if ($parsedOrg) {
                    $result[] = $parsedOrg;
                }
            }
        }

        return $result;
    }

    private function getRelevantFields($org)
    {
        $orgArray = [];

        if($org->SubDeed->attributes()['SubUICType'] == 'B2_Branch'){
            return $this->getBranchData($org);
        }
        else if($this->parseBranchesOnly){
            return false;
        }

        if (isset($org->attributes()['UIC'])) {
            $orgArray['eik'] = (string) $org->attributes()['UIC'];
        } else {
            return false;
        }

        if (isset($org->attributes()['CompanyName'])) {
            $orgArray['name'] = (string) $org->attributes()['CompanyName'];
        } else {
            return false;
        }

        if (isset($org->SubDeed->LegalForm) && isset($org->SubDeed->LegalForm->attributes()['FieldEntryDate'])) {
            $orgArray['reg_date'] = date('Y-m-d H:i:s', strtotime((string)$org->SubDeed->LegalForm->attributes()['FieldEntryDate']));           
        }

        if (isset($org->SubDeed->UIC) && isset($org->SubDeed->UIC->BulstatDeed)) {
            $orgArray['description'] = '';
            if(isset($org->SubDeed->UIC->BulstatDeed->Deed) && !empty($org->SubDeed->UIC->BulstatDeed->Deed)){
                $orgArray['description'] .= 'дело номер: ' . (string)$org->SubDeed->UIC->BulstatDeed->Deed;
            }
            if(isset($org->SubDeed->UIC->BulstatDeed->Year) && !empty($org->SubDeed->UIC->BulstatDeed->Year)){
                if(!empty($orgArray['description'])){
                    $orgArray['description'] .= ', ';
                }
                $orgArray['description'] .= 'година: ' . (string)$org->SubDeed->UIC->BulstatDeed->Year;
            }
            if(isset($org->SubDeed->UIC->BulstatDeed->CourtCode) && !empty($org->SubDeed->UIC->BulstatDeed->CourtCode)){
                if(!empty($orgArray['description'])){
                    $orgArray['description'] .= ', ';
                }
                $orgArray['description'] .= 'съд: ' . CourtNomenclatures::getName((string)$org->SubDeed->UIC->BulstatDeed->CourtCode);
            }

            if(empty($orgArray['description'])){
                unset($orgArray['description']);
            }
        }

        if (isset($org->SubDeed->Seat->Address)) {
            $address = $org->SubDeed->Seat->Address;
            //$orgArray['city'] = (string) (isset($address->Settlement) ? $address->Settlement : '');
            $orgArray['city'] = '';
            $orgArray['address'] =
                    ((isset($address->District) && !empty((string) $address->District) ? 'област ' . (string) $address->District : '')) .
                    ((isset($address->Municipality) && !empty((string) $address->Municipality) ? ' община ' . (string) $address->Municipality : '')) .
                    ((isset($address->Settlement) && !empty((string) $address->Settlement) ? ' ' . (string) $address->Settlement : '')) .
                    ((isset($address->Area) && !empty((string) $address->Area) ? ' ' . (string) $address->Area : '')) .
                    ((isset($address->PostCode) && !empty((string) $address->PostCode) ? ' ' . (string) $address->PostCode : '')) .
                    ((isset($address->HousingEstate) && !empty((string) $address->HousingEstate) ? ' жк ' . (string) $address->HousingEstate : '')) .
                    (isset($address->Street) ? ' ' . (string)$address->Street : '') . ' ' .
                    ((isset($address->StreetNumber) ? (string)$address->StreetNumber : '')) .
                    ((isset($address->Block) && !empty((string) $address->Block) ? ' бл. ' . (string) $address->Block : '')) .
                    ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                    ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                    ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));
        }

        if (isset($org->SubDeed->Seat->Contacts)) {
            $contact = $org->SubDeed->Seat->Contacts;
            $orgArray['phone'] = (string) (isset($contact->Phone) ? $contact->Phone : '');
            $orgArray['email'] = (string) (isset($contact->EMail) ? $contact->EMail : '');
        }

        foreach ($org->SubDeed as $key => $deed) {
            if (isset($deed->attributes()['SubUICType']) && (string) $deed->attributes()['SubUICType'] == 'MainCircumstances' &&
                    isset($deed[0]->DesignatedToPerformPublicBenefit)) {
                $orgArray['public_benefits'] = (int) $deed[0]->DesignatedToPerformPublicBenefit;
                break;
            }
        }

        $orgArray['status'] = (string) $org->attributes()['DeedStatus'];

        $orgArray['representative'] = '';
        foreach($org->SubDeed->Representatives103 as $key => $representative) {
            if(isset($representative->Representative103->Person) && isset($representative->Representative103->Person->attributes()['Position'])){
                $orgArray['representative'] .= ' ' . (string)$representative->Representative103->Person->attributes()['Position'] . ':';
            }
            
            $orgArray['representative'] .= isset($representative->Representative103->Person) ? (string)$representative->Representative103->Person->Name : '';
        }
        if(empty($orgArray['representative'])){
            unset($orgArray['representative']);
        }

        if(isset($org->SubDeed->Objectives->Text)){
            $orgArray['goals'] = (string)$org->SubDeed->Objectives->Text;
        }
        if(isset($org->SubDeed->MeansOfAchievingTheObjectives)){
            $orgArray['tools'] = (string) $org->SubDeed->MeansOfAchievingTheObjectives;
        }

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        return isset($org->attributes()['LegalForm']) && in_array((string) $org->attributes()['LegalForm'], self::LEGAL_FORMS);
    }

    private function getBranchData($org)
    {
        $orgArray = [];

        if (isset($org->attributes()['UIC'])) {
            $orgArray['eik'] = (string) $org->attributes()['UIC'];
            $mainOrg = TradeRegister::find($orgArray['eik']);
            if(!$mainOrg){
                logger('Main organisation not found for branch:' . $orgArray['eik'] . ' - ' . (string)$org->SubDeed->attributes()['SubUIC']);
                return false;
            }
        } else {
            return false;
        }

        if(isset($org->SubDeed->attributes()['SubUIC'])){
            $orgArray['eik'] .= (string) $org->SubDeed->attributes()['SubUIC'];
        }
        else{
            return false;
        }

        if (isset($org->SubDeed->BranchFirm)) {
            $orgArray['name'] = (string) $org->SubDeed->BranchFirm;
        }
        else{
            return false;
        }

        if (isset($org->SubDeed->BranchSeat) && isset($org->SubDeed->BranchSeat->attributes()['FieldEntryDate'])) {
            $orgArray['reg_date'] = date('Y-m-d H:i:s', strtotime((string)$org->SubDeed->BranchSeat->attributes()['FieldEntryDate']));
        }
        else if(isset($org->SubDeed->BranchManagers[0]) && isset($org->SubDeed->BranchManagers[0]->attributes()['FieldEntryDate'])){
            $orgArray['reg_date'] = date('Y-m-d H:i:s', strtotime((string)$org->SubDeed->BranchManagers[0]->attributes()['FieldEntryDate']));
        }
        
        if (isset($org->SubDeed->BranchSeat->Address)) {
            $address = $org->SubDeed->BranchSeat->Address;
            //$orgArray['city'] = (string) (isset($address->Settlement) ? $address->Settlement : '');
            $orgArray['city'] = '';
            $orgArray['address'] = 
                    ((isset($address->District) && !empty((string) $address->District) ? 'област ' . (string) $address->District : '')) .
                    ((isset($address->Municipality) && !empty((string) $address->Municipality) ? ' община ' . (string) $address->Municipality : '')) .
                    ((isset($address->Settlement) && !empty((string) $address->Settlement) ? ' ' . (string) $address->Settlement : '')) .
                    ((isset($address->Area) && !empty((string) $address->Area) ? ' ' . (string) $address->Area : '')) .
                    ((isset($address->PostCode) && !empty((string) $address->PostCode) ? ' ' . (string) $address->PostCode : '')) .
                    ((isset($address->HousingEstate) && !empty((string) $address->HousingEstate) ? ' жк ' . (string) $address->HousingEstate : '')) .
                    (isset($address->Street) ? ' ' . (string)$address->Street : '') . ' ' .
                    ((isset($address->StreetNumber) ? (string)$address->StreetNumber : '')) .
                    ((isset($address->Block) && !empty((string) $address->Block) ? ' бл. ' . (string) $address->Block : '')) .
                    ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                    ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                    ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));
        }

        if (isset($org->SubDeed->BranchSeat->Contacts)) {
            $contact = $org->SubDeed->BranchSeat->Contacts;
            $orgArray['phone'] = (string) (isset($contact->Phone) ? $contact->Phone : '');
            $orgArray['email'] = (string) (isset($contact->EMail) ? $contact->EMail : '');
        }

        $orgArray['public_benefits'] = $mainOrg->public_benefits;

        $orgArray['status'] = (string) $org->attributes()['DeedStatus'];

        $orgArray['representative'] = '';
        foreach($org->SubDeed->BranchManagers as $key => $representative) {
            if(isset($representative->BranchManager->Person) && isset($representative->BranchManager->Person->attributes()['Position']) &&
                    !empty($representative->BranchManager->Person->attributes()['Position'])){
                $orgArray['representative'] .= ' ' . (string)$representative->BranchManager->Person->attributes()['Position'] . ':';
            }

            $orgArray['representative'] .= isset($representative->BranchManager->Person) ? (string)$representative->BranchManager->Person->Name : '';
        }
        if(empty($orgArray['representative'])){
            unset($orgArray['representative']);
        }
 
        if(isset($org->SubDeed->BranchSubjectOfActivity)){
            $orgArray['goals'] = (string)$org->SubDeed->BranchSubjectOfActivity;
        }

        $orgArray['tools'] = $mainOrg->tools;

        return $orgArray;
    }

    public function setParseBranchesOnly($parseBranchesOnly)
    {
        $this->parseBranchesOnly = $parseBranchesOnly;
    }
}
