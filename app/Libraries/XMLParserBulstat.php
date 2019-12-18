<?php

namespace App\Libraries;

/**
 * XML parser specific for Bulstat register xml files format
 */
class XMLParserBulstat implements IXMLParser
{
    private $data;

    public function __construct()
    {
        ;
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
     * @param string $path
     * @return array
     */
    public function getParsedData()
    {
        if(!isset($this->data->Body->StateOfPlay[0])){
            return [];
        }

        $result = [];
        foreach($this->data->Body->StateOfPlay[0] as $org) {
            if(isset($org->attributes()['UIC']) && $this->isOrgRelevant($org)){
                $parsedOrg = $this->getRelevantFields($org);
                if($parsedOrg){
                    $result[] = $parsedOrg;
                }
            }
        }

        return $result;
    }

    private function getRelevantFields($org)
    {
        $orgArray = [];

        if(isset($org->attributes()['UIC'])){
            $orgArray['eik'] = (string)$org->attributes()['UIC'];
        }
        else{
            return false;
        }

        if(isset($org->attributes()['CompanyName'])){
            $orgArray['name'] = (string)$org->attributes()['CompanyName'];
        }
        else{
            return false;
        }

        if(isset($org->SubDeed->Seat->Address)){
            $address = $org->SubDeed->Seat->Address;
            $orgArray['city'] = (string)(isset($address->Settlement) ? $address->Settlement : '');
            $orgArray['address'] = (string)(isset($address->Street) ? $address->Street : '') . ' ' .
                    ((isset($address->StreetNumber) ? $address->StreetNumber : '')) .
                    ((isset($address->Entrance) && !empty((string)$address->Entrance) ? ' вх. ' . (string)$address->Entrance : '')) .
                    ((isset($address->Floor) && !empty((string)$address->Floor) ? ' ет. ' . (string)$address->Floor : '')) .
                    ((isset($address->Apartment) && !empty((string)$address->Apartment) ? ' ап. ' . (string)$address->Apartment : ''));
        }

        if(isset($org->SubDeed->Seat->Contacts)){
            $contact = $org->SubDeed->Seat->Contacts;
            $orgArray['phone'] = (string)(isset($contact->Phone) ? $contact->Phone : '');
            $orgArray['email'] = (string)(isset($contact->EMail) ? $contact->EMail : '');
        }

        $publicBenefit = 0;
        foreach($org->SubDeed as $key => $deed) {
            if(isset($deed->attributes()['SubUICType']) && (string)$deed->attributes()['SubUICType'] == 'MainCircumstances'){
                $publicBenefit += (string)$deed[0]->DesignatedToPerformPublicBenefit;
                break;
            }
        }
        $orgArray['public_benefits'] = $publicBenefit;
        $orgArray['status'] = (string)$org->attributes()['DeedStatus'];

        $orgArray['goals'] = (string)(isset($org->SubDeed->Objectives->Text) ? $org->SubDeed->Objectives->Text : '');
        $orgArray['tools'] = (string)(isset($org->SubDeed->MeansOfAchievingTheObjectives) ? $org->SubDeed->MeansOfAchievingTheObjectives : '');

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        return true; //TODO
    }
}


