<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
	///  ����, ����� ��� �������, ��������� � ������� API, ���� ������������
	///  � ���� ���������. 
    protected $table = 'api_vehicles';

	///  ��������� ����� ��� ���� ������ ������ �� �����. � ���� ����� ����.
	public $timestamps = false;
}
