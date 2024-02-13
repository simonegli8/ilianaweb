function SortTable()
{
 //private attributes
 var t;
 var col=-1;
 var type;
 var dir=1; //sort direction (alternating)
 var length;
 var x;
 var y;

 //public attributes
 this.method = 'quicksort'; //quicksort|bubblesort

 //public methods
 this.sort = function(tableID,sorted_col,sorted_type)
 {
  t=document.getElementById(tableID).tBodies[0];
  length=t.rows.length;
  //if (col==sorted_col) {reverseTable(); return;}
  dir = (col==sorted_col)? (dir?0:1) :0;
  col = sorted_col;
  type = sorted_type;
  var start = new Date().getTime();//time measure starts
  initArrays();
  //this.method='bubblesort';
  switch(this.method)
  {
   case 'bubblesort': bubbleSort(); break;
   case 'quicksort': quickSort(0,length-1); break;
   default: break;
  }
  //updating the table:
  var n=document.createElement('tbody'); //for fast nodes appending
  for (var i=0; i<length; i++)
  {
   var r=y[i].cloneNode(true);
   n.appendChild(r);
   //t.appendChild(y[i]); //this is much slower than cloneNode
  }
  t.parentNode.replaceChild(n,t);
  var end = new Date().getTime();//time measure ends
  return (end-start); // this method returns execution length
  //alert('cas '+(end-start));
 }

 //private methods:

 function bubbleSort()
 {
  var ri,rj,tmp;
  for (var i=0; i<length; i++)
  {
   ri=x[i];
   for (var j=i+1; j<length; j++)
   {
    rj=x[j];
    if ((dir==0 && rj<ri) //compare values upward
     || (dir==1 && rj>ri)) //compare values downward
    {
     //alert(this.method+' swapping rows '+ri+' + '+rj);
     tmp=x[i];//swapping row values
     x[i]=x[j];
     x[j]=tmp;
     tmp=y[i];//swapping row references
     y[i]=y[j];
     y[j]=tmp;
     ri=rj;//updating ri variable for this pass
    }
   }
  }
 }

function quickSort(L,R)
{
 var i,j,xp,tmp;
 i=L; j=R;
 xp=x[Math.floor((L+R)/2)]; /* pivot from the middle of unsorted array */
 do{
  if(dir)
  { //descending sort
   while((x[i]>xp)&&(i<R)) i++;
   while((x[j]<xp)&&(j>L)) j--;
  }
  else
  { //ascending sort
   while((x[i]<xp)&&(i<R)) i++;
   while((x[j]>xp)&&(j>L)) j--;
  }
  if(i<=j){
     //alert('swapping rows '+x[i]+' + '+x[j]);
     tmp=x[i];//swapping row values
     x[i]=x[j];
     x[j]=tmp;
     tmp=y[i];//swapping row references
     y[i]=y[j];
     y[j]=tmp;
   i++;
   j--;
  }
 }while(i<=j);
 if(j>L) quickSort(L,j);
 if(i<R) quickSort(i,R);
}

 function initArrays()
 {
  x = new Array(length);
  y = new Array(length);

  for (var i=0; i<length; i++) //getting values in the column col
  {
   //x[i] = t.rows[i].cells[col].innerHTML; //value on the row i
   x[i] = getCellText(t.rows[i].cells[col]); //value on the row i
   x[i] = x[i].replace(/^\s*/g,''); //remove whitespaces alias ltrim (optional)
   if (type==2) x[i]=parseInt(x[i],10);
   if (type==3) x[i]=parseFloat(x[i]);
   y[i]=t.rows[i]; // references to table rows
  }
 }

 function reverseTable()
 {
  for (var i=1; i<=length; i++)
   t.appendChild(t.rows[length-i]);
 }

 function getCellText(el)
 {
  var str = '';
  var cs = el.childNodes;
  var l = cs.length;
  for (var i = 0; i < l; i++)
  {
   if (cs[i].nodeType==1)//ELEMENT_NODE
   {
    if (cs[i].nodeName=='INPUT' && cs[i].attributes.getNamedItem('type') && cs[i].attributes.getNamedItem('type').value=='text') //html form inputbox
     return cs[i].value ? cs[i].value:'';
    else
     str += getCellText(cs[i]); // div, p, a...
   }
   else if (cs[i].nodeType==3)//TEXT_NODE
    str += cs[i].nodeValue;
  }
  return str;
  //thanks to http://www.kryogenix.org/code/browser/sorttable/
 }
}

function addLoadEvent(func) { // http://www.webreference.com/programming/javascript/onloads/
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}


var sorter = new SortTable();

function new_data_params()
{
	var ai=$('#new_data_params input');
	//alert(ai.length); return;
	var s='';
	for (var i=0; i<ai.length; i++)
	{
		if (ai[i].type=='checkbox' && ai[i].checked)
			s += '&'+ai[i].name;
		if (ai[i].type=='radio' && ai[i].checked)
			s += '&after_update='+ai[i].value;
	}
	var sp=document.getElementById('start_processing');
	sp.setAttribute('href',sp.getAttribute('href')+s);
}

