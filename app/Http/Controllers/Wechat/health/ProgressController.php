<?php

namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Models\Health\ExperimentData;
use App\Services\Health\ProgressService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function progress(Request $request)
	{
		$experiment_data_id = $request->route('experiment_data_id');
		$progressService = new ProgressService();
		$progress= $progressService->getOneProgress($experiment_data_id);
		$allProgress=$progressService->getAllProgress();		
		if(!$progress){
			return redirect('wechat/health/detection/new');
		}
		$progress_id=array();
		$progresses=array();
		foreach ($progress as $k => $v) {
			$progress_id[$k]=$progress[$k]->progress_id;
			$progresses[$k]=$progress[$k]->data;
			$progresses[$k]['created_at']=$progress[$k]->created_at;
		}
		foreach ($allProgress as $key => $value) {
			if(in_array($allProgress[$key]->id,$progress_id))
			{
				unset($allProgress[$key]);
			}
		}
		$data['progress']=$progresses;
		$data['noProgress']=$allProgress;
		$data['experiment_data_id']=$experiment_data_id;
		return $this->theme_view("health/detection_progress", $data);
	}
}