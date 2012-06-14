<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */


return array(

	/**
	 * Whether to append the assets last modified timestamp to the url.
	 * This will aid in asset caching, and is recommended.  It will create
	 * tags like this:
	 *
	 *     <link type="text/css" rel="stylesheet" src="/assets/css/styles.css?1303443763" />
	 */
	'add_mtime' => false,

);
