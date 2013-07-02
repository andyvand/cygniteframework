<?php
/**
 * Get class that inherits Global base class and implements SecureData interface
 *
 * PHP Version 5.1.6 or newer
 *
 * @category     : PHP
 *
 * @name         : Get
 *
 * @author    	 : Cygnite Dev Team
 *
 * @Copyright    : Copyright (c) 2013 - 2014,
 * @License      : http://www.appsntech.com/license.txt
 * @Link	     : http://appsntech.com
 * @Since	     : Version 1.0
 * @Filesource
 * @Warning      : Any changes in this library can cause abnormal behaviour of the framework
 *
 */
class Get extends Globals implements SecureData {  public $_var = "_GET";

	public function doValidation($key){}

}