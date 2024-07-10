var RyxState = function () {
	var _map = {};
	return {
		init: function (map) {
			_map = {};
			_map = map;
			if (_map.length > 0) {
				RyxState.updateAllValue();
			}
		},
		updateAllValue: function () {
			for (var i = 0; i < Object.keys(_map).length; i++) {
				if (!_map[Object.keys(_map)[i]].static_value) {
					_map[Object.keys(_map)[i]].value = $('#' + Object.values(_map)[i].id).val();
				}
			}
			return _map;
		},
		setValue: function (key, val) {
			_map[key].value = val;
		},
		setStateValue: function (key, objs) {
			for (var i = 0; i < Object.keys(objs).length; i++) {
				var k = Object.keys(objs)[i];
				var v = Object.values(objs)[i];
				_map[key][k] = v;
			}
		},
		currentState: function () {
			_map = RyxState.updateAllValue();
			return _map;
		}
	}
}();
