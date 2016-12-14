<?php

namespace App\Http\Controllers\Api\Health;

use App\Services\Health\DetectionService;
use App\Services\QuestionnaireService;
use Dingo\Api\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Services\AuthService;
use App\Models\Health\Barcode;

class QuestionnaireController extends Controller
{
    protected $authService;
	protected $questionnaireService;

    public function __construct(AuthService $authService,QuestionnaireService $questionnaireService )
    {
        $this->authService = $authService;
        $this->questionnaireService = $questionnaireService;
    }

	public function initQuestionnaire()
	{
		try {
			return  $this->questionnaireService->initQuestionnaire();
		}catch (\Exception $e) {
			return json_exception_response($e);
		}
	}
	public function searchAnswerByCode($code)
	{
		try {
            $barcode = Barcode::where('code',$code)->first();
            if($barcode == null){
                throw new \Dingo\Api\Exception\StoreResourceFailedException("没有找到条码");
            }
			$answer=$this->questionnaireService->getAnswerById($barcode->id);
			return $answer;
		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}

	public function addAnswer(Request $request)
	{
		try{
			$data=$request->all();
			$questionnaireService=new QuestionnaireService();
			$questionnaireService->addAnswer($data);

		}catch(\Exception $e) {
			return json_exception_response($e);
		}
	}

	public function getQuestionnaireByPage($page)
	{
		return $this->questionnaireService->getQuestionnaireByPage($page);
	}

}
