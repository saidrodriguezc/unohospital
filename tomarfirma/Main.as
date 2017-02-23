package
{
	import flash.display.MovieClip;
	import fl.events.ColorPickerEvent;
	import flash.display.BitmapData;
	import flash.utils.ByteArray;
	import flash.net.URLLoader;
	import flash.net.URLVariables;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLLoaderDataFormat;
	import flash.net.navigateToURL;
	import flash.events.*;
	import com.adobe.images.*;
	import com.dynamicflash.util.Base64;
	
	public class Main extends MovieClip
	{
		private var da_color:Number=0x000000;
		private var clip:MovieClip;
		private var boo:Boolean=false;
		private var bitmap_data:BitmapData;
		private var byte_array:ByteArray;
		
		public function Main()
		{
			init();
		}
		
		private function init():void
		{
			stage.frameRate=31;
			
			clip=new MovieClip();
			addChild(clip);
			clip.graphics.lineStyle(2,da_color,1);
			clip.graphics.moveTo(mouseX,mouseY);
			
			//send_btn.label='CREA IMMAGINE';
			
			addListeners();
		}
		
		private function addListeners():void
		{
			picker.addEventListener(ColorPickerEvent.CHANGE,setColor);
			picker.addEventListener(ColorPickerEvent.ITEM_ROLL_OVER,setBooTrue);
			picker.addEventListener(ColorPickerEvent.ITEM_ROLL_OUT,setBooFalse);
			
			stage.addEventListener(MouseEvent.MOUSE_DOWN,startDraw);
			stage.addEventListener(MouseEvent.MOUSE_UP,stopDraw);
			
			send_btn.addEventListener(MouseEvent.MOUSE_DOWN,go);
		}
		private function setBooTrue(evt:ColorPickerEvent):void
		{
			boo=true;
		}
		
		private function setBooFalse(evt:ColorPickerEvent):void
		{
			boo=false;
		}
		
		private function setColor(evt:ColorPickerEvent):void
		{
			da_color=evt.target.selectedColor;
			clip.graphics.lineStyle(2,da_color,1);
		}
		
		private function startDraw(evt:MouseEvent):void
		{
			clip.graphics.moveTo(mouseX,mouseY);
			clip.removeEventListener(Event.ENTER_FRAME,drawing);
			if(!picker.hitTestPoint(mouseX,mouseY)&&!boo)
				clip.addEventListener(Event.ENTER_FRAME,drawing);
		}
		
		private function drawing(evt:Event):void
		{
			clip.graphics.lineTo(mouseX,mouseY);
		}
		
		private function stopDraw(evt:MouseEvent):void
		{
			clip.removeEventListener(Event.ENTER_FRAME,drawing);
			
		}
		
		function go(evt:MouseEvent):void
		{
			if(clip.width>2&&clip.height>2)
			{
				var encoding:PNGEncoder;
				//bitmap_data=new BitmapData(stage.stageWidth,stage.stageHeight,false,0xFFFFFF);
				bitmap_data=new BitmapData(550,400,false,0xFFFFFF);
				bitmap_data.draw(clip);
				byte_array=PNGEncoder.encode(bitmap_data);
				
				sendPNG();
			}
		}
		
		private function sendPNG():void
		{
			var encoded:String=Base64.encodeByteArray(byte_array);
			var variables:URLVariables=new URLVariables();
			variables.png=encoded;
			var richiesta:URLRequest=new URLRequest();
			richiesta.url='prova2.php';
			richiesta.method=URLRequestMethod.POST;
			richiesta.data=variables;
			var loader:URLLoader=new URLLoader();
			loader.dataFormat=URLLoaderDataFormat.BINARY;
			addLoaderListeners(loader);
			try 
			{
				loader.load(richiesta);
			} 
			catch (error:Error) 
			{
				trace('Unable to load the document.');
			}
		}
		
		private function addLoaderListeners(d:IEventDispatcher):void
		{
			d.addEventListener(Event.OPEN,inizio);
			d.addEventListener(ProgressEvent.PROGRESS,inProgresso);
			d.addEventListener(Event.COMPLETE,completato);
			d.addEventListener(SecurityErrorEvent.SECURITY_ERROR,securityError);
		}
		
		private function inizio(e:Event):void 
		{
			trace('start');
		}
		
		private function inProgresso(e:ProgressEvent):void 
		{
			trace('percentuale caricata: '+e.bytesLoaded+' totale: '+e.bytesTotal+'\n');
		}
		
		private function completato(e:Event):void
		{
			var vars:URLVariables=new URLVariables(e.target.data);
			var req:URLRequest=new URLRequest('images/'+vars.imageurl);
			navigateToURL(req,'_self');
		}
		
		private function securityError(e:SecurityErrorEvent):void 
		{
			trace('errore sicurezza: '+e+'\n');
		}
	}
}