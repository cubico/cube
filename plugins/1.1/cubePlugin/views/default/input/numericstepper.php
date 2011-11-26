<?php
	if (!defined("INPUT_NUMERIC_STEPPER")){
	define("INPUT_NUMERIC_STEPPER",true);
?>
<script type="text/javascript">
/**
 * Numeric Stepper
 * ------------------------------------------------
 *
 * Copyright 2007 Ca Phun Ung
 *	
 * This software is licensed under the CC-GNU LGPL
 * http://creativecommons.org/licenses/LGPL/2.1/
 * 
 * Version 0.1
 *
 */

/**
 * Numeric Stepper Class.
 */
var NumericStepper = {	
	register : function(name, minValue, maxValue, stepSize){
		this.minValue = minValue;
		this.maxValue = maxValue;
		this.stepSize = stepSize;
		var elements = this.getElementsByClassName(document, "*", name);
		
		for (var i=0; i<elements.length; i++){
			var textbox = elements[i].getElementsByTagName('input')[0];
			if (textbox){
				if (textbox.value == undefined || textbox.value == '' || isNaN(textbox.value)) 
					textbox.value = 0;			
				textbox.onkeypress = function(e){
					if(window.event){
						keynum = e.keyCode; // IE
					} else if(e.which){
						keynum = e.which; // Netscape/Firefox/Opera
					}
					keychar = String.fromCharCode(keynum);
					numcheck = /[0-9\-]/;
					if (keynum==8)
						return true;
					else
						return numcheck.test(keychar);
				};
				textbox.onblur = function(){
					if (parseInt(this.value) < NumericStepper.minValue)
						this.value = NumericStepper.minValue;
					if (parseInt(this.value) >NumericStepper. maxValue)
						this.value = NumericStepper.maxValue;
				};
				var buttons = elements[i].getElementsByTagName('button');
				if (buttons[0]){
					this.addButtonEvent(buttons[0], textbox, this.stepUp);
				}
				if (buttons[1])
					this.addButtonEvent(buttons[1], textbox, this.stepDown);
			}
		}
	}	
  ,addButtonEvent:function(o,textbox, func){
    o.textbox = textbox;
		// convert button type to button to prevent form submission onclick
		if (o.getAttribute("type")=="submit"){
			o.removeAttribute("type"); // IE fix
			o.setAttribute("type","button");
		}
    o.onclick = func;
	}
  ,stepUp:function(){
    NumericStepper.stepper(this.textbox, NumericStepper.stepSize);
  }
  ,stepDown:function(){
    NumericStepper.stepper(this.textbox, -NumericStepper.stepSize);
  }
	,stepper:function(textbox, val){
    if (textbox == undefined) 
      return false;
    if (val == undefined || isNaN(val)) 
      val = 1;
    if (textbox.value == undefined || textbox.value == '' || isNaN(textbox.value)) 
      textbox.value = 0;
    textbox.value = parseInt(textbox.value) + parseInt(val);
    if (parseInt(textbox.value) < NumericStepper.minValue)
      textbox.value = NumericStepper.minValue;
    if (parseInt(textbox.value) >NumericStepper. maxValue)
      textbox.value = NumericStepper.maxValue;
  }
  ,getElementsByClassName:function (oElm, strTagName, strClassName){
	  var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	  var arrReturnElements = new Array();
	  strClassName = strClassName.replace(/-/g, "\-");
	  var oRegExp = new RegExp("(^|\s)" + strClassName + "(\s|$)");
	  var oElement;
	  for(var i=0; i<arrElements.length; i++){
	    oElement = arrElements[i];
	    if(oRegExp.test(oElement.className)){
	      arrReturnElements.push(oElement);
	    }
	  }
	  return (arrReturnElements)
  }
}


$(window).load(function() {
	var myNumericStepper=NumericStepper.register("numeric-stepper", 0, 100, 1);
});	
</script>
<?php
}
?>	
<span class="numeric-stepper" id="object<?php echo $vars['internalname']; ?>">
	<input type="text" name="<?php echo $vars['internalname']; ?>" value="<?php echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>" size="2" />
	<button type="button" name="<?php echo $vars['internalname']; ?>ns_button_1" value="1" class="plus">+</button>
	<button type="button" name="<?php echo $vars['internalname']; ?>ns_button_2" value="-1" class="minus">-</button>
</span>