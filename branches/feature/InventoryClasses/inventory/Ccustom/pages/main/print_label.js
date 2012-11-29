// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011 PhreeSoft, LLC             |
// | http://www.PhreeSoft.com                                        |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/inventory/custom/pages/main/print_label.js
//

var sleepCounter = 0;
   
function print(tempArtId, tempPrice, tempDescr, qty ) {
	var applet = document.jZebra;
	tempStr = tempDescr.split(15);      
	// Send characters/raw commands to applet using "append"
	// Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
//printer is 300 dpi is gelijk aan 12 dots per mm 
	applet.append("N"+"\n");
	applet.append("Q120,24"+"\n");
	applet.append("S1"+"\n");
	applet.append("D5"+"\n");
	applet.append("JF"+"\n");	    	        
	applet.append("A250,24,0,3,1,1,N,\""+tempArtId+"\"\n");
	applet.append("A250,52,0,3,1,1,N,\"C\"\n");
	applet.append("LO248,66,15,1"+"\n");
	applet.append("LO248,70,15,1"+"\n");
	applet.append("A275,52,0,3,1,1,N,\""+tempPrice+"\"\n");
	applet.append("A498,12,0,1,1,1,N,\""+tempDescr.substr(0,15)+"\"\n");
	if(tempDescr.length >15)applet.append("A498,36,0,1,1,1,N,\""+tempDescr.substr(15,15)+"\"\n");
	if(tempDescr.length >31)applet.append("A498,60,0,1,1,1,N,\""+tempDescr.substr(31,15)+"\"\n");

	
	applet.append("ZB"+"\n");
	var amount = prompt("Hoeveel labels wil je",qty);
	if (amount==null || amount==""){
		return;
	}else if( amount == 1) {
		applet.append("P1"+"\n");
	}else{
		applet.append("P"+amount+"\n");
	}
	
	
	applet.append("\n");   
            
	// Send characters/raw commands to printer
	applet.print();
	 
	 
	monitorPrinting();
  
}

function monitorPrinting() {
	  var applet = document.jZebra;
	  if (applet != null) {
	    if (!applet.isDonePrinting()) {
	      window.setTimeout('monitorPrinting()', 1000);
	    } else {
	      var e = applet.getException();
	      if (e != null) {
		    alert("Exception occured: " + e.getLocalizedMessage());

		  }
	    }
	  } else {
		alert("Error: Java label printing applet not loaded!");
	  }
	}
      
 