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


		if ($request->hasFile($FileID))
		{
			//return "File is here";

			$DataFile = $R->file($FileID);
			$InputFileTempLocation = '/temp1.jpeg';

			///   �������� �������� ���� � ������� ��������� ������. 
			///   ������������ ����������� ����� "Storage". 
			$VirtualStorage = Storage::disk($StorageDisk);
			$VirtualStorage->put($InputFileTempLocation, fopen($DataFile, 'r+'));

			return "File saved correctly.";
		}
		else 
		{
			return "Error. File not found!";
		}
	}
}

