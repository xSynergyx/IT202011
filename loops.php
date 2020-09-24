<?php
$numList = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

echo "Loop:<br />";
foreach($numList as $value)
	echo "$value<br />";

/*
Explanation:
I looped through the array one time to print all the values.

I then looped through the array again and checked each value
to see if it was divisible by 2 using the modulus operator
and printed them if they were even.
*/

echo "<br /><br />Even Nums:<br />";
foreach($numList as $value){
	if($value % 2 == 0)
		echo "$value<br />";
}
?>
