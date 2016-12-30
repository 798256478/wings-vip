<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AuthService;
use App\Services\Health\ConfigService;
use App\Http\Controllers\Api\Controller;
use App\Models\Health\Project;

class ConfigController extends Controller
{
    protected $authService;
    protected $configService;
    protected $experimentService;
    public function __construct(AuthService $authService, ConfigService $configService)
    {
        $this->authService = $authService;
        $this->configService = $configService;
    }

    public function getFile(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $file = $request->file('setting');
            $experiment= $request->input('experiment');
            if (!isset($experiment['name']) || $experiment['name'] == '') {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('实验名称为空', []);
            }
            if (!isset($experiment['type']) || $experiment['type'] == '') {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('实验类别为空', []);
            }
            $reader = Excel::load($file)->get()->toArray();
            $projectsList = $this->createData($reader);
            $this->configService->saveSetting($projectsList, $experiment);
            return 'success';
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function createData($reader)
    {
        $projectsList = [];
        $parent = '';
        $project ='';
        $gene ='';
        foreach ($reader as $value) {
            if (isset($value['parent'])) {
                $parent = trim($value['parent']);
                $projectsList[$parent]=array();
            }
            if(isset($value['project'])){
                    $project = trim($value['project']);
                    $projectsList[$parent][$project]= [
                        'name' => trim($value['project']),
                        'method' => '',
                        'projectRisk'=>json_decode($value['projectrisk']),
                        'genes'=>[]
                    ];
            }
            if(isset($value['gene'])){
                    $gene = trim($value['gene']);
                    $projectsList[$parent][$project]['genes'][$gene]=[
                        'name' => trim($value['gene']),
                        'default_is_positive' => 1,
                        'default_effect' => trim($value['effect']),
                        'sites'=> []
                    ];
            }
            $code=trim($value['code']);

            $projectsList[$parent][$project]['genes'][$gene]['sites'][$code]=[
                'code' => 'JT-'.trim($value['code']),
                'rs_code' => trim($value['rs_code']),
                'type' => isset($value['type'])?trim($value['type']):'snp',
                'mutation' => trim($value['mutation']),
                'SNPSite' => trim($value['snpsite']),
                'weight'=>[
                    trim($value['jieguo1']) =>
                    [ 
                        'score' => isset($value['risk1'])? trim($value['risk1']):null,
                        'tag' => isset($value['tag1'])? trim($value['tag1']):null,
                        'mean' => isset($value['mean1'])? trim($value['mean1']):null,
                    ],
                    trim($value['jieguo2']) =>
                    [ 
                       'score' => isset($value['risk2'])? trim($value['risk2']):null,
                       'tag' => isset($value['tag2'])? trim($value['tag2']):null,
                       'mean'=> isset($value['mean2'])? trim($value['mean2']):null,
                    ],
                    trim($value['jieguo3']) =>
                    [ 
                        'score' => isset($value['risk3'])? trim($value['risk3']):null,
                        'tag' => isset($value['tag3'])? trim($value['tag3']):null,
                        'mean' => isset($value['mean3'])? trim($value['mean3']):null,
                    ],
                ],
            ];
        }
        return $projectsList;
    }
    


}
