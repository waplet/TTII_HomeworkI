<?php
/**
 * This is the main file which receives and analyzes data, 
 * generates response data and finally calls the template.
 */
 /*
 * Lot of bugs with conversions from string to number format, to floats, and so on. MJ
 */
///error_reporting(E_ALL);
//ini_set("display_errorrs","on");

$result_status = ""; //valid values: empty string, "success", "error"
$result_message = "";
$target_currencies = array('LVL', 'GBP', 'RUB', 'CHF', 'SEK', 'NOK', 'JPY', 'LTL');
$source_currencies = array('LVL','EUR');
if(!empty($_GET))
{
	if(!is_numeric($_GET['amount']) || !((float)$_GET['amount'] >= 0))
	{
		$result_status = "error";
		$result_message = "Incorrect amount";
	}
	else if(!in_array($_GET['target'],$target_currencies))
	{
		$result_status = "error";
		$result_message = "No currency selected";
	}
	else if(!in_array($_GET['source'],$source_currencies))
	{
		$result_status = "error";
		$result_message = "No converting value selected";
	}
	else
	{
		if($_GET['source'] == "LVL")
		{
			$url = 'http://www.bank.lv/vk/xml.xml';
			$xml = simplexml_load_file($url);
			if(!$xml)
			{
				$result_status = "error";
				$result_message = "No currency rates available";
			}
			else
			{
				if($_GET['target'] != "LVL")
				{
				$rate = $xml->xpath("//Currency[ID=\"".(string)$_GET['target']."\"]");
				$rate = $rate[0];
				}
				else
				{
					$rate->Rate = 1.0;
					$rate->Currency = "LVL";
					$rate->Units = 1;
					
				}
				$result_status = "success";
            
            $lvls = number_format((float)$_GET['amount']*(float)$rate->Rate/(int)$rate->Units,2,'.','');
            
				$result_message = "Calculation result: ". number_format($_GET['amount'],2,'.','') ." ".$_GET['target']." equals to ".$lvls ." ". $_GET['source'].". ";
				
				$rateeur = $xml->xpath("//Currency[ID=\"EUR\"]");
				$rateeur = $rateeur[0];
				$euros = number_format(number_format((float)$_GET['amount']*(float)$rate->Rate/(int)$rate->Units,2,'.','')/(float)$rateeur->Rate,2,'.','');// no piemeram britu naudas iegustam LVL un tad konvertejam uz euro.
				$goldfixing = file_get_contents("http://www.goldfixing.com/vars/goldfixing.vars");
				preg_match_all("/&(am|pm)euro=(.*?) &/", $goldfixing,$prices);
				$price = ($prices[2][0] != "") ? (float)$prices[2][0] : (float)$prices[2][1];
				$ounces = (float)$euros/$price;
				$result_message .= " Amount of gold you could get: ". number_format($ounces,2,'.','') ." oz";
			}	
		}
		else
		{
			$xml=simplexml_load_file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
			$xml->registerXPathNamespace("ns0","http://www.ecb.int/vocabulary/2002-08-01/eurofxref");//bugfix priekš xpath'a,jo tukšs namespace nevar būt. http://stackoverflow.com/questions/1805292/why-my-xpath-request-to-xml-file-on-web-doesnt-work
			
			//the file is updated daily between 2.15 p.m. and 3.00 p.m. CET
			if(!$xml)
			{
				$result_status = "error";
				$result_message = "No currency rates available";
			}
			else
			{
				$rate = $xml->xpath("//ns0:Cube[@currency=\"".$_GET['target']."\"]");
            $rate = $rate[0]->attributes();
				$result_status = "success";
            
            $euros = number_format((float)$_GET['amount']/(float)$rate->rate,2,'.','');
            
				$result_message = "Calculation result: ". number_format($_GET['amount'],2,'.','') ." ".$_GET['target']." equals to ".$euros ." ". $_GET['source'].". ";
            /**
             No need of EUR converting to EUR
             $euros - already setting before.
            **/
				$goldfixing = file_get_contents("http://www.goldfixing.com/vars/goldfixing.vars");
				preg_match_all("/&(am|pm)euro=(.*?) &/", $goldfixing,$prices);
				$price = ($prices[2][0] != "") ? (float)$prices[2][0] : (float)$prices[2][1];
				$ounces = (float)$euros/$price;
				$result_message .= " Amount of gold you could get: ". number_format($ounces,2,'.','') ." oz";
         }
		}
	}
}
require("view.php");