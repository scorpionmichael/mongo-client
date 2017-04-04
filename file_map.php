<?php

$namespaces = array();

scanDirerctory('app/');
scanDirerctory('PHPSQLParser/');

function scanDirerctory($dir, $parentNS = '')
{
	global $namespaces;

	$scandir = scandir($dir);

	for ($i = 0; $i < count($scandir); $i++)
	{
		if ($scandir[$i] != '.' && $scandir[$i] != '..')
		{
			if (!is_dir($dir . $scandir[$i]))
			{
				$file = $dir . $scandir[$i];

				$namespace = $parentNS;
				$namespace .= str_replace('/', '\\', $file);
				$namespace = str_replace('.php', '', $namespace);

				$namespaces[$namespace] = $file;
			}
			else
			{
				scanDirerctory($dir . $scandir[$i] . '/', $parentNS);
			}
		}
	}
}

return $namespaces;