<?php

/**
 * Silex hook for the script tag
 */
function property_editor_tool_index_body_end_hook(){
?>
<script type="text/javascript">
   // <![CDATA[
	// add a hook on the silex javascript API "loginSuccess" event
	silexNS.HookManager.addHook("loginSuccess",openTextEditorTool);
	function openTextEditorTool(event)
	{	
/*		if(/msie/.test(navigator.userAgent.toLowerCase()))
		{
			alert("WARNING:\n\nINTERNET EXPLORER is NOT supported FOR EDITING in Silex\nYour website can be viewed but you can not edit texts. We are working on this issue...");
		}
		else
		{*/
			if (!$("#propertyEditorTool").length)
			{
				//$('#toolsContainerDiv').height("20%");
				//$('#flashcontent').height("80%");
				
				// to do : use jsframe class of silex framework ??
				$frame = $('<div id="propertyEditorTool" name="propertyEditorTool" ></div>');
				$('#toolsContainerDiv').append($frame);
				
				// load the base script
				silexNS.SilexApi.addScript(javascriptFolderUrl+"toolbox.min.js");
				silexNS.SilexApi.includeJSSCripts(doAjaxCallTextEditorTool);
			}
//		}
		silexNS.HookManager.removeHook("loginSuccess",openTextEditorTool);
	}
	function doAjaxCallTextEditorTool()
	{
		// ajax load html frame 
		$("#propertyEditorTool").load($rootUrl+"plugins/properties_editor_tool/frame.html");
	}
   // ]]>
</script>
<?php
	return true;
}

/**
 * register the hooks for php Silex API
 */
$hook_manager->add_hook('index-body-end', 'property_editor_tool_index_body_end_hook');
//$silex_hooks_array['index-body-end'][] = Array('hook_name' => 'index-body-end', 'hook_function' => 'property_editor_tool_index_body_end_hook', 'params' => Array());

?>
