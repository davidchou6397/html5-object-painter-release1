<!DOCTYPE html>
<html>
<head>
	<?php
		$imageFolder = 'img/';
		$imageTypes = '{*.jpg,*.JPG,*.jpeg,*.JPEG,*.png,*.PNG,*.gif,*.GIF}';
		$images = glob($imageFolder . $imageTypes, GLOB_BRACE);
		
	?>
  <script src="https://cdn.rawgit.com/konvajs/konva/2.1.7/konva.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <meta charset="utf-8">
  <title></title>
  <style>
    body {
      margin: 0;
      padding: 0;
	  overflow:hidden;
      background-color: #F0F0F0;
    }
	.containerBox {
		border-top: 5px solid black;
	}
  </style>
</head>
<body>
  <div id="container"></div>
  <div id="containerBox" class="containerBox"></div>
  <script type="text/javascript">
    var width = window.innerWidth;
    var height = window.innerHeight*0.92;
	var containerText;
	var containerGroup;
	var containerArr = [];

    var tween = null;
	
	function addContainer(layer, stage) {
		containerGroup = new Konva.Group({
			x: 0,
			y: 0
		});
	
		containerText = new Konva.Text({
			fontSize: 26,
			fontFamily: 'Calibri',
			text: 'Container',
			fill: 'black',
			padding: 10
		});

		var containerRect = new Konva.Rect({
			width: containerText.getWidth(),
			height: document.getElementById('containerBox').style.height,
			fill: '#aaf'
		});
	
		containerGroup.add(containerRect).add(containerText);
		layer.add(containerGroup);
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
            scale: {
                x : scale,
                y : scale
            },
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
	
	function addimg(layer, stage) {
		var allimages = <?php echo json_encode($images); ?>;
		for(var i = 0; i < allimages.length; i++){
			var scale = Math.random();
			var imgPath = allimages[i];
			Konva.Image.fromURL(allimages[i], function(image){
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
	}
	
	var stageContainer = new Konva.Stage({
		container: 'containerBox',
		width: window.innerWidth,
		height: window.innerHeight*0.08
	});
	
	var containerLayer = new Konva.Layer();
	
	addContainer(containerLayer, stageContainer);
	
	stageContainer.add(containerLayer);
	
    var stage = new Konva.Stage({
        container: 'container',
        width: width,
        height: height
    });

    var layer = new Konva.Layer();
    var dragLayer = new Konva.Layer();

    for(var n = 0; n < 10; n++) {
        addStar(layer, stage);
    }
	addimg(layer, stage);
	
    stage.add(layer);
    stage.add(dragLayer);

    // bind stage handlers
    stage.on('mousedown', function(evt) {
        var shape = evt.target;
        shape.moveTo(dragLayer);
        stage.draw();
        // restart drag and drop in the new layer
        shape.startDrag();
    });

    stage.on('mouseup', function(evt) {
        var shape = evt.target;
        shape.moveTo(layer);
        stage.draw();
    });

    stage.on('dragstart', function(evt) {
        
    });

    stage.on('dragend', function(evt) {
        var shape = evt.target;

		console.log(shape.x());
		console.log(shape.y());
		if(shape.y() >= window.innerHeight-containerText.getHeight()){
			console.log("in");
			console.log(shape.id());
			containerArr.push(shape.id());
			shape.remove();
			console.log(containerArr.length);
		}
    });
	
	containerGroup.on('click', function() {
		console.log('you clicked me!');
		// jquery extend function
$.extend(
{
    redirectPost: function(location, args)
    {
        var form = '';
        $.each( args, function( key, value ) {
            form += '<input type="hidden" name="'+key+'" value="'+value+'">';
        });
        $('<form action="'+location+'" method="POST">'+form+'</form>').appendTo('body').submit();
    }
});
		$.redirectPost('test.php', {data: containerArr});

	});
  </script>

</body>
</html>