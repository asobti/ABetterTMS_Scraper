<?php
	/* class that holds DB config.
	
	Create a new object for this class without arguments to use the default datatabse, or call the constructor
	with an argument to use another database.
	Argument must be an array with the following keys declared: hostname, database, username, password.
	
	*/
	class dbconfig
	{
		private $host;
		private $database;
		private $username;
		private $password;		
		private $defaultConfig = array
								(
									"hostname" => "hostname",
									"database" => "database",
									"username" => "username",
									"password" => "password"
								);
				
		public function __construct($config = NULL)
		{
			if ($config != NULL)
			{
				$this->host = $config['hostname'];
				$this->database = $config['database'];
				$this->username = $config['username'];
				$this->password = $config['password'];
			}
			else
			{
				$this->host = $this->defaultConfig['hostname'];
				$this->database =$this->defaultConfig['database'];
				$this->username = $this->defaultConfig['username'];
				$this->password = $this->defaultConfig['password'];

			}
		}
		
		public function getConnDetails()
		{
			$connString['host'] = $this->host;
			$connString['username'] = $this->username;
			$connString['password'] = $this->password;
			$connString['database'] = $this->database;						
			return $connString;
		}		
	}
?>
