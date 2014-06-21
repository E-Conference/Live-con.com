'use strict';

/* App Module */

var liveconApp = angular.module('liveconApp', [
  'ngRoute',
  'ngAnimate',
  'angular-loading-bar',
  'liveconControllers',
  'liveconFilters',
  'liveconServices'
]);

liveconApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/publications', {
        templateUrl: '../../web/bundles/frontend/app/partials/publication/publication-list.html',
        controller: 'publicationsCtrl'
      }).
      when('/publication/:publicationId', {
        templateUrl: '../../web/bundles/frontend/app/partials/publication/publication-detail.html',
        controller: 'publicationCtrl'
      }).
      when('/speakers', {
        templateUrl: '../../web/bundles/frontend/app/partials/person/persons-list.html',
        controller: 'personsCtrl'
      }).
      when('/speaker/:personId', {
        templateUrl: '../../web/bundles/frontend/app/partials/person/person-detail.html',
        controller: 'personCtrl'
      }).
      when('/organizations', {
        templateUrl: '../../web/bundles/frontend/app/partials/organization/organization-list.html',
        controller: 'organizationsCtrl'
      }).
      when('/organization/show/:organizationId', {
        templateUrl: '../../web/bundles/frontend/app/partials/organization/organization-show.html',
        controller: 'organizationCtrl'
      }).
      when('/organization/edit/:organizationId', {
        templateUrl: '../../web/bundles/frontend/app/partials/organization/organization-edit.html',
        controller: 'organizationCtrl'
      }).
       when('/organization/new', {
        templateUrl: '../../web/bundles/frontend/app/partials/organization/organization-new.html',
        controller: 'organizationCtrl'
      }).
      when('/', {
        templateUrl: '../../web/bundles/frontend/app/partials/conference/conference-detail.html',
        controller: 'conferenceCtrl'
      }).
      otherwise({
        redirectTo: '/'
      });
  }]);

