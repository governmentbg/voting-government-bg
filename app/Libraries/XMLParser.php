<?php

namespace App\Libraries;

/**
 * XML parser specific for trade register xml files format
 */
class XMLParser implements IXMLParser
{
    private $data;

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
                //dump($parsedOrg);
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

        if (isset($org->SubDeed->Seat->Address)) {
            $address = $org->SubDeed->Seat->Address;
            $orgArray['city'] = (string) (isset($address->Settlement) ? $address->Settlement : '');
            $orgArray['address'] = (string) (isset($address->Street) ? $address->Street : '') . ' ' .
                    ((isset($address->StreetNumber) ? $address->StreetNumber : '')) .
                    ((isset($address->Entrance) && !empty((string) $address->Entrance) ? ' вх. ' . (string) $address->Entrance : '')) .
                    ((isset($address->Floor) && !empty((string) $address->Floor) ? ' ет. ' . (string) $address->Floor : '')) .
                    ((isset($address->Apartment) && !empty((string) $address->Apartment) ? ' ап. ' . (string) $address->Apartment : ''));
        }

        if (isset($org->SubDeed->Seat->Contacts)) {
            $contact = $org->SubDeed->Seat->Contacts;
            $orgArray['phone'] = (string) (isset($contact->Phone) ? $contact->Phone : '');
            $orgArray['email'] = (string) (isset($contact->EMail) ? $contact->EMail : '');
        }

        $publicBenefit = 0;
        foreach ($org->SubDeed as $key => $deed) {
            if (isset($deed->attributes()['SubUICType']) && (string) $deed->attributes()['SubUICType'] == 'MainCircumstances') {
                $publicBenefit += (int) $deed[0]->DesignatedToPerformPublicBenefit;
                break;
            }
        }
        $orgArray['public_benefits'] = $publicBenefit;
        $orgArray['status'] = (string) $org->attributes()['DeedStatus'];

        $orgArray['representative'] = '';
        foreach($org->SubDeed->Representatives103 as $key => $representative) {
            if(isset($representative->Representative103->Person->attributes()['Position'])){
                $orgArray['representative'] .= ' ' . (string)$representative->Representative103->Person->attributes()['Position'] . ':';
            }
            $orgArray['representative'] .= (string)$representative->Representative103->Person->Name;
        }

        $orgArray['goals'] = (string) (isset($org->SubDeed->Objectives->Text) ? $org->SubDeed->Objectives->Text : '');
        $orgArray['tools'] = (string) (isset($org->SubDeed->MeansOfAchievingTheObjectives) ? $org->SubDeed->MeansOfAchievingTheObjectives : '');

        return $orgArray;
    }

    private function isOrgRelevant($org)
    {
        return isset($org->attributes()['LegalForm']) && in_array((string) $org->attributes()['LegalForm'], self::LEGAL_FORMS);
    }
}
