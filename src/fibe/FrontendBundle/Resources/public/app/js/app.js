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
      when('/dashboard/overview', {
        templateUrl: '../../web/bundles/frontend/app/partials/dashboards/dashboard-overview.html',
        controller: 'dashboardCtrl'
      }).
      when('/dashboard/schedule', {
        templateUrl: '../../web/bundles/frontend/app/partials/dashboards/dashboard-schedule.html',
        controller: 'dashboardCtrl'
      }).
      when('/dashboard/widget', {
        templateUrl: '../../web/bundles/frontend/app/partials/dashboards/dashboard-widget.html',
        controller: 'dashboardCtrl'
      }).
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
      when('/widget/hightlight', {
        templateUrl: '../../web/bundles/frontend/app/partials/widget/hightlight.html',
        controller: 'widgetCtrl'
      }).
      when('/widget/app', {
        templateUrl: '../../web/bundles/frontend/app/partials/widget/mobileApp.html',
        controller: 'widgetCtrl'
      }).
      when('/widget/ticketPage', {
        templateUrl: '../../web/bundles/frontend/app/partials/widget/ticketPage.html',
        controller: 'widgetCtrl'
      }).
       when('/widget/calendar', {
        templateUrl: '../../web/bundles/frontend/app/partials/widget/eCalendar.html',
        controller: 'widgetCtrl'
      }).
      when('/schedule/calendar', {
        templateUrl: '../../web/bundles/frontend/app/partials/schedule/schedule-calendar.html',
        controller: 'scheduleCtrl'
      }).
       when('/schedule/list', {
        templateUrl: '../../web/bundles/frontend/app/partials/schedule/schedule-list.html',
        controller: 'scheduleCtrl'
      }).
      when('/schedule/thumbnail', {
        templateUrl: '../../web/bundles/frontend/app/partials/schedule/schedule-thumbnail.html',
        controller: 'scheduleCtrl'
      }).
      when('/schedule/tree', {
        templateUrl: '../../web/bundles/frontend/app/partials/schedule/schedule-tree.html',
        controller: 'scheduleCtrl'
      }).
      when('/search/event', {
        templateUrl: '../../web/bundles/frontend/app/partials/home/searchEvent.html',
        controller: 'scheduleCtrl'
      }).
       when('/search/organization', {
        templateUrl: '../../web/bundles/frontend/app/partials/home/searchOrganization.html',
        controller: 'scheduleCtrl'
      }).
        when('/search/person', {
        templateUrl: '../../web/bundles/frontend/app/partials/home/searchPerson.html',
        controller: 'scheduleCtrl'
      }).
      when('/', {
        templateUrl: '../../web/bundles/frontend/app/partials/home/home.html',
        controller: 'conferenceCtrl'
      }).
      otherwise({
        redirectTo: '/'
      });
  }]);

