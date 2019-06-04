var app = window.app || {};

app.globals = {

    windowW       : 0,
    windowH       : 0,
    isInit        : false,
    isMobile      : false,

    // Init
    // ==========================================================================

    init : function ()
    {
        var self = this;

        if (!self.isInit) {
            self.isMobile = (/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera);

            if ( self.isMobile ) {
                app.elements.$body.addClass('has-touch');
            }

            app.scrollAnimations.init();
            app.svg.init();

            FastClick.attach(document.body);

            self.resize();
            $(window).on('throttledresize', function() {
                self.resize();
            });

            self.isInit = true;
        };
    },

    // Resize
    // ==========================================================================

    resize : function ()
    {
        this.windowW = window.innerWidth;
        this.windowH = window.innerHeight;
    }

};

// ==========================================================================
// Scroll animations
// ==========================================================================
var app = window.app || {};

app.scrollAnimations = {

    manager: null,

    init: function ()
    {
        var widget = this;

        if ( $('body').hasClass('is-welcoming-visitor') ) {
            // First visit delay
            // ==========================================================================
            setTimeout(function(){
                widget.wow();
            },6100);
        } else {
            // Normal visit
            // ==========================================================================
            widget.wow();
        }
    },

    wow: function ()
    {
        this.manager = new WOW({
            boxClass     : 'js-wow',
            animateClass : 'is-animated'
        });

        this.manager.init();
    }

};

// ==========================================================================
// Svg
// ==========================================================================
/* global svg4everybody */
var app = window.app || {};

app.svg = {

    init : function() {

        // Svg use polyfill
        // ==========================================================================
        svg4everybody();

    }

};

// ==========================================================================
// Lazy load
// ==========================================================================
var app = window.app || {};
app.widgets = app.widgets || {};

app.widgets.lazyLoad = {

    init : function() {

        if ( ! app.globals.isMobile ) {
            $('.js-lazy-load').each(function() {
                var $this = $(this),
                    dataImage = $this.data('lazy-image'),
                    dataClass = $this.data('lazy-class');

                $this.append('<img class="'+dataClass+'" src="'+dataImage+'">');
            });

            // load video on desktop
            $('.js-home-morphing').each(function() {
                var $this = $(this);

                $this.attr('src', $this.data('src'));
            });
        }

        $('.js-home-random-image').each(function() {
            var $this   = $(this)
                basePath = $this.data('path-base')
                src = '',
                min = 0,
                max = 15;

            switch (Math.floor(Math.random() * (max - min + 1)) + min) {
                case 0:
                    src = 'antoine1.jpg';
                    break;
                case 1:
                    src = 'ben1.jpg';
                    break;
                case 2:
                    src = 'charles1.jpg';
                    break;
                case 3:
                    src = 'chauncey1.jpg';
                    break;
                case 4:
                    src = 'dan1.jpg';
                    break;
                case 5:
                    src = 'delphine1.jpg';
                    break;
                case 6:
                    src = 'dimitri1.jpg';
                    break;
                case 7:
                    src = 'dom1.jpg';
                    break;
                case 8:
                    src = 'emilie1.jpg';
                    break;
                case 9:
                    src = 'fred1.jpg';
                    break;
                case 10:
                    src = 'jeff1.jpg';
                    break;
                case 11:
                    src = 'laura1.jpg';
                    break;
                case 12:
                    src = 'mat1.jpg';
                    break;
                case 13:
                    src = 'max1.jpg';
                    break;
                case 14:
                    src = 'quentin1.jpg';
                    break;
                case 15:
                    src = 'seb1.jpg';
                    break;
            }

            $this.attr('src', basePath + src);
        });
    }

};

// ==========================================================================
// Widget
// ==========================================================================
var app = window.app || {};
app.widgets = app.widgets || {};

app.widgets.scrollto = {

    ANCHOR_SELECTOR: '.js-scrollto',

    elements: {
        $anchors: []
    },

    init : function()
    {
        this.elements.$anchors = $(this.ANCHOR_SELECTOR);

        this.elements.$anchors.on('click.loco.scrollable', this.onScroll.bind(this));
    },

    onScroll : function(event)
    {
        event.preventDefault();

        var section = $(event.currentTarget).attr('href'),
            offset  = $(section).offset().top;

        this.scroll(offset);
    },

    scroll : function(offset, speed)
    {
        speed = speed || 1000;

        app.elements.$scrolled.animate({
            scrollTop: offset
        }, speed);
    }

};

// ==========================================================================
// App
// ==========================================================================
var app = window.app || {};

app.init = function() {

    'use strict';

    var self = this;

    self.EVENT_NAMESPACE = 'loco';

    self.params = {};

    self.templates = self.templates || {};

    self.widgets = self.widgets || {};

    // Globals
    // ==========================================================================
    if (typeof self.globals === 'object') {
        self.globals.init();
    }

    // Modules
    // ==========================================================================
    self.params.current_modules = [];

    var modules = document.querySelectorAll('[data-app]');
    for (var m = 0; m < modules.length; m++) {
        var dataApp = modules[m].getAttribute('data-app');
        if (typeof self[dataApp] === 'object' && self.params.current_modules.indexOf(dataApp) === -1) {
            self[dataApp].init();
            self.params.current_modules.push(dataApp);
        }
    }

    // Template
    // ==========================================================================
    self.params.current_template = document.body.getAttribute('data-template');

    if (typeof self.templates[ self.params.current_template ] === 'object') {
        self.templates[ self.params.current_template ].init();
    }

    // Widgets
    // ==========================================================================
    self.params.current_widgets = [];

    var widgets = document.querySelectorAll('[data-widget]');
    for (var w = 0; w < widgets.length; w++) {
        var dataWidget = widgets[w].getAttribute('data-widget');
        if (typeof self.widgets[dataWidget] === 'object' && self.params.current_widgets.indexOf(dataWidget) === -1) {
            self.widgets[dataWidget].init();
            self.params.current_widgets.push(dataWidget);
        }
    }

};

// Init
// ==========================================================================
$(function() {

    var loaded = false;
    var maxLoad = 3000;

    app.elements = {
        $window   : $(window),
        $document : $(document),
        $html     : $(document.documentElement),
        $body     : $(document.body),
        $scrolled : $('html,body'),
        $main     : $('#main')
    }

    // On load
    // ==========================================================================
    $(window).load(function() {
        if(!loaded) {
            loaded = true;
            load();
        }
    });

    // Maximum load
    // ==========================================================================
    setTimeout(function() {
        if(!loaded) {
            loaded = true;
            load();
        }
    }, maxLoad);

    // Load
    // ==========================================================================
    function load() {
        app.elements.$body.removeClass('is-loading is-initial-load').addClass('is-loaded dom-is-ready');
        app.init();
    }

});
