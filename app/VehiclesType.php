<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehiclesType extends Model
{
	///  ����, ����� ��� �������, ��������� � ������� API, ���� ������������
	///  � ���� ���������. 
    protected $table = 'api_vehicletypes';

	///  ��������� ����� ��� ���� ������ ������ �� �����. � ���� ����� ����.
	public $timestamps = false;
}
