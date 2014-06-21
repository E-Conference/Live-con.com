'use strict';

/* Services */

var liveconServices = angular.module('liveconServices', ['ngResource']);

liveconServices.factory('publicationFactory', ['$resource',
  function($resource){
    return $resource('/livecon.com/web/app_dev.php/api/schedule_paper.json?id=:publicationId', {}, {
      query: {method:'GET',  params:{}, isArray:true},
      create: {method:'GET', params:{}, isArray:true},
       show: {method:'GET', params:{}, isArray:true},
       list: {method:'GET', params:{'url' : '/livecon.com/web/app_dev.php/api/schedule_paper.json?conference_id=:conferenceId'}, isArray:true}
    });
  }]);



liveconServices.factory('personFactory', ['$resource',
  function($resource){
    return $resource('/livecon.com/web/app_dev.php/api/schedule_person.json?id=:personId', {}, {
      query: {method:'GET',  isArray:true},
      create: {method:'GET', params:{}, isArray:true},
       show: {method:'GET', params:{}, isArray:true},
       list: {method:'GET',  url : '/livecon.com/web/app_dev.php/api/schedule_person.json?conference_id=:conferenceId', params:{}, isArray:true}
    });
  }]);



liveconServices.factory('conferenceFactory', ['$resource',
  function($resource){
    return $resource('/livecon.com/web/app_dev.php/api/schedule_event.json?id=:conferenceId', {}, {
      query: {method:'GET',  isArray:true},
      create: {method:'GET', params:{}, isArray:true},
       show: {method:'GET', params:{}, isArray:true},
       list: {method:'GET', url :'/livecon.com/web/app_dev.php/api/schedule_event.json?id=:conferenceId', params:{}, isArray:true}
    });
  }]);



liveconServices.factory('organizationFactory', ['$resource',
  function($resource){
    return $resource('../app_dev.php/apiREST/organizations/:organizationId.json', {}, {
      query: {method:'GET',  isArray:false},
      create: {method:'POST', url :'../app_dev.php/apiREST/organizations.json', params:{}, isArray:false},
      show: {method:'GET', isArray:false},
      list: {method:'GET', url :'../app_dev.php/apiREST/organizations.json', params:{}, isArray:true}
    });
  }]);

