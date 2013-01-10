<?php
	class BetterTMS_DBManager
	{
		private $dbCred;		
		
		function __construct()
		{
			include_once('dbconfig.php');
			$dbObject = new dbconfig;
			$this->dbCred = $dbObject->getConnDetails();
		}
		
		function storeTerms($terms)
		{			
			mysql_connect($this->dbCred['host'],$this->dbCred['username'],$this->dbCred['password']);
			mysql_select_db($this->dbCred['database']);
			
			foreach ($terms as $term)
			{
				$query = sprintf("INSERT INTO `terms` (`termcode`,`termname`,`termlink`) VALUES ('%s','%s','%s')"
								, mysql_real_escape_string($term['termcode'])
								, mysql_real_escape_string($term['termname'])
								, mysql_real_escape_string($term['termlink']));
				
				mysql_query($query) or die(mysql_error());
			}			
			mysql_close();
		}
		
		function storeColleges($colleges)
		{
			mysql_connect($this->dbCred['host'],$this->dbCred['username'],$this->dbCred['password']);
			mysql_select_db($this->dbCred['database']);

			foreach($colleges as $college)
			{
				$query = sprintf("SELECT `collegecode` FROM `colleges` WHERE `collegecode` = '%s'"
								, mysql_real_escape_string($college['collegecode']));
				$existcheck = mysql_query($query);
				
				if (mysql_num_rows($existcheck) == 0)
				{
					$query = sprintf("INSERT INTO `colleges` (`collegecode`,`collegename`) VALUES ('%s','%s')"
									, mysql_real_escape_string($college['collegecode'])
									, mysql_real_escape_string($college['collegename']));
									
					mysql_query($query) or die(mysql_error());
				}			
			}
			
			mysql_close();
		}
		
		function storeSubjects($college)
		{
			mysql_connect($this->dbCred['host'],$this->dbCred['username'],$this->dbCred['password']);
			mysql_select_db($this->dbCred['database']);

			$collegecode = $college['collegecode'];
			
			foreach($college['subjects'] as $subject)
			{
				$query = sprintf("SELECT `subjectcode` from `subjects` WHERE `subjectcode` = '%s'"
								, mysql_real_escape_string($subject['subjectcode']));
				$existcheck = mysql_query($query);
				
				if (mysql_num_rows($existcheck) == 0)
				{
					$query = sprintf("INSERT INTO `subjects` (`subjectcode`,`subjectname`,`subjectlink`,`collegecode`) VALUES ('%s','%s','%s','%s')"
									, mysql_real_escape_string($subject['subjectcode'])
									, mysql_real_escape_string($subject['subjectname'])
									, mysql_real_escape_string($subject['subjectlink'])
									, mysql_real_escape_string($collegecode));
					
					mysql_query($query) or die(mysql_error());
				}
			}
			
			mysql_close();
		}
		
		function getSubLinks()
		{
			mysql_connect($this->dbCred['host'],$this->dbCred['username'],$this->dbCred['password']);
			mysql_select_db($this->dbCred['database']);
			
			$query = sprintf("SELECT `subjectlink` FROM `subjects`");
			$linksres = mysql_query($query) or die(mysql_error());
			$links;
			
			while ($link = mysql_fetch_array($linksres))
				$links[] = $link['subjectlink'];
					
			mysql_close();
			return $links;
		}
		
		//stores courses. Inserts into two tables: `courses` and `sections`
		function storeCourses($coursearray, $term)
		{
			mysql_connect($this->dbCred['host'],$this->dbCred['username'],$this->dbCred['password']);
			mysql_select_db($this->dbCred['database']);
			
			$query2 = "INSERT INTO `sections` (`crn`,`sec`,`courseid`,`termcode`) VALUES ";

			foreach($coursearray as $course)
			{
				$query = sprintf("SELECT `courseid` FROM `courses` WHERE `subjectcode` = '%s' AND `coursenumber` = %d"
								, mysql_real_escape_string($course['subjectcode'])
								, (int)$course['courseno']);
				$existcheck = mysql_query($query);
				
				$courseid = '';
				
				if (mysql_num_rows($existcheck) == 0)	//does not exist in `courses` table
				{
					$query1 = sprintf("INSERT INTO `courses` (`subjectcode`,`coursenumber`,`coursetitle`) VALUES ('%s',%d,'%s')"
										, mysql_real_escape_string($course['subjectcode'])
										, (int)$course['courseno']
										, mysql_real_escape_string($course['coursetitle']));
					mysql_query($query1) or die(mysql_error());
					
					$courseid = mysql_insert_id();					
				}
				else		//aready exists. Get existing record's id
				{
					$row = mysql_fetch_array($existcheck);
					$courseid = $row['courseid'];
				}
				
				$query2 .= sprintf("(%d,'%s',%d,'%s'),"
									, (int)$course['crn']
									, mysql_real_escape_string($course['sec'])
									, (int)$courseid
									, mysql_real_escape_string($term['termcode']));
				//mysql_query($query2) or die(mysql_error());
			}
			return $query2;
			
			mysql_close();
		}
	}
?>