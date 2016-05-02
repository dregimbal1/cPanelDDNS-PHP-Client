<?php
/**
 * Updates Type 'A' Record in your cPanel DNS settings for specified domain
 *
 * PHP version 5.3
 *
 * LICENSE: 
 *	
 * @package 		DDNSUpdater
 * @author 			David Regimbal <david@regimbal.me>
 * @copyright 	2016 David Regimbal
 * @version 		0.0.1
 * @link				http://github.com/regimbal93
 *
 */

class DDNSUpdater
{
	private $lastSubmittedIP = "";
	private $database = "Core/Services/LocalStorage/settings.conf";
	
	public function __construct()
	{
		// Configuration file
		include 'config.php';
		$this->config = $config;
		$this->debug = $debug;
		
		// cPanel Service [xmlapi-php]
		require_once 'api.php';

		$this->cpanel = new xmlapi($config['cpanel']['host']);
		$this->cpanel->password_auth($config['cpanel']['user'],$config['cpanel']['password']);
		$this->cpanel->set_port($config['cpanel']['port']);
		
		// LocalStorage Service
		$this->lastSubmittedIP = trim(file_get_contents($this->database)); 


	}
	
	private function logging($message)
	{
		// in the root directory of the project store log details
		if($this->debug){
			$file = fopen("log.txt","a+");
			return fwrite($file,"\n" . "[".date("Y-m-d h:i:sa")."] " . $message);			
		}
	}
	
	private function removeARecord($ip)
	{
		// First get our zones
		$zones = $this->cpanel->api2_query($this->config['cpanel']['account'],'ZoneEdit', 'fetchzones')->data->zones->{'regimbal.me'};

		$this->logging($zones);
		
		// Go line by line looking for our IP
		foreach ( $zones as $line => $zone )
		{
			if (strpos($zone, $ip) !== false) 
			{
				// Remove it
				$remove = $this->cpanel->api2_query(
				   'ZoneEdit', 'remove_zone_record',
					 array(
		        'domain' => $this->config['settings']['domain'],
		        'line' => $line,
					  )
				);
				
				$this->logging($remove);
				
			}
		}
		
	}
	
	private function addARecord()
	{
		// This gets your machines Public IP address
		$ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
		
		// Add the IP to your zone file
		$zone = $this->cpanel->api2_query($this->config['cpanel']['account'],
		   'ZoneEdit', 'add_zone_record',
			 array(
	      'domain' => $this->config['settings']['domain'],
	      'name' => $this->config['settings']['name'],
	      'type' => $this->config['settings']['type'],
	      'address' => $ip,
	      'ttl' => $this->config['settings']['ttl'],
	      'class' => 'IN',
			  )
		);
		
		$this->logging($zone);
		
		$this->lastSubmittedIP = $ip;
		
		// LocalStorage Service		
		file_put_contents($this->database, $ip);
		
		
	}
	
	public function update()
	{
		// This gets your machines Public IP address
		$ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
		
		if($this->lastSubmittedIP !== $ip)
		{
			
			$this->logging('IP change detected to ' . $ip . ' from ' . $this->lastSubmittedIP);
			
			$this->removeARecord($this->lastSubmittedIP);
			$this->addARecord();
			
		}
		else
		{
			$this->logging('Your IP has not changed. No further action required.');
		}
		
	}
	
} 
?>