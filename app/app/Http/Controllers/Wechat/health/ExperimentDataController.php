<?php
/**
 * Created by PhpStorm.
 * User: shenzhaoke
 * Date: 2016/5/12
 * Time: 15:05
 */

namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Services\Health\ExperimentDataService;
use Illuminate\Http\Request;

class ExperimentDataController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$experimentDataService = new ExperimentDataService;
		$experiment_datas = $experimentDataService->getListByCardId($this->currentCard->id);
		return $this->theme_view('health.detections', ['experiment_datas' => $experiment_datas]);
	}

	public function getInfo($experiment_data_id)
	{
		$experimentDataService = new ExperimentDataService;
		$data['experiment_data'] = $experimentDataService->getbyId($experiment_data_id);
		return $this->theme_view('health/detection_info', $data);
	}
}