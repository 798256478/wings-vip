<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\Health\Barcode;
use App\Exceptions\WingException;


class QuestionnaireService
{

	function __construct()
	{
	}

	public function initQuestionnaire()
	{
		$questionnaire = new Questionnaire;
		$res=$questionnaire->first();
		if(!$res){
			throw new WingException("问卷未配置", 401);
		}
		$data=$res->sections;
		return $data[0];
	}

	public function getQuestionnaireByPage($page)
	{
		$questionnaire = new Questionnaire;
		$res=$questionnaire->first();
		$data=$res->sections;
		return $data[$page];
	}

	public function getQuestionnaire()
	{
		$questionnaire = new Questionnaire;
		return $questionnaire->first();
	}

	public function getAnswerById($codeId)
	{
		$questionnaireAnswer = new QuestionnaireAnswer;
		return $questionnaireAnswer->where('barcode_id', $codeId)->first();
	}

	public function addAnswer($data)
	{
		$barcode=Barcode::where('code',$data['code'])->first();
		$oneAnswer = $this->getAnswerById($barcode->id);
		$data['barcode_id']=$barcode->id;
		unset($data['code']);
		if ($oneAnswer) {
			foreach ($data as $key => $val) {
				$oneAnswer->$key = $val;
			}
			$res = $oneAnswer->save();
			if (!$res) {
				throw new WingException("保存失败", 401);
			}
		} else {
			$answer = new QuestionnaireAnswer();
			foreach ($data as $key => $val) {
				$answer->$key = $val;
			}
			$res = $answer->save();
			if (!$res) {
				throw new WingException("保存失败", 401);
			}
		}
	}

}