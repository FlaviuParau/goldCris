window.onload = function(){
    displayCategories();
};

function displayCategories()
{
    var type  = document.querySelector('#productlabels_tabs_general_section_content #productlabels_form > div > table > tbody > tr:nth-child(2) select#type').value,
        cat   = document.querySelector('#productlabels_tabs_general_section_content #productlabels_form > div > table > tbody > tr:nth-child(4)');

    if (type == "promo" || type == "new") {
        cat.style.display = "none";
    } else if (type == "custom") {
        cat.style.display = "table-row";
    }
}
