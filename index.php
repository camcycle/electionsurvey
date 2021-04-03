<?php

# Load the settings
require_once ('./.config.php');

# Load and run the elections system, passing in the settings
require_once ('./elections.php');
new elections ($settings);

?>
