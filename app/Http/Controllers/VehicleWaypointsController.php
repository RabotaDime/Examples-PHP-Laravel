<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class VehicleWaypointsController extends Controller
{
	public function index ()
	{
		return view('vehicles.routes.index');
	}



    public function import (Request $request)
	{
		$R = $request; //dd($R->all());

		///  ������������� ��������� ������������ �����. 
		$FileID = 'datafile';

		///  ������������� ������������ ���������. 
		$StorageDisk = 'local';

		///  ������������ ������ ������.
		$FileRecordMaximumLength = 1000;


/*
		///  ������������ ������ ����� CSV = 50 ��.
		$FileMaxSize = 50 * 1024 * 1024;

		$ValidateData = $R->validate([
			"$FileID" => "required|size:$FileMaxSize",
		]);
*/


		if ($R->hasFile($FileID))
		{
			$DataFile = $R->file($FileID);

			$RecordsCount = -1;

			if (($InputStream = fopen($DataFile, 'r+')) !== false)
			{
				$RecordsCount = 0;

				while (($FileRecord = fgetcsv($InputStream, $FileRecordMaximumLength, ";")) !== false)
				{
					$RecordsCount++;
				}
			}

/*
			///   �������� �������� ���� � ������� ��������� ������. 
			///   ������������ ����������� ����� "Storage". 
			$InputFileTempLocation = '/temp1.jpeg';
			$VirtualStorage = Storage::disk($StorageDisk);
			$VirtualStorage->put($InputFileTempLocation, fopen($DataFile, 'r+'));
			return "File saved correctly.";
*/
			return "File was imported correctly. Number of records = " . $RecordsCount;
		}
		else 
		{
			return "Error. File not found!";
		}
	}
}

