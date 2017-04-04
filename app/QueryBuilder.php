<?php

namespace app;

/**
 * QueryBuilder make sample from MongoDB
 */

use PHPSQLParser\PHPSQLParser;
use app\MongoQuery;
use app\QuerySorter;

Class QueryBuilder
{
	private $select = array(), $from = array(), $where = array(), $order = array(), $group = array(), $limit = array();
	private $mongoQuery = array();

	public function __construct($query)
	{
		$parser = new PHPSQLParser($query);
		$elements = $parser->parsed;

		$this->sortQuery($elements);

		if ($this->sortWhere())
		{
			$this->mongoQuery = $this->sortWhere();
		}
	}

	/**
     * This method repeat resort select array
     *
     * @param array $elements is elements Array
     */

	private function sortQuery($elements)
	{
		$this->select = QuerySorter::sortSelect($elements['SELECT']);
		$this->from = QuerySorter::sortSelect($elements['FROM']);
		$this->where = QuerySorter::sortWhere($elements['WHERE']);
		$this->group = QuerySorter::sortGroup($elements['GROUP']);
		$this->order = QuerySorter::sortOrder($elements['ORDER']);
		$this->limit = $elements['LIMIT'];
	}

	/**
     * This method repeat resort where array
     *
     * @return array $whereSort2
     */

	private function sortWhere()
	{
		$whereSort1 = QuerySorter::whereResort1($this->where);
		$whereSort2 = QuerySorter::whereResort2($whereSort1);

		return $whereSort2;
	}

	/**
     * This method create result array
     *
     * @param array $elements is result Mongo query
     * @return array $arrayResult
     */

	private function queryBuild($arrayQuery)
	{
		$arrayResult = array();

		if (!in_array('*', $this->select))
		{
			foreach ($arrayQuery as $row) 
			{
				$rowResult = array();

				foreach ($this->select as $column) 
				{
					$rowResult[$column] = $row[$column];
				}

				array_push($arrayResult, $rowResult);
			}
		}
		else
		{
			$arrayResult = $arrayQuery;
		}

		if (isset($this->group))
		{
			$countArrayResult = count($arrayResult);

			foreach ($this->group as $column) 
			{
				$columns = array();

				for ($i = 0; $i < $countArrayResult; $i++) 
				{ 
					if (in_array($arrayResult[$i][$column], $columns))
					{
						unset($arrayResult[$i]);
					}
					else
					{
						array_push($columns, $arrayResult[$i][$column]);
					}
				}
			}

			sort($arrayResult);
		}

		if (isset($this->order))
		{
			$countQueryResult = count($arrayResult);

			for ($i = 0; $i < $countQueryResult; $i++)
			{
				for ($j = $i + 1; $j < $countQueryResult; $j++)
				{
					switch (mb_strtolower($this->order['direction'])) 
					{
						case 'asd':
							if ($arrayResult[$i][$this->order['column']] > $arrayResult[$j][$this->order['column']])
							{
								$temp = $arrayResult[$j];
								$arrayResult[$j] = $arrayResult[$i];
								$arrayResult[$i] = $temp;
							}

							break;
						
						case 'desc':
							if ($arrayResult[$i][$this->order['column']] < $arrayResult[$j][$this->order['column']])
							{
								$temp = $arrayResult[$j];
								$arrayResult[$j] = $arrayResult[$i];
								$arrayResult[$i] = $temp;
							}
							
							break;
					}
				}         
			}
		}

		if (isset($this->limit))
		{
			if (!$this->limit['offset'])
			{
				array_splice($arrayResult, $this->limit['rowcount']);
			}
			else
			{
				$arrayResult = array_slice($arrayResult, $this->limit['offset'] - 1, $this->limit['rowcount']);
			}
		}

		return $arrayResult;
	}

	/**
     * This method return result of QL query
     *
     * @return array $result
     */

	public function getResult()
	{
		$arrayQuery = MongoQuery::find($this->from[0], $this->mongoQuery);

		$result = $this->queryBuild($arrayQuery);

		return $result;
	}
}