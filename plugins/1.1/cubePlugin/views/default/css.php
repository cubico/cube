<?php /* Prueba */ ?>
/* ***************************************
	RESET BASE STYLES
*************************************** */
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-weight: inherit;
	font-style: inherit;
	/*font-size: 98%; */
	font-size: 0.98em;
	font-family: inherit;
	vertical-align: baseline;
}
div.deletefileuploaded{
	font-size: 1em;
	cursor:pointer;
}
/* remember to define focus styles! */
:focus {
	outline: 0;
}
ol, ul {
	list-style: none;
}
/* tables still need cellspacing="0" (for ie6) */
table {
	border-collapse: separate;
	border-spacing: 0;
}
caption, th, td {
	text-align: left;
	font-weight: normal;
	vertical-align: top;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: "";
}
blockquote, q {
	quotes: "" "";
}
.clearfloat { 
	clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;
}

.numeric-stepper {
	width:46px;
	height:22px;
	font-size:14px;
	position:relative;
	overflow:hidden;	
	background:#fff url(/img/bg_numeric-stepper.gif) no-repeat;
	display:block;
	float:left;
}

.numeric-stepper input {
	width:30px;
	height:100%;
	float:left;
	text-align:center;
	vertical-align:center;
	font-size:100%;
	border:none;
	background:none;
	padding:2px 0;
}

.numeric-stepper button {
	z-index:100;
	position:absolute;
	right:0;
	width:16px;
	height:10px;
	background:none;
	border:1px solid #000;
	padding:0;
	margin:0;
	filter:alpha(opacity=0);	/* IE */
	-moz-opacity:0;	/* Gecko */
	-khtml-opacity:0; /* Konqueror */
	opacity:0;	/* CSS2 */	
}

.numeric-stepper button.minus {
	bottom:0;
}
/* ***************************************
	DEFAULTS
*************************************** */

/* elgg open source		blue 			#4690d6 */
/* elgg open source		dark blue 		#0054a7 */
/* elgg open source		light yellow 	#FDFFC3 */
/* elgg open source		light blue	 	#bbdaf7 */


body {
	text-align:left;
	margin:0 auto;
	padding:0;
	background: #dedede;
	/*background:url("/img/body.png") repeat scroll 0 0 #4D4B4A;*/
	color:#222;
	font: 80%/1.4  "Lucida Grande", Verdana, sans-serif;
}

a {
	color: #4690d6;
	text-decoration: none;
	-moz-outline-style: none;
	outline: none;
}
a:visited {
	
}
a:hover {
	color: #0054a7;
	text-decoration: underline;
}
p {
	margin: 0px 0px 15px 0;
}
img {
	border: none;
}
ul {
	margin: 5px 0px 15px;
	padding-left: 20px;
}
ul li {
	margin: 0px;
}
ol {
	margin: 5px 0px 15px;
	padding-left: 20px;
}
ul li {
	margin: 0px;
}
form {
	margin: 0px;
	padding: 0px;
}
small {
	font-size: 90%;
}
h1, h2, h3, h4, h5, h6 {
	font-weight: bold;
	line-height: normal;
}
h1 { font-size: 1.8em; }
h2 { font-size: 1.5em; }
h3 { font-size: 1.2em; }
h4 { font-size: 1.0em; }
h5 { font-size: 0.9em; }
h6 { font-size: 0.8em; }

dt {
	margin: 0;
	padding: 0;
	font-weight: bold;
}
dd {
	margin: 0 0 1em 1em;
	padding: 0;
}
pre, code {
	font-family:Monaco,"Courier New",Courier,monospace;
	font-size:12px;
	background:#EBF5FF;
	overflow:auto;
}
code {
	padding:2px 3px;
}
pre {
	padding:3px 15px;
	margin:0px 0 15px 0;
	line-height:1.3em;
}
blockquote {
	padding:3px 15px;
	margin:0px 0 15px 0;
	line-height:1.3em;
	background:#EBF5FF;
	border:none !important;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
blockquote p {
	margin:0 0 5px 0;
}

/* ***************************************
    PAGE LAYOUT - MAIN STRUCTURE
*************************************** */
#page_container {
	margin:0;
	padding:0;
}
#page_wrapper {
	width:990px;
	margin:0 auto;
	padding:0;
	min-height: 300px;

}
#layout_header {
	text-align:left;
	width:100%;
	height:67px;
	background:#dedede;
}
#wrapper_header {
	margin:0;
	padding:10px 20px 20px 0px;
}
#wrapper_header h1 {
	margin:10px 0 0 0;
	letter-spacing: -0.03em;
}
#layout_canvas {
	margin:0 0 20px 0;
	padding:20px;
	min-height: 360px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background: white;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
}


/* canvas layout: 1 column, no sidebar */
#one_column {
/* 	width:928px; */
	margin:0;
	min-height: 360px;
	background: #dedede;
	padding:0 0 10px 0;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}

/* canvas layout: 2 column left sidebar */
#two_column_left_sidebar {
	width:210px;
	margin:0 20px 0 0;
	min-height:360px;
	float:left;
	background: #dedede;
	padding:0px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
}

#two_column_left_sidebar_maincontent {
	width:718px;
	margin:0;
	min-height: 360px;
	float:left;
	background: #dedede;
	padding:0 0 5px 0;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}




#two_column_left_sidebar_maincontent_boxes {
	margin:0 0px 20px 20px;
	padding:0 0 5px 0;
	width:718px;
	background: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	float:left;
}
#two_column_left_sidebar_boxes {
	width:210px;
	margin:0px 0 20px 0px;
	min-height:360px;
	float:left;
	padding:0;
}
#two_column_left_sidebar_boxes .sidebarBox {
	margin:0px 0 22px 0;
	background: #dedede;
	padding:4px 10px 10px 10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
}
#two_column_left_sidebar_boxes .sidebarBox h3 {
	padding:0 0 5px 0;
	font-size:1.25em;
	line-height:1.2em;
	color:#0054A7;
}

.contentWrapper {
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
    padding:10px;
    margin:0 10px 10px 10px;
}

.minicontentWrapper {
	padding: 0px;
    margin:0 0px 0px 12px;
}

span.contentIntro p {
	margin:0 0 0 0;
}
.notitle {
	margin-top:10px;
}

/* canvas layout: widgets (profile and dashboard) */
#widgets_left {
	width:303px;
	margin:0 20px 20px 0;
	min-height:360px;
	padding:0;
}
#widgets_middle {
	width:303px;
	margin:0 0 20px 0;
	padding:0;
}
#widgets_right {
	width:303px;
	margin:0px 0 20px 20px;
	float:left;
	padding:0;
}
#widget_table td {
	border:0;
	padding:0;
	margin:0;
	text-align: left;
	vertical-align: top;
}
/* IE6 fixes */
* html #widgets_right { float:none; }
* html #profile_info_column_left {
	margin:0 10px 0 0;
	width:200px;
}
* html #dashboard_info { width:585px; }
/* IE7 */
*:first-child+html #profile_info_column_left { width:200px; }


/* ***************************************
	SPOTLIGHT
*************************************** */
#layout_spotlight {
	margin:20px 0 20px 0;
	padding:0;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background: white;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
}
#wrapper_spotlight {
	margin:0;
	padding:0;
	height:auto;
}
#wrapper_spotlight #spotlight_table h2 {
	color:#4690d6;
	font-size:1.25em;
	line-height:1.2em;
}
#wrapper_spotlight #spotlight_table li {
	list-style: square;
	line-height: 1.2em;
	margin:5px 20px 5px 0;
	color:#4690d6;
}
#wrapper_spotlight .collapsable_box_content  {
	margin:0;
	padding:10px 10px 5px 10px;
	background:none;
	min-height:60px;
	border:none;
	border-top:1px solid #cccccc;
}
#spotlight_table {
	margin:0 0 2px 0;
}
#spotlight_table .spotlightRHS {
	float:right;
	width:270px;
	margin:0 0 0 50px;
}
/* IE7 */
*:first-child+html #wrapper_spotlight .collapsable_box_content {
	width:958px;
}
#layout_spotlight .collapsable_box_content p {
	padding:0;
}
#wrapper_spotlight .collapsable_box_header  {
	border: none;
	background: none;
}


/* ***************************************
	FOOTER
*************************************** */
#layout_footer {
	background: #b6b6b6;
	height:80px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	margin:0 0 20px 0;
}
#layout_footer table {
   margin:0 0 0 20px;
}
#layout_footer a, #layout_footer p {
   color:#333333;
   margin:0;
}
#layout_footer .footer_toolbar_links {
	text-align:right;
	padding:15px 0 0 0;
	font-size:1.2em;
}
#layout_footer .footer_legal_links {
	text-align:right;
}


/* ***************************************
  HORIZONTAL ELGG TOPBAR
*************************************** */
#elgg_topbar {
	background:#333333 url(/img/toptoolbar_background.gif) repeat-x top left;
	color:#eeeeee;
	border-bottom:1px solid #000000;
	position:relative;
	/* min-width:998px; */
	/*width:100%;*/
	width:990px;
	height:24px;
	z-index: 9000; /* if you have multiple position:relative elements, then IE sets up separate Z layer contexts for each one, which ignore each other */
}
#elgg_topbar_container_left {
	float:left;
	height:24px;
	left:0px;
	top:0px;
	position:absolute;
	text-align:left;
	width:60%;
}
#elgg_topbar_container_right {
	float:right;
	height:24px;
	position:absolute;
	right:0px;
	top:0px;
	/* width:120px;*/
	text-align:right;
}
#elgg_topbar_container_search {
	float:right;
	height:21px;
	/*width:280px;*/
	position:relative;
	right:120px;
	text-align:right;
	margin:3px 0 0 0;
}
#elgg_topbar_container_left .toolbarimages {
	float:left;
	margin-right:20px;
}
#elgg_topbar_container_left .toolbarlinks {
	margin:0 0 10px 0;
	float:left;
}
#elgg_topbar_container_left .toolbarlinks2 {
	margin:3px 0 0 0;
	float:left;
}
#elgg_topbar_container_left a.loggedinuser {
	color:#eeeeee;
	font-weight:bold;
	margin:0 0 0 5px;
}
#elgg_topbar_container_left a.pagelinks {
	color:white;
	margin:0 15px 0 5px;
	display:block;
	padding:3px;
}
#elgg_topbar_container_left a.pagelinks:hover {
	background: #4690d6;
	text-decoration: none;
}
#elgg_topbar_container_left a.privatemessages {
	background:transparent url(/img/toolbar_messages_icon.gif) no-repeat left 2px;
	padding:0 0 4px 16px;
	margin:0 15px 0 5px;
	cursor:pointer;
}
#elgg_topbar_container_left a.privatemessages:hover {
	text-decoration: none;
	background:transparent url(/img/toolbar_messages_icon.gif) no-repeat left -36px;
}
#elgg_topbar_container_left a.privatemessages_new {
	background:transparent url(/img/toolbar_messages_icon.gif) no-repeat left -17px;
	padding:0 0 2px 18px;
	margin:0 15px 0 5px;
	color:white;
}
/* IE6 */
* html #elgg_topbar_container_left a.privatemessages_new { background-position: left -18px; } 
/* IE7 */
*+html #elgg_topbar_container_left a.privatemessages_new { background-position: left -18px; } 

#elgg_topbar_container_left a.privatemessages_new:hover {
	text-decoration: none;
}

#elgg_topbar_container_left a.usersettings {
	margin:0 0 0 20px;
	color:#999999;
	padding:3px;
}
#elgg_topbar_container_left a.usersettings:hover {
	color:#eeeeee;
}
#elgg_topbar_container_left img {
	margin:0 0 0 5px;
}
#elgg_topbar_container_left .user_mini_avatar {
	border:1px solid #eeeeee;
	margin:0 0 0 20px;
}
#elgg_topbar_container_right {
	padding:3px 0 0 0;
}
#elgg_topbar_container_right a.logout {
	color:#eeeeee;
	margin:0 5px 0 0;
	background:transparent url(/img/elgg_toolbar_logout.gif) no-repeat top right;
	padding:0 21px 0 0;
	display:block;
	height:20px;
}

#elgg_topbar_container_right a.login {
	color:#eeeeee;
	margin:0 5px 0 0;
	background:transparent url(/img/elgg_toolbar_login.gif) no-repeat top right;
	padding:0 21px 0 0;
	display:block;
	height:20px;
}

/* IE6 fix */
* html #elgg_topbar_container_right a { 
	width: 120px;
}
#elgg_topbar_container_right a:hover {
	background-position: right -21px;
}
#elgg_topbar_panel {
	background:#333333;
	color:#eeeeee;
	height:200px;
	width:100%;
	padding:10px 20px 10px 20px;
	display:none;
	position:relative;
}
#searchform input.search_input {
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	background-color:#FFFFFF;
	border:1px solid #BBBBBB;
	color:#999999;
	font-size:12px;
	font-weight:bold;
	margin:0pt;
	padding:2px;
	width:180px;
	height:12px;
}
#searchform input.search_submit_button {
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	color:#333333;
	background: #cccccc;
	border:none;
	font-size:12px;
	font-weight:bold;
	margin:0px;
	padding:2px;
	width:auto;
	height:18px;
	cursor:pointer;
}
#searchform input.search_submit_button:hover {
	color:#ffffff;
	background: #4690d6;
}


/* ***************************************
	TOP BAR - VERTICAL TOOLS MENU
*************************************** */
/* elgg toolbar menu setup */
ul.topbardropdownmenu, ul.topbardropdownmenu ul {
	margin:0;
	padding:0;
	display:inline;
	float:left;
	list-style-type: none;
	z-index: 9000;
	position: relative;
}
ul.topbardropdownmenu {
	margin:0pt 20px 0pt 5px;
}
ul.topbardropdownmenu li { 
	display: block;
	list-style: none;
	margin: 0;
	padding: 0;
	float: left;
	position: relative;
}
ul.topbardropdownmenu a {
	display:block;
}
ul.topbardropdownmenu ul {
	display: none;
	position: absolute;
	left: 0;
	margin: 0;
	padding: 0;
}
/* IE6 fix */
* html ul.topbardropdownmenu ul {
	line-height: 1.1em;
}
/* IE6/7 fix */
ul.topbardropdownmenu ul a {
	zoom: 1;
} 
ul.topbardropdownmenu ul li {
	float: none;
}   
/* elgg toolbar menu style */
ul.topbardropdownmenu ul {
	width: 300px;
	top: 24px;
	border-top:1px solid black;
}
ul.topbardropdownmenu *:hover {
	background-color: none;
}
ul.topbardropdownmenu a {
	padding:3px;
	text-decoration:none;
	color:white;
}
ul.topbardropdownmenu li.hover a {
	background-color: #4690d6;
	text-decoration: none;
}
ul.topbardropdownmenu ul li.drop a {
	font-weight: normal;
}
/* IE7 fixes */
*:first-child+html #elgg_topbar_container_left a.pagelinks {

}
*:first-child+html ul.topbardropdownmenu li.drop a.menuitemtools {
	padding-bottom:6px;
}
ul.topbardropdownmenu ul li a {
	background-color: #999999;/* menu off state color */
	font-weight: bold;
	padding-left:6px;
	padding-top:4px;
	padding-bottom:0;
	height:22px;
	border-bottom: 1px solid white;
}
ul.topbardropdownmenu ul a.hover {
	background-color: #333333;
}
ul.topbardropdownmenu ul a {
	opacity: 0.9;
	filter: alpha(opacity=90);
}


/* ***************************************
  SYSTEM MESSSAGES
*************************************** */
.messages {
    background:#ccffcc;
    color:#000000;
    padding:3px 10px 3px 10px;
    z-index: 8000;
	margin:0;
	position:fixed;
	top:30px;
	width:969px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	border:4px solid #00CC00;
	cursor: pointer;
}
.messages_error {
    border:4px solid #D3322A;
    background:#F7DAD8;
    color:#000000;
    padding:3px 10px 3px 10px;
    z-index: 8000;
	margin:0;
	position:fixed;
	top:30px;
	width:969px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	cursor: pointer;
}
.closeMessages {
	float:right;
	margin-top:17px;
}
.closeMessages a {
	color:#666666;
	cursor: pointer;
	text-decoration: none;
	font-size: 80%;
}
.closeMessages a:hover {
	color:black;
}


/* ***************************************
  COLLAPSABLE BOXES
*************************************** */
.collapsable_box {
	margin: 0 0 20px 0;
	height:auto;

}
/* IE6 fix */
* html .collapsable_box  { 
	height:10px;
}
.collapsable_box_header {
	color: #4690d6;
	padding: 5px 10px 5px 10px;
	margin:0;
	border-left: 1px solid white;
	border-right: 1px solid #cccccc;
	border-bottom: 1px solid #cccccc;
	-moz-border-radius-topleft:8px;
	-moz-border-radius-topright:8px; 
	-webkit-border-top-right-radius:8px;
	-webkit-border-top-left-radius:8px;
	background:#dedede;
}
.collapsable_box_header h1 {
	color: #0054a7;
	font-size:1.25em;
	line-height: 1.2em;
}
.collapsable_box_content {
	padding: 10px 0 10px 0;
	margin:0;
	height:auto;
	background:#dedede;
	-moz-border-radius-bottomleft:8px;
	-moz-border-radius-bottomright:8px;
	-webkit-border-bottom-right-radius:8px;
	-webkit-border-bottom-left-radius:8px;
	border-left: 1px solid white;
	border-right: 1px solid #cccccc;
	border-bottom: 1px solid #cccccc;
}
.collapsable_box_content .contentWrapper {
	margin-bottom:5px;
}
.collapsable_box_editpanel {
	display: none;
	background: #a8a8a8;
	padding:10px 10px 5px 10px;
	border-left: 1px solid white;
	border-bottom: 1px solid white;
}
.collapsable_box_editpanel p {
	margin:0 0 5px 0;
}
.collapsable_box_header a.toggle_box_contents {
	color: #4690d6;
	cursor:pointer;
	font-family: Arial, Helvetica, sans-serif;
	font-size:20px;
	font-weight: bold;
	text-decoration:none;
	float:right;
	margin: 0;
	margin-top: -7px;
}
.collapsable_box_header a.toggle_box_edit_panel {
	color: #4690d6;
	cursor:pointer;
	font-size:9px;
	text-transform: uppercase;
	text-decoration:none;
	font-weight: normal;
	float:right;
	margin: 3px 10px 0 0;
}
.collapsable_box_editpanel label {
	font-weight: normal;
	font-size: 100%;
}
/* used for collapsing a content box */
.display_none {
	display:none;
}
/* used on spotlight box - to cancel default box margin */
.no_space_after {
	margin: 0 0 0 0;
}



/* ***************************************
	GENERAL FORM ELEMENTS
*************************************** */
label {
	font-weight: bold;
	color:#333333;
	font-size: 100%;
}

table label {
	font-weight: bold;
	color:#333333;
	font-size: 110%;
}
table.formtable tr td{
	vertical-align: middle;
	padding:0px;
}

table.formparagraph, p.formparagraph{
	vertical-align: middle;
	margin: 0 0 5px 0;
	font-size: 1em;
	display: block;
}

table.formparagraph_inline{
	vertical-align: middle;
	margin: 0 0 5px 0;
	font-size: 1em;
	display: inline;
}

td.element_list_actions{
	/*background: #875;*/
	margin: 0px;
	padding: 0px;
}

td.element_list_actions table, td.element_list_actions table tr td{
	border: 0px;
	padding: 0px;
	margin: 0px;
} 
/*
td.element_list_actions a{
	
}
*/
th.list_header{
	background:#C2DFEF;
	padding:3px;
	font-weight: bold;
	color: #05354F;
	border-right: 1px solid #DFF4FF; 
}

th.list_header a{
	color: #05354F;
}

input {
	font: 120% Arial, Helvetica, sans-serif;
	padding: 5px;
	/*border: 1px solid #cccccc;*/
	color:#666666;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}

input.input-text{
    border: 1px solid #cccccc;
}

input.input-text-gris{
	background-color: #E4E4E4
}

.calendar-input-format{
	position:absolute;
	float:left;
	display:inline;
	margin: 0;
	font-size: 1em;
	padding: 6px;
	cursor:pointer;
}

.radio-button{
	font-size: 1em;
}

.required {
	border: 1px solid #EF5959;
}

textarea {
	font: 120% Arial, Helvetica, sans-serif;
	border: solid 1px #cccccc;
	padding: 5px;
	color:#666666;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
textarea:focus, input[type="text"]:focus {
	border: solid 1px #4690d6;
	background: #e4ecf5;
	color:#333333;
}
.submit_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#4690d6;
	border: 1px solid #4690d6;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 0;
	cursor: pointer;
}
.submit_button:hover, input[type="submit"]:hover {
	background: #0054a7;
	border-color: #0054a7;
}

input[type="submit"] {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#4690d6;
	border: 1px solid #4690d6;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 0;
	cursor: pointer;
}

.red_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #fff;
	background:#DF4545;
	border: 1px solid #EF0000;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 10px;
	cursor: pointer;
}

.red_button:hover {
	background: #AF2B2B;
}

.green_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #fff;
	background:#6DAF70;
	border: 1px solid #279F2C;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 0px;
	cursor: pointer;
}

.green_button:hover {
	background: #508F53;
}

.orange_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #fff;
	background:#CF7000;
	border: 1px solid #AF5E00;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 0px;
	cursor: pointer;
}

.orange_button:hover {
	background: #AF5E00;
}

.cancel_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #999999;
	background:#dddddd;
	border: 1px solid #999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:10px 0 10px 10px;
	cursor: pointer;
}
.cancel_button:hover {
	background: #cccccc;
}

.input-text,
.input-tags,
.input-url,
.input-textarea {
	width:98%;
}

.input-textarea {
	height: 200px;
}

.input-textarea-gris {
	background-color: #E4E4E4
}

.missatge{
	padding: 0px;
	margin: 5px;
	border-bottom: 1px solid #000;
	width: 99%;
	color: #000;
	font-size: 1.35em;
	font-weight: bold;
}

.missatge span.data{
	font-size: 0.8em;
	float: right;
	color: #555;
	text-align:right;
	margin: 0 5px 0 0;
}

.missatge div.description{
	color: #666;
	background:#fff;
	font-size: 0.8em;
	padding: 5px;
	font-weight: normal;
	clear:both;
	display:none;
}

.missatge div.ERR{
	background:transparent url(/img/icon/exclamation.png) no-repeat 0 0;
	background-position:22px;
	cursor:pointer;
	width: 100%;
	height: 20px;
}

.missatge div.INF{
	background:transparent url(/img/icon/information.png) no-repeat 0 0;
	background-position:22px;
	cursor:pointer;
	width: 100%;
	height: 20px;
}

.missatge div.BUG{
	background:transparent url(/img/icon/bug.png) no-repeat 0 0;
	background-position:22px;
	cursor:pointer;
	width: 100%;
	height: 20px;
}

.missatge div.SUG{
	background:transparent url(/img/icon/light_bulb.png) no-repeat 0 0;
	background-position:22px;
	cursor:pointer;
	width: 100%;
	height: 20px;
}

.missatge div.ACT{
	background:transparent url(/img/icon/flag.png) no-repeat 0 0;
	background-position: 22px;
	cursor:pointer;
	width: 100%;
	height: 20px;
}	

.missatgetypes span.ERR{
	background:transparent url(/img/icon/exclamation.png) no-repeat scroll 0 0;
	float: left;
	background-position:left 6px;
	
}

.missatgetypes span.INF{
	background:transparent url(/img/icon/information.png) no-repeat scroll 0 0;
	float: left;
	background-position:left 6px;
	
}

.missatgetypes span.BUG{
	background:transparent url(/img/icon/bug.png) no-repeat scroll 0 0;
	float: left;
	background-position: left 6px;
}

.missatgetypes span.SUG{
	background:transparent url(/img/icon/light_bulb.png) no-repeat scroll 0 0;
	float: left;
	background-position: left 6px;
}

.missatgetypes span.ACT{
	background:transparent url(/img/icon/flag.png) no-repeat 0 0;
	float: left;
	background-position: left 6px;
}
/* ***************************************
	LOGIN / REGISTER
*************************************** */
#login-box {
	margin:0 0 10px 0;
	padding:0 0 10px 0;
	background: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	width:240px;
    text-align:left;
}
#login-box form {
	margin:0 10px 0 10px;
	padding:0 10px 4px 10px;
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	width:200px;
}
#login-box h2 {
	color:#0054A7;
	font-size:1.35em;
	line-height:1.2em;
	margin:0 0 0 8px;
	padding:5px 5px 0 5px;
}
#login-box .login-textarea {
	width:178px;
}
#login-box label,
#register-box label {
	font-size: 1.2em;
	color:gray;
}
#login-box p.loginbox {
	margin:0;
}
#login-box input[type="text"],
#login-box input[type="password"],
#register-box input[type="text"],
#register-box input[type="password"] {
	margin:0 0 10px 0;
}
#register-box input[type="text"],
#register-box input[type="password"] {
	width:380px;
}
#login-box h2,
#login-box-openid h2,
#register-box h2,
#add-box h2,
#forgotten_box h2 {
	color:#0054A7;
	font-size:1.35em;
	line-height:1.2em;
	margin:0pt 0pt 5px;
}
#register-box {
    text-align:left;
    width:400px;
    padding:10px;
    background: #dedede;
    margin:0;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#persistent_login label {
	font-size:1.0em;
	font-weight: normal;
}
/* login and openID boxes when not running custom_index mod */
#two_column_left_sidebar #login-box {
	width:auto;
	background: none;
}
#two_column_left_sidebar #login-box form {
	width:auto;
	margin:10px 10px 0 10px;
	padding:5px 0 5px 10px;
}
#two_column_left_sidebar #login-box h2 {
	margin:0 0 0 5px;
	padding:5px 5px 0 5px;
}
#two_column_left_sidebar #login-box .login-textarea {
	width:158px;
}


/* ***************************************
	PROFILE
*************************************** */
#profile_info {
	margin:0 0 20px 0;
	padding:20px;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
	background: #e9e9e9;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#profile_info_column_left {
	float:left;
	padding: 0;
	margin:0 20px 0 0;
}
#profile_info_column_middle {
	float:left;
	width:365px;
	padding: 0;
}
#profile_info_column_right {
	width:578px;
	margin:0 0 0 0;
	background:#dedede;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	padding:4px;
}
#dashboard_info {
	margin:0px 0px 0 0px;
	padding:20px;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
	background: #bbdaf7;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#profile_menu_wrapper {
	margin:10px 0 10px 0;
	width:200px;
}
#profile_menu_wrapper p {
	border-bottom:1px solid #cccccc;
}
#profile_menu_wrapper p:first-child {
	border-top:1px solid #cccccc;
}
#profile_menu_wrapper a {
	display:block;
	padding:0 0 0 3px;
}
#profile_menu_wrapper a:hover {
	color:#ffffff;
	background:#4690d6;
	text-decoration:none;
}
p.user_menu_friends, p.user_menu_profile, 
p.user_menu_removefriend, 
p.user_menu_friends_of {
	margin:0;
}
#profile_menu_wrapper .user_menu_admin {
	border-top:none;
}

#profile_info_column_middle p {
	margin:7px 0 7px 0;
	padding:2px 4px 2px 4px;
}
/* profile owner name */
#profile_info_column_middle h2 {
	padding:0 0 14px 0;
	margin:0;
}
#profile_info_column_middle .profile_status {
	background:#bbdaf7;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	padding:2px 4px 2px 4px;
	line-height:1.2em;
}
#profile_info_column_middle .profile_status span {
	display:block;
	font-size:90%;
	color:#666666;	
}
#profile_info_column_middle a.status_update {
	float:right;	
}
#profile_info_column_middle .odd {
	background:#EFEFEF;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
#profile_info_column_middle .even {
	background:#dedede;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
#profile_info_column_right p {
	margin:0 0 7px 0;
}
#profile_info_column_right .profile_aboutme_title {
	margin:0;
	padding:0;
	line-height:1em;
}
/* edit profile button */
.profile_info_edit_buttons {
	float:right;
	margin:0  !important;
	padding:0 !important;
}
.profile_info_edit_buttons a {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#4690d6;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 2px 6px 2px 6px;
	margin:0;
	cursor: pointer;
}
.profile_info_edit_buttons a:hover {
	background: #0054a7;
	text-decoration: none;
	color:white;
}


/* ***************************************
	RIVER
*************************************** */
#river,
.river_item_list {
	border-top:1px solid #dddddd;
}
.river_item p {
	margin:0;
	padding:0 0 0 21px;
	line-height:1.1em;
	min-height:17px;
}
.river_item {
	border-bottom:1px solid #dddddd;
	padding:2px 0 2px 0;
}
.river_item_time {
	font-size:90%;
	color:#666666;
}
/* IE6 fix */
* html .river_item p { 
	padding:3px 0 3px 20px;
}
/* IE7 */
*:first-child+html .river_item p {
	min-height:17px;
}
.river_user_update {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_user_profileupdate {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_user_profileiconupdate {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_annotate {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_bookmarks_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_bookmarks.gif) no-repeat left -1px;
}
.river_object_bookmarks_comment {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_status_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_status.gif) no-repeat left -1px;
}
.river_object_file_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_files.gif) no-repeat left -1px;
}
.river_object_file_update {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_files.gif) no-repeat left -1px;
}
.river_object_file_comment {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_widget_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_plugin.gif) no-repeat left -1px;
}
.river_object_forums_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_forums_update {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_widget_update {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_plugin.gif) no-repeat left -1px;	
}
.river_object_blog_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;
}
.river_object_blog_update {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;
}
.river_object_blog_comment {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_forumtopic_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_user_friend {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_friends.gif) no-repeat left -1px;
}
.river_object_relationship_friend_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_friends.gif) no-repeat left -1px;
}
.river_object_relationship_member_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_thewire_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_thewire.gif) no-repeat left -1px;
}
.river_group_join {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_groupforumtopic_annotate {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_groupforumtopic_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_sitemessage_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;	
}
.river_user_messageboard {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;	
}
.river_object_page_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_pages.gif) no-repeat left -1px;
}
.river_object_page_top_create {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_pages.gif) no-repeat left -1px;
}
.river_object_page_top_comment {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_page_comment {
	background: url(http://elgg.mine.nu/_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}

/* ***************************************
	SEARCH LISTINGS	
*************************************** */
.search_listing {
	display: block;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
	margin:0 10px 5px 10px;
	padding:5px;
}
.search_listing_icon {
	float:left;
}
.search_listing_icon img {
	width: 40px;
}
.search_listing_icon .avatar_menu_button img {
	width: 15px;
}
.search_listing_info {
	margin-left: 50px;
	min-height: 40px;
}
/* IE 6 fix */
* html .search_listing_info {
	height:40px;
}
.search_listing_info p {
	margin:0 0 3px 0;
	line-height:1.2em;
}
.search_listing_info p.owner_timestamp {
	margin:0;
	padding:0;
	color:#666666;
	font-size: 90%;
}
table.search_gallery {
	border-spacing: 10px;
	margin:0 0 0 0;
}
.search_gallery td {
	padding: 5px;
}
.search_gallery_item {
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	width:170px;
}
.search_gallery_item:hover {
	background: black;
	color:white;
}
.search_gallery_item .search_listing {
	background: none;
	text-align: center;
}
.search_gallery_item .search_listing_header {
	text-align: center;
}
.search_gallery_item .search_listing_icon {
	position: relative;
	text-align: center;
}
.search_gallery_item .search_listing_info {
	margin: 5px;
}
.search_gallery_item .search_listing_info p {
	margin: 5px;
	margin-bottom: 10px;
}
.search_gallery_item .search_listing {
	background: none;
	text-align: center;
}
.search_gallery_item .search_listing_icon {
	position: absolute;
	margin-bottom: 20px;
}
.search_gallery_item .search_listing_info {
	margin: 5px;
}
.search_gallery_item .search_listing_info p {
	margin: 5px;
	margin-bottom: 10px;
}


/* ***************************************
	FRIENDS
*************************************** */
/* friends widget */
#widget_friends_list {
	display:table;
	width:275px;
	margin:0 10px 0 10px;
	padding:8px 0 4px 8px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
}
.widget_friends_singlefriend {
	float:left;
	margin:0 5px 5px 0;
}


/* ***************************************
	ADMIN AREA - PLUGIN SETTINGS
*************************************** */
.plugin_details {
	margin:0 10px 5px 10px;
	padding:0 7px 4px 10px;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
.admin_plugin_reorder {
	float:right;
	width:200px;
	text-align: right;
}
.admin_plugin_reorder a {
	padding-left:10px;
	font-size:80%;
	color:#999999;
}
.plugin_details a.pluginsettings_link {
	cursor:pointer;
	font-size:80%;
}
.active {
	border:1px solid #999999;
    background:white;
}
.not-active {
    border:1px solid #999999;
    background:#dedede;
}
.plugin_details p {
	margin:0;
	padding:0;
}
.plugin_details a.manifest_details {
	cursor:pointer;
	font-size:80%;
}
.manifest_file {
	background:#dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	padding:5px 10px 5px 10px;
	margin:4px 0 4px 0;
	display:none;
}
.admin_plugin_enable_disable {
	width:150px;
	margin:10px 0 0 0;
	float:right;
	text-align: right;
}
.contentIntro .enableallplugins,
.contentIntro .disableallplugins {
	float:right;
}
.contentIntro .enableallplugins {
	margin-left:10px;
}
.contentIntro .enableallplugins, 
.not-active .admin_plugin_enable_disable a {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#4690d6;
	border: 1px solid #4690d6;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 4px;
	cursor: pointer;
}
.contentIntro .enableallplugins:hover, 
.not-active .admin_plugin_enable_disable a:hover {
	background: #0054a7;
	border: 1px solid #0054a7;
	text-decoration: none;
}
.contentIntro .disableallplugins, 
.active .admin_plugin_enable_disable a {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#999999;
	border: 1px solid #999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 4px;
	cursor: pointer;
}
.contentIntro .disableallplugins:hover, 
.active .admin_plugin_enable_disable a:hover {
	background: #333333;
	border: 1px solid #333333;
	text-decoration: none;
}
.pluginsettings {
	margin:15px 0 5px 0;
	background:#bbdaf7;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	padding:10px;
	display:none;
}
.pluginsettings h3 {
	padding:0 0 5px 0;
	margin:0 0 5px 0;
	border-bottom:1px solid #999999;
}
#updateclient_settings h3 {
	padding:0;
	margin:0;
	border:none;
}
.input-access {
	margin:5px 0 0 0;
}

/* ***************************************
	GENERIC COMMENTS
*************************************** */
.generic_comment_owner {
	font-size: 90%;
	color:#666666;
}
.generic_comment {
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
    padding:10px;
    margin:0 10px 10px 10px;
}
.generic_comment_icon {
	float:left;
}
.generic_comment_details {
	margin-left: 60px;
}
.generic_comment_details p {
	margin: 0 0 5px 0;
}
.generic_comment_owner {
	color:#666666;
	margin: 0px;
	font-size:90%;
	border-top: 1px solid #aaaaaa;
}
/* IE6 */
* html #generic_comment_tbl { width:676px !important;}

	
/* ***************************************
  PAGE-OWNER BLOCK
*************************************** */
#owner_block {
	padding:10px;
}
#owner_block_icon {
	float:left;
	margin:0 10px 0 0;
}
#owner_block_rss_feed,
#owner_block_odd_feed,
#owner_block_bookmark_this,
#owner_block_report_this {
	padding:5px 0 0 0;
}
#owner_block_report_this {
	padding-bottom:5px;
	border-bottom:1px solid #cccccc;
}
#owner_block_rss_feed a {
	font-size: 90%;
	color:#999999;
	padding:0 0 4px 20px;
	background: url(http://elgg.mine.nu/_graphics/icon_rss.gif) no-repeat left top;
}
#owner_block_odd_feed a {
	font-size: 90%;
	color:#EFEFEF;
	padding:0 0 4px 20px;
	background: url(http://elgg.mine.nu/_graphics/icon_odd.gif) no-repeat left top;
}
#owner_block_bookmark_this a {
	font-size: 90%;
	color:#999999;
	padding:0 0 4px 20px;
	background: url(http://elgg.mine.nu/_graphics/icon_bookmarkthis.gif) no-repeat left top;
}
#owner_block_report_this a {
	font-size: 90%;
	color:#999999;
	padding:0 0 4px 20px;
	background: url(http://elgg.mine.nu/_graphics/icon_reportthis.gif) no-repeat left top;
}
#owner_block_rss_feed a:hover,
#owner_block_odd_feed a:hover,
#owner_block_bookmark_this a:hover,
#owner_block_report_this a:hover {
	color: #0054a7;
}
#owner_block_desc {
	padding:4px 0 4px 0;
	margin:0 0 0 0;
	line-height: 1.2em;
	border-bottom:1px solid #cccccc;
	color:#666666;
}
#owner_block_content {
	margin:0 0 4px 0;
	padding:3px 0 0 0;
	min-height:35px;
	font-weight: bold;
}
#owner_block_content a {
	line-height: 1em;
}
.ownerblockline {
	padding:0;
	margin:0;
	border-bottom:1px solid #cccccc;
	height:1px;
}
#owner_block_submenu {
	margin:20px 0 20px 0;
	padding: 0;
	width:100%;
}
#owner_block_submenu ul {
	list-style: none;
	padding: 0;
	margin: 0;
}
#owner_block_submenu ul li.selected a {
	background: #4690d6;
	color:white;
}
#owner_block_submenu ul li.selected a:hover {
	background: #4690d6;
	color:white;
}
#owner_block_submenu ul li a {
	text-decoration: none;
	display: block;
	margin: 2px 0 0 0;
	color:#4690d6;
	padding:4px 6px 4px 10px;
	font-weight: bold;
	line-height: 1.1em;
	-webkit-border-radius: 10px; 
	-moz-border-radius: 10px;
}
#owner_block_submenu ul li a:hover {
	color:white;
	background: #0054a7;
}

/* IE 6 + 7 menu arrow position fix */
* html #owner_block_submenu ul li.selected a {
	background-position: left 10px;
}
*:first-child+html #owner_block_submenu ul li.selected a {
	background-position: left 8px;
}

#owner_block_submenu .submenu_group {
	border-bottom: 1px solid #cccccc;
	margin:10px 0 0 0;
	padding-bottom: 10px;
}

#owner_block_submenu .submenu_group .submenu_group_filter ul li a,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li a {
	color:#666666;
}
#owner_block_submenu .submenu_group .submenu_group_filter ul li.selected a,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li.selected a {
	background:#999999;
	color:white;
}
#owner_block_submenu .submenu_group .submenu_group_filter ul li a:hover,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li a:hover {
	color:white;
	background: #999999;
}


/* ***************************************
	PAGINATION
*************************************** */
.pagination {
	/*-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
	margin:5px 10px 5px 10px;
	padding:5px;*/
	margin: 5px 10px 5px 10px;
	padding: 0px;
	height: 20px;
}
.pagination .pagination_number {
	display:block;
	float:left;
	background:#ffffff;
	border:1px solid #4690d6;
	text-align: center;
	color:#4690d6;
	font-size: 12px;
	font-weight: normal;
	margin:0 6px 0 0;
	padding:0px 4px;
	cursor: pointer;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.pagination .pagination_number:hover {
	background:#4690d6;
	color:white;
	text-decoration: none;
}
.pagination .pagination_more {
	display:block;
	float:left;
	background:#ffffff;
	border:1px solid #ffffff;
	text-align: center;
	color:#4690d6;
	font-size: 12px;
	font-weight: normal;
	margin:0 6px 0 0;
	padding:0px 4px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.pagination .pagination_previous,
.pagination .pagination_next {
	display:block;
	float:left;
	border:1px solid #4690d6;
	color:#4690d6;
	text-align: center;
	font-size: 12px;
	font-weight: normal;
	margin:0 6px 0 0;
	padding:0px 4px;
	cursor: pointer;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.pagination .pagination_previous:hover,
.pagination .pagination_next:hover {
	background:#4690d6;
	color:white;
	text-decoration: none;
}
.pagination .pagination_currentpage {
	display:block;
	float:left;
	background:#4690d6;
	border:1px solid #4690d6;
	text-align: center;
	color:white;
	font-size: 12px;
	font-weight: bold;
	margin:0 6px 0 0;
	padding:0px 4px;
	cursor: pointer;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}

.pagination .sorting{
	display:block;
	float:left;
	border:1px solid #508F53;
	color:#508F53;
	text-align: center;
	font-size: 12px;
	font-weight: normal;
	margin:0 6px 0 0;
	padding:0px 4px;
	cursor: pointer;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.pagination .sorting:hover{
	background:#508F53;
	color:white;
	text-decoration: none;
}
	
/* ***************************************
	FRIENDS COLLECTIONS ACCORDIAN
*************************************** */	
ul#friends_collections_accordian {
	margin: 0 0 0 0;
	padding: 0;
}
#friends_collections_accordian li {
	margin: 0 0 0 0;
	padding: 0;
	list-style-type: none;
	color: #666666;
}
#friends_collections_accordian li h2 {
	background:#4690d6;
	color: white;
	padding:4px 2px 4px 6px;
	margin:10px 0 10px 0;
	font-size:1.2em;
	cursor:pointer;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#friends_collections_accordian li h2:hover {
	background:#333333;
	color:white;
}
#friends_collections_accordian .friends_picker {
	background:white;
	padding:0;
	display:none;
}
#friends_collections_accordian .friends_collections_controls {
	font-size:70%;
	float:right;
}
#friends_collections_accordian .friends_collections_controls a {
	color:#999999;
	font-weight:normal;
}
	
	
/* ***************************************
	FRIENDS PICKER SLIDER
*************************************** */		
.friendsPicker_container h3 {
	font-size:4em !important;
	text-align: left;
	margin:0 0 10px 0 !important;
	color:#999999 !important;
	background: none !important;
	padding:0 !important;
}
.friendsPicker .friendsPicker_container .panel ul {
	text-align: left;
	margin: 0;
	padding:0;
}
.friendsPicker_wrapper {
	margin: 0;
	padding:0;
	position: relative;
	width: 100%;
}
.friendsPicker {
	position: relative;
	overflow: hidden; 
	margin: 0;
	padding:0;
	width: 678px;
	
	height: auto;
	background: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
.friendspicker_savebuttons {
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	margin:0 10px 10px 10px;
}
.friendsPicker .friendsPicker_container { /* long container used to house end-to-end panels. Width is calculated in JS  */
	position: relative;
	left: 0;
	top: 0;
	width: 100%;
	list-style-type: none;
}
.friendsPicker .friendsPicker_container .panel {
	float:left;
	height: 100%;
	position: relative;
	width: 678px;
	margin: 0;
	padding:0;
}
.friendsPicker .friendsPicker_container .panel .wrapper {
	margin: 0;
	padding:4px 10px 10px 10px;
	min-height: 230px;
}
.friendsPickerNavigation {
	margin: 0 0 10px 0;
	padding:0;
}
.friendsPickerNavigation ul {
	list-style: none;
	padding-left: 0;
}
.friendsPickerNavigation ul li {
	float: left;
	margin:0;
	background:white;
}
.friendsPickerNavigation a {
	font-weight: bold;
	text-align: center;
	background: white;
	color: #999999;
	text-decoration: none;
	display: block;
	padding: 0;
	width:20px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.tabHasContent {
	background: white; color:#333333 !important;
}
.friendsPickerNavigation li a:hover {
	background: #333333;
	color:white !important;
}
.friendsPickerNavigation li a.current {
	background: #4690D6;
	color:white !important;
}
.friendsPickerNavigationAll {
	margin:0px 0 0 20px;
	float:left;
}
.friendsPickerNavigationAll a {
	font-weight: bold;
	text-align: left;
	font-size:0.8em;
	background: white;
	color: #999999;
	text-decoration: none;
	display: block;
	padding: 0 4px 0 4px;
	width:auto;
}
.friendsPickerNavigationAll a:hover {
	background: #4690D6;
	color:white;
}
.friendsPickerNavigationL, .friendsPickerNavigationR {
	position: absolute;
	top: 46px;
	text-indent: -9000em;
}
.friendsPickerNavigationL a, .friendsPickerNavigationR a {
	display: block;
	height: 43px;
	width: 43px;
}
.friendsPickerNavigationL {
	right: 48px;
	z-index:1;
}
.friendsPickerNavigationR {
	right: 0;
	z-index:1;
}
.friendsPickerNavigationL {
	background: url("http://elgg.mine.nu/_graphics/friends_picker_arrows.gif") no-repeat left top;
}
.friendsPickerNavigationR {
	background: url("http://elgg.mine.nu/_graphics/friends_picker_arrows.gif") no-repeat -60px top;
}
.friendsPickerNavigationL:hover {
	background: url("http://elgg.mine.nu/_graphics/friends_picker_arrows.gif") no-repeat left -44px;
}
.friendsPickerNavigationR:hover {
	background: url("http://elgg.mine.nu/_graphics/friends_picker_arrows.gif") no-repeat -60px -44px;
}	
.friends_collections_controls a.delete_collection {
	display:block;
	cursor: pointer;
	width:14px;
	height:14px;
	margin:2px 3px 0 0;
	background: url("http://elgg.mine.nu/_graphics/icon_customise_remove.png") no-repeat 0 0;
}
.friends_collections_controls a.delete_collection:hover {
	background-position: 0 -16px;
}
.friendspicker_savebuttons .submit_button,
.friendspicker_savebuttons .cancel_button {
	margin:5px 20px 5px 5px;
}

#collectionMembersTable {
	background: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	margin:10px 0 0 0;
	padding:10px 10px 0 10px;
}

	
/* ***************************************
  WIDGET PICKER (PROFILE & DASHBOARD)
*************************************** */
/* 'edit page' button */
a.toggle_customise_edit_panel { 
	float:right;
	clear:right;
	color: #4690d6;
	background: white;
	border:1px solid #cccccc;
	padding: 5px 10px 5px 10px;
	margin:0 0 20px 0;
	width:280px;
	text-align: left;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
a.toggle_customise_edit_panel:hover { 
	color: #ffffff;
	background: #0054a7;
	border:1px solid #0054a7;
	text-decoration:none;
}
#customise_editpanel {
	display:none;
	margin: 0 0 20px 0;
	padding:10px;
	background: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}

/* Top area - instructions */
.customise_editpanel_instructions {
	width:690px;
	padding:0 0 10px 0;
}
.customise_editpanel_instructions h2 {
	padding:0 0 10px 0;
}
.customise_editpanel_instructions p {
	margin:0 0 5px 0;
	line-height: 1.4em;
}

/* RHS (widget gallery area) */
#customise_editpanel_rhs {
	float:right;
	width:230px;
	background:white;
}
#customise_editpanel #customise_editpanel_rhs h2 {
	color:#333333;
	font-size: 1.4em;
	margin:0;
	padding:6px;
}
#widget_picker_gallery {
	border-top:1px solid #cccccc;
	background:white;
	width:210px; 
	height:340px;
	padding:10px;
	overflow:scroll;
	overflow-x:hidden;
}

/* main page widget area */
#customise_page_view {
	width:656px;
	padding:10px;
	margin:0 0 10px 0;
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#customise_page_view h2 {
	border-top:1px solid #cccccc;
	border-right:1px solid #cccccc;
	border-left:1px solid #cccccc;
	margin:0;
	padding:5px;
	width:200px;
	color: #0054a7;
	background: #dedede;
	font-size:1.25em;
	line-height: 1.2em;
}
#profile_box_widgets {
	width:422px;
	margin:0 10px 10px 0;
	padding:5px 5px 0px 5px;
	min-height: 50px;
	border:1px solid #cccccc;
	background: #dedede;
}
#customise_page_view h2.profile_box {
	width:422px;
	color: #999999;
}
#profile_box_widgets p {
	color:#999999;
}
#leftcolumn_widgets {
	width:200px;
	margin:0 10px 0 0;
	padding:5px 5px 40px 5px;
	min-height: 190px;
	border:1px solid #cccccc;
}
#middlecolumn_widgets {
	width:200px;
	margin:0 10px 0 0;
	padding:5px 5px 40px 5px;
	min-height: 190px;
	border:1px solid #cccccc;
}
#rightcolumn_widgets {
	width:200px;
	margin:0;
	padding:5px 5px 40px 5px;
	min-height: 190px;
	border:1px solid #cccccc;
}
#rightcolumn_widgets.long {
	min-height: 288px;
}
/* IE6 fix */
* html #leftcolumn_widgets { 
	height: 190px;
}
* html #middlecolumn_widgets { 
	height: 190px;
}
* html #rightcolumn_widgets { 
	height: 190px;
}
* html #rightcolumn_widgets.long { 
	height: 338px;
}

#customise_editpanel table.draggable_widget {
	width:200px;
	background: #cccccc;
	margin: 10px 0 0 0;
	vertical-align:text-top;
	border:1px solid #cccccc;
}
#widget_picker_gallery table.draggable_widget {
	width:200px;
	background: #cccccc;
	margin: 10px 0 0 0;
}

/* take care of long widget names */
#customise_editpanel table.draggable_widget h3 {
	word-wrap:break-word;/* safari, webkit, ie */
	width:140px;
	line-height: 1.1em;
	overflow: hidden;/* ff */
	padding:4px;
}
#widget_picker_gallery table.draggable_widget h3 {
	word-wrap:break-word;
	width:145px;
	line-height: 1.1em;
	overflow: hidden;
	padding:4px;
}
#customise_editpanel img.more_info {
	background: url(http://elgg.mine.nu/_graphics/icon_customise_info.gif) no-repeat top left;
	cursor:pointer;
}
#customise_editpanel img.drag_handle {
	background: url(http://elgg.mine.nu/_graphics/icon_customise_drag.gif) no-repeat top left;
	cursor:move;
}
#customise_editpanel img {
	margin-top:4px;
}
#widget_moreinfo {
	position:absolute;
	border:1px solid #333333;
	background:#e4ecf5;
	color:#333333;
	padding:5px;
	display:none;
	width: 200px;
	line-height: 1.2em;
}
/* droppable area hover class  */
.droppable-hover {
	background:#bbdaf7;
}
/* target drop area class */
.placeholder {
	border:2px dashed #AAA;
	width:196px !important;
	margin: 10px 0 10px 0;
}
/* class of widget while dragging */
.ui-sortable-helper {
	background: #4690d6;
	color:white;
	padding: 4px;
	margin: 10px 0 0 0;
	width:200px;
}
/* IE6 fix */
* html .placeholder { 
	margin: 0;
}
/* IE7 */
*:first-child+html .placeholder {
	margin: 0;
}
/* IE6 fix */
* html .ui-sortable-helper h3 { 
	padding: 4px;
}
* html .ui-sortable-helper img.drag_handle, * html .ui-sortable-helper img.remove_me, * html .ui-sortable-helper img.more_info {
	padding-top: 4px;
}
/* IE7 */
*:first-child+html .ui-sortable-helper h3 {
	padding: 4px;
}
*:first-child+html .ui-sortable-helper img.drag_handle, *:first-child+html .ui-sortable-helper img.remove_me, *:first-child+html .ui-sortable-helper img.more_info {
	padding-top: 4px;
}


/* ***************************************
	BREADCRUMBS
*************************************** */
#pages_breadcrumbs {
	font-size: 80%;
	color:#bababa;
	padding:0;
	margin:2px 0 0 10px;
}
#pages_breadcrumbs a {
	color:#999999;
	text-decoration: none;
}
#pages_breadcrumbs a:hover {
	color: #0054a7;
	text-decoration: underline;
}


/* ***************************************
	MISC.
*************************************** */
/* general page titles in main content area */
#content_area_user_title h2 {	
	margin:0 0 0 8px;
	padding:5px;
	color:#0054A7;
	font-size:1.35em;
	line-height:1.2em;
}
/* reusable generic collapsible box */
.collapsible_box {
	background:#dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	padding:5px 10px 5px 10px;
	margin:4px 0 4px 0;
	display:none;
}	
a.collapsibleboxlink {
	cursor:pointer;
}

/* tag icon */	
.object_tag_string {
	background: url(http://elgg.mine.nu/_graphics/icon_tag.gif) no-repeat left 2px;
	padding:0 0 0 14px;
	margin:0;
}	

/* profile picture upload n crop page */	
#profile_picture_form {
	height:145px;
}	
#current_user_avatar {
	float:left;
	width:160px;
	height:130px;
	border-right:1px solid #cccccc;
	margin:0 20px 0 0;
}	
#profile_picture_croppingtool {
	border-top: 1px solid #cccccc;
	margin:20px 0 0 0;
	padding:10px 0 0 0;
}	
#profile_picture_croppingtool #user_avatar {
	float: left;
	margin-right: 20px;
}	
#profile_picture_croppingtool #applycropping {

}
#profile_picture_croppingtool #user_avatar_preview {
	float: left;
	position: relative;
	overflow: hidden;
	width: 100px;
	height: 100px;
}	


/* ***************************************
	SETTINGS & ADMIN
*************************************** */
.admin_statistics,
.admin_users_online,
.usersettings_statistics,
.admin_adduser_link,
#add-box,
#search-box,
#logbrowser_search_area {
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
	margin:0 10px 10px 10px;
	padding:10px;
}

.usersettings_statistics h3,
.admin_statistics h3,
.admin_users_online h3,
.user_settings h3,
.notification_methods h3 {
	background:#e4e4e4;
	color:#333333;
	font-size:1.1em;
	line-height:1em;
	margin:0 0 10px 0;
	padding:5px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;	
}
h3.settings {
	background:#e4e4e4;
	color:#333333;
	font-size:1.1em;
	line-height:1em;
	margin:10px 0 4px 0;
	padding:5px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.admin_users_online .profile_status {
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	background:#bbdaf7;
	line-height:1.2em;
	padding:2px 4px;
}
.admin_users_online .profile_status span {
	font-size:90%;
	color:#666666;
}
.admin_users_online  p.owner_timestamp {
	padding-left:3px;
}


.admin_debug label,
.admin_usage label {
	color:#333333;
	font-size:100%;
	font-weight:normal;
}

.admin_usage {
	border-bottom:1px solid #cccccc;
	padding:0 0 20px 0;
}
.usersettings_statistics .odd,
.admin_statistics .odd {
	background:#EFEFEF;
}
.usersettings_statistics .even,
.admin_statistics .even {
	background:#ffffff;
}

.usersettings_statistics .selected,
.admin_statistics .selected {
	/*background:#4690D6;
	color: #fff;*/
	color: #00407F;
}

.usersettings_statistics td,
.admin_statistics td {
	padding:2px 4px 2px 4px;
	border-bottom:1px solid #cccccc;
}
.usersettings_statistics td.column_one,
.admin_statistics td.column_one {
	width:200px;
}
.usersettings_statistics table,
.admin_statistics table {
	width:100%;
}
.usersettings_statistics table,
.admin_statistics table {
	border-top:1px solid #cccccc;
}

.admin_statistics table tr.important {
	background-color: #FDE5DD;
	color: #000;
}


.usersettings_statistics table tr:hover,
.admin_statistics table tr:hover {
	background: #00407F;
	color: #fff;
}

.usersettings_statistics table tr .urgent,
.admin_statistics table tr .urgent{
	background:#FFCFCF;
	color:#FE0404;
}

.usersettings_statistics table tr.selected:hover,.admin_statistics table tr.selected:hover{
	background:  #00407F;
	color: #FFE900;
}

.usersettings_statistics table tr:hover .urgent,
.admin_statistics table tr:hover .urgent{
	background:  #00407F;
	color: #fff;
}




.admin_users_online .search_listing {
	margin:0 0 5px 0;
	padding:5px;
	border:2px solid #cccccc;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}

/* force tinyMCE editor initial width for safari */
.mceLayout {
	width:683px;
}
p.longtext_editarea {
	margin:0 !important;
}
.toggle_editor_container {
	margin:0 0 15px 0;
}
/* add/remove longtext tinyMCE editor */
a.toggle_editor {
	display:block;
	float:right;
	text-align:right;
	color:#666666;
	font-size:1em;
	font-weight:normal;
}

div.ajax_loader {
	background: white url(/images/ajax_loader.gif) no-repeat center 30px;
	width:auto;
	height:100px;
	margin:0 10px 0 10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}



/* reusable elgg horizontal tabbed navigation 
   (used on friends collections, external pages, & riverdashboard mods)
*/
#elgg_horizontal_tabbed_nav {
	margin:0 0 5px 0;
	padding: 0;
	border-bottom: 2px solid #cccccc;
	display:table;
	width:100%;
}
#elgg_horizontal_tabbed_nav ul {
	list-style: none;
	padding: 0;
	margin: 0;
}
#elgg_horizontal_tabbed_nav li {
	float: left;
	border: 2px solid #cccccc;
	border-bottom-width: 0;
	background: #eeeeee;
	margin: 0 0 0 10px;
	-moz-border-radius-topleft:5px;
	-moz-border-radius-topright:5px;	
	-webkit-border-top-left-radius:5px;
	-webkit-border-top-right-radius:5px;
}
#elgg_horizontal_tabbed_nav a {
	text-decoration: none;
	display: block;
	padding:3px 10px 0 10px;
	color: #999999;
	text-align: center;
	height:21px;
}
/* IE6 fix */
* html #elgg_horizontal_tabbed_nav a { display: inline; }

#elgg_horizontal_tabbed_nav a:hover {
	color: #4690d6;
	background: #dedede;
}
#elgg_horizontal_tabbed_nav .selected {
	border-color: #cccccc;
	background: white;
}
#elgg_horizontal_tabbed_nav .selected a {
	position: relative;
	top: 2px;
	background: white;
	color: #4690d6;
}
/* IE6 fix */
* html #elgg_horizontal_tabbed_nav .selected a { top: 3px; }


/* ***************************************
	ADMIN AREA - REPORTED CONTENT
*************************************** */
.reportedcontent_content {
	margin:0 0 5px 0;
	padding:0 7px 4px 10px;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
.reportedcontent_content p.reportedcontent_detail,
.reportedcontent_content p {
	margin:0;
}
.active_report {
	border:1px solid #D3322A;
    background:#F7DAD8;
}

.archived_report {
	border:1px solid #508F53;
    background:#D1EFD2;
}

.archived_report_blue {
	border:1px solid #0054A7;
    background:#DFEFFF;
}

.archived_report_event {
	border:1px solid #fff;
    margin: 10px 30px;
    padding:10px;
    background:#09589F;
    color:#fff;
    font-size:1.3em;
    font-weight: bold;
}

a.archive_report_button {
	float:right;
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#4690d6;
	border: 1px solid #4690d6;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 4px;
	margin:15px 0 0 20px;
	cursor: pointer;
}
a.archive_report_button:hover {
	background: #0054a7;
	border: 1px solid #0054a7;
	text-decoration: none;
}
a.delete_report_button {
	float:right;
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#999999;
	border: 1px solid #999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 4px;
	margin:15px 0 0 20px;
	cursor: pointer;
}
a.delete_report_button:hover {
	background: #333333;
	border: 1px solid #333333;
	text-decoration:none;
}
.reportedcontent_content .collapsible_box {
	background: white;
}




#profile_icon_wrapper {
	float:left;
}
	
.usericon {
	position:relative;
}

.avatar_menu_button {
	width:15px;
	height:15px;
	position:absolute;
	cursor:pointer;
	display:none;
	right:0;
	bottom:0;
}
.avatar_menu_arrow {
	background: url(http://elgg.mine.nu/_graphics/avatar_menu_arrows.gif) no-repeat left top;
	width:15px;
	height:15px;
}
.avatar_menu_arrow_on {
	background: url(http://elgg.mine.nu/_graphics/avatar_menu_arrows.gif) no-repeat left -16px;
	width:15px;
	height:15px;
}
.avatar_menu_arrow_hover {
	background: url(http://elgg.mine.nu/_graphics/avatar_menu_arrows.gif) no-repeat left -32px;
	width:15px;
	height:15px;
}
.usericon div.sub_menu { 
	display:none; 
	position:absolute; 
	padding:2px; 
	margin:0; 
	border-top:solid 1px #E5E5E5; 
	border-left:solid 1px #E5E5E5; 
	border-right:solid 1px #999999; 
	border-bottom:solid 1px #999999;  
	width:160px; 
	background:#FFFFFF; 
	text-align:left;
}
div.usericon a.icon img {
	z-index:10;
}

.usericon div.sub_menu a {margin:0;padding:2px;}
.usericon div.sub_menu a:link, 
.usericon div.sub_menu a:visited, 
.usericon div.sub_menu a:hover{ display:block;}	
.usericon div.sub_menu a:hover{ background:#cccccc; text-decoration:none;}

.usericon div.sub_menu h3 {
	font-size:1.2em;
	padding-bottom:3px;
	border-bottom:solid 1px #dddddd;
	color: #4690d6;
	margin:0 !important;
}
.usericon div.sub_menu h3:hover {

}

.user_menu_addfriend,
.user_menu_removefriend,
.user_menu_profile,
.user_menu_friends,
.user_menu_friends_of,
.user_menu_blog,
.user_menu_file,
.user_menu_messages,
.user_menu_admin,
.user_menu_pages {
	margin:0;
	padding:0;
}
.user_menu_admin {
	border-top:solid 1px #dddddd;
}
.user_menu_admin a {
	color:red;
}
.user_menu_admin a:hover {
	color:white !important;
	background:red !important;
}

.resetdefaultprofile {
	padding:0 10px 0 10px;
}
.resetdefaultprofile input[type="submit"] {
	background: #dedede;
	border-color: #dedede;
	color:#333333;
}
.resetdefaultprofile input[type="submit"]:hover {
	background: red;
	border-color: red;
	color:white;
}

/* Banned user */
#profile_banned {
	background-color:#FF8888;
	border:3px solid #FF0000;
	padding:2px;
}
#facebox {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 100;
	text-align: left;
}
#facebox .popup {
	position: relative;
}
#facebox .body {
	padding: 10px;
	background: white;
	width: 730px;
	-webkit-border-radius: 12px; 
	-moz-border-radius: 12px;
}
#facebox .loading {
	text-align: center;
	padding: 100px 10px 100px 10px;
}
#facebox .image {
	text-align: center;
}
#facebox .footer {
	float: right;
	width:22px;
	height:22px;
	margin:0;
	padding:0;
}
#facebox .footer img.close_image {
	background: url(http://elgg.mine.nu/mod/embed/images/close_button.gif) no-repeat left top;
}
#facebox .footer img.close_image:hover {
	background: url(http://elgg.mine.nu/mod/embed/images/close_button.gif) no-repeat left -31px;
}
#facebox .footer a {
	-moz-outline: none;
	outline: none;
}
#facebox_overlay {
	position: fixed;
	top: 0px;
	left: 0px;
	height:100%;
	width:100%;
}
.facebox_hide {
	z-index:-100;
}
.facebox_overlayBG {
	background-color: #000000;
	z-index: 99;
}

* html #facebox_overlay { /* ie6 hack */
 position: absolute;
 height: expression(document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight + 'px');
}


/* EMBED MEDIA TABS */
#embed_media_tabs {
	margin:10px 0 0 10px;
	padding:0;
}
#embed_media_tabs ul {
	list-style: none;
	padding-left: 0;
}
#embed_media_tabs ul li {
	float: left;
	margin:0;
	background:white;
}
#embed_media_tabs ul li a {
	font-weight: bold;
	font-size:1.35em;
	text-align: center;
	text-decoration: none;
	color:#b6b6b6;
	background: white;
	display: block;
	padding: 0 10px 0 10px;
	margin:0 10px 0 10px;
	height:25px;
	width:auto;
	border-top:2px solid #dedede;
	border-left:2px solid #dedede;
	border-right:2px solid #dedede;
	-moz-border-radius-topleft: 8px;
	-moz-border-radius-topright: 8px;
	-webkit-border-top-left-radius: 8px;
	-webkit-border-top-right-radius: 8px;
}
/* IE6 fix */
* html #embed_media_tabs ul li a { display: inline; }

#embed_media_tabs ul li a:hover {
	background:#b6b6b6;
	color:white;
	border-top:2px solid #b6b6b6;
	border-left:2px solid #b6b6b6;
	border-right:2px solid #b6b6b6;
}
#embed_media_tabs ul li a.embed_tab_selected {
	border-top:2px solid #dedede;
	border-left:2px solid #dedede;
	border-right:2px solid #dedede;
	-webkit-border-top-left-radius: 8px;
	-webkit-border-top-right-radius: 8px;
	-moz-border-radius-topleft: 8px;
	-moz-border-radius-topright: 8px;
	background: #dedede;
	color:#666666;
	position: relative;
	/* top: 2px; - only needed if selected tab needs to sit over a border */
}

#mediaUpload,
#mediaEmbed {
	margin:0 5px 10px 5px;
	padding:10px;
	border:2px solid #dedede;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	background: #dedede;
}
#mediaEmbed .search_listing {
	margin:0 0 5px 0;
}

h1.mediaModalTitle {
	color:#0054A7;
	font-size:1.35em;
	line-height:1.2em;
	margin:0 0 0 8px;
	padding:5px;
}

#mediaEmbed .pagination,
#mediaUpload .pagination {
	float:right;
	margin:0;
}
#mediaUpload label {
	font-size:120%;
}
#mediaEmbed p.embedInstructions {
	margin:10px 0 5px 0;
}

a.embed_media {
	margin:0;
	float:right;
	display:block;
	text-align: right;
	font-size:1.0em;
	font-weight: normal;
}
label a.embed_media {
	font-size:0.8em;
}

.singleview {
	margin-top:10px;
}

.blog_post_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}

.blog_post h3 {
	font-size: 150%;
	margin:0 0 10px 0;
	padding:0;
}

.blog_post h3 a {
	text-decoration: none;
}

.blog_post p {
	margin: 0 0 5px 0;
}

.blog_post .strapline {
	margin: 0 0 0 35px;
	padding:0;
	color: #aaa;
	line-height:1em;
}
.blog_post p.tags {
	background:transparent url(http://elgg.mine.nu/_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 7px 35px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}
.blog_post .options {
	margin:0;
	padding:0;
}

.blog_post_body img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}
.blog_post_body img[align="right"] {
	margin: 10px 0 10px 10px;
	float:right;
}
.blog_post_body img {
	margin: 10px !important;
}

.blog-comments h3 {
	font-size: 150%;
	margin-bottom: 10px;
}
.blog-comment {
	margin-top: 10px;
	margin-bottom:20px;
	border-bottom: 1px solid #aaaaaa;
}
.blog-comment img {
	float:left;
	margin: 0 10px 0 0;
}
.blog-comment-menu {
	margin:0;
}
.blog-comment-byline {
	background: #dddddd;
	height:22px;
	padding-top:3px;
	margin:0;
}
.blog-comment-text {
	margin:5px 0 5px 0;
}

/* New blog edit column */
#blog_edit_page {
	/* background: #bbdaf7; */
	margin-top:-10px;
}
#blog_edit_page #content_area_user_title h2 {
	background: none;
	border-top: none;
	margin:0 0 10px 0px;
	padding:0px 0 0 0;
}
#blog_edit_page #blog_edit_sidebar #content_area_user_title h2 {
	background:none;
	border-top:none;
	margin:inherit;
	padding:0 0 5px 5px;
	font-size:1.25em;
	line-height:1.2em;
}
#blog_edit_page #blog_edit_sidebar {
	margin:0px 0 22px 0;
	background: #dedede;
	padding:5px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	border-bottom:1px solid #cccccc;
	border-right:1px solid #cccccc;
}
#blog_edit_page #two_column_left_sidebar_210 {
	width:210px;
	margin:0px 0 20px 0px;
	min-height:360px;
	float:left;
	padding:0;
}
#blog_edit_page #two_column_left_sidebar_maincontent {
	margin:0 0px 20px 20px;
	padding:10px 20px 20px 20px;
	width:670px;
	background: #bbdaf7;
}
/* unsaved blog post preview */
.blog_previewpane {
    border:1px solid #D3322A;
    background:#F7DAD8;
	padding:10px;
	margin:10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;	
}
.blog_previewpane p {
	margin:0;
}

#blog_edit_sidebar .publish_controls,
#blog_edit_sidebar .blog_access,
#blog_edit_sidebar .publish_options,
#blog_edit_sidebar .publish_blog,
#blog_edit_sidebar .allow_comments,
#blog_edit_sidebar .categories {
	margin:0 5px 5px 5px;
	border-top:1px solid #cccccc;
}
#blog_edit_page ul {
	padding-left:0px;
	margin:5px 0 5px 0;
	list-style: none;
}
#blog_edit_page p {
	margin:5px 0 5px 0;
}
#blog_edit_page #two_column_left_sidebar_maincontent p {
	margin:0 0 15px 0;
}
#blog_edit_page .publish_blog input[type="submit"] {
	font-weight: bold;
	padding:2px;
	height:auto;
}
#blog_edit_page .preview_button a {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	background:white;
	border: 1px solid #cccccc;
	color:#999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: auto;
	padding: 3px;
	margin:1px 1px 5px 10px;
	cursor: pointer;
	float:right;
}
#blog_edit_page .preview_button a:hover {
	background:#4690D6;
	color:white;
	text-decoration: none;
	border: 1px solid #4690D6;
}
#blog_edit_page .allow_comments label {
	font-size: 100%;
}







.categories .input-checkboxes {
	padding:0;
	margin:2px 5px 0 0;
}
.categories label {
	font-size: 100%;
	line-height:1.2em;
}

#two_column_left_sidebar_maincontent .contentWrapper h2.categoriestitle {
	padding: 0 0 3px 0;
	margin:0;
	font-size:120%;
	color:#333333;
}
#two_column_left_sidebar_maincontent .contentWrapper .categories {
	border:1px solid #CCCCCC;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	padding:5px;
	margin:0 0 15px 0;	
}
#two_column_left_sidebar_maincontent .contentWrapper .categories p {
	margin:0;	
}
#two_column_left_sidebar_maincontent .contentWrapper .blog_post .categories {
	border:none;
	margin:0;
	padding:0;
}

#two_column_left_sidebar .blog_categories {
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
    padding:10px;
    margin:0 10px 10px 10px;
}
#two_column_left_sidebar .blog_categories h2 {
	background:none;
	border-top:none;
	margin:0;
	padding:0 0 5px 0;
	font-size:1.25em;
	line-height:1.2em;
	color:#0054A7;
}
#two_column_left_sidebar .blog_categories ul {
	color:#0054A7;
	margin:5px 0 0 0;
}

p.filerepo_owner {
	margin:0;
	padding:0;
}
.filerepo_owner_details {
	margin:0;
	padding:0;
	line-height: 1.2em;
}
.filerepo_owner_details small {
	color:#666666;
}
.filerepo_owner .usericon {
	margin: 3px 5px 5px 0;
	float: left;
}

.filerepo_download a {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: white;
	background:#4690d6;
	border:none;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
	width: auto;
	height: 25px;
	padding: 3px 6px 3px 6px;
	margin:10px 0 10px 0;
	cursor: pointer;
}
.filerepo_download a:hover {
	background: black;
	color:white;
	text-decoration: none;
}

/* FILE REPRO WIDGET VIEW */
.filerepo_widget_singleitem {
	margin:0 0 5px 0;
	padding:5px;
	min-height:60px;
	display:block;
	background:white;
   	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
.filerepo_widget_singleitem_more {
	margin:0;
	padding:5px;
	display:block;
	background:white;
   	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;	
}
.filerepo_listview_icon {
	float: left;
	margin-right: 10px;
}
.filerepo_timestamp {
	color:#666666;
	margin:0;
}
.filerepo_listview_desc {
	display:none;
	padding:0 10px 10px 0;
	line-height: 1.2em;
}
.filerepo_listview_desc p {
	color:#333333;
}
.filerepo_widget_content {
	margin-left: 70px;
}
.filerepo_title {
	margin:0;
	padding:6px 5px 0 0;
	line-height: 1.2em;
	color:#666666;
	font-weight: bold;
}

.collapsable_box #filerepo_widget_layout {
	margin:0 10px 0 10px;
	background: none;
}

/* widget gallery view */
#filerepo_widget_layout .filerepo_widget_galleryview {
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background: white;
	margin:0 0 5px 0;	
}
.filerepo_widget_galleryview img {
	padding:0;
    border:1px solid white;
    margin:4px;
}
.filerepo_widget_galleryview img:hover {
	border:1px solid #333333;
}

/* SINGLE ITEM VIEW */
.filerepo_file {
	background:white;
	margin:10px 10px 0 10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
.filerepo_file .filerepo_title_owner_wrapper {
	min-height:60px;

}
.filerepo_title_owner_wrapper .filerepo_title,
.filerepo_title_owner_wrapper .filerepo_owner {
	margin-left: 70px !important;
}
.filerepo_file .filerepo_maincontent {
	padding:0 20px 0 0;
}
.filerepo_file .filerepo_icon {
	width: 70px;
	position: absolute;
	margin:10px 0 10px 10px;
}
.filerepo_file .filerepo_title {
	margin:0;
	padding:7px 4px 10px 10px;
	line-height: 1.2em;
}
.filerepo_file .filerepo_owner {
	padding:0 0 0 10px;
}
.filerepo_file .filerepo_description {
	margin:10px 0 0 0;
	padding:0 0 0 10px;
}
.filerepo_download,
.filerepo_controls {
	padding:0 0 1px 10px;
	margin:0 0 10px 0;
}
.filerepo_file .filerepo_description p {
	padding:0 0 5px 0;
	margin:0;
}
.filerepo_file .filerepo_specialcontent img {
	padding:10px;
	margin:0 0 0 10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background: black; 
}
/* zaudio player */
.filerepo_maincontent .filerepo_specialcontent {
	margin:0 0 20px 0;
}
.filerepo_tags {
	padding:0 0 10px 10px;
	margin:0;
}

/* file repro gallery items */
.search_gallery .filerepo_controls {
	padding:0;
}
.search_gallery .filerepo_title {
	font-weight: bold;
	line-height: 1.1em;
	margin:0 0 10px 0;
}
.filerepo_gallery_item {
	margin:0;
	padding:0;
	text-align:center;

}
.filerepo_gallery_item p {
	margin:0;
	padding:0;
}
.filerepo_gallery_item .filerepo_controls {
	margin-top:10px;
}
.filerepo_gallery_item .filerepo_controls a {
	padding-right:10px;
	padding-left:10px;
}
.search_gallery .filerepo_comments {
	font-size:90%;
}

.filerepo_user_gallery_link {
	float:right;
	margin:5px 5px 5px 50px;
}
.filerepo_user_gallery_link a {
	padding:2px 25px 5px 0;
	background: transparent url(http://elgg.mine.nu/_graphics/icon_gallery.gif) no-repeat right top;
	display:block;
}
.filerepo_user_gallery_link a:hover {
	background-position: right -40px;
}

/* IE6 */
* html #description_tbl { width:676px !important;}




#content_area_group_title h2 {
	color:#0054A7;
	font-size:1.35em;
	line-height:1.2em;
	margin:0 0 0 8px;
	padding:5px;
}
#topic_posts #content_area_group_title h2 {
	margin:0 0 0 0;
}

#two_column_left_sidebar_maincontent #owner_block_content {
	margin:0 0 10px 0 !important;
}

#groups_info_column_left {
	float:left:
	width:435px;
	margin-left:230px;
	margin-right:10px;
}

#groups_info_column_left .odd {
	background:#EFEFEF;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
#groups_info_column_left .even {
	background:#E9E9E9;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
#groups_info_column_left p {
	margin:0 0 7px 0;
	padding:2px 4px;
}

#groups_info_column_right {
	float:left;
	width:230px;
	margin:0 0 0 10px;
}
#groups_info_wide p {
	text-align: right;
	padding-right:10px;
}
#group_stats {
	width:190px;
	background: #e9e9e9;
	padding:5px;
	margin:10px 0 20px 0;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
}
#group_stats p {
	margin:0;
}
#group_members {
	margin:10px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background: white;
}

#right_column {
	clear:left;
	float:right;
	width:340px;
	margin:0 10px 0 0;
}
#left_column {
	width:340px;
	float:left;
	margin:0 10px 0 10px;

}
/* IE 6 fixes */
* html #left_column { 
	margin:0 0 0 5px;
}
* html #right_column { 
	margin:0 5px 0 0;
}

#group_members h2,
#right_column h2,
#left_column h2,
#fullcolumn h2 {
	margin:0 0 10px 0;
	padding:5px;
	color:#0054A7;
	font-size:1.25em;
	line-height:1.2em;
}
#fullcolumn .contentWrapper {
	margin:0 10px 20px 10px;
	padding:0 0 5px;
}

.member_icon {
	margin:0 0 6px 6px;
	float:left;
}

/* IE6 */
* html #topic_post_tbl { width:676px !important;}

/* all browsers - force tinyMCE on edit comments to be full-width */
.edit_forum_comments .defaultSkin table.mceLayout {
	width: 636px !important;
}

/* topics overview page */
#forum_topics {
    padding:10px;
    margin:0 10px 0 10px;
    background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;    
}
/* topics individual view page */
#topic_posts {
	margin:0 10px 5px 10px;
}
#topic_posts #pages_breadcrumbs {
	margin:2px 0 0 0px;
}
#topic_posts form {
    padding:10px;
    margin:30px 0 0 0;
    background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px; 
}
.topic_post {
	padding:10px;
    margin:0 0 5px 0;
    background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;  
}
.topic_post .post_icon {
    float:left;
    margin:0 8px 4px 0;
}
.topic_post h2 {
    margin-bottom:20px;
}
.topic_post p.topic-post-menu {
	margin:0;
}
.topic_post p.topic-post-menu a.collapsibleboxlink {
	padding-left:10px;
}
.topic_post table, td {
    border:none;
}

/* group latest discussions widget */
#latest_discussion_widget {
	margin:0 0 20px 0;
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
/* group files widget */
#filerepo_widget_layout {
	margin:0 0 20px 0;
	padding: 0 0 5px 0;
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
/* group pages widget */
#group_pages_widget {
	margin:0 0 20px 0;
	padding: 0 0 5px 0;
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
#group_pages_widget .search_listing {
	border: 2px solid #cccccc;
}
#right_column .filerepo_widget_singleitem {
	background: #dedede !important;
	margin:0 10px 5px 10px;
}
#left_column .filerepo_widget_singleitem {
	background: #dedede !important;
	margin:0 10px 5px 10px;
}
.forum_latest {
	margin:0 10px 5px 10px;
	background: #dedede;
	padding:5px;
   	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
}
.forum_latest:hover {

}
.forum_latest .topic_owner_icon {
	float:left;
}
.forum_latest .topic_title {
	margin-left:35px;
}
.forum_latest .topic_title p {
	line-height: 1.0em;
    padding:0;
    margin:0;
    font-weight: bold;
}
.forum_latest p.topic_replies {
    padding:3px 0 0 0;
    margin:0;
    color:#666666;
}
.add_topic {
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
	margin:5px 10px;
	padding:10px 10px 10px 6px;
}

a.add_topic_button {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: white;
	background:#4690d6;
	border:none;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
	width: auto;
	height: auto;
	padding: 3px 6px 3px 6px;
	margin:0;
	cursor: pointer;
}
a.add_topic_button:hover {
	background: #0054a7;
	color:white;
	text-decoration: none;
}



/* latest discussion listing */
.latest_discussion_info {
	float:right;
	width:300px;
	text-align: right;
	margin-left: 10px;
}
.groups .search_listing br {
	height:0;
	line-height:0;
}
span.timestamp {
	color:#666666;
	font-size: 90%;
}
.latest_discussion_info .timestamp {
	font-size: 0.85em;
}
/* new groups page */
.groups .search_listing {
	border:2px solid #cccccc;
	margin:0 0 5px 0;
}
.groups .search_listing:hover {
	background:#dedede;
}
.groups .group_count {
	font-weight: bold;
	color: #666666;
	margin:0 0 5px 4px;
}
.groups .search_listing_info {
	color:#666666;
}
.groupdetails {
	float:right;
}
.groupdetails p {
	margin:0;
	padding:0;
	line-height: 1.1em;
	text-align: right;
}
#groups_closed_membership {
	margin:0 10px 20px 10px;
	padding: 3px 5px 5px 5px;
	background:#bbdaf7;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;	
}
#groups_closed_membership p {
	margin:0;
}

/* groups membership widget */
.groupmembershipwidget .contentWrapper {
	margin:0 10px 5px 10px;
}
.groupmembershipwidget .contentWrapper .groupicon {
	float:left;
	margin:0 10px 0 0;
}
.groupmembershipwidget .search_listing_info p {
	color: #666666;
}
.groupmembershipwidget .search_listing_info span {
	font-weight: bold;
}

/* groups sidebar */
.featuredgroups .contentWrapper {
	margin:0 0 10px 0;
}
.featuredgroups .contentWrapper .groupicon {
	float:left;
	margin:0 10px 0 0;
}
.featuredgroups .contentWrapper p {
	margin: 0;
	line-height: 1.2em;
	color:#666666;
}
.featuredgroups .contentWrapper span {
	font-weight: bold;
}
#groupssearchform {
	border-bottom: 1px solid #cccccc;
	margin-bottom: 10px;
}
#groupssearchform input[type="submit"] {
	padding:2px;
	height:auto;
	margin:4px 0 5px 0;
}
.sidebarBox #owner_block_submenu {
	margin:5px 0 0 0;
}

/* delete post */
.delete_discussion {
	
}
.delete_discussion a {
	display:block;
	float:right;
	cursor: pointer;
	width:14px;
	height:14px;
	margin:0;
	background: url("http://elgg.mine.nu/_graphics/icon_customise_remove.png") no-repeat 0 0;
}
.delete_discussion a:hover {
	background-position: 0 -16px;
	text-decoration: none;
}
/* IE6 */
* html .delete_discussion a { font-size: 1px; }
/* IE7 */
*:first-child+html .delete_discussion a { font-size: 1px; }

/* delete group button */
#delete_group_option input[type="submit"] {
	background:#dedede;
	border-color:#dedede;
	color:#333333; 
	margin:0;
	float:right;
	clear:both;
}
#delete_group_option input[type="submit"]:hover {
	background:red;
	border-color:red;
	color:white;
}



#logbrowserSearchform {
	padding: 10px;
	background-color: #dedede;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}

.log_entry {
	width: 699px;
	font-size: 80%;
	background:white;
	margin:0 10px 5px 10px;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	border:1px solid white;
}
.log_entry td {
}

.log_entry_user {
	width: 120px;
}

.log_entry_time {
	width: 210px;
	padding:2px;
}

.log_entry_item {
	
}

.log_entry_action {
	width: 75px;
}
/* new members page */
.members .search_listing {
	border:2px solid #cccccc;
	margin:0 0 5px 0;
}
.members .search_listing:hover {
	background:#dedede;
}
.members .group_count {
	font-weight: bold;
	color: #666666;
	margin:0 0 5px 4px;
}
.members .search_listing_info {
	color:#666666;
}

.members .profile_status {
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	background:#bbdaf7;
	line-height:1.2em;
	padding:2px 4px;
}
.members .profile_status span {
	font-size:90%;
	color:#666666;
}
.members  p.owner_timestamp {
	padding-left:3px;
}
.members .pagination {
	border:2px solid #cccccc;
	margin:5px 0 5px 0;
}


#memberssearchform {
	border-bottom: 1px solid #cccccc;
	margin-bottom: 10px;
}
#memberssearchform input[type="submit"] {
	padding:2px;
	height:auto;
	margin:4px 0 5px 0;
}



/*-------------------------------
MESSAGING PLUGIN
-------------------------------*/
#messages {
	margin:0 10px 0 10px;
}
.actiontitle {
	font-weight: bold;
	font-size: 110%;
	margin: 0 0 10px 0;
}
#messages .pagination {
	margin:5px 0 5px 0;
}
#messages input[type="checkbox"] {
	margin:0;
	padding:0;
	border:none;
}
.messages_buttonbank {
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	background:white;
	margin:5px 10px;
	padding:5px;
	text-align: right;
}
.messages_buttonbank input {
	margin:0 0 0 10px;
}
.messages_buttonbank input[type="button"] {
	font: 12px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #4690D6;
	background:#dddddd;
	border: 1px solid #999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	height: 25px;
	padding: 2px 6px 2px 6px;
	margin:0 0 0 10px;
	cursor: pointer;
}
.messages_buttonbank input[type="button"]:hover {
	background: #0054a7;
	border: 1px solid #0054a7;
	color:white;
}

#messages td {
	text-align: left;
	vertical-align:middle;
	padding: 5px;
}
#messages .message_sent {
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
	margin-bottom: 5px;
	background: white;
	border:1px solid #cccccc; 	
}
#messages .message_notread {
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
	margin-bottom: 5px;
	background: #F7DAD8;
	border:1px solid #ff6c7c; 
}
#messages .message_read {
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
	margin-bottom: 5px;
	background: white;
	border:1px solid #cccccc; 
}
#messages .message_notread td {

}
#messages .message_read td {

}

#messages .delete_msg a {
	display:block;
	cursor: pointer;
	width:14px;
	height:14px;
	margin:0;
	background: url("http://elgg.mine.nu/_graphics/icon_customise_remove.png") no-repeat right 0;
	text-indent: -9000px;
	float:right;
}
#messages .delete_msg a:hover {
	background-position: right -16px;
}
/* IE6 */
* html #messages .delete_msg a { background-position: right 4px; }
* html #messages .delete_msg a:hover { background-position: right 4px; } 

#messages .usericon,
#messages .groupicon {
	float: left;
	margin: 0 15px 0 0;
}

#messages .msgsender {
	color:#666666;
	line-height: 1em;
	margin:0;
	padding:0;
	float:left;
}
#messages .msgsender small {
	color:#AAAAAA;
}


#messages .msgsubject {
	font-size: 120%;
	line-height: 100%;
}

.msgsubject {
	font-weight:bold;
}

.messages_single_icon  {
	float: left;
	width:110px;
}

.messages_single_icon .usericon,
.messages_single_icon .groupicon {
	float: left;
	margin: 0 10px 10px 0;
}

/* view and reply to message view */
.message_body {
	margin-left: 120px;
}
.message_body .messagebody {
	padding:0;
	margin:10px 0 10px 0;
	font-size: 120%;
	border-bottom:1px solid #cccccc;
}

/* drop down message reply form */
#message_reply_form { display:none; }

.new_messages_count {
	color:#666666;
}
/* tinyMCE container */
#message_reply_editor #message_tbl {
	width:680px !important;
}
/* IE6 */
* html #message_reply_editor #message_tbl { width:676px !important;}

#messages_return {
	margin:4px 0 4px 10px;
}
#messages_return p {
	margin:0;
}
.messages_single {
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	margin:0 10px 10px 10px;
	padding:10px;	
}
/* when displaying original msg in reply view */
.previous_message {
    background:#dedede;
	-webkit-border-radius: 5px; 
	-moz-border-radius: 5px;
    padding:10px;
    margin:0 0 20px 0;
}
.previous_message p {
    padding:0;
    margin:0 0 5px 0;
    font-size: 100%;
}




#notificationstable td.sitetogglefield {
	width:50px;
	text-align: center;
	vertical-align: middle;
}
#notificationstable td.sitetogglefield input {
	margin-right:36px;
	margin-top:5px;
}
#notificationstable td.sitetogglefield a {
	width:46px;
	height:24px;
	cursor: pointer;
	display: block;
	outline: none;
}
#notificationstable td.sitetogglefield a.sitetoggleOff {
	background: url(http://elgg.mine.nu/mod/messages/graphics/icon_notifications_site.gif) no-repeat right 2px;
}
#notificationstable td.sitetogglefield a.sitetoggleOn {
	background: url(http://elgg.mine.nu/mod/messages/graphics/icon_notifications_site.gif) no-repeat right -36px;
}







.treeview, .treeview ul { 
	padding: 0;
	margin: 0;
	list-style: none;
}

.treeview ul {
	background-color: white;
	margin-top: 4px;
}

.treeview .hitarea {
	background: url(http://elgg.mine.nu/mod/pages/images/treeview-default.gif) -64px -25px no-repeat;
	height: 16px;
	width: 16px;
	margin-left: -16px;
	float: left;
	cursor: pointer;
}
/* fix for IE6 */
* html .hitarea {
	display: inline;
	float:none;
}

.treeview li { 
	margin: 0;
	padding: 3px 0pt 3px 16px;
}

.treeview a.selected {
	background-color: #eee;
}

#treecontrol { margin: 1em 0; display: none; }

.treeview .hover { color: red; cursor: pointer; }

.treeview li { background: url(http://elgg.mine.nu/mod/pages/images/treeview-default-line.gif) 0 0 no-repeat; }
.treeview li.collapsable, .treeview li.expandable { background-position: 0 -176px; }

.treeview .expandable-hitarea { background-position: -80px -3px; }

.treeview li.last { background-position: 0 -1766px }
.treeview li.lastCollapsable, .treeview li.lastExpandable { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-default.gif); }  
.treeview li.lastCollapsable { background-position: 0 -111px }
.treeview li.lastExpandable { background-position: -32px -67px }

.treeview div.lastCollapsable-hitarea, .treeview div.lastExpandable-hitarea { background-position: 0; }

.treeview-red li { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-red-line.gif); }
.treeview-red .hitarea, .treeview-red li.lastCollapsable, .treeview-red li.lastExpandable { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-red.gif); } 

.treeview-black li { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-black-line.gif); }
.treeview-black .hitarea, .treeview-black li.lastCollapsable, .treeview-black li.lastExpandable { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-black.gif); }  

.treeview-gray li { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-gray-line.gif); }
.treeview-gray .hitarea, .treeview-gray li.lastCollapsable, .treeview-gray li.lastExpandable { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-gray.gif); } 

.treeview-famfamfam li { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-famfamfam-line.gif); }
.treeview-famfamfam .hitarea, .treeview-famfamfam li.lastCollapsable, .treeview-famfamfam li.lastExpandable { background-image: url(http://elgg.mine.nu/mod/pages/images/treeview-famfamfam.gif); } 


.filetree li { padding: 3px 0 2px 16px; }
.filetree span.folder, .filetree span.file { padding: 1px 0 1px 16px; display: block; }
.filetree span.folder { background: url(http://elgg.mine.nu/mod/pages/images/folder.gif) 0 0 no-repeat; }
.filetree li.expandable span.folder { background: url(http://elgg.mine.nu/mod/pages/images/folder-closed.gif) 0 0 no-repeat; }
.filetree span.file { background: url(http://elgg.mine.nu/mod/pages/images/file.gif) 0 0 no-repeat; }

.pagesTreeContainer {
		margin:0;
		min-height: 200px;
}

#pages_page .strapline {
    text-align:right;
    border-top:1px solid #efefef;
    margin:10px 0 10px 0;
    color:#666666;
}
#pages_page .categories {
    border:none !important;
    padding:0 !important;
}

#pages_page .tags {
    padding:0 0 0 16px;
    margin:10px 0 4px 0;
	background:transparent url(http://elgg.mine.nu/_graphics/icon_tag.gif) no-repeat scroll left 2px;
}

#pages_page img[align="left"] {
	margin: 10px 20px 10px 0;
	float:left;
}
#pages_page img[align="right"] {
	margin: 10px 0 10px 10px;
	float:right;
}

.pageswelcome p {
	margin:0 0 5px 0;
}

#sidebar_page_tree {
	background:white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
    padding:10px;
    margin:0 10px 10px 10px;
}
#sidebar_page_tree h3 {
	background: none;
	border-top: none;
	border-bottom: 1px solid #cccccc;
	font-size:1.25em;
	line-height:1.2em;
	margin:0 0 5px 0;
	padding:0 0 5px 5px;
	color:#0054A7;
}

/* IE6 */
* html #pages_welcome_tbl { width:676px !important;}

.pages_widget_singleitem_more {
	margin:0 10px 0 10px;
	padding:5px;
	display:block;
	background:white;
   	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;	
}


/* widget */
.thewire-singlepage {
	margin:0 10px 0 10px;
}
.thewire-singlepage .note_body {
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
}
.collapsable_box_content .note_body {
	line-height:1.2em;
}
.thewire-singlepage .thewire-post {
	margin-bottom:5px;
	background:transparent url(http://elgg.mine.nu/mod/thewire/graphics/thewire_speech_bubble.gif) no-repeat right bottom; 
}
.thewire-post {
	background:#cccccc;
	margin-bottom:10px;
}
.thewire-post .note_date {
	font-size:90%;
	color:#666666;
	padding:0;
}
.thewire_icon {
    float:left;
    margin:0 8px 4px 2px;
}
.note_body {
	margin:0;
	padding:6px 4px 4px 4px;
	min-height: 40px;
	line-height: 1.4em;
}
.thewire_options {
	float:right;
	width:65px;
}
.thewire-post .reply {
	font: 11px/100% Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #ffffff;
	background:#999999;
	border: 2px solid #999999;
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px;
	width: auto;
	padding: 0 3px 2px 3px;
	margin:0 0 5px 5px;
	cursor: pointer;
	float:right;
}
.thewire-post .reply:hover {
	background: #4690d6;
	border: 2px solid #4690d6;
	color:white;
	text-decoration: none;
}
.thewire-post .delete_note {
	width:14px;
	height:14px;
	margin:3px 0 0 0;
	float:right;
}
.thewire-post .delete_note a {
	display:block;
	cursor: pointer;
	width:14px;
	height:14px;
	background: url("http://elgg.mine.nu/_graphics/icon_customise_remove.png") no-repeat 0 0;
	text-indent: -9000px;
}
.thewire-post .delete_note a:hover {
	background-position: 0 -16px;
}
/* IE 6 fix */
* html .thewire-post .delete_note a { background-position-y: 2px; }
* html .thewire-post .delete_note a:hover { background-position-y: -14px; }

.post_to_wire {
	background: white;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	margin:0 10px 10px 10px;
	padding:10px;	
}
.post_to_wire input[type="submit"] {
	margin:0;
}

/* reply form */
textarea#thewire_large-textarea {
	width: 664px;
	height: 40px;
	padding: 6px;
	font-family: Arial, 'Trebuchet MS','Lucida Grande', sans-serif;
	font-size: 100%;
	color:#666666;
}
/* IE 6 fix */
* html textarea#thewire_large-textarea { 
	width: 642px;
}

input.thewire_characters_remaining_field { 
	color:#333333;
	border:none;
	font-size: 100%;
	font-weight: bold;
	padding:0 2px 0 0;
	margin:0;
	text-align: right;
	background: white;
}
input.thewire_characters_remaining_field:focus {
	border:none;
	background:white;
}
.thewire_characters_remaining {
	text-align: right;
}


div#calendarmenucontainer {
	position: relative;
}

ul#calendarmenu {
	list-style: none;
	position: absolute;
	top: 0px;
	left: -15px;
}

ul#calendarmenu li {
	float: left;
	border-top: 1px solid #969696;
	border-left: 1px solid #969696;
	border-bottom: 1px solid #969696;
	background-color: #F5F5F5;
}


ul#calendarmenu li.sys_calmenu_last {
	border-right: 1px solid #969696;
}

ul#calendarmenu li a {
	text-decoration: none;
	padding: 4px 12px;
	float: left;
}

ul#calendarmenu li a:hover, ul#calendarmenu li.sys_selected a{
	text-decoration: none;
	padding: 4px 12px;
	float: left;
	color: #FFFFFF;
	background: #3874B7;
}


.river_object_event_calendar_create {
	background: url(http://elgg.mine.nu/mod/event_calendar/images/river_icon_event.gif) no-repeat left -1px;
}
.river_object_event_calendar_update {
	background: url(http://elgg.mine.nu/mod/event_calendar/images/river_icon_event.gif) no-repeat left -1px;
}
#event_list {
	width:440px;
	margin:0;
	float:left;
	padding:5px 0 0 0;
}
#event_list .search_listing {
	border:2px solid #cccccc;
	margin:0 0 5px 0;
}

.events {
	min-height: 300px;
}
blockquote {
    margin:10px;
    border:1px solid #efefef;
    padding:4px;
}

strong {
    font-weight:bold;
}

ul {
   list-style: disc;
}

ol {
  list-style: decimal;
}

/* uploadify/example/css/default.css*/
#fileQueue {
	width: 400px;
	height: 50px;
	overflow: auto;
	border: 1px solid #E5E5E5;
	margin-bottom: 10px;
}
/* uploadify/example/css/uploadify.css*/
.uploadifyQueueItem {
	font: 11px Verdana, Geneva, sans-serif;
	border: 2px solid #E5E5E5;
	background-color: #F5F5F5;
	margin-top: 5px;
	padding: 10px;
	width: 350px;
}
.uploadifyError {
	border: 2px solid #FBCBBC !important;
	background-color: #FDE5DD !important;
}
.uploadifyQueueItem .cancel {
	float: right;
}

.uploadifyProgress {
	background-color: #FFFFFF;
	border-top: 1px solid #808080;
	border-left: 1px solid #808080;
	border-right: 1px solid #C5C5C5;
	border-bottom: 1px solid #C5C5C5;
	margin-top: 10px;
	width: 100%;
}
.uploadifyProgressBar {
	background-color: #0099FF;
	width: 1px;
	height: 3px;
}

.buttonAction{
	cursor:pointer;
}

/* autocompletar */
.suggestionsBox {
		position:relative;
		left: 30px;
		margin: 10px 0px 0px 0px;
		width: 320px;
		height: 200px;
		z-index:5000;
		overflow-x: none;
		overflow-y: scroll;
		background-color: #DFEFFF;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border: 1px solid #0054A7;	
		color: #0054A7;
		font-size: 1em;		
	}
	
	.suggestionList {
		margin: 0px;
		padding: 0px;
	}
	
	.suggestionList div {
		
		margin: 0px 0px 3px 0px;
		padding: 3px;
		cursor: pointer;
		font-size: 1em;
	}
	
	.suggestionList div:hover {
		background-color: #fff;
	}
	
	
	.portal_link{
		cursor:pointer;
	}
/* modal search list*/
#layer1 
		{
			position: absolute;
			left:200px;
			top:100px;
			width:250px;
			background-color:#f0f5FF;
			border: 1px solid #000;
			z-index: 50;
		}
		#layer1_handle 
		{
			background-color:#5588bb;
			padding:2px;
			text-align:center;
			font-weight:bold;
			color: #FFFFFF;
			vertical-align:middle;
		}
		#layer1_content 
		{
			padding:5px;
		}
		#close
		{
			float:right;
			text-decoration:none;
			color:#FFFFFF;
		}	
		
.doublelist {
	font-size: 1em;
	padding: 10px;
}

.doublelist button{
	border: 1px solid #555;
	background: #eee;
	color: #333;
}

.navigationBatchItem {
	float:right;
	margin: 0px 5px;
}

.navigationBatchItems{
	float: right;
	margin: 0px;
	padding: 0px;
	width: 170px;
}

.navigationBatchItems table tr:hover{
	background: #fff;
}

.navigationBatchItems table, .navigationBatchItems td{
	border: 0px;
	padding: 0px;
}

.navigationBatchItems table:hover{
	border: 0px;
	padding: 0px;
}

.batch_option{
	font-size: 1.2em;
	cursor: pointer;
}

.show_number{
	font-size: 1em;
	font-family: Verdana;
	color: #777;
	font-weight: bold;
	text-align: right;
	background: #ddd;
	width: 70px;
}

.show_number:hover{
	font-size: 1em;
	font-family: Verdana;
	color: #eee;
	font-weight: bold;
	text-align: right;
	background: #999;
}

.show_number table, .show_number td{
	border: 0px;
	margin: 0px;
	padding: 0px;
	font-size: 1em;
}

.show_number input{
	padding: 0px;
}


.listboxdictionary{
	padding: 4px;
	position: absolute;
	display: block;
	background:  #9af;
	width: 200px;
}


