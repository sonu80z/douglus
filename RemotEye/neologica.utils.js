if (this.NeoLogica === undefined)
{
  this.NeoLogica = {};
}

NeoLogica.Utils = (function(){
  var MSG_GETJAVAINFO_RQ = "MSG_GETJAVAINFO_RQ";
  var MSG_GETJAVAINFO_RSP = "MSG_GETJAVAINFO_RSP";
  var MSG_LAUNCHJNLP_RQ = "MSG_LAUNCHJNLP_RQ";
  var MSG_LAUNCHJNLP_RSP = "MSG_LAUNCHJNLP_RSP";

  var bWebSockCompatible = ("WebSocket" in window);
  var aWebSockPorts = [9088, 9089, 9090, 9091, 9092];
  var aWebSrvPorts = aWebSockPorts;
  var aWebSockPortToBeChecked = aWebSockPorts.slice();
  var webSocket = null;
  var keepCallingCallback = false;
  var connectionTimeout = null;
  var imageLoadTimeout = null;
  var info = {
    javaInfo: null,
    isJavaInstalled: false,
    isRIAHelperInstalled: false,
    isWebSockCompatible: bWebSockCompatible,
    isWebSockConnected: false
  };
  var onWebSockConnectedFired = false;
  var riahImageFound = false;
  var iRIAHImageFailCount = 0;
  var iListeningPort = null;    
  // contains [{secure: true|false, port: int}]
  // it is used when the image check works  
  var aWebSocksToBeTested = [];
  var bWebSockTestRunning = false;
  var aImgsToBeChecked = [];
  // check if it is win64
  var bIsWin64 = navigator.userAgent.indexOf('WOW64') > -1 ||
                 navigator.platform == 'Win64' ||
                 navigator.userAgent.indexOf('Win64') > -1 ||
                 navigator.userAgent.indexOf('x64') > -1;
  
  // used as a fallback launching method
  // on some old os + ie
  var riahIFrameID = "riahFrame" + Math.round(Math.random() * 1000);

  var _removeImageToBeChecked = function(img)
  {
    for (var i = 0; i < aImgsToBeChecked.length; i++)
    {
      if (aImgsToBeChecked[i] === img)
      {
        break;
      }
    }
    if (i < aImgsToBeChecked.length)
    {      
      aImgsToBeChecked.splice(i, 1);
    }
  };
  // the function called when the connection
  // with the web socket is available
  var onWebSockConnected = function(){};

  var _onJavaInfoResponse = function(message)
  {
    if (message.data.constructor == String)
    {
      try
      {
        var msg = JSON.parse(message.data);
        if (msg.msgType == MSG_GETJAVAINFO_RSP)
        {
          // good correct answer
          // remove the current listener
          var dataMap = msg.dataMap;
          webSocket.removeEventListener('message', _onJavaInfoResponse, false);
          info.isWebSockConnected = true;          
          if (dataMap && dataMap.status == "OK")
          {
            delete dataMap.status;
            info.javaInfo = dataMap;
            // console.log(info.javaInfo);
            // finally call the callback
            info.isRIAHelperInstalled = true;
            onWebSockConnectedFired = true;            
            onWebSockConnected(info);
          }
          else
          {
            info.isRIAHelperInstalled = true;
            onWebSockConnectedFired = true;
            onWebSockConnected(info);
          }
          // test finished, no more tests needed
          bWebSockTestRunning = false;          
          aWebSocksToBeTested = [];
        }
      }
      catch (e)
      {
        console.log("HERE");
        console.log(e);
        webSocket.close();
        _onWebSocketConnectionFailed();
      }
    }
    else
    {
      // console.log("Warning an unexpected binary message has arrived");
      webSocket.close();
      _onWebSocketConnectionFailed();
    }
  };

  var _onWebSocketConnectionLost = function() 
  {
    bWebSockTestRunning = false;
    info.isWebSockConnected = false;
    _connect();
  };

  var _onWebSocketConnectionFailed = function() 
  {
    // try again if there's still other port.
    bWebSockTestRunning = false;
    _connect();    
  };

  var _connect = function()
  {    
    if ((bWebSockTestRunning === false) && 
        (!info.isWebSockConnected))
    {
      if (bWebSockCompatible)
      {
        bWebSockTestRunning = true;
        info.isWebSockConnected = false;
        var webSockInfo = aWebSocksToBeTested.shift();        
        if (webSockInfo)
        {
          var port = webSockInfo.port;
          var hostname = webSockInfo.secure ? "wss://localhost.neologica.it:" : "ws://127.0.0.1:";
          if (port)
          {
            try
            {
              webSocket = new WebSocket(hostname + port +
                              "/RIAHelper/RIAHelperWS?encoding=text");
              webSocket.onopen = function() {
                // webSocket = this;
                // get Java Info now
                // console.log("Connected on port " + port);
                webSocket.addEventListener('message', _onJavaInfoResponse, false);

                webSocket.onclose = function(e) {
                  webSocket.onerror = function() {};
                  _onWebSocketConnectionLost();
                };                
                webSocket.onerror = function(e) {
                  webSocket.onclose = function() {};
                  _onWebSocketConnectionLost();
                };                
                webSocket.send(JSON.stringify({
                  msgType: MSG_GETJAVAINFO_RQ
                }));
              };

              webSocket.onerror = function(e) {
                webSocket.onclose = function() {};
                _onWebSocketConnectionFailed();
              };

              webSocket.onclose = function(e) {
                webSocket.onerror = function() {};
                // console.log("(onclose) Unable to connect to ws on " + port);
                _onWebSocketConnectionFailed();
              };
            }
            catch (e)
            {
              console.log(e);
               _onWebSocketConnectionFailed();
            }
          }
          else
          {
            _onWebSocketConnectionFailed();
          }
        }
        else
        {
          // ok no more ports to be tested,
          bWebSockTestRunning = false;
          webSocket = null;
          aWebSockPortToBeChecked = aWebSockPorts.slice();
          // All the ports have been tested,
          // call the callback
          // I was not able to connect via websocket
          // but the image has been found
          info.isWebSockConnected = false;
          onWebSockConnectedFired = true;
          onWebSockConnected(info);
        }
      }
      else
      {
        // Do nothing _checkImage has already worked
        // web socket not supported
        bWebSockTestRunning = false;
        webSocket = null;
        aWebSockPortToBeChecked = aWebSockPorts.slice();
        info.isWebSockCompatible = false;
        info.isWebSockConnected = false;
        onWebSockConnectedFired = true;
        onWebSockConnected(info);
      }        
    }
    // if the check was already running, then the next check will be
    // automatically made on connection fail.
    
  };

  var _onRIAHImageDownloaded = function() {
    // this = dom image    
    _removeImageToBeChecked(this);
    iRIAHImageFailCount = 0;
    info.isRIAHelperInstalled = true;
    // Ok, now connect the websocket (directly to the image port)    
    aWebSocksToBeTested.push({
      secure: this.secure,
      port: this.listeningPort
    });    
    _connect();
  };

  var _onRIAHImageDownloadFailed = function() 
  {
    // this = dom image    
    _removeImageToBeChecked(this);
  };

  var _onRIAHImageDownloadTimeout = function() {    
    if (aImgsToBeChecked.length)
    {
      var img = aImgsToBeChecked.pop();
      while (img) 
      {
        img.onload = function() {};
        img.onerror = function() {};
        img.src = "";
        img = aImgsToBeChecked.pop();
      }
    }
    // at this point check if we are already connected
    // if not, emit the error
    if (!info.isRIAHelperInstalled)
    {
      info.isRIAHelperInstalled = false;
      onWebSockConnected(info);
      // avoid calling the callback when and if the
      // web socket connects
      if (!keepCallingCallback)
      {
        onWebSockConnected = function(){};
      }
      // check again in 1 minute      
      connectionTimeout = setTimeout(_checkImagePresence, 60000);
    }    
  };

  var _checkImagePresence = function() {
    // check on every port simoultaneously
    iRIAHImageFailCount = 0;
    aImgsToBeChecked = [];
    for (var i = 0; i < aWebSockPorts.length; i++)
    {
      var port = aWebSockPorts[i];
      var httpsSrc = "https://localhost.neologica.it:" + port + "/images/RIAHelper_16x16.png?" +
                new Date().getTime();
      
      var httpsImg = new Image();
      httpsImg.onload = _onRIAHImageDownloaded;
      httpsImg.onerror =  _onRIAHImageDownloadFailed;
      httpsImg.src = httpsSrc;
      httpsImg.listeningPort = port;
      httpsImg.secure = true;

      aImgsToBeChecked.push(httpsImg);
      
      if (i === 0) // avoid trying to check http on first port. If used, it will always be https
      {
        continue;
      }

      var httpSrc = "http://127.0.0.1:" + port + "/images/RIAHelper_16x16.png?" +
                new Date().getTime();

      var httpImg = new Image();
      httpImg.onload = _onRIAHImageDownloaded;
      httpImg.onerror =  _onRIAHImageDownloadFailed;
      httpImg.src = httpSrc;
      httpImg.listeningPort = port;
      httpImg.secure = false;
      
      aImgsToBeChecked.push(httpImg);
    }
    imageLoadTimeout = setTimeout(_onRIAHImageDownloadTimeout, 5000);
  };

  var _launch = function(jnlpURL, shortcutName, shortcutExt, preferHandler)
  {
    // starting from re-v 9.2.3 the preferred launching method is
    // via the protocol handler
    if (preferHandler === false && webSocket)
    {
      if (webSocket)
      {
        var o = {
          msgType: MSG_LAUNCHJNLP_RQ,
          dataMap: {
            jnlpURL: jnlpURL
          }
        };
        if (shortcutName)
        {
          o.dataMap.shortcutName = shortcutName;
        }
        if (shortcutExt)
        {
          o.dataMap.shortcutExt = shortcutExt;
        }
        webSocket.send(JSON.stringify(o));
      }
      else if (info.isRIAHelperInstalled) // in case the image validation worked
      {
        // try with the handler
        window.location.href = _getRIAHelperHandler(jnlpURL, shortcutName, shortcutExt);
      }
    }
    
    if (info.isRIAHelperInstalled) // in case the image validation worked
    {
      // try with the handler
      // old version of Windows + IE (even with IE 11 on Windows 7)
      // might throw an exception if the URL lenght is > than 508 characters
      // In that case, we can work around the limitation using an iframe
      try 
      {
        window.location.href = 
          _getRIAHelperHandler(jnlpURL, shortcutName, shortcutExt);
      }
      catch (e)
      {
        var iFrame = document.getElementById(riahIFrameID);
        if (!iFrame) 
        {
          iFrame = document.createElement("iframe");
          iFrame.id = riahIFrameID;
          iFrame.style.display = "none";
          document.body.appendChild(iFrame);
        }
        var finalLink = "rhjnlp:" + 
          encodeURIComponent(
            _getRIAHelperHandler(jnlpURL, shortcutName, shortcutExt)
              .replace("rhjnlp:", ""));          
        iFrame.src = finalLink;
      }
      
      return;
    }
            
  };

  var _isJavaActiveXComponentAvailable = function()
  {
    if (!('ActiveXObject' in window))
    {
      return false;
    }

    var aVer = [6, 7, 8, 9];
    var bFound = false;
    for (var i = 0; i < aVer.length; i++)
    {
      var ver = aVer[i];
      try
      {
        // Detect Java X platform
        var obj = new ActiveXObject("JavaWebStart.isInstalled.1." + ver + ".0.0");
        if (obj !== null)
        {
          bFound = true;
          break;
        }
      }
      catch (exception)
      {

      }
    }
    return bFound;
  };

  var _isJavaWSMimeTypeAvailable = function()
  {
    if (navigator.mimeTypes && navigator.mimeTypes.length)
    {
      var m = navigator.mimeTypes['application/x-java-jnlp-file'];
      if (m)
      {
        return true;
      }
    }
    return false;
  };

  var _isJavaAppletMimeTypeAvailable = function()
  {
    var aVer = [6, 7, 8, 9];
    var bFound = false;
    if (navigator.mimeTypes && navigator.mimeTypes.length)
    {
      for (var i = 0; i < aVer.length; i++)
      {
        var ver = aVer[i];
        if (navigator.mimeTypes && navigator.mimeTypes.length)
        {
          var sMimeType = "application/x-java-applet;version=1." + ver;
          var m = navigator.mimeTypes[sMimeType];
          if (m)
          {
            bFound = true;
            break;
          }
        }
      }
    }
    return bFound;
  };

  var _isJavaInstalled = function(callback)
  {
    // if the callback is not specified then
    // behave like old code
    info.isJavaInstalled = _isJavaActiveXComponentAvailable() ||
                           _isJavaWSMimeTypeAvailable()       ||
                           _isJavaAppletMimeTypeAvailable();
    if (!!!callback)
    {
      return info.isJavaInstalled;
    }
    else
    {
      // check already successfull ?
      if (onWebSockConnectedFired)
      {
        callback(info);
      }
      else
      {
        onWebSockConnected = callback;
      }
    }

  };

  var _isJavaInstalledCheckNow = function (callback) {
    // if the callback is not specified then
    // behave like old code
    info.isJavaInstalled = _isJavaActiveXComponentAvailable() ||
                           _isJavaWSMimeTypeAvailable()       ||
                           _isJavaAppletMimeTypeAvailable();
    if (!!!callback)
    {
      return info.isJavaInstalled;
    }
    else
    {
      if (info.isRIAHelperInstalled)
      {
        callback(info);
      }
      else
      {
        clearTimeout(connectionTimeout);
        clearTimeout(imageLoadTimeout);
        webSocket = null;
        aWebSockPortToBeChecked = aWebSockPorts.slice();
        onWebSockConnected = callback;
        _checkImagePresence();
      }
    }
  };

  var _isWindows = function()
  {
    return (navigator.platform.indexOf("Win") != -1);
  };

  var _isMacOS = function()
  {
    return (navigator.platform.indexOf("Mac") != -1);
  };

  var _isLinux = function()
  {
    return (navigator.platform.indexOf("Linux") != -1);
  };

  var _isWSConnected = function()
  {
    return (!!webSocket) && (webSocket.readyState === 1);
  };

  var _isInitialCheckCompleted = function()
  {
    return info.isRIAHelperInstalled;
  };

  var _getRIAHelperHandler = function(url, shortcutName, shortcutExt)
  {
    var o = {
      msgType: MSG_LAUNCHJNLP_RQ,
      dataMap: {
        jnlpURL: url
      }
    };
    if (shortcutName)
    {
      o.dataMap.shortcutName = shortcutName;
    }
    if (shortcutExt)
    {
      o.dataMap.shortcutExt = shortcutExt;
    }
    return "rhjnlp:" + encodeURIComponent(JSON.stringify(o));
  };

  // _connect();
  _checkImagePresence();

  return {
    setKeepCallingCallback: function(bVal) { keepCallingCallback = bVal; },
    isJavaInstalled: _isJavaInstalled,
    isJavaInstalledCheckNow: _isJavaInstalledCheckNow,
    isWindows: _isWindows,
    isMacOS: _isMacOS,
    isLinux: _isLinux,
    isWSConnected: _isWSConnected,
    launch: _launch,
    getRIAHelperHandler: _getRIAHelperHandler,
    isInitialCheckCompleted: _isInitialCheckCompleted,
    isWin64: function() { return bIsWin64; }
  };

})();
