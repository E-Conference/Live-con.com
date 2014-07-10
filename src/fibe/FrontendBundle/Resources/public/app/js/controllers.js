'use strict';

/* Controllers */

var liveconControllers = angular.module('liveconControllers', []);

/*********************************** NAVS **********************************************/
liveconControllers.controller('mainCtrl', ['$scope', '$routeParams',
  function($scope, $routeParams) {

  }]);



/*********************************** NAVS **********************************************/
liveconControllers.controller('navLeftCtrl', ['$scope', '$routeParams', 'CONFERENCE_CONFIG',
  function($scope, $routeParams, CONFERENCE_CONFIG) {
    $scope.liveconLogoPath = CONFERENCE_CONFIG.liveconLogoPath;
  }]);

liveconControllers.controller('navRightCtrl', ['$scope', '$routeParams', 
  function($scope, $routeParams) {
  }]);

liveconControllers.controller('navTopCtrl', ['$scope', '$routeParams', 'CONFERENCE_CONFIG',
  function($scope, $routeParams, CONFERENCE_CONFIG) {
     $scope.liveconLogoPath = CONFERENCE_CONFIG.liveconLogoPath;
     $scope.toggleNavLeft = function() {
        $("#wrapper").removeClass("active-right");
        $("#wrapper").toggleClass("active-left");
    
      };
  }]);

/*********************************** PUBOLICATIONS **********************************************/
liveconControllers.controller('publicationsCtrl', ['$scope', '$rootScope', 'publicationFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, publicationFactory, CONFERENCE_CONFIG) {
    $scope.publications = publicationFactory.list({conferenceId: CONFERENCE_CONFIG.conferenceId});
    $scope.orderProp = 'age';
    $rootScope.title = "Publications";
  }]);


liveconControllers.controller('publicationCtrl', ['$scope', '$rootScope', '$routeParams', 'publicationFactory',
  function($scope, $rootScope, $routeParams, publicationFactory) {
    publicationFactory.show({publicationId: $routeParams.publicationId}, function(publication) {  
       $scope.publication = publication[0];
       $rootScope.title = publication[0].title;
    });
}]);


/*********************************** PERSONS **********************************************/
liveconControllers.controller('personsCtrl', ['$scope', '$rootScope', '$routeParams', 'personFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, personFactory, CONFERENCE_CONFIG) {
    personFactory.list({conferenceId: CONFERENCE_CONFIG.conferenceId}, function(persons) {
      $scope.persons = persons;
      $rootScope.title = "Speakers";
    });
  }]);

liveconControllers.controller('personCtrl', ['$scope', '$rootScope','$routeParams', 'personFactory',
  function($scope, $rootScope, $routeParams, personFactory) {
    personFactory.show({personId: $routeParams.personId}, function(person) {
      $scope.person = person[0]; 
      $rootScope.title = person[0].name;
    });
 }]);



/*********************************** CONFERENCE **********************************************/
liveconControllers.controller('conferenceCtrl', ['$scope', '$rootScope','$routeParams', 'conferenceFactory',  'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, conferenceFactory, CONFERENCE_CONFIG) {
    $scope.phone = conferenceFactory.show({conferenceId: CONFERENCE_CONFIG.conferenceEventId}, function(conference) {
      $scope.conference = conference[0];
      $rootScope.title = conference[0].name;
    });
  }]);


/*********************************** ORGANIZATIONS **********************************************/
liveconControllers.controller('organizationsCtrl', ['$scope', '$rootScope','$routeParams', 'organizationFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, organizationFactory, CONFERENCE_CONFIG) {
    $scope.organizations = organizationFactory.list();
  }]);

liveconControllers.controller('organizationCtrl', ['$scope', '$rootScope','$routeParams', 'organizationFactory',
  function($scope, $rootScope, $routeParams, organizationFactory) {

    $scope.organization = organizationFactory.query({organizationId: $routeParams.organizationId});
    $scope.create = function(){$scope.organization.$create()};
 }]);

/*********************************** DASHBOARDS **********************************************/
liveconControllers.controller('dashboardCtrl', ['$scope', '$rootScope','$routeParams', 'organizationFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, organizationFactory, CONFERENCE_CONFIG) {
    
}]);

/*********************************** WIDGETS **********************************************/
liveconControllers.controller('widgetCtrl', ['$scope', '$rootScope','$routeParams', 'organizationFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, organizationFactory, CONFERENCE_CONFIG) {
    
}]);


/*********************************** SCHEDULE **********************************************/
liveconControllers.controller('scheduleCtrl', ['$scope', '$rootScope','$routeParams', 'organizationFactory', 'CONFERENCE_CONFIG',
  function($scope, $rootScope, $routeParams, organizationFactory, CONFERENCE_CONFIG) {
    
}]);

