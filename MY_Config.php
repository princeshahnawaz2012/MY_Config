<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//---------------------------------------------------------------
// MY_Config.php
//
// This is an extension of the default Config library that ships 
// with CodeIgniter 1.7.2 to allow saving and loading of settings 
// from files..
//
// by Lonnie Ezell (http://lonnieezell.com)
//
//---------------------------------------------------------------

class MY_Config extends CI_Config {
	
	//---------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
	}
	
	//---------------------------------------------------------------
	
	/**
	 *	GET()
	 *
	 * Returns an array of configuration settings from a single 
	 * config file. 
	 */
	public function get($file, $fail_gracefully=TRUE) 
	{
		$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
		
		if ( ! file_exists(APPPATH.'config/'.$file.EXT))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.EXT.' does not exist.');
		}
		
		include(APPPATH.'config/'.$file.EXT);

		if ( ! isset($config) OR ! is_array($config))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
		}
		
		return $config;
	}
	
	//---------------------------------------------------------------
	
	/**
	 *	SAVE()
	 *
	 *	Saves the passed array settings into a single config file located
	 * 	in the /config directory. 
	 */
	public function save($file='', $settings=null) 
	{
		if (empty($file) || !is_array($settings	))
		{
			return false;
		}
		
		if (!file_exists(APPPATH.'config/'.$file.EXT))
		{
			return false;
		}
		
		// Load the file so we can loop through the lines
		$contents = file_get_contents(APPPATH.'config/'.$file.EXT);
		
		// Clean up post
		if (isset($settings['submit'])) unset($settings['submit']);
		
		foreach ($settings as $name => $val)
		{
			// Is the config setting in the file? 
			$start = strpos($contents, '$config[\''.$name.'\']');
			$end = strpos($contents, ';', $start);
			
			$search = substr($contents, $start, $end-$start+1);
			
			if (is_array($val))
			{
				$tval  = 'array(\'';
				$tval .= implode("','", $val);
				$tval .= '\')';
			
				$val = $tval;
				unset($tval);
			} else 
			{
				$val ="'$val'";
			}
			
			$contents = str_replace($search, '$config[\''.$name.'\'] = '. $val .';', $contents);
		}
		
		// Backup the file for safety
		$source = APPPATH . 'config/'.$file.EXT;
		$dest = APPPATH . 'config/'.$file.EXT.'.bak';
		copy($source, $dest);
		
		// Make sure the file still has the php opening header in it...
		if (strpos($contents, '<?php') === FALSE)
		{
			$contents = '<?php' . "\n" . $contents;
		}
		
		// Write the changes out...
		$result = file_put_contents(APPPATH.'config/'.$file.EXT, $contents, LOCK_EX);
		
		if ($result === FALSE)
		{
			return false;
		} else {
			return true;
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Retrieves the config/database.php file settings.
	 */  
	public function get_db() 
	{
		$file = 'database';
		
		if ( ! file_exists(APPPATH.'config/'.$file.EXT))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.EXT.' does not exist.');
		}
		
		include(APPPATH.'config/'.$file.EXT);

		if ( ! isset($db) OR ! is_array($db))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
		}
		
		return $db;
	}
	
	//---------------------------------------------------------------
	
	/**
	 *	Saves the settings to the config/database.php file.
	 */
	public function save_db($settings=null) 
	{
		if (!is_array($settings	))
		{
			return false;
		}
		
		// Clean up post
		if (isset($_POST['submit'])) unset($_POST['submit']);
		
		// Load the file so we can loop through the lines
		$contents = file_get_contents(APPPATH.'config/'.'database'.EXT);
		
		foreach ($settings as $group => $values)
		{
			if ($group != 'submit')
			{
				foreach ($values as $name => $value)
				{
					// Convert on/off to TRUE/FALSE values
					$value = strtolower($value);
					if ($value == 'on' || $value == 'yes' || $value == 'true') $value = 'TRUE';
					if ($value == 'on' || $value == 'no' || $value == 'false') $value = 'FALSE';
				
					if ($value != 'TRUE' && $value != 'FALSE')
					{
						$value = "'$value'";
					}
				
					// Is the config setting in the file? 
					$start = strpos($contents, '$db[\''.$group.'\'][\''. $name .'\']');
					$end = strpos($contents, ';', $start);
					
					$search = substr($contents, $start, $end-$start+1);
					
					$contents = str_replace($search, '$db[\''.$group.'\'][\''. $name .'\'] = '. $value .';', $contents);
				}
			}
		}
		
		// Backup the file for safety
		$source = APPPATH . 'config/database'.EXT;
		$dest = APPPATH . 'config/database'.EXT.'.bak';
		copy($source, $dest);
		
		// Make sure the file still has the php opening header in it...
		if (!strpos($contents, '<?php') === FALSE)
		{
			$contents = '<?php' . "\n" . $contents;
		}
		
		// Write the changes out...
		$result = file_put_contents(APPPATH.'config/'.'database'.EXT, $contents, LOCK_EX);
		//$result = false;
		
		if ($result === FALSE)
		{
			return false;
		} else {
			return true;
		}
	}
	
	//---------------------------------------------------------------
}

// END Library class

/* End of file MY_Config.php */
/* Location: ./application/libraries/MY_Config.php */