function ajax_call(method, url, params, response_destination, encode_header, callback) {

  if (window.XMLHttpRequest) {
    // IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  } else {
    // IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      if (xmlhttp.responseText != '') {
        if (response_destination) {
          response_destination.innerHTML = xmlhttp.responseText;
        }
      }
      callback();
    }
  }

  xmlhttp.open(method, url, true);

  if (encode_header == true) {
    xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  }

  xmlhttp.send(params);

}

function like_add(base_url, item_id, word_add, word_remove) {

  var url = base_url + 'likes/add/' + item_id;
  var response_destination = document.getElementById('likes_' + item_id);

  ajax_call('POST', url, null, response_destination, null, function(){
    if (update_element = document.getElementById('like_link_' + item_id)) {
      update_element.innerHTML = '<a href="#" onclick="like_remove(\'' + base_url + '\', ' + item_id + ', \'' + word_add.replace("'", "\\\'") + '\', \'' + word_remove.replace("'", "\\\'") + '\'); return false;">' + word_remove + '</a>';
    }
  });

}

function like_remove(base_url, item_id, word_add, word_remove) {

  var url = base_url + 'likes/remove/' + item_id;
  var response_destination = document.getElementById('likes_' + item_id);

  ajax_call('POST', url, null, response_destination, null, function(){
    if (update_element = document.getElementById('like_link_' + item_id)) {
      update_element.innerHTML = '<a href="#" onclick="like_add(\'' + base_url + '\', ' + item_id + ', \'' + word_add.replace("'", "\\\'") + '\', \'' + word_remove.replace("'", "\\\'") + '\'); return false;">' + word_add + '</a>';
    }
  });

}

function comment_add(base_url, item_id) {

  var content = document.forms['comment_form_' + item_id].content;

  var url = base_url + 'comments/add';
  var params = 'item_id=' + item_id + '&content=' + content.value;
  var response_destination = document.getElementById('comments_' + item_id);

  ajax_call('POST', url, params, response_destination, true, function(){
    if (update_element = content) {
      update_element.value = '';
    }
  });

}

function comment_remove(base_url, comment_id, item_id) {

  var url = base_url + 'comments/remove/' + comment_id;
  var response_destination = document.getElementById('comments_' + item_id);

  ajax_call('POST', url, null, response_destination, null, null);

}

function friend_add(base_url, friend_user_id) {

  var url = base_url + 'friends/add/' + friend_user_id;
  var response_destination = document.getElementById('friends_' + friend_user_id);

  ajax_call('POST', url, null, response_destination, null, null);

}

function friend_remove(base_url, friend_user_id) {

  var url = base_url + 'friends/remove/' + friend_user_id;
  var response_destination = document.getElementById('friends_' + friend_user_id);

  ajax_call('POST', url, null, response_destination, null, null);

}