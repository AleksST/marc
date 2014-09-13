var App = angular.module('App', []);

App.controller('searchCtrl', function ($scope, $http) {
    $scope.records = [];
    $scope.sources = {};
    $scope.search = '';
    $scope.selectedSources = [];

    $scope.init = function () {
        $http.get('index.php?r=site/getSources').
        success(function(data){
            angular.forEach(data, function(server) {
                if (typeof $scope.sources[server.name] === 'undefined') {
                    $scope.sources[server.name] = [server];
                } else {
                    $scope.sources[server.name].push(server);
                }
            });
        }).error(function(){
            alert('no sources');
        });
    };

    $scope.loadData = function() {
        if (!$scope.selectedSources.length) {
            return;
        }

        $scope.records = [];
        angular.forEach($scope.selectedSources, function(server) {
            $http.get('index.php?r=site/search&q=' + $scope.search + '&s=' + server.id).
            success(function(data){
                $scope.records = $scope.records.concat(data);
                console.log('For ' + server.name + ':' + server.db + ' results: ' + data.length);
            }).error(function(){
                console.log('no results for ' + server.name + ':' + server.db);
            });
        });
    };
});
