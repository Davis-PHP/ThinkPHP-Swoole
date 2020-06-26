$(function () {
    $('#enter_key').keydown(function (e) {
        if (e.keyCode == 13) {
            var text = $(this).val();
            var url = 'http://127.0.0.1:9502/?s=index/chart/index';
            var data = {'content': text, game_id: 1};
            $.post(url, data, function (data) {
                if (data.code == 1) {
                    layer.msg(data.msg);
                }
            }, 'json');
            $(this).val('');
        }
    })
});