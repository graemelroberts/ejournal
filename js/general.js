// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


// Button on the index.php screen (called from Activities block)
function indexereflectgo( p_url )
{
    //alert('in function ereflectgo');
    
    var x = document.getElementById("menuereflect_id").selectedIndex;
    p_value = document.getElementsByTagName("option")[x].value;
    
    var res = p_value.split(':');
    
    //alert('value split: '+res);
    
    var v_ereflect_id = res[0];
    var v_course_id = res[1];
    
    //alert('ereflect_id: '+v_ereflect_id+', course_id: '+v_course_id);
    
    var v_url = p_url+'&id='+v_course_id+'&ereflect_id='+v_ereflect_id;
    
    //alert('in ereflectgo with url '+ v_url );        
    
    if(p_value=='')
    {
        alert('Please select an eReflect Questionnaire from the drop down list');
    }
    else
    {
        location.href = v_url;	
    }
}

function ereflectgo( p_url )
{
    var x = document.getElementById("menuereflect_id").selectedIndex;
    p_value = document.getElementsByTagName("option")[x].value;

    var v_url = p_url+'&ereflect_id='+p_value;
    
    //alert('in ereflectgo with url '+ v_url );    
    
    if(p_value=='')
    {
        alert('Please select an eReflect Questionnaire from the drop down list');
    }
    else
    {
        location.href = v_url;	
    }
}

function usergo( p_url )
{
    var x = document.getElementById("menustudent_id").selectedIndex;
    p_value = document.getElementsByTagName("option")[x].value;

    var v_url = p_url+'&student_id='+p_value;
    
    //alert('in usergo with url '+ v_url );    
    
    if(p_value=='')
    {
        alert('Please select a user from the drop down list');
    }
    else
    {
        location.href = v_url;	
    }
}

function orderbygo( p_url )
{
    var x = document.getElementById("menuorder_by").selectedIndex;
    p_value = document.getElementsByTagName("option")[x].value;

    var v_url = p_url+'&order_by='+p_value;
    
    //alert('in ereflectgo with url '+ v_url );    
    
    if(p_value=='')
    {
        alert('Please select a sequence from the drop down list');
    }
    else
    {
        location.href = v_url;	
    }
}
/*
function getPosition(element)
{
    var e = document.getElementById(element);
    var left = 0;
    var top = 0;

    do{
        left += e.offsetLeft;
        top += e.offsetTop;
    } while(e = e.offsetParent);

    return [left, top];
}
*/

function collexpandpost( p_id, p_action)
{
    //alert('gr In collexpandpost for id '+p_id+', and an action of '+p_action);
    
    if(p_action == 'collapse')
    {
        document.getElementById('div_pd_'+p_id).style.display = 'none';   
        document.getElementById('minus_id_'+p_id).style.display = 'none';   
        document.getElementById('plus_id_'+p_id).style.display = 'block';   
        
        //#div_pp_'.$ejd->id.
        //window.scrollTo(getPosition('#div_pp_'+p_id));                
        /*var top = document.getElementById('#div_pp_'+p_id).offsetTop; //Getting Y of target element
        window.scrollTo(0, top);*/
        
        /*var url = location.href;               //Save down the URL without hash.
        location.href = "#div_pp_"+p_id;                 //Go to the target element.
        alert('location.href : '+location.href);
        history.replaceState(null,null,url);   //Don't like hashes. Changing it back.*/
    }
    else if (p_action == 'expand')
    {
        document.getElementById('div_pd_'+p_id).style.display = 'block';
        document.getElementById('minus_id_'+p_id).style.display = 'block';   
        document.getElementById('plus_id_'+p_id).style.display = 'none';   
        
        //window.scrollTo(getPosition('#div_pp_'+p_id));                
        
        /*var top = document.getElementById('#div_pp_'+p_id).offsetTop; //Getting Y of target element
        window.scrollTo(0, top);*/
        
        /*var url = location.href;               //Save down the URL without hash.
        location.href = "#div_pp_"+p_id;                 //Go to the target element.        
        alert('location.href : '+location.href);        
        history.replaceState(null,null,url);   //Don't like hashes. Changing it back.*/
    }
    
    //alert('id : '+p_id);        
    location.hash = "'"+p_id+"'";
    
}

function openwindow( p_url )
{
    //alert('in open window with url ' +p_url);
    window.open(p_url,"_blank","toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=1000, height=1000");
}

