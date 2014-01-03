<?php
/**
 * TestBootstrap.php
 * 11/15/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 *
 * Sets up test environment for PHPUnit
 */

// Get our autoloader
$autoloader = require(__DIR__.'/../vendor/autoload.php');
// Add test namespace
$autoloader->add('UCI\\Tests\\TypeConverter', 'tests');