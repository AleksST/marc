var App = angular.module('App', []);

App.controller('searchCtrl', function ($scope, $http) {
    $scope.records = [];
    $scope.search = '';
    $scope.loadData = function() {
        $http.get('/marc/libsearch/public/index.php?r=site/search&q=' + $scope.search).
            success(function(data){
                $scope.records = data;
            }).error(function(){
                alert('no results');
            });
    }
});