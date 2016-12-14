
/**
 * Customer Controller
 */
'use strict';

angular.module('app')
.controller('ExperimentController', ['$scope', '$location', 'AuthService','ExperimentService','GeneService',
    function($scope, $location, AuthService,ExperimentService,GeneService) {
        if (AuthService.current_user == null) {
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('实验管理');
        $scope.form = {};
        $scope.experiments=[];
        $scope.sitepages={pageindex: 1,pagesize:10,totla:0,code:''};
        $scope.type="hand";
        $scope.init=function()
        {
            $scope.current_experiment={id:0,name:'',sampleType:'',type:'score'};
            $scope.current_project={id:0,experiment_id:0,name:'',sitecount:0,order:0,method:'',parent:0,parent_name:''};
            $scope.genes=[];
            $scope.project_genes=[];
            $scope.focus='experiment';
        }
        $scope.init();
       
       
        $scope.get_experiments=function() {
            ExperimentService.get_experiments().then(function(res) {
                $scope.experiments=res;
            }, function(res) {
                alert(res.message);
            });
        }
        
        $scope.get_genes=function() {
            GeneService.get_genes($scope.sitepages.code, $scope.sitepages.pageindex, $scope.sitepages.pagesize).then(function(res) {
                $scope.sitepages.total = res.count;
                $scope.genes=res.genes;
            }, function(res) {
                alert(res.message);
            });
        }
        
        
        $scope.get_sites_by_projectId=function(){
            ExperimentService.get_sites_by_projectId($scope.current_project.id).then(function(res) {
                $scope.project_genes=res.project_genes;
            }, function(res) {
                alert(res.message);
            });
        }
        $scope.get_experiments();
        $scope.experiment_module_add=function(){
            $scope.init();
            $scope.form.experiment.$setPristine();
            $scope.type="hand";
        }
        
         $scope.experiment_module_export=function(){
              $scope.type="export";
         }
        
         $scope.experiment_module_edit=function(experiment) {
            $scope.current_experiment=experiment;
            $scope.focus='experiment';
            $scope.type="hand";
        }
        
        $scope.experiment_save=function(type){
            ExperimentService.save_experiment($scope.current_experiment).then(function(res) {
                $scope.current_experiment.id=res;
                $scope.experiments.push($scope.current_experiment);
                if(type==0){
                    $scope.init();
                    $scope.form.experiment.$setPristine();
                }
                else{
                     $scope.focus='project';
                }
               
            }, function(res) {
                alert(res.message);
            });
        }
        
        $scope.project_module_edit=function(project,parent_name){
            $scope.current_project=project;
            $scope.current_project.parent_name=parent_name;
            $scope.focus='project';
        }
        
        $scope.project_module_add=function(parent,parent_name){
             $scope.current_project={id:0,name:'',sitecount:0,order:0,method:'',parent:parent,parent_name:parent_name};
             $scope.focus='project';
             $scope.form.project.$setPristine();
        }
        
        $scope.project_save=function(type){
            $scope.current_project.experiment_id = $scope.current_experiment.id;
            if(typeof($scope.current_experiment.projects)== "undefined")
            {
                 $scope.current_experiment.projects=[];
            }
            ExperimentService.save_project($scope.current_project).then(function(res) {
                if($scope.current_project.parent==0||$scope.current_project.parent==null){
                    if($scope.current_project.id==0){
                        $scope.current_project.id=res;
                        $scope.current_project.subs=[];
                        $scope.current_experiment.projects.push($scope.current_project);  
                    }
                }
                else{
                      if($scope.current_project.id==0){
                           $scope.current_project.id=res;
                           var index = _.findIndex($scope.current_experiment.projects, {'id':$scope.current_project.parent});
                           $scope.current_experiment.projects[index].subs.push($scope.current_project);  
                      }
                }
                if(type==0){
                    $scope.current_project={
                        id:0,
                        experiment_id: $scope.current_experiment.id,
                        name:'',
                        sitecount:0,
                        order:0,
                        method:'',
                        parent: $scope.current_project.parent,
                        parent_name: $scope.current_project.parent_name,
                    };
                    $scope.focus='project';
                    $scope.form.project.$setPristine();
                }
                else{
                    $scope.focus='site';
                }
              
            }, function(res) {
                alert(res.message);
            });
        }
        
        $scope.site_module_add=function(project,parent_name){
             $scope.focus='site';
             $scope.current_project=project;
             $scope.current_project.parent_name=parent_name;
             $scope.get_sites_by_projectId();
        }
        
        $scope.gene_add=function(gene){
            var geneIndex = _.findIndex($scope.project_genes, {'gene.name': gene.name});
            var project_gene={};
            if(geneIndex < 0){
                project_gene.project_id=$scope.current_project.id;
                project_gene.gene_id=gene.id;
                project_gene.effect=gene.effect;
                project_gene.gene=gene;
                project_gene.project_sites=[];
                $scope.project_genes.push(project_gene);
            }
            else
            {
                project_gene=$scope.project_genes[geneIndex];
            }
            
            $.each(gene.sites, function(index, site){
                var siteIndex = _.findIndex(project_gene.project_sites, {'code': site.code});
                if(siteIndex < 0){
                    var weight={};
                    weight[site.SNPSite[0]+site.SNPSite[0]]={'score':null,'tag':'','mean':''};
                    weight[site.SNPSite[0]+site.SNPSite[2]]={'score':null,'tag':'','mean':''};
                    weight[site.SNPSite[2]+site.SNPSite[2]]={'score':null,'tag':'','mean':''};
                    var project_site={
                        id:0,
                        project_id:$scope.current_project.id,
                        code:site.code,
                        weight:weight,
                        isPositive:gene.default_is_positive,
                        mutation:site.mutation,
                        isdelete:true
                    };
                    project_gene.project_sites.push(project_site);
                }
            });
        }
        
        $scope.sites_remove=function(gene_name,site_code){
             $.each($scope.project_genes, function(index, project_gene){
                if(project_gene.gene.name==gene_name){
                    var siteIndex = _.findIndex(project_gene.project_sites, {'code': site_code});
                    project_gene.project_sites.splice(siteIndex,1);
                    if( project_gene.project_sites.length==0){
                        $scope.project_genes.splice(index,1);
                    }
                }
            });
        }
        
      
        
        $scope.sites_save=function(){
             ExperimentService.save_sites($scope.project_genes).then(function(res) {
                var count=0;
                $.each($scope.project_genes, function(index, project_gene){
                    count+=project_gene.project_sites.length;
                })
                $scope.current_project.sitecount=count;
                $scope.focus='project';
                alert('保存成功');
            }, function(res) {
                alert(res.message);
            });
        }
        
        $scope.site_weight_module_add=function(project_site){
            $scope.current_project_site=project_site;
        }
        $scope.site_weight_save=function(){
            $.each($scope.project_genes, function(index, project_gene){
                var siteIndex = _.findIndex(project_gene.project_sites, {'code': $scope.current_project_site.code});
                if(siteIndex >= 0)
                {
                    project_gene.project_sites[siteIndex].weight = $scope.current_project_site.weight;
                    return;
                }
            });
            $scope.current_project_site = null;
           
        }
        $scope.$watchCollection('sitepages.pageindex', function(newVal, oldVal) {
            if(newVal==oldVal) return;
            if(newVal <= 0)  return;
            $scope.get_genes();
        });
        
        $scope.risk_module_add=function(project){
              $scope.current_project = project;
              $scope.focus = 'risk';
              $scope.risks=['一般风险','中度风险','高度风险'];
              $scope.circumrisks=['差','较差'];
              ExperimentService.get_risk_by_projectId($scope.current_project.id).then(function(res) {
                  if(res.length==0){
                       $scope.project_risks=[];
                       $scope.project_circumrisks=[];
                  }
                  else{
                       $scope.project_risks=res.project_risks;
                       $scope.project_circumrisks=res.project_circumrisks;
                  }
                   
              }, function(res) {
                    alert(res.message);
              });
              $scope.isSync = false;
        }
        
      
        
        $scope.updateRisk=function($event,risk){
           var index=  _.findIndex( $scope.project_risks, {'tag': risk});
           if(index < 0 && $event.target.checked){
                var item={id:0,tag:risk,min:0,max:0,instructions:'',level:1,character:'',project_id:$scope.current_project.id};
                $scope.project_risks.push(item);
           }
           if(index >=0 && (!$event.target.checked)){
                 $scope.project_risks.splice(index,1);
           }
        }
        
        $scope.updateCircuRisk=function($event,risk){
           var index=  _.findIndex( $scope.project_circumrisks, {'tag': risk});
           if(index < 0 && $event.target.checked){
                var item={id:0,tag:risk,min:0,max:0,instructions:'',level:1,project_id:$scope.current_project.id};
                $scope.project_circumrisks.push(item);
           }
           if(index >=0 && (!$event.target.checked)){
                 $scope.project_circumrisks.splice(index,1);
           }
        }
        
        
        
        $scope.addRisk=function(){
            var item={id:0,tag:'',min:0,max:0,character:'',instructions:'',project_id:$scope.current_project.id};
            $scope.project_risks.push(item);
        }
        
         $scope.addCircumRisk=function(){
            var item={id:0,tag:'',min:0,max:0,instructions:'',project_id:$scope.current_project.id};
            $scope.project_circumrisks.push(item);
        }
        
        $scope.isSelected=function(risk){
             var index=  _.findIndex( $scope.project_risks, {'tag': risk});
             return index >= 0;
        }
        
        $scope.isSelectedCircumRisk=function(risk){
             var index=  _.findIndex( $scope.project_circumrisks, {'tag': risk});
             return index >= 0;
        }
        
        $scope.risk_save=function(){
            if($scope.project_risks.length > 0){
                 ExperimentService.save_risk($scope.project_risks).then(function(res) {
                    $scope.focus = 'project';
                }, function(res) {
                    alert(res.message);
                });
            }
            else{
                alert('必须添加风险程度');
            }
        }
        
        $scope.circumrisk_save=function(){
            if($scope.project_circumrisks.length > 0){
                 ExperimentService.save_circumRisk($scope.project_circumrisks).then(function(res) {
                    $scope.focus = 'project';
                }, function(res) {
                    alert(res.message);
                });
            }
            else{
                alert('必须添加风险程度');
            }
        }
    }
]); 
