<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\Health\GeneService;
use App\Http\Controllers\Api\Controller;

class GeneController extends Controller
{

    protected $authService;
    protected $geneService;
    public function __construct(AuthService $authService, GeneService $geneService)
    {
        $this->authService = $authService;
        $this->geneService = $geneService;
    }
    
    public function getGenes($name,$pageindex,$pagesize)
    {
       try{
            $this->authService->singleRoleVerify('admin');
            return $this->geneService->get_genes($name,$pageindex,$pagesize);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public function get_site_by_geneId($geneId)
    {
       try{
            $this->authService->singleRoleVerify('admin');
            return $this->geneService->get_site_by_geneId($geneId);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    
    public function getGeneById($id)
    {
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->geneService->get_gene_by_id($id);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public function createGene(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $gene = $this->checkgene($request);
            return $this->geneService->create_gene($gene);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    
    
    public function updateGene(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $gene = $this->checkgene($request);
            $this->geneService->update_gene($gene);
            return $gene['id'];
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function saveSite(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $site = $this->checksite($request);
            return $this->geneService->save_site($site);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    private  function checkgene(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'default_is_positive' => ['required'],
            // 'default_effect' => ['required'],
        ];
        $gene = $request->all();
        $validator = app('validator')->make($gene, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('基因信息验证失败', $validator->errors());
        }
        return  $gene;
    }
    
    private  function checksite(Request $request)
    {
        $rules = [
            'code' => ['required'],
            'rs_code' => ['required'],
            'mutation' => ['required'],
            'SNPSite' => ['required'],
            // 'DNASingleType' => ['required'],
        ];
        $site = $request->all();
        $validator = app('validator')->make($site, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('基因信息验证失败', $validator->errors());
        }
        return  $site;
    }




}
