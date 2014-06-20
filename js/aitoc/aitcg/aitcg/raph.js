
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.Raph = new Class.create();
Aitcg.Raph.prototype =
{
    p : null, //paper
    img : null, //thumbnail url
    reset : false,
    control : {
        r : 5,
        o : 3,
        a : 4
    },
    initialize : function(el, sizeX, sizeY, minX, minY) 
    {
        this.p = Raphael(el, sizeX, sizeY);
        this.x = sizeX;
        this.y = sizeY;
        this.minX = minX;
        this.minY = minY;
        
        Raphael.el.moveTo = function(x, y) {            
            var dX = - this.lastX + x,
                dY = - this.lastY + y;
            this.translate(dX, dY);
            this.lastX = x;
            this.lastY = y;
        }
        Raphael.el.moveBy = function(dx, dy) {            
            this.translate(dx - this.last_x, dy - this.last_y);
            this.lastX = this.lastX + dx - this.last_x;
            this.lastY = this.lastY + dy - this.last_y;
            this.last_x = dx;
            this.last_y = dy;
        }        
        //store object position in private variables
        Raphael.el.save = function () {
            //if(this.type=='path' || this.itype=='translate') {
                this.last_x = 0;
                this.last_y = 0;
                this.ox = 0;
                this.oy = 0;
        };
        Raphael.el.rotateAbs = function (angle, x, y) {
            this.rot_x = x;
            this.rot_y = y;
            this.angle = angle;
            this.rotate(angle, x, y);
        };
        Raphael.el.rotateBack = function (x) {
            this.rotate(x * this.angle, this.rot_x, this.rot_y);
            return this.angle;
        };
        Raphael.el.setPos = function( box ) {
            //vert: 0 - top, 1 - bottom
            //hor: 0 - left, 1 - right
            this.rotate(0, this.rot_x, this.rot_y);
            var x, y;
            if( this.hor == 0 ) {                                  
                x = box.minX;
            } else {
                x = box.maxWidth;
            }
            if(x >= this.img.maxXPos - this.ow ) {
                x = this.img.maxXPos - this.ow;
            } else if (x < this.ow) {
                x = this.ow;
            }
            if( this.vert == 0 ) {
                y = box.minY;
            } else {
                y = box.maxHeight;
            }
            if(y >= this.img.maxYPos - this.oh ) {
                y = this.img.maxYPos - this.oh;
            } else if (y < this.oh) {
                y = this.oh;
            }
            this.moveTo(x,y);
        };
        //return coordinats of rotated image
        
        /**
         *  if you've made changes in this method be sure to
         *  make same changes in Aitoc_Aitcg_Model_Image
         */
        Raphael.el.getBBoxRotated = function() { 
                var degreesAsRadians = this._.rt.deg * Math.PI / 180; 
                var points = new Array(); 
                var box = this.getBBox(); 
                points.push({x: 0, y: 0}); 
                points.push({x: box.width, y: 0}); 
                points.push({x: 0, y: box.height}); 
                points.push({x: box.width, y: box.height}); 
                var bb = new Array();
                var newX, newY;
                bb['left'] = 0;bb['right'] = 0;bb['top'] = 0;bb['bottom'] = 0; 

                for (_px = 0; _px < points.length; _px++) { 
                    var p = points[_px]; 
                    newX = parseInt((p.x * Math.cos(degreesAsRadians)) + (p.y * Math.sin(degreesAsRadians))); 
                    newY = parseInt((p.x * Math.sin(degreesAsRadians)) + (p.y * Math.cos(degreesAsRadians))); 
                    bb['left'] = Math.min(bb['left'], newX); 
                    bb['right'] = Math.max(bb['right'], newX); 
                    bb['top'] = Math.min(bb['top'], newY); 
                    bb['bottom'] = Math.max(bb['bottom'], newY); 
                } 

                var newWidth = parseInt(Math.abs(bb['right'] - bb['left'])); 
                var newHeight = parseInt(Math.abs(bb['bottom'] - bb['top'])); 
                newX = (box.x + (box.width) / 2) - newWidth / 2; 
                newY = (box.y + (box.height) / 2) - newHeight / 2; 
                
                var minX = Math.max(0, Math.min(this.maxXPos, newX)),
                    minY = Math.max(0, Math.min(this.maxYPos, newY)),
                    maxX = Math.max(0, Math.min(this.maxXPos, newX+newWidth)),
                    maxY = Math.max(0, Math.min(this.maxYPos, newY+newHeight));
                return {x: newX, y: newY, width: newWidth, height: newHeight, minX:minX, minY:minY, maxWidth:maxX, maxHeight:maxY}; 
        };
    },
    addThumbnail : function( url, sizes, mult ) 
    {
        var new_sizes = Aitcg.checkSizes( sizes.full_x, sizes.full_y, this.x, this.y );
        //this.show(new_sizes, 'Mult: ' +mult);
        this.img = this.p.image(url, new_sizes.posX, new_sizes.posY, new_sizes.x, new_sizes.y);
        this.img.pair = new Array();
        this.def_pos = new_sizes;
        this.def_img_mult = mult;
        
        this.img.maxXPos = this.x;
        this.img.maxYPos = this.y;
        this.img.rotated = 0;
        this.img.last_scale = {x:1, y:1};

        return new_sizes.mult;
    },
    show : function(el) {
        var str = "";
        for(var i in el) {
            str += i+" -> "+el[i]+"\n";
        }
        if(typeof(arguments[1])!='undefined') {
            str = arguments[1] + ": \n"+ str;
        }        
        alert(str);
    },
    setImageDrag : function() {
        var start = function () {
                this.ox = this.attr("x");
                this.oy = this.attr("y");
                this.w = this.attr("width")/2;
                this.h = this.attr("height")/2;
                
                var box = this.getBBoxRotated();
                var offset = 10;
                this.border = {
                    maxX: this.paper.width - box.x + offset,
                    minX: (box.x + box.width + offset)*-1,
                    maxY: this.paper.height - box.y + offset,
                    minY: -(box.y + box.height + offset)
                };
                
                this.animate({opacity: .75}, 500, ">");

                for(var i in this.pair) {
                    if(typeof(this.pair[i].type)!='undefined') {
                        this.pair[i].save();
                        //this.pair[i].animate({opacity: .75}, 500, ">");
                    }
                }
            },
            move = function (dx, dy) {
                if(dx > this.border.maxX || dx < this.border.minX) {
                    dx = 0;
                }
                if(dy > this.border.maxY || dy < this.border.minY) {
                    dy = 0;
                }
                this.attr({x: this.ox + dx, y: this.oy + dy});                    
                for(var i in this.pair) {
                    if(typeof(this.pair[i].type)!='undefined') {
                        this.pair[i].moveBy(dx,dy);
                    }
                }                
            },
            up = function () {
                this.animate({opacity: 1}, 500, ">");
                var box = this.getBBoxRotated();
                for(var i in this.pair) {
                    if(typeof(this.pair[i].type)!='undefined') {
                        this.pair[i].setPos(box);
                        //this.pair[i].animate({opacity: 1}, 500, ">");
                    }
                }
            };
        this.img.attr({cursor:"move"});
        this.img.drag(move, start, up);        
    },
    
    addResizeSquare : function(vert, hor, cssstyle) {
        //vert: 0 - top, 1 - bottom
        //hor: 0 - left, 1 - right
        var x = this.getImg("x") + hor * this.getImg("width"),
            y = this.getImg("y") + vert* this.getImg("height"),
            c = this.control,
            radius = c.r, offset = c.o, al = c.a,//arrowLength
            start = (x-radius)+","+(y-radius), end = (x+radius)+","+(y+radius),
            rs = this.p.rect(x-radius-offset, y-radius-offset, (radius+offset)*2, (radius+offset)*2).attr({fill:"#aaa",stroke: "#000", opacity: 1,cursor:cssstyle+'-resize'});
            ra = this.p.path("M"+start+"L"+end+"M"+start+"L"+(x-radius)+","+(y-radius+al)+"M"+start+"L"+(x-radius+al)+","+(y-radius)+
            "M"+end+"L"+(x+radius)+","+(y+radius-al)+"M"+end+"L"+(x+radius-al)+","+(y+radius)
            ).attr({stroke: "#fff", cursor:cssstyle+'-resize'});

        var start = function () {
                this.imgx = this.img.attr("x");
                this.imgy = this.img.attr("y");
                
                this.box = this.img.getBBoxRotated();
                this.rotated = this.img.rotated != 0 ? this.img.rotated : 0;

                this.last_scale = {x:1,y:1};
                this.min_scale = 0.05;
                this.center_x = this.imgx + this.img.attr("width")/2;
                this.center_y = this.imgy + this.img.attr("height")/2;
                
                this.angle = this.rotated * Math.PI/180;
                if(typeof(this.img.last_scale) != 'undefined') {
                     this.last_scale = this.img.last_scale;
                }
                
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined')
                        this.img.pair[i].save();
                }
            },
            move = function (dx, dy) {
                var attr = {x:this.imgx, y:this.imgy},
                    cos = Math.abs(Math.cos(this.angle).toFixed(4)),
                    sin = Math.abs(Math.sin(this.angle).toFixed(4));
                dx = dy = Math.max(dx, dy);
                var x_scale = Math.max(this.min_scale, this.last_scale.x * ( 
                        (cos*cos*(this.hor_mod*dx+this.box.width))/(this.box.width) + 
                        (sin*sin*(this.vert_mod*dy+this.box.height))/(this.box.height)
                    )  ),
                    y_scale = Math.max(this.min_scale, this.last_scale.y * ( 
                        (sin*sin*(this.hor_mod*dx+this.box.width))/(this.box.width) + 
                        (cos*cos*(this.vert_mod*dy+this.box.height))/(this.box.height)
                    )  ),
                    move_hor  = (x_scale==this.min_scale) ? false : true,
                    move_vert = (y_scale==this.min_scale) ? false : true;
                x_scale = y_scale = Math.min(x_scale, y_scale);

                this.img.scale(x_scale, y_scale);
                this.img.last_scale = {x : x_scale, y: y_scale};
                
                var box = this.img.getBBoxRotated();
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined') {
                        this.img.pair[i].setPos(box);
                    }
                }
            },
            up = function () {
                var box = this.img.getBBoxRotated();
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined') {
                        this.img.pair[i].setPos(box);
                    }
                }                
            };
        rs.lastX = ra.lastX = x;
        rs.lastY = ra.lastY = y;
        rs.minX = ra.minX = this.minX;
        rs.minY = ra.minY = this.minY;
        rs.vert = ra.vert = vert;
        rs.hor = ra.hor = hor;
        ra.hor_mod = rs.hor_mod = hor ? 1 : -1;
        ra.vert_mod = rs.vert_mod = vert ? 1 : -1;
        ra.ow = rs.ow = ra.oh = rs.oh = c.r + c.o;
        rs.img = ra.img = this.img;
        //rs.itype = ra.itype = 'translate';
        ra.drag(move, start, up);//resize
        rs.drag(move, start, up);
        this.img.pair.push(ra);
        this.img.pair.push(rs);
        
    },
    addRotate : function(vert, hor, cssstyle) {
        //vert: 0 - top, 1 - bottom
        //hor: 0 - left, 1 - right
        var x = this.getImg("x") + hor * this.getImg("width"),
            y = this.getImg("y") + vert* this.getImg("height"),
            c = this.control,
            radius = c.r, offset = c.o, al = c.a,//arrowLength
            start = (x-radius)+","+(y), end = (x)+","+(y+radius),
            rs = this.p.rect(x-radius-offset, y-radius-offset, (radius+offset)*2, (radius+offset)*2).attr({fill:"#aaa",stroke: "#000", opacity: 1,cursor:cssstyle});
            r = this.p.path("M"+start+"C"+start+" "+(x-radius)+","+(y-radius)+" "+(x)+","+(y-radius)+
            "C"+(x)+","+(y-radius)+" "+(x+radius)+","+(y-radius)+" "+(x+radius)+","+(y)+
            "C"+(x+radius)+","+(y)+" "+(x+radius)+","+(y+radius)+" "+ end+
            "M"+(x-radius-1)+","+(y+1)+"L"+(x-radius-1)+","+(y-al)+"M"+(x-radius-1)+","+(y+1)+"L"+(x-radius+al)+","+(y+1)
            ).attr({stroke: "#fff", cursor:cssstyle});
 
        var start = function () {
                this.w = this.img.attr("width") / 2;
                this.h = this.img.attr("height") / 2;
                this.x = this.img.attr("x");
                this.y = this.img.attr("y");
                var center_x = this.x + this.w,
                    center_y = this.y + this.h;
                this.hor_pos = 0;
                this.ver_pos = 0;
                    
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined')
                        this.img.pair[i].save();
                }
                if(this.ox > center_x) {
                    this.hor_pos = 1;
                }
                if(this.oy > center_y) {
                    this.ver_pos = 1;
                }
                
                this.hyp = function(a,b){return Math.sqrt(a*a+b*b);};
                this.grad = function(rad){return (180/Math.PI)*rad;};
                
                this.def_angle = this.grad(Math.acos( this.w / this.hyp(this.w,this.h) ));//acos|asin
                //sin for right, cos for left
                this.rotated = this.img.rotated != 0 ? this.img.rotated : 0;
            },
            move = function (dx, dy) {
                var Pi = 0;
                dx = dx * -1;// -1 for left, 1 for right
                //dy = dy * -1;// -1 for top, 1 for bottom

                if(dy<0 && Math.abs(dy)>this.h) {
                    //dy = Math.abs(dy)-this.h*2;
                    dy = dy * -1 - this.h*2;
                    dx = dx * -1 - this.w*2;
                    Pi = -180;
                }
                var alp2 = Math.acos( (dx+this.w) / this.hyp(this.w+dx, this.h+dy) );// acos|asin
                alp2 = Math.round(Pi + this.def_angle - this.grad(alp2));
                var rot = this.rotated + alp2; 
                this.img.rotate(rot,true); 
                this.img.rotated = rot;
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined')
                        this.img.pair[i].rotateAbs( alp2, Math.round(this.x+this.w), Math.round(this.y+this.h) );
                }
            },
            up = function () {
                var box = this.img.getBBoxRotated();
                for(var i in this.img.pair) {
                    if(typeof(this.img.pair[i].type)!='undefined') {
                        this.img.pair[i].setPos(box);
                    }
                }
            };
            
        rs.lastX = r.lastX = x;
        rs.lastY = r.lastY = y;
        rs.minX = r.minX = this.minX;
        rs.minY = r.minY = this.minY;
        rs.vert = r.vert = vert;
        rs.hor = r.hor = hor;
        rs.img = r.img = this.img;
        r.ow = rs.ow = r.oh = rs.oh = c.r + c.o;
        //rs.itype = r.itype = 'translate';
        r.drag(move, start, up);//rotate
        rs.drag(move, start, up);
        this.img.pair.push(r);
        this.img.pair.push(rs);
    },
    check : function(data, id) {
         return (typeof(data[id])=='undefined')?false:true;
    },
    
    applyImageData : function ( data ) {
        var zero = 0;
        var attr = {};
        //this.show(data, 'applyData to  image');
        if(data.x != 0 || data.y!=0 || (data.scale_x!=1 && data.scale_x!=this.def_img_mult) || (data.scale_y != 1 && data.scale_y!=this.def_img_mult) || data.angle != 0) {
            if(this.check(data,"x")) {
                attr.x = data.x;
            } else {
                attr.x = this.getImg("x");
            }
            if(this.check(data,"y")) {
                attr.y = data.y;
            } else {
                attr.y = this.getImg("y");
            }
            if(this.check(data,"scale_x") && this.check(data,"scale_y")) {
                //this.img.scale( data.scale_x, data.scale_y, attr.x, attr.y );
                this.img.scale( data.scale_x, data.scale_y);
                this.img.last_scale = {x:data.scale_x, y:data.scale_y};
            }
            this.img.attr(attr);
            if(this.check(data,"angle")) {
                if(data.angle != 0 ) {
                    this.img.rotate( data.angle, true );
                }
                this.img.rotated = data.angle;
            }
        }
        var box = this.img.getBBoxRotated();
        for(var i in this.img.pair) {
            if(typeof(this.img.pair[i].type)!='undefined') {
                this.img.pair[i].save();
                this.img.pair[i].setPos(box);
            }
        }        
    },
    
    reset : function() {
        this.img.scale(1, 1);
        this.img.attr({x:this.def_pos.posX,y:this.def_pos.posY});
        this.img.last_scale = {x:1, y:1};
        this.img.rotate( 0, true );
        this.img.rotated = 0;
        
        var box = this.img.getBBoxRotated();
        for(var i in this.img.pair) {
            if(typeof(this.img.pair[i].type)!='undefined') {
                this.img.pair[i].save();
                this.img.pair[i].setPos(box);
            }
        }
    },
    
    getImageData: function() {
        var ret = {
            x :         this.getImg("x"),
            y :         this.getImg("y"),
            width:      this.getImg("width"),
            height:     this.getImg("height"),
            angle :     this.getImgRotate() % 360,
            scale_x:    this.getImgScale("x"),
            scale_y:    this.getImgScale("y"),
            shown_x:    this.x,
            shown_y:    this.y,
            force  :    1
        };
        //this.show(ret, 'Ret on apply');
        return ret;
    },
    
    getImgRotate : function() {
        return this.img.rotated;
    },
    getImgScale : function(id) {
        return this.img.last_scale[id];
    },    
    getImg : function (id) {
        return this.img.attr(id);
    },
    get : function() {
        return this.p;
    }
};