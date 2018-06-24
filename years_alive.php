<?php main() ;

function main()
{
	// Incoming dataset is an array where values are comma delimited
	// and the first value is year of birth and the second is the year of death.
	$date_list = array( "1929,1999","1920,1942","1919,1958","1950,1990","1913,1956",
						"1934,1981","1921,1980","1945,2000","1906,1996","1903,1961",
						"1966,1973","1933,1996","1929,1995","1903,1971","1901,1941",
						"1929,1991","1919,1981","1909,1978","1910,1956","1988,1988",
						"1901,1992","1995,1998","1969,1977","1954,1999","1940,1958",
						"1936,1985","1951,1971","1964,1990","2000,2000","1908,1926",
						"1933,1981","1932,2000","1947,1978","1900,1995","1923,1997",
						"1912,1975","1926,1968","1943,1984","1900,2000","1900,1946",
						"1901,1998","1957,1969","1923,1923","1932,1935","1959,1977",
						"1900,1987","1942,1971","1922,1986","1902,1946","1949,1991") ;

	// Verify dataset integrity
	$msg = "" ;
	$ret = check_dataset($date_list, $msg) ;
	if ($ret===FALSE)
	{
		die("\n$msg\n") ;
	}

	// Call the function to process the years and get the result
	$ret = get_years_with_most_alive($date_list) ;
	
	// Print the results
	echo "The following years contain the most people alive:\n\n" ;
	printf("%-10s %-10s\n", "Year", "# people alive") ;
	printf("---------------------------------------------\n") ;
	foreach($ret as $key=>$value)
	{
		printf("%-10s %-10s\n", $key, $value) ;
	}
	exit(0) ;
}

// Split the birth years out of the comma delimited string
// and return them as an assoc. array
function get_years($string)
{
	$fields = explode(",", $string) ;
	$ret = array() ;
	$ret["birth"] = $fields[0] ;
	$ret["death"] = $fields[1] ;
	return $ret ;
}

// Verify integrity of the dataset.  Return TRUE if good
// Return FALSE if there is an issue with the date.  Place issue in $msg variable
function check_dataset($date_list, &$msg)
{
	foreach($date_list as $value)
	{
		if (!strstr($value, ","))
		{
			$msg = "Invalid data: One or more entries is not formatted correctly" ;
			return FALSE ;
		}
		$years = get_years($value) ;
		$birth = $years["birth"] ;
		$death = $years["death"] ;
		if ( ($birth < 1900) || ($birth > 2000) )
		{
			$msg = "Invalid data: One or more birth years out of scope" ;
			return FALSE ;
		}
		if ( ($death < 1900) || ($death > 2000) )
		{
			$msg = "Invalid data: One or more deatch years out of scope" ;
			return FALSE ;
		}
		if($birth > $death)
		{
			$msg = "Invalid data: One or more birth years occur after a respective death year" ;
			return FALSE ;
		}
	}
	return TRUE ;
}

// Take as an input the array of birth and death years
// Return an array with the years of people most alive 
// Where the key is the year and the value is the number of people alive
function get_years_with_most_alive($date_list)
{
	$track_years = array() ;	// Local place to increment years w/ living people
	
	// Walk the dataset
	foreach($date_list as $value)
	{
		$years = get_years($value) ;
		$birth = $years["birth"] ;
		$death = $years["death"] ;
		
		// Accumulate living years by incrementing for this entry
		for($i=$birth;$i<=$death;$i++)
		{
			if (!isset($track_years[$i])) // Init as we go to increase performance
			{
				$track_years[$i] = 0 ;
			}
			$track_years[$i]++ ;
		}
	}
	
	// Sort the array so the items w/ the highest value are on top
	arsort($track_years) ;

	// Get the first value which should be the highest
	$most_alive = reset($track_years) ;
	
	// Build the return w/ every record matching the highest value
	$ret = array() ;
	foreach($track_years as $key=>$value)
	{
		if ($value != $most_alive) 
		{
			break ; // No more years match, abort and skip the rest of the list
		}
		$ret[$key] = $value; // This year matches most alive. Add it
	}
	ksort($ret) ;	// Sort so the years are in order 
	return $ret ;
}

	
?>