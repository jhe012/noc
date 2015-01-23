<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    
    <tr class="template-upload fade">
    <td class="preview"><span class="fade"></span></td>
    <td class="name"><span>{%=file.name%}</span></td>
    <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
    {% if (file.error) {  %}
    <td class="error" colspan="2"><span class="label label-danger">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
    {% } else if (o.files.valid && !i) { attachedFiles.push(file); attached_ctr = attachedFiles.length; %}
        
    <td>
        <input id="saved_returns_transaction_type" type="hidden" name="saved_returns_transaction_type" class="tagValues" />          
        <input id="saved_returns_transaction_id" type="hidden" name="saved_returns_transaction_id" class="tagValues" />          
    </td>    
        
    <td class="start">{% if (!o.options.autoUpload) { %}
    <button class="btn btn-primary btn-sm btn-flat" style="display: none;">
    <i class="glyphicon glyphicon-upload"></i>
    <span>{%=locale.fileupload.start%}</span>
    </button>
    {% } %}</td>
    {% } else { %}
    <td colspan="2"></td>
    {% } %}
    <td class="cancel">{% if (!i) { %}
    <button class="btn btn-warning btn-sm btn-flat submit_butt2" onclick="removeAttachedbyID('{%=attached_ctr%}');">  <!--onclick="alert('{%=attached_ctr - 1%}');"> -->
    <i class="glyphicon glyphicon-ban-circle"></i>
    <span>{%=locale.fileupload.cancel%}</span>
    </button>
    {% } %}</td>
    </tr>
    
    {% } %}
    
    
</script>
