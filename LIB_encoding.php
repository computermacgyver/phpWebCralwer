<?php
# Include http, parse, and address resolution libraries
#include("LIB_http.php");
#include("LIB_parse.php");

function  get_encoding($strHTML,$header=false)  {

	$enc="";
	if ($header) {//try to pull encoding from header information
		$strHeader = substr($strHTML,0,strpos($strHTML,"<"));
		//looking for line Content-Type: text/html; charset=utf-8
		$pos=strpos($strHeader,"charset=");
		if ($pos!==FALSE) {
			$pos2=strpos($strHeader,"\n",$pos);
			$enc=substr($strHeader,$pos+8,$pos2-$pos-8);
		}
	}
	#$head_section = return_between($string=$strHTML, $start="<head>", $end="</head>", $type=EXCL);

	# Create an array of all the meta tags
	$meta_tag_array = parse_array($strHTML, $beg_tag="<meta", $close_tag=">");
	$new_page="";
	# Examine each meta tag for a redirection command
	for($xx=0; $xx<count($meta_tag_array); $xx++)
	    {
	    # Look for http-equiv attribute
	    $meta_attribute = get_attribute($meta_tag_array[$xx], $attribute="http-equiv");
	    #echo $meta_tag_array[$xx] . "\n";
	    if(strtolower($meta_attribute)=="content-type") {
		#echo "HERE!";
		$new_page = return_between($meta_tag_array[$xx], $start="charset", $end=">", $type=EXCL);
		
		# Clean up URL
		$new_page = trim(str_replace("", "", $new_page));
		$new_page = str_replace("/", "", $new_page);
		$new_page = str_replace("=", "", $new_page);
		$new_page = str_replace("\"", "", $new_page);
		$new_page = str_replace("'", "", $new_page);      
		$new_page = str_replace(" ", "", $new_page); 
		break;
		}
	    }
	    if (strlen($enc)>0 && strlen($new_page)>0) {
			return $enc . "," . $new_page;
	    } elseif (strlen($new_page)>0){
   		    return $new_page;
	    } elseif (strlen($enc)>0){
   		    return $enc;
	    } else {
		return "";
	    }
}

function getLanguage($text) {
#Sample Response: {"responseData": {"language":"en","isReliable":false,"confidence":0.0867152}, "responseDetails": null, "responseStatus": 200}
#URL http://ajax.googleapis.com/ajax/services/language/detect?v=1.0&q=
	$URL="http://ajax.googleapis.com/ajax/services/language/detect?v=1.0&q=" . urlencode($text);
	$downloaded_page = http_get($URL, "");
	$lang = $downloaded_page['FILE'];
	$lang = return_between($lang,"\"language\":\"","\",",EXCL);
	echo "LANGUAGE IS: $lang\n\n";
	return $lang;
}

function strtolower_utf8($string){ 
  $convert_to = array( 
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", 
    "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", 
    "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж", 
    "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", 
    "ь", "э", "ю", "я" 
  ); 
  $convert_from = array( 
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", 
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", 
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", 
    "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ", 
    "Ь", "Э", "Ю", "Я" 
  ); 

  return str_replace($convert_from, $convert_to, $string); 
}
# Echo results of script
#echo "HTML Head redirection detected<br>";
#echo "Redirect page = ".$new_page;

?>
