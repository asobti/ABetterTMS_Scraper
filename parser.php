<?php

/*
	This class is responsible for parsing the information and dumping it in the MySQL database
*/

	class BetterTMS_Parser
	{
		function parseTerms($terms)
		{
			$termArray;
			foreach ($terms->find('TD[width=92%]') as $term)
			{
				
				$termObject['termname'] = $term->find('a',0)->plaintext;
				$termObject['termlink'] = "https://duapp3.drexel.edu/webtms_du/" . $term->find('a',0)->href;
				
				$regex = "/Term=([0-9]{6})/";
				$matches;
				preg_match($regex,$termObject['termlink'],$matches);
				
				$termObject['termcode'] = $matches[1];
				$termArray[] = $termObject;
			}			
			return $termArray;			
		}
		
		function parseSubjects($subjects)
		{
			$collegeObject;
			foreach($subjects->find('TD[width=300]') as $table)
			{
				$college['collegename'] = $table->find('FONT[COLOR=800000]',0)->plaintext;
				
				foreach($table->find('a') as $major)
				{
					$subject['subjectname'] = $major->plaintext;
					$subject['subjectlink']= "https://duapp3.drexel.edu/webtms_du/". $major->href;
					
					$regex = "/SubjCode=([A-Z]*)/";
					$matches;
					preg_match($regex,$subject['subjectlink'],$matches);
					$subject['subjectcode'] = $matches[1]; 
					$college['subjects'][] = $subject;
				}
				
				$regex = "/CollCode=([A-Z]*)/";
				$matches;
				preg_match($regex,$college['subjects'][0]['subjectlink'],$matches);
				$college['collegecode'] = $matches[1];
				
				$collegeObject[] = $college;
				unset($college);				
			}
			
			return $collegeObject;
		}
		
		function parseCourses($courses)
		{
			$table = $courses->find('table[BGCOLOR=#FFEAC1]',0);			
			
			$courses = $table->find('tr');
			
			$courseobject = array
							('subjectcode' => '',
							 'courseno' => '',							 
							 'sec' => '',
							 'crn' => '',
							 'coursetitle' => '',
							 'classlink' => ''							 		 
							);
			$coursecollection = array();
			
			for ($i=1;$i < count($courses); $i++)
			{		
				if(@$courses[$i]->find('td',0)->bgcolor == "#999999")		
					continue;
				if(@$courses[$i]->find('td',2)->bgcolor == "#AAAAAA")		
					continue;
				if(@$courses[$i]->find('td',0)->colspan == "2")		
				{
					$courseobject['subjectcode'] = '';		
					$courseobject['courseno'] = '';					
					$courseobject['sec'] = trim($courses[$i]->find('td',2)->plaintext);
					$courseobject['crn'] = trim($courses[$i]->find('td',3)->plaintext);
					$courseobject['classlink'] = "https://duapp3.drexel.edu/webtms_du/" . trim($courses[$i]->find('td',3)->find('p',0)->find('a',0)->href);
					$courseobject['coursetitle'] = trim($courses[$i]->find('td',4)->plaintext);
				}
				else
				{			
					$courseobject['subjectcode'] = trim($courses[$i]->find('td',0)->plaintext);		
					$courseobject['courseno'] = trim($courses[$i]->find('td',1)->plaintext);
					$courseobject['sec'] = trim($courses[$i]->find('td',3)->plaintext);
					$courseobject['crn'] = trim($courses[$i]->find('td',4)->plaintext);
					$courseobject['classlink'] = "https://duapp3.drexel.edu/webtms_du/" . trim($courses[$i]->find('td',4)->find('p',0)->find('a',0)->href);
					$courseobject['coursetitle'] = trim($courses[$i]->find('td',5)->plaintext);					
				}
				
				if (empty($courseobject['subjectcode']) || $courseobject['subjectcode'] == '&nbsp;')
					$courseobject['subjectcode'] = $bufferobject['subjectcode'];
				if (empty($courseobject['courseno']) || $courseobject['courseno'] == '&nbsp;')
					$courseobject['courseno'] = $bufferobject['courseno'];
				
				$bufferobject = $courseobject;
				
				$coursecollection[] = $courseobject;
				unset($courseobject);		
			}
			
			return $coursecollection;
					
		}
	}
	
?>