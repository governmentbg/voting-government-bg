<?php

namespace App\Libraries;

/**
 * XML parser specific for Bulstat register xml files format
 */
class XMLParserBulstat implements IXMLParser
{
    const LEGAL_FORMS = ['Сдружение'/*'ASSOC', 'FOUND', 'CC', 'BFLE'*/]; //todo check forms

    private $data;

    public function __construct()
    {
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

    public static function getRelevantFields($org)
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

        $orgArray['address'] = '';
        foreach ($org->Subject->Addresses as $key => $address) {
            $orgArray['city'] = (string) (isset($address->Location) ? $address->Location : '');
            $orgArray['address'] .= (string) (isset($address->AddressType) ? $address->AddressType : '') . ': ' . (string) (isset($address->Street) ? $address->Street : '') . ' ' .
                        ((isset($address->StreetNumber) ? $address->StreetNumber : '')) .
                        ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                        ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                        ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));
        }

        if (isset($org->Subject->Communications)) {
            foreach ($org->Subject->Communications as $key => $communication) {
                if ($communication->Type->Code == 721) { //todo Code
                    $orgArray['phone'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
                if ($communication->Type == 'имейл') { //todo Code
                    $orgArray['email'] = (string) (isset($communication->Value) ? $communication->Value : '');
                }
            }
        }

        $orgArray['public_benefits'] = 1; //TODO check
        $orgArray['status'] = 'Y';

        //$org->Subject->ScopeOfActivity = 530
        $orgArray['goals'] = (string) (isset($org->Subject->ScopeOfActivity->Description) ? $org->Subject->ScopeOfActivity->Description : '');
        $orgArray['tools'] = (string) (isset($org->SubDeed->MeansOfAchievingTheObjectives) ? $org->SubDeed->MeansOfAchievingTheObjectives : ''); //todo

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        return true;
        return isset($org->Subject->LegalEntitySubject) && in_array($org->Subject->LegalEntitySubject->LegalForm, self::LEGAL_FORMS);
    }
}
