<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class VehiclesWaypoint extends Model
{
	///  ����, ����� ��� �������, ��������� � ������� API, ���� ������������
	///  � ���� ���������. 
    protected $table = 'api_vehicleswaypoints';

	///  ��������� ����� ��� ���� ������ ������ �� �����. � ���� ����� ����.
	public $timestamps = false;
}

