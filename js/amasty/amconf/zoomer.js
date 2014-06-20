var AmZoomer  = Class.create();
AmZoomer.prototype = ({
    imageArray: [],
    initialize: function () {
			var imgPreloader = new Image();
			$(imgPreloader).src = $('mainImage').up().href;
			
			Event.observe(imgPreloader, 'load', (function() {
				this.paramInitialization(imgPreloader);					
				if (this.zoomEnable) {
					this.setObserverForThumbnails();
					this.setObserverForMainImg();
				};
			}).bind(this));
    },
	//
	// paramInitialization()
	// Initialization for all parameters
	//
	paramInitialization: function (imgPreloader) {
	    // Main settings
		var mainImg = $('mainImage');
		this.mainImgComplete = true;
		this.showZoomArea = true;
		this.zoomerClose = true;
		
		this.currentImage = imgPreloader.src;
		this.zoomEnable = Boolean(parseInt(AmZoomerOptions.zoomEnable, 10));
		
		if (Boolean(parseInt(AmZoomerOptions.useTintEffect, 10)) && AmZoomerOptions.zoomType == 'outside') {
		    this.zoomTint = true;
		} else {
		    this.zoomTint = false;
		}
		
		Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
		Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
	    Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7; // ie 8 and higher
		
		// Size from configuration
		this.mainImgWidth = parseInt(AmZoomerOptions.mainImWidth, 10);
		this.mainImgHeight = parseInt(AmZoomerOptions.mainImgHeight, 10);
		this.viewerWidth = AmZoomerOptions.viewerWidth;
		this.viewerHeight = AmZoomerOptions.viewerHeight;
		this.showProductName = Boolean(parseInt(AmZoomerOptions.showProductName, 10));
		this.thumbnailSize = AmZoomerOptions.thumbnailSize;
		
		// Thumbnails settings
		this.thumbContainerLeft = this.thumbContainerTop = 0;
		this.thumbScaleX = this.thumbScaleY = 0;
		this.thumbCenterX = this.thumbCenterY = this.thumbnailSize/2;
		this.thumbPaddingWidth = this.thumbPaddingHeight = 0;
		this.thumbBorder = 1;
		
		// Main image settings
		var border = $('mainImage').style.borderWidth;
		this.mainImgBorder = parseInt(border.slice(0, border.indexOf('px')), 10);
		
		if (mainImg.getAttribute('title') != "") {
		   this.mainImgTitle = mainImg.getAttribute('title');
	    } else {
		   this.mainImgTitle = mainImg.getAttribute('alt'); 				   
		}
		
	    this.widthLimitForMainImg = this.mainImgWidth + 2 * this.mainImgBorder;   
		this.heightLimitForMainImg = this.mainImgHeight + 2 * this.mainImgBorder; 
		
		if (this.mainImgWidth != this.mainImgHeight) {
		    if (this.mainImgHeight < this.mainImgWidth) {
				var coefficient = imgPreloader.height/this.mainImgHeight;
				this.width = imgPreloader.width/coefficient;
				this.mainImgPaddingWidth = (this.mainImgWidth - this.width)/2;
				this.mainImgPaddingHeight = 0;
			} else {
				var coefficient = imgPreloader.width/this.mainImgWidth;
				this.height = imgPreloader.height/coefficient;
				this.mainImgPaddingWidth = 0; 
				this.mainImgPaddingHeight = (this.mainImgHeight - this.height)/2;
			}
		} else {
			if (imgPreloader.width != imgPreloader.height) {
				if (imgPreloader.height > imgPreloader.width) {
					var coefficient = imgPreloader.height/this.mainImgHeight;
					this.width = imgPreloader.width/coefficient;
					this.mainImgPaddingWidth = (this.mainImgWidth - this.width)/2;
					this.mainImgPaddingHeight = 0;

				} else {
					var coefficient = imgPreloader.width/this.mainImgWidth;
					this.height = imgPreloader.height/coefficient;
					this.mainImgPaddingWidth = 0; 
					this.mainImgPaddingHeight = (this.mainImgHeight - this.height)/2;
				}
			} else {
			   var coefficient = imgPreloader.height/this.mainImgHeight;
			   this.width = imgPreloader.width/coefficient;
			   this.height = this.width; 
			   this.mainImgPaddingWidth = this.mainImgPaddingHeight = 0;
			}
		}
		
		if (AmZoomerOptions.zoomType == 'outside') {
			this.scaleX = imgPreloader.width / (this.mainImgWidth - 2 * this.mainImgPaddingWidth);
			this.scaleY = imgPreloader.height / (this.mainImgHeight - 2 * this.mainImgPaddingHeight);
		} else {
		    this.scaleX = imgPreloader.width / this.mainImgWidth;
			this.scaleY = imgPreloader.height / this.mainImgHeight;
		}
		
		this.zoomAreaWidth  = this.viewerWidth/this.scaleX;
		this.zoomAreaHeight = this.viewerHeight/this.scaleY;
		
        this.centerX = this.mainImgWidth/2;
		this.centerY = this.mainImgHeight/2;
		
		this.containerLeft = this.absPosition(mainImg).x + this.mainImgBorder;
		this.containerTop  = this.absPosition(mainImg).y + this.mainImgBorder;
		
		if (Prototype.Browser.IE7) {
			this.containerLeft = Position.page($('mainImage'))[0];
		}
		
		this.chooseZoomerStructure();
		this.allowZoom = this.allowToOpenZoom();
					 
		if (AmZoomerOptions.zoomType == 'outside') {
			this.containerRight = this.containerLeft + this.mainImgWidth + 2 * this.mainImgBorder;
			this.containerBottom = this.containerTop + this.mainImgHeight + 2 * this.mainImgBorder;
			
			var zoomArea = $('zoomArea');
			this.zoomAreaWidth = this.viewerWidth/this.scaleX;
			this.zoomAreaHeight = this.viewerHeight/this.scaleY;
			zoomArea.style.width = this.zoomAreaWidth + 'px';
			zoomArea.style.height = this.zoomAreaHeight + 'px';

			this.showTitle();
		}
	},
    //
	// paramInitializationOnCategoryGrid()
	// Initialization for all parameters
	//
	paramInitializationOnCategoryGrid: function () {
		this.showZoomArea = true;
		this.zoomerClose = true;
		
		this.currentImage = '';
		this.zoomEnable = Boolean(parseInt(AmZoomerOptions.zoomEnable, 10));

		Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
		Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
	    Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7; // ie 8 and higher

		this.viewerWidth = AmZoomerOptions.viewerWidth;
		this.viewerHeight = AmZoomerOptions.viewerHeight;
		this.showProductName = Boolean(parseInt(AmZoomerOptions.showProductName, 10));
		this.thumbnailSize = this.widthLimitForMainImg = this.heightLimitForMainImg = AmZoomerOptions.thumbnailSize;
		
		this.thumbContainerLeft = this.thumbContainerTop = 0;
		this.thumbScaleX = this.thumbScaleY = 0;
		this.thumbCenterX = this.thumbCenterY = this.thumbnailSize/2;
		this.thumbPaddingWidth = this.thumbPaddingHeight = 0;
		this.thumbBorder = 0;
		this.title = '';
		this.zoomAreaWidth = this.viewerWidth/this.scaleX;
		this.zoomAreaHeight = this.viewerHeight/this.scaleY;
		this.insertZoomerStructureOnCategoryGrid(document.body, this.viewerWidth, this.viewerHeight, 0, 0);
	},
	//
	// showTitle()
	// Title forming for zoomer
	//
	showTitle: function () {
	    if (this.showProductName) {
		    var scrollerBorder = $('scroller').style.borderWidth;
			var zoomerTitle = $('zoomerTitle');
		    var zoomTitleHeight = 30;
		    var borderStyle = $('scroller').style.border;
			
			scrollerBorder = parseInt(scrollerBorder.slice(0, scrollerBorder.indexOf('px')), 10);
			zoomerTitle.style.borderRight = borderStyle;
			zoomerTitle.style.borderLeft = borderStyle;
			zoomerTitle.style.borderTop = borderStyle;
			zoomerTitle.style.height = zoomTitleHeight + 'px';
			
			if (AmZoomerOptions.zoomType == 'outside') {
				zoomerTitle.style.width = this.viewerWidth + 'px';
			} else {
			    zoomerTitle.style.width = this.mainImgWidth + 'px';    
			}
			
			zoomerTitle.style.top = - zoomTitleHeight + 'px';
			zoomerTitle.style.lineHeight = zoomTitleHeight + 'px';
			
			if (!AmZoomerOptions.categoryGrid) {
				var mainImg = $('mainImage');
				if (mainImg.getAttribute('title') != "") {
				   zoomerTitle.innerHTML = mainImg.getAttribute('title');
				} else {
				   zoomerTitle.innerHTML = mainImg.getAttribute('alt');      
				}
			} else {
			    zoomerTitle.innerHTML = this.title;
			}
		}			
	},
	//
	// chooseZoomerStructure()
	// Choose position for zoomer
	//
	chooseZoomerStructure: function () {
	    if (AmZoomerOptions.zoomType == 'inside') {
	        this.insertZoomerStructure($('mainImage').up(), this.mainImgWidth, this.mainImgHeight, 0, 0);
		} else {
		    var viewerPositionX = 0;
			var viewerMargin = parseInt(AmZoomerOptions.viewerMargin, 10);
			if (viewerMargin < 0) { viewerMargin = 15; }
			
			if (AmZoomerOptions.viewerPosition == 'left') { 
			    viewerPositionX = - this.widthLimitForMainImg - viewerMargin; 
			} else {
                if (AmZoomerOptions.carouselDirection == 'left') { 
				    viewerPositionX = this.widthLimitForMainImg + viewerMargin + parseInt(this.thumbnailSize, 10) + 10;
                } else {				
			        viewerPositionX = this.widthLimitForMainImg + viewerMargin; 
				}
			}

			this.insertZoomerStructure($('wrapper').up(), this.viewerWidth, this.viewerHeight, 0, viewerPositionX);
			var border = $('zoomArea').style.borderWidth;
		    this.zoomAreaBorder = parseInt(border.slice(0, border.indexOf('px')), 10);
		}
	},
	//
	// chooseZoomerStructureOnCategoryGrid()
	// Choose position for zoomer
	//
    chooseZoomerStructureOnCategoryGrid: function (left, top) {
	    var viewerPositionX = viewerPositionY = 0;
		var viewerMargin = parseInt(AmZoomerOptions.viewerMargin, 10);
		if (viewerMargin < 0) { viewerMargin = 15; }
		var zoomer = $('zoomer');
		
		if (AmZoomerOptions.viewerPosition == 'left') {
			viewerPositionX = left - this.viewerWidth - viewerMargin;
		} else {			
			viewerPositionX = left + AmZoomerOptions.thumbnailSize + viewerMargin; 
		}
		
		viewerPositionY = top - (this.viewerWidth - AmZoomerOptions.thumbnailSize)/2;
		if (viewerPositionY < 0) { viewerPositionY = 0; }
		
		zoomer.style.left = viewerPositionX + 'px';
		zoomer.style.top  = viewerPositionY + 'px';
	},
	//
	// insertZoomerStructure()
	// Insert structure of zoomer
	//
	insertZoomerStructure: function (parent, width, height, top, left) {
	    parent.appendChild(
		    Builder.node('div', {id: 'zoomer', style: 'position: absolute; top: ' + top + 'px; left: ' + left + 'px; display: none; z-index: 8000'},[ 
			    Builder.node('div',{id: 'scroller', className: 'shadow', style: 'position: absolute; top: 0; left: 0; z-index: 2; overflow: hidden; border: 1px solid #ccc; width: ' + width + 'px; height: ' + height + 'px; background-color: white;'},[
				    Builder.node('img', {id:'realImage', alt: '', src: this.currentImage, style: ''})
			    ]),
			    Builder.node('div',{className: 'ieShadow', style: 'position: absolute; top: 0; left: 0px; width: ' + width + 'px; height: ' + height + 'px;'})
			])
		);
        $('zoomer').setOpacity(0);
		
		if (this.showProductName) {
		    var zoomerTitle = Builder.node('div',{id: 'zoomerTitle', className: 'zoomerTitle shadow', style: 'position: absolute; top: 0px; left: 0px; z-index: 3; text-align: center;'})
			 $('zoomer').appendChild(zoomerTitle);
		}
		
	    if (AmZoomerOptions.zoomType == 'outside') {
		    var frag;
		    if (this.zoomTint) {
				var width  = this.mainImgWidth - 2 * this.mainImgPaddingWidth;
				var height = this.mainImgHeight - 2 * this.mainImgPaddingHeight;
				
			    frag = Builder.node('div', {id: 'zoomArea', className: 'cursor', style: 'display: none; overflow: hidden; position: absolute; border: 1px solid #ccc; width: 10px; height: 10px; background-color: white; padding: 0;'},[ 
					       Builder.node('img', {id: 'smallImage', alt: '', src: this.currentImage, style: 'width: ' + width + 'px; height: ' + height + 'px;'})
					   ]);
				
			    var tint = Builder.node('div', {id: 'tint', className: 'tint', style: 'display: none; position: absolute; top: ' + (this.mainImgPaddingHeight + this.mainImgBorder) + 'px; left: ' + (this.mainImgPaddingWidth + this.mainImgBorder) + 'px; width: ' + width + 'px; height: ' + height + 'px;'});
			    $('mainImage').up().appendChild(tint);
				$('tint').setOpacity( 0.5 );
			} else {
		        frag = Builder.node('div', {id:'zoomArea', className: 'tint cursor', style: 'display: none; position: absolute; top: 0; left: 0px; border: 1px solid #ccc; width: 10px; height: 10px;'});
			}
		    $('mainImage').up().appendChild(frag);
		}
	},
	//
	// insertZoomerStructureOnCategoryGrid()
	// Insert structure of zoomer on category grid
	//
	insertZoomerStructureOnCategoryGrid: function (parent, width, height, top, left) {
	    parent.appendChild(
		    Builder.node('div', {id: 'zoomer', style: 'position: absolute; top: ' + top + 'px; left: ' + left + 'px; display: none;'},[ 
			    Builder.node('div',{id: 'scroller', className: 'shadow', style: 'position: absolute; top: 0; left: 0; z-index: 2; overflow: hidden; border: 1px solid #ccc; width: ' + width + 'px; height: ' + height + 'px; background-color: white;'},[
				    Builder.node('img', {id:'realImage', alt: '', src: '', style: ''})
			    ]),
			    Builder.node('div',{className: 'ieShadow', style: 'position: absolute; top: 0; left: 0px; width: ' + width + 'px; height: ' + height + 'px;'})
			])
		);

		if (this.showProductName) {
		    var zoomerTitle = Builder.node('div',{id: 'zoomerTitle', className: 'zoomerTitle shadow', style: 'position: absolute; top: 0px; left: 0px; z-index: 3; text-align: center;'})
			 $('zoomer').appendChild(zoomerTitle);
		}
	},
    //
    // setObserverForMainImg()
    // Applies events depending on type of zoomer
	//
	setObserverForMainImg: function() {
	    var mainImg = $('mainImage');
	    var _this = this;
		
		if (AmZoomerOptions.zoomType == 'inside') {
			mainImg.observe('mouseenter', function(event) {
			    clearTimeout(_this.timerZoomerClose);
				if (_this.allowZoom) {
					_this.timerZoomerOpen = setTimeout(
					   function () {
                           $('mainImage').title = _this.mainImgTitle;					   
                           _this.zoomActivate(event);						   
					   }, 
					   250
					);
				}
			});

			$('wrapper').observe('mouseleave', function(event) {
			    clearTimeout(_this.timerZoomerOpen);
				if (_this.allowZoom) {
					_this.timerZoomerClose = setTimeout(
					   function () {
						   _this.zoomDeactivate();
					   }, 
					   500
					);
				}				
			});
			
		    $('wrapper').observe('mouseenter', (function(event) {
			    clearTimeout(this.timerZoomerClose);
		    }).bind(this));
		   
		} else { // outside	
			$('mainImage').up().style.cursor = 'pointer';
			
		    $('mainImage').up().observe('mouseenter', function(event) {
				if (_this.allowZoom) {
					_this.timerZoomerOpen = setTimeout(
					   function () {				   
					       _this.zoomActivate(event);   			   
					   }, 
					   250
					);
				}				
			});
			
			$('mainImage').up().observe('mouseleave', function(event) {
				clearTimeout(_this.timerZoomerOpen);
				if (_this.allowZoom) {
					_this.timerZoomerClose = setTimeout(
					   function () {
						   _this.zoomDeactivate();
					   }, 
					   350
					); 	
				}				
			});
			
			$('mainImage').up().observe('mouseover', (function(event) {
				clearTimeout(this.timerZoomerClose);
			}).bind(this));
		}
	},
	//
    // move()
	// Calculate movement of scroller
    //
	move: function (event) { 
		if (AmZoomerOptions.zoomType == 'outside') {
		    var x, y, left, top;
			x = Event.pointerX(event) - this.containerLeft - this.mainImgPaddingWidth - this.mainImgBorder;	
			left = x * this.scaleX - this.viewerWidth/2;
			
			y = Event.pointerY(event) - this.containerTop - this.mainImgPaddingHeight - this.mainImgBorder;

			if (y > (this.mainImgHeight - this.zoomAreaHeight / 2 - 2 * this.mainImgPaddingHeight - 2 * this.mainImgBorder) && Prototype.Browser.IE8) {
				top = $('scroller').scrollHeight - 270;
			} else { top = y * this.scaleY - this.viewerHeight / 2; }	
			
		    jQuery('#scroller').stop().animate({scrollTop: top, scrollLeft: left}, 700, 'easeOutCirc');
			
			if (this.zoomTint) {
				var scrollMainImg = $('zoomArea');
				
                scrollMainImg.scrollLeft = x - this.zoomAreaWidth/2;
				
				if (y > (this.mainImgHeight - this.zoomAreaHeight/2 - 2 * this.mainImgPaddingHeight - 2 * this.mainImgBorder) && Prototype.Browser.IE8) {
				    scrollMainImg.scrollTop = scrollMainImg.scrollHeight - 70; 
				} else { 
				    scrollMainImg.scrollTop = y - this.zoomAreaHeight / 2; 
				}
			}
		} else {
		    var x = Event.pointerX(event) - this.containerLeft - this.mainImgBorder;
		    var y = Event.pointerY(event) - this.containerTop - this.mainImgBorder;
			
			var left = x * this.scaleX - this.centerX;
		    var top  = y * this.scaleY - this.centerY;
			
		    jQuery('#scroller').stop().animate({scrollTop: top, scrollLeft: left}, 700, 'easeOutCirc');
		}
	},
	//
    // move()
	// Calculate movement of zoom area
    //
	moveZoomArea: function (event) {		
	    if (this.allowZoom) {
			var zoomArea = $('zoomArea');
            if (this.zoomTint) {
			    var scrollMainImg = $('zoomArea');
			}

			var pointerX = Event.pointerX(event);
			var pointerY = Event.pointerY(event);
           
			if ((pointerX - this.zoomAreaWidth/2) < (this.containerLeft + this.mainImgPaddingWidth + this.mainImgBorder)) {
				zoomArea.style.left = this.mainImgPaddingWidth + this.mainImgBorder - this.zoomAreaBorder + 'px';
			} else if ((pointerX + this.zoomAreaWidth/2) > (this.containerRight - this.mainImgPaddingWidth - this.mainImgBorder)) {
				zoomArea.style.left = this.widthLimitForMainImg - this.mainImgPaddingWidth - this.mainImgBorder - this.zoomAreaBorder - this.zoomAreaWidth + 'px';
			} else {
				zoomArea.style.left = pointerX - this.containerLeft - this.zoomAreaWidth/2 + 'px';
			}
			
			// move top side
			if ((pointerY - this.zoomAreaHeight/2) < (this.containerTop + this.mainImgPaddingHeight + this.mainImgBorder)) {
				zoomArea.style.top = this.mainImgPaddingHeight + this.mainImgBorder - this.zoomAreaBorder + 'px';
			} else if ((pointerY + this.zoomAreaHeight/2) > (this.containerBottom - this.mainImgPaddingHeight - this.mainImgBorder)) {
				zoomArea.style.top = this.heightLimitForMainImg - this.mainImgPaddingHeight - this.zoomAreaHeight - this.mainImgBorder - this.zoomAreaBorder + 'px';
			} else {
				zoomArea.style.top = pointerY - this.containerTop - this.zoomAreaHeight / 2 - this.zoomAreaBorder + 'px';				
			}
			
			this.move(event);
		}
	},
    //
    // setObserverForThumbnails()
    // Loops through anchor tags looking for 'lightbox' references and applies onclick
    // events to appropriate links. You can rerun after dynamically adding images w/ajax.
    //	
    setObserverForThumbnails: function () {
		this.setObserverForThumbnails = Prototype.emptyFunction;
		
		if (AmZoomerOptions.changeMainImgWith != 'disable') {
			var action = 'mouseover';
			
			if (AmZoomerOptions.changeMainImgWith === 'click') {
				action = 'click';
			};
			
			document.observe(action, (function (event) {
				var target = event.findElement('a[rel^=lightbox]') || event.findElement('area[rel^=lightbox]');
				if (target) {
				   event.stop();
				   clearTimeout(this.timerZoomerClose);
				   this.zoomDeactivate();
				   switch (action) {
					   case 'mouseover':
						   var _this = this;
						   if (this.mainImgComplete) {
							   this.timerForThumbnails = setTimeout(
								   function () { 
									   var imageNum = _this.calculateImageNumber(target);
									   if (_this.allowToReloadMainImg(imageNum)) {
										   _this.mainImgComplete = false;
										   _this.setMainImage(imageNum);
										   clearTimeout(_this.timerForThumbnails);
									   };
								   }, 
								   200
							   );
							   $(target).observe('mouseout', (function(event) {
								  clearTimeout(this.timerForThumbnails);
							   }).bind(this));
						   }
					   break;
					   default: //click
						   clearTimeout(this.timerThumbZoomerOpen);
						   var imageNum = this.calculateImageNumber(target);
						   if (this.allowToReloadMainImg(imageNum)) {
							   this.setMainImage(imageNum);
						   };
				   };			   
				};
			}).bind(this));
			
			if (action == 'click' && Boolean(parseInt(AmZoomerOptions.zoomForThumbnailsEnable, 10))) {
				var _this = this;
				$$('a[rel^="lightbox"]').each(function (a) {								
					a.observe('mousemove', function(event) {
						_this.moveZoomThumbnails(event);
					});
					
					a.observe('mouseenter', function(event) {
						var _element = this;
						if (!_this.zoomerClose) {
							_this.zoomDeactivateDirectly();
						} else {
							if (Prototype.Browser.IE7) {
								_this.thumbContainerLeft = Position.page(_element)[0];
							} else {
								_this.thumbContainerLeft = _this.absPosition(_element).x; 
							}
							_this.thumbContainerTop  = _this.absPosition(_element).y;
							
							_this.timerThumbZoomerOpen = setTimeout(
							   function () {
								  _this.showZoomArea = false;
								  
								  var imageNum = _this.calculateImageNumber(_element);
								  var imgPreloader = new Image(); 
								  imgPreloader.src = _this.imageArray[imageNum][0];
								  
								  $('mainImage').title = _this.imageArray[imageNum][1];
								  _this.showTitle();
								  _this.loadCheckThumbnail(imgPreloader, event, _element.down().next());
							   }, 
							   600
							);
						}						
					});

					a.observe('mouseleave', function(event) {
						_element = this;
						clearTimeout(_this.timerThumbZoomerOpen);
						clearTimeout(_this.timerPreloader);
						_element.down().next().hide();
						_this.zoomDeactivate();				
					});
				});
			}
		}
    },
	//
    // setObserverForThumbnailsOnCategoryGrid()
    // Loops through anchor tags looking for 'lightbox' references and applies onclick
    // events to appropriate links. You can rerun after dynamically adding images w/ajax.
    //	
	setObserverForThumbnailsOnCategoryGrid: function (event) {
	    var _this = this;
		$$('a[rel^="lightbox"]').each(function (a) {								
			a.observe('mousemove', function(event) {
				_this.moveZoomThumbnails(event);
			});
			
			a.observe('mouseenter', function(event) {
				var _element = this;
		        
				if (!_this.zoomerClose) {
					_this.zoomDeactivateDirectly();
				} else {
				
					if (Prototype.Browser.IE7) {
						_this.thumbContainerLeft = Position.page(_element)[0];
					} else {
						_this.thumbContainerLeft = _this.absPosition(_element).x; 
					}
					_this.thumbContainerTop  = _this.absPosition(_element).y;
					
					_this.timerThumbZoomerOpen = setTimeout(
					   function () {
					      var leftSide = _this.absPosition(_element).x;
						  var topSide = _this.absPosition(_element).y;
						  _this.chooseZoomerStructureOnCategoryGrid(leftSide, topSide);
						  _this.showZoomArea = false;
						  
						  var rel = _element.rel;
						  var imageNum = rel.split('[')[1];
						  imageNum = imageNum.split(']')[0];

						  var imgPreloader = new Image();
						  imgPreloader.src = AmZoomerOptions.images[imageNum][0];
						  
                          _this.title = _element.down().alt;
						  _this.showTitle();
						  _this.loadCheckThumbnail(imgPreloader, event, _element.down().next());
					   },
					   1000
					);
				}						
			});

			a.observe('mouseleave', function(event) {
			    var _element = this;
				clearTimeout(_this.timerThumbZoomerOpen);
				clearTimeout(_this.timerPreloader);
				_element.down().next().hide();
				_this.zoomDeactivate();				
			});
			
			a.observe('click', function(event) {
			    var _element = this;
				clearTimeout(_this.timerThumbZoomerOpen);
				clearTimeout(_this.timerPreloader);
				_element.down().next().hide();
				_this.zoomDeactivateDirectly();				
			});
		});
	},
    //
    // moveZoomThumbnails()
	// Calculate movement of zoom area for thumbnails
    //
	moveZoomThumbnails: function (event) {
		var x = Event.pointerX(event) - this.thumbContainerLeft - this.thumbPaddingWidth;
		var y = Event.pointerY(event) - this.thumbContainerTop - this.thumbPaddingHeight;
		
		var left = x * this.thumbScaleX - this.viewerWidth/2;
		var top  = y * this.thumbScaleY - this.viewerHeight/2;
		
		jQuery('#scroller').stop().animate({scrollTop: top, scrollLeft: left}, 700, 'easeOutCirc');
	},
	//
    // loadCheckThumbnail()
	// Checking whether the picture from thumbnail has been cached
    //
	loadCheckThumbnail: function (imgPreloader, event, preloader) {
		if (imgPreloader.complete) {
			preloader.hide();
			clearTimeout(this.timerPreloader);
			$('realImage').src = imgPreloader.src;
		    this.calculateThumbnailPaddings(imgPreloader);
			
			if (imgPreloader.width > this.widthLimitForMainImg && imgPreloader.width > this.viewerWidth && imgPreloader.height > this.heightLimitForMainImg && imgPreloader.height > this.viewerHeight) {			
				this.zoomActivate(event);
			}
		} else {
		   preloader.show();
		   var _this = this;
		   this.timerPreloader = setTimeout(function() {
			   _this.loadCheckThumbnail(imgPreloader, event, preloader);
		   }, 2500);
		}
	},
	//
    // calculateThumbnailPaddings()
	// Deside how to load image depending on the wrapper shape
    //
	calculateThumbnailPaddings: function(imgPreloader) {
	    if (imgPreloader.width != imgPreloader.height) {
		    if (imgPreloader.height > imgPreloader.width) {
			    var coefficient = imgPreloader.height/this.thumbnailSize;
				var width = imgPreloader.width/coefficient;
				this.thumbPaddingWidth = (this.thumbnailSize - width)/2;
				this.thumbPaddingHeight = 0;
			} else {
			    var coefficient = imgPreloader.width/this.thumbnailSize;
				var height = imgPreloader.height/coefficient;
				this.thumbPaddingWidth = 0;
				this.thumbPaddingHeight = (this.thumbnailSize - height)/2;
			}
		} else {
		   	this.thumbPaddingWidth = this.thumbPaddingHeight = 0;
		}
		
		this.thumbScaleX = imgPreloader.width / (this.thumbnailSize - 2 * this.thumbPaddingWidth);
		this.thumbScaleY = imgPreloader.height / (this.thumbnailSize - 2 * this.thumbPaddingHeight);
	},
	//
    // zoomActivate()
	// Open zoomer
    //
	zoomActivate: function (event) {
	    var _this = this;
		this.showTitle();
		
		$('realImage').up(1).show();		
		new Effect.Opacity('zoomer', { from: 0.0, to: 1.0, duration: 0.5 });
		if (!AmZoomerOptions.categoryGrid) {
			if (AmZoomerOptions.zoomType == 'outside') {
				if (_this.showZoomArea) {
					$('zoomArea').setOpacity(0);
					$('zoomArea').show();
					
					if (_this.zoomTint) {
						$('tint').show();
						new Effect.Opacity('zoomArea', { from: 0.0, to: 1.0, duration: 0.3 });
					} else {
						new Effect.Opacity('zoomArea', { from: 0.0, to: 0.5, duration: 0.3 });
					}
					
					pointerX = Event.pointerX(event);
					pointerY = Event.pointerY(event);
					if (pointerX > (this.containerLeft + this.mainImgPaddingWidth + this.mainImgBorder) && pointerX < (this.containerRight - this.mainImgPaddingWidth - this.mainImgBorder) && pointerY > (this.containerTop + this.mainImgPaddingHeight + this.mainImgBorder) && pointerY < (this.containerBottom - this.mainImgPaddingHeight - this.mainImgBorder)) {
						$('mainImage').up().style.cursor = 'move'; 
					} else {$('mainImage').up().style.cursor = 'default';}
				}
			} else { $('mainImage').up().style.cursor = 'default'; }
		}
		this.zoomerClose = false;
	},
	//
    // zoomDeactivate()
	// Close zoomer
    //
	zoomDeactivate: function () {
	    var _this = this;
		new Effect.Opacity('zoomer', { from: 1.0, to: 0.0, duration: 0.2, afterFinish: function () { 
			$('zoomer').hide(); 
			_this.showZoomArea = true;
			$('realImage').src = _this.currentImage;
			
			if (AmZoomerOptions.showProductName) { $('zoomerTitle').innerHTML = ''; }
			
			if (AmZoomerOptions.zoomType == 'outside' && !AmZoomerOptions.categoryGrid) {
			    if (_this.showZoomArea) {
					if (_this.zoomTint) {
						$('tint').hide();
					}
					
					$('zoomArea').hide();
				}
			}
		}});
		this.zoomerClose = true;
	},
	//
    // zoomDeactivateDirectly()
	// Close zoomer quickly
    //
	zoomDeactivateDirectly: function () {
	    var _this = this;
		var zoomer = $('zoomer');
		zoomer.hide(); 
		zoomer.style.opactity = 0;
		if (AmZoomerOptions.showProductName) { $('zoomerTitle').innerHTML = ''; }
		this.showZoomArea = true;
		$('realImage').src = _this.currentImage;
			
		if (AmZoomerOptions.zoomType == 'outside' && !AmZoomerOptions.categoryGrid) {
			if (_this.showZoomArea) {
				if (_this.zoomTint) {
					$('tint').hide();
				}
				
				$('zoomArea').hide();
			}
		}
	},
    //
	// allowToReloadMainImg()
	// Checking whether the picture is already opened in mainImage
	//
	allowToReloadMainImg: function(imageNum) {
	    var imgForReplace = this.imageArray[imageNum][0]; 
						
	    var currentImgSrc = this.getLastPartOfSrc($("mainImage").src);
	    var newImgSrc = this.getLastPartOfSrc(imgForReplace);
		
	    if (currentImgSrc != newImgSrc) {
	        return true;
	    } else {
	        return false;
	    };
	},
	//
	// allowToOpenZoom()
	// Checking whether: 
	// 1) real size of a picture > size of the viewer
	// 2) real size of a picture > size of the main image
	//
	allowToOpenZoom: function() {
		var imgPreloader = new Image(); 
		$(imgPreloader).src = this.currentImage;
		
		var width  = imgPreloader.width;
		var height = imgPreloader.height;
		
		if (width > this.widthLimitForMainImg && width > this.viewerWidth && height > this.heightLimitForMainImg && height > this.viewerHeight) {
            if (AmZoomerOptions.zoomType == 'outside') {
				this.zoomAreaMovement = this.moveZoomArea.bind(this);
				$('mainImage').up().observe('mousemove', this.zoomAreaMovement);
			} else {
				this.zoomMovement = this.move.bind(this);
				$('mainImage').up().observe('mousemove', this.zoomMovement);
			}
			
			this.scrollerMovement = this.move.bindAsEventListener(this);
			$('mainImage').up(1).observe('mouseover', this.scrollerMovement);	
		    return true;
		} else {
			if (AmZoomerOptions.zoomType == 'outside') {
			    $('mainImage').up().stopObserving('mousemove', this.zoomAreaMovement);
			} else {
			    $('mainImage').up().stopObserving('mousemove', this.zoomMovement);
			}
			$('mainImage').up(1).stopObserving('mouseover', this.scrollerMovement);
		    return false;
		}
	},
	//
	// getLastPartOfSrc()
	// Get name of image from url
	//
	getLastPartOfSrc: function (str) {
	    return str.substring(str.lastIndexOf('/') + 1).toString();
	},
	//
	// calculateImageNumber()
	// Looking for index number of image in a set
	//
    calculateImageNumber: function(imageLink) {   
        this.imageArray = [];
        this.imageArray = AmZoomerOptions.images;
        
        var imageNum = 0;   
        if ((imageLink.getAttribute("rel") == 'lightbox')) {
            // if image is NOT part of a set, add single image to imageArray
            this.imageArray.push([imageLink.href, imageLink.title]);       
        } else {
            // if image is part of a set
            while (this.imageArray[imageNum][0] != imageLink.href) { imageNum++; }
        };
		return imageNum.toString();
    },
	//
	// setMainImage()
	// Change main image and set it's paddings
	//
    setMainImage: function(imageNum) {        
        AmZoomerOptions.activeImage = imageNum;
        var imgPreloader = new Image(); 
        imgPreloader.src = this.imageArray[imageNum][0];
		this.mainImgTitle = this.imageArray[imageNum][1];
        this.loadCheck(imgPreloader);    
    },
    //
	// loadCheck()
	// Checking whether the picture has been cached
	//
	loadCheck: function (imgPreloader) {
	    var _this = this;
	    if (imgPreloader.complete) {
		   this.currentImage = imgPreloader.src;
           this.allowZoom = this.allowToOpenZoom();
		   
		   var preloadWidth = imgPreloader.width;	
		   var preloadHeight = imgPreloader.height;
		   
		   clearTimeout(this.timerPreloaderMain);
		   $('preloader').hide();

		   new Effect.Opacity('mainImage', {
			   from: 1.0, to: 0.0, 
			   duration: 0.1,
			   afterFinish: function () {
				   _this.setMainImgPaddings(preloadWidth, preloadHeight);
				   new Effect.Opacity('mainImage', {
					   from: 0.0, to: 1.0, 
					   duration: 0.15,
					   afterFinish: function () {
						   _this.mainImgComplete = true;
					   }
				   });
			   }
		   });
	    } else {
		   $('preloader').show();
		   this.timerPreloaderMain = setTimeout(function() {
		       _this.loadCheck(imgPreloader);
		   }, 250);
	    } 
	},
	//
	// setMainImgPaddings()
	// Deside how to load image depending on the wrapper shape
	//
	setMainImgPaddings: function (preloadWidth, preloadHeight) {
		var widthMainImg = this.mainImgWidth;
		var heightMainImg = this.mainImgHeight;
		
        var wrapperShape = 'square';
        if (widthMainImg != heightMainImg) {
		    wrapperShape = 'rectangle'; 		
		}
		
		switch (wrapperShape) {
			case 'square': 
				if (preloadWidth != preloadHeight) {
					if (preloadWidth > preloadHeight) { // width 100%
						this.setFullWidth(true, preloadWidth, preloadHeight);
					} else { // height 100%
						this.setFullWidth(false, preloadWidth, preloadHeight);
					}
				} else { //square img
					this.setFullWidth(true, preloadWidth, preloadHeight);
				}
			break;
			default: // rectangle shape
				if (widthMainImg > heightMainImg) { // height 100%
					this.setFullWidth(false, preloadWidth, preloadHeight);
				} else { // width 100% 		
					this.setFullWidth(true, preloadWidth, preloadHeight);
				};
		};
	},
    //
	// setFullWidth()
	// Set image parameters
	//
	setFullWidth: function (marker, preloadWidth, preloadHeight) {
		var widthLimit = this.widthLimitForMainImg;
		var heightLimit = this.heightLimitForMainImg;
		
		var mainImage = $('mainImage');
		var mainImgStyle = mainImage.style;
		var mainImgParentStyle = mainImage.up().style;
		
		if (marker) { //width 100%
			var coefficient = preloadWidth/widthLimit;
			var mainImgHeight = preloadHeight/coefficient;
			var heightPadding = 0; // for square images
			
			if (this.mainImgWidth == this.mainImgHeight) {
				if (preloadWidth != preloadHeight) { 
					heightPadding = (this.mainImgHeight - mainImgHeight)/2;
				}
			} else {
			    heightPadding = (this.mainImgHeight - mainImgHeight)/2;
			}
			
			var parentHeight = heightLimit - heightPadding;
			mainImgStyle.width = this.mainImgWidth + 'px';
			mainImgStyle.height = preloadHeight/coefficient + 'px';
			
			mainImgParentStyle.width = widthLimit + 'px';
			mainImgParentStyle.height = heightLimit - heightPadding + 'px';
			
			mainImgParentStyle.paddingTop = heightPadding + 'px';
			mainImgParentStyle.paddingLeft = 0;
		} else { // height 100% 
			if (preloadWidth == preloadHeight) { // for square images
				mainImgWidth = mainImgHeight = this.mainImgHeight;
				coefficient = preloadHeight/mainImgHeight;
				var widthPadding = (this.mainImgWidth - mainImgWidth)/2;
				
				var parentWidth = widthLimit - 2 * widthPadding;
				
				mainImgStyle.width = mainImgWidth + 'px';
				mainImgStyle.height = mainImgHeight + 'px';
				
				mainImgParentStyle.width = widthLimit - widthPadding + 'px';
				mainImgParentStyle.height = heightLimit + 'px';
				
				mainImgParentStyle.paddingTop = 0;
				mainImgParentStyle.paddingLeft = widthPadding + 'px';
			} else {
				var coefficient = preloadHeight/heightLimit;
				var mainImgWidth = preloadWidth/coefficient;

				if (mainImgWidth > this.mainImgWidth && this.mainImgWidth > this.mainImgHeight) {
					mainImgWidth = this.mainImgWidth;
					coefficient = preloadWidth/mainImgWidth;
					var mainImgHeight = preloadHeight/coefficient;					
					var heightPadding = (this.mainImgHeight - mainImgHeight)/2;
					var parentHeight = heightLimit - heightPadding;
					mainImgStyle.width = mainImgWidth + 'px';
					mainImgStyle.height = mainImgHeight + 'px';
					
					mainImgParentStyle.width = mainImgWidth + 'px';
					mainImgParentStyle.height = heightLimit - heightPadding + 'px';
					mainImgParentStyle.paddingTop = heightPadding + 'px';
					mainImgParentStyle.paddingLeft = 0;
				} else {
					var widthPadding = (this.mainImgWidth - mainImgWidth)/2;
					var parentWidth = widthLimit - 2 * widthPadding;
					
					mainImgStyle.width = preloadWidth/coefficient + 'px';
					mainImgStyle.height = heightLimit + 'px';
					
					mainImgParentStyle.width = widthLimit - widthPadding + 'px';
					mainImgParentStyle.height = heightLimit + 'px';
					mainImgParentStyle.paddingTop = 0;
					mainImgParentStyle.paddingLeft = widthPadding + 'px';
				}
			}
	    }; 
	    mainImage.src = this.currentImage;
		mainImage.title = this.imageArray[AmZoomerOptions.activeImage][1];
		$('realImage').src = this.currentImage;
		mainImage.up().href = this.currentImage;
		
		if (AmZoomerOptions.zoomType == 'outside') {
		    this.mainImgPaddingWidth  = this.absPosition(mainImage).x - this.absPosition(mainImage.up()).x;	
			this.mainImgPaddingHeight = this.absPosition(mainImage).y - this.absPosition(mainImage.up()).y;
			
			this.scaleX = preloadWidth / (this.mainImgWidth - 2 * this.mainImgPaddingWidth);
			this.scaleY = preloadHeight / (this.mainImgHeight - 2 * this.mainImgPaddingHeight);
			
			if (AmZoomerOptions.zoomType == 'outside') {
				var zoomArea = $('zoomArea');
				this.zoomAreaWidth = this.viewerWidth/this.scaleX;
				this.zoomAreaHeight = this.viewerHeight/this.scaleY;
				zoomArea.style.width = this.zoomAreaWidth + 'px';
				zoomArea.style.height = this.zoomAreaHeight + 'px';
				
				if (this.zoomTint) {
                    var smallImage = $('smallImage');
                    smallImage.src = this.currentImage;
					smallImage.style.width = this.mainImgWidth - 2 * this.mainImgPaddingWidth + 'px';
					smallImage.style.height = this.mainImgHeight - 2 * this.mainImgPaddingHeight + 'px';
					
                    var tint = $('tint');
					tint.style.width = this.widthLimitForMainImg - 2 * this.mainImgPaddingWidth - 2 * this.mainImgBorder + 'px';					
				    tint.style.height = this.heightLimitForMainImg - 2 * this.mainImgPaddingHeight - 2 * this.mainImgBorder + 'px';
                    tint.style.top = this.mainImgPaddingHeight + this.mainImgBorder + 'px';
					tint.style.left = this.mainImgPaddingWidth + this.mainImgBorder + 'px';					
				}
			}
		} else {
		    this.scaleX = preloadWidth / this.mainImgWidth;
			this.scaleY = preloadHeight / this.mainImgHeight;
		}
	},
	//
	// absPosition()
	// Calculate position of elements on a page
	//
	absPosition: function(obj) {
        var x = y = 0;
        while (obj) {
            x += obj.offsetLeft; 
            y += obj.offsetTop;  
            obj = obj.offsetParent;
        }
        return {x:x, y:y};
    }
});

function setStructureForGrid(src)
{
	if (Boolean(AmZoomerOptions.zoomEnable)) {
		document.observe('dom:loaded', function (event){
			var allProducts = $$('a[class^="product-image"]');
			AmZoomerOptions.thumbnailSize = allProducts[0].getWidth();
			allProducts.each(function(a) {
				var child = a.up().getElementsByClassName('price-box')[0];
				var productId = 0;
				if (child) {
					var childNext = child.childElements()[0];
					if(childNext){
						productId = childNext.id.replace(/[^\d]/gi, '');
					}

					if(!productId && productId != '') {
						child.childElements()[0].childElements().each(function(childNext) {
							productId = childNext.id.replace(/[a-z-]*/, '');
						});
					}

					if (parseInt(productId) > 0) {
						var rel = 'lightbox['+ productId + ']' 
						a.rel = rel;

						var img = document.createElement("img"); 
						img.alt = "Preloader";
						img.src = src;
						img.style.display  = 'none';
						img.style.position = 'absolute';
						img.style.zIndex   = '100';
						img.style.marginLeft = -(AmZoomerOptions.thumbnailSize/2 + 15) + 'px';
						img.style.marginTop  = (AmZoomerOptions.thumbnailSize/2 - 14) + 'px';
						a.appendChild(img);
					} 
				}
			});
		});
	}
}