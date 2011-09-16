<?php

namespace Sparkling\VATBundle\Service;

use Sparkling\VATBundle\Exception\InvalidCountryCodeException;
use Sparkling\VATBundle\Exception\InvalidVATNumberException;

class VATService
{
	public function validate($countryCode, $vatNumber)
	{
		if(!preg_match('/^[A-Z]{2}$/', $countryCode))
			throw new InvalidCountryCodeException('The countrycode is not valid. It must be in format [A-Z]{2}');

		if(!preg_match('/^[0-9A-Za-z\+\*\.]{2,12}$/', $vatNumber))
			throw new InvalidVATNumberException('The VAT number is not valid. It must be in format [0-9A-Za-z\+\*\.]{2,12}');

		try
		{
			ini_set("soap.wsdl_cache_enabled", 0);

			$client = new \SoapClient(__DIR__.'/../Resources/wsdl/checkVatService.wsdl', array(
				'soap_version'  => SOAP_1_1,
				'style'         => SOAP_DOCUMENT,
				'encoding'      => SOAP_LITERAL,
				'location'      => 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService',
				'trace' => 1
			));

			$result = $client->checkVat(array(
				'countryCode'   => $countryCode,
				'vatNumber'     => $vatNumber
			));

			return $result->valid ?  true : false;
		}
		catch(\SoapFault $exception)
		{
			throw new VATException($exception);
		}
	}
}