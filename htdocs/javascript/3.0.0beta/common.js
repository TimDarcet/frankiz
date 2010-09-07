$(document).ready(function(){
    $.ajaxSetup({ cache: false });
});

function trueFormReset(target)
{
	$.each($(target).find("input[type=text]"), function(index, value) {
		$(value).val("");
	   });
}

function defaultCallback(json) {
    if (!json.done)
    {
        showError(json.message);
    }
};

function request(path, data, callbacks, stringify)
{
    if (!$.isEmptyObject(callbacks)) {
        if (callbacks.custom)
        {
            callback = callbacks.custom;
        } else {
            if (callbacks.success && callbacks.fail) {
                callback = function(json) { if (json.success) { callbacks.success(json); } else { callbacks.fail(json); } };
            } else if (callbacks.success) {
                callback = function(json) { if (json.success) { callbacks.success(json); } else { showError(json.error); } };
            } else if (callbacks.fail) {
                callback = function(json) { if (!json.success) { callbacks.fail(json); } };
            }
        }
    } else {
        callback = function(json) { if (!json.success) { showError(json.error); }};
    }

    if (stringify == undefined) stringify = true;
    $.getJSON(path, 'json='+((stringify)?JSON.stringify(data):data), callback);
}

$.fn.formToJSON = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function key_exists(key, search) {
    return key in search;
}

