/*

And here's the initial frontend. It's not perfect, but it totally works.
I'll come back and refactor when I finally settle on a paradigm.

*/

var Ele = {
  byId: function(id) {
    return document.getElementById(id);
  }
}

var Ajaxy = {
  get: function(url, callback) {
    xmlhttp = new XMLHttpRequest();
    xmlhttp.open('get', url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        callback(xmlhttp.responseText);
      }
    }
  }
}

var UI = {
  startup: function() {
    Ele.byId('mimetype').disabled = true;
    Ajaxy.get('../backend/?action=getMimeTypes', this.fillMimetypes);
  },
  fillMimetypes: function(response) {
    var dropdown = Ele.byId('mimetype');
    response = JSON.parse(response);
    for (var i in response.mimetypes) {
      dropdown.innerHTML += '<option>'+response.mimetypes[i].ext+' ('+response.mimetypes[i].type+')</option>';
    }
    Ele.byId('mimetype').disabled = false;
  }
}

UI.startup();