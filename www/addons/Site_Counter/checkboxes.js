//Custom JavaScript Functions by Shawn Olson
//Copyright 2006-2008
//http://www.shawnolson.net/a/639/select-all-checkboxes-in-a-form-with-javascript.html

function checkUncheckAll(theElement)
{
	for(i=0; i<theElement.form.length; i++)
		if (theElement.form[i].type == 'checkbox')
			theElement.form[i].checked = theElement.checked;
}

function checkUncheckAllt(theElement)
{
	for(i=0; i<theElement.form.length; i++)
		if ((theElement.form[i].type == 'checkbox')  
		&& (theElement.form[i].name != 'ip_filter_screen'))
			theElement.form[i].checked = theElement.checked;
}

function collect_files(form)
{
	var inputs=form.getElementsByTagName('input');
	var n='';
	for (x in inputs)
	{
		if (inputs[x].type=='checkbox' && inputs[x].checked && !inputs[x].disabled && inputs[x].name!='cb_checkall')
		{
			n = inputs[x].name; //cb$i
			//alert(n);
			n = 'l'+ n.substr(1); //change identifier to lb$i
			form.todelete.value += document.getElementById(n).innerHTML +';';
			inputs[x].checked = false;
		}
	}
	//alert(form.todelete.value);
	return form.todelete.value==''? false:true;
}

