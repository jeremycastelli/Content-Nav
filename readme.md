Content Nav
====================

##What is Content Nav?
This a a wordpress plugin that create a navigation based on headings(H2-h5) of the content.


##Usage

```php

<?php 
	// specify the depth level of the nav
	$args = array(
		'limit'   => 3
	);
	echo jelli_get_content_nav($args); 
?>

```


##Changelog

###v1.0.1
- array of arguments as parameters