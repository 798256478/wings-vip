<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\Health\RecordService;
use App\Http\Controllers\Api\Controller;

class RecordController extends Controller
{

    protected $recordService;
    public function __construct(RecordService $recordService)
    {
        $this->recordService = $recordService;
    }
    
    public function getRcords($barcode,$experimentid,$pageindex,$pagesize)
    {
       try{
            return $this->recordService->get_records($barcode,$experimentid,$pageindex,$pagesize);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public function get_by_id($id)
    {
       try{
            return $this->recordService->get_by_id($id);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public  function getReportData($experiment_data_id)
    {
       try{
            return $this->recordService->getParent($experiment_data_id);
            return  $data;
       }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public function getReportDetail($experiment_data_id,$projectId)
    {
       try{
            $recordService = new RecordService();
            $data = $recordService->get_by_projectid($experiment_data_id, $id);
		    return $data;
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    } 
    
}
