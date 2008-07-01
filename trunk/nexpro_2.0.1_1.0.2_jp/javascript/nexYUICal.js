var over_cal=false;
        
var nexYUICal=function(){
    var container;    
    var textbox;
    //var over_cal = false;
    var cal1;
    var monthorder=2;
    var yearorder=1;
    var dayorder=3;
    var separator='/';
    
    return{
        init : function (cont,tb){
            container=cont;
            textbox=tb;
            cal1 = new YAHOO.widget.Calendar(cont+tb,cont);
            cal1.selectEvent.subscribe(this.getDate, cal1 , true);
            cal1.renderEvent.subscribe(setupListeners, cal1, true);
            
            YAHOO.util.Event.addListener(textbox, 'focus', this.showCal);
            YAHOO.util.Event.addListener(textbox, 'blur', this.hideCal);
            
            
            cal1.render();
            cal1.hide();
        },//end init function
        
         getDate:function() {
                var calDate = cal1.getSelectedDates()[0];
                var cdate='';
                if(monthorder<=dayorder && monthorder<=yearorder) {  //month first
                    cdate= (calDate.getMonth() + 1);
                    if(dayorder<yearorder){
                        cdate += separator + calDate.getDate() + separator  + calDate.getFullYear() ;
                    }else{
                        cdate += separator + calDate.getFullYear() + separator  + calDate.getDate();
                    }
                    
                }else{
                    if(dayorder<=monthorder && dayorder<=yearorder) {  //day first
                        cdate= calDate.getDate() ;
                        if(monthorder<yearorder){
                            cdate += separator + (calDate.getMonth() + 1) + separator  + calDate.getFullYear() ;
                        }else{
                            cdate += separator + calDate.getFullYear() + separator  + (calDate.getMonth() + 1);
                        }
                        
                    }else{ //its the year first
                        cdate= calDate.getFullYear();
                        if(dayorder<monthorder){
                            cdate += separator + calDate.getDate() + separator  + (calDate.getMonth() + 1) ;
                        }else{
                            cdate += separator + (calDate.getMonth() + 1) + separator  + calDate.getDate();
                        }
                    }
                }
                
                //calDate = (calDate.getMonth() + 1) + '/' + calDate.getDate() + '/' + calDate.getFullYear();
                
                
                YAHOO.util.Dom.get(textbox).value = cdate;
                over_cal=true; 
                YAHOO.util.Dom.setStyle(container, 'display', 'none');
        },

         showCal:function() {
            over_cal=false;
            var darr;
            var y,m,d;
            
            var xy = YAHOO.util.Dom.getXY(textbox);
            var date = YAHOO.util.Dom.get(textbox).value;
            if (date) {
                //using the separator, pick apart the date and put it into the format of yyyy,mm,dd
                darr=date.split(separator);
                //now using the array, we have the date in 3 parts.  use the ordering mechanism to determine which is the year, month and day.
              
                if(monthorder<=dayorder && monthorder<=yearorder) {  //month first
                    m=darr[0];
                    if(dayorder<yearorder){ //second is day
                       d=darr[1];
                       y=darr[2];
                    }else{ //year is 2nd
                        y=darr[1];
                        m=darr[2];
                    }
                    
                }else{
                    if(dayorder<=monthorder && dayorder<=yearorder) {  //day first
                        d=darr[0];
                        if(monthorder<yearorder){//month is second
                            m=darr[1];
                            y=darr[2];
                        }else{ //year is second
                            y=darr[1];
                            m=darr[2];
                        }
                        
                    }else{ //its the year first
                        y=darr[0];
                        if(dayorder<monthorder){ //next the day
                            d=darr[1];
                            m=darr[2];
                        }else{//or next is the month
                             d=darr[2];
                             m=darr[1];
                        }
                    }
                }
                var test=new Date(y+'/'+m+'/'+d);
                var today=new Date();
                if(isNaN(test)){
                    cal1.cfg.setProperty('selected', '2007/1/1');
                    cal1.cfg.setProperty('pagedate', today, true);
                }else{
                    cal1.cfg.setProperty('selected', date);
                    cal1.cfg.setProperty('pagedate', new Date(y+'/'+m+'/'+d), true);
                }
                
               
                cal1.render();
            }
            YAHOO.util.Dom.setStyle(container, 'display', 'block');
            xy[1] = xy[1] + 23;
            YAHOO.util.Dom.setXY(container, xy);
        },

         hideCal:function() {
            if (!over_cal) {
                YAHOO.util.Dom.setStyle(container, 'display', 'none');
            }
        },
        setFormat : function (dorder,morder,yorder,sep){ //provide a few options on how to populate the date....
            monthorder=morder;
            yearorder=yorder;
            dayorder=dorder;
            separator=sep;
        },
        test : function (){
            alert(container);
            alert(textbox);
            
        }//end of test
        
    }//end of return
    
}

function setupListeners(p1,p2,obj) {
    YAHOO.util.Event.addListener(obj.oDomContainer, 'mouseover', overCal);
    YAHOO.util.Event.addListener(obj.oDomContainer, 'mouseout', outCal);
}
        
        
 function overCal() {
    over_cal = true;
}

 function outCal() {
    over_cal = false;
}