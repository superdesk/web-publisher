var OFFLINE_URL = '/public/offlinePage.html';
// helper regex that matches success response.status
var successResponses = /^0|([123]\d\d)|(40[14567])|410$/;
toolbox.precache([OFFLINE_URL]);

toolbox.router.default = pagesCacheHandler;

/**
 * sw-toolbox custom strategy
 */
 function pagesCacheHandler(request, values, options) {
    // check if url is in sections database
    return db_get(request.url).then(function(result){
        // if section -> handle with sectionsCacheHandler
        if(result) return sectionsCacheHandler(request);
        //else find request in caches
        return caches.match(request).then(function(response) {
          // if response is found it must be in articles-cache
          // we open that cache and run fetchAndCache helper function
          // in order to fetch latest version of article
          if (response){
            caches.open('articles-cache').then(function(cache){
              fetchAndCache(request.url, cache);
            });
            // fetchAndCache works in bg. We return response from cache
            return response.clone();
          }
          // else we fetch request
          console.log("pagesCacheHandler | fetch fired for: "+request.url);
          return fetch(request).catch(function(error){
            // in case of error we try pass request to function that returns offline page if possible
            return offlineHandler(request);
            throw Error('offline');
          });
        });
      });
}


/**
 * sectionsCacheHandler
 * @param {Request} request
 * @returns {Response}
 * network first request handler for sections-cache
 */
 function sectionsCacheHandler(request){
  return caches.open('sections-cache').then(function(cache) {
    var timeoutId;
    var promises = [];
    var networkTimeoutSeconds = 3;

    var cacheWhenTimedOutPromise = new Promise(function(resolve) {
      timeoutId = setTimeout(function() {
        cache.match(request).then(function(response) {
          if (response) resolve(response);
        });
      }, networkTimeoutSeconds * 1000);
    });
    promises.push(cacheWhenTimedOutPromise);

    var networkPromise = fetchAndCache(request.url, cache).then(function(response) {
        // We've got a response, so clear the network timeout if there is one.
        if (timeoutId) clearTimeout(timeoutId);

        if (successResponses.test(response.status)) {
          // if we have a valid response it is already updated in cache by fetchAndCache.
          // We should now update sections-cache database
          db_update(request.url);
          // ...and return response
          return response;
        }

        throw new Error('Bad response');
      }).catch(function(error) {

        return cache.match(request).then(function(response) {
          // If there's a match in the cache, resolve with that.
          if (response) return response;
          // pass request to offlineHandler that returns offline page
          return offlineHandler(request);
          // If we don't have a Response object, offlineHandler couldn't respond for any reason
          // then reject with the failure error.
          throw error;
        });
      });

      promises.push(networkPromise);
    // RACE!
    return Promise.race(promises);
  });
}



/**
 * Event Listener
 * handles communication with website and cahces received article urls
 */
 self.addEventListener('message', function(event) {
  var data = event.data;
  if (data.command == "pleaseCache") {
    caches.open('articles-cache').then(function(cache) {
      cache.match(data.url).then(function(response) {
        if(!response) fetchAndCache(data.url, cache);
      });
      return;
    });
  }
});

// --------------- HELPERS

/**
 * offlineHandler
 * @param {Request} request
 * @returns {Response}
 * returns offline page if possible
 */
 function offlineHandler(request){
  //  if U don't want to have custom offline page uncomment line below.
  //  throw Error('Offline');
  if (request.method === 'GET' && request.headers.get('accept').includes('text/html')){
    console.log("Getting offline page...");
    return caches.match(OFFLINE_URL).then(function(response) {
      if (response) return response;
      throw Error('The cached response that was expected is missing.');
    });
  }
}

/**
 * fetchAndCache
 * @param {string} url - url to fetch and cache
 * @param {Object} cache - cache object
 * @returns {Response}
 * fetches given url, puts result to given cache, clears given cache
 */
 function fetchAndCache(url, cache) {
  return fetch(url).then(function (response) {
    if (successResponses.test(response.status)) {
      // caching response
      console.log('got '+url);
      cache.put(url, response.clone());
      // clear cache. Maximum items in cache is 100
      cache.keys().then(function(keys) {
        if(keys.length > 100){
          var loopLength = keys.length - 100;
          for (var i = 0; i < loopLength; i++) {
            cache.delete(keys[i]);
            console.log("deleting: "+keys[i] );
          }
        }
      });
    }
    return response.clone();
  });
};


/**
 * db_get
 * @param {string} url
 * @returns {Promise}
 * checks if given url exists in sections-cache database
 */
 function db_get(url){
  return new Promise(function(resolve, reject) {

    var _url = url;
    var open = indexedDB.open("sections-cache", 1);

    open.onupgradeneeded = function() {
      var db = open.result;
      var store = db.createObjectStore("store", {keyPath: "url"});
      var index = store.createIndex("url", "url", {unique: true});
    };

    open.onsuccess = function() {
      var db = open.result;
      var store = db.transaction("store", "readwrite").objectStore("store");
      var index = store.index("url");

      var _get = index.get(_url);
      _get.onsuccess = function() {
        if(_get.result) resolve(_get.result);
        resolve(false);
      };

    }
  });
}

/**
 * db_update
 * @param {string} url
 * updates `cached` and `timestamp` keys for given url in sections-cache database.
 * It is later used to mark cached sections in menu
 */
 function db_update(url){

  var _url = url;
  var open = indexedDB.open("sections-cache", 1);

  open.onupgradeneeded = function() {
    var db = open.result;
    var store = db.createObjectStore("store", {keyPath: "url"});
    var index = store.createIndex("url", "url", {unique: true});
  };

  open.onsuccess = function() {
    var db = open.result;
    var store = db.transaction("store", "readwrite").objectStore("store");

    var _timestamp = Date.now();
    // put overwrites existing keys
    store.put({url: _url, cached: 1, timestamp: _timestamp});
  };
}
