<?php

namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\DB;



	///                                                                                            
	///   ��������� �������� ������ ������, ����� �� ������������ ������, ������� 
	///   ������� ������� �������� ����������� � ������ ��������� ������ ������. 
	///____________________________________________________________________________________________
	///                                                                                            
	///   ������� ����� ��� ���� ���������� ������. 
	///                                                                                            
	class StructureSource
	{
		public const Name = 'api';
		public const DatabasePrefix = self::Name . '_';

		public const ID = 'ID';



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



