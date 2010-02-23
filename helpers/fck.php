<?php
class FckHelper extends HtmlHelper {
    function load($id, $toolbar = 'Default', $width=575, $height=400) {
        //$did = Inflector::camelize(str_replace('/', '_', $id));
		$did = $id;
        $js = $this->webroot.'js/';
        return<<<FCK_CODE
<script type="text/javascript">
fckLoader_$did = function () {
    var bFCKeditor_$did = new FCKeditor('$did');
    bFCKeditor_$did.BasePath = '$js';
    bFCKeditor_$did.ToolbarSet = '$toolbar';
 	bFCKeditor_$did.Width = '$width';
	bFCKeditor_$did.Height = '$height';
    bFCKeditor_$did.ReplaceTextarea();
}
fckLoader_$did();
</script>
FCK_CODE;
    }
    function fileBrowserInput($fieldName, $htmlAttributes = array(), $return = false) {
        $output = $this->input($fieldName, $htmlAttributes, $return);
        if (!isset($htmlAttributes['id'])) {
            $htmlAttributes['id'] = $this->model . Inflector::camelize($this->field);
        }
        $output .= '<script type="text/javascript">';
        $output .= "//<![CDATA[\n";
        $output .= "function openFileBrowser(id){\n";
        $output .= "var fck = new FCKeditor(id);\n";
        $output .= "fck.BasePath = '".$this->webroot."js/'\n";
        $output .= "var url = fck.BasePath + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php';\n";
        $output .= "var sOptions = 'toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes';\n";
        $output .= "sOptions += ',width=640';\n";
        $output .= "sOptions += ',height=480';\n";
        $output .= "window.SetUrl = function(fileUrl){\n";
        $output .= "\$(id).value = fileUrl;\n";
        $output .= "}\n";
        $output .= "var oWindow = window.open( url, 'FCKBrowseWindow', sOptions ) ;\n";
        $output .= "}\n";
        $output .= "//]]>\n";
        $output .= '</script>';
        $output .= '<a href="#" onclick="openFileBrowser(\''.$htmlAttributes['id'].'\'); return false;">select an image...</a>';
        return $output;
    }
}
?>