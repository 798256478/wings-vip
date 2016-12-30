/**
 * Customer Controller
 */
'use strict';

angular.module('app')
.controller('GeneController', ['$scope', '$location', 'AuthService','GeneService',
    function($scope, $location, AuthService,GeneService) {
        if (AuthService.current_user == null) {
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('基因管理');
        $scope.form = {};
        $scope.genes=[];
        $scope.search={code:''};
        $scope.pages={pageindex: 1,pagesize:20,totla:0};
        $scope.init=function()
        {
            $scope.current_gene={id:0,name:'',default_effect:'',site_count:0,default_is_positive:0};
            $scope.current_site={code:'',rs_code:'',mutation:'',SNPSite:'',DNASingleType:'',type:'snp'};
            $scope.focus='gene';
        }
        $scope.init();
       
        $scope.get_genes=function() {
            GeneService.get_genes($scope.search.code, $scope.pages.pageindex, $scope.pages.pagesize).then(function(res) {
                $scope.pages.total = res.count;
                $scope.genes=res.genes;
            }, function(res) {
                alert(res.message);
            });
        }
         $scope.get_genes();
        
        $scope.gene_module_add=function() {
            $scope.init();
            $scope.form.gene.$setPristine();
        }
        
        $scope.gene_module_edit=function(gene) {
            $scope.current_gene=gene;
            $scope.focus='gene';
        }
       
        $scope.gene_save=function(type) {
            var gene={};
            gene.id=$scope.current_gene.id;
            gene.name=$scope.current_gene.name;
            gene.default_effect=$scope.current_gene.default_effect;
            gene.site_count=$scope.current_gene.site_count;
            gene.default_is_positive=$scope.current_gene.default_is_positive;
            GeneService.gene_save(gene).then(function(res) {
                $scope.get_genes();
                if(type==0){
                    $scope.init();
                }
                else{
                     $scope.focus='site';
                     $scope.current_gene.id = res;
                }
                $scope.form.gene.$setPristine();
              
            }, function(res) {
                alert(res.message);
            });
        }
        $scope.update_focus=function(str){
             $scope.focus=str;
        }
        
        $scope.site_module_edit=function(site) {
            $scope.focus='site';
            $scope.current_site=site;
        }
        
        $scope.sites_save=function() {
             $scope.current_site.gene_id = $scope.current_gene.id;
             GeneService.site_save( $scope.current_site).then(function(res) {
                $scope.current_site={code:'',rs_code:'',mutation:'',SNPSite:'',DNASingleType:'',type:'snp'};
                $scope.current_gene.sites=res.sites;
                $scope.form.site.$setPristine();
            }, function(res) {
                alert(res.message);
            });
        }
        
         $scope.$watchCollection('pages.pageindex', function(newVal, oldVal) {
            if(newVal==oldVal) return;
            if(newVal <= 0)  return;
            $scope.get_genes();
        });
        
    }
]);
