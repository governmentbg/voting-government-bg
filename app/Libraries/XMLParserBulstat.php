<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

/**
 * XML parser specific for Bulstat register xml files format
 */
class XMLParserBulstat implements IXMLParser
{
    //    - Фондация – 485
    //    - Сдружение – 486
    //    - Клон на чуждестранно юридическо лице с нестопаска цел - 1307
    //    - Народно читалище – 488
    //    - Читалищно сдружение – 1586
    const LEGAL_FORMS = ['485' , '486', '488', '1307', '1586'];

    const COURT_LEGAL_FORM = 502;

    //435 - юридическо лице
    const LEGAL_STATUTE = 435;

    //    1199	sobstvenost_na  - клон на
    //    708	kam_vishestoiast    -	към висшестояща
    //    710	kam_gorestoiasta    -	към горестояща
    //    709	kam_priaka_gorestoista  -	към пряка горестояща
    const VALID_BELONG_TYPES = ['708', '709', '710', '1199'];

    const STATUS_ACTIVE = 571; //развиващ дейност
    const STATUS_LIQUIDATION = 574; //в ликвидация
    const STATUS_INACTIVE = 575; //неактивен
    const STATUS_REREGISTRED = 1; //пререгистриран в ТР
    const STATUS_ARCHIVED = 2; //архивиран

    private $data;

    private $ekatte;

    private $managerPostitions;

    private $addressTypes;

    private $courtNames;

    public function __construct()
    {
        $this->loadEkatte();
        $this->loadManagerPositions();
        $this->loadAddressTypes();
        $this->loadCourtNames();
    }

    /**
     * Tries to parse XML file from a given path.
     * @param  string  $path
     * @return boolean
     */
    public function loadFile($path)
    {
        $this->data = simplexml_load_file($path);   //load file to get namespaces
        if($this->data === false) {
            return false;
        }
        
        //strip namespaces
        $xml = str_replace(array_map(function($e) { return empty($e)? '' : "$e:"; }, array_keys($this->data->getDocNamespaces())), array(), file_get_contents($path));

        $this->data = simplexml_load_string($xml);

        return $this->data === false ? false : true;
    }

    /**
     * Tries to parse XML file from a given string.
     * @param  string  $xmlData
     * @return boolean
     */
    public function loadString($xmlData)
    {
        $this->data = simplexml_load_string($xmlData);   //load xml to get namespaces
        if($this->data === false) {
            return false;
        }

        //strip namespaces
        $xml = str_replace(array_map(function($e) { return empty($e)? '' : "$e:"; }, array_keys($this->data->getDocNamespaces(true))), array(), $xmlData);

        $this->data = simplexml_load_string($xml);

        if(isset($this->data->Body) && isset($this->data->Body->SendSubscription) && isset($this->data->Body->SendSubscription->SendSubscriptionRequest)){
            //get corrent node if it is update by subscription service
            $this->data = $this->data->Body->SendSubscription->SendSubscriptionRequest;
        }

        return $this->data === false ? false : true;
    }

    /**
     * Return parsed data as array.
     * @param  string $path
     * @return array
     */
    public function getParsedData()
    {
        if (!isset($this->data->StateOfPlay)) {
            return [];
        }

        $result = [];
        foreach ($this->data->StateOfPlay  as $org) {
            if (isset($org->Subject) && isset($org->Subject->UIC) && $this->isOrgRelevant($org)) {
                $parsedOrg = $this->getRelevantFields($org);
                if ($parsedOrg) {
                    $result[] = $parsedOrg;
                }
            }
        }

        return $result;
    }

    public function getCourtNomenclatures()
    {
        if (!isset($this->data->StateOfPlay)) {
            return [];
        }

        $result = [];
        foreach ($this->data->StateOfPlay  as $org) {
            if (isset($org->Subject) && isset($org->Subject->UIC) && $this->isCourt($org)) {
                $data = [];
                if (isset($org->Subject->UIC)) {
                    $data['eik'] = (string) $org->Subject->UIC->UIC;
                } else {
                    continue;
                }

                if (isset($org->Subject->LegalEntitySubject) && isset($org->Subject->LegalEntitySubject->CyrillicFullName)) {
                    $data['name'] = (string) $org->Subject->LegalEntitySubject->CyrillicFullName;
                } else {
                    continue;
                }
             
                if(!empty($data)){
                    $result[$data['eik']] = $data;
                }
            }
        }

        return $result;
    }

    public function getRelevantFields($org)
    {
        $orgArray = [];

        if (isset($org->Subject->UIC)) {
            $orgArray['eik'] = (string) $org->Subject->UIC->UIC;
        } else {
            return false;
        }

        if (isset($org->Subject->LegalEntitySubject) && isset($org->Subject->LegalEntitySubject->CyrillicFullName)) {
            $orgArray['name'] = (string) $org->Subject->LegalEntitySubject->CyrillicFullName;
        } else {
            return false;
        }

        if (isset($org->Subject->UIC) && isset($org->Subject->UIC->EntryTime) && !empty((string)$org->Subject->UIC->EntryTime)) {
            $orgArray['reg_date'] = date('Y-m-d H:i:s', strtotime((string)$org->Subject->UIC->EntryTime));
        }

        $orgArray['representative'] = '';
        $notFirst = false;
        foreach($org->Managers as $key => $manager) {
            if(isset($manager->RelatedSubject->NaturalPersonSubject)){
                if($notFirst){
                    $orgArray['representative'] .= ', ';
                }
                if(isset($manager->Position)){
                    $orgArray['representative'] .= $this->getManagerPostion((string)$manager->Position->Code) . ': ';
                }
                $orgArray['representative'] .= (string)$manager->RelatedSubject->NaturalPersonSubject->CyrillicName;
                $notFirst = true;
            }
        }

        if (isset($org->Event) && isset($org->Event->Case)) {
            $orgArray['description'] = '';
            if(isset($org->Event->Case->Number) && !empty((string)$org->Event->Case->Number)){
                $orgArray['description'] .= 'дело номер: ' . (string)$org->Event->Case->Number;
            }
            if(isset($org->Event->Case->Year) && !empty($org->Event->Case->Year)){
                if(!empty($orgArray['description'])){
                    $orgArray['description'] .= ', ';
                }
                $orgArray['description'] .= 'година: ' . (string)$org->Event->Case->Year;
            }
            if(isset($org->Event->Case->Court)){
                if(!empty($orgArray['description'])){
                    $orgArray['description'] .= ', ';
                }
                $orgArray['description'] .= 'съд: ' . $this->getCourtName((string)$org->Event->Case->Court->Code);
            }

            if(empty($orgArray['description'])){
                unset($orgArray['description']);
            }
        }

        $orgArray['address'] = '';
        foreach ($org->Subject->Addresses as $key => $address) {
            $orgArray['city'] = (isset($address->Location) ? $this->getCityName((string)$address->Location->Code) : '');
            if(!empty(trim($orgArray['address']))){
                $orgArray['address'] .= ', ';
            }
            $addressType = (string) (isset($address->AddressType) ? $this->getAddressType((string)$address->AddressType->Code) . ': ' : '');
            $addressString = (string) (isset($address->Street) ? $address->Street : '') . ' ' .
                        ((isset($address->StreetNumber) ? $address->StreetNumber : '')) .
                        ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                        ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                        ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));

            $orgArray['address'] .= isset($addressString) && !empty(trim($addressString)) ? $addressType . $addressString : '';
        }

        if (isset($org->Subject->Communications)) {
            foreach ($org->Subject->Communications as $key => $communication) {
                if (isset($communication->Type) && $communication->Type->Code == 721) {
                    $orgArray['phone'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
                if (isset($communication->Type) && $communication->Type->Code == 723) {
                    $orgArray['email'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
            }
        }

        $orgArray['status'] = isset($org->State->State->Code) ? (string)$org->State->State->Code : '';

        $orgArray['goals'] = (string) (isset($org->ScopeOfActivity->Description) ? $org->ScopeOfActivity->Description : '');
        $orgArray['tools'] = '';

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        return isset($org->Subject->LegalEntitySubject->LegalForm) && 
            in_array((string)$org->Subject->LegalEntitySubject->LegalForm->Code, self::LEGAL_FORMS) &&
            (isset($org->Subject->LegalEntitySubject->LegalStatute) ?
            $org->Subject->LegalEntitySubject->LegalStatute->Code == self::LEGAL_STATUTE : true) ||
            $this->isAssociationBranch($org);
    }

    private function isCourt($org)
    {
        return isset($org->Subject->LegalEntitySubject->LegalForm) &&
            (string)$org->Subject->LegalEntitySubject->LegalForm->Code == self::COURT_LEGAL_FORM;
    }

    private function isAssociationBranch($org)
    {
        return isset($org->Belonging) && isset($org->Belonging->RelatedSubject->LegalEntitySubject) &&
                in_array($org->Belonging->RelatedSubject->LegalEntitySubject->LegalForm->Code, self::LEGAL_FORMS) &&
                in_array($org->Belonging->Type->Code, self::VALID_BELONG_TYPES);
    }

    private function getCityName($ekatte)
    {
        if(empty($ekatte)){
            return '';
        }
        
        foreach($this->ekatte as $key => $obj) {
            if($ekatte == $obj['ekatte']){
                return $obj['t_v_m'] . ' ' . $obj['name'];
            }
        }

        return '';
    }

    private function loadEkatte()
    {
        $json = Storage::disk('local')->get('/nomenclatures/ekatte.json');
        $this->ekatte = json_decode($json, true);
    }

    private function loadManagerPositions()
    {
        $json = Storage::disk('local')->get('/nomenclatures/manager_positions.json');
        $this->managerPostitions = json_decode($json, true);
    }

    private function loadAddressTypes()
    {
        $json = Storage::disk('local')->get('/nomenclatures/addr_types.json');
        $this->addressTypes = json_decode($json, true);
    }

    private function loadCourtNames()
    {
        $json = Storage::disk('local')->get('/nomenclatures/court_names.json');
        $this->courtNames = json_decode($json, true);
    }

    private function getManagerPostion($code)
    {
        if(empty($code)){
            return '';
        }

        foreach($this->managerPostitions as $key => $obj) {
            if($code == $obj['MANAGER_POSITION_ID']){
                return $obj['NAME'];
            }           
        }

        return '';
    }

    private function getAddressType($code)
    {
        if(empty($code)){
            return '';
        }

        foreach($this->addressTypes as $key => $obj) {
            if($code == $obj['ADDR_TYPE_ID']){
                return $obj['NAME'];
            }
        }

        return '';
    }

    private function getCourtName($code)
    {
        if(empty($code)){
            return '';
        }


        if(isset($this->courtNames[$code])){
            return $this->courtNames[$code]['name'];
        }else{
            logger('Court name not found. EIK: ' . $code);
        }


        return $code;
    }
}
