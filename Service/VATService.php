<?php

namespace Sparkling\VATBundle\Service;

use Sparkling\VATBundle\Exception\VATException;
use Sparkling\VATBundle\Exception\InvalidCountryCodeException;
use Sparkling\VATBundle\Exception\InvalidVATNumberException;

class VATService
{
    static $validCountries = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');

    public function validate($countryCode, $vatNumber = null)
    {
        if (!isset($vatNumber)) {
            $vatNumber = substr($countryCode, 2);
            $countryCode = substr($countryCode, 0, 2);
        }

        $countryCode = preg_replace('/[^a-zA-Z]/', '', $countryCode);
        $vatNumber = preg_replace('/[^a-zA-Z0-9]/', '', $vatNumber);

        if(!preg_match('/^[A-Z]{2}$/', $countryCode))
            throw new InvalidCountryCodeException('The countrycode is not valid. It must be in format [A-Z]{2}');

        if(!in_array($countryCode, self::$validCountries))
            throw new InvalidCountryCodeException('The countrycode is not valid. It must be one of '.implode(', ', self::$validCountries));

        if(!preg_match('/^[0-9A-Za-z\+\*\.]{2,12}$/', $vatNumber))
            throw new InvalidVATNumberException('The VAT number is not valid. It must be in format [0-9A-Za-z\+\*\.]{2,12}');

        return $this->checkWithVIES($countryCode, $vatNumber);
    }

    protected function checkWithVIES($countryCode, $vatNumber)
    {
        try {
            ini_set("soap.wsdl_cache_enabled", 0);

            $client = new \SoapClient(__DIR__.'/../Resources/wsdl/checkVatService.wsdl', array(
                'soap_version'  => SOAP_1_1,
                'style'         => SOAP_DOCUMENT,
                'encoding'      => SOAP_LITERAL,
                'location'      => 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService',
                'trace'         => 1
            ));

            $result = $client->checkVat(array(
                'countryCode'   => $countryCode,
                'vatNumber'     => $vatNumber
            ));

            return $result->valid ?  true : false;
        } catch (\SoapFault $exception) {
            throw new VATException($exception);
        }
    }

    protected function checkWithAppspot($countryCode, $vatNumber)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_URL, 'http://isvat.appspot.com/' . addslashes($countryCode) . '/' . addslashes($vatNumber) . '/'); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result == 'true';
    }
}
