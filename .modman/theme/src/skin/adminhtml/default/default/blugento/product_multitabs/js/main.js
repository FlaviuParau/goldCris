window.onload = function(){
    displayContentField();
    displaySpecificProductsField()
};

function displayContentField()
{
    var contentType  = document.querySelector('#multitabs_tabs_tab_section_content #multitabs_form > div > table > tbody > tr:nth-child(2) select#content').value,
        contentBlock = document.querySelector('#multitabs_tabs_tab_section_content #multitabs_form > div > table > tbody > tr:nth-child(3)'),
        contentAttr  = document.querySelector('#multitabs_tabs_tab_section_content #multitabs_form > div > table > tbody > tr:nth-child(4)');

    if (contentType == 1) {
        contentBlock.style.display = "table-row";
        contentAttr.style.display = "none";
    } else if (contentType == 2) {
        contentAttr.style.display = "table-row";
        contentBlock.style.display = "none";
    }
}

function displaySpecificProductsField()
{
    var active  = document.getElementById("active_on_products").value,
        specificProd = document.querySelector('#multitabs_tabs_tab_section_content #multitabs_form > div > table > tbody > tr:nth-child(6)');

    if (active == 1) {
        specificProd.style.display = "none";
    } else if (active == 2) {
        specificProd.style.display = "table-row";
    }
}
