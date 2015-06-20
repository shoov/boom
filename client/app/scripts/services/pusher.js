'use strict';

/**
 * @ngdoc service
 * @name clientApp.account
 * @description
 * # account
 * Service in the clientApp.
 */
angular.module('clientApp')
  .service('channelManager', function ($q, $http, $timeout, $pusher, $log, Config, Auth) {

    var channels = {};

    this.getChannels =  function () {
      return channels;
    };

    this.addChannel = function (repoId) {
      var pusherConf = {
        authEndpoint: Config.backend + '/api/v1.0/pusher_auth',
        auth: {
          headers: {
            "access-token": Auth.getAccessToken()
          }
        }
      };

      var client = new Pusher('b2ac1e614d90c85ec13b', pusherConf);
      var pusher = $pusher(client);
      channels[repoId] = pusher.subscribe('private-repo-' + repoId);
      return channels[repoId];
    };
  });
