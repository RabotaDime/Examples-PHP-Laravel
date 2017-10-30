<?php

namespace App\My\WaypointsAPI\ID
{
	class API
	{
		public const Name = 'api';
		public const DatabasePrefix = self::Name . '_';
	}

	class Source
	{
		public const ID = 'ID';
	}
}

	
	
namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\DB;



	class StructureSource
	{
		public static function Specify ($WhatToSpecify, $BasedOn)
		{
			return array_merge($BasedOn, $WhatToSpecify);
		}

		public static function ClearTable ($TableID)
		{
			return DB::table($TableID)->delete();
		}
	}
}



