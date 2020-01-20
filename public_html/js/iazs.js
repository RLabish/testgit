  function gridtoexcel(gridID, url) {
    var list = document.getElementById(gridID).getElementsByTagName("thead")[0].getElementsByTagName("tr");
    var elems = list[list.length - 1].getElementsByTagName("*");
    for(var i=0; i<elems.length; i++) {
      if ((elems[i].tagName == "INPUT") || (elems[i].tagName == "SELECT")) {
        url = url + "&" + elems[i].name + "=" + elems[i].value + "&CardOperations_sort=date";
      }
    }
    location.href=url;
    return false;
  }
