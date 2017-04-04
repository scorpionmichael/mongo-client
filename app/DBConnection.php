<?php

namespace app;

/**
 * DBConnection is abstract Class, it return db connection object

 * @property object $dbConnection
 */

abstract Class DBConnection
{
	private static $dbConnection;

	private function __construct() {
	}

	/**
     * This method create connect to MongoDB
     *
     * @return object self::$dbConnection
     */

	public static function getConnection()
	{
		if (!self::$dbConnection)
		{
			$mongoClient = new \MongoClient();
			self::$dbConnection = $mongoClient->selectDB('main');
		}

		return self::$dbConnection;
	}
}