<?php
/**
 * @author jdia07
 * @copyright (c) 2015, Vainglory07 02.07
 * 
 */


if (! function_exists('base_url')) {
    /**
     * Gets the base url of the application.
     * With HTTP injection filter.
     * 
     * @return string
     */
    function baseurl() {
        // Force custom Base URL
        $protocol = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = trim(preg_replace('/[^a-z \d \. _]/i ', '', strip_tags($_SERVER['HTTP_HOST'])));
        $location = substr(str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']), 0, -1);
        $base_url = "{$protocol}://{$host}{$location}/";
        
        return $base_url;
    }
}

if (! function_exists('set_env')) {
    /*!
     * Detect and set global project environment
     * 
     * @param array  $setup    Environment setup
     *                         array(
     *                           'env_name' => string|array of hostname
     *                         )
     * @param string $constant Constant name. Defaults to `ENVI`
     */
    function set_env ($setup = array(), $constant = 'ENVI') {
        if (is_array($setup) and !empty($setup)) {
            $cons = !defined($constant) ? $constant : trigger_error("Constant already exists.");
            $host = gethostname();

            if ($host) {
                foreach ($setup as $env => $hosts) {
                    if ((is_string($hosts) and $hosts === $host) or (is_array($hosts) and in_array($host, $hosts))) {
                        return !defined($constant) ? define($constant, $env) : $cons;
                    }
                } return !defined($constant) ? define($constant, null) : $cons;
            } return trigger_error("Function is not supported in this server.");
        } return trigger_error("Setup data is empty or not an array.");
    }
}

if (! function_exists('io')) {
    /**
     * Outputs any number of passed values in <pre> tag
     * Accepts any datatype
     */
    function io() {
        $args = func_get_args();
        foreach ($args as $arg) {
            echo '<pre>';
                print_r($arg);
            echo '</pre>';
        }
    }
}



/* *******************************************************
 * String
 * 
 * *******************************************************/

if (! function_exists('cast')) {
    /**
     * Cast data to specific data type.
     * If data type is not provided, raw data is returned.
     * If array|object is converted to string, returned data is json_encoded 
     * instead.
     * 
     * @param data Any data value
     * @param str cast Data type [string|int|float|double|array|object|bool]
     * 
     * defaults the datatype to string if not defined
     * sensitive to urlencoded string. 
     * i don't know why (string) changes the original value of a string.
     * do not use this for strings which values are urlencoded.
     * 
     */
    function cast($data = 0, $cast = 'string') {
        $result = 0;
        $object = is_object($data) ? 1 : 0;
        switch ($cast) {
            case 'string':
            case 'str':
            default:
                $string = (is_array($data) or $object) ? json_encode($data) : (string) $data;
                $result = xss_clean($string);
                break;
            case 'integer':
            case 'int':
                $result = $object ? 0 : (int) $data;
                break;
            case 'float':
                $result = $object ? 0 : (float) $data;
                break;
            case 'double':
                $result = $object ? 0 : (double) $data;
                break;
            case 'array':
            case 'arr':
                $result = (array) $data;
                break;
            case 'object':
            case 'obj':
                $result = (object) $data;
                break;
            case 'bool':
                $result = $data ? true : false;
                break;
        }
        
        return $result;
    }
}

if (! function_exists('is_json')) {
    /**
     * Checks if valid json. 
     * Returns decoded json if valid.
     * 
     * @param  all  $json
     * @param  boolean $is_array json_decode to array option
     * 
     * @return boolean|array|object
     * 
     */
    function is_json($json, $is_array = false) {
        if ($json and is_string($json)) {
            if (is_object(json_decode($json, $is_array)) or is_array(json_decode($json, $is_array)))  { 
                return json_decode($json, $is_array);
            }
        } return false;
    }
}



/* *******************************************************
 * Number
 * 
 * *******************************************************/
if (! function_exists('minify_number')) {
    /**
     * Converts any given number into shorthand value.
     * 
     * @param integer|float $number
     * @param boolean $as_word Use word or letter for big numbers
     * 
     * @return string
     * 
     */
    function minify_number($number = 0, $as_word = false) {
        $float = number_format(cast($number, 'float'));
        $suffix = array(
            'Thousand' => 'K',
            'Million' => 'M',
            'Billion' => 'B',
            'Trillion' => 'T',
            'Quadrillion' => 'Quad',
            'Quintillion' => 'Quin'
        );
        
        $extract = explode(',', $float);
        $count = count($extract) - 2;
        $i = 0;
        
        if ($count >= 0 and $number > 0) {
            foreach ($suffix as $word => $shorthand) {
                if ($count === $i) {
                    $addfix = $as_word ? " $word" : $shorthand;
                    $get_dec = substr($extract[1], 0, 1);
                    $decimal = $get_dec ? ".{$get_dec}" : '';
           
                    return "{$extract[0]}{$decimal}" . $addfix;
                }
                $i++;
            }
        } return $number;
    }
}

if (! function_exists('get_probability')) {
    /**
     * Get random data based from given probability
     * 
     * @param array|object $weightedValues array({value} => {percentage of probability})
     * 
     * @return string|integer Key from $weightedValues
     * 
     */
    function get_probability($weightedValues = array()) {
    	// @param arr $weightedValues - {value} => {percentage of probability}
    	// @return arr $key;
    	$values = array();
        $weights = array();
        foreach ($weightedValues as $k => $v) {
            $values[] = $k;
            $weights[] = $v;
        }
        
        $count = count($values); 
        $i = 0; 
        $n = 0; 
        $num = mt_rand(0, array_sum($weights)); 
        while($i < $count){
            $n += $weights[$i]; 
            if($n >= $num){
                break; 
            }
            $i++; 
        } 
        return $values[$i];
    }
}



/* *******************************************************
 * Date and Time
 * 
 * *******************************************************/
if (! function_exists('list_timezone')) {
    /**
     * Returns an array|list of valid PHP supported timezones
     * 
     * @param  boolean $as_select_option Return result as <select> option
     * 
     * @return array|string
     * 
     */
    function list_timezone($as_select_option = false) {
    	// Get complete list of php supported gmt values
    	$timezone = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $result = array();

    	foreach($timezone as $zones) {
    		$now = new DateTime("now");
    		$now->setTimezone(new DateTimeZone($zones));

            if ($as_select_option) {
                $result[] = "<option value=\"{$zones}\"> [{$now->format('P')}] {$now->format('e')} </option>";
            } else {
                $result[$zones] = "({$now->format('P')})|{$now->format('e')}";
            }
    	}

    	return $as_select_option ? implode("\n", $result) : $result;
    }
}

if (! function_exists('date_convert')) {
    /**
     * Convert time from one timezone to another
     * 
     * @param string $date
     * @param string $timezone Defaults to UTC
     * @param string $format Defaults to Y-m-d H:i:s Timestamp Standard
     * 
     * @return string
     * 
     */
    function date_convert($date = '', $timezone = 'UTC', $format = 'Y-m-d H:i:s') {
        if ($date) {
            $valid_timezone = list_timezone();
            if (isset($valid_timezone[$timezone])) {
                $obj_date = new DateTime($date);
                $obj_date->setTimezone(new DateTimeZone($timezone));
                return $obj_date->format($format);
            } return defined('DATETIME_EMPTY') ? DATETIME_EMPTY : '0000-00-00 00:00:00';
        } return defined('DATETIME_EMPTY') ? DATETIME_EMPTY : '0000-00-00 00:00:00';
    }
}

if (! function_exists('timeline')) {
    /**
     * Timeline tracker.
     * 
     * @param string $datetime Datetime to track with.
     * 
     * @return boolean|string
     * 
     */
    function timeline($datetime = null) {
        if ($datetime and strtotime($datetime)) {
            $ref_dt = date(defined('TIMESTAMP_FORMAT') ? TIMESTAMP_FORMAT : 'Y-m-d H:i:s');
            $off_dt = strtotime($ref_dt) - strtotime($datetime);
            
            if ($off_dt > 0) {
                $stats = date_diff(new DateTime($datetime), new DateTime($ref_dt));

                $params = array(
                    'y' => 'year', 
                    'm' => 'month', 
                    'd' => 'day', 
                    'h' => 'hour', 
                    'i' => 'minute', 
                    's' => 'second'
                );
                
                $result = array();

                foreach ($params as $param => $legend) {
                    $value = $stats->$param;
                    if ($value > 0) {
                        $result[$param] = "{$value} {$legend}";
                        $result[$param] .= $value > 0 ? 's' : '';
                    }
                }

                $chunk = array_chunk($result, 2);
                $final = reset($chunk);
                return implode(', ', $final);
            } return 'Now';
        } return false;
    }
}
