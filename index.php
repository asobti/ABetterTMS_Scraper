<?php
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 

	//includes
	include_once('simple_html_dom.php');
	include_once('parser.php');
	include_once('scraper.php');
	include_once('dbmanager.php');
	
	$mainURL = "https://duapp3.drexel.edu/webtms_du/";
	$collegesURL = "https://duapp3.drexel.edu/webtms_du/Colleges.asp?Term=201125&univ=DREX";
	
	$parser = new BetterTMS_Parser;
	$scraper = new BetterTMS_Scraper($mainURL);
	$dbmanager = new BetterTMS_DBManager;
	
	
	$terms = $scraper->scrapeTerms();
	$termsParsed = $parser->parseTerms($terms);
	$dbmanager->storeTerms($termsParsed);	
	
	foreach($termsParsed as $term)
	{
		$college = $scraper->scrapeSubjects($term['termlink']);
		$collegeObject = $parser->parseSubjects($college);
		$dbmanager->storeColleges($collegeObject);
		
		foreach($collegeObject as $college)
		{
			$dbmanager->storeSubjects($college);
		}
	}
	
	$subjectLinks = $dbmanager->getSubLinks();	// should return array of all links
	
	$term['termlink'] = "https://duapp3.drexel.edu/webtms_du/Colleges.asp?Term=201125&univ=DREX";
	$term['termcode'] = '201125';
	
	foreach($subjectLinks as $subjectLink)
	{		
		$courses = $scraper->scrapeCourses($subjectLink,$term);	
		$coursesparsed = $parser->parseCourses($courses);
		$dbmanager->storeCourses($coursesparsed, $term);		
	}
	
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime); 
