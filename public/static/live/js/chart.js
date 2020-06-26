var ws = "ws://127.0.0.1:9503";
var web_socket = new WebSocket(ws);

//实例化连接
web_socket.onopen = function (event) {
    console.log("连接成功");
}

//实例化接收消息
web_socket.onmessage = function (event) {
    push_msg_chart(event.data);
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

function push_msg_chart(data) {
    data = JSON.parse(data);
    html = '<div class="comment">\n' +
        '\t\t\t\t\t<span>' + data.user + '</span>\n' +
        '\t\t\t\t\t<span>' + data.content + '</span>\n' +
        '\t\t\t\t</div>';
    $('#comments').prepend(html);
}