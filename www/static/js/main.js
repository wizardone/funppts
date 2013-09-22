
	// Gives focus to input text field
	function give_focus(obj){
	
		obj.value = "";	
	}

	//Add or Remove favourite ppt
	
	function add_remove_fav(id)
	{
		var add = document.getElementById('add_fav_'+id);
		var rem = document.getElementById('remove_fav_'+id);
		if(add.style.display == "block" || add.style.display == "")
		{
			add.style.display = "none";
			rem.style.display = "block";
		}
		else if(add.style.display == "none")
		{
			add.style.display = "block";
			rem.style.display = "none";	
		}
	}
	
	
	//show loader before ppt upload
	
	function show_loader()
	{
		var browser = navigator.appName;
		var code = navigator.appCodeName;
		
		var err = document.getElementById('error');
		var load = document.getElementById('loader');
		var inn = document.getElementById('inner_content');
		
		load.style.display = "block";
		err.style.display = "none";
		if(err)
		{
			if(browser == "Netscape")
			{
				load.style.height = 558 + "px";
				load.style.width = 600 + "px";
				inn.style.marginLeft = 0;
			}
			else if(browser == "Microsoft Internet Explorer")
			{
				load.style.height = 535 + "px";
				load.style.width = 600 + "px";
				inn.style.marginLeft = 0;
				inn.style.marginTop = -5 + "px";
			}
			else if(browser == "Opera")
			{
				load.style.height = 515 + "px";
				load.style.width = 600 + "px";
				inn.style.marginLeft = 0;
			}
		}
	}
	
	function show_loader_small()
	{
		var browser = navigator.appName;
		var code = navigator.appCodeName;
		
		var err = document.getElementById('error');
		var load = document.getElementById('loader_small');
		var inn = document.getElementById('inner_content');
		
		load.style.display = "block";
		err.style.display = "none";
		
		if(err)
		{
			if(browser == "Netscape")
			{
				load.style.height = 190 + "px";
				load.style.width = 580 + "px";
				inn.style.marginLeft = 0;
			}
			else if(browser == "Microsoft Internet Explorer")
			{
				load.style.height = 150 + "px";
				load.style.width = 580 + "px";
				inn.style.marginLeft = 0;
				inn.style.marginTop = -5 + "px";
			}
			else if(browser == "Opera")
			{
				load.style.height = 175 + "px";
				load.style.width = 580 + "px";
				inn.style.marginLeft = 0;
			}
		}
	}
	
	