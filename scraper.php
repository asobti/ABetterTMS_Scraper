<?php
/*
	This class is responsible for the screen scraping.
*/
	include_once('simple_html_dom.php');	
	
	class BetterTMS_Scraper
	{
		private $mainURL;
		
		function __construct($mainURL)
		{
			$this->mainURL = $mainURL;
		}
		
		//scrapes the main TMS page for the terms available and returns the HTML DOM tree
		function scrapeTerms()
		{
			$ch = curl_init();
			//curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $this->mainURL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$termHtml = curl_exec($ch);
			curl_close($ch);
			
			return str_get_html($termHtml);
		}
		
		function scrapeSubjects($termLinks)
		{
			$ckfile = tempnam ("/tmp", "CURLCOOKIE");
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $this->mainURL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			curl_close($ch);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $termLinks);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$coursedata = curl_exec($ch);
			curl_close($ch);
			
			return str_get_html($coursedata);
		}
		
		//should assume $link is array and iterate over all values
		function scrapeCourses($link, $term)
		{
			$ckfile = tempnam ("/tmp", "CURLCOOKIE");
			$ckfile2 = tempnam ("/tmp", "CURLCOOKIE2");
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $this->mainURL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			curl_close($ch);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $term['termlink']);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			curl_close($ch);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			return str_get_html($data);


		}
	}
	/*
	$ckfile = tempnam ("/tmp", "CURLCOOKIE");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$termdata = curl_exec($ch);
	curl_close($ch);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$collegedata = curl_exec($ch);
	curl_close($ch);
	
	$termdata = str_get_html($termdata);
	
	foreach ($termdata->find('TD[width=92%]') as $term)
	{
		echo "<br/><br/>";
		echo $term->find('a',0)->plaintext;
		echo "  :  ";
		echo $term->find('a',0)->href;
	}
	
	*/


/*
	include_once('simple_html_dom.php');
	
	$url = "https://duapp3.drexel.edu/webtms_du/";
	$url2 = "https://duapp3.drexel.edu/webtms_du/Colleges.asp?Term=201125&univ=DREX";
	$ckfile = tempnam ("/tmp", "CURLCOOKIE");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	
	$html =  str_get_html($data);
	//$ret = trim($html->find('FONT[color=800000]',0)->plaintext);
	
	foreach($html->find('TD[width=300]') as $table)
	{
		echo "<br/><br/><br/>";
		echo trim($table->find('FONT[COLOR=800000]',0)->plaintext) . "<br/>";
		
		foreach($table->find('a') as $major)
			echo $major->plaintext . "  (https://duapp3.drexel.edu/webtms_du/". $major->href .")<br/>";
	}
	//var_dump($matches);
*/

?>