$(document).ready(SetClicks);

function SetClicks(){

//for requested pages table
$("#rp_table").delegate('th,td','mouseover mouseleave', function(e) {
  if (e.type == 'mouseover') {
    var uan=document.getElementById('user_agent_name');
    var x=$(this).parents('table').find('th');
    var i=$(this).index();
    var t= (this.nodeName=='TD')? x[i-1].getAttribute('title'): x[i].getAttribute('title');
    uan.innerHTML=t;
    if (this.nodeName=='TD' && i>2 && i<16)
        this.setAttribute('title',parseInt(' '+this.getAttribute('title'))+' / '+t);
    $(this).addClass('hover');
  }
  else
  {
    //if (this.nodeName=='TD')
      //this.removeAttribute('title');
    $(this).removeClass('hover');
  }
});

$("#rp_table tbody td").click(function() {
 //hilite cells on click
 if ($(this).hasClass("hover1"))
   {$(this).removeClass("hover1");}
 else
   {$(this).addClass("hover1");}
});

} //SetClicks - http://css-tricks.com/row-and-column-highlighting/

function SwapDayTal(link)
{
  var title;
  $('#rp_table tbody:first tr').each(function(){//rows
    //s='';
    $(this).find('td:gt(2):lt(13)').each(function(){//filtered cells
      //s+=this.innerHTML;
      title=parseInt(' '+this.getAttribute('title'));
      this.setAttribute('title',this.innerHTML);
      this.innerHTML = title;
    });
    //alert(s+this.toSource());
  });
  title=link.getAttribute('title');
  link.setAttribute('title',link.innerHTML);
  link.innerHTML=title;
  $('#rp_table tbody:eq(1) tr:first td').each(function(i){
    //alert(i+':'+this.innerHTML);//bottom line
    if (i>2 && i<16){
      var title=parseInt(' '+this.getAttribute('title'));
      this.setAttribute('title',this.innerHTML);
      this.innerHTML = title;
    }
  });
}
