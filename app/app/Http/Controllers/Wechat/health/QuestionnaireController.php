<?php

namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Models\QuestionnaireAnswer;
use App\Services\Health\DetectionService;
use App\Services\QuestionnaireService;
use App\Models\Health\Barcode;
use App\Models\Health\ExperimentData;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function saveQuestionnaireAnswer(Request $request)
	{
		$data['input'] = $request->all();
		$questionnaireService = new QuestionnaireService;
		$questionnaire=$questionnaireService->getQuestionnaireByPage($data['input']['nowSection']);
		foreach($questionnaire['questions'] as $key=>$val){
			if(!isset($data['input'][$val['position']])){
				$data['input'][$val['position']]='';
			}
		}
		$data['questionnaireAnswer'] = $questionnaireService->getAnswerById($request->route('barcode_id'));
		unset($data['input']['nowSection']);
		foreach ($data['input'] as $key => $val) {
             $data['questionnaireAnswer']->$key = $val;
		}
		if(isset($data['input']['is_complete']) && $data['input']['is_complete'] == 1) {
			$data['questionnaireAnswer']->time=time();
		}
		$res = $data['questionnaireAnswer']->save();
		return response()->json($res);
	}

	public function showQuestionnaire($barcode_id,$experiment_data_id){
		$barcode=Barcode::find($barcode_id);
		$questionnaireService = new QuestionnaireService;
		$data['questionnaireAnswer'] = $questionnaireService->getAnswerById($barcode->id);
		$data['questionnaire'] = $questionnaireService->getQuestionnaire();
        $data['barcode_id']=$barcode->id;
        $data['experiment_data_id'] = $experiment_data_id;
		if (!$data['questionnaireAnswer']) {
			$questionnaireAnswer = new QuestionnaireAnswer;
			$questionnaireAnswer->barcode_id=$barcode->id;
			$questionnaireAnswer->save();
			$data['questionnaireAnswer'] = $questionnaireService->getAnswerById($barcode->id);
		}
		if (isset($data['questionnaireAnswer']->time) && $data['questionnaireAnswer']->time !='') {
			return $this->theme_view('questionnaire_answer', $data);
		}
		$test = $data['questionnaire']->sections;
		foreach ($test as &$section) {
			foreach ($section['questions'] as &$topic) {
				if ($topic['type'] != 'text') {
					$topic['options'] = $this->sortArray($topic['options']);
				}
			}
		}
		$data['questionnaire']->sections = $test;
		return $this->theme_view('questionnaire', $data);
	}


	protected function sortArray($arr)
	{
		$arr1 = array_values($arr);
		usort($arr1, function ($a, $b) {
			if (strlen($a) == strlen($b)) {
					return 0;
				}
			return (strlen($a) < strlen($b)) ? -1 : 1;
		});
		$arr = array_flip($arr);
		$arr1 = array_flip($arr1);
		$res = array_merge($arr1, $arr);
		$res = array_flip($res);
		return $res;
	}


//	public function showQuestionnaireAnswer(Request $request)
//	{
//		$detectionService = new DetectionService;
//		$data['detection'] = $detectionService->getDetectionById($request->route('id'));
//		$questionnaireService = new QuestionnaireService;
//		$data['questionnaire'] = $questionnaireService->getQuestionnaire();
//		$data['answer'] = $questionnaireService->getAnswerById($data['detection']->answer_id);
////		dd($data['answer']);
//		return $this->theme_view('questionnaire_answer', $data);
//	}

}