
<html lang="en">
<head>
  <meta charset="UTF-8"/>
    <script type="text/javascript" src="/livecon/web/js/jquery.js"></script>
      <title>Livecon upgrade your conference</title>

        <script type="text/javascript" src="/livecon/web/js/jquery.js"></script>
        <script type="text/javascript">
$(function(){

  var test,
    url,
    entities  ={orga:[]},
    oldCookie = document.cookie
    existingOrgaId = 650
    ;


	$.ajaxSetup({  
		async:false,
		complete:function(a,b,c) {
			if(a.status>=300)
				$("body").append("<span style='color:red;'> "+a.statusText+"</span>")
			else if(a.responseText.indexOf('_username') > 0)
				$("body").append("<span style='color:red;'> login default page</span>")
			else
				$("body").append("<span style='color:green;'> "+a.statusText+"</span>")
			$("body").append($("<pre>").text(a.responseJSON ? JSON.stringify(a.responseJSON) : JSON.stringify(a) )); 
		}, 
	}); 


    
	//login 
	test = "test Login";
	url =  'app_dev.php/login/login_check';
	$("body").append("<br/><span style='font-size:20px;font-weight: bold'>"+test+" : "+url+"</h3>");
	$('<form id="login-form" action="'+url+'" method="post">\
      <input name="_username" value="admin"/>\
      <input name="_password" value="admin"/>\
      <input type="checkbox" id="remember_me" name="_remember_me" checked="checked"/>\
    </form> ')
		.appendTo('body')
		.submit(function(e)
		{
	    var postData = $(this).serializeArray();
	    var formURL = $(this).attr("action");
	    $.ajax(
	    {
			    contentType: "application/x-www-form-urlencoded", 
	        url : formURL,
	        type: "POST",
	        data : postData, 
	    });
	    return false;
		})
		.submit().remove();

  test = "test Cookie";  
  assertTrue('cookie not changed !',oldCookie != document.cookie);

	$.ajaxSetup({ contentType: "application/json"});

	//test simple request
  test = "test simple request";
  url  = 'app_dev.php/apiREST/organizations.json';
	$("body").append("<br/><span style='font-size:20px;font-weight: bold'>"+test+" : "+url+"</h3>");
	$.ajax({
	    url: url,  
	});

	//test ACL
	test = "test ACL";
	url =   'app_dev.php/apiREST/organizations/'+existingOrgaId+'.json';
	$("body").append("<br/><span style='font-size:20px;font-weight: bold'>"+test+" : "+url+"</h3>");
	$.ajax({
	    url: url,  
	});

	//test POST
	test = "test POST";
	url =   'app_dev.php/apiREST/organizations.json'; 
	$("body").append("<br/><span style='font-size:20px;font-weight: bold'>"+test+" : "+url+"</h3>");
	$.ajax({
	    type: "POST",
	    url: url,  
	    data: '{"name": "test_orga"}',
      success:function(a,b,c){
        entities['orga'].push(a);
      }
	});

    //test PUT
  if(entities['orga'] && entities['orga'][0])
  {
    test = "test PUT";
    url =   'app_dev.php/apiREST/organizations/'+entities['orga'][0].id+'.json';
    $("body").append("<br/><span style='font-size:20px;font-weight: bold'>"+test+" : "+url+"</h3>");
    $.ajax({
        type: "PUT",
        url: url,  
        data: '{"id": "'+entities['orga'][0].id+'","name": "lol"}',
    });   
    url =   'app_dev.php/apiREST/organizations/'+entities['orga'][0].id+'.json';
    $.ajax({
        url: url,  
        success:function(a,b,c){  
          assertTrue('wrong updated name !',a.name   == "lol");
        }
    }); 
  }

  //TODO test pagination
  //TODO test remove



  function assertTrue(errorMsg,test)
  {
    if (!test)
      $("body").append(" <span style='color:red;'> "+errorMsg+"</span>")
  }

});

        </script>
</head>
<body>
</body>