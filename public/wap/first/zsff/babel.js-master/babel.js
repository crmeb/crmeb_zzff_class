/*
 * String supplant
 * By Douglas Crockford
 * http://javascript.crockford.com/remedial.html
 */
if (!String.prototype.supplant) {
	String.prototype.supplant = function (o) {
		return this.replace(/{([^{}]*)}/g,
			function (a, b) {
				var r = o[b];
				return typeof r === 'string' || typeof r === 'number' ? r : a;
			}
		);
	};
}

var babel = (function (window, document) {

	var config = {
		lang: "en",
		dir: "lang",
		extension: "json"
	};

	var next;
	var loaded = {};

	var loadLanguage = function(lang) {
		var xhr = new XMLHttpRequest();
		xhr.open("GET", config.dir + "/" + lang + "." + config.extension, true);
		xhr.addEventListener("load", function(e) {
			loaded[lang] = JSON.parse(xhr.responseText);
			
			if (next == lang) {
				replace(lang);
			}
		}, false);
		xhr.addEventListener("error", function(e) {
			loaded[lang] = {};
		}, false);
		xhr.send();
	};
	
	var replace = function(lang) {
		Object.keys(loaded[lang]).forEach(function (key) {
			var node = document.getElementById(key);
			if (node) {
				node.innerHTML = loaded[lang][key].supplant(loaded[lang]);
			}
		});
	};
	
	var babel = {
		config : function(options) {
			Object.keys(options).forEach(function (key) {
				config[key] = options[key];
			});
		},
		
		activate: function(lang) {
			if (!loaded[lang]) {
				loadLanguage(lang);
				next = lang;
			} else {
				replace(lang);
			}
		}
	};
	
	// bootstrap
	document.addEventListener("DOMContentLoaded", function (e) {
		babel.activate(config.lang);
	});
	
	return babel;
	
})(window, document);