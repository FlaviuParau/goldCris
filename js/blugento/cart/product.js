function changePurchaseType(){
    var type = document.getElementById("purchase-type").value;

    if(type == 1){
        document.getElementById("company-fields").style.display = "none";
    }else{
        document.getElementById("company-fields").style.display = "block";
    }
}