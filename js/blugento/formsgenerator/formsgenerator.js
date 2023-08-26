function getFieldCode(){
    var url           = BASE_URL.replace(/index.php(.*)/i, "formsgenerator/index/createField"),
        label         = document.getElementById("field_label").value,
        inputType     = document.getElementById("field_type").value,
        placeholder   = document.getElementById("field_placeholder").value,
        required      = document.getElementById("field_required").value,
        defaultValue  = document.getElementById("default_value").value,
        values        = document.getElementById("multiple_values").value,
        selectedValue = document.getElementById("selected_value").value,
        checked       = document.getElementById("field_checked").value,
        multiple      = document.getElementById("multiple_file").value,
        formCode      = document.getElementById("fields_code").value,
        comment       = document.getElementById("field_comment").value;

    if(formCode != ""){
        formCode = true;
    }

    url += '?label='+label
        +'&inputType='+inputType
        +'&placeholder='+placeholder
        +'&required='+required
        +'&defaultValue='+defaultValue
        +'&values='+values
        +'&selected='+selectedValue
        +'&checked='+checked
        +'&code='+formCode
        +'&multiple='+multiple
        +'&comment='+comment;

    new Ajax.Request(url, {
        method: 'get',
        onSuccess: function(transport){
            displayFieldCode(transport.responseText);
        },
        onFailure: function(){
            alert(Translator.translate('Something went wrong...'));
        }
    });
}

function displayFieldCode(message){
    if(message == false){
        alert(Translator.translate('\'Field Label\' and \'Field Type\' is required! If you selected \'select\' or \'radio\' type, the \'Values\' field is required.'));
    }else{
        var formCodeField = document.getElementById("fields_code");
        var actualValue = formCodeField.value;

        actualValue = actualValue.replace("</ul>", "");
        actualValue = actualValue.replace("</form>", "");
        formCodeField.value = actualValue+message;
    }

    document.getElementById("field_comment").value = "";
    document.getElementById("field_label").value = "";
    document.getElementById("field_type").selectedIndex = "";
    document.getElementById("default_value").value = "";
    document.getElementById("multiple_values").value = "";
    document.getElementById("field_placeholder").value = "";
    document.getElementById("selected_value").value = "";
    document.getElementById("field_checked").selectedIndex = 1;
    document.getElementById("field_required").selectedIndex = 1;
    document.getElementById("multiple_file").selectedIndex = 1;

    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(3)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(4)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(5)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(6)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(7)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(8)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(9)').style.display = "none";
    document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(10)').style.display = "none";
}

function displayAdminFields(){

    var defaultField         = ["text", "textarea", "hidden"],
        requiredCommField    = ["text", "password", "select", "textarea", "file"],
        placeholderField     = ["text", "textarea"],
        selectedValuesFields = ["select", "radio"],
        actualValue          = document.getElementById("field_type").value;

    var commentField = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(3)'),
        defField     = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(4)'),
        valField     = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(5)'),
        selField     = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(6)'),
        placehField  = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(7)'),
        reqField     = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(8)'),
        checkedField = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(9)'),
        multipleFile = document.querySelector('#formsgenerator_tabs_field_section_content #formsgenerator_form > div > table > tbody > tr:nth-child(10)');

    commentField.style.display = "none";
    defField.style.display     = "none";
    valField.style.display     = "none";
    selField.style.display     = "none";
    placehField.style.display  = "none";
    reqField.style.display     = "none";
    checkedField.style.display = "none";
    multipleFile.style.display = "none";

    if(defaultField.indexOf(actualValue) > -1){
        defField.style.display = "table-row";
    }

    if(requiredCommField.indexOf(actualValue) > -1){
        reqField.style.display     = "table-row";
        commentField.style.display = "table-row";
    }

    if(placeholderField.indexOf(actualValue) > -1){
        placehField.style.display = "table-row";
    }

    if(selectedValuesFields.indexOf(actualValue) > -1){
        valField.style.display = "table-row";
        selField.style.display = "table-row";
    }

    if(actualValue == "checkbox"){
        checkedField.style.display = "table-row";
    }

    if(actualValue == "file"){
        multipleFile.style.display = "table-row";
    }
}

function previewForm(){
    document.getElementById("formsgenerator_preview_text").value = document.getElementById("fields_code").value;

    document.getElementById("form_preview_form").submit();
}