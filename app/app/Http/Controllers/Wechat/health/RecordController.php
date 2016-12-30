<?php

namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Services\Health\CustomerService;
use App\Services\Health\RecordService;
use App\Services\Health\ExperimentDataService;
use App\Services\Health\ProgressService;
use App\Models\Health\ExperimentData;
use App\Models\Health\Project;
use Illuminate\Http\Request;

class RecordController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($experiment_data_id)
	{
        $recordService = new RecordService();
		$data = $recordService->getParent($experiment_data_id);
		return $this->theme_view('health/detection_report', $data);
	}

	public function reportDetail($experiment_data_id, $id)
	{
		$recordService = new RecordService();
		$data = $recordService->get_by_projectid($experiment_data_id, $id);
       
		return $this->theme_view('health/detection_report_detail', $data);
	}
    
    public function riskDatas($experiment_data_id)
    {
        $recordService = new RecordService();
		$data = $recordService->get_riskdatas($experiment_data_id);
		return $this->theme_view('health/riskdatas', $data);
    }
}