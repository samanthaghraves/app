<?php

function fnDomainHook(&$parser, &$text)
{ 
  global $action;
  global $exDomainList;

//  $text = "action $action im " . $parser->getOptions()->getInterfaceMessage() . " ns " . 
//    $parser->getTitle()->getNamespace() . " ti " . $parser->getTitle()->getText() . $text;

  if($action === 'edit' || $action === 'history' || $action === 'submit' ||
      $parser->getOptions()->getInterfaceMessage() ||
      $parser->getTitle()->getNamespace() != 0 ||
      !ereg($exDomainList, $parser->getTitle()->getText() ))
    return(true);

  if(strlen($text) < 150)      // gallery goes thru parse 
  {
    return(true);
  }
  $text = wsinfo($parser->getTitle()->getText(), $parser, $text) . $text;

  return(true);
}

//include "ratingdraw.php";

$wswErotik = 0;

function wsinfo($title, $parser, $thetext)
{
  global $dbhost;
  global $dbuser;
  global $dbpass;
  global $db;
  global $wgOut;
  global $wswErotik;

  $forsale = "";

  if(isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], 'oldid='))
    return "\n<!-- no wsinfo -->\n";

  if(strpos($thetext, 'Neue Website anlegen? Dann hier zunächst den Basiseintrag erstellen'))
    return "\n<!-- no wsinfo -->\n";

  $wswErotik = 0;


  $hour = date("H");
  $day = 0;

  $erolink = "http://www.privatelivecams.de/?WMID=5143&CTRLID=Jlc9Mg%3D%3D7&WMEC=5&PID=1";

  if($hour > 6 && $hour < 22)
  {
    $day = 1;
    // $erolink = "http://www.affaire.com/?WMID=5143&CTRLID=&PID=1&WMEC=5&pop=0";
    $erolink = "http://www.dates4you.net/?WMID=5143&CTRLID=&PID=1&WMEC=5&pop=0";
  }

  if(strstr($thetext, "Die Website dieses Artikels entspricht nicht den Bedingungen der deutschen Jugendschutzbestimmungen."))
  {
    $wswErotik = 1;
  }

  if(array_key_exists("Erotik", $parser->mOutput->mCategories) ||
     array_key_exists("Adult", $parser->mOutput->mCategories) ||
     array_key_exists("Pornografie", $parser->mOutput->mCategories) 
     )
  {
    $wswErotik = 1;
  }


//  $wgOut->addScript("<link rel=\"stylesheet\" type=\"text/css\" href=\"/skins/rating/rating.css\" />\n");

  $dom = strtolower($title);

  if(ereg("[^-0-9.a-z]", $dom))
    return "";

  if(array_key_exists("Geparkt", $parser->mOutput->mCategories) ||
     array_key_exists("Baustelle", $parser->mOutput->mCategories) ||
     array_key_exists("Fehlerseiten", $parser->mOutput->mCategories) 
     )
  {
    $forsale = "<a href=\"http://www.sedo.de/checkdomainoffer.php4?partnerid=13318&domain=$dom\" class=\"external text\" target=\"_new\">Ankauf der domain versuchen</a><br>\n";
  }



  if($wswErotik)
    $issex = "sex";
  else
    $issex = "no";

  $armor = substr(md5($dom), 0,16) . 'WsWImG=' . $dom . "=$issex=Ws3ik1Ju5ch=" . substr(md5($dom), 16);
  $crypt = strtr(trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'WsImgS33CCrret', $armor, MCRYPT_MODE_ECB, 
                mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))), '+/', '-!');
  
  $img = "http://thumbs.websitewiki.de/$crypt";

  if(substr_count($dom, ".") > 1)
  {
    if(substr_count($dom, ".") == 2 && (strstr($dom, "or.at") || strstr($dom, "co.at") ) )
      $domlink = "http://www.$dom/";
    else
      $domlink = "http://$dom/";
    $domtext = "$dom";
  }
  else
  {
    $domlink = "http://www.$dom/";
    $domtext = "www.$dom";
  }

  $result = "";

  if($day && $wswErotik)
  {
    $result .= "\n<table border=\"1\" cellspacing=\"0\" cellpadding=\"4\" style=\"float:right; margin:0 0 .5em 1em;
     width:260px; background:#fff; border-collapse:collapse; border:1px solid #999;
     font-size:smaller; line-height:1.5; \" summary=\"Website-Infos f&uuml;r $title\">
     <tr><td style=\"text-align:center; background:#c0c0c0;\"><b><a href=\"/Website-Infos\">Website-Infos</a>
     f&uuml;r $title</b></td></tr>
     <tr><td style=\"text-align:center; background:#f0f0f0;\"><img src=\"$img\"
	 width=\"250\" height=\"188\" alt=\"Screenshot $domtext\" border=\"0\" /></td></tr>
     <tr>$forsale<td style=\"text-align:center; background:#f0f0f0;\"><a href=\"/Spezial:Checksite/?param=$dom\" rel=\"nofollow\">&Uuml;berpr&uuml;fen</a>
	 </td></tr>
	 \n";
  }
  else
  {
    $result .= "\n<table border=\"1\" cellspacing=\"0\" cellpadding=\"4\" style=\"float:right; margin:0 0 .5em 1em;
     width:260px; background:#fff; border-collapse:collapse; border:1px solid #999;
     font-size:smaller; line-height:1.5; \" summary=\"Website-Infos f&uuml;r $title\">
     <tr><td style=\"text-align:center; background:#c0c0c0;\"><b><a href=\"/Website-Infos\">Website-Infos</a>
     f&uuml;r $title</b></td></tr>
     <tr><td style=\"text-align:center; background:#f0f0f0;\"><a href=\"$domlink\"
         title=\"$domtext\" rel=\"nofollow\" target=\"_new\"><img src=\"$img\"
	 width=\"250\" height=\"188\" alt=\"Screenshot $domtext\" border=\"0\" /></a></td></tr>
     <tr><td style=\"text-align:center; background:#f0f0f0;\"><a href=\"$domlink\"
         title=\"$domtext\" class=\"external text\" target=\"_new\">$domtext</a><br>
	 $forsale<a href=\"/Spezial:Checksite/?param=$dom\" rel=\"nofollow\">&Uuml;berpr&uuml;fen</a>
	 </td></tr>
	 \n";
  }

  global $wgUploadDirectory, $wgUploadPath;

  $rev = Revision::newFromTitle( $parser->getTitle() );
  if(is_object($rev))
  {
    $wikitext = $rev->getText();
    $regs = array();
    if(ereg('{{PLZ\|(D|A|CH)-([0-9]{4,5})}}', $wikitext, $regs) &&
      (($regs[1] == 'D' && strlen($regs[2]) == 5)  || ($regs[1] != 'D' && strlen($regs[2]) == 4)))
    {
      $country = $regs[1];
      $myplz = $regs[2];
      $imgurl = '/plzmap/' . strtolower($country{0}) . $myplz{0} .
	'/' . strtolower($country{0}) . $myplz . '.png';
      $imgfile = $wgUploadDirectory . $imgurl;

      if(file_exists($imgfile))
      {
	$result .= <<< EOI
	<tr><td align="center" style="background:#ffffff;"><div style="position:relative; top:6px; width:250px;
	  height:0px; text-align:center;"><strong><a href="/Kategorie:$country-$myplz" style="background-color:white;">&nbsp;$country-$myplz&nbsp;</a></strong></div>
	<img src="$wgUploadPath$imgurl" width="250" height="200" alt="$country-$myplz">
	<div style="position:relative; bottom:15px; left:3px; width:250px; height:0px; text-align:left;"><a href="/Landkarten_Copyright_OpenStreetMap" title="&copy; OpenStreetMap contributors">&copy; OSM</a></div>
	 </td></tr>

EOI;
      }
    }
  }
/*
  $result .= "<tr><td align=\"center\" style=\"background:#ffffff;\"><strong><a
    href=\"/Spezial:Bewertungen\">Website-Bewertung</a></strong><br />\n";
  $result .= rating_bar($dom, 5);
  $result .= "</td></tr>\n";
*/
  global $wgTitle;

  $bookurl = $wgTitle->getFullURL();
  $bookdesc = "WebsiteWiki+$title";
  $result .= "<tr><td align=\"center\" style=\"background:#ffffff;\"><strong>
    Bookmarken bei ...</strong><br />\n";
  $result .= "<a href=\"http://www.mister-wong.de/index.php?action=addurl&amp;bm_url=$bookurl&amp;bm_description=$bookdesc\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/mrwong.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Mr. Wong\" alt=\"Mr. Wong\"></a>\n";
  $result .= "<a href=\"http://www.google.com/bookmarks/mark?op=add&amp;hl=de&amp;bkmk=$bookurl&amp;title=$bookdesc\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/google.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Google\" alt=\"Google\"></a>\n";
  $result .= "<a href=\"http://del.icio.us/post?url=$bookurl&amp;title=$bookdesc\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/delicious.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"del.icio.us\" alt=\"del.icio.us\"></a>\n";
  $result .= "<a href=\"http://linkarena.com/bookmarks/addlink/?url=$bookurl'&amp;title=$bookdesc&amp;desc=&amp;tags=\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/linkarena.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Linkarena\" alt=\"Linkarena\"></a>\n";
  $result .= "<a href=\"http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Description=&amp;Url=$bookurl&amp;Title=$bookdesc\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/blinklist.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Blinklist\" alt=\"Blinklist\"></a>\n";
  $result .= "<a href=\"http://myweb2.search.yahoo.com/myresults/bookmarklet?u=$bookurl&amp;t=$bookdesc\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/yahoo.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Yahoo\" alt=\"Yahoo\"></a>\n";
  $result .= "<a href=\"http://twitter.com/home/?source=WebsiteWiki&amp;status=$bookdesc - $bookurl\" target=\"_new\" rel=\"nofollow\"><img src=\"$wgUploadPath/social/twitter.gif\" border=\"0\" width=\"20\" height=\"20\" title=\"Twitter\" alt=\"Twitter\"></a>\n";
  $result .= "</td></tr>\n";

  if($wswErotik)
  {
    $result .= "<tr><td align=\"center\" style=\"background:#ffffff;\">Anzeige<p>
    <a href=\"$erolink\" rel=\"nofollow\" target=\"_new\"><img
    src=\"http://www.sexnutz.de/images/soft_200x200.jpg\" alt=\"Erotik\" width=\"200\" height=\"200\" border=\"0\">
    </td></tr>\n";
  } 

/**** no wellness and bilder 
  else if(stristr($thetext, "wellness"))
  {
    $result .= <<<EOI
    <tr><td align="center" height="250" style="background:#ffffff">
    <table width="100%" height="100%" style="font-size:larger; line-height:1.4; font-family:arial,sans-serif;">
    <tr><td align="left" height="250" style="margin:2px; background-color:#f0f0f0; ">
      <a href="http://www.wellness-wochenende.com/" style="text-decoration:none; font-family:arial,sans-serif;"><span style="font-weight:bold; color:#002bb8; text-decoration:underline;">Wellness-Wochenende</span><br>
      <span style="color:black; text-decoration:none;}">Kurzurlaub? - Entspannen beim Wellness-Wochenende</span><br>
      <span style="color:#002bb8; text-decoration:underline;">www.wellness-wochenende.com</span></a>
    </td></tr>
    </table>
    </td></tr>
EOI;
  }
  else if(stristr($thetext, "bilder"))
  {
    $result .= <<<EOI
    <tr><td align="center" height="250" style="background:#ffffff">
    <table width="100%" height="100%" style="font-size:larger; line-height:1.4; font-family:arial,sans-serif;">
    <tr><td align="left" height="250" style="margin:2px; background-color:#f0f0f0; ">
      <a href="http://www.luftbilder.aero/" style="text-decoration:none; font-family:arial,sans-serif;"><span style="font-weight:bold; color:#002bb8; text-decoration:underline;">Luftbilder.aero</span><br>
      <span style="color:black; text-decoration:none;}">Tolle Luftbilder aus Deutschland und der ganzen Welt.</span><br>
      <span style="color:#002bb8; text-decoration:underline;">www.Luftbilder.aero</span></a>
    </td></tr>
    </table>
    </td></tr>
EOI;
  }
********** end wellness ***/
  else
  {
    global $wgUser;
    if(is_object($wgUser) && User::isIP($wgUser->getName()))
    {
      $result .= <<<EOI
<tr><td align="center" height="250" style="background:#ffffff;">
<script type="text/javascript"><!--
google_ad_client = "pub-7078170683927487";
/* 250x250, created 1/8/10 for websitewiki.de hard coded slot */
google_ad_slot = "2130596228";
google_ad_width = 250;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</td></tr>
EOI;
    }
  }

  $result .= "</table>\n";

  return "\n<!-- wsinfo -->$result<!-- /wsinfo -->\n";
}

############## Hot Link / Legal

# Define a setup function
$wgExtensionFunctions[] = 'wfWsWParserFuncHotLink_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]       = 'wfWsWParserFuncHotLink_Magic';

function wfWsWParserFuncHotLink_Setup() {
        global $wgParser;
        # Set a function hook associating the "hotlink" magic word with our function
        $wgParser->setFunctionHook( 'hotlink', 'wfWsWParserFuncHotLink_Render', 1 );
        $wgParser->setFunctionHook( 'legal', 'wfWsWParserFuncLegal_Render', 1 );
}


function wfWsWParserFuncHotLink_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['hotlink'] = array( 0, 'hotlink' );
        $magicWords['legal'] = array( 0, 'legal' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}


function wfWsWParserFuncHotLink_Render( &$parser, $param1 = '0') {
        # The parser function itself
        # The input parameters are wikitext with templates expanded
        # The output should be wikitext too
        global $wgOut;
        global $wgUser;
        
       $result = "<a onclick=\"alert('Einfach diesen Link auf den Toolbar des Browsers ziehen (Linke Maustaste halten, ziehen und loslassen)'); return false;\" href=\"javascript:d=document;d.location.href='http://www.websitewiki.de/Spezial:NeueWebsite?param='+escape(d.location.href);\"><b>Ins WebsiteWiki!</b></a>";

       return array($result, 'noparse' => true, 'isHTML' => true);
}


function wfWsWParserFuncLegal_Render( &$parser, $param1 = '0') {
        # The parser function itself
        # The input parameters are wikitext with templates expanded
        # The output should be wikitext too
        global $wgOut;
        global $wgUser;
        
       $result =<<< EOLEGAL
<img src="http://legal.netznutz.net/legal.png"><br>
E-Mail-Kontakt Betreiber: info@websitewiki.de<p>

Jugendschutzbeauftragter gemäß JMStV §7:<br>
<a href="http://www.JS-Beauftragter.de/">JS-Beauftragter.de</a><br>
Mail: info@js-beauftragter.de<p>

EOLEGAL;

       return array($result, 'noparse' => true, 'isHTML' => true);
}
