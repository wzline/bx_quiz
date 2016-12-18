(function(Aniart, $){

    if(!Aniart){
        window.Aniart = {};
        Aniart = window.Aniart;
    }

    var Widget = Aniart.Widget = function($el, params, events){
        this.$el = $el ? $el : (this.$el ? this.$el : null);
        params = (typeof(params) == 'object') ? params: {};
        if(typeof(this.defaults) != 'object'){
            if(this.defaults instanceof Function){
                this.defaults = this.defaults.apply();
            }
            else{
                this.defaults = {};
            }
        }
        $.extend(true, this, this.defaults, params);
        this.events = $.extend({}, this.events, events);
        this._initEvents();
        this.initialize.apply(this, arguments);
    };

    $.extend(Widget.prototype, {

        initialize: function(){},

        _initEvents: function(){
            if(typeof this.events == 'object'){
                for(var eventName in this.events){
                    var func = this.events[eventName];
                    if(func instanceof Function){
                        $(this).on(eventName, func);
                    }
                }
            }
        }
    });

    var has = function(obj, key) {
        return obj != null && hasOwnProperty.call(obj, key);
    };


    Aniart.Widget.extend = function(protoProps, staticProps) {
        var parent = this;
        var child;
        if (protoProps && has(protoProps, 'constructor')) {
            child = protoProps.constructor;
        } else {
            child = function() {
                return parent.apply(this, arguments);
            };
        }

        $.extend(child, parent, staticProps);

        var Surrogate = function() {
            this.constructor = child;
        };
        Surrogate.prototype = parent.prototype;
        child.prototype = new Surrogate;

        if (protoProps){
            $.extend(child.prototype, protoProps);
        }

        child.__super__ = parent.prototype;

        return child;
    };

})(window.Aniart, jQuery);