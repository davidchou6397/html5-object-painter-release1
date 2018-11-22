<!DOCTYPE html>
<html>
<head>
	<?php
if(isset($_POST)){
    if(isset($_POST['data'])){
	$containerArr = $_POST['data'];
    
}}
?>
  <script src="https://cdn.rawgit.com/konvajs/konva/2.1.7/konva.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://apis.google.com/js/platform.js" async defer></script>
  <script>
	
        window.fbAsyncInit = function () {
            FB.init({
                appId: '2099267457067447',
                cookie: true,
                xfbml: true,
                version: 'v3.0'
            });
        };
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
  <meta charset="utf-8">
  <title></title>
  <style>
    body {
      margin: 0;
      padding: 0;
      overflow: hidden;
      background-color: #F0F0F0;
    }
	/* Split the screen in half */
.split {
  height: 100%;
  width: 50%;
  position: fixed;
  z-index: 1;
  top: 0;
  overflow-x: hidden;
}

/* Control the left side */
.left {
  left: 0;

}

/* Control the right side */
.right {
  right: 0;
  border-left: 5px solid black;
}
/*Share Button Styles*/
        .share-btn {
            padding: 0px 10px;
            width: 25%;
            
            margin: 10px;
            line-height: 44px;
            color: white;
            text-align: center;
            border-radius: 3px;
        }
        .share-btn, .share-btn:hover {
            color: #ffffff;
            text-decoration: none;
        }
        .share-btn.facebook,
        .btn.facebook {
            background: #3b5998;
        }
        .share-btn.facebook:hover, .btn.facebook:hover {
            background: #4c70ba !important;
        }
        .share-btn.twitter,
        .btn.twitter {
            background: #00aced;
        }
		.btn.download {
			background: #00FF7F;
		}
        .share-btn.twitter:hover, .btn.twitter:hover {
            background: #21c2ff !important;
        }
  </style>
</head>
<body>
<div class="split left">
	<div id="draw">
	</div>
	<div id="share">
            <button id='shareFB' class='btn share-btn facebook'>Post to Facebook</button>
            <button id='shareTW' class='btn share-btn twitter'>Post to Twitter</button>
			<button id='download' class='btn share-btn download'>Download</button>
	</div>
</div>

<div id="container" class="split right">
</div>
<script type="text/javascript">
Object.defineProperty(Date.prototype, 'YYYYMMDDHHMMSS', {
    value: function() {
        function pad2(n) {  // always returns a string
            return (n < 10 ? '0' : '') + n;
        }

        return this.getFullYear() +
               pad2(this.getMonth() + 1) + 
               pad2(this.getDate()) +
               pad2(this.getHours()) +
               pad2(this.getMinutes()) +
               pad2(this.getSeconds());
    }
});
        // Blob used for Facebook
        var blob;
        // Twitter oauth handler
        $.oauthpopup = function (options) {
            if (!options || !options.path) {
                throw new Error("options.path must not be empty");
            }
            options = $.extend({
                windowName: 'ConnectWithOAuth' // should not include space for IE
                , windowOptions: 'location=0,status=0,width=800,height=400'
                , callback: function () {
                    debugger;
                    //window.location.reload();
                }
            }, options);
            var oauthWindow = window.open(options.path, options.windowName, options.windowOptions);
            var oauthInterval = window.setInterval(function () {
                if (oauthWindow.closed) {
                    window.clearInterval(oauthInterval);
                    options.callback();
                }
            }, 1000);
        };
        // END Twitter oauth handler
        //bind to element and pop oauth when clicked
        $.fn.oauthpopup = function (options) {
            $this = $(this);
            $this.click($.oauthpopup.bind(this, options));
        };
		$('#download').click(function(){
			var dataURL = drawStage.toDataURL();
            downloadURI(dataURL, new Date().YYYYMMDDHHMMSS()+'.png');
		});
		
		function downloadURI(uri, name) {
            var link = document.createElement("a");
            link.download = name;
            link.href = uri;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;
        }
		
        $('#shareFB').click(function () {
            var data = drawStage.toDataURL();
            try {
                blob = dataURItoBlob(data);
            } catch (e) {
                console.log(e);
            }
            FB.getLoginStatus(function (response) {
                console.log(response);
                if (response.status === "connected") {
                    postImageToFacebook(response.authResponse.accessToken, "Canvas to Facebook/Twitter", "image/png", blob, "");
                } else if (response.status === "not_authorized") {
                    FB.login(function (response) {
                        postImageToFacebook(response.authResponse.accessToken, "Canvas to Facebook/Twitter", "image/png", blob, "");
                    }, {scope: 'public_profile,email'});
                } else {
                    FB.login(function (response) {
                        postImageToFacebook(response.authResponse.accessToken, "Canvas to Facebook/Twitter", "image/png", blob, "");
                    }, {scope: 'public_profile,email'});
                }
            });
        });
        $('#shareTW').click(function () {
            var dataURL = drawStage.toDataURL();
            $.oauthpopup({
                path: './auth/twitter.php',
                callback: function () {
                    console.log(window.twit);
                    var data = new FormData();
                    // Tweet text
                    data.append('status', "");
                    // Binary image
                    data.append('image', dataURL);
                    // oAuth Data
                    data.append('oauth_token', window.twit.oauth_token);
                    data.append('oauth_token_secret', window.twit.oauth_token_secret);
                    // Post to Twitter as an update with
                    return $.ajax({
                        url: './auth/share-on-twitter.php',
                        type: 'POST',
                        data: data,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {
							alert("Posted to Twitter.");
                            console.log('Posted to Twitter.');
                            console.log(data);
                        }
                    });
                }
            });
        });
        function postImageToFacebook(token, filename, mimeType, imageData, message) {
            var fd = new FormData();
            fd.append("access_token", token);
            fd.append("source", imageData);
            fd.append("no_story", true);
            // Upload image to facebook without story(post to feed)
            $.ajax({
                url: "https://graph.facebook.com/me/photos?access_token=" + token,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                cache: false,
                success: function (data) {
                    console.log("success: ", data);
                    // Get image source url
                    FB.api(
                        "/" + data.id + "?fields=images",
                        function (response) {
                            if (response && !response.error) {
                                //console.log(response.images[0].source);
                                // Create facebook post using image
                                FB.api( "/me/feed", "POST",
                                    {
                                        "message": "",
                                        "picture": response.images[0].source, // 90-Day Deprecation - https://developers.facebook.com/docs/apps/changelog
                                        // "object_attachment": response.images[0].source, // 90-Day Deprecation - https://developers.facebook.com/docs/apps/changelog
                                        "link": window.location.href,
                                        "name": '',
                                        "description": message,
                                        "privacy": {
                                            value: 'SELF'
                                        }
                                    },
                                    function (response) {
                                        if (response && !response.error) {
                                            /* handle the result */
                                            alert("Posted story to facebook successfully");
                                            console.log("Posted story to facebook");
                                            console.log(response);
                                        } else {
											alert("Posted story to facebook successfully");
                                            console.log("Failed to post story");
                                            console.log(response);
                                        }
                                    }
                                );
                            }
                        }
                    );
                },
                error: function (shr, status, data) {
                    console.log("error " + data + " Status " + shr.status);
                },
                complete: function (data) {
                    console.log('Post to facebook Complete');
                }
            });
        }
        function dataURItoBlob(dataURI) {
            var byteString = atob(dataURI.split(',')[1]);
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], {type: 'image/png'});
        }
    

	var width = window.innerWidth;
    var height = window.innerHeight;

	function addContainer(layer,stage){
		var allimages = '<?php echo $containerArr; ?>';
		var allimgArr = allimages.split(",");
		for(var i = 0; i < allimgArr.length; i++){
			if(allimgArr[i] === "star"){
				addStar(layer,stage);
			}else{
				addimg(layer, stage, allimgArr[i]);
			}
		}
	}
	
	function addimg(layer, stage, imgPath) {
			var scale = Math.random();
			Konva.Image.fromURL(imgPath, function(image){
				// image is Konva.Image instance
				image.setAttrs({
					x: Math.random() * stage.getWidth(),
            y: Math.random() * stage.getHeight(),
            numPoints: 5,
            innerRadius: 30,
            outerRadius: 50,
            fill: '#89b717',
            opacity: 0.8,
            draggable: true,
            shadowColor: 'black',
            shadowBlur: 10,
            shadowOffset: {
                x : 5,
                y : 5
            },
            shadowOpacity: 0.6,
            startScale: scale
                });
			image.id(imgPath);
			layer.add(image);
			layer.draw();
		});
	}
	
	
	
	function addStar(layer, stage) {
        var scale = Math.random();

        var star = new Konva.Star({
            x: Math.random() * stage.getWidth(),
            y: Math.random() * stage.getHeight(),
            numPoints: 5,
            innerRadius: 30,
            outerRadius: 50,
            fill: '#89b717',
            opacity: 0.8,
            draggable: true,
            rotation: Math.random() * 180,
            shadowColor: 'black',
            shadowBlur: 10,
            shadowOffset: {
                x : 5,
                y : 5
            },
            shadowOpacity: 0.6,
            startScale: scale
        });
		star.id("star");
        layer.add(star);
    }
	
	var stageContainer = new Konva.Stage({
		container: 'container',
		width: window.innerWidth*0.5,
		height: window.innerHeight
	});
	
	var containerLayer = new Konva.Layer();
	
	addContainer(containerLayer, stageContainer);
	
	stageContainer.add(containerLayer);
	
	var drawStage = new Konva.Stage({
		container: 'draw',
		width: window.innerWidth*0.5,
		height: window.innerHeight*0.9
	});
	
	var drawLayer = new Konva.Layer();
	
	drawStage.add(drawLayer);
	
	stageContainer.on('mousedown', function(evt) {
        var shape = evt.target;
        shape.moveTo(containerLayer);
        stageContainer.draw();
        // restart drag and drop in the new layer
        shape.startDrag();
    });

    stageContainer.on('mouseup', function(evt) {
        var shape = evt.target;
        shape.moveTo(containerLayer);
        stageContainer.draw();
    });

    stageContainer.on('dragstart', function(evt) {

    });

    stageContainer.on('dragend', function(evt) {
        var shape = evt.target;

		console.log(shape.x());
		if(shape.x() <= 0){
			console.log("in");
			console.log(shape.id());
			if(shape.id() === "star"){
				addStar(drawLayer,drawStage);
				drawLayer.draw();
				shape.remove();
			}else{
				addimg(drawLayer, drawStage, shape.id());
				shape.remove();
			}
			
		}
    });
	
	drawStage.on('mousedown', function(evt) {
        var shape = evt.target;
        shape.moveTo(drawLayer);
        drawStage.draw();
        // restart drag and drop in the new layer
        shape.startDrag();
    });

    drawStage.on('mouseup', function(evt) {
        var shape = evt.target;
        shape.moveTo(drawLayer);
        drawStage.draw();
    });

    drawStage.on('dragstart', function(evt) {

    });

    drawStage.on('dragend', function(evt) {
        var shape = evt.target;

		console.log(shape.x());
		if(shape.x() >= window.innerWidth*0.5){
			console.log("in");
			console.log(shape.id());
			if(shape.id() === "star"){
				addStar(containerLayer,stageContainer);
				containerLayer.draw();
				shape.remove();
			}else{
				addimg(containerLayer, stageContainer, shape.id());
				shape.remove();
			}
			
		}
    });
	
	
</script>
</body>
</html>