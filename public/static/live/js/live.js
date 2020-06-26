var ws = "ws://127.0.0.1:9502";
var web_socket = new WebSocket(ws);

//实例化连接
web_socket.onopen = function (event) {
    console.log("连接成功");
}

//实例化接收消息
web_socket.onmessage = function (event) {
    push_msg(event.data);
    console.log('接收到消息: ' + event.data);
}

//关闭
web_socket.onclose = function () {
    console.log("连接关闭");
}

//错误
web_socket.onerror = function (event, err) {
    console.log('错误: ' + err);
}

function push_msg(data) {
    data = JSON.parse(data);
    html = '<div class="frame">\n' +
        '\t\t\t\t\t<h3 class="frame-header">\n' +
        '\t\t\t\t\t\t<i class="icon iconfont icon-shijian"></i>' + data.type + ' ' + data.time + '\n' +
        '\t\t\t\t\t</h3>\n' +
        '\t\t\t\t\t<div class="frame-item">\n' +
        '\t\t\t\t\t\t<span class="frame-dot"></span>\n' +
        '\t\t\t\t\t\t<div class="frame-item-author">\n' +
        '\t\t\t\t\t\t\t<img src="' + data.logo + '" width="20px" height="20px" />' + data.title + '\n' +
        '\t\t\t\t\t\t</div>\n' +
        '\t\t\t\t\t\t<p>' + data.time + ' ' + data.content + '</p>\n' +
        '<p>' +
        '<img src="' + data.image + '" width="40%" />' +
        '</p>\n' +
        '\t\t\t\t\t</div>\n' +
        '\t\t\t\t</div>';
    $('#match-result').prepend(html);
}