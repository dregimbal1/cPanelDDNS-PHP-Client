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
 * @version 		0.0.2
 * @link				http://github.com/regimbal93
 *
 */

// Change to your timezone
// A list can be found @ http://php.net/manual/en/timezones.php
date_default_timezone_set('America/New_York');

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
		$this->cpanel->set_debug('0');
		
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
		$zones = $this->cpanel->api2_query($this->config['cpanel']['account'],'ZoneEdit', 'fetchzones')->data->zones->{$this->config['settings']['domain']};
		
		$this->logging('Removing record with IP: ' . $ip);
		
		// Go line e by line looking for our IP
		$i;
		foreach ( $zones as $line => $zone )
		{
			$i++;	
			if (strpos($zone, $ip) !== false) 
			{
				$this->logging('Found the record you wanted to remove at line ' . $i);
				$this->logging($zone);
				
				$remove = $this->cpanel->api2_query($this->config['cpanel']['account'],
				   'ZoneEdit', 'remove_zone_record',
					 array(
		        'domain' => $this->config['settings']['domain'],
		        'line' => $i
					  )
				);//remove it
				
			}//if $zone matches previous ip
			
		}//foreach $zone
		
	}//removeARecord function
	
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