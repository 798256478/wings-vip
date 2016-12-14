(function () {
	'use strict';

	angular.module('app')
		.service('ReservationService', ['$http', 'urls', 'Upload', '$q', function ($http, urls, Upload, $q) {
			var self = this;

			this.getReservationList=function(filter){
				var deferred = $q.defer();
				$http.post(urls.BASE_API + '/reservation',filter).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			};


		}
		])
})();