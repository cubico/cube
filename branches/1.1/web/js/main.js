	function getElementsByTagNames(list,obj)
	{
		if (!obj) var obj = document;
		var tagNames = list.split(',');
		var resultArray = new Array();
		for (var i=0;i<tagNames.length;i++) {
			var tags = obj.getElementsByTagName(tagNames[i]);
			for (var j=0;j<tags.length;j++) {
				resultArray.push(tags[j]);
			}
		}
		var testNode = resultArray[0];
		if (!testNode) return [];
		if (testNode.sourceIndex) {
			resultArray.sort(function (a,b) {
					return a.sourceIndex - b.sourceIndex;
			});
		}
		else if (testNode.compareDocumentPosition) {
			resultArray.sort(function (a,b) {
					return 3 - (a.compareDocumentPosition(b) & 6);
			});
		}
		return resultArray;
	}
	
	function _deleteValues(id)
	{
		var element = document.getElementById(id);
		var nodos = getElementsByTagNames('input,select,textarea,file,checkbox,radio',element);
		for(i in nodos)
		{
			if (nodos[i].type!='button' && nodos[i].type!='submit' && nodos[i].type!='reset')
				nodos[i].value='';
		}
	}
	
	//////// reescalado de iframe
	
	function getWindowData(n,i){
		var ifr=(document.getElementById(i).contentWindow.document || document.getElementById(i).contentDocument);
		var widthViewport,heightViewport,xScroll,yScroll,widthTotal,heightTotal;
		if (typeof window.frames[n].innerWidth != 'undefined'){
			widthViewport= window.frames[n].innerWidth;
			heightViewport= window.frames[n].innerHeight;
		}else if ((typeof ifr.documentElement != 'undefined') && typeof ifr.documentElement.clientWidth !='undefined' && ifr.documentElement.clientWidth != 0){
			widthViewport=ifr.documentElement.clientWidth;
			heightViewport=ifr.documentElement.clientHeight;
		}else{
			widthViewport= ifr.getElementsByTagName('body')[0].clientWidth;
			heightViewport=ifr.getElementsByTagName('body')[0].clientHeight;
		}
		xScroll=window.frames[n].pageXOffset || (ifr.documentElement.scrollLeft+ifr.body.scrollLeft);
		yScroll=window.frames[n].pageYOffset || (ifr.documentElement.scrollTop+ifr.body.scrollTop);
		widthTotal=Math.max(ifr.documentElement.scrollWidth,ifr.body.scrollWidth,widthViewport);
		heightTotal=Math.max(ifr.documentElement.scrollHeight,ifr.body.scrollHeight,heightViewport);
		return [widthViewport,heightViewport,xScroll,yScroll,widthTotal,heightTotal];
	}
	
	function resizeIframe(ID,NOMBRE){
		try {
			var m = getWindowData(NOMBRE, ID);
			
			document.getElementById(ID).height = null;
			document.getElementById(ID).width = null;
			
			document.getElementById(ID).height = m[5];
			document.getElementById(ID).width = m[4];
		}catch(err){}
	}
	
	/////////////////
	
	function utf8_decode ( str_data ) {
	    // Converts a UTF-8 encoded string to ISO-8859-1  
	    // 
	    // version: 909.322
	    // discuss at: http://phpjs.org/functions/utf8_decode
	    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	    // +      input by: Aman Gupta
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: Norman "zEh" Fuchs
	    // +   bugfixed by: hitwork
	    // +   bugfixed by: Onno Marsman
	    // +      input by: Brett Zamir (http://brett-zamir.me)
	    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // *     example 1: utf8_decode('Kevin van Zonneveld');
	    // *     returns 1: 'Kevin van Zonneveld'
	    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
	    
	    str_data += '';
	    
	    while ( i < str_data.length ) {
	        c1 = str_data.charCodeAt(i);
	        if (c1 < 128) {
	            tmp_arr[ac++] = String.fromCharCode(c1);
	            i++;
	        } else if ((c1 > 191) && (c1 < 224)) {
	            c2 = str_data.charCodeAt(i+1);
	            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
	            i += 2;
	        } else {
	            c2 = str_data.charCodeAt(i+1);
	            c3 = str_data.charCodeAt(i+2);
	            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
	            i += 3;
	        }
	    }
	
	    return tmp_arr.join('');
	}
	
	function utf8_encode ( argString ) {
	    // Encodes an ISO-8859-1 string to UTF-8  
	    // 
	    // version: 909.322
	    // discuss at: http://phpjs.org/functions/utf8_encode
	    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: sowberry
	    // +    tweaked by: Jack
	    // +   bugfixed by: Onno Marsman
	    // +   improved by: Yves Sucaet
	    // +   bugfixed by: Onno Marsman
	    // +   bugfixed by: Ulrich
	    // *     example 1: utf8_encode('Kevin van Zonneveld');
	    // *     returns 1: 'Kevin van Zonneveld'
	    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	
	    var utftext = "";
	    var start, end;
	    var stringl = 0;
	
	    start = end = 0;
	    stringl = string.length;
	    for (var n = 0; n < stringl; n++) {
	        var c1 = string.charCodeAt(n);
	        var enc = null;
	
	        if (c1 < 128) {
	            end++;
	        } else if (c1 > 127 && c1 < 2048) {
	            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
	        } else {
	            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
	        }
	        if (enc !== null) {
	            if (end > start) {
	                utftext += string.substring(start, end);
	            }
	            utftext += enc;
	            start = end = n+1;
	        }
	    }
	
	    if (end > start) {
	        utftext += string.substring(start, string.length);
	    }
	
	    return utftext;
	}

   function is_object (mixed_var) {
       // Returns true if variable is an object
       //
       // version: 1103.1210
       // discuss at: http://phpjs.org/functions/is_object    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
       // +   improved by: Legaev Andrey
       // +   improved by: Michael White (http://getsprink.com)
       // *     example 1: is_object('23');
       // *     returns 1: false    // *     example 2: is_object({foo: 'bar'});
       // *     returns 2: true
       // *     example 3: is_object(null);
       // *     returns 3: false
       if (mixed_var instanceof Array) {        return false;
       } else {
           return (mixed_var !== null) && (typeof(mixed_var) == 'object');
       }
   }

	function sprintf ( ) {
	    // http://kevin.vanzonneveld.net
	    // +   original by: Ash Searle (http://hexmen.com/blog/)
	    // + namespaced by: Michael White (http://getsprink.com)
	    // +    tweaked by: Jack
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +      input by: Paulo Ricardo F. Santos
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +      input by: Brett Zamir (http://brett-zamir.me)
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // *     example 1: sprintf("%01.2f", 123.1);
	    // *     returns 1: 123.10
	    // *     example 2: sprintf("[%10s]", 'monkey');
	    // *     returns 2: '[    monkey]'
	    // *     example 3: sprintf("[%'#10s]", 'monkey');
	    // *     returns 3: '[####monkey]'
	
	    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
	    var a = arguments, i = 0, format = a[i++];
	
	    // pad()
	    var pad = function (str, len, chr, leftJustify) {
	        if (!chr) {chr = ' ';}
	        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
	        return leftJustify ? str + padding : padding + str;
	    };
	
	    // justify()
	    var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
	        var diff = minWidth - value.length;
	        if (diff > 0) {
	            if (leftJustify || !zeroPad) {
	                value = pad(value, minWidth, customPadChar, leftJustify);
	            } else {
	                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
	            }
	        }
	        return value;
	    };
	
	    // formatBaseX()
	    var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
	        // Note: casts negative numbers to positive ones
	        var number = value >>> 0;
	        prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
	        value = prefix + pad(number.toString(base), precision || 0, '0', false);
	        return justify(value, prefix, leftJustify, minWidth, zeroPad);
	    };
	
	    // formatString()
	    var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
	        if (precision != null) {
	            value = value.slice(0, precision);
	        }
	        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
	    };
	
	    // doFormat()
	    var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
	        var number;
	        var prefix;
	        var method;
	        var textTransform;
	        var value;
	
	        if (substring == '%%') {return '%';}
	
	        // parse flags
	        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
	        var flagsl = flags.length;
	        for (var j = 0; flags && j < flagsl; j++) {
	            switch (flags.charAt(j)) {
	                case ' ': positivePrefix = ' '; break;
	                case '+': positivePrefix = '+'; break;
	                case '-': leftJustify = true; break;
	                case "'": customPadChar = flags.charAt(j+1); break;
	                case '0': zeroPad = true; break;
	                case '#': prefixBaseX = true; break;
	            }
	        }
	
	        // parameters may be null, undefined, empty-string or real valued
	        // we want to ignore null, undefined and empty-string values
	        if (!minWidth) {
	            minWidth = 0;
	        } else if (minWidth == '*') {
	            minWidth = +a[i++];
	        } else if (minWidth.charAt(0) == '*') {
	            minWidth = +a[minWidth.slice(1, -1)];
	        } else {
	            minWidth = +minWidth;
	        }
	
	        // Note: undocumented perl feature:
	        if (minWidth < 0) {
	            minWidth = -minWidth;
	            leftJustify = true;
	        }
	
	        if (!isFinite(minWidth)) {
	            throw new Error('sprintf: (minimum-)width must be finite');
	        }
	
	        if (!precision) {
	            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
	        } else if (precision == '*') {
	            precision = +a[i++];
	        } else if (precision.charAt(0) == '*') {
	            precision = +a[precision.slice(1, -1)];
	        } else {
	            precision = +precision;
	        }
	
	        // grab value using valueIndex if required?
	        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];
	
	        switch (type) {
	            case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
	            case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
	            case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
	            case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
	            case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
	            case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
	            case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
	            case 'i':
	            case 'd':
	                number = parseInt(+value, 10);
	                prefix = number < 0 ? '-' : positivePrefix;
	                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
	                return justify(value, prefix, leftJustify, minWidth, zeroPad);
	            case 'e':
	            case 'E':
	            case 'f':
	            case 'F':
	            case 'g':
	            case 'G':
	                number = +value;
	                prefix = number < 0 ? '-' : positivePrefix;
	                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
	                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
	                value = prefix + Math.abs(number)[method](precision);
	                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
	            default: return substring;
	        }
	    };
	
	    return format.replace(regex, doFormat);
	}
	
	function is_callable (v, syntax_only, callable_name) {
	    // Returns true if var is callable.  
	    // 
	    // version: 1004.2314
	    // discuss at: http://phpjs.org/functions/is_callable    // +   original by: Brett Zamir (http://brett-zamir.me)
	    // %        note 1: The variable callable_name cannot work as a string variable passed by reference as in PHP (since JavaScript does not support passing strings by reference), but instead will take the name of a global variable and set that instead
	    // %        note 2: When used on an object, depends on a constructor property being kept on the object prototype
	    // *     example 1: is_callable('is_callable');
	    // *     returns 1: true    // *     example 2: is_callable('bogusFunction', true);
	    // *     returns 2:true // gives true because does not do strict checking
	    // *     example 3: function SomeClass () {}
	    // *     example 3: SomeClass.prototype.someMethod = function (){};
	    // *     example 3: var testObj = new SomeClass();    // *     example 3: is_callable([testObj, 'someMethod'], true, 'myVar');
	    // *     example 3: alert(myVar); // 'SomeClass::someMethod'
	    var name='', obj={}, method='';
	    var getFuncName = function (fn) {
	        var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);        if (!name) {
	            return '(Anonymous)';
	        }
	        return name[1];
	    };    if (typeof v === 'string') {
	        obj = this.window;
	        method = v;
	        name = v;
	    }    else if (v instanceof Array && v.length === 2 && typeof v[0] === 'object' && typeof v[1] === 'string') {
	        obj = v[0];
	        method = v[1];
	        name = (obj.constructor && getFuncName(obj.constructor))+'::'+method;
	    }    else {
	        return false;
	    }
	    if (syntax_only || typeof obj[method] === 'function') {
	        if (callable_name) {            this.window[callable_name] = name;
	        }
	        return true;
	    }
	    return false;
	}
	
	function openSection(id){
		var target=$('#section'+id);
		var targetContent=target.find('div.collapsable_box');
		
		if (targetContent.css('display') == 'none') {
			targetContent.show('fast');
			target.find("span.toggle_box_contents").html(' - ');
		}else {
			targetContent.hide('fast');
			target.find("span.toggle_box_contents").html(' + ');
		}
		
	}
	
	function imprSelec(nombre)
	{
	  var ficha = document.getElementById(nombre);
	  var ventimp = window.open(' ', 'popimpr');
	  ventimp.document.write( ficha.innerHTML );
	  ventimp.document.close();
	  ventimp.print( );
	  ventimp.close();
	} 
	
	function str_pad (input, pad_length, pad_string, pad_type) {
	    // http://kevin.vanzonneveld.net
	    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // + namespaced by: Michael White (http://getsprink.com)
	    // +      input by: Marco van Oort
	    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	    // *     example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
	    // *     returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
	    // *     example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
	    // *     returns 2: '------Kevin van Zonneveld-----'
	
	    var half = '', pad_to_go;
	
	    var str_pad_repeater = function (s, len) {
	        var collect = '', i;
	
	        while (collect.length < len) {collect += s;}
	        collect = collect.substr(0,len);
	
	        return collect;
	    };
	
	    input += '';
	    pad_string = pad_string !== undefined ? pad_string : ' ';
	    
	    if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') { pad_type = 'STR_PAD_RIGHT'; }
	    if ((pad_to_go = pad_length - input.length) > 0) {
	        if (pad_type == 'STR_PAD_LEFT') { input = str_pad_repeater(pad_string, pad_to_go) + input; }
	        else if (pad_type == 'STR_PAD_RIGHT') { input = input + str_pad_repeater(pad_string, pad_to_go); }
	        else if (pad_type == 'STR_PAD_BOTH') {
	            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go/2));
	            input = half + input + half;
	            input = input.substr(0, pad_length);
	        }
	    }
	
	    return input;
	}
	