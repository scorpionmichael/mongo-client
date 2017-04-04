<?php

namespace app;

/**
 * QueryParser sort query array
 */

Class QuerySorter
{
	private function __construct()
	{

	}	

	/**
     * This method resort select array
     *
     * @param array $elements is select Array
     * @return array $select
     */

	public static function sortSelect($elements)
	{
		$select = array();

		foreach ($elements as $part) 
		{
			array_push($select, $part['base_expr']);
		}

		return $select;
	}

	/**
     * This method resort from array
     *
     * @param array $elements is from Array
     * @return array $from
     */

	public static function sortFrom($elements)
	{
		$from = array();

		foreach ($elements as $part) 
		{
			array_push($from, $part['table']);
		}

		return $from;
	}

	/**
     * This method resort where array
     *
     * @param array $elements is where Array
     * @return array $where
     */

	public static function sortWhere($elements)
	{
		$where = array();

		if (isset($elements))
		{
			foreach ($elements as $part) 
			{
				if ($part['expr_type'] == 'colref')
				{
					$column = $part['base_expr'];
					$where[$column] = array();
				}
				else if((mb_strtolower($part['base_expr']) == 'and' || mb_strtolower($part['base_expr']) == 'or') && $part['expr_type'] == 'operator')
				{
					array_push($where, $part['base_expr']);
				}
				else
				{
					array_push($where[$column], trim($part['base_expr'], "'"));
				}
			}
		}

		return $where;
	}

	/**
     * This method resort group array
     *
     * @param array $elements is group Array
     * @return array $group
     */

	public static function sortGroup($elements)
	{
		$group = array();

		if (isset($elements))
		{
			foreach ($elements as $part) 
			{
				array_push($group, $part['base_expr']);
			}
		}

		return $group;
	}

	/**
     * This method resort order array
     *
     * @param array $elements is order Array
     * @return array $order
     */

	public static function sortOrder($elements)
	{
		$order = array('column' => $elements['ORDER'][0]['base_expr'], 'direction' => $elements['ORDER'][0]['direction']);

		return $order;
	}

	/**
     * This method repeat resort select array
     *
     * @param array $where is where Array
     * @return array $whereSort
     */

	public static function whereResort1($where)
	{
		$whereSort = array();

		foreach ($where as $key => $value) 
		{
			if (is_array($value))
			{
				$count = count($value);

				for ($i = 0; $i < $count; $i++)
				{
					if ($i % 2 == 0)
					{
						switch ($value[$i]) 
						{
							case '=':
								array_push($whereSort, array($key => $value[$i + 1]));

								break;

							case '>':
								array_push($whereSort, array($key => array('$gt' => (float) $value[$i + 1])));

								break;

							case '>=':
								array_push($whereSort, array($key => array('$gte' => (float) $value[$i + 1])));

								break;

							case '<':
								array_push($whereSort, array($key => array('$le' => (float) $value[$i + 1])));

								break;

							case '<=':
								array_push($whereSort, array($key => array('$lte' => (float) $value[$i + 1])));

								break;

							case '!=':
								array_push($whereSort, array($key => array('$ne' => (float) $value[$i + 1])));

								break;
						}
					}
				}
			}
			else
			{
				array_push($whereSort, $value);
			}
		}

		return $whereSort;
	}

	/**
     * This method repeat resort select array
     *
     * @param array $where is where Array
     * @return array $whereSort
     */

	public static function whereResort2($where)
	{
		$count = count($where);

		if ($count > 1)
		{
			for ($i = 0; $i < $count; $i++)
			{
				if (!is_array($where[$i]))
				{
					switch (mb_strtolower($where[$i]))
					{
						case 'and':
							if (!$whereSort)
							{
								$where[$i + 1] = array('$and' => array($where[$i - 1], $where[$i + 1]));

								$whereSort = $where[$i + 1];
							}
							else
							{
								$whereSort = array('$and' => array($whereSort, $where[$i + 1]));
							}

							break;

						case 'or':		
							if (!$whereSort)
							{
								$where[$i + 1] = array('$or' => array($where[$i - 1], $where[$i + 1]));

								$whereSort = $where[$i + 1];
							}
							else
							{
								$whereSort = array('$or' => array($whereSort, $where[$i + 1]));
							}

							break;
					}
				}
			}
		}
		else
		{
			$whereSort = $whereSort1[0];
		}

		return $whereSort;
	}
}