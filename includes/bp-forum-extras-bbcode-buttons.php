<?php
//just some functions for _ck_'s bbcode buttons - so we can include it on shortcodes and bbcode

function bp_forum_extras_bbcode_buttons() { 
	global $bp;

	$tags = bp_forum_extras_filter_allowedtags();
	
	echo  "<scr"."ipt type='text/javascript' defer='defer'>
		function BBcodeButtons_init() {
		BBcodeButtons.push(new BBcodeButton('ed_bold','B','[b]','[/b]','b','font-weight:bold;','bold'));
		BBcodeButtons.push(new BBcodeButton('ed_italic','I','[i]','[/i]','i','padding-right:7px;font-style:italic;','italics'));
		BBcodeButtons.push(new BBcodeButton('ed_under','U','[u]','[/u]','u','text-decoration:underline;','underline'));
		BBcodeButtons.push(new BBcodeButton('ed_strike','S','[s]','[/s]','s','text-decoration:line-through;','strike through'));
		BBcodeButtons.push(new BBcodeButton('ed_link','URL','','[/url]','a','text-decoration:underline;','make a link')); 
		BBcodeButtons.push(new BBcodeButton('ed_block','&#147;quote&#148;','[quote]','[/quote]','q','padding:0 1px 1px 1px;','quote'));";

	if (isset($tags['img'])) {
		echo "BBcodeButtons.push(new BBcodeButton('ed_img','IMG','[img]','[/img]','m',-1));";
	}
	
	if (bb_get_option( 'bp_sc_bbcode_buttons')) {
		echo "BBcodeButtons.push(new BBcodeButton('ed_ul','UL','[ul]','[/ul]','u','','unordered list')); BBcodeButtons.push(new BBcodeButton('ed_ol','OL','[ol]','[/ol]','o','','ordered list')); BBcodeButtons.push(new BBcodeButton('ed_li','LI','[li]','[/li]','l','','list item'));";	
	} else {
		echo "BBcodeButtons.push(new BBcodeButton('ed_ul','UL','[list]','[/list]','u','','unordered list')); BBcodeButtons.push(new BBcodeButton('ed_ol','OL','[list=1]','[/list]','o','','ordered list')); BBcodeButtons.push(new BBcodeButton('ed_li','LI','[*]','[/*]','l','','list item'));";	
	}
	
	if (isset($tags['center'])) {
		echo "BBcodeButtons.push(new BBcodeButton('ed_center','center','[center]','[/center]','c','','center'));";
	}	

	echo  "BBcodeButtons.push(new BBcodeButton('ed_code','CODE','[code]','[/code]','p','line-height:160%;font-size:80%;letter-spacing:1px;font-family:anadale,serif;','unformatted / code')); BBcodeButtons.push(new BBcodeButton('ed_close','close','','','c',' ','auto-close any tags you left open')); }</scr"."ipt>";

} 


function bp_forum_extras_bbcode_insert_head() {
	global $bp;
	
	echo "<script type='text/javascript' src='" . BP_FORUM_EXTRAS_URL ."/_inc/js/bp-bbcode-buttons.js'></script>";
}

?>