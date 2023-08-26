
function isDecimal(n){
    if(n == "")
        return false;
    var strCheck = "0123456789";
    var i;
    for(i in n){
        if(strCheck.indexOf(n[i]) == -1)
            return false;
    }
    return true;
}



Validation.add('validate-eprate', 'Numarul de rate nu pare a fi in regula!', function(v) {
	var res = v.split(",");
	if(v=="")return true;
	for(var i=0;i<res.length;i++){
		if(!(/^([0-9])+$/i.test(res[i])) || parseInt(res[i])<2 || parseInt(res[i])>12 || res[i][0]=="0"){
			return false;
		}
	}
    return true;
})

Validation.add('validate-eporder', 'Ordinea ratelor nu pare a fi in regula!', function(v) {
	var res = v.split(",");
	if(v=="")return true;
	for(var i=0;i<res.length;i++){
		if(!(/^([1-5])+$/i.test(res[i])) || parseInt(res[i])<1 || parseInt(res[i])>5){
			return false;
		}
	}
    return true;
})