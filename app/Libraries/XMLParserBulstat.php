<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

/**
 * XML parser specific for Bulstat register xml files format
 */
class XMLParserBulstat implements IXMLParser
{
    const LEGAL_FORMS = ['Сдружение'/*'ASSOC', 'FOUND', 'CC', 'BFLE'*/]; //todo check forms

    private $data;

    private $ekatte;

    public function __construct()
    {
        $this->loadEkatte();
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
        $xml = str_replace(array_map(function($e) { return empty($e)? '' : "$e:"; }, array_keys($this->data->getDocNamespaces())), array(), $xmlData);

        $this->data = simplexml_load_string($xml);

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

        $orgArray['representative'] = '';
        foreach($org->Managers as $key => $manager) {
            if(isset($manager->RelatedSubject->NaturalPersonSubject)){
                $orgArray['representative'] = (string)$manager->RelatedSubject->NaturalPersonSubject->CyrillicName;
            }
        }

        $orgArray['address'] = '';
        foreach ($org->Subject->Addresses as $key => $address) {
            $orgArray['city'] = (isset($address->Location) ? $this->getCityName((string)$address->Location->Code) : '');
            $orgArray['address'] .= (string) (isset($address->AddressType) ? $address->AddressType : '') . ': ' . (string) (isset($address->Street) ? $address->Street : '') . ' ' .
                        ((isset($address->StreetNumber) ? $address->StreetNumber : '')) .
                        ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                        ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                        ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));
        }

        if (isset($org->Subject->Communications)) {
            foreach ($org->Subject->Communications as $key => $communication) {
                if ($communication->Type->Code == 721) { 
                    $orgArray['phone'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
                if ($communication->Type->Code == 723) { 
                    $orgArray['email'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
            }
        }

        //$orgArray['public_benefits'] = 1;
        $orgArray['status'] = 'Y';

        $orgArray['goals'] = (string) (isset($org->ScopeOfActivity->Description) ? $org->ScopeOfActivity->Description : '');
        $orgArray['tools'] = '';

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        //dump($org->MainActivity2008->KID2008->Code);
        return true;
        return isset($org->Subject->LegalEntitySubject) && in_array($org->Subject->LegalEntitySubject->LegalForm, self::LEGAL_FORMS);
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
}
