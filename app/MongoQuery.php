<?php

namespace app;

/**
 * MongoQuery make query in MongoDB
 */

use app\DBConnection;

Class MongoQuery
{
	/**
     * This method find rows in MongoDB
     *
     * @param string $collection is collection in Mongo
     * @param array $conditions find conditions
     * @return array $objects
     */

	public static function find($collection, $conditions)
	{
		$objects = array();

		$collection = new \MongoCollection(DBConnection::getConnection(), $collection);

		$query = $collection->find($conditions);

		foreach ($query as $row) 
		{
			array_push($objects, $row);
		}

		return $objects;
	}
}