$(document).ready(SetClicks);

function SetClicks(){

$("#ds_table").delegate('th,td','mouseover mouseleave', function(e) {
  var overbars = $(this).parent().parent()[0] == $('#ds_table tbody')[1]; //boolean, whether tbody1:hover or not
  if (e.type == 'mouseover') {
    var x=$(this).parents('table').find('th');
    var i=$(this).index();
    var t=x[i].getAttribute('title');
    if (!overbars)
    {
      x[0].innerHTML=t;
      if (this.nodeName=='TD')
        this.setAttribute('title',t);
    }
    else
    {
      x[0].innerHTML=this.getAttribute('title');
    }
    $(this).addClass('hover');
  }
  else
  {
    if (this.nodeName=='TD' && !overbars)
      this.removeAttribute('title');
    $(this).removeClass('hover');
  }
});

$("#ds_table tbody td").click(function() {
 //hilite cells on click
 if ($(this).hasClass("hover1"))
   {$(this).removeClass("hover1");}
 else
   {$(this).addClass("hover1");}
});

$("#ds_table thead th").click(function() {
  //hide bars
  var bodies=$("#ds_table tbody");
  bodies[0].style.display='';
  bodies[1].style.display='none';
});


} //SetClicks - http://css-tricks.com/row-and-column-highlighting/



function ShowBars(c,max)  //c=column index , max=max.value in column
{
  var bodies=$("#ds_table tbody");
  //show bars
  bodies[0].style.display='none';
  bodies[1].style.display='';
  //thanks to http://www.sitepoint.com/forums/showthread.php?172982-table-tbody-collapse
  var rows=$(bodies[0]).find('tr'); //all numberrows
  var bars=$(bodies[1]).find('div.bar'); //all bars
  //alert(bars.toSource());
  if (max==0) max=1;
  var ttl=$('#ds_table').find('th')[c].getAttribute('title');

  for (var i=0; i<bars.length; i++)
  {
   w=$(rows[i]).find('td')[c].innerHTML;
   bars[i].style.width = (100*w/max)+'%';
   bars[i].setAttribute('title', w+' x');
   bars[i].parentNode.setAttribute('title',ttl);
  }
  //alert('a'+bodies.toSource());
}
