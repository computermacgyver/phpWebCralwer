<?php
# The spider will exclude any links containing any of the following substrings
#SAH: These are now matched in regular expressions: preg_match($exclusion_array[$xx],$link)>0

$exclusion_array[] = "/googlesyndication/i";         // Exclude Google AdWords links.
$exclusion_array[] = "/doubleclick\.net/i";           // Exclude doubleclick banner ads
$exclusion_array[] = "/\.pdf\b/i";
$exclusion_array[] = "/\.wmv\b/i";
$exclusion_array[] = "/\.mp3\b/i";
$exclusion_array[] = "/\.avi\b/i";
$exclusion_array[] = "/\.doc\b/i";
$exclusion_array[] = "/\.docx\b/i";
$exclusion_array[] = "/\.xls\b/i";
$exclusion_array[] = "/\.xlsx\b/i";
$exclusion_array[] = "/\.zip\b/i";
$exclusion_array[] = "/\.tar\b/i";
$exclusion_array[] = "/\.tar.gz\b/i";
$exclusion_array[] = "/\.flv\b/i";
$exclusion_array[] = "/\.avi\b/i";
$exclusion_array[] = "/\.wav\b/i";
$exclusion_array[] = "/\.mid\b/i";
$exclusion_array[] = "/\.swf\b/i";
$exclusion_array[] = "/mailto\:/i";
$exclusion_array[] = "/\.mov\b/i";
$exclusion_array[] = "/\.mp3\b/i";
$exclusion_array[] = "/\.aa\b/i";
$exclusion_array[] = "/\.mpg\b/i";
$exclusion_array[] = "/\.jpg\b/i";
$exclusion_array[] = "/\.png\b/i";
$exclusion_array[] = "/\.gif\b/i";
$exclusion_array[] = "/\.ppt\b/i";
$exclusion_array[] = "/\.pptx\b/i";
$exclusion_array[] = "/\.odt\b/i";
$exclusion_array[] = "/\mgconvert2pdf\.aspx/i";


/*$exclusion_array[] = "/en/Diol1/EmploymentInteractiveTools/DG_065397";
$exclusion_array[] = "/en/Diol1/EmploymentInteractiveTools/DG_10028440";
$exclusion_array[] = "/en/Diol1/EmploymentInteractiveTools/DG_10028030";
$exclusion_array[] = "/en/Diol1/EmploymentInteractiveTools/DG_065384";
$exclusion_array[] = "/en/Diol1/EmploymentInteractiveTools/DG_10028510";
$exclusion_array[] = "/en/Diol1/MotoringDecisionTrees/DuplicateLicences";
$exclusion_array[] = "/en/Diol1/MotoringDecisionTrees/HowToImportorExportaVehicle";
$exclusion_array[] = "/en/Diol1/MotoringDecisionTrees/ExchangeaTestPassorForeignLicence";
$exclusion_array[] = "/en/Diol1/MotoringDecisionTrees/DG_10034670";
$exclusion_array[] = "/en/Hl1/Help/ContactUs/ContactUsForm/DG_179941";
$exclusion_array[] = "/en/MoneyTaxAndBenefits/BenefitsTaxCreditsAndOtherSupport/Inretirement/DG_10018657";
$exclusion_array[] = "/en/ContentItemManager";
$exclusion_array[] = "idcservice=";
$exclusion_array[] = "/dr_consum_dg/";
$exclusion_array[] = "/en/AdvancedSearch/";
$exclusion_array[] = "/en/AdvancedSearch";
$exclusion_array[] = "/en/AdvancedSearch/Searchresults/index.htm ";
$exclusion_array[] = "/en/AdvancedSearch/top100/DG_072883";
$exclusion_array[] = "/en/Diol1/SAGA";
$exclusion_array[] = "/en/EducationAndLearning/UniversityAndHigherEducation/StudentFinance/DG_181283";
$exclusion_array[] = "/en/MoneyTaxAndBenefits/BenefitsTaxCreditsAndOtherSupport/Inretirement/DG_10018668";
$exclusion_array[] = "/prod_consum_dg/groups/dg_digitalassets/@dg/@en/@over50/documents/digitalasset/dg_180219.pdf";
$exclusion_array[] = "/en/Diol1/DoItOnline/DG_184845";*/
//$exclusion_array[] = "/en/Diol1/DoItOnline/DG_184845?cid=rss";

#http[s]://.*http[s]://.*
#http[s]://.*http[s]%3A%2F%2F.*
#http://www.nytimes.com/adx/bin/adx_click.html?
#valuecommerce.com
#$exclusion_array[] = "http://add.my.yahoo.com/rss?" //just a link to bookmark
#http://del.icio.us/post?
#http://reddit.com/submit?
#http://www.stumbleupon.com/submit?
#http://digg.com/submit?
#http://www.facebook.com/sharer.php?
#http://technorati.com/faves?
?>
