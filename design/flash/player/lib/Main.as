package lib
{
	import flash.display.*;
	import flash.net.*;
	import flash.events.*;
	import flash.xml.*;
	
	public class Main 
	{
		private static const PRELOADED_SLIDES:uint = 2;
		private static const SIMILAR_LIMIT:uint = 2;
		
		private var timeline:*;
		private var slidesContainer:Sprite;
		private var id:uint;
		private var totalSlides:uint;
		private var slides:Array;
		private var url:String;
		private var currentSlide:uint;
		private var iface:MovieClip;
		private var lSlide:MovieClip;
		private var lastSlide:MovieClip;
		private var urlSimilar:String;
		private var urlMain:String;
		private var similarPage:uint;
		private var similarXml:XML;
		private var similarPages:uint;
		private var similarChunks:Object;
		
		public function Main(timeline:*)
		{
			this.timeline = timeline;
			
			id = parseInt(timeline.stage.loaderInfo.parameters.id);
			totalSlides = parseInt(timeline.stage.loaderInfo.parameters.totalSlides);
			url = timeline.stage.loaderInfo.parameters.url;
			urlSimilar = timeline.stage.loaderInfo.parameters.urlSimilar;
			urlMain = timeline.stage.loaderInfo.parameters.urlMain;
			
			similarChunks = new Object();
			slides = new Array();
			
			slidesContainer = new Sprite();
			timeline.addChild(slidesContainer);
			
			iface = new Interface();
			timeline.addChild(iface);
			
			gotoFirst();
			
			var btn_prev:SimpleButton = iface.btn_prev;
			var btn_next:SimpleButton = iface.btn_next;
			
			timeline.addChild(btn_prev);
			timeline.addChild(btn_next);
			
			btn_prev.addEventListener(MouseEvent.CLICK, onPrevious);
			btn_next.addEventListener(MouseEvent.CLICK, onNext);
		}
		
		private function loadSlide(slide:uint)
		{
				var loader:Loader = new Loader();
				var req:URLRequest = new URLRequest(url + "/" + id + "/" + slide + ".jpg");
				
				slides[slide - 1] = loader;
				loader.load(req);
		}
		
		private function removeSlide(slide:uint):void
		{
			slides[slide - 1] = null;
		}
		
		private function onNext(e:MouseEvent):void
		{
			if(currentSlide + 1 > totalSlides)
			{
				showSimilar();
				getSimilar();
				
				return;
			}
			
			showSlide(currentSlide + 1);
			
			preloadSlides(true);
			
			/*if(currentSlide == totalSlides)
			{
				iface.btn_next.visible = false;
			}
			*/
			if(currentSlide > 1)
			{
				iface.btn_prev.visible = true;
			}
		}
		
		private function onPrevious(e:MouseEvent):void
		{
			showSlide(currentSlide - 1);
			
			preloadSlides(false);
			
			if(currentSlide == 1)
			{
				iface.btn_prev.visible = false;
			}
			
			if(currentSlide < totalSlides)
			{
				iface.btn_next.visible = true;	
			}
		}
		
		private function showSlide(slide:uint):void
		{
			if(currentSlide > 0)
			{
				slidesContainer.removeChild(slides[currentSlide - 1]);
			}
			
			slidesContainer.addChild(slides[slide - 1]);
			currentSlide = slide;
		}
		
		private function getSimilar():void
		{
			 var similarReq:URLRequest = new URLRequest(urlSimilar +"&id=" + id);
			 var loader:URLLoader = new URLLoader(similarReq);
			
			 loader.addEventListener(Event.COMPLETE, similarCompleted);
			
		}
		
		private function similarCompleted(event:Event):void 
		{
			try
			{
				similarXml = new XML(event.target.data);
			}
			catch(e:Error)
			{
				return;
			}
			
			var count:uint = similarXml.presentation.length();
		
			if(count == 0)
			{
				similarPages = 0;
				toggleSimilarButtons();
				return;	
			}
			
			similarPages = Math.ceil(count / SIMILAR_LIMIT);
			
			if(similarPages < 1)
			{
				similarPages = 1;	
			}
			
			similarPage = 1;
			toggleSimilarButtons();
			loadSimilarPage(similarPage);
		}
		
		private function onSimilarClick(e:MouseEvent):void
		{
			var req:URLRequest = new URLRequest(e.target.clickUrl);
			navigateToURL(req, "_self");
		}
		
		private function onShareClick(e:MouseEvent):void
		{
			var req:URLRequest = new URLRequest(urlMain + "share/"+id+"#share");
			navigateToURL(req, "_blank");
		}

		private function showSimilar():void
		{
				iface.btn_next.visible = false;
				iface.btn_prev.visible = false;
				
				if(!lastSlide)
				{
					lastSlide = new last_slide();
					lastSlide.prev_sim.addEventListener(MouseEvent.CLICK, onSimPrev);
					lastSlide.next_sim.addEventListener(MouseEvent.CLICK, onSimNext);
					
				}
				else
				{
					clearChunks();
				}
				
				timeline.addChild(lastSlide);
				lastSlide.prev_sim.visible = false;
				lastSlide.next_sim.visible = false;
				
				lastSlide.replay_but.addEventListener(MouseEvent.CLICK, gotoFirst);
		}
		
		private function onSimPrev(e:MouseEvent):void
		{
			if(similarPage <= 1)
			{
				return;	
			}
			
			similarPage--;
			
			loadSimilarPage(similarPage);
			toggleSimilarButtons();
		}
		
		private function onSimNext(e:MouseEvent):void
		{
			if(similarPage >= similarPages)
			{
					return;
			}
			
			similarPage++;
			
			loadSimilarPage(similarPage);
			toggleSimilarButtons();
			
		}
		
		private function toggleSimilarButtons():void
		{
			if(similarPages == 0 || similarPage >= similarPages)
			{
				lastSlide.next_sim.visible = false;	
			}
			else
			{
				lastSlide.next_sim.visible = true;
			}
			
			if(similarPages > 0 && similarPage > 1)
			{
				lastSlide.prev_sim.visible = true;	
			}
			else
			{
				lastSlide.prev_sim.visible = false;	
			}
		}
		
		private function clearChunks():void
		{
			if(similarChunks[1])
			{
					lastSlide.removeChild(similarChunks[1]);
					similarChunks[1] = null;
			}
		
			if(similarChunks[2])
			{
					lastSlide.removeChild(similarChunks[2]);
					similarChunks[2] = null;
			}	
		}
		
		private function loadSimilarPage(page:uint):void
		{	
				if(similarPages == 0)
				{
					return;	
				}
				
				clearChunks();
				
				var offset:uint = (page - 1) * SIMILAR_LIMIT;
				var chunk1:XML = similarXml.presentation[offset];
				loadSimilarChunk(chunk1, 1);
				
				if(offset + 1 < similarXml.presentation.length())
				{
					var chunk2:XML = similarXml.presentation[offset + 1];
					loadSimilarChunk(chunk2, 2);
				}
		}
		
		private function loadSimilarChunk(chunk:XML, pos:uint):void
		{
			similarChunks[pos] = new content_main();
			var c:MovieClip = similarChunks[pos];
			
			lastSlide.addChild(c);
			var baseY:Number = 60;
			var marginY:Number = 7;
			
			c.y = baseY + ((pos - 1) * marginY) + ((pos - 1) * c.height);
			c.x = 60;
			
			c.title.text = chunk.title;
			c.description.text = chunk.description;
			
			var loader:Loader = new Loader();
			loader.load(new URLRequest(chunk.thumb));
			
			c.pic.addChild(loader);
			
			c.buttonMode = true;
			c.useHandCursor = true;
			c.mouseChildren = false;
			
			c.addEventListener(MouseEvent.CLICK, onSimilarClick);
			var c2:Object = c as Object;
			c2.clickUrl = chunk.url;
			
			lastSlide.send_but.addEventListener(MouseEvent.CLICK, onShareClick);
		}
		
		private function gotoFirst(e:MouseEvent = null):void
		{
			if(e != null)
			{
				lastSlide.replay_but.removeEventListener(MouseEvent.CLICK, gotoFirst);
				timeline.removeChild(lastSlide);
			}
			
			iface.btn_prev.visible = false;
			iface.btn_next.visible = true;
			
			var preloadSlides:uint = (Main.PRELOADED_SLIDES + 1 <= totalSlides) ? Main.PRELOADED_SLIDES + 1 : totalSlides;
			
			for(var x:uint = 0; x < preloadSlides; x++)
			{
				loadSlide(x + 1);
			}
			
			showSlide(1);
		}
		
		private function preloadSlides(isForward:Boolean):void
		{
			if(isForward)
			{
				if(currentSlide + Main.PRELOADED_SLIDES <= totalSlides)
				{
					loadSlide(currentSlide + Main.PRELOADED_SLIDES);
				}
				
				if(currentSlide - Main.PRELOADED_SLIDES  > 1)
				{
					removeSlide(currentSlide - Main.PRELOADED_SLIDES - 1);
				}
			}
			else
			{
				if(currentSlide - Main.PRELOADED_SLIDES > 0)
				{
					loadSlide(currentSlide - Main.PRELOADED_SLIDES);
				}
				
				if(currentSlide + Main.PRELOADED_SLIDES + 1 <= totalSlides)
				{
					removeSlide(currentSlide + Main.PRELOADED_SLIDES + 1);
				}
			}
		}
		
		
	}
}