<?php
function formatPrice($price)
{
	if(!$price > 0) $price = 0;

	return number_format($price, 2, ",", ".");
	
}
?>